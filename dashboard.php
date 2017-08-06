<?php 
	session_start();
	
?>
<?php
include('head.html');
include('model.php'); 

$dbModel = new db();
$productsId = $dbModel->getAllProducts();
?>
<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
		<div style="float:right">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="http://localhost/bata/index.php" ><span class="glyphicon glyphicon-plus"></span> Create Product</a></li>
				<li><a href="#" data-title="Search" data-toggle="modal" data-target="#search"><span class="glyphicon glyphicon-search"></span> Search</a></li>
				
			</ul>
		</div>
	</div>
</div>

<div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="search" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
				<h4 class="modal-title custom_align" id="Heading">Search Products</h4>
				
			</div>
			<a onclick="addSeachCondition()" href="javascript:void(0)">Add Condition</a>
			<form id="searchForm" enctype="multipart/form-data">
				<input type="hidden" id="conditionCount" value="1" >
				<div id="searchCondition">
					<div class="col-md-12">
						<div class="form-group">
							<label for=''>Select All product containing property</label>
							<input class='form-control' id='searchByPropertyAttr1' name='search_by_property_attr_1' value='' type='text'>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Condition: </label>
								<select class='form-control' id='searchByPropertyCondition1' name='search_by_property_condition_1'>
									<option value='any'>Any</option>
									<option value='like'>LIKE</option>
									<option value='like%'>LIKE%</option>
									<option value='neq'>Not Equal</option>
									<option value='eq'>Equal</option>
									<option value='gthen'>Greater Then</option>
									<option value='lthen'>Less Then</option>
									<option value='gtheneq'>Greater Then Equal</option>
									<option value='ltheneq'>Less Then Equal</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for=''>with its value</label>
								<input class='form-control' id='searchByPropertyValue1' name='search_by_property_value_1' value='' type='text'>
							</div>
						</div>
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


