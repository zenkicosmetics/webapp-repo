<?php
    ci()->load->library('invoices/invoices_api');
    $invoice_number = invoices_api::generateInvoiceNumber();
    $currency_short = APUtils::get_currency_short_in_user_profiles();
?>
/**
 * Process when user click to view detail customer information
 */
$('.view_customer_detail').live('click', function(){
	var customer_id = $(this).attr('data-id');

	 // Clear control of all dialog form
    $('.dialog-form').html('');

    // Open new dialog
	$('#viewDetailCustomer').openDialog({
		autoOpen: false,
		height: 600,
		width: 1200,
        title: "<?php admin_language_e('customer_js_js_customer_info_ViewCustomerDetails'); ?>",
		modal: true,
		open: function() {
			$(this).load("<?php echo base_url() ?>customers/admin/view_detail_customer?id=" + customer_id, function() {
			$('#addEditCustomerForm_email').focus();
		});
	},
	buttons: {
        '<?php admin_language_e('customer_js_js_customer_info_ViewVerificationDetails'); ?>': function () {
           var url =  '<?php echo base_url()?>cases/todo/view_verification_detail?cid=' + customer_id;
           window.open(url);
        },
		'<?php admin_language_e('customer_js_js_customer_info_Payment'); ?>': function () {
			recordExternalPayment(customer_id);
		},
		'<?php admin_language_e('customer_js_js_customer_info_Credit'); ?>': function () {
			recordRefundPayment(customer_id);
		},
		'<?php admin_language_e('customer_js_js_customer_info_Charge'); ?>': function () {
			createDirectCharge(customer_id);
		},
		'<?php admin_language_e('customer_js_js_customer_info_ChargeInvoice'); ?>': function () {
			createDirectChargeInvoice(customer_id);
		},
		'<?php admin_language_e('customer_js_js_customer_info_Invoice'); ?>': function () {
			createDirectInvoice(customer_id);
		},
		'<?php admin_language_e('customer_js_js_customer_info_Cancel'); ?>': function () {
			$(this).dialog('close');
		}
	}
});
$('#viewDetailCustomer').dialog('option', 'position', 'center');
$('#viewDetailCustomer').dialog('open');
});

/**
 * Create direct charge
 */
function createDirectCharge(customer_id) {
	// Check if the customer has no credit card first!
	var submitUrl = "<?php echo base_url() ?>customers/admin/check_customer_has_no_credit_card";
    $.ajaxExec({
        url: submitUrl,
        data: {
            id: customer_id
        },
        success: function(data) {
            if (data.status) {

				 // Clear control of all dialog form
				$('#createDirectChargeWithoutInvoice').html('');

				// Open new dialog
				$('#createDirectChargeWithoutInvoice').openDialog({
					autoOpen: false,
					height: 250,
					width: 500,
					modal: true,
                    title: "<?php admin_language_e('customer_js_js_customer_info_CreateDirectCharge'); ?>",
					open: function() {
						$(this).load("<?php echo base_url() ?>customers/admin/create_direct_charge_without_invoice?id=" + customer_id, function() {
						});
					},
					buttons: {
						'Submit': function () {
							saveDirectChargeWithoutInvoice(customer_id);
						}
					}
				});
				$('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
				$('#createDirectChargeWithoutInvoice').dialog('open');

			} else {
				$.displayError(data.message);
			}
		}
	});
};

/**
 * Save direct charge without invoice
 */
function saveDirectChargeWithoutInvoice(customer_id) {
	var submitUrl = "<?php echo base_url() ?>customers/admin/save_direct_charge_without_invoice?id=" + customer_id;
	$.ajaxSubmit({
		url: submitUrl,
		formId: 'createDirectChargeWithoutInvoiceForm',
		success: function(data) {
			if (data.status) {
				$('#createDirectChargeWithoutInvoice').dialog('close');
				$.displayInfor(data.message, null,  function() {
				});
			} else {
				$.displayError(data.message);
			}
		}
	});
}

/**
 * Create direct charge
 */
function recordExternalPayment(customer_id) {
    // Clear control of all dialog form
    $('#recordExternalPayment').html('');
    $('#recordRefundPayment').html('');
    $('#recordRefundPayment').dialog('destroy');

    // Open new dialog
    $('#recordExternalPayment').openDialog({
            autoOpen: false,
            height: 300,
            width: 600,
            modal: true,
            title: "<?php admin_language_e('customer_js_js_customer_info_RecordExternalPayment'); ?>",
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/admin/record_external_payment?id=" + customer_id, function() {
                });
            },
            buttons: {
                'Submit': function () {
                        saveExternalPayment(customer_id);
                }
            }
    });
    $('#recordExternalPayment').dialog('option', 'position', 'center');
    $('#recordExternalPayment').dialog('open');
};


