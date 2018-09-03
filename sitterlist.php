<?php
	//session_start();
	require("../connection.php");
	require("functions.php");
	
	if(isset($_GET)) extract($_GET);
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);	
	
	if(isset($token) && !empty($token)) 
	{			
		if( isValidToken($token,$user_type)!== false ) 
		{
			$query = "SELECT s.sitter_id, s.first_name, s.last_name, s.description, s.photo, s.ages_handling, 
			s.service_start_date, s.service_end_date,s.service_timeslot 
			FROM sitters s LEFT JOIN address_info a ON s.sitter_id = a.sitter_id ";
			if($zipcode != ''){
				$query.= " AND a.zipcode = '".$zipcode."' ";
			}
			if(isset($service_type) && $service_type !=''){
				$query.= " AND s.service_type = '".$service_type."'"; //service type recurring or onetime
			}
			if($start_time !='' && $end_time !=''){ //eg: 10am-6pm
				$service_timeslot = $start_time."-".$end_time;
				$query.= " AND s.service_timeslot = '".$service_timeslot."'"; //service_timeslot 10am-6pm
			}
			if(isset($start_date) && $start_date !='' && $end_date == ''){
				$query.= " AND ((s.service_start_date = '".$start_date."' OR  s.service_end_date = '".$start_date."') OR ('".
						$start_date."' BETWEEN s.service_start_date AND s.service_end_date )) "; 		
			}
			if($start_date !='' && $end_date !=''){
				$query.= " AND (s.service_start_date = '".$start_date."' AND s.service_end_date = '".$end_date."')";
			}
			if(isset($child_age) && $child_age !=''){
				
				$query.= " AND s.ages_handling <= '".$child_age."'"; //service type recurring or onetime
			}
			
			$query .= " WHERE a.address_id IS NOT NULL";
			
			//echo $query;exit;
						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			
			if($result)
			{
				$provider = array();
				// Associative array
				while($row = mysqli_fetch_assoc($result)) {
					$provider[] = $row;
				}
				$response = array(
					'status' => "success",					
					'sitterlist' => $provider					
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