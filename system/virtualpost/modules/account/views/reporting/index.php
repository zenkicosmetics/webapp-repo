<div id="invoice-body-wrapper11">
    <div class="ym-grid">
        <div style="width: 980px;margin: 20px 0 0 40px;">
            <h2 style="border-bottom: 1px solid #dadada; color: #336699; font-size: 23px; padding-bottom: 10px;">Reporting for your account</h2>
            <div class="ym-clearfix" style="height:1px;"></div>

            <form action="" method="post" id="userSearchForm">
                <div class="ym-grid">

                    <h3>Month: 
                        <?php
                        echo my_form_dropdown(array(
                            "data" => $list_year,
                            "value_key" => 'id',
                            "label_key" => 'label',
                            "value" => $select_year,
                            "name" => 'year',
                            "id" => 'year',
                            "clazz" => 'input-width',
                            "style" => 'width: 70px',
                            "has_empty" => false
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
                            "clazz" => 'input-width',
                            "style" => 'width: 50px',
                            "has_empty" => false
                        ));
                        ?>
                        <button class="input-btn  btn-yellow" type="button" id="searchReportButton">Search</button>
                    </h3>
                </div>
                <div class="ym-clearfix"></div>
                <div class="ym-grid" style="padding: 5px 0px;">
                    <div class="ym-gl ym-g70"><label>Postbox account charges: 
                            <?php echo APUtils::convert_currency($total_postbox_fee, $currency->currency_rate, 2, $decimal_separator) . ' ' . $currency->currency_short; ?>
                        </label></div>
                    <div class="ym-gr ym-g30"  style="text-align: right"><label>Total charges: 
                            <?php echo APUtils::convert_currency($total_invoice, $currency->currency_rate, 2, $decimal_separator) . ' ' . $currency->currency_short; ?>
                        </label></div>
                </div>
                <div class="ym-clearfix"></div>

                <div class="ym-grid" style="padding: 5px 0px;">
                    <div class="ym-gl ym-g50"><label>Postbox activity charges: 
                            <?php echo APUtils::convert_currency($total_invoice - $total_postbox_fee, $currency->currency_rate, 2, $decimal_separator) . ' ' . $currency->currency_short; ?></label></div>
                    <div class="ym-gr ym-g50" style="text-align: right">
                        Deposit remaining: <label <?php if($open_balance['RemainChargeAmount'] < 0.01){?>style='color:green' <?php }?>>
                            <?php echo $open_balance['RemainChargeAmount'] < 0 ? APUtils::convert_currency( (-1) *$open_balance['RemainChargeAmount'], $currency->currency_rate, 2, $decimal_separator) . ' ' . $currency->currency_short : 0; ?>
                        </label></div>
                </div>
                <div class="ym-clearfix"></div>

<!--                <div class="ym-grid" style="padding: 5px 0px;">
                    <div class="ym-gl ym-g50"><label>Phone activity charges: 
                            <?php echo APUtils::convert_currency($phone_balance, $currency->currency_rate, 2, $decimal_separator) . ' ' . $currency->currency_short; ?>
                        </label></div>
                </div>
                <div class="ym-clearfix"></div>-->

                <div class="ym-grid" >
                    
                    <h3>
                        <div style="margin-top: 10px; float:left">Current Month Activities</div>
                        <div style="float:right"><input type="text" name="currentActivity" id="currentActivity"
                                                         placeholder="search name or email" value="" class="input-width" /></div>
                    </h3>
                    <div class="ym-clearfix" style="clear:both"></div>
                    <br />
                    <table id="dataGridResult" ></table>
                    <div id="dataGridPager"></div>
                    <div class="clear-height"></div>
                </div>

                <div class="ym-clearfix" style="height:35px;"></div>
                <div class="ym-grid" >
                    <div class="ym-gl ym-g25">
                        <h3><span >List of all user invoices</span></h3>
                    </div>
                    <div class="ym-gl ym-g50">
                        <span>
                            Use VAT <input type="text" class="input-width" style="width: 70px;" name="vat_rate" id="vat_rate"
                                           value="<?php echo AccountSetting::get($customer_id, APConstants::CUSTOMER_NEW_VAT_KEY) ?>"/> % as standard for new invoices
                            <button type="button" class="input-btn  btn-yellow" id="saveVATRate" style="margin-left: 10px;">Save VAT</button>
                        </span>
                    </div>
                    <div class="ym-gl ym-g25">
                        <input type="text" name="oldActivity" id="oldActivity" placeholder="search name or email" 
                               style="float: right" value="" class="input-width" />
                    </div>
                    <div class="clear-height"></div>
                    
                    <div class="ym-clearfix"  style="clear:both"></div>
                    <br />
                    <table id="dataGridResult2" ></table>
                    <div id="dataGridPager2"></div>
                    <div class="clear-height"></div>
                    <div class="ym-clearfix" style="height:80px"></div>
                </div>
            </form>
        </div>

    </div>
    <div class="hide" style="display: none;">
        <a id="display_pdf_invoice" class="iframe" href="#">Display PDF Invoice</a>
        <a id="display_paypal_invoice" class="iframe" href="#">Display PDF Invoice</a>
    </div>
