<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">Partner Admin Managerment</h2>
</div>
<div class="button_container">
    <div class="button-func">
        <button id="addUserButton" class="admin-button">Add Partner User</button>
    </div>
</div>

<div id="searchTableResult" style="margin-top: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addUser" title="Add Partner User" class="input-form dialog-form">
	</div>
	<div id="editUser" title="Edit Partner User" class="input-form dialog-form">
	</div>
	<div id="changePasswordUser" title="Change Password" class="input-form dialog-form">
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
		var url = '<?php echo base_url() ?>users/admin';
		var tableH = $(document).height() - $($('#user-page').children()[0]).height() - $("#mailbox>.mailbox").height() - 150;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: tableH,
            width: 1240,
            rowNum: '<?php echo APContext::getAdminPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'UserName',
            viewrecords: true,
            shrinkToFit: true,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','User Name', 'Email', 'Group', 'Active', 'Partner', 'Created Date', 'Last visit', 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'username',index:'username', width:170},
               {name:'email',index:'email', width:225},
               {name:'groupname',index:'groupname', width:150},
               {name:'active',index:'active', width:100, align:"center", formatter: activeFormater},
               {name:'partner_name',index:'partner_name', width:180},
               {name:'created_on',index:'created_on', width:100},
               {name:'last_login',index:'last_login', width:100},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen(1240);
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
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-change-pass" data-id="' + cellvalue + '" title="Change Password"></span></span>';
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
			height: 560,
			width: 460,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>users/admin/add", function() {
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
			height: 540,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>users/admin/edit?id=" + user_id, function() {
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
            	var submitUrl = '<?php echo base_url()?>users/admin/delete?id=' + user_id;
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

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-change-pass').live('click', function() {
		var user_id = $(this).attr('data-id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#changePasswordUser').openDialog({
			autoOpen: false,
			height: 300,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>users/admin/change_pass?id=" + user_id, function() {
				});
			},
			buttons: {
				'Save': function() {
					resetPasswordUser();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#changePasswordUser').dialog('option', 'position', 'center');
		$('#changePasswordUser').dialog('open');
	});

	/**
	 * Save Customer
	 */
	function resetPasswordUser() {
		var submitUrl = $('#resetPasswordUserForm').attr('action');
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'resetPasswordUserForm',
			success: function(data) {
				if (data.status) {
					$('#changePasswordUser').dialog('close');
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