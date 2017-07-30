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

if($_POST['type'] === 'createProduct')
{
	$attrSet = $_POST['attr_set'];
	unset($_POST['attr_set']);
	unset($_POST['type']);
	
	$sku = $attrSet.'-'.substr(time(),6);
	
	$entityId = $dbModel->execute("INSERT INTO eav_entity (attribute_set,sku) VALUES ('$attrSet', '$sku')");
	
	foreach($_POST as $k=>$value)
	{
		$query = "INSERT INTO eav_value (entity_id,attribute_id,value) VALUES ('$entityId', '$k', '$value')";
		$dbModel->execute($query);
	}
	
	echo json_encode(array('status' => true,'message'=> 'product created successfully !'));
	
}elseif($_POST['type'] === 'edit')
{
	$editData =$_POST;
	
	/* echo '<pre>';
	print_r($editData);
	echo '</pre>'; */
	
	$entity_id = $editData['pid'];
	unset($editData['pid']);
	unset($editData['type']);
	
	foreach($editData as $id=>$data)
	{
		$isIdExist = "SELECT value_id from eav_value WHERE attribute_id='$id' AND entity_id='$entity_id'";
		if(!empty($dbModel->getOne($isIdExist)))
		{
			$sql = "UPDATE eav_value SET value='$data' WHERE attribute_id='$id' AND entity_id='$entity_id'";
			$query = $dbModel->execute($sql);
		}else{
			$sql = "INSERT INTO eav_value (attribute_id,value,entity_id) VALUES ('$id', '$data', '$entity_id')";
			$query = $dbModel->execute($sql);
		}
	}
	
	$msg = "Product id: $entity_id has been updated";
	echo json_encode(array('status' => true,'message'=> $msg));
	
}elseif($_POST['type'] === 'createTable')
{
	
	$arr = $_POST;
	
	$attrSet = $arr['attr_set_name'];
	$isSetExist = $dbModel->isIdExistInTable($selct='entity_id',$attrSet,'attribute_set',$table='eav_entity');
	
	if($isSetExist)
	{
		$msg = 'Attribute Set: ('.$attrSet.') is already exist! try with different one!';
		echo json_encode(array('status' => false,'message'=> $msg));
		exit;
	}
	
	$sku = $attrSet.'-'.substr(Time(),5);
	
	$sql = "INSERT INTO eav_entity (attribute_set,sku) VALUES ('$attrSet', '$sku')";
	$entity_id = $dbModel->execute($sql);
	
	unset($arr['attr_set_name']);
	unset($arr['type']);
	
	
	
	$attLen = count($arr)/3;
	$queryString = '';
	for($j=$attLen;$j>=1;$j--)
	{
		$attribute_codee = $arr["tb_code_$j"];
		$attribute_type = $arr["tb_type_$j"];
		$attribute_label = $arr["tb_label_$j"];
		
		$isIdExist = $dbModel->isIdExistInTable($selct='attribute_id',$attribute_codee,$attribute_code='attribute_code',$table='eav_attribute');
		if(!$isIdExist)
		{
			$queryString = "INSERT INTO `eav_attribute` (attribute_set,attribute_code,attribute_type,attribute_label) VALUES ('$attrSet', '$attribute_codee','$attribute_type', '$attribute_label')";
			$dbModel->execute($queryString);
			
		}else{
			$msg = $attribute_codee.' : is already exist! try with different one!';
			echo json_encode(array('status' => false,'message'=> $msg));
			exit;
		}
		
		
	}
	
	echo json_encode(array('status' => true,'message'=> 'table has been created successfully.'));
	
}elseif($_POST['type'] === 'showAttribute')
{
	
	$arr = $_POST;
	$sttrSetName = $arr['set'];
	$attrDetails = $dbModel->getAll("SELECT attribute_id,attribute_code,attribute_type,attribute_label FROM `eav_attribute` WHERE `attribute_set` = '$sttrSetName' ORDER BY `attribute_id` DESC ");
	
	/* echo '<pre>';
	print_r($attrDetails);
	echo '</pre>'; */
	
	echo json_encode(array('status' => true,'data'=> $attrDetails));
}elseif($_POST['type'] === 'editAttribute')
{
	$arr = $_POST;
	$sttrSetName = $arr['set'];
	
	$attrDetails = $dbModel->getAll("SELECT attribute_id,attribute_type,attribute_label FROM `eav_attribute` WHERE `attribute_set` = '$sttrSetName' ORDER BY `attribute_id` DESC ");
	
	$html = "";
	foreach($attrDetails as $att)
	{
		$attId = $att['attribute_id'];
		
		$html .="<tr id='editAttrTr-$attId'><td><input type='text' disabled class='form-control' name='".$att['attribute_id']."' id='attrText-".$att['attribute_id']."' value='".$att['attribute_label']."'></td><td><select disabled required id='attrSelect-".$att['attribute_id']."' name='".$att['attribute_id']."'>";
		
		if($att['attribute_type'] == 'text')
		{
			$html .="<option selected value='text'>Text</option><option value='number'>Number</option>";
		}else{
			$html .="<option value='text'>Text</option><option selected value='number'>Number</option>";
		}
		
		$html .="</select></td><td><p style='float:left' title='Edit'><a href='javascript:void(0)' id='editAttribute-$attId' class='' onclick='editProductAttribute($attId)' ><span class='glyphicon glyphicon-pencil'></span></a></p><p style='display:none;margin-left: 14px;' id='updateAttr-$attId'><a href='javascript:void(0)' onclick='updateAttribute($attId)'>Update</a></p>
		</td><td><p title='Delete'><a href='javascript:void(0)' id='deleteAttribute-$attId' class='' onclick='deleteProductAttribute($attId)' ><span class='glyphicon glyphicon-trash'></span></a></p></td></tr>";
	}
	
	echo json_encode(array('status' => true,'msg'=> $html));
}


?>