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
	$arr = $_POST;
	unset($arr['type']);
	$dbModel->createProduct($arr,$pid=null);
	echo json_encode(array('status' => true,'message'=> 'product created successfully !'));
}elseif($_POST['type'] === 'viewProduct')
{	
	$pid = $_POST['_pid'];
	$sql = "SELECT att_table.att_id,value_table.value_id,att_table.attribute_code as Property, value_table.value_type as Type, value_table.value_item as AttVaue FROM entity_table JOIN att_table ON entity_table.att_id = att_table.att_id JOIN value_table ON entity_table.value_id = value_table.value_id WHERE entity_table.pro_id = $pid";
	
	$products = $dbModel->getAll($sql);
	if(empty($products))
	{
		echo json_encode(array('status' => false,'message'=> 'product not present by this product id!!'));
	}else{
		echo json_encode(array('status' => true,'message'=> $products));
	}
}elseif($_POST['type'] === 'search')
{
	
	$arr = $_POST;
	unset($arr['type']);
	
	
	
	$products = Array();
	$attLen = count($arr)/3;
	for($i=1;$i<=$attLen;$i++)
	{
		$queryString = '';
		$queryString .= "SELECT entity_table.pro_id as Pid FROM entity_table JOIN att_table ON entity_table.att_id = att_table.att_id JOIN value_table ON entity_table.value_id = value_table.value_id WHERE";
		$att_code = $arr["search_by_property_attr_$i"];
		$att_condition = $arr["search_by_property_condition_$i"];
		$att_value = $arr["search_by_property_value_$i"];
		/* if($i>1)
		{
			$queryString .= " AND ";
		} */
		switch ($att_condition) {
			case "gthen":
				$att_value = (int)$att_value;
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item > $att_value)";
				break;
			case "lthen":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item < $att_value)";
				break;
			case "gtheneq":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item >= $att_value)";
				break;
			case "ltheneq":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item <= $att_value)";
				break;
			case "eq":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item = $att_value)";
				break;
			case "neq":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'number' AND value_table.value_item != $att_value)";
				break;
			case "like":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'string' AND value_table.value_item LIKE '$att_value')";
				break;
			case "like%":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%' AND value_table.value_type = 'string' AND value_table.value_item LIKE '%$att_value%')";
				break;
			case "any":
				$queryString .= " (att_table.attribute_code LIKE '%$att_code%')";
				break;
		}
		$products[] = $dbModel->getAll($queryString);
	}
	$pids = array();
	$dProducts = array();
	$ddProducts = array();
	if(count($products) == 1)
	{
		$products = $products[0];
		
		echo json_encode(array('status' => true,'message'=> $products));
		
	}else if(count($products) > 1){
		
		for($i=0;$i<count($products);$i++)
		{
			for($j=0;$j<count($products[$i]);$j++)
			{
				$pids[] = $products[$i][$j]['Pid'];
			}
		}
		
		$uniquePids = array_unique($pids);
		$dProducts = array_diff_assoc($pids, $uniquePids);
		foreach($dProducts as $_p)
		{
			$ddProducts[]['Pid'] = $_p;
		}
		echo json_encode(array('status' => true,'message'=> $ddProducts));
	}else{
		echo json_encode(array('status' => false,'message'=> 'No Products found !!'));
	}
	
}

?>