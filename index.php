<?php
include('head.html');
include('model.php'); 
$dbModel = new db();


$dbModel->execute('CREATE SCHEMA IF NOT EXISTS `Bata_Schema`');
$dbModel->execute('USE `Bata_Schema`');
$dbModel->createInitialTables();

$attrSet = $dbModel->getTotalAttributeSet();
?>
<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
		<div style="float:right">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="http://localhost/bata/dashboard.php" >Show Data Table</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="container">    
	
	<div class="attribute-data">
		
	
	<div id="attr_edit_data">
		<?php if(!empty($attrSet)){?>
		<h2>Edit Exist Atribute By Set</h2>
		<div class="form-group">
		  <label for="attr_edit_set">Select Attribute Set:</label>
		  <select class="form-control" id="attr_edit_set" name="attr_edit_set">
			<option value="">----</option>
			<?php foreach($attrSet as $set){?>
			<option value="<?php echo $set['attribute_set']?>"><?php echo $set['attribute_set'] ?></option>
			<?php } ?>
		  </select>
		</div>
		<table class="table table-bordred table-striped" id="att-table"  style="display:none">
			<thead>
				<th>Attribute Label</th>
				<th>Attribute Type</th>
				<th>Attribute Edit</th>
				<th>Attribute Delete</th>
			</thead>
			<tbody>
			</tbody>
		</table>
		<form id="updateAttributeForm" style="display:none" enctype="multipart/form-data">	
			<input type="hidden" id="update-attribute-set" name="set" value="">
			<div>
				<h3> Add attribute and Type : <span id="attExistSet"></span></h3>
				<p>
					<a href="javascript:void(0);" onclick="addAttributeElement();">Add</a>
					<a href="javascript:void(0);" onclick="removeAttributeElement();">Remove</a>
				</p>
				<div id="tableAttribute" ></div>
			</div>
			<input type="hidden" name="type" value="updateAttributeForm">
			<button type="submit" class="btn btn-default">Update</button>
		</form>
		<?php } ?>
	</div>
	</div>
	
	<div class="newTableForm">
		<h2>Create New Atribute Set</h2>
		<form id="createTableForm">
			<div class="form-group">
				<label for="attr_set_name">Set Name</label>
				<input type="text" class="form-control" name="attr_set_name" id="attr_set_name">
			</div>
			<h3> Add attribute and Type : <span id="attSet"></span></h3>
			<p>
				<a href="javascript:void(0);" onclick="addElement();">Add</a>
				<a href="javascript:void(0);" onclick="removeElement();">Remove</a>
			</p>
			<div id="tableAttr" ></div>
		  <input type="hidden" name="type" value="createTable">
		  <button type="submit" id="createSetButton" class="btn btn-default" disabled="disabled">Submit</button>
		</form>
	</div>
</div>

