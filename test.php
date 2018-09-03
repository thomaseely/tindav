<?php
	// Include the wp-load.php so that we can use username_exists() function in WordPress API
 
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	//$_SERVER[REQUEST_URI]
 
	//require_once( $actual_link.'/tindev/wp-load.php' );


  /*
  // return all our data to an AJAX call
  echo json_encode($data, JSON_PRETTY_PRINT);
  */
  
  class TinDavAPI {
    private $db;

    // Constructor - open DB connection
    function __construct() {
        $this->db = new mysqli('localhost', 'root', '', 'tindav');
        $this->db->autocommit(FALSE);
    }

    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }

    // Main method to redeem a code
    function testselect() {
        // Print all codes in database
        $stmt = $this->db->prepare('SELECT option_id,option_name FROM wp_options');
        $stmt->execute();
        $stmt->bind_result($option_id,$option_name);
        //while ($stmt->fetch()) {
            //echo " $option_id,uses remaining! $option_name.<br>";
       // }
	    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)){
			$category_id=$row_category['option_id'];
		}
        $stmt->close();
    }
}

$obj = new TinDavAPI();

$res = $obj->testselect();
echo json_encode($res);


?>