<?php
	/**
	Client: Tindav.com
	API name: Sitter's logged in and find parents list, Get request
	Filename: sitters_parentlist.php
	//provider mandatory parameters
	Params: token, user_type
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 08-08-2018
	Modified On: 01-09-2018
	Description: This API is used to find the parents list of booked sitter .
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
	
	if(isset($_GET)) extract($_GET);
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);	
	
	if(isset($token) && !empty($token)) 
	{			
		$row = isValidToken($token,$user_type);
		
		if( !empty($row)) 
		{  
			$query = "SELECT p.parent_id, p.first_name, p.last_name, p.service_type, p.start_date, p.start_time, p.end_date, p.end_time, 
					  a.address, a.city, a.state, a.country, a.zipcode, ps.rate, ps.created_on					  
					  FROM parents p LEFT JOIN parents_sitters ps ON p.parent_id = ps.parent_id  
					  LEFT JOIN address_info a on ps.parent_id = a.parent_id 
					  WHERE ps.sitter_id = '".$row['sitter_id']."'
					  ORDER BY p.start_date DESC";
			
			//echo $query;exit;
						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			
			if(!empty($result))
			{
				$parents = array();
				// Associative array
				while($row = mysqli_fetch_assoc($result)) {
					$parents[] = $row;
				}
				$response = array(
					'status' => "success",					
					'upcoming_parent_list' => $parents					
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