<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h4>Bootstrap Snipp for Datatable</h4>
			<div class="table-responsive">
				<div id="refreshTable"></div>
				<table id="showProductsTable" class="table table-bordred table-striped">
					<thead>
						<th>Product Id</th>
						<th>Edit</th>
						<th>Delete</th>
					</thead>
					<tbody>
						<?php foreach($productsId as $_pid){?>
						<tr>
						<td><a data-title="View" data-toggle="modal" data-target="#view" href="" onclick="showProductDetails('<?php echo $_pid['pid']?>')"> <?php echo $_pid['pid'] ?></a></td>
						<td><p data-placement="top" data-toggle="tooltip" title="Edit"><button class="btn btn-primary btn-xs" data-title="View" data-toggle="modal" data-target="#view" href="" onclick="showProductDetails('<?php echo $_pid['pid']?>')" ><span class="glyphicon glyphicon-pencil"></span></button></p></td>
						<td><p data-placement="top" data-toggle="tooltip" title="Delete"><button onclick='assignDeleteProductId(<?php echo $_pid['pid'] ?>)' class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete" ><span class="glyphicon glyphicon-trash"></span></button></p></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<div class="clearfix"></div>
			</div>
        </div>
	</div>
</div>
<div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="view" aria-hidden="true">
      <div class="modal-dialog">
		<div class="modal-content">
          <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
			<h4 class="modal-title custom_align" id="Heading">View Product Detail</h4>
			<div><button type="button" onclick="editProduct()" name="submit" value="submit" class="btn btn-warning " style=""><span class="glyphicon glyphicon-ok-sign"></span>Edit</button></div>
		  </div>
			
			<form id="editForm" enctype="multipart/form-data">
				<div class="modal-body">
					<div id="product_data"></div>
					<div id="product_attribute" style="display:none">
						<input type='hidden' name='counter' id='counter' value='0'>
						<input type='hidden' name='counterDesc' id='counterDesc' value='0'>
						<h3> Add Product Property and value : <span id="attSet"></span></h3>
						<p>
							<a href="javascript:void(0);" onclick="addElement();">Add</a>
							<a href="javascript:void(0);" onclick="removeElement();">Remove</a>
						</p>
						<div id="tableAttr" ></div>
					</div>
				</div>
				<input type="hidden" name="type" value="editProducte">
				<input type="hidden" id="editProductId" name="editProducteId" value="">
				<div class="modal-footer" style="border: none;">
					<button type="submit" name="submit" value="submit" disabled class="btn btn-warning btn-lg" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign"></span>Update</button>
				</div>
			</form>
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
		<div class="modal-footer " >
		<button type="button" class="btn btn-success" onclick="deleteProduct()" ><span class="glyphicon glyphicon-ok-sign"></span> Yes</button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
	  </div>
		</div>
  </div>
</div>
	

<script type="text/javascript" >
	
	function addSeachCondition()
	{
		var count = parseInt($('#conditionCount').val());
		count++;
		var html='';
		html+= "<p><strong>AND</strong></p><div class='col-md-12'><div class='form-group'><label for=''>Property</label><input class='form-control' id='searchByPropertyAttr"+count+"' name='search_by_property_attr_"+count+"' value='' type='text'>";
		
		html+= "<div class='col-md-6'><div class='form-group'><label>Condition: </label><select class='form-control' id='searchByPropertyCondition"+count+"' name='search_by_property_condition_"+count+"'><option value='any'>Any</option><option value='like'>LIKE</option><option value='like%'>LIKE%</option><option value='neq'>Not Equal</option><option value='eq'>Equal</option><option value='gthen'>Greater Then</option><option value='lthen'>Less Then</option><option value='gtheneq'>Greater Then Equal</option><option value='ltheneq'>Less Then Equal</option></select></div></div><div class='col-md-6'><div class='form-group'><label for=''>with its value</label><input class='form-control' id='searchByPropertyValue"+count+"' name='search_by_property_value_"+count+"' value='' type='text'></div></div>";
		
		$('#conditionCount').val(count);
		$('#searchCondition').append(html);
	}
	
	function showProductDetails(pid)
	{
		$.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: {"_pid": pid,"type":'viewProduct'},
			success: function(data){
				if(data.status==true)
				{
					var formHTML = '';	
					$('#product_data').empty();
					$.each(data.message, function(i){
						//i++;
						$('#counter').empty();
						var j = i+1;
						var attId = parseInt(data.message[i].att_id);
						var valueId = parseInt(data.message[i].value_id);
						formHTML +="<div class='form-group'><div class='row' style='padding-top: 10px;padding-bottom: 10px;    border-bottom: 1px solid #e5e5e5;'><div class='col-md-4'><label for='property_code_"+j+"'>Property</label><input class='form-control' disabled id='property_code_"+j+"' name='property_code_"+j+"' value='"+data.message[i].Property+"' type='text'></div><div class='col-md-4'><label for='property_value_"+j+"'>Property Value</label><input class='form-control' disabled id='property_value_"+j+"' name='property_value_"+j+"' value='"+data.message[i].AttVaue+"' type='text'></div><div class='col-md-4'><label for='property_type_"+j+"'>Value Type</label><select class='form-control'  disabled required id='selectedTypeId' name='property_type_"+j+"'>";
						
						if(data.message[i].Type === 'number')
						{
							formHTML+="<option selected value='number'>Number</option><option value='string'>String</option></select></div>";
						}else if(data.message[i].Type === 'string'){
							formHTML+="<option value='number'>Number</option><option selected value='string'>String</option></select></div>";
						}
						
						formHTML+= '<div class="col-md-4"><label>Remove Property</label><button disabled type="button" onclick="removeAttributee('+pid+','+attId+','+valueId+')" name="submit" class="btn btn-warning"><span class="glyphicon glyphicon-ok-sign"></span>Remove Property</button></div></div></div>';
						
						$('#counter').val(j);
					});
					$('#product_data').append(formHTML);
					$('#editProductId').val(pid) ;
					
				}else{
					alert(data.message);
				}
				
				
			}
		  });
		  
		
	}
	
	
	function refreshTablee()
	{
		window.location.reload();
	}
	
	
	function removeAttributee(pid,attId,valueId)
	{
		if (confirm("Are You Sure?") == true) {
			$.ajax({
				type: "POST",
				url: "edit.php",
				dataType: 'json',
				data: {"_pid": pid,"_attid": attId,"_valid": attId,"type":'removeAttribute'},
				success: function(data){
					if(data.status === true)
					{
						alert(data.message);
						$('#view').modal('hide');
						// window.location.reload();
					}
				}
			});
		}
	}
	
	function hasWhiteSpace(s) {
	  return /\s/g.test(s);
	}
	
	function editProduct()
	{
		var pid = $('#editProductId').val();
		$('#product_attribute').show();
		var elements = document.getElementById("editForm").elements;
		for (var j = 0, element; element = elements[j++];) {
			element.disabled = false;
		}
		
		$('#editForm').submit(function(event) {
			event.preventDefault();
			
			var i = 1;
			var validation = true;
			$(':input', '#editForm').each(function() {
				var aa = 'property_code_'+i;
				if(this.id == aa)
				{
					if(hasWhiteSpace(this.value))
					{
						alert('Attribute name should not contain any space');
						validation = false;
						return false;
					}
					i=i+1;
				}
				
			});
			
			if(validation === true)
			{
			var formdata = $("#editForm").serialize();
			$.ajax({
				type: "POST",
				url: "edit.php",
				dataType: 'json',
				data: formdata,
				success: function(data){
					if(data.status === true)
					{
						alert(data.message);
						$('#view').modal('hide');
						// window.location.reload();
					}
				}
			  });
			} 
		});
		
	}
	
	function addElement() 
	{
		var intTextBox = 0;
		if(parseInt($('#counterDesc').val()) < 1)
		{
			intTextBox = parseInt($('#counter').val());
			
		}else{
			intTextBox = parseInt($('#counterDesc').val());
		}
		intTextBox++;
		var objNewDiv = document.createElement('div');
		objNewDiv.setAttribute('id', 'div_' + intTextBox);
		objNewDiv.setAttribute('style', 'padding-bottom:5px');
		objNewDiv.innerHTML = '<div class="col-md-4" style="padding-top:10px;padding-bottom:10px;"><span>Product Property</span>' + ': <input type="text" class="form-control" required id="property_code_' + intTextBox + '" name="property_code_' + intTextBox + '"/></div>';
		objNewDiv.innerHTML += '<div class="col-md-4" style="padding-top:10px;padding-bottom:10px;"><span>Property Value</span>' + ': <input type="text" class="form-control" required id="property_value_' + intTextBox + '" name="property_value_' + intTextBox + '"/></div>';
		objNewDiv.innerHTML += "<div class='col-md-4' style='padding-top:10px;padding-bottom:10px;'><span>Property Value Type</span> " + ": <select class='form-control' required id='property_type_"+intTextBox+"' name='property_type_"+intTextBox+"'><option value='number'>Number</option><option value='string'>String</option></select></div>";
		$('#counterDesc').val(intTextBox);
		document.getElementById('tableAttr').appendChild(objNewDiv);
	}
	
	
	function removeElement()
	{
		var intTextBox = parseInt($('#counterDesc').val());
		var valuee = parseInt($('#counter').val());
		if(valuee < intTextBox) {
			document.getElementById('tableAttr').removeChild(document.getElementById('div_' + intTextBox));
			intTextBox--;
		} else {
			alert("No textbox to remove");
		}
		$('#counterDesc').val(intTextBox);
	}
	
	$(document).ready(function() {
		$('#view').on('hidden.bs.modal', function () {
			$('#product_attribute').hide();
			
			var elements = document.getElementById("editForm").elements;
			for (var i = 0, element; element = elements[i++];) {
				element.disabled = true;
			}
		})
		
		$('#searchForm').submit(function(event) {
		event.preventDefault();
		var formdata = $("#searchForm").serialize();
		var html = '';
		$('#refreshTable').empty(); 
		var refreshHtml = "<a href='javascript:void(0)' onclick='refreshTablee()'>Refresh Table</a>";
		 $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: formdata,
			success: function(data){
				
				$('#showProductsTable > tbody').empty();
				if(data.status === true)
				{
					for(var j=0;j<data.message.length;j++)
					{
						html+= "<tr><td><a data-title='View' data-toggle='modal' data-target='#view' href='' onclick='showProductDetails("+data.message[j].Pid+")'>"+data.message[j].Pid+"</a></td><td><p data-placement='top' data-toggle='tooltip' title='Edit'><button class='btn btn-primary btn-xs' data-title='View' data-toggle='modal' data-target='#view' href='' onclick='showProductDetails("+data.message[j].Pid+")'><span class='glyphicon glyphicon-pencil'></span></button></p></td><td><p data-placement='top' data-toggle='tooltip' title='Delete'><button onclick='assignDeleteProductId("+data.message[j].Pid+")' class='btn btn-danger btn-xs' data-title='Delete' data-toggle='modal' data-target='#delete' ><span class='glyphicon glyphicon-trash'></span></button></p></td></tr>";
					}
					$('#showProductsTable > tbody').append(html);
					
				}else{
					$('#showProductsTable > tbody').append(data.message);
				}
				$('#search').modal('hide');
				$('#refreshTable').append(refreshHtml);
			}
		  });
		});
	});
	
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
			data: {id:id,type:'deleteProduct'},
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
