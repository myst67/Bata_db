<?php
if(!isset($_SESSION)) 
{ 
	session_start(); 
}
$currentTable = $_SESSION['current_table'];
include('model.php'); // call db.class.php

$dbModel = new db();
if($_POST['type'] == 'edit')
{
	
	if($_POST['id'])
	{
		$productData = $dbModel->fetchOneProduct($_POST['id']);
		
		if(!$productData)
		{
			echo json_encode(array('status' => false,'message'=> 'Not Able to fetch data from current query, please go through the log details..'));
		}else{
			echo json_encode(array('status' => true,'message'=> $productData));
		}
	}
	
}elseif($_POST['type'] == 'delete')
{
	if($_POST['id'])
	{
		$productData = $dbModel->deleteOneProduct($_POST['id']);
		echo json_encode(array('status' => true,'message'=> 'Product was deleted'));
	}
}elseif($_POST['type'] == 'search')
{
	$filter = $_POST['filter'];
	$filterCondition = $_POST['filterCondition'];
	$filterValue = $_POST['filterValue']; 
	$searchProductData = '';
	
	if($filter == 'shoe_size' || $filter == 'shoe_price')
	{
		$searchProductData = $dbModel->searchProductsByValue($filter,$filterCondition,$filterValue);
		
	}elseif($filter == 'shoe_name' || $filter == 'shoe_category'|| $filter == 'shoe_color')
	{
		$query = '';
		switch ($filterCondition) {
			case "like%":
				$query = "SELECT * FROM $currentTable WHERE $filter LIKE '%$filterValue%'";
				break;
			case "like":
				$query = "SELECT * FROM $currentTable WHERE $filter LIKE '$filterValue'";
				break;
			case "not_like":
				$query = "SELECT * FROM $currentTable WHERE $filter NOT LIKE '$filterValue'";
				break;
			case "regexp":
				$query = "SELECT * FROM $currentTable WHERE $filter REGEXP '$filterValue'";
				break;
			case "not_regexp":
				$query = "SELECT * FROM $currentTable WHERE $filter NOT REGEXP '$filterValue'";
				break;
			default:
				echo "Youre not select any query";
		}
		$searchProductData = $dbModel->searchProductsByName($query);
	}
	echo json_encode(array('status' => true,'message'=> $searchProductData));
}


?>