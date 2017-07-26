<?php
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
}


?>