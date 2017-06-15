<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('customers_view_admin_postboxlist_PostboxListManagement'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerSearchForm" action="<?php echo base_url() ?>admin/customers/postboxlist" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <?php
                // check access for supper admin and instance admin.
                if (APContext::isAdminParner() || APContext::isAdminUser()) {
                    echo my_form_dropdown(array(
                        "data" => $list_access_location,
                        "value_key" => 'id',
                        "label_key" => 'location_name',
                        "value" => APContext::getLocationUserSetting(),
                        "name" => 'location_id',
                        "id" => 'location_id',
                        "clazz" => 'input-width',
                        "style" => '',
                        "has_empty" => true
                    ));
                } else {
                    echo my_form_dropdown(array(
                        "data" => $list_access_location,
                        "value_key" => 'id',
                        "label_key" => 'location_name',
                        "value" => APContext::getLocationUserSetting(),
                        "name" => 'location_id',
                        "id" => 'location_id',
                        "clazz" => 'input-width',
                        "style" => '',
                        "html_option" => '',
                        "has_empty" => false
                    ));
                }
                ?>
                <input style="margin-left: 6px;" type="checkbox" id="hideDeletedPostbox" name="hideDeletedPostbox" value="1"
                           checked="checked"><span
                        style="font-size: 15px; margin-left: 3px;"><?php admin_language_e('customers_view_admin_postboxlist_HideDeletedPostbox'); ?></span>
            </div>
        </div>
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <input type="text" id="searchCustomerForm_enquiry" name="enquiry" style="width: 250px" value="" class="input-txt" maxlength=255/>
                <button style="margin-left: 6px;" id="searchCustomerButton" class="admin-button"><?php admin_language_e('customers_view_admin_postboxlist_Search'); ?></button>
                <button id="exportButton" class="admin-button"><?php admin_language_e('customers_view_admin_postboxlist_ExportCSV'); ?></button>
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
    <div id="addCustomer" title="<?php admin_language_e('customers_view_admin_postboxlist_AddCustomer'); ?>" class="input-form dialog-form"></div>
    <div id="editCustomer" title="<?php admin_language_e('customers_view_admin_postboxlist_EditCustomer'); ?>" class="input-form dialog-form"></div>
    <form id="hiddenExportCustomerForm" action="<?php echo base_url() ?>admin/customers/export" method="post">
        <input type="hidden" name="export" value="1"/>
    </form>
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value=""/>
    </form>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', true);

        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();

        // Call search method
        searchCustomers();
        /**
         * Search data
         */
        function searchCustomers() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/customers/postboxlist';
            var tableH = $.getTableHeight();
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#customerSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting();//Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE);?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                sortname: '',
                sortorder: '',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: [
                    '',
                    '<?php admin_language_e('customers_view_admin_postboxlist_CustomerID'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_PostboxID'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_PostboxName'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_PostboxCompany'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_PostboxType'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_CreatedDate'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_CustomerStatus'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_PostboxStatus'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_Email'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_NumberReceivedItem'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_InvoiceName'); ?>',
                    '<?php admin_language_e('customers_view_admin_postboxlist_InvoiceCompany'); ?>'
                ],
                colModel: [
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {
                        name: 'customer_code',
                        index: 'customer_code',
                        width: 90,
                        formatter: toCustomerFormater02,
                        sortable: false
                    },
                    {name: 'postbox_code', index: 'postbox_code', width: 160, sortable: false},
                    {name: 'postbox_name', index: 'postbox_name', width: 180, sortable: true},
                    {name: 'postbox_company', index: 'postbox_company', width: 180, sortable: true},
                    {name: 'postbox_type', index: 'postbox_type', width: 90, sortable: false},
                    {name: 'created_date', index: 'created_date', width: 90, sortable: false},
                    {name: 'customer_status', index: 'customer_status', width: 130, sortable: false},
                    {name: 'postbox_status', index: 'postbox_status', width: 130, sortable: false},
                    {name: 'email', index: 'email', width: 190, sortable: false, formatter: toCustomerFormater},
                    {name: 'number_received_items', index: 'number_received_items', sortable: false, width: 155},
                    {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 180, sortable: false},
                    {name: 'invoicing_company', index: 'invoicing_company', width: 180, sortable: false}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    $("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', false).button("enable");
                    $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return "";
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="view_customer_detail" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[9] + '</a>';
        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            return '<a class="access_customer_site" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[1] + '</a>';
        }

        /**
         * Process when user click to search button
         */
        $('#searchCustomerButton').click(function (e) {
            $("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', true).button("disable");
            searchCustomers();
            e.preventDefault();
        });

        $('#exportButton').live('click', function () {
            $('#customerSearchForm').attr('action', '<?php echo base_url() ?>admin/customers/export_postbox_csv');
            $('#customerSearchForm').submit();
        });

        /** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
        <?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
        /** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */

        /**
         * Access the customer site
         */
        $('.access_customer_site').live('click', function () {
            var customer_id = $(this).attr('data-id');
            $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
            $('#hiddenAccessCustomerSiteForm').submit();
        });

        /**
         * change location.
         */
        $("#location_id").live("change", function () {
            searchCustomers();
        });
    });
</script>