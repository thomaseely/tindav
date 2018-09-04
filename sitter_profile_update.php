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

	//CHECK PARAMS
	if($data["sitter_id"] !='') 
	{		
		$query = "UPDATE sitters SET ";	
		
		$query .= "  dob = '".$data["dob"]."', 
					 can_work_in_us = '".$data["can_work_in_us"]."', 
					 dmv_check = '".$data["dmv_check"]."', 
					 highest_education = '".$data["highest_education"]."', 
					 years_experience = '".$data["years_experience"]."', 
					 ages_handling = '".$data["ages_handling"]."', 
					 have_children = '".$data["have_children"]."', 
					 rate = '".$data["rate"]."',";

		if(isset($data["photo"]) && $data["photo"]!='')	{
			$query .= " photo='".$data["photo"]."',";
		} 
	    if(isset($data["first_name"]) && $data["first_name"]!='') {
			$query .= " first_name='".$data["first_name"]."',"; 
		}
		if(isset($data["last_name"]) && $data["last_name"]!='') {
			$query .= " last_name='".$data["last_name"]."',"; 
		 }			
		if(isset($data["phone"]) && $data["phone"]!='') {
			$query .= " phone='".$data["phone"]."',";			
		}

		$query = substr($query, 0, -1);

		$query .=" WHERE sitter_id='".$data["sitter_id"]."'";	
		
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
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		