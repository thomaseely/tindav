<?php	

	//check username exists
	function isUsernameExists($username, $user_type){
		global $connection;
		
		if($user_type == "parent") {
			//Check in parents table
			$query = "SELECT parent_id FROM parents WHERE username='".$username."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row['parent_id']!='') return true;
			return false;	
		}
	
		if($user_type == "sitter") {
			//Check in sitters table
			$query = "SELECT sitter_id FROM sitters WHERE username='".$username."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row['sitter_id']!='') return true;		
			return false;		
		}
	}
	
	function isValidToken($token, $user_type){
		global $connection;
		if($user_type == "parent") {
			$query = "SELECT * FROM parents WHERE token='".$token."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row['parent_id']!='') return $row;
		}
		
		if($user_type == "sitter") {
			$query = "SELECT * FROM sitters WHERE token='".$token."' LIMIT 1";						
			$result = mysqli_query($connection,$query) or die("Error:".mysqli_error($connection));			
			// Associative array
			$row = mysqli_fetch_assoc($result);
			if($row['sitter_id']!='') return $row;
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
		if($input !=""){
			return mysqli_real_escape_string(trim($input));
		}
		return false;
	}
	
?>