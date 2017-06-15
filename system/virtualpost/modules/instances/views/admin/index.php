<div class="ym-grid mailbox">
    <form id="customerSearchForm" action="<?php echo base_url()?>scans/incoming/add" method="post">
        <div class="ym-g80 ym-gl">
        	<div class="ym-grid input-item">
        		<div class="ym-g20 ym-gl" style="width: 100px">
        			<label style="text-align: left;">Name:</label>
        		</div>
        		<div class="ym-g80 ym-gl">
        		    <input type="text" id="searchCustomerForm_name" name="name" style="width: 250px"
    					value="" class="input-txt" maxlength=255 />
        		</div>
        	</div>
        	<div class="ym-clearfix"></div>
        	<div class="ym-grid input-item">
        		<div class="ym-g20 ym-gl" style="width: 100px">
        			<label style="text-align: left;">Domain:</label>
        		</div>
        		<div class="ym-g80 ym-gl">
        		    <input type="text" id="searchCustomerForm_domain_name" name="domain_name" style="width: 250px"
    					value="" class="input-txt" maxlength=255 />
    				<button id="searchCustomerButton" class="admin-button">Search</button>
                    <button id="addInstanceButton" class="admin-button">Add</button>
        		</div>
        	</div>
        </div>
	</form>
</div>
<div class="button_container">
    <div class="button-func">    
        
    </div>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
    	<table id="dataGridResult"></table>
    	<div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addInstance" title="Add Enterprise Customer" class="input-form dialog-form">
	</div>
	<div id="editCustomer" title="Edit Enterprise Customer" class="input-form dialog-form">
	</div>
</div>
<!-- Content for dialog -->
<div class="hide">
	<div id="viewDetailInstance" title="View Customer Details" class="input-form dialog-form">
	</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	$('#mailbox').css('margin', '20px 0 0 20px');
    $('button').button();
    
	// Call search method
	searchInstances();
	/**
	 * Search data
	 */
	function searchInstances() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>admin/instances';
		var tableH = $(document).height() - $($('#user-page').children()[0]).height() - $("#mailbox>.mailbox").height() - 150;
	    console.log(tableH);
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#customerSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
        	width: DATAGRID_WIDTH,
            height:tableH,
            rowNum: '<?php echo APContext::getAdminPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['Name', 'Domain Type','Domain Name', 'S3 Type','S3 Name', 'Database Type','Database Name', 'Action'],
            colModel:[
               {name:'name',index:'name', width: 200},
               {name:'domain_type',index:'domain_type', width: 100, sortable: false},
               {name:'domain_name',index:'domain_name', width: 200, sortable: true},
               {name:'s3_type',index:'s3_type', width: 100, sortable: false},
               {name:'s3_name',index:'s3_name', width: 200, sortable: true},
               {name:'database_type',index:'database_type', width: 100, sortable: false},
               {name:'database_name',index:'database_name', width: 200, sortable: true},
               {name:'id',index:'id', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
            },
            loadComplete: function() {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
		      + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
	}

	function toCustomerFormater(cellvalue, options, rowObject) {
		return '<a class="view_customer_detail" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[4] + '</a>';
	}

	/**
	 * Process when user click to search button
	 */
	$('#searchCustomerButton').click(function(e) {
		searchInstances();
		e.preventDefault();
	});

	/**
	 * Process when user click to add group button
	 */
	$('#addInstanceButton').click(function() {
	    // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#addInstance').openDialog({
			autoOpen: false,
			height: 550,
			width: 950,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>instances/admin/add", function() {
					$('#addEditInstanceForm_email').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveCustomer();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#addInstance').dialog('option', 'position', 'center');
		$('#addInstance').dialog('open');
	    return false;
	});

	/**
	 * Process when user click to edit icon.
	 */
	$('.managetables-icon-edit').live('click', function() {
		var customer_id = $(this).attr('data-id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#editCustomer').openDialog({
			autoOpen: false,
			height: 550,
			width: 950,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>instances/admin/edit?id=" + customer_id, function() {
					$('#addEditInstanceForm_email').focus();
				});
			},
			buttons: {
				'Save': function() {
					saveCustomer();
				},
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#editCustomer').dialog('option', 'position', 'center');
		$('#editCustomer').dialog('open');
	});

	/**
	 * Process when user click to delete icon.
	 */
	$('.managetables-icon-delete').live('click', function() {
	    var customer_id = $(this).attr('data-id');

		 // Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function() {
            	var submitUrl = '<?php echo base_url()?>instances/admin/delete?id=' + customer_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                        	 searchInstances();
                         } else {
                         	$.displayError(data.message);
                         }
                     }
                 });
            }
        });
	});
	
	/**
	 * Save Customer
	 */
	function saveCustomer() {
		var submitUrl = $('#addEditInstanceForm').attr('action');
		var action_type = $('#h_action_type').val();
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'addEditInstanceForm',
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#addInstance').dialog('close');
					} else if (action_type == 'edit') {
						$('#editCustomer').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchInstances();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}

	/**
	 * Save Customer
	 */
	function resetPasswordCustomer() {
		var submitUrl = $('#resetPasswordCustomerForm').attr('action');
		var action_type = $('#h_action_type').val();
		$.ajaxSubmit({
			url: submitUrl,
			formId: 'resetPasswordCustomerForm',
			success: function(data) {
				if (data.status) {
					if (action_type == 'add') {
					    $('#addInstance').dialog('close');
					} else if (action_type == 'edit') {
						$('#editCustomer').dialog('close');
					}
					$.displayInfor(data.message, null,  function() {
						// Reload data grid
						searchInstances();
					});
									
				} else {
					$.displayError(data.message);
				}
			}
		});
	}

	/**
	 * Generate invoice
	 */
	$('#generateInvoiceButton').live('click', function() {
		var customer_id = $(this).attr('data-id');
      	var submitUrl = '<?php echo base_url()?>customers/admin/generate_invoice_code?id=' + customer_id;
        $.ajaxExec({
           url: submitUrl,
           success: function(data) {
               if (data.status) {
                   // Reload data grid
           	       $('#addEditInstanceForm_invoice_code').val(data.data.invoice_code);
               } else {
                   $.displayError(data.message);
               }
           }
        });
        return false;
	});
	
	/**
	 * Process when user click to view detail customer information
	 */
	$('.view_customer_detail').live('click', function(){
		var customer_id = $(this).attr('data-id');
	    
		 // Clear control of all dialog form
	    $('.dialog-form').html('');

	    // Open new dialog
		$('#viewDetailInstance').openDialog({
			autoOpen: false,
			height: 600,
			width: 1200,
			modal: true,
			open: function() {
				$(this).load("<?php echo base_url() ?>customers/admin/view_detail_customer?id=" + customer_id, function() {
					$('#addEditInstanceForm_email').focus();
				});
			},
			buttons: {
				'Cancel': function () {
					$(this).dialog('close');
				}
			}
		});
		$('#viewDetailInstance').dialog('option', 'position', 'center');
		$('#viewDetailInstance').dialog('open');
	});
});
</script>