/**
 * Save external payment
 */
function saveExternalPayment(customer_id) {
    var submitUrl = "<?php echo base_url() ?>customers/admin/save_external_payment?id=" + customer_id;
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'recordExternalPaymentForm',
        success: function(data) {
            if (data.status) {
                $('#recordExternalPayment').dialog('close');
                $.displayInfor(data.message, null,  function() {

                });
            } else {
                if (data.data != null && data.data.pending_activity_flag == true) {
                    $('#recordExternalPayment').dialog('close');
                    // Open this pending activity
                    openListPendingPrepaymentActivity(customer_id);
                } else {
                    $.displayError(data.message);
                }
                
            }
        }
    });
}

/**
 * Create direct charge
 */
function openListPendingPrepaymentActivity(customer_id) {
    // Clear control of all dialog form
    if (! $('#listPendingPrepaymentActivity').length) {
        // Append div to document
        var newDialogHtml = '<div id="listPendingPrepaymentActivity" title="Complete Pending Activity" class="input-form dialog-form"></div>';
        $('#content-wrapper').prepend(newDialogHtml);
    }
    $('#listPendingPrepaymentActivity').html('');

    // Open new dialog
    $('#listPendingPrepaymentActivity').openDialog({
            autoOpen: false,
            height: 450,
            width: 680,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/admin/list_prepayment_activity?id=" + customer_id, function() {
                });
            },
            buttons: {
                'Submit': function () {
                    saveListPendingPrepaymentActivity(customer_id);
                }
            }
    });
    $('#listPendingPrepaymentActivity').dialog('option', 'position', 'center');
    $('#listPendingPrepaymentActivity').dialog('open');
};

/**
 * Save external payment
 */
function saveListPendingPrepaymentActivity(customer_id) {
    var selIds = $("#dataGridlistPendingPrepaymentActivityDetailResult").jqGrid("getGridParam", "selarrrow");
    var listActivity = [];
    var selectedCost = 0;
    if (selIds.length == 0) {
        $.displayError('Please select pending activity.');
        return;
    }
    for (i = 0, n = selIds.length; i < n; i++) {
        var dataRow = $("#dataGridlistPendingPrepaymentActivityDetailResult").getRowData(selIds[i]);
        selectedCost = selectedCost + parseFloat(dataRow.eur_net_amount);
        var activity = {};
        activity.envelope_id = dataRow.id;
        activity.activity_id = dataRow.activity_id;
        listActivity.push(activity);
    }
    var availCost = parseFloat($('#listPendingPrepaymentActivityForm_total_avail_cost').val());
    console.log('Selected Cost:' + selectedCost);
    if (selectedCost > availCost) {
        $.displayError('Can not complete this activity because the deposit is not high enough.');
        return;
    }
    $('#listPendingPrepaymentActivityForm_list_pending_activity').val(JSON.stringify(listActivity));
    var submitUrl = "<?php echo base_url() ?>customers/admin/submit_list_prepayment_activity?id=" + customer_id;
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'listPendingPrepaymentActivityForm',
        success: function(data) {
            if (data.status) {
                $('#listPendingPrepaymentActivity').dialog('close');
                $.displayInfor(data.message, null,  function() {

                });
            } else {
                $.displayError(data.message);
            }
        }
    });
}

/**
 * Create direct charge
 */
function createDirectChargeInvoice(customer_id) {
	 // Clear control of all dialog form
    $('#createDirectInvoice').html('');
    $('#createDirectCharge').html('');
    $('#createDirectInvoice').dialog('destroy');
    $('#createDirectCharge').dialog('destroy');
    $('#recordRefundPayment').html('');
    $('#recordRefundPayment').dialog('destroy');
    $('#recordExternalPayment').html('');
    $('#recordExternalPayment').dialog('destroy');
    

    // Open new dialog
	$('#createDirectCharge').openDialog({
		autoOpen: false,
		height: 600,
		width: 800,
		modal: true,
        title: "<?php admin_language_e('customer_js_js_customer_info_CreateDirectChargeInvoice'); ?>",
		open: function() {
			$(this).load("<?php echo base_url() ?>customers/admin/create_direct_charge?id=" + customer_id, function() {
			});
		},
		buttons: {
			'Submit': function () {
				saveDirectCharge(customer_id);
			}
		}
	});
	$('#createDirectCharge').dialog('option', 'position', 'center');
	$('#createDirectCharge').dialog('open');
};

