<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_monthly_report_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="monthlyReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 150px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_monthly_report_LblLocation'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <?php
                    echo my_form_dropdown(array (
                            "data" => $locations,
                            "value_key" => 'id',
                            "label_key" => 'location_name',
                            "value" => APContext::getLocationUserSetting(),
                            "name" => 'location_available_id',
                            "id" => 'location_available_id',
                            "clazz" => 'input-txt',
                            "style" => 'width: 250px',
                            "has_empty" => true 
                    ));
                    ?>
                    <button style="margin-left: 20px" id="searchMonthlyReportButton" class="admin-button"><?php admin_language_e('report_views_admin_monthly_report_BtnSearch'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide"></div>
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_monthly_report_DisPdfInv'); ?></a>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    $('#display_pdf_invoice').fancybox({
		width: 900,
		height: 700,
		'onClosed': function() {
	     $("#fancybox-inner").empty();
	    }
	});

    /**
	 * Process when user click to search button
	 */
	$('#searchMonthlyReportButton').live('click', function(e) {
		searchMonthyReport();
		e.preventDefault();
	});
	
	// Call search method
    searchMonthyReport();

	/**
	 * Search data
	 */
	function searchMonthyReport() {
		$("#dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>admin/report/monthy_report_search';
		var tableH = $.getTableHeight() + 5;
        $("#dataGridResult").jqGrid({
        	url: url,
        	postData: $('#monthlyReportingSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
        	width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height:tableH, //#1297 check all tables in the system to minimize wasted space 
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'invoice_month',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            rownumbers: true,
            captions: '',
            colNames:['',
                '<?php admin_language_e('report_views_admin_monthly_report_ColDate'); ?>',
                '<?php admin_language_e('report_views_admin_monthly_report_ColLocation'); ?>',
                '<?php admin_language_e('report_views_admin_monthly_report_ColGrossPrice'); ?>',
                '<?php admin_language_e('report_views_admin_monthly_report_ColCsv'); ?>',
                '<?php admin_language_e('report_views_admin_monthly_report_ColPdf'); ?>'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'invoice_month',index:'invoice_month', width: 350, align:"center"},
               {name:'location',index:'location', width: 460},
               {name:'gross_price',index:'gross_price', width: 600, align:"center"},
               {name:'csv',index:'csv', sortable: false, width: 200, align:"center", formatter: actionCsvFormater},
               {name:'pdf',index:'pdf', sortable: false, width: 200, align:"center", formatter: actionPdfFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));  //#1297 check all tables in the system to minimize wasted space 
            }
        });

        function actionCsvFormater(cellvalue, options, rowObject) {
    		return '<a class="csv" target="_blank" href="<?php echo base_url()?>admin/report/view_pdf_invoice?url=<?php echo APContext::getFullBasePath()?>admin/report/export_invoice/'+ cellvalue +'?customer_id='+rowObject[1]+'" id="'+cellvalue+'" data-customer-id="' + rowObject[1] + '">&nbsp;</a>';
    	}
    	
        function actionPdfFormater(cellvalue, options, rowObject) {
    		return '<a class="pdf" target="_blank" href="<?php echo base_url()?>admin/report/view_pdf_invoice?url=<?php echo APContext::getFullBasePath()?>admin/report/export_invoice/'+ cellvalue +'?customer_id='+rowObject[1]+'" id="'+cellvalue+'" data-customer-id="' + rowObject[1] + '">&nbsp;</a>';
    	}

        /**
    	 * When user click pdf icon
    	 */
    	$("a.pdf").live('click', function() {
        	var customer_id =  $(this).attr('data-customer-id');
    		var submitUrl = '<?php echo base_url()?>admin/report/check_payment_exist?customer_id=' + customer_id;
    		var invoices_href = this.href;
            $.ajaxExec({
                 url: submitUrl,
                 success: function(data) {
                     if (data.status) {
                     	if (data.message == '1') {
                     		$('#display_pdf_invoice').attr('href', invoices_href);
                            $('#display_pdf_invoice').click();
                     		
                     	} else {
                     		$.displayError('Payment account not added.');
                        }
                     } else {
                     	$.displayError(data.message);
                     }
                 }
             });
    	  
    	    return false;
    	});
	}
});
</script>