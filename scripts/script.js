'use strict';

$(document).ready(function() {

    var option = '<option value="0" selected="selected">Select Option</option>';

    var clearDropDown = function(arrayObj, startIndex) {
        $.each(arrayObj, function(index, value) {
            if(index >= startIndex) {
                $(value).html(option);
            }
        });
    };

    var disableDropDown = function(arrayObj, startIndex) {
        $.each(arrayObj, function(index, value) {
            if(index >= startIndex) {
                $(value).attr('disabled', 'disabled');
            }
        });
    };

    var enableDropDown = function(that) {
        that.removeAttr('disabled');
    };
	
	var enableTextbox = function(that) {
        that.removeAttr('disabled');
    };

    var generateOptions = function(element, selection) {
        var options = '';
		
		if(selection === 'shoe_name' || selection === 'shoe_category' || selection === 'shoe_color')
		{
			options += '<option value="like%">%Like%</option>';
			options += '<option value="like">Like</option>';
			options += '<option value="not_like">NOT LIKE</option>'; 
			options += '<option value="regexp">REGEXP</option>'; 
			options += '<option value="not_regexp">NOT REGEXP</option>'; 
		}
		if(selection === 'shoe_size' || selection === 'shoe_price')
		{
			options += '<option value="=">Equal</option>';
			options += '<option value=">">Greater Then</option>';
			options += '<option value=">=">Greater Then Equal</option>'; 
			options += '<option value="<">Less Then</option>'; 
			options += '<option value="<=">Less Then Equal</option>'; 
			options += '<option value="!=">Not Equal</option>'; 
		}
		
        element.append(options);
    };

    var firstDropDown = $('#first');
    var secondDropDown = $('#second');
    var thirdTextbox = $('#third');

    var firstSelection = '';
    var secondSelection = '';
    var selection = '';

    firstDropDown.on('change', function() {

	
        firstSelection = firstDropDown.val();

        clearDropDown($('select'), 1);

        disableDropDown($('select'), 1);
		
		thirdTextbox.val('');
		thirdTextbox.attr("disabled", "disabled"); 
		
        if(firstSelection === '0') {
            return;
        }

        enableDropDown(secondDropDown);

        selection = firstSelection;
        generateOptions(secondDropDown, selection);
    });

    secondDropDown.on('change', function() {
        secondSelection = secondDropDown.val();
        clearDropDown($('select'), 2);

        if(secondSelection === '0') {
			thirdTextbox.val('');
			thirdTextbox.attr("disabled", "disabled"); 
            return;
        }
        enableTextbox(thirdTextbox);
    });
	
	
	$('#searchForm').submit(function(event) {
		event.preventDefault();
		
		 $.ajax({
			type: "POST",
			url: "edit.php",
			dataType: 'json',
			data: $("#searchForm").serialize(),
			success: function(data){
				console.log('after search sucess');
				console.log(data.status);
				if(data.status==true)
				{
					console.log('after data status true');
					console.log(data);
					var trHTML = '';
					$('#shoetable > tbody').html(trHTML);
					$.each(data.message, function(i){
						
						trHTML += "<tr><td><input type='checkbox' class='checkthis' /></td><td>" + data.message[i].shoe_name + "</td><td>" + data.message[i].shoe_category + "</td><td>" + data.message[i].shoe_color + "</td><td>" + data.message[i].shoe_size + "</td><td>" + data.message[i].shoe_price +"</td><td> <p data-placement='top' data-toggle='tooltip' title='Edit'><button onclick='editProductDetails("+ data.message[i].id +")' class='btn btn-primary btn-xs' data-title='Edit' data-toggle='modal' data-target='#edit' ><span class='glyphicon glyphicon-pencil'></span></button></p> </td><td><p data-placement='top' data-toggle='tooltip' title='Delete'><button onclick='assignDeleteProductId("+ data.message[i].id +")' class='btn btn-danger btn-xs' data-title='Delete' data-toggle='modal' data-target='#delete' ><span class='glyphicon glyphicon-trash'></span></button></p> </td></tr>";
					});
					var refreshHtml = "<a href='javascript:void(0)' onclick='refreshTable()'>Refresh Table</a>";
					
					$('#refreshTable').append(refreshHtml); 
					$('#shoetable > tbody').append(trHTML);
					$('#search').modal('hide');
					
				}else{
					alert(data.message);
				}
			}
		});
	});
	
	
	
	$("#mytable #checkall").click(function () {
		if ($("#mytable #checkall").is(':checked')) {
			$("#mytable input[type=checkbox]").each(function () {
				$(this).prop("checked", true);
			});

		} else {
			$("#mytable input[type=checkbox]").each(function () {
				$(this).prop("checked", false);
			});
		}
	});
	
	$("[data-toggle=tooltip]").tooltip();
	
	
  
  
  
	
	
});
