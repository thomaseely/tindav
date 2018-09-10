<?php
	/**
	Client: Tindav.com
	API name: Reviews list, Get request
	Filename: reviewlist.php
	
	Params: token, user_type
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 29-08-2018
	Modified On: 13-08-2018
	Description: This API is used to register the user like Sitter or Parent .
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
	
	if(isset($_GET)) extract($_GET);
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);	
	
	if(isset($token) && !empty($token) && !empty($user_type)) 
	{			
		$valid = isValidToken($token, $user_type);
		
		if($valid !== false) {
			
			if($valid['sitter_id'] !='') {
				$query = "SELECT p.first_name, p.last_name,r.rating, r.reviews,r.created_on 
							FROM parents p LEFT JOIN parents_reviews r ON p.parent_id = r.parent_id 
							WHERE r.sitter_id = '".$valid['sitter_id']."'";
			
				//echo $query;exit;
							
				$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
				
				if(!empty($result))
				{
					$reviews = array();
					// Associative array
					while($row = mysqli_fetch_assoc($result)) {
						$reviews[] = $row;
					}
					$response = array(
						'status' => "success",					
						'reviewlist' => $reviews					
					);
				}
				else
				{
					$response = array(
						'status' => "failure",
						'status_message' => "Record not found."
					);
				}
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
	
	} catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
?>