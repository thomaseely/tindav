<?php
	session_start();	
	if (!isset($_SESSION['token'])) {
        echo "Please Login again";
		exit;
    }
	require("../connection.php");
	require("functions.php");	
	
	$isValidSession = isValidSession();	
	if(!$isValidSession) exit;
	
	if(isset($_GET)) extract($_GET);
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);	
	
	if(isset($token) && !empty($token)) 
	{			
		$row = isValidToken($token,$user_type);
		
		if(!empty($row)) {
			
			if($row['sitter_id'] !='') {
				$query = "SELECT p.first_name, p.last_name,r.rating, r.reviews,r.created_on 
							FROM parents p LEFT JOIN parents_reviews r ON p.parent_id = r.parent_id 
							WHERE r.sitter_id = '".$row['sitter_id']."'";
			
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
			'status_message' => "Missing parameter token."
		);
	}
	
	
	header('Content-Type: application/json');
	echo json_encode($response);
?>