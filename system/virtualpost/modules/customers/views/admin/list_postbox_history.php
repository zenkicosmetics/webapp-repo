<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('customer_view_admin_listpostboxhistory_ManagePostboxHistory'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="postboxHistorySearchForm" action="" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 150px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('customer_view_admin_listpostboxhistory_SearchText'); ?><span style="color:red;">*</span></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="searchCustomerForm_customer" name="customer" style="width: 248px" value="" class="input-txt" maxlength=255/>
                    <button id="searchPostboxHistoryButton" class="admin-button"><?php admin_language_e('customer_view_admin_listpostboxhistory_Search'); ?></button>
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
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value=""/>
    </form>
    <div id="viewDetailCustomer" title="<?php admin_language_e('customer_view_admin_listpostboxhistory_ViewCustomerDetails'); ?>" class="input-form dialog-form">
    </div>
</div>
<style>
    .input-error {
        border: 1px #800 solid !important;
        color: #800;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        //$("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', true).button("disable");

        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();
        var tableH = $.getTableHeight() - 30;
        //searchCustomers();
        $("#dataGridResult").jqGrid({
            width: ($(window).width() - 40),
            height: tableH, //#1297 check all tables in the system to minimize wasted space,
            datatype: '{"page":"1","total":0,"records":0}',
            colNames: [
                '',
                '',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_CustomerId'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_PostboxId'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_Email'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_Name'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_Company'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_Activity'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_Date'); ?>',
                '<?php admin_language_e('customer_view_admin_listpostboxhistory_AfterType'); ?>'],
            colModel: [
                {name: 'customer_id', index: 'customer_id', hidden: true},
                {name: 'postbox_id', index: 'postbox_id', hidden: true},
                {name: 'customer_code', index: 'customer_code', width: 90},
                {name: 'postbox_code', index: 'postbox_code', width: 90},
                {name: 'email', index: 'email', width: 130},
                {name: 'name', index: 'name', width: 90, sortable: false},
                {name: 'company', index: 'company', width: 90, sortable: false},
                {name: 'action_type', index: 'action_type', width: 90, sortable: false},
                {name: 'action_date', index: 'action_date', width: 50},
                {name: 'type', index: 'type', width: 80, sortable: false}
            ]
        });

        /**
         * Search data
         */
        function searchPostboxHistory() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>customers/admin/postboxhistorylist';
            var tableH = $.getTableHeight() + 35;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#postboxHistorySearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space,
                rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'postbox_code,postbox_name,name,company,action_type,action_date,type',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: [
                    '',
                    '',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_CustomerId'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_PostboxId'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_Email'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_Name'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_Company'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_Activity'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_Date'); ?>',
                    '<?php admin_language_e('customer_view_admin_listpostboxhistory_AfterType'); ?>'
                ],
                colModel: [
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'postbox_id', index: 'postbox_id', hidden: true},
                    {name: 'customer_code', index: 'customer_code', width: 200, sortable: false, formatter: toCustomerFormater},
                    {name: 'postbox_code', index: 'postbox_code', width: 250},
                    {name: 'email', index: 'email', width: 300, sortable: false},
                    {name: 'name', index: 'name', width: 250},
                    {name: 'company', index: 'company', width: 250},
                    {name: 'action_type', index: 'action_type', width: 160},
                    {name: 'action_date', index: 'action_date', width: 150, align: "center"},
                    {name: 'type', index: 'type', width: 100, align: "center"}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    //  $("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', false).button("enable");
                    $.autoFitScreen(($(window).width() - 40));
                }
            });
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="access_customer_site" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[2] + '</a>';
        }


        /**
         * Process when user click to search button
         */

        $('#searchPostboxHistoryButton').click(function () {

            // Check input customer
            var input_val = $('#searchCustomerForm_customer').val();

            if (input_val.length != 0) {
                $('#searchCustomerForm_customer').removeClass('input-error');
                searchPostboxHistory();
//                e.preventDefault();
            } else {
                $('#searchCustomerForm_customer').addClass('input-error');
                var submitUrl = '<?php echo base_url() ?>customers/admin/postboxhistorylist';
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'postboxHistorySearchForm',
                    success: function (data) {
                        if (data.status) {
                            searchPostboxHistory();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
            return false;
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
    });
</script>