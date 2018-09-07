<?php
	/**
	Client: Tindav.com
	API name: Parent cancel Sitter
	Filename: parent_cancel_sitter.php
	//provider mandatory parameters
	Params: parent_id, sitter_id, cancel_review
	//seeker mandatory parameters
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 29-08-2018
	Description: This API is used to cancel sitter by the parent .
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
	if($data["parent_id"] !='' && $data["sitter_id"] !='' && $data["cancel_review"] !='' && $data["parent_sitter_id"]!='') 
	{			
		$query = "UPDATE parents_sitters SET status = 1 WHERE parent_id='".sanitize($data["parent_id"])."' 
														AND sitter_id='".sanitize($data["sitter_id"])."' 
														AND status=0";
			
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
		{				
			//success response					
			$response = array(
				'status' => "success",				
				'status_message' =>" Sitter cancelled successfully."
			);			
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' =>" Sitter cancel Failed."
			);
		}			
			
		if($data['cancel_reviews'] !="") {
			$query = "UPDATE parents_reviews SET cancel_reviews='".sanitize($data["cancel_reviews"])."', status = 1 
						WHERE parent_id='".sanitize($data["parent_id"])."' AND sitter_id='".sanitize($data["sitter_id"])."'";
						
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{				
				//success response					
				$response = array(
					'status' => "success",				
					'status_message' =>" Your sitter cancelled successfully."
				);			
			}
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>" Sitter cancel Failed."
				);
			}					
			
		}	
	}
	else
	{		
		$response = array(
			'status' => "failure",
			'status_message' => "Missing parameter parent_id,sitter_id,cancel_review,parent_sitter_id."
		);
	}
		
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		