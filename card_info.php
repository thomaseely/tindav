<?php
	/**
	Client: Tindav.com
	API name:Card info
	Filename: card_info.php
	
	Params: parent_id or sitter_id, cardinfo
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 31-08-2018
	Description: This API is used to add/edit/delete card informations .
	*/
	
	require("../connection.php");
	require("functions.php");
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);

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
		
		$query .= " card_type='".$data["card_type"]."', name_on_card='".
					$data["name_on_card"]."', card_number='".$data["card_number"]."', expiry='".
					$data["expiry"]."', cvv='".$data["cvv"]."', created_on=now(), status = 0";
					
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
			$query = "DELETE FROM card_info WHERE card_id='".$data["card_id"]."'";
			
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
				$response = array(
					'status' => "success",
					'status_message' =>" Card deleted successfully."
				);
			}
		}	
		else if($data['card_edit'] !="") {
			$query = "UPDATE card_info SET card_type='".$data["card_type"]."', name_on_card='".
						$data["name_on_card"]."', card_number='".$data["card_number"]."', expiry='".
					    $data["expiry"]."', cvv='".$data["cvv"]."' WHERE card_id='".$data["card_id"]."'";
			
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
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		