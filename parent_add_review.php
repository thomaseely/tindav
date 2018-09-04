<?php
	/**	
	Client: Tindav.com
	API name: Parent Add Review about Sitter
	Filename: parent_add_review.php
	//provider mandatory parameters
	Params: parent_id, sitter_id, rating, reviews
	//seeker mandatory parameters
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 29-08-2018
	Description: This API is used to add review to the sitter by the parent .
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
	if($data["parent_id"] !='' && $data["sitter_id"] !='' && $data["rating"] !='' && $data["reviews"] !='' ) 
	{			
		$query = "INSERT INTO parents_reviews SET parent_id='".$data["parent_id"]."', sitter_id='".
					$data["sitter_id"]."', rating='".$data["rating"]."', reviews='".
					$data["reviews"]."', created_on=now(), status = 0";
		
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
		{				
			$lastinsertid = mysqli_insert_id($connection);
			//success response					
			$response = array(
				'status' => "success",
				'parent_review_id' => $lastinsertid,
				'status_message' =>" Review added successfully."
			);
			
		}
		
		//failure response
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' =>" Review added Failed."
			);
		}	
	}
	else if($data['parent_review_id']!="" ){		
		
		//update the rating and reviews
		$query = "UPDATE parents_reviews SET rating='".$data["rating"]."', reviews='".
					$data["reviews"]."' WHERE parent_review_id='".$data["parent_review_id"]."'";
		
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
			$response = array(
				'status' => "success",
				'parent_review_id' => $data['parent_review_id'],
				'status_message' =>" Review updated successfully."
			);
		}				
	}	
	else
	{
		$response = array(
			'status' => "failure",
			'status_message' =>"Missing Fileds Parent_id, sitter_id, rating or reviews"
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		