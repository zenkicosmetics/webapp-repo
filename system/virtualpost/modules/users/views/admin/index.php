<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('user_view_admin_index_UserManagement'); ?></h2>
</div>
<!--#1310 add name search in worker setting-->
<table>
    <tr>
    <form id="usersSearchForm" action="" method="post">
        <td class="ym-g10 ym-gl">
            <label style="font-size: 15px; text-align: left;"><?php admin_language_e('user_view_admin_index_SearchText'); ?></label>
        </td>
        <td class="ym-g22 ym-gl">
            <input type="text" id="usersSearchForm_enquiry" name="enquiry" style="width: 248px" value="" class="input-txt" maxlength=255 
                   placeholder="<?php admin_language_e('user_view_admin_index_SearchPlaceHolderText'); ?>"/>
        </td>
        <td class="ym-g4 ym-gl">
            <button id="searchUserButton" class="admin-button"><?php admin_language_e('user_view_admin_index_SearchBtn'); ?></button> 
        </td>
    </form>
        <td class="ym-g10 ym-gl"> 
            <button id="addUserButton" class="admin-button"><?php admin_language_e('user_view_admin_index_AddUserBtn'); ?></button>
        </td>
    </tr>
</table>
<div id="searchTableResult" style="margin-top: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addUser" title="<?php admin_language_e('user_view_admin_index_AddUserPopup'); ?>" class="input-form dialog-form">
	</div>
	<div id="editUser" title="<?php admin_language_e('user_view_admin_index_EditUserPopup'); ?>" class="input-form dialog-form">
	</div>
	<div id="changePasswordUser" title="<?php admin_language_e('user_view_admin_index_ChangePasswordPopup'); ?>" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    
     $("#usersSearchForm_enquiry, .admin-button").prop('readonly', true);
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
		var tableH = $.getTableHeight()-30;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usersSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'userName',
            viewrecords: true,
            shrinkToFit: true,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('user_view_admin_index_ID'); ?>',
                '<?php admin_language_e('user_view_admin_index_UserName'); ?>',
                '<?php admin_language_e('user_view_admin_index_Email'); ?>', 
                '<?php admin_language_e('user_view_admin_index_Group'); ?>', 
                '<?php admin_language_e('user_view_admin_index_Active'); ?>',
                '<?php admin_language_e('user_view_admin_index_Location'); ?>',
                '<?php admin_language_e('user_view_admin_index_CreatedDate'); ?>',
                '<?php admin_language_e('user_view_admin_index_LastVisit'); ?>',
                '<?php admin_language_e('user_view_admin_index_Status'); ?>', 
                '<?php admin_language_e('user_view_admin_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'username',index:'username', width:170},
               {name:'email',index:'email', width:225},
               {name:'groupname',index:'groupname', width:150},
               {name:'active',index:'active', width:100, align:"center", formatter: activeFormater},
               {name:'location_name',index:'location_name', width:180},
               {name:'created_on',index:'created_on', width:100},
               {name:'last_login',index:'last_login', width:100},
               {name:'status',index:'status', width:60, align:"center", formatter: statusFormater},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            },
            loadComplete: function() {
                 $("#usersSearchForm_enquiry, .admin-button").prop('readonly', false).button("enable");
                $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
            }
        });
	}

	function statusFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<?php admin_language_e('user_view_admin_index_StatusDeleted'); ?>';
		} else {
			return '';
		}
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('user_view_admin_index_Check'); ?></span></span>';
		} else {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete"><?php admin_language_e('user_view_admin_index_UnCheck'); ?></span></span>';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="<?php admin_language_e('user_view_admin_index_Edit'); ?>"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="<?php admin_language_e('user_view_admin_index_Delete'); ?>"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-change-pass" data-id="' + cellvalue + '" title="<?php admin_language_e('user_view_admin_index_ChangePassword'); ?>"></span></span>';
	}

	/**
	 * Process when user click to search button
	 */
	$('#searchUserButton').click(function(e) {
        $("#usersSearchForm_enquiry, .admin-button").prop('readonly', true).button("disable");
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
			height: 590,
			width: 1100,
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
			height: 590,
			width: 1100,
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
            message: '<?php admin_language_e('user_view_admin_index_ConfirmDeleteMessage'); ?>',
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
        var group_id = $("#group_id");
        $('option', group_id).prop('selected', true);
        
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