<?php
	/**
	Client: Tindav.com
	API name: Registration, Post request
	Filename: registration.php
	//provider mandatory parameters
	Params: username, password, first_name, last_name, phone, address1, city, state, country, zipcode
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 08-08-2018
	Modified On: 13-08-2018
	Description: This API is used to register the user like Sitter or Parent .
	*/
	
	require("../connection.php");
	require("functions.php");
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);

	//CHECK PARAMS
	if($data["first_name"] !='' && $data["last_name"] !='' && $data["username"] !='' && $data["password"] !='' && $data["phone"] !='' && $data["address"] !='' && $data["user_type"] !='') 
	{		
		$checkUserName = isUsernameExists($data["username"],$data["user_type"]); //check username is exists
		
		if($checkUserName === false) {
					
			$password = base64_encode(sanitize($data["password"]));
			
			$tbl = ($data["user_type"] === "sitter")? "sitters":"parents";
			
			$query = "INSERT INTO $tbl SET first_name='".
						sanitize($data["first_name"])."', last_name='".sanitize($data["last_name"])."', username='".
						sanitize($data["username"])."', password='".$password."', phone='".
						sanitize($data["phone"])."', created_on=now(), status=0";			

			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{				
				$lastinsertid = mysqli_insert_id($connection);
				
				if($data['address'] !='') {
					//add address info table
					$query = "INSERT INTO address_info SET ";
					
					if($data["user_type"] === 'sitter') {	
						$query .=" sitter_id='".$lastinsertid."',";
					}
					if($data["user_type"] === 'parent') {	
						$query .=" parent_id='".$lastinsertid."',";
					}
					
					$query .=" address='".sanitize($data["address"])."', city='".
								sanitize($data["city"])."', state='".sanitize($data["state"])."', country='".
								sanitize($data["country"])."', zipcode='".sanitize($data["zipcode"])."', created_on=now(), status=0";
								
					if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
					{
						//success response					
						$response = array(
							'status' => "success",
							$data['user_type'] => $lastinsertid,
							'status_message' => $data['user_type']." Registration successfull."
						);	
					}
				}				
			}
			
			//failure response
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' => $data['user_type']." Registration Failed."
				);
			}				
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' => "Username already exists."
			);
		}	
	}
	else
	{
		$response = array(
			'status' => "failure",
			'status_message' =>"Missing any Fileds first_name, last_name, username, password, phone or address and user_type"
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		