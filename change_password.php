<?php
	/**
	Client: Tindav.com
	API name: Change password
	Filename: change_password.php
	
	Params: parent_id or sitter_id, old password, new password
		
	API vertion: 1.0
	Created By: Thomas
	Created On: 07-09-2018
	Modified On: 07-09-2018
	Description: This API is used to change password.
	*/
	
	session_start();	
	if (!isset($_SESSION['token'])) {
        echo "Please Login again";
		exit;
    }
	require("../connection.php");
	require("functions.php");	
	
	try { 
	
		$isValidSession = isValidSession();	
		if(!$isValidSession) exit;
		
		//DB CONNECTION
		$db = new dbObj();
		$connection =  $db->getConnstring();
		
		//GET RAW INPUT
		$data = json_decode(file_get_contents('php://input'), true);
		
		//CHECK PARAMS
		if($data["token"] !='' && ($data["user_type"]=='parent' || $data["user_type"]=='sitter'))
		{		
			$row = isValidToken($data["token"], $data["user_type"]);		

			//CHECK PARAMS
			if($row["parent_id"] !='' || $row["sitter_id"] !='' && $data["oldpassword"] !='' && $data["newpassword"] !='') 
			{		
				$checkOldPassword = checkOldPassword($data["token"], $data["user_type"], $data["oldpassword"]);
							
				$tbl = ($data["user_type"] === "sitter")? "sitters":"parents";
				$col = ($data["user_type"] === "parent")? "parent_id":"sitter_id";	
				
				if($checkOldPassword !== false) {
					
					$query = "UPDATE $tbl SET password='".sanitize($data["newpassword"])."'	
								WHERE token ='".sanitize($data["token"])."' AND $col ='".$checkOldPassword."'";
						
					if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
					{				
						//success response					
						$response = array(
							'status' => "success",				
							'status_message' =>" Password changed successfully."
						);			
					}
					else
					{
						$response = array(
							'status' => "failure",
							'status_message' =>" Password change failed."
						);
					}
				}
				else
				{
					$response = array(
						'status' => "failure",
						'status_message' =>" Passwords are mismatch."
					);
				}			
					
			}
			else
			{		
				$response = array(
					'status' => "failure",
					'status_message' => "Missing parameter parent_id, sitter_id, oldpassword, newpassword."
				);
			}
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' => "Missing fields token or invalid and user_type(parent or sitter)."
			);
		}	
	} catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		