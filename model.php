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
	
	function showTables(){
		
		$cnx = $this->conn;
		if (!$cnx||$this->status_fatal) {
			return null;
		}
		
		$cur = mysql_query("Show tables");
		$tables = Array();
		$i = 0;
		while ($row = mysql_fetch_array($cur, MYSQL_ASSOC)) {
			$tables[$i] = $row;
			$i++;
		}
		
		return $tables;
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
	
	function searchProductsByValue($filter,$filterCondition,$filterValue)
	{
		$product = $this->getAll("SELECT * FROM $this->currentTable WHERE $filter $filterCondition $filterValue");
		return $product;
	}
	
	function searchProductsByName($query)
	{
		$product = $this->getAll($query);
		return $product;
	}
	
	function deleteOneProduct($id)
	{
		$product = $this->execute("DELETE FROM $this->currentTable WHERE id = '$id'");
		return $product;
	}
	
	function validate($postdata)
	{
		$result = Array();
		$shoe_name = $postdata['shoe_name'];
		$shoe_category = $postdata['shoe_category'];
		$shoe_color = $postdata['shoe_color'];
		$shoe_size = $postdata['shoe_size'];
		$shoe_price = $postdata['shoe_price'];
		
		$result['success'] = true;
		if($shoe_name == null || empty($shoe_name))
		{
			$result['success'] = false;
			$result['msg'] = 'Shoe name is required field';
		}
		if($shoe_category == null || empty($shoe_category))
		{
			$result['success'] = false;
			$result['msg'] = 'shoe category is required field';
		}
		if($shoe_color == null || empty($shoe_color))
		{
			$result['success'] = false;
			$result['msg'] = 'Shoe color is required field';
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
}
