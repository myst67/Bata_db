<?php 
	session_start();
	
?>
<?php
include('head.html');
include('model.php'); 
$currentTable = '';//$_SESSION['current_table'];

$dbModel = new db();

		
$productSetDetails = $dbModel->getAllProducts();
$attrSet = $dbModel->getTotalAttributeSet();



		
 /* echo '<pre>';
print_r($attrSet);
echo '</pre>'; */
?>


<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
		<div style="float:right">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="http://localhost/bata/index.php" ><span class="glyphicon glyphicon-pencil"></span> Edit Attribute</a></li>
				<?php if(!empty($attrSet)){?>
				<li><a href="#" data-title="Create" data-toggle="modal" data-target="#create" ><span class="glyphicon glyphicon-plus"></span> Craete</a></li>
				<li><a href="#" data-title="Search" data-toggle="modal" data-target="#search"><span class="glyphicon glyphicon-search"></span> Search</a></li>
				<?php }?>
			</ul>
		</div>
	</div>
	
	<div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="search" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					<h4 class="modal-title custom_align" id="Heading">Search Shoes</h4>
				</div>
				<form id="searchForm" enctype="multipart/form-data">
					<?php 
						$attributes = $dbModel->getAllAttributeDetails();
						/* echo '<pre>';
						print_r($attributes);
						echo '</pre>'; */
					?>
					<div style="margin: 0 auto;width: 88%;padding: 10 0 10 0;" role="menu">
						<div class="form-group">
							<label for="first">Filter by (Attribute Code)</label>
							<select id="first" name="filter" class="form-control" role="listbox">
								<option value="0" selected>Select Field</option>
								<?php foreach($attributes as $attribute){?>
									<option value="<?php echo $attribute['attribute_code'].'|'.$attribute['attribute_type']?>"><?php echo $attribute['attribute_code']?></option>
								<?php }?>
							</select>
						</div>
						<div class="form-group">
							<label for="second" id="secondDropdown">Condition</label>
							<select id="second" name="filterCondition" class="form-control" role="listbox" disabled="disabled">
								<option value="0" selected="selected">Select Option</option>
							</select>
						</div>
						<div class="form-group">
							<label for="contain">Input Value</label>
							<input id="third" name="filterValue" class="form-control" type="text" disabled="disabled"/>
						</div>
					</div>
					<input type="hidden" name="type" value="search">
					<div class="modal-footer">
						<button type="submit" name="submit" value="submit" class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Apply Search</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					<h4 class="modal-title custom_align" id="Heading">Create Product</h4>
				</div>
				<form id="createForm" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="form-group">
						  <label for="attr_set">Select Attribute Set:</label>
						  <select class="form-control" id="attr_set" name="attr_set">
							<option value="">----</option>
							<?php foreach($attrSet as $set){?>
							<option value="<?php echo $set['attribute_set']?>"><?php echo $set['attribute_set'] ?></option>
							<?php } ?>
						  </select>
						</div>
						<div id="attr_data"></div>
					</div>
					<input type="hidden" name="type" value="createProduct">
					<div class="modal-footer">
						<button type="submit" name="submit" value="submit" class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Create</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div id="refreshTable"></div>
			<div class="table-responsive" id="table-responsive">
				<?php if(!empty($attrSet)){?>
				<?php foreach($attrSet as $set){?>
					<h4>Bata <?php echo $set['attribute_set']?> Datatable</h4>
					<?php 
						$products = $dbModel->getProductDetailsInEachSet($set['attribute_set']);
						if(empty($products))
						{
					?>
					You dont have any products with this set
					<?php }else{
						
						$groupedProducts = $dbModel->groupApply($products,'product_id');
						foreach($groupedProducts as $id=>$products){
						/* echo '<pre>';
						print_r($groupedProducts);
						echo '</pre>'; */
					?>
						<div class='col-xs-18 col-sm-4 col-md-3'>
						<ul>
						<li style='display: inline;float: left;width: 100%;'>
							<strong>Product Id: </strong><?php echo $id ?>
							<?php 
							$attrArray = Array();
							foreach($products as $product)
							{
								
								$attrArray[] = $product['attribute_code'];
							?>
								<li style='display: inline;float: left;width: 100%;'><strong><?php echo $product['product_attribute']?> : </strong><?php echo $product['prodcut_data']?></li>
							<?php } 
								
								$arrayNotAttrValue = $dbModel->getAttributesNotValue($set['attribute_set'],$attrArray);
								/* echo '<pre>';
								print_r($arrayNotAttrValue);
								echo '</pre>'; */
								if(!empty($arrayNotAttrValue)){
								foreach($arrayNotAttrValue as $notValue)
								{
							?>
								<li style='display: inline;float: left;width: 100%;'><strong><?php echo $notValue['attribute_label']?> : </strong>No Value</li>
							<?php
								} }
							?>
							
							<p style="float: left;margin-right: 5px;" data-placement="top" data-toggle="tooltip" title="Edit"><button onclick="editProductDetails(<?php echo $id ?>,'<?php echo $set['attribute_set']?>')" class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit" ><span class="glyphicon glyphicon-pencil"></span></button></p>
							
							<p data-placement="top" data-toggle="tooltip" title="Delete"><button onclick="assignDeleteProductId(<?php echo $id ?>)" class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete" ><span class="glyphicon glyphicon-trash"></span></button></p>
						</li>
						</ul>
						</div>
						<?php } } ?>
						
					<div class="clearfix"></div>
					<?php } } ?>
				
			</div>
        </div>
	</div>
