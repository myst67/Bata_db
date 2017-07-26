<?php
if(!isset($_SESSION)) 
{ 
	session_start(); 
	if(isset($_SESSION['current_table']))
	{
		$currentTable = $_SESSION['current_table'];
	}
	
}

include('model.php'); // call db.class.php
$dbModel = new db();

if($_POST['type'] === 'create')
{
	$validateData = $dbModel->validate($_POST);
	if($validateData['success'] == false)
	{
		echo json_encode(array('status' => false,'message'=> $validateData['msg']));
		
	}else{
		$shoe_name = $_POST['shoe_name'];
		$shoe_category = $_POST['shoe_category'];
		$shoe_color = $_POST['shoe_color'];
		$shoe_size = $_POST['shoe_size'];
		$shoe_price = $_POST['shoe_price'];
		
		$query = $dbModel->execute("INSERT INTO $currentTable (shoe_name,shoe_category,shoe_color,shoe_size,shoe_price) VALUES ('$shoe_name', '$shoe_category', '$shoe_color','$shoe_size','$shoe_price')");
		
		if(!$query)
		{
			echo json_encode(array('status' => false,'message'=> 'Not Able to insert query, please go through the log details..'));
		}else{
			
			$product = $dbModel->fetchOneProduct($query);
			if(!$product)
			{
				echo json_encode(array('status' => false,'message'=> 'Not Able to fetch data from current query, please go through the log details..'));
			}else{
				echo json_encode(array('status' => true,'message'=> $product));
			}
		}
	}
}elseif($_POST['type'] === 'edit')
{
	$editData = Array();
	
	$id = $_POST['id'];
	$editData['shoe_name'] = $_POST['edit_shoe_name'];
	$editData['shoe_category'] = $_POST['edit_shoe_category'];
	$editData['shoe_color'] = $_POST['edit_shoe_color'];
	$editData['shoe_size'] = $_POST['edit_shoe_size'];
	$editData['shoe_price'] = $_POST['edit_shoe_price'];
	
	$shoe_name = $_POST['edit_shoe_name'];
	$shoe_category = $_POST['edit_shoe_category'];
	$shoe_color = $_POST['edit_shoe_color'];
	$shoe_size = $_POST['edit_shoe_size'];
	$shoe_price = $_POST['edit_shoe_price'];
	
	$validateData = $dbModel->validate($editData);
	
	if($validateData['success'] == false)
	{
		echo json_encode(array('status' => false,'message'=> $validateData['msg']));
	}else{
		$query = $dbModel->execute("UPDATE $currentTable SET shoe_name='$shoe_name', shoe_category='$shoe_category', shoe_color='$shoe_color', shoe_size='$shoe_size', shoe_price='$shoe_price' WHERE id='$id'");
		echo json_encode(array('status' => true,'message'=> 'data has been updated successfully.'));
	}
}elseif($_POST['type'] === 'createTable')
{
	$editData = Array();
	
	$table_name = $_POST['table_name'];
	$queryString = "CREATE TABLE IF NOT EXISTS `Bata_Schema`.`$table_name` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `shoe_name` VARCHAR(45) NULL,
		  `shoe_category` VARCHAR(45) NULL,
		  `shoe_color` VARCHAR(45) NULL,
		  `shoe_size` INT(20) NULL,
		  `shoe_price` INT(20) NULL,
		  PRIMARY KEY (`id`))
		ENGINE = InnoDB;";
	$query = $dbModel->execute($queryString);
	
	echo json_encode(array('status' => true,'message'=> 'table has been created successfully.'));
}


?>