<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_accounting_report_Title') ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="accountReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_accounting_report_LblFromDate'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="accountReportingSearchForm_fromDate" name="fromDate" style="width: 150px" value=""
                           class="input-txt datepicker" maxlength=12 />
                </div>
                <div class="ym-g20 ym-gl" style="width: 150px; margin-left: 20px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_accounting_report_LblVat'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="checkbox" id="accountReportingSearchForm_withVAT" name="withVAT" value="1" />
                </div>
            </div>
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_accounting_report_LblToDate'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="accountReportingSearchForm_fromTo" name="toDate" style="width: 150px" value="" class="input-txt datepicker"
                           maxlength=12 />
                </div>
                <div class="ym-g20 ym-gl" style="width: 150px; margin-left: 20px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_accounting_report_LblRevCharge'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="checkbox" id="accountReportingSearchForm_reverse_charge" name="reverse_charge" value="1" />
                </div>
            </div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_accounting_report_LblLocation'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $locations,
                        "value_key" => 'id',
                        "label_key" => 'location_name',
                        "value" => APContext::getLocationUserSetting(),
                        "name" => 'location_available_id',
                        "id" => 'location_available_id',
                        "clazz" => 'input-txt',
                        "style" => 'width:150px',
                        "has_empty" => true
                    ));
                    ?>
                    <button style="margin-left: 30px" id="accountReportingButton" class="admin-button">
                        <?php admin_language_e('report_views_admin_accounting_report_BtnSearch'); ?>
                    </button>
                    <button style="margin-left: 10px" id="accountExportCSVButton" class="admin-button">
                        <?php admin_language_e('report_views_admin_accounting_report_BtnExprtCsv'); ?>
                    </button>
                    <button style="margin-left: 10px" id="accountImportCSVButton" type="button" class="admin-button">
                        <?php admin_language_e('report_views_admin_accounting_report_BtnImportCsv'); ?>
                    </button>
                </div>
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
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_accounting_report_DispPdfInv'); ?></a>
    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form">
    </div>

    <input type="file" name="userfile" id="userfile" accept=".csv" />

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('button').button();
        $(".datepicker").datepicker();
        $(".datepicker").datepicker("option", "dateFormat", 'dd.mm.yy');

        // Call search method
        accountReportings();

        /**
         * Process when user click to search button
         */
        $('#accountReportingButton').live('click', function (e) {
            accountReportings();
            e.preventDefault();
        });

        /**
         * Search data
         */
        function accountReportings() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/report/account_report_search';
            var tableH = $.getTableHeight() + 10;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#accountReportingSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($(window).width() - 40),
                height: tableH,
                rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'invoice_month',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                rownumbers: true,
                captions: '',
                colNames: ['',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColCusId'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColName'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColCompany'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColEmail'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColDate'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColInvNum'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColNetTotal'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColVat'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColMulVat'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColGrossTotal'); ?>',
                    '<?php admin_language_e('report_views_admin_accounting_report_ColRev'); ?>'],
                colModel: [
                    {name: 'invoice_id', index: 'invoice_id', hidden: true},
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'name', index: 'name', width: 250},
                    {name: 'company', index: 'company', width: 250, sortable: false},
                    {name: 'email', index: 'email', width: 300, sortable: false, formatter: toCustomerFormater},
                    {name: 'invoice_month', index: 'invoice_month', width: 150, align:"center", sortable: false},
                    {name: 'invoice_code', index: 'invoice_code', width: 200, sortable: false},
                    {name: 'net_total', index: 'net_total', width: 150, sortable: false},
                    {name: 'vat', index: 'vat', width: 150, sortable: false},
                    {name: 'multiple_vat', index: 'multiple_vat', width: 100, align:"center", sortable: false},
                    {name: 'gross_total', index: 'gross_total', width: 150, sortable: false},
                    {name: 'reverse_charge', index: 'reverse_charge', align:"center", sortable: false, width: 75}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                },
                loadComplete: function () {
                    $.autoFitScreen(($(window).width() - 40));
                }
            });
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="view_customer_detail" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[4] + '</a>';
        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[3] + '</a>';
        }

        $('#accountExportCSVButton').live('click', function () {
            $('#accountReportingSearchForm').attr('action', '<?php echo base_url() ?>admin/report/export_accounting_csv');
            $('#accountReportingSearchForm').submit();
        });

        /** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
<?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
        /** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */

        $("#accountImportCSVButton").click(function () {
            $("#userfile").click();
        });

        $('#userfile').change(function (click) {
            // Upload data here
            $.ajaxFileUpload({
                id: 'userfile',
                data: {
                    input_file_client_name: 'userfile'
                },
                url: '<?php echo base_url() ?>admin/report/import_csv',
                resetFileValue: true,
                success: function (obj) {
                    $.displayInfor("<?php admin_language_e('report_views_admin_accounting_report_ImpotOutput'); ?>");
                }
            });
        });

    });
</script>