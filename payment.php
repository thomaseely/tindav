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
	
	$db = new dbObj();
	$connection =  $db->getConnstring();
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	//print_r($data);exit;	
	//sitter_id has to send by ghouse from app
	if($data["token"] !='' && $data["sitter_id"] !='' && $data["payment_method"] !='' && $data["card_holder_name"]!='' && $data["card_type"] !='' && $data["card_number"] !='' 
	&& $data["card_expiry"] !='' && $data["cvv"] !='' && $data["gateway_used"] !='' && $data["gateway_response"] !='' && $data["amount"] !='') 
		{
			$user_id = isValidToken($data["token"]);	
			
			if($user_id !== false) {
				
				$query = "INSERT INTO transaction_details SET user_id='".$user_id."', sitter_id='".
							$data["sitter_id"]."', payment_method='".$data["payment_method"]."', card_holder_name='".
							$data["card_holder_name"]."', card_type='".$data["card_type"]."', card_number='".
							$data["card_number"]."', card_expiry='".$data["card_expiry"]."', cvv='".
							$data["cvv"]."', gateway_used='".$data["gateway_used"]."', gateway_response='".
							$data["gateway_response"]."', amount='".$data["amount"]."', paid_on=now(), status='ok'";
			
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
				'status_message' => "Missing Fileds on Transaction."
			);
		}	
		
	header('Content-Type: application/json');
	echo json_encode($response);	
	
?>
		