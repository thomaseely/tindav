<?php
	/**
	Client: Tindav.com
	API name: Registration
	Filename: registration.php
	//provider mandatory parameters
	Params: username, password, first_name, photo, address1, city, state, country, phone1, mobile1, zipcode, weekdays, service_weekends,
			service_type, service_timeslot
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 08-08-2018
	Modified On: 13-08-2018
	Description: This API is used to register the user like Provider or Seeker with children information .
	*/
	
	require("../connection.php");
	require("functions.php");
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);

	//CHECK PARAMS
	if($data["first_name"] !='' && $data["last_name"] !='' && $data["username"] !='' && $data["password"] !='' && $data["phone"] !='' && $data["user_type"] !='') 
	{		
		$checkUserName = isUsernameExists($data["username"],$data["user_type"]); //check username is exists
		
		if($checkUserName === false) {
			
			$token = md5(sha1($data["username"].$data["password"]));	//generate token				
			$password = base64_encode($data["password"]);
			
			if($data["user_type"] === 'parent') {	 //if parent insert into parents detail table		
				
				$query = "INSERT INTO parents SET first_name='".
							$data["first_name"]."', last_name='".$data["last_name"]."', username='".
							$data["username"]."', password='".$password."', token='".$token."', phone='".
							$data["phone"]."', created_on=now(),status=0";
			}	

			if($data["user_type"] === 'sitter') {	 //if sitter insert into sitters table		
				
				$query = "INSERT INTO sitters SET username='".$data["username"]."', password='".
							$password."',token='".$token."', first_name='".
							$data["first_name"]."', last_name='".$data["last_name"]."', phone='".
							$data["phone"]."', created_on=now(), status=0";
			}			

			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{				
				$lastinsertid = mysqli_insert_id($connection);
				
				//add address info table
				$query = "INSERT INTO address_info SET ";
				if($data["user_type"] === 'sitter') {	
					$query .=" sitter_id='".$lastinsertid."',";
				}
				if($data["user_type"] === 'parent') {	
					$query .=" parent_id='".$lastinsertid."',";
				}
				
				$query .=" address='".$data["address"]."', city='".
							$data["city"]."', state='".$data["state"]."', country='".
							$data["country"]."', zipcode='".$data["zipcode"]."', created_on=now(), status=0";
				if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
				{
					//success response					
					$response = array(
						'status' => "success",
						'token' => $token,
						$data['user_type'] => $lastinsertid,
						'status_message' =>$data['user_type']." Registration successfull."
					);	
				}	
			}
			
			//failure response
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>$data['user_type']." Registration Failed."
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
			'status_message' =>"Missing Fileds Username or Password"
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		