var selected_column = ["description", "quantity", "net_price"];
var meta_data_column = {
	description: {data_type: 'string', allow_null: false, max_length: 255, display_name: 'Description'},
	quantity: {data_type: 'integer', allow_null: false, max_length: 0, display_name: 'Quantity'},
	net_price: {data_type: 'double', allow_null: false, max_length: 0, display_name: 'Net Price'}
};

/**
 * Validate data input.
 */
function validateData() {
    var gridData = $("#dataGridDirectChargeDetailResult").jqGrid('getGridParam','data');
    var submitData = [];
    var lengthData = gridData.length;
    var obj = "";
    console.log("griddata", gridData);
    
    for (var i=0; i < lengthData; i++) {
        var data_row = gridData[i];
        if (data_row.description != '') {
        	data_row.net_price = $.replaceAll(data_row.net_price, ',', '.');

        	// #472: add vat case
        	console.log("vat_case_value", data_row.vat_case);
        	//data_row.vat_case = $("#"+data_row.id+"_vat_case").val();
            var valid_data = validateDataRow(data_row, i+1);
            if (valid_data && data_row.vat_case != '') {
            	submitData.push(data_row);
            } else {
                $.displayError('Data row ' + (i+1) + ' is invalid. Please correct it before submit.');
                return '';
            }
        }
    }

    if (submitData.length == 0) {
    	$.displayError('Please declare invoice information.');
        return '';
    }
    return submitData;
}

 /**
 * Validate data row input.
 */
function validateDataRow(data_row, row_index) {

    var row_error = false;
    var column_error = false;
    // For each data column

    $.each (selected_column, function(i, column) {
        var data_type = meta_data_column[column].data_type;
        var allow_null = meta_data_column[column].allow_null;
        var max_length = meta_data_column[column].max_length;
        var cell_value = data_row[column];
        column_error = false;
        // Validate required
        if (!allow_null) {
            if ($.isEmpty(cell_value)) {
                // Log message
                row_error = true;
                column_error = true;

                // Highlight cell color
                highlightError(row_index, column);
            }
        }

        // Validate data type
        if ($.isNotEmpty(cell_value)) {
            if (data_type == "integer") {
                if (!$.isValidInt(cell_value)) {
                	console.log('Integer value of column name: ' + column + ' is invalid.');
                    // Log message
                    row_error = true;
                    column_error = true;

                    // Highlight cell color
                    highlightError(row_index, column);
                }
            } else if (data_type == "double") {
                if (!$.isValidNumber(cell_value)) {
                    console.log('Double value of column name: ' + column + ' is invalid.');
                    // Log message
                    row_error = true;
                    column_error = true;

                    // Highlight cell color
                    highlightError(row_index, column);
                }
            }
        }

        // Validate max length
        if ($.isNotEmpty(cell_value) && max_length > 0) {
           if (data_type == "string") {
               if (cell_value.length > max_length) {
            	   console.log('Max length of column name: ' + column + ' is invalid.');
                    // Log message
                    row_error = true;
                    column_error = true;

                    // Highlight cell color
                    highlightError(row_index, column);
               }
           }
        }

        // Remove cell hightlight if cell ok
        if (!column_error) {
        	$("#dataGridDirectChargeDetailResult").jqGrid('setCell', row_index, column,"",{color:'#000'});
        } else {
        	$("#dataGridDirectChargeDetailResult").jqGrid('setCell', row_index, column,"",{color:'red'});
        }
    });

    // Remove highlight if no error occur
    if (!row_error) {
        removeHighlightError(row_index, '');
    } else {
    	highlightError(row_index, '');
    }
    return !row_error;
}
/**
 * Highlight cell color & background of row color
 */
function highlightError(row_id, column_name) {
    $("#dataGridDirectChargeDetailResult").jqGrid('setCell', row_id, column_name,"",{color:'red'});
    $('#' + (row_id)).addClass('ui-state-error');
}

/**
 * Remove highlight error.
 */
function removeHighlightError(row_id, column_name) {
    $("#dataGridDirectChargeDetailResult").jqGrid('setCell', row_id , column_name,"",{color:'#000'});
    $('#' + (row_id )).removeClass('ui-state-error');
}

