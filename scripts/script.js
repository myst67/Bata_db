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
		
		var fields = selection.split('|');
		var attribute_code = fields[0];
		var attribute_type = fields[1];
		
		if(attribute_type === 'text')
		{
			options += '<option value="like%">%Like%</option>';
			options += '<option value="like">Like</option>';
			options += '<option value="not_like">NOT LIKE</option>'; 
			options += '<option value="regexp">REGEXP</option>'; 
			options += '<option value="not_regexp">NOT REGEXP</option>'; 
		}else if(attribute_type === 'number')
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
	
	
	/* $('#searchForm').submit(function(event) {
		event.preventDefault();
		$('#refreshTable').append('');
		var refreshHtml = '';
		console.log('before ajax==');
		console.log(refreshHtml);
		
		
		console.log('search from data====');
		console.log($("#searchForm").serialize());
		
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
					$("#table-responsive").empty();
					$("#table-responsive").css('margin-top', '20px');
					$('#table-responsive').append(data.message); 
					$('#search').modal('hide'); 
					refreshHtml = "<a href='javascript:void(0)' onclick='refreshTable()'>Refresh Table</a>";
					$('#refreshTable').append(refreshHtml); 
				}else{
					alert(data.message);
				}
			}
		}); 
	}); */
	
});
