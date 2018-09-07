<?php
	/**
	Client: Tindav.com
	API name: Login, GET Request
	Filename: login.php
	
	Params: username, password, user_type
	Description: Successful logged in users will get token for the next and further request.
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 08-08-2018
	Modified On: 06-09-2018
	Description: This API is used to login the user to Tindav .
	*/
	
	require("../connection.php");
	require("functions.php");
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	if(isset($_GET)) extract($_GET);
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	//print_r($data);exit;	
	
	if($username !='' && $password !='' && $user_type !="") {		
		
		$col = ($user_type === "parent")? "parent_id":"sitter_id";		
		$tbl = ($user_type === "sitter")? "sitters":"parents";
					
	    $query = "SELECT $col FROM $tbl 
								WHERE username = '".sanitize($username)."' 
								AND password = '".base64_encode(sanitize($password))."'
								AND status = 0 LIMIT 1";
								
		$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
		
		if($result !='')
		{		
			$row = mysqli_fetch_assoc($result);
			
			if(!empty($row)){	
				//generate new token
				$str = $row['username'].$row['password'];
				$token = md5(uniqid($str, true));
				$updateToken = updateToken($token, $tbl, $col, $row[$col]);
				
				if($updateToken !== false) {
					session_start();
					$_SESSION['start'] = time(); // Taking now logged in time.
					// Ending a session in 30 minutes from the starting time.
					
					$_SESSION['token'] = $token;
					$_SESSION['expire'] = $_SESSION['start'] + (30 * 60); //30*60 for half an hour
				
					$response = array(
						'status' => "success",
						$col => $row[$col],
						'token' => $token				
					);
				}
			}
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>"Invalid username or password"
				);
			}
		}			
	}
	else
	{
		$response = array(
			'status' => "failure",
			'status_message' =>"username , password or user_type param should not be empty."
		);
	}
	header('Content-Type: application/json');
	echo json_encode($response);
?>