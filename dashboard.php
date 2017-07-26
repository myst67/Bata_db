<?php 
	session_start();
	if(isset($_GET['tableName']) && $_GET['tableName']){
		$_SESSION['current_table']=$_GET['tableName'];
	}else{
		header("Location: http://localhost/bata/index.php"); 
		die('table could not be found');
		exit();
	}
?>
<?php
include('head.html');
include('model.php'); 
$currentTable = $_SESSION['current_table'];

$dbModel = new db();

$productDetails = $dbModel->getAll("SELECT * FROM $currentTable");


?>


<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
		<div style="float:right">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#" data-title="Create" data-toggle="modal" data-target="#create" ><span class="glyphicon glyphicon-plus"></span> Craete</a></li>
				<?php if(!empty($productDetails)){?>
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
					<div style="margin: 0 auto;width: 88%;padding: 10 0 10 0;" role="menu">
						<div class="form-group">
							<label for="first">Filter by</label>
							<select id="first" name="filter" class="form-control" role="listbox">
								<option value="0" selected>Select Field</option>
								<option value="shoe_name">By Name</option>
								<option value="shoe_category">By Category</option>
								<option value="shoe_color">By Color</option>
								<option value="shoe_size">By Size</option>
								<option value="shoe_price">By Price</option>
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
					<h4 class="modal-title custom_align" id="Heading">Create Shoes</h4>
				</div>
				<form id="createForm" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="form-group">
							<label for="shoe_name">Shoe Name</label>
							<input class="form-control" id="shoe_name" name="shoe_name" type="text">
						</div>
						<div class="form-group">
							<label for="shoe_category">Shoe Category</label>
							<input class="form-control" id="shoe_category" name="shoe_category" type="text">
						</div>
						<div class="form-group">
							<label for="shoe_color">Shoe Color</label>
							<input class="form-control" id="shoe_color" name="shoe_color" type="text">
						</div>
						<div class="form-group">
							<label for="shoe_size">Shoe Size</label>
							<input class="form-control" id="shoe_size" name="shoe_size" type="number" min="0">
						</div>
						<div class="form-group">
							<label for="shoe_price">Shoe Price</label>
							<input class="form-control" id="shoe_price" name="shoe_price" type="number" min="0">
						</div>
					</div>
					<input type="hidden" name="type" value="create">
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
			<h4>Bata Shoe Datatable</h4>
			<div id="refreshTable"></div>
			<div class="table-responsive">
				<table id="shoetable" class="table table-bordred table-striped">
					<thead>
						<th><input type="checkbox" id="checkall" /></th>
						<th>Shoe name</th>
						<th>Shoe category</th>
						<th>Shoe color</th>
						<th>Shoe size</th>
						<th>Shoe price</th>
						<th>Edit</th>
					   <th>Delete</th>
					</thead>
					<?php if(empty($productDetails)){?>
						<div id="emptyTable">There is no shoe data. Please create some shoe from top right create button</div>
					<?php }else{?>
					<tbody>
						
						<?php foreach($productDetails as $_product){?>
							<tr id="column-<?php echo $_product['id'] ?>">
								<td><input type="checkbox" class="checkthis" /></td>
								<td><?php echo $_product['shoe_name'] ?></td>
								<td><?php echo $_product['shoe_category'] ?></td>
								<td><?php echo $_product['shoe_color'] ?></td>
								<td><?php echo $_product['shoe_size'] ?></td>
								<td><?php echo $_product['shoe_price'] ?></td>
								<td><p data-placement="top" data-toggle="tooltip" title="Edit"><button onclick="editProductDetails(<?php echo $_product['id']?>)" class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit" ><span class="glyphicon glyphicon-pencil"></span></button></p></td>
								<td><p data-placement="top" data-toggle="tooltip" title="Delete"><button onclick="assignDeleteProductId(<?php echo $_product['id']?>)" class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete" ><span class="glyphicon glyphicon-trash"></span></button></p></td>
								<input type="hidden" id="deleteId" name="deleteId">
							</tr>
						<?php }?>
					</tbody>
					<?php }?>
				</table>
				<div class="clearfix"></div>
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
					<div class="modal-body">
						<div class="form-group">
							<label for="edit_shoe_name">Shoe Name</label>
							<input class="form-control" id="edit_shoe_name" name="edit_shoe_name" type="text">
						</div>
						<div class="form-group">
							<label for="edit_shoe_category">Shoe Category</label>
							<input class="form-control" id="edit_shoe_category" name="edit_shoe_category" type="text">
						</div>
						<div class="form-group">
							<label for="edit_shoe_color">Shoe Color</label>
							<input class="form-control" id="edit_shoe_color" name="edit_shoe_color" type="text">
						</div>
						<div class="form-group">
							<label for="edit_shoe_size">Shoe Size</label>
							<input class="form-control" id="edit_shoe_size" name="edit_shoe_size" type="text">
						</div>
						<div class="form-group">
							<label for="edit_shoe_price">Shoe Price</label>
							<input class="form-control" id="edit_shoe_price" name="edit_shoe_price" type="text">
						</div>
						<input type="hidden" id="editId" name="id">
						<input type="hidden" name="type" value="edit">
					</div>
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
			<div class="modal-footer ">
			<button type="button" class="btn btn-success" onclick="deleteProduct()" ><span class="glyphicon glyphicon-ok-sign"></span> Yes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
		  </div>
			</div>
	  </div>
    </div>
	

<script type="text/javascript" >
	
	$(document).ready(function() {
		
		$('#createForm').submit(function(event) {
		
		event.preventDefault();
		var formdata = $("#createForm").serialize();
		
		 $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: $("#createForm").serialize(),
			success: function(data){
				console.log(data.status);
				if(data.status==true)
				{
					console.log(data);
					window.location.reload();
				}else{
					alert(data.message);
				}
			}
		  });
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
	
	function editProductDetails(id)
	{
	  $.ajax({
		type: "POST",
		url: "edit.php",
		dataType: 'json',
		data: {id:id,type:'edit'},
		success: function(data){
			if(data.status==true)
			{
				$('#edit_shoe_name').val(data.message.shoe_name);
				$('#edit_shoe_category').val(data.message.shoe_category);
				$('#edit_shoe_color').val(data.message.shoe_color);
				$('#edit_shoe_size').val(data.message.shoe_size);
				$('#edit_shoe_price').val(data.message.shoe_price);
				$('#editId').val(data.message.id);
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
					var removeEl = 'table#shoetable tr#column-'+id;
					$(removeEl).remove();
					$('#delete').modal('hide');
				}else{
					alert(data.message);
				}
			}
		});
	}
	</script>
