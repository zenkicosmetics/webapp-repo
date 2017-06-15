<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_transaction_report_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="transactionReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 150px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_transaction_report_LblSearch'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="transactionReportingSearchForm_keyword" name="enquiry" placeholder="<?php admin_language_e('report_views_admin_transaction_report_Placeholder'); ?>"
                           style="width: 350px" value="" class="input-txt" />
                </div>
            </div>
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 150px">
                    <label style="text-align: left;">From:</label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_year,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_year,
                        "name" => 'year',
                        "id" => 'year',
                        "clazz" => 'input-txt',
                        "style" => 'width: 80px',
                        "has_empty" => true
                    ));
                    ?>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_month,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_month,
                        "name" => 'month',
                        "id" => 'month',
                        "clazz" => 'input-txt',
                        "style" => 'width: 80px',
                        "has_empty" => true
                    ));
                    ?>
                </div>
                <div class="ym-g20 ym-gl" style="width:28px">
                    <label style="text-align: left;">To:</label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_year,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_year,
                        "name" => 'to_year',
                        "id" => 'to_year',
                        "clazz" => 'input-txt',
                        "style" => 'width: 78px',
                        "has_empty" => true
                    ));
                    ?>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_month,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_month,
                        "name" => 'to_month',
                        "id" => 'to_month',
                        "clazz" => 'input-txt',
                        "style" => 'width: 78px',
                        "has_empty" => true
                    ));
                    ?>
                </div>

                <button style="margin-left: 20px" id="transactionReportingButton" class="admin-button"><?php admin_language_e('report_views_admin_transaction_report_BtnSearch'); ?></button>
                <button style="margin-left: 20px" id="transactionExportCSVButton" class="admin-button"><?php admin_language_e('report_views_admin_transaction_report_BtnExport'); ?></button>
            </div>

        </div>
    </form>
</div>
<div class="button_container">
    <div class="button-func"></div>
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
    <input type="file" id="imagepath_banner" name="imagepath" style="display: none; visibility: hidden;" />
</div>
<!-- Content for dialog -->
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_transaction_report_DisPdfInv'); ?></a>
    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form">
	</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
     //#1297 check all tables in the system to minimize wasted space
    var tableH = $.getTableHeight() + 10;
    
    $('button').button();
    
	// Call search method
    transactionReportings();

    /**
	 * Process when user click to search button
	 */
	$('#transactionReportingButton').live('click', function(e) {
		transactionReportings();
		e.preventDefault();
	});

	/**
	 * Search data
     * function transactionReportings()
	 */
	function transactionReportings() {
        
		$("#dataGridResult").jqGrid('GridUnload');
        
        // Url defined
		var url = '<?php echo base_url() ?>admin/report/transaction_report_search';
        
        // search 
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#transactionReportingSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
        	width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space 
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'tran_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            rownumbers: true,
            captions: '',
            colNames:['',
                '<?php admin_language_e('report_views_admin_transaction_report_ColCusId'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColCusName'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColCompany'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColEmail'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColTransId'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColDate'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColAmount'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColStatus'); ?>',
                '<?php admin_language_e('report_views_admin_transaction_report_ColAction'); ?>',
                ''],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'customer_id', index:'customer_id', hidden: true},
               {name:'name', index:'name', width: 280, sortable: false},
               {name:'company',index:'company', width: 280, sortable: false},
               {name:'email', index:'email', width: 350, sortable: false, formatter: toCustomerFormater},
               {name:'txid', index:'txid', width: 250, sortable: false},
               {name:'tran_date',index:'tran_date', width: 200, align: "center", sortable: false},
               {name:'amount', index:'amount', width: 250, sortable: false},
               {name:'tran_status', index:'tran_status', width: 100, align: "center", sortable: false},
               {name: 'id', ndex: 'id', width: 100, align: "center", sortable: false, formatter: actionFormater},
               {name:'tran_type',index:'tran_type', hidden: true}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));   //#1297 check all tables in the system to minimize wasted space
            }
        });
	}
    
    // #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
     function actionFormater(cellvalue, options, rowObject) {
        var check_user = '<?php echo $check_super_admin?>';
        if(check_user){
               return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id-tran="'
                   + cellvalue + '" data-customer-id="'+ rowObject[1] + '" data-tran-type="'+ rowObject[10]
                   + '" title="<?php admin_language_e('report_views_admin_transaction_report_TitDelete'); ?>"></span></span>';
        } else {
            return '';
        }
    }

	function toCustomerFormater(cellvalue, options, rowObject) {
		return '<a class="view_customer_detail" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[4] + '</a>';
	}
	
	function toCustomerFormater02(cellvalue, options, rowObject) {
		return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[3] + '</a>';
	}

	$('#transactionExportCSVButton').live('click', function() {
	    $('#transactionReportingSearchForm').attr('action', '<?php echo base_url() ?>admin/report/transaction_report_export');
	    $('#transactionReportingSearchForm').submit();
	});

	$("#transactionImportCSVButton").live('click', function (){
    	$('#imagepath_banner').click();
    	return false;
    });
    
    /**
     * When select file
     */
    $('#imagepath_banner').live('change', function(){
    	myfile= $( this ).val();
 	    var ext = myfile.split('.').pop();
 	    if (ext == '') {
  	       return;
 	 	}
 	    if(ext.toUpperCase() != "CSV"){
 	       $.displayError('<?php admin_language_e('report_views_admin_transaction_report_UploadCsvErr'); ?>', null, function() {
  	    	  $('#container').css('visibility', '');
               });
 	        return;
 	    }

 	    // Call upload file
        $.ajaxFileUpload({
                id: 'imagepath_banner',
                url: '<?php echo base_url()?>admin/report/import_transaction',
                success: function(data) {
                    if (data.status) {
                        document.location = '<?php echo base_url()?>admin/report/transaction';
                    } else {
                    	$.displayError(data.message);
                    }
                }
        });
    });
    
     /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
      * Process when user click to delete icon.
    */
    $('.managetables-icon-delete').live('click', function () {
        var transaction_id = $(this).attr('data-id-tran');
        var customer_id = $(this).attr('data-customer-id');
        var tran_type = $(this).attr('data-tran-type');
        
        // Show confirm dialog
        $.confirm({
            message: '<?php admin_language_e('report_views_admin_transaction_report_DeletePaymentConfirm'); ?>',
            yes: function () {
                // add to blacklist
                deletePayment(transaction_id,customer_id, tran_type);
            }
        });

    });
    
     /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
    * Delete invoice
    */
    function deletePayment(transaction_id,customer_id,tran_type) {
        var submitUrl = '<?php echo base_url()?>invoices/admin/delete_payment?transaction_id=' + transaction_id + "&customer_id=" + customer_id + "&tran_type=" + tran_type;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    var message = '<?php admin_language_e('report_views_admin_transaction_report_DeletePaymentSucc'); ?>';
                    $.infor({
                        message: message,
                        ok: function(){
                            // Reload data grid
                           transactionReportings();
                        }
                    });
                   
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
    <?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
    /** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
});
</script>