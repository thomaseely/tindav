<?php
	/**
	Client: Tindav.com
	API name: use this address as current address
	Filename: use_this_address.php
	//provider mandatory parameters
	Params:	parent_id or sitter_id and address information
	//seeker mandatory parameters
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 29-08-2018
	Description: This API is used to register the user like Provider or Seeker with children information .
	*/
	
	session_start();	
	if (!isset($_SESSION['token'])) {
        echo "Please Login again";
		exit;
    }
	require("../connection.php");
	require("functions.php");	
	
	$isValidSession = isValidSession();	
	if(!$isValidSession) exit;
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);
	
	if(($data["parent_id"] !='' || $data["sitter_id"] !='') && $data["address"] !='' ) 
	{
		$query = "UPDATE address_info SET status=0 WHERE ";	
		
		if($data['parent_id']!=""){
			$query .=" parent_id='".$data["parent_id"]."'";
		}
		
		if($data['sitter_id']!=""){
			$query .=" sitter_id='".$data["sitter_id"]."'";
		}
		
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
		{		
			$query = "INSERT INTO address_info SET ";

			if($data['parent_id']!=""){
				$query .=" parent_id='".$data["parent_id"]."'";
			}
			if($data['sitter_id']!=""){
				$query .=" sitter_id='".$data["sitter_id"]."'";
			}
			
			$query .= ", address='".sanitize($data["address"])."', city='".
						sanitize($data["city"])."', state='".sanitize($data["state"])."', country='".
						sanitize($data["country"])."', zipcode='".sanitize($data["zipcode"])."', created_on=now(), status = 1"; //1 means current address
						
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{				
				$lastinsertid = mysqli_insert_id($connection);
				//success response					
				$response = array(
					'status' => "success",
					'address_id' => $lastinsertid,
					'status_message' =>" Address added successfully."
				);				
			}
			
			//failure response
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>" Address added Failed."
				);
			}
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' =>" Update address Failed."
			);
		}
		
	}


?>