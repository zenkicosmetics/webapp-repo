<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_invoices_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="invoiceReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 150px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_invoices_LblSearch'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="invoiceReportingSearchForm_keyword" name="enquiry" placeholder="<?php admin_language_e('report_views_admin_invoices_TxbPlaceHolder'); ?>"
                           style="width: 350px" value="" class="input-txt" />
                </div>
            </div>
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 150px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_invoices_LblFrom'); ?></label>
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
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_invoices_LblTo'); ?></label>
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
                
                <button style="margin-left: 20px" id="searchInvoiceButton" class="admin-button"><?php admin_language_e('report_views_admin_invoices_BtnSearch'); ?></button>
                <button style="margin-left: 20px" id="exportInvoiceButton" class="admin-button"><?php admin_language_e('report_views_admin_invoices_BtnExport'); ?></button>
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
<div class="hide"></div>
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_invoices_DispPdfInv'); ?></a>
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
	
    // init screen
    var tableH = $.getTableHeight() + 30;
    $("#dataGridResult").jqGrid({
        mtype: 'POST',
        datatype: '{"page":"1","total":0,"records":0}',
        width: ($( window ).width()- 40),
        height: tableH, //#1297 check all tables in the system to minimize wasted space
        colNames:['', '',
            '<?php admin_language_e('report_views_admin_invoices_ColCusId'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColInvCode'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColName'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColCompany'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColCountryCode'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColVatId'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColCountry'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColEu'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColEmail'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColPb'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColCharge'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColValue'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColNetTotal'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColVatId'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColDate'); ?>' ,
            '<?php admin_language_e('report_views_admin_invoices_ColDdmm'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColRev'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColSh'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColGkonto'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColKonto'); ?>',
            '<?php admin_language_e('report_views_admin_invoices_ColAction'); ?>'],
        colModel:[
            {name:'invoice_id',index:'invoice_id', hidden: true},
            {name:'customer_id',index:'customer_id', hidden: true},
            {name:'customer_code',index:'customer_code', width: 120},
            {name:'invoice_code',index:'invoice_code', width: 120},
            {name:'name',index:'name', width: 120},
            {name:'company',index:'company', width: 150, sortable: false},
            {name:'country_code',index:'country_code', width: 120},
            {name:'vat_id',index:'vat_id', width: 50, sortable: false},
            {name:'country_name',index:'country_name', width: 100, sortable: false},
            {name:'eu_flag',index:'eu_flag', width: 50, sortable: false},
            {name:'email',index:'email', width: 180},
            {name:'total_postbox',index:'total_postbox', width: 50, sortable: false},
            {name:'charge',index:'charge', width: 50, sortable: false},
            {name:'value',index:'value', width: 50, sortable: false},
            {name:'net_total',index:'net_total', width: 60, sortable: false},
            {name:'vat',index:'vat', width: 40, sortable: false},
            {name:'invoice_date',index:'invoice_date', width: 80, sortable: false},
            {name:'invoice_daymonth',index:'invoice_daymonth', width: 100, sortable: false},
            {name:'reverse_charge',index:'reverse_charge', sortable: false, width: 40},
            {name:'type',index:'type', sortable: false, width: 40},
            {name:'gkonto',index:'gkonto', sortable: false, width: 80},
            {name:'konto',index:'konto', sortable: false, width: 80},
            {name:'invoice_code',index:'invoice_code', sortable: false, width: 75, align:"center"}
        ]
    });
    

    /**
    * Process when user click to search button
    */
    $('#searchInvoiceButton').live('click', function(e) {
         searchInvoicesReport();
         e.preventDefault();
    });

    /**
     * Process when user click to export button
     */
    $('#exportInvoiceButton').live('click', function(e) {
        var url = '<?php echo base_url() ?>admin/report/invoice_report_export';
        $('#invoiceReportingSearchForm').attr('action', url);
        $('#invoiceReportingSearchForm').submit();
        e.preventDefault();
    });

    /**
     * Search data
     */
    function searchInvoicesReport() {
        var from_year = $("#year").val();
        var from_month = $("#month").val();
        var to_year = $("#to_year").val();
        var to_month = $("#to_month").val();
        
        if( (from_month != '' && from_year == '') || (from_month == '' && from_year != '') ){
            $.displayError("Please select the from year/month from list.");
            return;
        }
        
        if( (to_month != '' && to_year == '') || (to_month == '' && to_year != '') ){
            $.displayError("Please select the to year/month from list.");
            return;
        }
        
        if( from_year != '' && to_year == ''){
            $.displayError("Please select the to year/month from list.");
            return;
        }
        
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>admin/report/invoice_report_search';
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#invoiceReportingSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH - 20, //#1297 check all tables in the system to minimize wasted space 
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'invoice_month',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit:false,
            rownumbers: true,
            captions: '',
            colNames:['', '',
                '<?php admin_language_e('report_views_admin_invoices_ColCusId'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColInvCode'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColName'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColCompany'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColCountryCode'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColVatId'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColCountry'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColEu'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColEmail'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColPb'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColCharge'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColValue'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColNetTotal'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColVatId'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColDate'); ?>' ,
                '<?php admin_language_e('report_views_admin_invoices_ColDdmm'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColRev'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColSh'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColGkonto'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColKonto'); ?>',
                '<?php admin_language_e('report_views_admin_invoices_ColAction'); ?>'],
            colModel:[
                {name:'invoice_id',index:'invoice_id', hidden: true},
                {name:'customer_id',index:'customer_id', hidden: true},
                {name:'customer_code',index:'customer_code', width: 120},
                {name:'invoice_code',index:'invoice_code', width: 120},
                {name:'name',index:'name', width: 150},
                {name:'company',index:'company', width: 150, sortable: false},
                {name:'country_code',index:'country_code', width: 120},
                {name:'vat_id',index:'vat_id', width: 50, sortable: false},
                {name:'country_name',index:'country_name', width: 100, sortable: false},
                {name:'eu_flag',index:'eu_flag', width: 50, sortable: false},
                {name:'email',index:'email', width: 180},
                {name:'total_postbox',index:'total_postbox', width: 50, sortable: false},
                {name:'charge',index:'charge', width: 50, sortable: false},
                {name:'value',index:'value', width: 50, sortable: false},
                {name:'net_total',index:'net_total', width: 60, sortable: false},
                {name:'vat',index:'vat', width: 40, sortable: false},
                {name:'invoice_date',index:'invoice_date', width: 80, sortable: false},
                {name:'invoice_daymonth',index:'invoice_daymonth', width: 60, sortable: false},
                {name:'reverse_charge',index:'reverse_charge', sortable: false, width: 40},
                {name:'type',index:'type', sortable: false, width: 40},
                {name:'gkonto',index:'gkonto', sortable: false, width: 80},
                {name:'konto',index:'konto', sortable: false, width: 80},
                {name:'invoice_code',index:'invoice_code', sortable: false, width: 75, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));  //#1297 check all tables in the system to minimize wasted space 
            }
        });
    }

    function actionFormater(cellvalue, options, rowObject) {
        var check_user = '<?php echo $check_super_admin ?>';
        var del_invoice_url = '';
        
       // #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
        if(check_user){
               del_invoice_url = '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-invoice-id="' + rowObject[0] + '" data-customer-id="'+ rowObject[1] + '" title="<?php admin_language_e('report_views_admin_invoices_TitleDelete'); ?>"></span></span>';
        }
        
        if (rowObject[19] == 'H') {
            
            url = encodeURIComponent('<?php echo APContext::getFullBasePath()?>admin/report/export_invoice/'+cellvalue+'?type=invoice&customer_id='+ rowObject[1]);
           
            return '<a class="pdf" target="_blank" href="<?php echo base_url()?>admin/report/view_pdf_invoice?url='+url+'" id="'+cellvalue+'" data-customer-id="' + rowObject[1] + '">&nbsp;</a>' + del_invoice_url;
        }else if(rowObject[19] == 'S'){
            url = encodeURIComponent('<?php echo APContext::getFullBasePath()?>admin/report/export_invoice/'+cellvalue+'?type=credit&customer_id='+ rowObject[1]);
            
            return '<a class="pdf" target="_blank" href="<?php echo base_url()?>admin/report/view_pdf_invoice?url='+url+'" id="'+cellvalue+'" data-customer-id="' + rowObject[1] + '">&nbsp;</a>' + del_invoice_url;
        }else {
            return '';
        }
    }

    /**
     * When user click pdf icon
     */
    $("a.pdf").live('click', function() {
        var invoices_href = this.href;
        $('#display_pdf_invoice').attr('href', invoices_href);
        $('#display_pdf_invoice').click();

        return false;
    });
    
    /**
    * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
      * Process when user click to delete icon.
    */
    $('.managetables-icon-delete').live('click', function () {
        var invoice_id = $(this).attr('data-invoice-id');
        var customer_id = $(this).attr('data-customer-id');
      
        // Show confirm dialog
        $.confirm({
            message: '<?php admin_language_e('report_views_admin_invoices_ConfirmMess'); ?>',
            yes: function () {
                // delete invoice
                deleteInvoice(invoice_id,customer_id);
            }
        });

    });
    
    /**
    * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
    * Delete invoice
    */
    function deleteInvoice(invoice_id,customer_id) {
        var submitUrl = '<?php echo base_url()?>invoices/admin/delete_invoice?invoice_id=' + invoice_id + "&customer_id=" + customer_id;
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                if (data.status) {
                    var message = '<?php admin_language_e('report_views_admin_invoices_DeletedMess'); ?>';
                    $.infor({
                        message: message,
                        ok: function(){
                            // Reload data grid
                           searchInvoicesReport();
                        }
                    });
                   
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
});
</script>