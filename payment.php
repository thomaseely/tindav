<?php
	/**
	Client: Tindav.com
	API name: payment
	Filename: payment.php
	//provider mandatory parameters
	Params:	token, sitter_id, card_type, name, number, cvv, amount
	//seeker mandatory parameters
	
	API vertion: 1.0
	Created By: Thomas
	Created On: 28-08-2018
	Modified On: 29-08-2018
	Description: This API is used to add payment information .
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
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	//print_r($data);exit;	
	//sitter_id has to send by ghouse from app
	if($data["token"] !='' && $data["sitter_id"] !='' && $data["payment_method"] !='' && $data["card_holder_name"]!='' && $data["card_type"] !='' && $data["card_number"] !='' 
	&& $data["card_expiry"] !='' && $data["cvv"] !='' && $data["gateway_used"] !='' && $data["gateway_response"] !='' && $data["amount"] !='') 
		{
			$row = isValidToken($data["token"]);	
			
			if($row !== false) {
				
				$query = "INSERT INTO transaction_details SET sitter_id='".
							sanitize($data["sitter_id"])."', payment_method='".sanitize($data["payment_method"])."', card_holder_name='".
							sanitize($data["card_holder_name"])."', card_type='".sanitize($data["card_type"])."', card_number='".
							sanitize($data["card_number"])."', card_expiry='".sanitize($data["card_expiry"])."', cvv='".
							sanitize($data["cvv"])."', gateway_used='".sanitize($data["gateway_used"])."', gateway_response='".
							sanitize($data["gateway_response"])."', amount='".sanitize($data["amount"])."', paid_on=now(), status='ok'";
			
				if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection)))
				{		
					$lastinsertid = mysqli_insert_id($connection);
					$response = array(
						'status' => 'success',	
						'transaction_id' => $lastinsertid,	
						'status_message' => "Transaction details successfully inserted."
					);						
				}
				else
				{
					$response = array(
						'status' => "failure",
						'status_message' =>"Transaction Insertion Failed."
					);
				}
			}
			else
			{
				$response = array(
					'status' => "failure",
					'status_message' =>"Token is null or invalid."
				);
			}	
			
			
		}
		else
		{
			$response = array(
				'status' => "failure",
				'status_message' => "Missing Fileds on Transaction details."
			);
		}	
	} catch(Exception $e){
		echo 'Exception: ' .$e->getMessage();
	}
	
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		