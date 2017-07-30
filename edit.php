<?php

include('model.php'); // call db.class.php

$dbModel = new db();
if($_POST['type'] == 'edit')
{
	$id = $_POST['id'];
	$set = $_POST['setId'];
	if($id)
	{
		$attributesData = $dbModel->getAttributeByProductId($id);
		$totalAttr = Array();
		if(!empty($attributesData))
		{
			$html = '';
			$arrFilled = Array();
			foreach($attributesData as $attr)
			{
				$arrFilled[] = $attr['attribute_code'];
				
				$html .= "<div class='form-group'>";
				$html .= "<label for='".$attr['attribute_id']."'>".$attr['product_attribute']."</label>";
				$html .= "<input class='form-control' id='".$attr['attribute_id']."' value='".$attr['prodcut_data']."' name='".$attr['attribute_id']."' type='".$attr['attribute_type']."'>";
			}
			
			if(!empty($arrFilled))
			{
				
				$arrayNotAttrValue = $dbModel->getAttributesNotValue($set,$arrFilled);
				/* echo '<pre>';
				print_r($arrayNotAttrValue);
				echo '</pre>'; */
				foreach($arrayNotAttrValue as $ex)
				{
					$html .= "<div class='form-group'>";
					$html .= "<label for='".$ex['attribute_id']."'>".$ex['attribute_label']."</label>";
					$html .= "<input class='form-control' id='".$ex['attribute_id']."' value='' name='".$ex['attribute_id']."' type='".$ex['attribute_type']."'>";
				}
			}
			
			$html .= "</div>";
			$html .= "<input type='hidden' id='editPid' value='$id' name='pid'>";
			$html .= "<input type='hidden' name='type' value='edit'>";
			echo json_encode(array('status' => true,'message'=> $html));
		}else{
			echo json_encode(array('status' => false,'message'=> 'Not Able to fetch data from current query, please go through the log details..'));
		}
	}
	
}elseif($_POST['type'] == 'delete')
{
	$deleteId = $_POST['id'];
	
	if($deleteId)
	{
		$dbModel->deleteOneProduct($deleteId);
		echo json_encode(array('status' => true,'message'=> 'Product was deleted'));
	}
}elseif($_POST['type'] == 'search')
{
	$filter = explode("|",$_POST['filter']);
	$filterCode = $filter[0];
	$filterType = $filter[1];
	$filterCondition = $_POST['filterCondition'];
	$filterValue = $_POST['filterValue'];
	
	$searchProductData = '';
	
	if($filterType == 'number')
	{
		$searchProductData = $dbModel->searchProductsByNumber($filterCode,$filterCondition,$filterValue);
		
	}elseif($filterType == 'text')
	{
		$searchProductData = $dbModel->searchProductsByName($filterCode,$filterCondition,$filterValue);
	}
	
	if(isset($searchProductData["emptyResult"]) && $searchProductData["emptyResult"] == true)
	{
		echo json_encode(array('status' => true,'message'=> 'No searchable products !!'));
	}else{
		$groupedProducts = $dbModel->groupApply($searchProductData,'sku');
		$html = '<h3>Search Result</h3>';
		foreach($groupedProducts as $id=>$products)
		{
			$html .="<div class='col-xs-18 col-sm-4 col-md-3'>";
			$html .="<ul>";
			$html .= "<li style='display: inline;float: left;width: 100%;'><strong>Product sku: </strong>".$id."</li>";
			
			foreach($products as $product)
			{
				$html .= "<li style='display: inline;float: left;width: 100%;'><strong>".$product['product_attribute']."</strong>: ".$product['prodcut_data']."</li>";
			}
			$html .="</ul>";
			$html .="</div>";
		}
		
		echo json_encode(array('status' => true,'message'=> $html));
	}
	
}elseif($_POST['type'] == 'editAttribute')
{
	// $arr = $_POST;
	$id = $_POST['id'];
	$arrLabel = $_POST['attrLbl'];
	$arrType = $_POST['attrType'];
	
	$sql = "UPDATE eav_attribute SET attribute_label='$arrLabel',attribute_type='$arrType' WHERE attribute_id='$id'";
	$query = $dbModel->execute($sql);
	
	echo json_encode(array('status' => true,'message'=> 'attribute updated successfully'));
}elseif($_POST['type'] == 'deleteAttribute')
{
	$id = $_POST['id'];
	$sql = "DELETE FROM eav_attribute WHERE attribute_id = '$id'";
	$query = $dbModel->execute($sql); 
	echo json_encode(array('status' => true,'message'=> 'attribute deleted successfully'));
	
}elseif($_POST['type'] == 'updateAttributeForm')
{
	
	$arr = $_POST;
	
	$attrSet = $arr['set'];
	unset($arr['set']);
	unset($arr['type']);
	
	$attLen = count($arr)/3;
	$queryString = '';
	for($j=$attLen;$j>=1;$j--)
	{
		$attribute_codee = $arr["tb_code_$j"];
		$attribute_type = $arr["tb_type_$j"];
		$attribute_label = $arr["tb_label_$j"];
		
		$isIdExist = $dbModel->isIdExistInTable($selct='attribute_id',$attribute_codee,$attribute_code='attribute_code',$table='eav_attribute');
		if(!$isIdExist){
			$queryString = "INSERT INTO `eav_attribute` (attribute_set,attribute_code,attribute_type,attribute_label) VALUES ('$attrSet','$attribute_codee','$attribute_type', '$attribute_label')";
			$dbModel->execute($queryString);
		}else{
			$msg = $attribute_codee.' : is already exist! try with different one!';
			echo json_encode(array('status' => false,'message'=> $msg));
			exit;
		}
	}
	
	echo json_encode(array('status' => true,'message'=> 'attribute has been updated successfully.'));
}

?>