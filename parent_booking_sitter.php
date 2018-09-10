<?php
	/**
	Client: Tindav.com
	API name: Parent booking Sitter
	Filename: parent_booking_sitter.php
	//provider mandatory parameters
	Params: parent_id, sitter_id
	
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 07-09-2018
	Modified On: 07-09-2018
	Description: This API is used to book a sitter by parent.
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
		
		//DB CONNECTION
		$db = new dbObj();
		$connection =  $db->getConnstring();
		
		//GET RAW INPUT
		$data = json_decode(file_get_contents('php://input'), true);
		
		//CHECK PARAMS
		if($data["token"] !='' && ($data["user_type"]=='parent' || $data["user_type"]=='sitter'))
		{		
			$row = isValidToken($data["token"], $data["user_type"]);		

			//CHECK PARAMS
			if($row["parent_id"] !='' && $data["sitter_id"] !='' && $data["rate"] !='') 
			{		
				$isParentBooked = isParentBookedSitter();
				if($isParentBooked === false) {
					$query = "INSERT INTO parents_sitters SET parent_id='".sanitize($data["parent_id"])."', 
																	sitter_id='".sanitize($data["sitter_id"])."', 
																	rate='".sanitize($data["rate"])."', 
																	created_on = now(), status=0";
						
					if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
					{				
						//success response					
						$response = array(
							'status' => "success",				
							'status_message' =>" Parent Booking sitter is successfull."
						);			
					}
					else
					{
						$response = array(
							'status' => "failure",
							'status_message' =>" Parent booking sitter is failed."
						);
					}
				}
				else
				{
					$response = array(
						'status' => "failure",
						'status_message' =>" Parent booked sitter already, need to cancel first if you want to book again."
					);
				}			
					
			}
			else
			{		
				$response = array(
					'status' => "failure",
					'status_message' => "Missing parameter parent_id, sitter_id."
				);
			}
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' => "Missing fields token or invalid and user_type(parent or sitter)."
			);
		}	
		
	} catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		