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
	
	require("../connection.php");
	require("functions.php");
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);

	//CHECK PARAMS
	if($data["zipcode"] !='') 
	{			
		$isZipcodeAvailable = isZipcodeAvailable($data["zipcode"]);
			
		if($isZipcodeAvailable === false)
		{				
			//success response					
			$response = array(
				'status' => "success",				
				'status_message' =>" Sorry, not serving in this area. Please leave your email address and we will notify you when we start."
			);			
		}
	}
	else
	{		
		$response = array(
			'status' => "failure",
			'status_message' => "Missing parameter zipcode."
		);
	}
		
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		