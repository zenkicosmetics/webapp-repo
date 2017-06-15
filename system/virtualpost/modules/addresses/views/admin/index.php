<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">Address Management</h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <button id="addLocationButton" class="admin-button">Add</button>
    <div class="clear-height"></div>
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addLocation" title="Add Location Address" class="input-form dialog-form">
	</div>
	<div id="editLocation" title="Edit Location Address" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	$('#mailbox').css('margin', '20px 0 0 20px');
    $('#addLocationButton').button();
	
	// Call search method
	searchLocations();
	/**
	 * Search data
	 */
	function searchLocations() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>addresses/admin';
		var tableH = $(document).height() - $($('#user-page').children()[0]).height() - $("#mailbox>.mailbox").height() - 150;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: tableH,
            width: 1100,
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'location_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','Location Name', 'Partner Code', 'Partner Name', 'Pricing Template', 'Street', 'Post Code', 'City', 'Region', 'Country', 'Language', 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'Location Name',index:'location_name', width:170},
               {name:'Partner Code',index:'partner_code', width:120},
               {name:'Partner Name',index:'partner_name', width:170},
               {name:'Pricing Template',index:'pricing_template_name', width:170},
               {name:'Street',index:'street', width:170},
               {name:'Post Code',index:'postcode', width:100},
               {name:'City',index:'city', width:120},
               {name:'Region',index:'region', width:100},
               {name:'Country',index:'country', width:125},
               {name:'Language',index:'language', width:125},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen(1100);
            }
        });
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
		} else {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete">UnCheck</span></span>';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
	}

	/**
	 * Process when user click to add group button
	 */
	$('#addLocationButton').click(function() {
	    // Clear control of all dialog form
	    $('.dialog-form').html('');
	    // Open new dialog
		$('#addLocation').openDialog({
			autoOpen: false,
			height: 500,
			width: 550,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>admin/addresses/add", function() {
					$('#addEditLocationForm_LocationName').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveLocation();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#addLocation').dialog('option', 'position', 'center');
		$('#addLocation').dialog('open');
	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
	    var location_id = $(this).data('id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#editLocation').openDialog({
			autoOpen: false,
			height: 450,
			width: 550,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>addresses/admin/edit?id=" + location_id, function() {
					$('#addEditLocationForm_LocationName').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveLocation();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#editLocation').dialog('option', 'position', 'center');
		$('#editLocation').dialog('open');
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var location_id = $(this).data('id');

		// Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>addresses/admin/delete?id=' + location_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchLocations();
                         } else {
                         	$.displayError(data.message);
                         }
                     }
                 });
            }
        });
	});
	
	/**
	 * Save group
	 */
	function saveLocation() {
		var submitUrl = $('#addEditLocationForm').attr('action');
		var action_type = $('#h_action_type').val();
		var data = $('#addEditLocationForm').serializeObject();
		var image_file_name = $('#imagepath_id').val();
// 		if (image_file_name == '') {
// 			 $.displayError('Image info not displayed.', null, function() {
      	    	  
//      	 	 });
//      	     return;
// 		}
		$.ajaxFileUpload({
			id: 'imagepath',
			url: submitUrl,
			data: data,
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#addLocation').dialog('close');
					} else if (action_type == 'edit') {
						$('#editLocation').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchLocations();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>