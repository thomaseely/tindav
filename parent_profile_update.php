<?php
	/**
	Client: Tindav.com
	API name: Parent Profile Update
	Filename: parent_profile_update.php
	//provider mandatory parameters
	Params:	token, service_type, start_date, start_time, end_date, end_time, no_of_child_tocare, 
	//seeker mandatory parameters
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 08-08-2018
	Modified On: 29-08-2018
	Description: This API is used to register the user like Provider or Seeker with children information .
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
	if($data["token"] !='' && ($data["user_type"]=='parent' || $data["user_type"]=='sitter'))
	{		
		$row = isValidToken($data["token"], $data["user_type"]);
			
			if($row["parent_id"] != '') {	
			
				$query = "UPDATE parents SET ";
				
				if($data["service_type"] !=''){
					$query .= " service_type='".sanitize($data["service_type"])."',";
				}
				if($data["start_date"] !=''){
					$query .= " start_date='".sanitize($data["start_date"])."',";
				}
				if($data["start_time"] !=''){
					$query .= " start_time='".sanitize($data["start_time"])."',";
				}
				if($data["end_date"] !=''){
					$query .= " end_date='".sanitize($data["end_date"])."',";
				}
				if($data["end_time"] !=''){
					$query .= " end_time='".sanitize($data["end_time"])."',";
				}
				if($data["no_of_child_tocare"] !=''){
					$query .= " no_of_child_tocare='".sanitize($data["no_of_child_tocare"])."',";
				}
				if(isset($data["photo"]) && $data["photo"]!='')	{
					$query .= " photo='".sanitize($data["photo"])."',";
				} 
				if(isset($data["first_name"]) && $data["first_name"]!='') {
					$query .= " first_name='".sanitize($data["first_name"])."',"; 
				}
				if(isset($data["last_name"]) && $data["last_name"]!='') {
					$query .= " last_name='".sanitize($data["last_name"])."',"; 
				}			
				if(isset($data["phone"]) && $data["phone"]!='') {
					$query .= " phone='".sanitize($data["phone"])."',";			
				}				
				
				$query .= " modified_on=now() WHERE parent_id='".sanitize($row['parent_id'])."'";				
			}

			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{															
				if( $row["parent_id"] != '' && !empty( $data["children"]) && $data["no_of_child_tocare"] > 0 ) { 
					
					$isThereChildOfParent = isThereChildOfParent($row["parent_id"]);
					
					if($isThereChildOfParent === true) {
						$query = "DELETE FROM child_info WHERE parent_id = '".sanitize($row['parent_id'])."' ";
						mysqli_query($connection, $query) or die("Error:".mysqli_error($connection));
					}
					
					for($i = 0; $i < count($data['children']); $i++){ // get the number of children of seeker					
													
						$query = "INSERT INTO child_info SET parent_id = '".sanitize($row['parent_id'])."', child_first_name = '".
									sanitize($data['children'][$i]["child_first_name"])."', child_last_name = '".
									sanitize($data['children'][$i]["child_last_name"])."', child_gender = '".
									sanitize($data['children'][$i]["child_gender"])."', child_age = '".
									sanitize($data['children'][$i]["child_age"])."' , created_on = now()";	
									
						//insert childrens information into child info table 			
						if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
						{					
							$response = array(
								'status' => 'success',										
								'status_message' => "Child Registered Successfully."
							);
						} 
						else
						{
							$response = array(
								'status' => "failure",
								'status_message' => "Child Registration Failed."
							);
						}
					} //end for
					
					//success response					
					$response = array(
						'status' => "success",
						'data' => $data,					
						'status_message' =>  $data['user_type']." Updated successfully."
					);
					
				}			
			}
			//failure response
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' => "Update Failed."
				);
			}		

	}
	else
	{
		$response = array(
			'status' => "failure",
			'status_message' => "Missing fields token and user_type(parent or sitter)."
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		