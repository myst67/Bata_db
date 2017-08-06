<?php
if(!isset($_SESSION)) 
{ 
	session_start(); 
}

class db {
	private $conn;
	private $host;
	private $user;
	private $password;
	private $baseName;
	private $port;
	private $Debug;
	private $currentTable;
	
	function __construct($params=array()) {
		$this->conn = false;
		$this->host = 'localhost'; 
		$this->user = 'root';
		$this->password = ''; 
		$this->baseName = 'bata_schema';
		$this->port = '3306';
		$this->debug = true;
		if(isset($_SESSION['current_table'])) $this->currentTable = $_SESSION['current_table'];
		
		$this->connect();
		
	}
 
	function __destruct() {
		$this->disconnect();
	}
	
	function connect() {
		if (!$this->conn) {
			$this->conn = mysql_connect($this->host, $this->user, $this->password);	
			mysql_select_db($this->baseName, $this->conn); 
			mysql_set_charset('utf8',$this->conn);
			
			if (!$this->conn) {
				$this->status_fatal = true;
				echo 'Connection BDD failed';
				die();
			} 
			else {
				$this->status_fatal = false;
			}
		}
 
		return $this->conn;
	}
 
	function disconnect() {
		if ($this->conn) {
			@pg_close($this->conn);
		}
	}
	
	function getOne($query) {
		$cnx = $this->conn;
		if (!$cnx || $this->status_fatal) {
			echo 'GetOne -> Connection BDD failed';
			die();
		}
 
		$cur = @mysql_query($query, $cnx);
 
		if ($cur == FALSE) {		
			$errorMessage = @pg_last_error($cnx);
			$this->handleError($query, $errorMessage);
		} 
		else {
			$this->Error=FALSE;
			$this->BadQuery="";
			$tmp = mysql_fetch_array($cur, MYSQL_ASSOC);
			
			$return = $tmp;
		}
 
		@mysql_free_result($cur);
		return $return;
	}
	
	function getAll($query) {
		$cnx = $this->conn;
		if (!$cnx || $this->status_fatal) {
			echo 'GetAll -> Connection BDD failed';
			die();
		}
		
		mysql_query("SET NAMES 'utf8'");
		$cur = mysql_query($query);
		$return = array();
		
		while($data = mysql_fetch_assoc($cur)) { 
			array_push($return, $data);
		} 
 
		return $return;
	}
	
	
	function execute($query,$use_slave=false) {
		$cnx = $this->conn;
		if (!$cnx||$this->status_fatal) {
			return null;
		}
 
		$cur = @mysql_query($query, $cnx);
 
		if ($cur == FALSE) {
			$ErrorMessage = @mysql_last_error($cnx);
			$this->handleError($query, $ErrorMessage);
		}
		else {
			$this->Error=FALSE;
			$this->BadQuery="";
			$this->NumRows = mysql_affected_rows();
			$insertedid = mysql_insert_id();
			return $insertedid;
		}
		@mysql_free_result($cur);
	}
	
	function handleError($query, $str_erreur) {
		$this->Error = TRUE;
		$this->BadQuery = $query;
		if ($this->Debug) {
			echo "Query : ".$query."<br>";
			echo "Error : ".$str_erreur."<br>";
		}
	}
	
	function createInitialTables()
	{
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`att_table` (
		  `att_id` INT NOT NULL AUTO_INCREMENT,
		  `attribute_code` VARCHAR(50) NOT NULL,
		   INDEX PRODUCT_ATTRIBUTE_VALUE USING BTREE (attribute_code(6)),
		  PRIMARY KEY (`att_id`)
		  )
		ENGINE = InnoDB");
	
	
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`value_table` (
		  `value_id` INT NOT NULL AUTO_INCREMENT,
		  `value_type` varchar(50) NOT NULL,
		  `value_item` varchar(50) NOT NULL,
		  INDEX PRODUCT_TYPE_VALUE USING BTREE (value_type,value_item),
		  PRIMARY KEY (`value_id`)
		  )
		ENGINE = InnoDB");
		
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`entity_table` (
		  `en_id` INT NOT NULL AUTO_INCREMENT,
		  `pro_id` INT(10) NOT NULL,
		  `att_id` INT(10) NOT NULL,
		  `value_id` INT(10) NOT NULL,
		  UNIQUE(`en_id`),
		  PRIMARY KEY(`pro_id`,`att_id`,`value_id`)
		  )
		ENGINE = InnoDB");
	} 
	
	function getAllProducts()
	{
		$query = "SELECT distinct entity_table.pro_id as pid FROM entity_table";
		
		$products = $this->getAll($query);
		
		/* echo '<pre>' ;
		print_r($products);
		echo '</pre>' ; */
		
		return $products;
	}
	
	function removeProductDetails($pid)
	{
		$attQuery = "SELECT att_id FROM entity_table WHERE pro_id = $pid";
		$existAttributes = $this->getAll($attQuery);
		$implodeAttArray = Array();
		foreach($existAttributes as $attr)
		{
			$implodeAttArray[] = $attr['att_id'];
		}
		$arr = '"'.implode('","', $implodeAttArray).'"';//implode('","', $implodeArray);
		
		$sql = "DELETE from att_table where att_id IN ($arr)";
		$this->execute($sql); 
		
		$valueQuery = "SELECT value_id FROM entity_table WHERE pro_id = $pid";
		$existAttributesValue = $this->getAll($valueQuery);
		$implodeValueArray = Array();
		foreach($existAttributesValue as $attr_value)
		{
			$implodeValueArray[] = $attr_value['value_id'];
		}
		$arrValue = '"'.implode('","', $implodeValueArray).'"';//implode('","', $implodeArray);
		
		$sql = "DELETE from value_table where value_id IN ($arrValue)";
		$this->execute($sql); 
		
		$entityQuery = "DELETE FROM entity_table WHERE pro_id = $pid";
		$this->execute($entityQuery);
	}
	
	function createProduct($arr,$pid)
	{
		$attLen = count($arr)/3;
		$queryString = '';
		if(empty($pid))
		{
			$pid = substr(Time(),5);
		}
		
		for($j=$attLen;$j>=1;$j--)
		{
			$attribute_code = $arr["property_code_$j"];
			$attribute_type = $arr["property_type_$j"];
			$attribute_value = $arr["property_value_$j"];
		
			$queryAttString = "INSERT INTO `att_table` (attribute_code) VALUES ('$attribute_code')";
			$att_id = $this->execute($queryAttString);
			
			$queryValString = "INSERT INTO `value_table` (value_type,value_item) VALUES ('$attribute_type','$attribute_value')";
			$value_id = $this->execute($queryValString);
			
			$queryEnString = "INSERT INTO `entity_table` (pro_id,att_id,value_id) VALUES ('$pid','$att_id','$value_id')";
			$en_id = $this->execute($queryEnString);
			
		}
	}
}
