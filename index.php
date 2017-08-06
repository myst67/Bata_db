<?php
include('head.html');
include('model.php'); 
$dbModel = new db();


$dbModel->execute('CREATE SCHEMA IF NOT EXISTS `Bata_Schema`');
$dbModel->execute('USE `Bata_Schema`');
$dbModel->createInitialTables();

$attrSet = null;//$dbModel->getTotalAttributeSet();
?>
<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
		<div style="float:right">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="http://localhost/bata/dashboard.php" >Show Product Table</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="container">    
	
	<div class="newTableForm">
		<h2>Create New Product</h2>
		<form id="createTableForm">
			<h3> Add Product Property and value : <span id="attSet"></span></h3>
			<p>
				<a href="javascript:void(0);" onclick="addElement();">Add</a>
				<a href="javascript:void(0);" onclick="removeElement();">Remove</a>
			</p>
			<div id="tableAttr" ></div>
		  <input type="hidden" name="type" value="createProduct">
		  <button type="submit" id="createSetButton" class="btn btn-default" disabled="disabled">Submit</button>
		  <button type="button" onclick="showProductProperty()" data-title="create" data-toggle="modal" data-target="#create" id="viewProperty" class="btn btn-default" style="display:none">View</button>
		</form>
	</div>
	
	<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
					<h4 class="modal-title custom_align" id="Heading">View Product Properties</h4>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	
	var intTextBox = 0;
	function addElement() {
		intTextBox++;
		if(intTextBox >= 1)
		{
			$('#createSetButton').attr('disabled', false);
			$('#viewProperty').show();
		}
		var objNewDiv = document.createElement('div');
		objNewDiv.setAttribute('id', 'div_' + intTextBox);
		objNewDiv.setAttribute('style', 'padding-bottom:5px');
		objNewDiv.innerHTML = '<span>Product Property</span>' + ': <input type="text" required id="property_code_' + intTextBox + '" name="property_code_' + intTextBox + '"/>';
		objNewDiv.innerHTML += '<span>Property Value</span>' + ': <input type="text" required id="property_value_' + intTextBox + '" name="property_value_' + intTextBox + '"/>';
		objNewDiv.innerHTML += "<span>Property Value Type</span> " + ": <select required id='property_type_"+intTextBox+"' name='property_type_"+intTextBox+"'><option value='number'>Number</option><option value='string'>String</option></select>";
		
		document.getElementById('tableAttr').appendChild(objNewDiv);
	}
	
	
	function removeElement() {
		
		if(0 < intTextBox) {
			document.getElementById('tableAttr').removeChild(document.getElementById('div_' + intTextBox));
			if(intTextBox <= 1)
			{
				$('#createSetButton').attr('disabled', true);
				$('#viewProperty').hide();
			}
			intTextBox--;
		} else {
			alert("No textbox to remove");
		}
	}
	function hasWhiteSpace(s) {
	  return /\s/g.test(s);
	}
	
	function showProductProperty()
	{
		var i=1;
		$(':input', '#createTableForm').each(function() {
			console.log(this);
			
			if(this.name !== 'type')
			{
				console.log(this.value+':'+this.value);
			}
			//console.log(this.value);
            
        });
	}
	$('#createTableForm').submit(function(event) {
		
		console.log('before submit====');
		
		var form = $('#createTableForm').prop('elements');
		var i = 1;
		var validation = true;
		$(':input', '#createTableForm').each(function() {
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
		event.preventDefault();
		if(validation === true)
		{
			var formdata = $("#createTableForm").serialize();
			 $.ajax({
				type: "POST",
				url: "create.php",
				dataType: 'json',
				data: $("#createTableForm").serialize(),
				success: function(res){
					if(res.status===true)
					{
						alert(res.message);
						window.location.href="http://localhost/bata/index.php";
					}else if(res.status==false){
						alert(res.message);
					}
				}
			});
		}
	  });
	
</script>