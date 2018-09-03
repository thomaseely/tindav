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
		
	    $query = "SELECT $col, token FROM $tbl 
								WHERE username = '".trim($username)."' 
								AND password = '".base64_encode(trim($password))."' 
								
								AND status = 0 LIMIT 1";
					
		$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
		
		if($result)
		{
			//$token = md5($_GET["username"].$_GET["password"]);	
			//session_start();
			//$_SESSION['token'] = $token;
			$row = mysqli_fetch_assoc($result);
			
					   
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
				'status_message' =>"Record not found."
			);
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