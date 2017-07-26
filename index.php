<?php
include('head.html');
include('model.php'); 
$dbModel = new db();

$dbModel->execute('CREATE SCHEMA IF NOT EXISTS `Bata_Schema`');
$dbModel->execute('USE `Bata_Schema`');

$tableArray = $dbModel->showTables();
?>
<div class="menu">
    <div class="container-fluid">
		<div class="navbar-header" style="float:left;padding-top:8px">
			<h4 style="color:black;margin-top: 5px;color:white;">ASSIGNMENT - 2</h4>
		</div>
	</div>
</div>

<div class="container">    
	
	<div class="existTableForm">
		<h2>Use Existing Table</h2>
		<ul class="list-group">
			<?php if(empty($tableArray)){?>
				<li class="list-group-item">There are no table in this db</li>
			<?php }else{?>
			<?php foreach($tableArray as $table){?>
				<?php 
					$tableName = $table['Tables_in_bata_schema'];
					$url = "dashboard.php?tableName=$tableName" 
				?>
				<li class="list-group-item"><a href="<?php echo $url ?>"><?php echo $tableName;?></a></li>
			<?php } }?>
		</ul>
	</div>
	
	<div class="newTableForm">
		<h2>Create New Table</h2>
		<form id="createTableForm">
		  <div class="form-group">
			<label for="table_name">Table Name</label>
			<input type="text" class="form-control" name="table_name" id="table_name">
		  </div>
		  <input type="hidden" name="type" value="createTable">
		  <button type="submit" class="btn btn-default">Submit</button>
		</form>
	</div>
</div>

<script>
	$('#createTableForm').submit(function(event) {
		if(!$('#table_name').val())
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
			success: function(data){
				if(data.status==true)
				{
					window.location.reload();
				}else{
					alert(data.message);
				}
			}
		  });
	  });
</script>