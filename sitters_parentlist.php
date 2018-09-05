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
		
		if( !empty($row)) 
		{  
			$query = "SELECT p.parent_id, p.first_name, p.last_name, p.service_type, p.start_date, p.start_time, p.end_date, p.end_time, 
					  a.address, a.city, a.state, a.country, a.zipcode, ps.rate, ps.created_on					  
					  FROM parents p LEFT JOIN parents_sitters ps ON p.parent_id = ps.parent_id  
					  LEFT JOIN address_info a on ps.parent_id = a.parent_id WHERE ps.sitter_id = '".$row['sitter_id']."'";
			
			//echo $query;exit;
						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			
			if($result)
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