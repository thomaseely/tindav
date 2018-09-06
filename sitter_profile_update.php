<?php
	/**
	Client: Tindav.com
	API name: sitter_profile_update
	Filename: sitter_profile_update.php
	//provider mandatory parameters
	Params: sitter_id
	
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 31-08-2018
	Description: This API is used to update sitters information in the sitters table.
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
	
	if(isset($data['token']) && !empty($data['token']) && !empty($data['user_type'])) 
	{			
		$row = isValidToken($data['token'], $data['user_type']);
		
		if( !empty($row)) 
		{  
			if($row["sitter_id"] !='') 
			{		
				$query = "UPDATE sitters SET ";	
				
				$query .= "  dob = '".sanitize($data["dob"])."', 
							 can_work_in_us = '".sanitize($data["can_work_in_us"])."', 
							 dmv_check = '".sanitize($data["dmv_check"])."', 
							 highest_education = '".sanitize($data["highest_education"])."', 
							 years_experience = '".sanitize($data["years_experience"])."', 
							 ages_handling = '".sanitize($data["ages_handling"])."', 
							 have_children = '".sanitize($data["have_children"])."', 
							 rate = '".sanitize($data["rate"])."',";

				if(isset($data["photo"]) && $data["photo"]!='')	{
					$query .= " photo='".sanitize($data["photo"])."',";
				} 
				if(isset($data["first_name"]) && $data["first_name"]!='') {
					$query .= " first_name='".sanitize($data["first_name"])."',"; 
				}
				if(isset($data["last_name"]) && $data["last_name"]!='') {
					$query .= " last_name='".sanitize($data["last_name"])."',"; 
				}			
				if(isset($data["phone"]) && $data["phone"]!='') {
					$query .= " phone='".sanitize($data["phone"])."',";			
				}

				$query = substr($query, 0, -1);

				$query .=" WHERE sitter_id='".$row["sitter_id"]."'";	
				
				//echo $query;exit;
				
				if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
				{							
					$response = array(
						'status' => "success",
						'status_message' =>" Sitter's profile updated successfully."
					);				
				}		
				else
				{
					$response = array(
						'status' => "failure",
						'status_message' =>"Sitter's Profile updation Failed."
					);
				}		
			}
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>"Missing Field sitter_id"
				);
			}
		}
		else
		{		
			$response = array(
				'status' => "failure",
				'status_message' => "Token is invalid."
			);
		}	
	}
	else
	{		
		$response = array(
			'status' => "failure",
			'status_message' => "Missing parameter token or user_type."
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		