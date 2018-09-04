<?php
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
		
		$token = md5(sha1($username.$password));	//generate token	
		
	    $query = "SELECT $col, token FROM $tbl 
								WHERE username = '".trim($username)."' 
								AND password = '".base64_encode(trim($password))."' 
								AND token = '".$token."'   	
								AND status = 0 LIMIT 1";
								
		$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
		
		if($result !='')
		{		
			$row = mysqli_fetch_assoc($result);
			
			if(!empty($row)){	 
				session_start();
				$_SESSION['start'] = time(); // Taking now logged in time.
				// Ending a session in 30 minutes from the starting time.
				
				$_SESSION['token'] = $row['token'];
				$_SESSION['expire'] = $_SESSION['start'] + (30 * 60); //30*60 for half an hour
			
				$response = array(
					'status' => "success",
					$col => $row[$col],
					'token' => $row['token']				
				);
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