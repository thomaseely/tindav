<?php
	/**
	Client: Tindav.com
	API name: parents search sitter list, get request
	Filename: sitterlist.php
	//provider mandatory parameters
	Params: token, user_type
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 18-08-2018
	Modified On: 06-09-2018
	Description: This API is used to search the sitters based on the parent needs like service_type or start_date or time .
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
	
	if(isset($token) && !empty($token) && !empty($user_type)) 
	{			
		if( isValidToken($token,$user_type)!== false ) 
		{
			$query = "SELECT s.sitter_id, s.first_name, s.last_name, s.description, s.rate, s.photo, s.phone, s.ages_handling, 
						s.service_start_date, s.service_end_date,s.service_timeslot, a.address, a.city, a.state, a.country, a.zipcode  
					  FROM sitters s LEFT JOIN address_info a 
					  ON s.sitter_id = a.sitter_id ";
			
			if(isset($sitter_id) && $sitter_id != ''){
				$query.= " AND s.sitter_id = '".$sitter_id."' ";
			}			  
			if(isset($zipcode) && $zipcode != ''){
				$query.= " AND a.zipcode = '".$zipcode."' ";
			}
			if(isset($service_type) && $service_type !=''){
				$query.= " AND s.service_type = '".$service_type."'"; //service type recurring or onetime
			}
			if(isset($start_time) && $start_time !='' && isset($end_time) && $end_time !=''){ //eg: 10am-6pm
				$service_timeslot = $start_time."-".$end_time;
				$query.= " AND s.service_timeslot = '".$service_timeslot."'"; //service_timeslot 10am-6pm
			}
			if(isset($start_date) && $start_date !='' && isset($end_date) && $end_date == ''){
				$query.= " AND ((s.service_start_date = '".$start_date."' OR  s.service_end_date = '".$start_date."') 
							OR ('".$start_date."' BETWEEN s.service_start_date AND s.service_end_date )) "; 		
			}
			if(isset($start_date) && $start_date !='' && isset($end_date) && $end_date !=''){
				$query.= " AND (s.service_start_date = '".$start_date."' AND s.service_end_date = '".$end_date."')";
			}
			if(isset($child_age) && $child_age !=''){
				
				$query.= " AND s.ages_handling <= '".$child_age."'"; //service type recurring or onetime
			}
			
			$query .= " WHERE a.address_id IS NOT NULL";
			
			//echo $query;exit;
						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			
			if(!empty($result))
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
			'status_message' => "Missing parameter token or user_type."
		);
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);
?>