// Calculate data
$('#calculateData').live('click', function(){
	var currency_short =  '<?php echo $currency_short?>';
	var submitData = validateData();
	if (submitData == '') {
	    return false;
	} else {
	    // #472: sum by vat case.
	    console.log("===============>>>>>>submit data==================");
	    console.log(submitData);
	    console.log("===============<<<<<<<<< submit data==================");
		
		var lengthData = submitData.length;
		var sum_amount = 0;
		var vat_amount = 0;
		var vat_case = 0;
		var vat = 0;
		console.log(lengthData);
		for (i = 0; i < lengthData; i++) {
		  console.log(submitData[i]);
		    vat = 0;
		    vat_case = 0;
		    // calculate vat from string.
		    if(submitData[i].vat_case != undefined){
		      vat_case = submitData[i].vat_case;
		      if(vat_case == '<?php echo APConstants::VAT_LOCAL_SERVICE_LABEL?>'){
		          vat = $("#vat_local_service_id").val();
		      }else if(vat_case == '<?php echo APConstants::VAT_DIGITAL_GOOD_LABEL?>'){
		          vat = $("#vat_digital_good_id").val();
		      }else if(vat_case == '<?php echo APConstants::VAT_SHIPPING_LABEL?>'){
		          vat = $("#vat_shipping_id").val();
		      }
		    }
		    
		    console.log("vat_case: ", vat);
		    sum_amount += submitData[i].quantity * submitData[i].net_price ;
		    vat_amount += submitData[i].quantity * submitData[i].net_price * vat;
		}
		
		var total_amount = sum_amount + Math.round(vat_amount*100)/100;
		
		$('#createDirectChargeDetailResult_Sum').html($.formatMoney(sum_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_VAT').html($.formatMoney(vat_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_Total').html($.formatMoney(total_amount) + ' ' + currency_short);
	}
});

/**
 * Make direct charge
 */
function saveDirectCharge() {
	var currency_short =  '<?php echo $currency_short?>';
	var submitData = validateData();
	if (submitData == '') {
	    return false;
	} else {
		var vat = $('#createDirectCharge_vat').val();
		var lengthData = submitData.length;
		var sum_amount = 0;
		for (i = 0; i < lengthData; i++) {
			sum_amount += submitData[i].quantity * submitData[i].net_price ;
		}
		var vat_amount = sum_amount * vat;
		var total_amount = sum_amount + Math.round(vat_amount*100)/100;
		$('#createDirectChargeDetailResult_Sum').html($.formatMoney(sum_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_VAT').html($.formatMoney(vat_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_Total').html($.formatMoney(total_amount) + ' ' +currency_short);
	}

	var customer_id = $('#createDirectCharge_customer_id').val();
	var customs_data = JSON.stringify(submitData);
    // Submit to server and refresh
	var submitUrl = '<?php echo base_url()?>admin/customers/save_direct_charge?customer_id=' + customer_id;
    $.ajaxExec({
         url: submitUrl,
         data: {
             customs_data: customs_data, 
             invoice_date: $('#createDirectChargeDetailResult_InvoiceDate').val(),
             location_id: $('#createDirectChargeDetailResult_location_id').val()
         },
         success: function(data) {
             if (data.status) {
            	 $.displayInfor(data.message, null, function() {
            		 $('#createDirectCharge').dialog('close');
                 });
             } else {
             	$.displayError(data.message);
             }
         }
     });
}

/**
 * Create direct charge
 */
function recordRefundPayment(customer_id) {
	 // Clear control of all dialog form
    $('#recordRefundPayment').html('');
    $('#recordRefundPayment').dialog('destroy');
    $('#recordExternalPayment').html('');
    $('#recordExternalPayment').dialog('destroy');
    $('#createDirectInvoice').html('');
    $('#createDirectCharge').html('');
    $('#createDirectInvoice').dialog('destroy');
    $('#createDirectCharge').dialog('destroy');

    // Open new dialog
	$('#recordRefundPayment').openDialog({
		autoOpen: false,
		height: 600,
		width: 900,
		modal: true,
        title: "<?php admin_language_e('customer_js_js_customer_info_RecordCredit'); ?>",
		open: function() {
			$(this).load("<?php echo base_url() ?>customers/admin/record_refund_payment?id=" + customer_id, function() {
			});
		},
		buttons: {
			'Submit': function () {
				saveRefundPayment(customer_id);
			}
		}
	});
	$('#recordRefundPayment').dialog('option', 'position', 'center');
	$('#recordRefundPayment').dialog('open');
};


/**
 * Save external payment
 */
function saveRefundPayment(customer_id) {
	var currency_short =  '<?php echo $currency_short?>';	
	var submitData = validateData();
	if (submitData == '') {
	    return false;
	} else {
		var vat = $('#createDirectCharge_vat').val();
		var lengthData = submitData.length;
		var sum_amount = 0;
		for (i = 0; i < lengthData; i++) {
			sum_amount += submitData[i].quantity * submitData[i].net_price;
		}
		var vat_amount = sum_amount * vat ;
		var total_amount = sum_amount + Math.round(vat_amount*100)/100;
		$('#createDirectChargeDetailResult_Sum').html($.formatMoney(sum_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_VAT').html($.formatMoney(vat_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_Total').html($.formatMoney(total_amount) + ' ' + currency_short);
	}

	var customer_id = $('#createDirectCharge_customer_id').val();
    var rev_share = $("#createDirectChargeDetailResult_RevShare").val();
	var customs_data = JSON.stringify(submitData);
    // Submit to server and refresh
	var submitUrl = '<?php echo base_url()?>admin/customers/save_credit_note?customer_id=' + customer_id;
    $.ajaxExec({
         url: submitUrl,
         data: {
             customs_data: customs_data, 
             invoice_date: $('#createDirectChargeDetailResult_InvoiceDate').val(),
             location_id: $('#createDirectChargeDetailResult_location_id').val(),
             rev_share: rev_share
         },
         success: function(data) {
             if (data.status) {
            	 $.displayInfor(data.message, null, function() {
            		 $('#recordRefundPayment').dialog('close');
                 });
             } else {
             	$.displayError(data.message);
             }
         }
     });
}

/**
* direct invoice.
*/
function createDirectInvoice(customer_id){
	// Clear control of all dialog form
	$('#createDirectInvoice').html('');
    $('#createDirectCharge').html('');
    $('#createDirectInvoice').dialog('destroy');
    $('#createDirectCharge').dialog('destroy');
    $('#recordRefundPayment').html('');
    $('#recordRefundPayment').dialog('destroy');
    $('#recordExternalPayment').html('');
    $('#recordExternalPayment').dialog('destroy');

    // Open new dialog
	$('#createDirectInvoice').openDialog({
		autoOpen: false,
		height: 600,
		width: 800,
		modal: true,
        title: "<?php admin_language_e('customer_js_js_customer_info_CreateDirectInvoice'); ?>",
		open: function() {
			$(this).load("<?php echo base_url() ?>customers/admin/create_direct_invoice?id=" + customer_id, function() {
			});
		},
		buttons: {
			'Submit': function () {
				// save direct invoice.
				saveDirectInvoice();
			}
		}
	});
	$('#createDirectInvoice').dialog('option', 'position', 'center');
	$('#createDirectInvoice').dialog('open');
}

/**
 * Make direct invoice
 */
function saveDirectInvoice() {
	var currency_short = '<?php echo $currency_short?>';
	var submitData = validateData();
	if (submitData == '') {
	    return false;
	} else {
		var vat = $('#createDirectCharge_vat').val();
		var lengthData = submitData.length;
		var sum_amount = 0;
		for (i = 0; i < lengthData; i++) {
			sum_amount += submitData[i].quantity * submitData[i].net_price;
		}
		var vat_amount = sum_amount * vat;
		var total_amount = sum_amount + Math.round(vat_amount*100)/100;
		$('#createDirectChargeDetailResult_Sum').html($.formatMoney(sum_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_VAT').html($.formatMoney(vat_amount) + ' ' + currency_short);
		$('#createDirectChargeDetailResult_Total').html($.formatMoney(total_amount) + ' ' +currency_short);
	}

	var customer_id = $('#createDirectCharge_customer_id').val();
    var rev_share = $("#createDirectChargeDetailResult_RevShare").val();
	var customs_data = JSON.stringify(submitData);
    // Submit to server and refresh
	var submitUrl = '<?php echo base_url()?>admin/customers/save_direct_invoice?customer_id=' + customer_id;
    $.ajaxExec({
         url: submitUrl,
         data: {
             customs_data: customs_data, 
             invoice_date: $('#createDirectChargeDetailResult_InvoiceDate').val(),
             location_id:  $('#createDirectChargeDetailResult_location_id').val(),
             customer_id : customer_id,
             rev_share: rev_share
         },
         success: function(data) {
             if (data.status) {
            	 $.displayInfor(data.message, null, function() {
            		 $('#createDirectInvoice').dialog('close');
                 });
             } else {
             	$.displayError(data.message);
             }
         }
     });
}