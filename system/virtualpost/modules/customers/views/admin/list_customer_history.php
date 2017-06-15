<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('customers_views_admin_listcustomerhistory_Header') ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerHistorySearchForm" action="" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 150px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('customers_views_admin_listcustomerhistory_SearchText') ?>:<span style="color:red;">*</span></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="searchCustomerForm_customer" name="customer" style="width: 248px" value="" class="input-txt" maxlength=255/>
                    <button id="searchCustomerHistoryButton" class="admin-button"><?php admin_language_e('customers_views_admin_listcustomerhistory_SearchBtn') ?></button>
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
    <div id="viewDetailCustomer" title="<?php admin_language_e('customers_views_admin_listcustomerhistory_ViewCustomerDetails') ?>" class="input-form dialog-form">
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
        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();

        //searchCustomers();
        $("#dataGridResult").jqGrid({
            width: ($( window ).width()- 40),
            height: ($(window).height()- 40), //#1297 check all tables in the system to minimize wasted space,
            datatype: '{"page":"1","total":0,"records":0}',
            colNames: ['',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_CusCodeCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_EmailCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_ActionTypeCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_OldDataCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_CurDataCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_CreatedByCol') ?>',
                '<?php admin_language_e('customers_views_admin_listcustomerhistory_CreatedDateCol') ?>'],
            colModel: [
                {name: 'customer_id', index: 'customer_id', width: 250, hidden:true},
                {name: 'customer_code', index: 'cus.customer_code', width: 120,formatter: toCustomerFormater},
                {name: 'email', index: 'cus.email', width: 250},
                {name: 'action_type', index: 'action_type', width: 250, sortable: false},
                {name: 'old_data', index: 'old_data', width: 250, sortable: false},
                {name: 'current_data', index: 'current_data', width: 250, sortable: false},
                {name: 'created_by', index: 'created_by_id', width: 250},
                {name: 'create_date', index: 'create_date'}
            ]
        });

        /**
         * Search data
         */
        function searchCustomerHistory() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>customers/admin/customerhistorylist';
            var tableH = $.getTableHeight() + 35;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#customerHistorySearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space,
                rowNum: '<?php  echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                sortname: 'cus.customer_code,cus.email,customer_history.created_date,id,created_by_id',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: ['',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_CusCodeCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_EmailCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_ActionTypeCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_OldDataCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_CurDataCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_CreatedByCol') ?>',
                    '<?php admin_language_e('customers_views_admin_listcustomerhistory_CreatedDateCol') ?>'],
                colModel: [
                    {name: 'customer_id', index: 'customer_id', width: 250, hidden:true},
                    {name: 'customer_code', index: 'cus.customer_code', width: 120,formatter: toCustomerFormater},
                    {name: 'email', index: 'cus.email', width: 250},
                    {name: 'action_type', index: 'action_type', width: 250, sortable: false},
                    {name: 'old_data', index: 'old_data', width: 250, sortable: false},
                    {name: 'current_data', index: 'current_data', width: 250, sortable: false},
                    {name: 'created_by', index: 'created_by_id', width: 250},
                    {name: 'create_date', index: 'customer_history.create_date'}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    //  $("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', false).button("enable");
                    $.autoFitScreen(($( window ).width()- 40));
                }
            });
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="access_customer_site" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[1] + '</a>';
        }


        /**
         * Process when user click to search button
         */

        $('#searchCustomerHistoryButton').click(function() {

            // Check input customer
            var input_val = $('#searchCustomerForm_customer').val();

            if(input_val.length != 0){
                $('#searchCustomerForm_customer').removeClass('input-error');
                searchCustomerHistory();
//                e.preventDefault();
            }else{
                $('#searchCustomerForm_customer').addClass('input-error');
                var submitUrl = '<?php echo base_url() ?>customers/admin/customerhistorylist';
                $.ajaxSubmit({
                    url: submitUrl,
                    formId: 'customerHistorySearchForm',
                    success: function(data) {
                        if (data.status) {
                            searchCustomerHistory();
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