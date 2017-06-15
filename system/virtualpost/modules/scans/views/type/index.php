<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('scan_view_type_index_TypeManagement'); ?></h2>
</div>
<div class="button_container">
    <div class="button-func">
        <button id="addUserButton" class="admin-button"><?php admin_language_e('scan_view_type_index_AddBtn'); ?></button>
    </div>
</div>

<div id="searchTableResult" style="margin-top: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addUser" title="<?php admin_language_e('scan_view_type_index_AddType'); ?>" class="input-form dialog-form">
	</div>
	<div id="editUser" title="<?php admin_language_e('scan_view_type_index_EditType'); ?>" class="input-form dialog-form">
	</div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	$('#mailbox').css('margin', '20px 0 0 20px');
    $('button').button();
    
	// Call search method
	searchTypes();
	
	/**
	 * Search data
	 */
	function searchTypes() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>scans/type';
		var tableH = $.getTableHeight()- 30;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height:  tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'LabelValue',
            viewrecords: true,
            shrinkToFit: true,
            multiselect: false,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('scan_view_type_index_ID'); ?>',
                '<?php admin_language_e('scan_view_type_index_TypeName'); ?>', 
                '<?php admin_language_e('scan_view_type_index_Customs'); ?>', 
                '<?php admin_language_e('scan_view_type_index_Type'); ?>', 
                '', 
                '<?php admin_language_e('scan_view_type_index_Action'); ?>'
            ],
            colModel:[
               {name:'SettingKey',index:'SettingKey', hidden: true},
               {name:'LabelValue',index:'LabelValue', width:200},
               {name:'CustomsFlag',index:'CustomsFlag', width:100, sortable: false},
               {name:'Type',index:'Type', width:100, sortable: false},
               {name:'ActualValue',index:'ActualValue', hidden: true},
               {name:'SettingKey',index:'SettingKey', width:75, sortable: false, align:"center", formatter: actionFormater}
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
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('scan_view_type_index_Check'); ?></span></span>';
		} else {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete"><?php admin_language_e('scan_view_type_index_UnCheck'); ?></span></span>';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('scan_view_type_index_Edit'); ?>"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('scan_view_type_index_Delete'); ?>"></span></span>';
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
			height: 260,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>scans/type/add", function() {
					$('#username').focus();
				});
			},
			buttons: {
				'<?php admin_language_e('scan_view_type_index_SaveBtn'); ?>': function() {
					saveType();
				},
				'<?php admin_language_e('scan_view_type_index_CancelBtn'); ?>': function () {
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
			height: 260,
			width: 450,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>scans/type/edit?SettingKey=" + user_id, function() {
					$('#LabelValue').focus();
				});
			},
			buttons: {
				'<?php admin_language_e('scan_view_type_index_SaveBtn'); ?>': function() {
					saveType();
				},
				'<?php admin_language_e('scan_view_type_index_CancelBtn'); ?>': function () {
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
            message: '<?php admin_language_e('scan_view_type_index_ConfirmDeleteMessage'); ?>',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>scans/type/delete?SettingKey=' + user_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchTypes();
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
	function saveType() {
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
						searchTypes();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}
});
</script>