</div>

<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
      <div class="modal-dialog">
    <div class="modal-content">
          <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
        <h4 class="modal-title custom_align" id="Heading">Edit Your Detail</h4>
      </div>
			<div class="modal-body">
				<form id="editForm" enctype="multipart/form-data">
					<div class="modal-body" id="modal-body-id"></div>
					<div class="modal-footer">
						<button type="submit" name="submit" value="submit" class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Update</button>
					</div>
				</form>
			</div>
        </div>
  </div>
</div>
    
    
    
    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
      <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
			<h4 class="modal-title custom_align" id="Heading">Delete this entry</h4>
			</div>
			
			<div class="modal-body">
				<div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> Are you sure you want to delete this Record?</div>
			</div>
			<input type="hidden" id="deleteId" name="delete" value="">
			<div class="modal-footer ">
			<button type="button" class="btn btn-success" onclick="deleteProduct()" ><span class="glyphicon glyphicon-ok-sign"></span> Yes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
		  </div>
			</div>
	  </div>
    </div>
	

<script type="text/javascript" >
	
	
	
	
	$(document).ready(function() {
		
		$('#attr_edit_set').on('change', function() {
		  $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: {"set": this.value,"type":'editAttribute'},
			success: function(data){
				if(data.status==true)
				{
					/* var formHTML = '';	
					$.each(data.data, function(i){
						formHTML +="<div class='form-group'><label for='"+data.data[i].attribute_code+"'>"+data.data[i].attribute_label+"</label><input class='form-control' id='"+data.data[i].attribute_code+"' name='"+data.data[i].attribute_id+"' type='"+data.data[i].attribute_type+"' min=0></div>";
					});
					$('#attr_edit_data').append(formHTML); */
				}else{
					alert(data.message);
				}
			}
		  });
		})
		
		$('#attr_set').on('change', function() {
			
			$("#attr_data").empty();
		  $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: {"set": this.value,"type":'showAttribute'},
			success: function(data){
				if(data.status==true)
				{
					var formHTML = '';	
					$.each(data.data, function(i){
						formHTML +="<div class='form-group'><label for='"+data.data[i].attribute_code+"'>"+data.data[i].attribute_label+"</label><input class='form-control' id='"+data.data[i].attribute_code+"' name='"+data.data[i].attribute_id+"' type='"+data.data[i].attribute_type+"' min=0></div>";
					});
					$('#attr_data').append(formHTML);
				}else{
					alert(data.message);
				}
			}
		  });
		})

		$('#createForm').submit(function(event) {
		
		event.preventDefault();
		var formdata = $("#createForm").serialize();
		var err = '';
		$('#createForm input').each(
			function(index){  
				var input = $(this);
				var str = input.val();
				if(str.toLocaleLowerCase().indexOf("'") != -1)
				{
					err = err+"please remove ' charecter from String: "+input.val();
				}
			}
		);
		
		if(err)
		{
			alert(err);
			window.location.reload();
		}else{
			$.ajax({
				type: "POST",
				url: "create.php",
				dataType: 'json',
				data: $("#createForm").serialize(),
				success: function(data){
					if(data.status==true)
					{
						window.location.reload();
					}else{
						alert(data.message);
					}
				}
			}); 
		}
	  });
	  
	  $('#editForm').submit(function(event) {
		event.preventDefault();
		var formdata = $("#editForm").serialize();
		 $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: $("#editForm").serialize(),
			success: function(data){
				console.log(data.status);
				window.location.reload();
			}
		  });
	  });
	});
	
	function refreshTable()
	{
		window.location.reload();
	}
	
	function editProductDetails(id,set)
	{
	  $.ajax({
		type: "POST",
		url: "edit.php",
		dataType: 'json',
		data: {id:id,type:'edit',setId:set},
		success: function(data){
			if(data.status==true)
			{
				$('#modal-body-id').empty();
				$('#modal-body-id').append(data.message);
				$('#create').modal('hide');
				
			}else{
				alert(data.message);
			}
		}
	  });
	}
	
	function assignDeleteProductId(id)
	{
		$('#deleteId').val(id);
	}
	
	function deleteProduct()
	{
		
		var id = $('#deleteId').val();
		$.ajax({
			type: "POST",
			url: "edit.php",
			dataType: 'json',
			data: {id:id,type:'delete'},
			success: function(data){
				if(data.status==true)
				{
					window.location.reload();
				}else{
					alert(data.message);
				}
			}
		});
	}
	</script>
