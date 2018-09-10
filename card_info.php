<?php
	/**
	Client: Tindav.com
	API name:Card info
	Filename: card_info.php
	
	Params: parent_id or sitter_id, cardinfo, card_add or card_id, card_edit/card_delete 
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 31-08-2018
	Description: This API is used to add/edit/delete card informations .
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

	try {
		//CHECK PARAMS
		if($data["card_add"] !='' ) 
		{
			
			$query = "INSERT INTO card_info SET ";
			
			if($data["parent_id"] !=''){
				$query .= " parent_id='".$data["parent_id"]."',";
			}
			
			if($data["sitter_id"] !=''){
				$query .= " sitter_id='".$data["sitter_id"]."',";
			}
			
			$query .= " card_type='".sanitize($data["card_type"])."', name_on_card='".
						sanitize($data["name_on_card"])."', card_number='".sanitize($data["card_number"])."', expiry='".
						sanitize($data["expiry"])."', cvv='".sanitize($data["cvv"])."', created_on=now(), status = 0";
						
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
			{				
				$lastinsertid = mysqli_insert_id($connection);
				//success response					
				$response = array(
					'status' => "success",
					'card_id' => $lastinsertid,
					'status_message' =>" Card added successfully."
				);			
			}
			
			//failure response
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>" Card adding Failed."
				);
			}	
		}
		else if($data['card_id']!="" ){	

			//TO cancel the CARD
			if($data['card_delete'] !="") {
				$query = "DELETE FROM card_info WHERE card_id='".sanitize($data["card_id"])."'";
				
				if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
					$response = array(
						'status' => "success",
						'status_message' =>" Card deleted successfully."
					);
				}
			}	
			else if($data['card_edit'] !="") {
				$query = "UPDATE card_info SET card_type='".sanitize($data["card_type"])."', name_on_card='".
							sanitize($data["name_on_card"])."', card_number='".sanitize($data["card_number"])."', expiry='".
							sanitize($data["expiry"])."', cvv='".sanitize($data["cvv"])."' WHERE card_id='".sanitize($data["card_id"])."'";
				
				if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
					$response = array(
						'status' => "success",
						'card_id' => $data['card_id'],
						'status_message' =>" Card updated successfully."
					);
				}
				else {
					$response = array(
						'status' => "failure",
						'card_id' => $data['card_id'],
						'status_message' =>" Card update failed."
					);
				}			
			}					
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' =>"Missing Fileds "
			);
		}	
	
	} catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
	}	
	
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		