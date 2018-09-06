<?php	
	//update new token after login
	function updateToken($token, $tbl, $col, $id) {
		global $connection;
		
		$query = "UPDATE $tbl SET token ='".$token."' WHERE $col ='".$id."' ";
		
		if(mysqli_query($connection, $query) or die("Error:".mysqli_error($connection))) {
			return true;
		}
		return false;
	}

	//check username exists
	function isUsernameExists($username, $user_type){
		global $connection;
		
		$tbl = ($user_type === "sitter")? "sitters":"parents";
		$col = ($user_type === "parent")? "parent_id":"sitter_id";	
			
		if($username != "") {
			$query = "SELECT $col FROM $tbl WHERE username='".sanitize($username)."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row["$col"]!='') return true;			
		}
		return false;	
	}
	
	function isValidToken($token, $user_type){
		global $connection;
		
		if($token !='') {
			$tbl = ($user_type === "sitter")? "sitters":"parents";
			$col = ($user_type === "parent")? "parent_id":"sitter_id";	
			
			$query = "SELECT * FROM $tbl WHERE token='".sanitize($token)."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row["$col"]!='') return $row;
		}
		return false;
	}
	
	function isThereChildOfParent($parent_id){
		
		global $connection;
		
		//Check in parents table
		$query = "SELECT parent_id FROM child_info WHERE parent_id='".$parent_id."' LIMIT 1";						
		$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
		// Associative array
		$row = mysqli_fetch_assoc($result);
		if($row['parent_id']!='') return true;
		return false;
	}
	
	function isZipcodeAvailable($zipcode){
		
		global $connection;
		
		//Check in parents table
		$query = "SELECT zipcode FROM address_info WHERE zipcode='".$zipcode."' LIMIT 1";						
		$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
		// Associative array
		$row = mysqli_fetch_assoc($result);
		if($row['zipcode']!='') return true;
		return false;
	}
	
	function sanitize($input){
		global $connection;
		
		if($input !=""){
			return $connection->real_escape_string(trim($input));
		}
		return false;
	}
	
	function isValidSession() {
		$now = time();
		if ($now > $_SESSION['expire']) {
			session_destroy();
			echo "Your session has expired!";
			exit;
		}
		return true;
	}
	
?>