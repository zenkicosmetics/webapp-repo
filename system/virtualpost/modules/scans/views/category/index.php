<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">Category Management</h2>
</div>
<div class="button_container">
    <div class="button-func">
        <button id="addUserButton" class="admin-button">Add</button>
    </div>
</div>

<div id="searchTableResult" style="margin-top: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addUser" title="Add Category" class="input-form dialog-form">
	</div>
	<div id="editUser" title="Edit Category" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	$('#mailbox').css('margin', '20px 0 0 20px');
    $('button').button();
    
	// Call search method
	searchUsers();
	
	/**
	 * Search data
	 */
	function searchUsers() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>scans/category';
//		var tableH = $.getTableHeight() - 50;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height:($(window).height()- 40), //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'LabelValue',
            viewrecords: true,
            shrinkToFit: true,
            multiselect: false,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','Category Name','', 'Action'],
            colModel:[
               {name:'SettingKey',index:'SettingKey', hidden: true},
               {name:'LabelValue',index:'LabelValue', width:400},
               {name:'ActualValue',index:'ActualValue', hidden: true},
               {name:'SettingKey',index:'SettingKey', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            },
            loadComplete: function() {
            	$.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
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
	 * Process when user click to search button
	 */
	$('#searchUserButton').click(function(e) {
		searchUsers();
		e.preventDefault();
	});

	/**
	 * Process when user click to add group button
	 */
	$('#addUserButton').click(function() {
	    // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#addUser').openDialog({
			autoOpen: false,
			height: 180,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>scans/category/add", function() {
					$('#username').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveUser();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#addUser').dialog('option', 'position', 'center');
		$('#addUser').dialog('open');

	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
	    var user_id = $(this).data('id');
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#editUser').openDialog({
			autoOpen: false,
			height: 180,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>scans/category/edit?SettingKey=" + user_id, function() {
					$('#LabelValue').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveUser();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#editUser').dialog('option', 'position', 'center');
		$('#editUser').dialog('open');
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var user_id = $(this).data('id');

		// Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>scans/category/delete?SettingKey=' + user_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchUsers();
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
	function saveUser() {
		var submitUrl = $('#addEditUserForm').attr('action');
		var action_type = $('#h_action_type').val();
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'addEditUserForm',
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#addUser').dialog('close');
					} else if (action_type == 'edit') {
						$('#editUser').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchUsers();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>