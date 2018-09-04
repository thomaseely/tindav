<?php
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
			
			$query .= ", address='".$data["address"]."', city='".
						$data["city"]."', state='".$data["state"]."', country='".
						$data["country"]."', zipcode='".$data["zipcode"]."', created_on=now(), status = 1"; //1 means current address
						
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
					'status_message' =>" Review added Failed."
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