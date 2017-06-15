<div class="button_container">
    <button id="addGroupButton" class="admin-button">Add</button>
</div>

<div id="searchTableResult" style="margin: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addGroup" title="Add Group" class="input-form dialog-form">
	</div>
	<div id="editGroup" title="Edit Group" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	// Call search method
	searchGroup();
	/**
	 * Search data
	 */
	function searchGroup() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>groups/admin';
		
        $("#dataGridResult").jqGrid({
        	url: url, 
        	datatype: "json",
            height: "100%",
            width: DATAGRID_WIDTH,
            rowNum: '<?php echo Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'Name',
            viewrecords: true,
            shrinkToFit:false,
            captions: '',
            colNames:['ID','Name', 'Short Name', ''],
            colModel:[
               {name:'ID',index:'id', hidden: true},
               {name:'Description',index:'Description', width:500},
               {name:'Name',index:'Name', width:440},
               {name:'ID',index:'ID', width:75, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            }
        });
	}

	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>'
		      + (rowObject[2]==='admin'?'':'<span style="display:inline-block;"><span class="managetables-icon managetables-icon-permission" data-id="' + cellvalue + '" title="Change Permission"></span></span>');
	}


	/**
	 * Process when user click to add group button
	 */
	$('#addGroupButton').click(function() {
	    // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#addGroup').openDialog({
			autoOpen: false,
			height: 180,
			width: 304,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>groups/admin/add", function() {
					$('#Name').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveGroup();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#addGroup').dialog('option', 'height', 180);
		$('#addGroup').dialog('option', 'position', 'center');
		$('#addGroup').dialog('open');

	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
	    var group_id = $(this).data('id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#editGroup').openDialog({
			autoOpen: false,
			height: 180,
			width: 304,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>groups/admin/edit?id=" + group_id, function() {
					$('#Name').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveGroup();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#editGroup').dialog('option', 'height', 180);
		$('#editGroup').dialog('option', 'position', 'center');
		$('#editGroup').dialog('open');
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var group_id = $(this).data('id');

		 // Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>groups/admin/delete?id=' + group_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchGroup();
                         } else {
                         	$.displayError(data.message);
                         }
                     }
                 });
            }
        });
	});

	/**
	 * Process when user click to permission icon.
	 */
	$('.managetables-icon-permission').live('click', function() {
	    var group_id = $(this).data('id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');
	    // Open new dialog
		$('#editGroup').openDialog({
			autoOpen: false,
			height: 400,
			width: 300,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>groups/admin/group/" + group_id, function() {
				});
			},
			buttons: {
				'Save': function() {
					editPermissions();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#editGroup').dialog('option', 'height', 400);
		$('#editGroup').dialog('option', 'position', 'center');
		$('#editGroup').dialog('open');
	});
	
	/**
	 * Save group
	 */
	function saveGroup() {
		var submitUrl = $('#addEditGroupForm').attr('action');
		var action_type = $('#h_action_type').val();
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'addEditGroupForm',
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#addGroup').dialog('close');
					} else if (action_type == 'edit') {
						$('#editGroup').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchGroup();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}

	/**
	 * Edit permissions
	 */
	function editPermissions() {
		var submitUrl = $('#edit-permissions').attr('action');
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'edit-permissions',
			success: function(data) {
				if (data.status) {
					$('#editGroup').dialog('close');
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchGroup();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>