<script>
	$(document).ready(function() {
		
		$('#attr_set_name').change(function () {
			$('#attSet').html($('#attr_set_name').val());
		});
		
		$('#attr_edit_set').on('change', function() {
			$('#att-table > tbody').empty();
			$('#attExistSet').html(this.value);
			$('#update-attribute-set').val(this.value);
			$.ajax({
				type: "POST",
				url: "create.php",
				dataType: 'json',
				data: {"set": this.value,"type":'editAttribute'},
				success: function(data){
					if(data.status==true)
					{
						$('#att-table > tbody').append(data.msg);
						$('#att-table').css('display', 'table');
						$('#updateAttributeForm').css('display', 'inline-block');
					}else{
						alert(data.message);
					}
				}
			});
		})
	});
		
	$('#updateAttributeForm').submit(function(event) {
		
		event.preventDefault();
		if(!$('#update-attribute-set').val())
		{
			console.log('Attribute set name is required while updating');
			return false;
		}else{
			var formdata = $("#updateAttributeForm").serialize();
			$.ajax({
				type: "POST",
				url: "edit.php",
				dataType: 'json',
				data: formdata,
				success: function(data){
					if(data.status==true)
					{
						alert(data.message);
						window.location.reload();
					}else{
						alert(data.message);
					}
				}
			});
		}
	  });
	
	function editProductAttribute(id)
	{
		var idTextField = "#attrText-"+id;
		var idSelectField = "#attrSelect-"+id;
		var idUpdateField = "#updateAttr-"+id;
		$(idTextField).prop( "disabled", false );
		$(idSelectField).prop( "disabled", false );
		$(idUpdateField).css('display', 'inline-block');
	}
	
	function deleteProductAttribute(id)
	{
		$.ajax({
			type: "POST",
			url: "edit.php",
			dataType: 'json',
			data: {"id":id,"type":'deleteAttribute'},
			success: function(data){
				if(data.status==true)
				{
					alert(data.message);
					var tableRow = '#editAttrTr-'+id;
					$(tableRow).remove();
				}else{
					alert(data.message);
				}
			}
		  });
		  
	}
	
	function updateAttribute(id)
	{
		var idTextField = "#attrText-"+id;
		var idSelectField = "#attrSelect-"+id;
		
		var textFieldValue=$(idTextField).val();
		var selectFieldValue=$(idSelectField).val();
		
		$.ajax({
			type: "POST",
			url: "edit.php",
			dataType: 'json',
			data: {"id":id,"type":'editAttribute',"attrLbl": textFieldValue,"attrType":selectFieldValue},
			success: function(data){
				if(data.status==true)
				{
					alert(data.message);
					var idTextField = "#attrText-"+id;
					var idSelectField = "#attrSelect-"+id;
					var idUpdateField = "#updateAttr-"+id;
					$(idTextField).prop( "disabled", true );
					$(idSelectField).prop( "disabled", true );
					$(idUpdateField).css('display', 'none');
					
				}else{
					alert(data.message);
				}
			}
		  });
		  
		  
		console.log(textFieldValue);
		console.log(selectFieldValue);
	}
	
	
	  
	  $('#createTableForm').submit(function(event) {
		
		if(!$('#attr_set_name').val())
		{
			alert('table name should not be empty');
			return false;
		}
		event.preventDefault();
		var formdata = $("#createTableForm").serialize();
		 $.ajax({
			type: "POST",
			url: "create.php",
			dataType: 'json',
			data: $("#createTableForm").serialize(),
			success: function(res){
				if(res.status===true)
				{
					console.log(res);
					window.location.href="http://localhost/bata/dashboard.php";
				}else if(res.status==false){
					alert(res.message);
				}
			}
		  });
	  });
	  
		var intTextBox = 0;
		function addElement() {
			intTextBox++;
			if(intTextBox >= 1)
			{
				$('#createSetButton').attr('disabled', false);
			}
			var objNewDiv = document.createElement('div');
			objNewDiv.setAttribute('id', 'div_' + intTextBox);
			objNewDiv.setAttribute('style', 'padding-bottom:5px');
			objNewDiv.innerHTML = 'Attribute Code ' + ': <input type="text" required id="tb_code_' + intTextBox + '" name="tb_code_' + intTextBox + '"/>';
			objNewDiv.innerHTML += 'Attribute Label ' + ': <input type="text" required id="tb_label_' + intTextBox + '" name="tb_label_' + intTextBox + '"/>';
			objNewDiv.innerHTML += "Attribute Type " + ": <select required id='tb_type_"+intTextBox+"' name='tb_type_"+intTextBox+"'><option value='text'>Text</option><option value='number'>Number</option></select>";
			
			document.getElementById('tableAttr').appendChild(objNewDiv);
		}
		
		
		function removeElement() {
			
			if(0 < intTextBox) {
				document.getElementById('tableAttr').removeChild(document.getElementById('div_' + intTextBox));
				if(intTextBox <= 1)
				{
					$('#createSetButton').attr('disabled', true);
				}
				intTextBox--;
			} else {
				alert("No textbox to remove");
			}
		}
		
		var intAttributeTextBox = 0;
		function addAttributeElement() {
			intAttributeTextBox++;
			var objAttributeNewDiv = document.createElement('div');
			objAttributeNewDiv.setAttribute('id', 'div_' + intAttributeTextBox);
			objAttributeNewDiv.setAttribute('style', 'padding-bottom:5px');
			objAttributeNewDiv.innerHTML = 'Attribute Code ' + ': <input type="text" required id="tb_code_' + intAttributeTextBox + '" name="tb_code_' + intAttributeTextBox + '"/>';
			objAttributeNewDiv.innerHTML += 'Attribute Label ' + ': <input type="text" required id="tb_label_' + intAttributeTextBox + '" name="tb_label_' + intAttributeTextBox + '"/>';
			objAttributeNewDiv.innerHTML += "Attribute Type " + ": <select required id='tb_type_"+intAttributeTextBox+"' name='tb_type_"+intAttributeTextBox+"'><option value='text'>Text</option><option value='number'>Number</option></select>";
			
			document.getElementById('tableAttribute').appendChild(objAttributeNewDiv);
		}
		
		
		function removeAttributeElement() {
			if(0 < intAttributeTextBox) {
				document.getElementById('tableAttribute').removeChild(document.getElementById('div_' + intAttributeTextBox));
				intAttributeTextBox--;
			} else {
				alert("No textbox to remove");
			}
		}
</script>