</div>
<div class="hide" style="display: none;">

</div>
<script type="text/javascript">
    $(document).ready(function () {
        // Call search method
        searchCurrInvoice();
        searchListCustomerInvoices();

        /**
         * Search data
         */
        function searchCurrInvoice() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>account/reporting/load_current_invoice';
            $("#dataGridResult").jqGrid('GridUnload');
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#userSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height: 280,
                width: 975,
                rowNum: '10',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'phone_number',
                sortorder: 'asc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: false,
                multiselectWidth: 40,
                captions: '',
                colNames: ['ID', 'Name', 'Email', 'Activity',  'Date', 'Charge'],
                colModel: [
                    {name: 'ID', index: 'id', width: 50, sortable: false},
                    {name: 'name', index: 'name', width: 200, sortable: false},
                    {name: 'email', index: 'email', width: 200, sortable: false},
                    {name: 'activity', index: 'activity', width: 170, sortable: false},
                    {name: 'date', index: 'create_date', width: 100, sortable: false},
                    {name: 'charge', index: 'charge', width: 100, sortable: false}
                ],
                loadComplete: function () {
                    // $.autoFitScreen(DATAGRID_WIDTH);
                }
            });
        }

        /**
         * Search data
         */
        function searchListCustomerInvoices() {
            $("#dataGridResult2").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>account/reporting/load_list_customer_invoice';

            $("#dataGridResult2").jqGrid({
                url: url,
                postData: $('#userSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height: 190,
                width: 975,
                rowNum: '10',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager2",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: ['','','', 'ID', 'Name', 'Email', 'Date', 'Charge', 'PDF', 'Sent'],
                colModel: [
                    {name: 'invoice_type', index: 'invoice_type',hidden: true},
                    {name: 'customer_id', index: 'customer_id',hidden: true},
                    {name: 'invoice_summary_id', index: 'invoice_summary_id',hidden: true},
                    {name: 'id', index: 'id', width: 50, sortable: false},
                    {name: 'name', index: 'name', width: 200, sortable: false},
                    {name: 'email', index: 'email', width: 200, sortable: false},
                    {name: 'date', index: 'Date', width: 100, sortable: false},
                    {name: 'charge', index: 'charge', width: 100, sortable: false},
                    {name: 'pdf', index: 'pdf', width: 50, sortable: false, formatter: actionFormater},
                    {name: 'send', index: 'send', width: 50, sortable: false, align: "center", formatter: sendFormater}
                ]
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
            if (rowObject[0] == 'Invoice') {
                return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath() ?>invoices/export_user_report/' 
                        + cellvalue + '?type=invoice&customer_id='+rowObject[1]+'">&nbsp;</a>';
            } else if (rowObject[0] == 'Credit Note') {
                return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath() ?>invoices/export_user_report/' 
                        + cellvalue + '?type=credit&customer_id='+rowObject[1]+'">&nbsp;</a>';
            } else {
                return '';
            }
        }
        
        function sendFormater(cellvalue, options, rowObject) {
            if (cellvalue == "1") {
                return '<a class="send-invoice-blue send-button-pdf" target="_blank" data-id="' + rowObject[2] + '" data-customer-id="'+rowObject[1]+'">&nbsp;</a>';
            } else {
                return '<a class="send-invoice send-button-pdf" target="_blank" data-id="' + rowObject[2] + '" data-customer-id="'+rowObject[1]+'">&nbsp;</a>';
            }
        }

        $('#display_pdf_invoice').fancybox({
            width: 1000,
            height: 800
        });

        /**
         * When user click pdf icon
         */
        $("a.pdf, a.temp_pdf").live('click', function () {
            var invoices_href = this.href;

            $('#display_pdf_invoice').attr('href', invoices_href);
            $('#display_pdf_invoice').click();
            return false;
        });
        
        $("#currentActivity").change(function(){
            searchCurrInvoice();
        });
        
        $("#oldActivity").change(function(){
            searchListCustomerInvoices();
        });
        
        $("#searchReportButton").click(function(){
            searchCurrInvoice();
            searchListCustomerInvoices();
            return false;
        });
        
        $("a.send-button-pdf").live('click', function(){
            var invoice_summary_id = $(this).data('id');
            var customer_id = $(this).data('customer-id');
            
            $.confirm({
                message: 'Do you want to send this invoice to customer?',
                yes: function () {
                    $.ajaxExec({
                        url: '<?php echo base_url() ?>account/reporting/send_invoice_report',
                        data: {customer_id: customer_id, invoice_summary_id: invoice_summary_id},
                        success: function (data) {
                            if (data.status) {
                                $("#searchReportButton").click();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
            
            return false;
        });
        
        $("#saveVATRate").click(function(){
            var vat = $.trim($('#vat_rate').val());
            if(vat == ''){
                $.displayError("Please input the new VAT rate.");
                return;
            }
            
            $.ajaxExec({
                url: '<?php echo base_url() ?>account/setting/save_vat',
                data: {vat: vat},
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message);
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        });
    });
</script>