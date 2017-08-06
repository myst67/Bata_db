<?php

include('model.php'); // call db.class.php

$dbModel = new db();

if($_POST['type'] == 'editProducte')
{
	
	
	$arr = $_POST;
	$pid = $arr['editProducteId'];
	unset($arr['type']);
	unset($arr['editProducteId']);
	unset($arr['counter']);
	unset($arr['counterDesc']);
	$dbModel->removeProductDetails($pid);
	$dbModel->createProduct($arr,$pid);
	
	echo json_encode(array('status' => true,'message'=> 'Product has been updated..'));
	
}else if($_POST['type']==='removeAttribute'){
	
	$pid = $_POST['_pid'];
	$attId = $_POST['_attid'];
	$valId = $_POST['_valid'];
	
	$entityQuery = "DELETE FROM entity_table WHERE (pro_id = $pid AND att_id = $attId AND value_id = $valId )";
	$dbModel->execute($entityQuery);
	$attQuery = "DELETE FROM att_table WHERE att_id = $attId";
	$dbModel->execute($attQuery);
	$valueQuery = "DELETE FROM value_table WHERE value_id = $valId";
	$dbModel->execute($valueQuery);
	
	echo json_encode(array('status' => true,'message'=> 'Attribute has been removed..'));
	
}else if($_POST['type']==='deleteProduct'){
	
	$pid = $_POST['id'];
	$dbModel->removeProductDetails($pid);
	
	echo json_encode(array('status' => true,'message'=> 'Product has been deleted..'));
}

?>