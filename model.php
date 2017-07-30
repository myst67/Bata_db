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
	
	
	function fetchOneProduct($id)
	{
		$product = $this->getOne("SELECT * FROM $this->currentTable WHERE id = '$id'");
		return $product;
	}
	
	function getAttributeByProductId($id)
	{
		$sql = "SELECT ea.attribute_id as attribute_id,ea.attribute_type as attribute_type,ea.attribute_code as attribute_code, ea.attribute_label as product_attribute, ev.value as prodcut_data FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.entity_id = ev.entity_id) where en.entity_id = '$id'";
		$attributes = $this->getAll($sql);
		
		return $attributes;
	}
	
	function searchProductsByNumber($filterCode,$filterCondition,$filterValue)
	{
		$sql = "SELECT en.entity_id as product_id FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.entity_id = ev.entity_id) where ea.attribute_code='$filterCode' AND ev.value $filterCondition '$filterValue'";
		
		$products = $this->getAll($sql);
		if(empty($products))
		{
			$result = array("emptyResult"=>true);
			return $result;
		}else{
			$productsArray = $this->searchByProductId($products);
			return $productsArray;
		}
	}
	
	function searchByProductId($products)
	{
		$searchByProductIdSql = 'SELECT en.entity_id as product_id, en.sku as sku, en.attribute_set as attribute_set, ea.attribute_label as product_attribute, ev.value as prodcut_data FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.entity_id = ev.entity_id) where ';
		
		$numItems = count($products);
		$i = 0;
		
		foreach($products as $_product)
		{
			$pid = $_product['product_id'];
			if(++$i === $numItems) 
			{
				$searchByProductIdSql .= "en.entity_id = '$pid'";
			}else{
				$searchByProductIdSql .= "en.entity_id = '$pid' OR ";
			}
		}
		$productsData = $this->getAll($searchByProductIdSql);
		
		return $productsData;
	}
	
	
	function searchProductsByName($filterCode,$filterCondition,$filterValue)
	{	
	
		$query = '';
		$sql = "SELECT en.entity_id as product_id FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.entity_id = ev.entity_id) where ea.attribute_code='$filterCode' AND ev.value";
		
		switch ($filterCondition) {
			case "like%":
				$sql .= " LIKE '%$filterValue%'";
				break;
			case "like":
				$sql .= " LIKE '$filterValue'";
				break;
			case "not_like":
				$sql .= " NOT LIKE '$filterValue'";
				break;
			case "regexp":
				$sql .= " REGEXP '$filterValue'";
				break;
			case "not_regexp":
				$sql .= " NOT REGEXP '$filterValue'";
				break;
			default:
				echo "Youre not select any query";
		}
		// echo $sql;
		
		$products = $this->getAll($sql);
		if(empty($products))
		{
			$result = array("emptyResult"=>true);
			return $result;
		}else{
			$productsArray = $this->searchByProductId($products);
			return $productsArray;
		}
		
		
	}
	
	function deleteOneProduct($id)
	{
		$sqlEntity = "DELETE FROM eav_entity WHERE entity_id = '$id'";
		$sqlValue = "DELETE FROM eav_value WHERE entity_id = '$id'";
		$this->execute($sqlEntity);
		$this->execute($sqlValue);
	}
	
	function validate($postdata)
	{
		$result = Array();
		$shoe_name = $postdata['shoe_name'];
		$shoe_color = $postdata['shoe_color'];
		$shoe_color = $postdata['shoe_color'];
		$shoe_size = $postdata['shoe_size'];
		$shoe_price = $postdata['shoe_price'];
		
		$result['success'] = true;
		if($shoe_name == null || empty($shoe_name))
		{
			$result['success'] = false;
			$result['msg'] = 'Shoe name is required field';
		}
		if($shoe_color == null || empty($shoe_color))
		{
			$result['success'] = false;
			$result['msg'] = 'shoe category is required field';
		}
		if($shoe_size == null || empty($shoe_size))
		{
			$result['success'] = false;
			$result['msg'] = 'Shoe size is required field';
		}
		if($shoe_price == null || empty($shoe_price))
		{
			$result['success'] = false;
			$result['msg'] = 'Shoe price is required field';
		}
		
		return $result;
	}
	
	function groupApply($data,$groupBy)
	{
		$grouped_types = Array();
		foreach($data as $set){
			$grouped_types[$set[$groupBy]][] = $set;
		}
		return $grouped_types;
	}
	
	function getProductDetailsInEachSet($set)
	{
		$query = "SELECT en.entity_id as product_id, ea.attribute_code as attribute_code, ea.attribute_label as product_attribute, ev.value as prodcut_data FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.entity_id = ev.entity_id) AND ea.attribute_set='$set'";
		
		$products = $this->getAll($query);
		return $products;
	}
	
	function getAllAttributeDetails()
	{
		$query = "SELECT distinct attribute_code, attribute_type, attribute_label FROM `eav_attribute`";
		
		$attributes = $this->getAll($query);
		return $attributes;
	}
	
	function getAttributeLengthBySet($set)
	{
		$query = "SELECT count(attribute_id) as count FROM `eav_attribute` where attribute_set='$set'";
		
		$count = $this->getOne($query);
		return $count['count'];
	}
	
	function getTotalAttributeSet()
	{
		$query = "SELECT distinct attribute_set FROM `eav_attribute`";
		// $query = "SELECT distinct attribute_code, attribute_type, attribute_label FROM `eav_attribute`";
		
		$attributeSet = $this->getAll($query);
		
		return $attributeSet;
	}
	function getAttributesNotValue($setId,$existAttributes)
	{
		$arr = '"'.implode('","', $existAttributes).'"';
		$sql = "select * from eav_attribute where attribute_code NOT IN ($arr) AND attribute_set = '$setId'";
		$extraAttributeSet = $this->getAll($sql);
		
		return $extraAttributeSet;
	}
	
	function getAllProducts()
	{
		$query = "SELECT en.entity_id as product_id, en.sku as sku, ea.attribute_set as set_id, ea.attribute_label as product_attribute, ev.value as prodcut_data FROM eav_value AS ev JOIN eav_attribute AS ea ON (ea.attribute_id = ev.attribute_id) JOIN eav_entity AS en ON (en.attribute_set = ea.attribute_set) GROUP BY en.entity_id, ea.attribute_label";
		
		
		$product = $this->getAll($query);
		$sroupeSetProducts = $this->groupApply($product,'set_id');
		return $sroupeSetProducts;
	}
	
	function isIdExistInTable($selct,$value,$attributeId,$table)
	{
		
		$sql = "SELECT $selct from $table WHERE $attributeId='$value'";
		if(!empty($this->getOne($sql)))
		{
			return true;
		}else{
			return false;
		}
	}
	
	function createInitialTables()
	{
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`eav_entity` (
		  `entity_id` INT NOT NULL AUTO_INCREMENT,
		  `attribute_set` VARCHAR(45) NOT NULL,
		  `sku` VARCHAR(45) DEFAULT NULL,
		  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  INDEX PRODUCT_ATTRIBUTE_SET_ID USING BTREE (attribute_set),
		  INDEX PRODUCT_SKU USING BTREE (sku),
		  UNIQUE(sku),
		  PRIMARY KEY (`entity_id`)
		  )
		ENGINE = InnoDB");
	
	
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`eav_value` (
		  `value_id` INT NOT NULL AUTO_INCREMENT,
		  `entity_id` INT(10) NOT NULL,
		  `attribute_id` INT(10) NOT NULL,
		  `value` VARCHAR(255) DEFAULT NULL,
		  INDEX PRODUCT_ATTRIBUTE_ID USING BTREE (attribute_id),
		  INDEX PRODUCT_ENTITY_ID USING BTREE (entity_id),
		  PRIMARY KEY (`value_id`)
		  )
		ENGINE = InnoDB");
		
		$this->execute("CREATE TABLE IF NOT EXISTS `Bata_Schema`.`eav_attribute` (
		  `attribute_id` INT NOT NULL AUTO_INCREMENT,
		  `attribute_set` VARCHAR(45) NOT NULL,
		  `attribute_code` VARCHAR(45) NOT NULL,
		  `attribute_type` VARCHAR(45) NOT NULL,
		  `attribute_label` VARCHAR(45) NULL,
		  INDEX PRODUCT_ATTRIBUTE_SET_CODE USING BTREE (attribute_set,attribute_code),
		  INDEX PRODUCT_ATTRIBUTE_SET_LABEL USING BTREE (attribute_set,attribute_label),
		  INDEX PRODUCT_ATTRIBUTE_SET_ID USING BTREE (attribute_set),
		  UNIQUE(attribute_code),
		  PRIMARY KEY (`attribute_id`)
		  )
		ENGINE = InnoDB");
	}
}
