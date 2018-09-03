<?php
	/**
	Client: Tindav.com
	API name:Address info
	Filename: address_info.php
	//provider mandatory parameters
	Params: parent_id or sitter_id, address information
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 29-08-2018
	Description: This API is used to add card informations .
	*/
	
	require("../connection.php");
	require("functions.php");
	
	//DB CONNECTION
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	//GET RAW INPUT
	$data = json_decode(file_get_contents('php://input'), true);

	//CHECK PARAMS
	if(($data["parent_id"] !='' || $data["sitter_id"] !='') && $data["address_add"] !='' ) 
	{		
		
		$query = "INSERT INTO address_info SET ";

		if($data['parent_id']!=""){
			$query .=" parent_id='".$data["parent_id"]."'";
		}
		if($data['sitter_id']!=""){
			$query .=" sitter_id='".$data["sitter_id"]."'";
		}
		
		$query .= ", address='".$data["address"]."', city='".
					$data["city"]."', state='".$data["state"]."', country='".
					$data["country"]."', zipcode='".$data["zipcode"]."', created_on=now(), status = 0";
					
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
		{				
			$lastinsertid = mysqli_insert_id($connection);
			//success response					
			$response = array(
				'status' => "success",
				'address_id' => $lastinsertid,
				'status_message' =>" Address added successfully."
			);
			
		}
		
		//failure response
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' =>" Address adding Failed."
			);
		}	
	}
	else if($data['address_id']!="" ){	

		//TO cancel the reviews make status to 1
		if($data['address_delete'] !="") {
			$query = "DELETE FROM address_info WHERE address_id='".$data["address_id"]."'";			
			
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
				$response = array(
					'status' => "success",
					'status_message' =>" Address deleted successfully."
				);
			}
		}	
		else if($data['address_edit'] !="") {
			$query = "UPDATE address_info SET address='".$data["address"]."', city='".
						$data["city"]."', state='".$data["state"]."', country='".
					    $data["country"]."', zipcode='".$data["zipcode"]."' 
						WHERE address_id='".$data["address_id"]."'";			
			
		}
		else {//make address to current address , so set status=1 for current, 0 old address
			$query = "UPDATE address_info SET status='1' WHERE address_id='".$data["address_id"]."'";
			
			if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))){
				$response = array(
					'status' => "success",
					'address_id' => $data['address_id'],
					'status_message' =>" Address activated successfully."
				);
			}
		}		
		
	}
	else
	{
		$response = array(
			'status' => "failure",
			'status_message' =>"Missing Fileds pass address_add/edit/delete accordingly "
		);
	}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		