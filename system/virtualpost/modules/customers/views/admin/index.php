<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('customers_view_admin_index_CustomerManagement'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerSearchForm" action="<?php echo base_url() ?>scans/incoming/add" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 120px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('customers_view_admin_index_Location'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
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
                    } ?>

                    <input type="checkbox" id="hideDeletedCustomer" name="hideDeletedCustomer" value="1"
                           checked="checked"><span
                        style="font-size: 15px; margin-left: 3px;"><?php admin_language_e('customers_view_admin_index_HideDeletedCustomer'); ?></span>
                </div>
            </div>
        </div>
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 120px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('customers_view_admin_index_AccountType'); ?>:</label>
                </div>
                <div class="ym-g80 ym-gl">
                    <?php echo code_master_form_dropdown(array(
                                     "code" => APConstants::CUSTOMER_TYPE,
                                     "value"=> '', 
                                     "name" => 'account_type',
                                     "id"    => 'account_type',
                                     "clazz" => 'input-width',
                                     "style" => '',
                                     "has_empty" => true
                                 ));?>
                </div>
            </div>
        </div>
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 120px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('customers_view_admin_index_SearchText'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="searchCustomerForm_enquiry" name="enquiry" style="width: 248px" value="" class="input-txt" maxlength=255/>
                    <button id="searchCustomerButton" class="admin-button"><?php admin_language_e('customers_view_admin_index_Search'); ?></button>
                    <button id="addCustomerButton" class="admin-button"><?php admin_language_e('customers_view_admin_index_Add'); ?></button>
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
    <div id="addCustomer" title="<?php admin_language_e('customers_view_admin_index_AddCustomer'); ?>" class="input-form dialog-form">
    </div>
    <div id="editCustomer" title="<?php admin_language_e('customers_view_admin_index_EditCustomer'); ?>" class="input-form dialog-form">
    </div>
    <form id="hiddenExportCustomerForm" action="<?php echo base_url() ?>admin/customers/export" method="post">
        <input type="hidden" name="export" value="1"/>
    </form>
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value=""/>
    </form>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="viewDetailCustomer" class="input-form dialog-form">
    </div>
    <div id="createDirectCharge" class="input-form dialog-form">
    </div>
    <div id="recordExternalPayment" class="input-form dialog-form">
    </div>
    <div id="recordRefundPayment" class="input-form dialog-form">
    </div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form">
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //$("#location_id, #searchCustomerForm_enquiry, .admin-button").prop('readonly', true).button("disable");

        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();

        //searchCustomers();
        var tableH = $.getTableHeight() + 28;
        $("#dataGridResult").jqGrid({
        	width: ($( window ).width()- 40),
			height: tableH, //#1297 check all tables in the system to minimize wasted space
        	datatype: '{"page":"1","total":0,"records":0}',
        	colNames: [
                '<?php admin_language_e('customers_view_admin_index_CustomerID'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Name'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Company'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Email'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Invoicing'); ?>',
                '<?php admin_language_e('customers_view_admin_index_InvoicingCompany'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Shipment'); ?>',
                '<?php admin_language_e('customers_view_admin_index_ShipmentCompany'); ?>',
                '<?php admin_language_e('customers_view_admin_index_AType'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Status'); ?>',
                '<?php admin_language_e('customers_view_admin_index_ChargeFee'); ?>',
                '<?php admin_language_e('customers_view_admin_index_EmailConfirmation'); ?>',
                '<?php admin_language_e('customers_view_admin_index_PaymentInformation'); ?>',
                '<?php admin_language_e('customers_view_admin_index_City'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Country'); ?>',
                '<?php admin_language_e('customers_view_admin_index_NumberReceivedItem'); ?>',
                '<?php admin_language_e('customers_view_admin_index_CreatedDate'); ?>',
                '<?php admin_language_e('customers_view_admin_index_VerficationAddress'); ?>',
                '<?php admin_language_e('customers_view_admin_index_NumberInactivateDays'); ?>',
                '<?php admin_language_e('customers_view_admin_index_Action'); ?>'
            ],
        	colModel: [
                       {name: 'customer_code', index: 'customer_code', width: 90},

                       {name: 'user_name', index: 'user_name', width: 130, sortable: false},
                       {name: 'company', index: 'company', width: 90, sortable: false},
                       {name: 'email', index: 'email', width: 130},
                       {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 100},
                       {name: 'invoicing_company', index: 'invoicing_company', width: 100},
                       {name: 'shipment_address_name', index: 'shipment_address_name', width: 100},
                       {name: 'shipment_company', index: 'shipment_company', width: 100},
                       {name: 'account_type', index: 'account_type', width: 50},
                       {name: 'status_flag', index: 'status_flag', width: 80, sortable: false},
                       {name: 'charge_flag', index: 'charge_flag', width: 80},
                       {name: 'email_confirm_flag', index: 'email_confirm_flag', sortable: false, width: 50},
                       {name: 'payment_detail_flag', index: 'payment_detail_flag', sortable: false, width: 50},
                       {name: 'city', index: 'city', sortable: false, width: 76},
                       {name: 'country', index: 'country', sortable: false, width: 80},
                       {name: 'number_received_items', index: 'number_received_items', sortable: false, width: 55},
                       {name: 'created_date', index: 'created_date', width: 85},
                       {
                           name: 'required_verification_flag',
                           index: 'required_verification_flag',
                           width: 100
                       },
                       {name: 'number_inactivate_days', index: 'number_inactivate_days', width: 100, sortable: false},
                       {
                           name: 'customer_id',
                           index: 'customer_id',
                           width: 120
                       }
                   ]
         });

        /**
         * Search data
         */
        function searchCustomers() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/customers';
            var tableH = $.getTableHeight() + 5;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#customerSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php  echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                sortname: 'created_date',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: [
                    '',
                    '<?php admin_language_e('customers_view_admin_index_ParentCustomerID'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_CustomerID'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Name'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Company'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Email'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Invoicing'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_InvoicingCompany'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Shipment'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_ShipmentCompany'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_AType'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Status'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_ChargeFee'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_EmailConfirmation'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_PaymentInformation'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_City'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Country'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_NumberReceivedItem'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_CreatedDate'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_VerficationAddress'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_NumberInactivateDays'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_DeletedBy'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_EnterpriseCustomer'); ?>',
                    '<?php admin_language_e('customers_view_admin_index_Action'); ?>'
                ],
                colModel: [
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'parent_customer_id', index: 'parent_customer_id', hidden: true},
                    {name: 'customer_code', index: 'customer_code', width: 90, formatter: toCustomerFormater02},
                    {name: 'user_name', index: 'user_name', width: 130, sortable: false},
                    {name: 'company', index: 'company', width: 90, sortable: false},
                    {name: 'email', index: 'email', width: 130, formatter: toCustomerFormater},
                    {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 100},
                    {name: 'invoicing_company', index: 'invoicing_company', width: 100},
                    {name: 'shipment_address_name', index: 'shipment_address_name', width: 100},
                    {name: 'shipment_company', index: 'shipment_company', width: 100},
                    {name: 'account_type', index: 'account_type', width: 50},
                    {name: 'status_flag', index: 'status_flag', width: 80, sortable: false},
                    {name: 'charge_flag', index: 'charge_flag', width: 80},
                    {name: 'email_confirm_flag', index: 'email_confirm_flag', sortable: false, width: 50},
                    {name: 'payment_detail_flag', index: 'payment_detail_flag', sortable: false, width: 50},
                    {name: 'city', index: 'city', sortable: false, width: 76},
                    {name: 'country', index: 'country', sortable: false, width: 80},
                    {name: 'number_received_items', index: 'number_received_items', sortable: false, width: 55},
                    {name: 'created_date', index: 'created_date', width: 85},
                    {name: 'required_verification_flag',index: 'required_verification_flag',width: 100,sortable: false},
                    {name: 'number_inactivate_days', index: 'number_inactivate_days', width: 100, sortable: false},
                    {name: 'deleted_by', index: 'deleted_by', width: 80, sortable: true},
                    {name: 'enterprise_customer', index: 'deleted_by', width: 120, sortable: false},
                    {name: 'customer_id',ndex: 'customer_id',width: 120,sortable: false,align: "center",formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    $("#location_id, #searchCustomerForm_enquiry, .admin-button, #account_type").prop('readonly', false).button("enable");
                    $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            if(rowObject[1] != '' && rowObject[1] != null){
                return '';
            }

            if (cellvalue !== -1) { //if not deleted yet
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="<?php admin_language_e('customers_view_admin_index_Edit'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="<?php admin_language_e('customers_view_admin_index_Delete'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-change-pass" data-id="' + cellvalue + '" title="<?php admin_language_e('customers_view_admin_index_ChangePassword'); ?>"></span></span>';
            } else {
                return '';
            }
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="view_customer_detail" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[5] + '</a>';
        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            return '<a class="access_customer_site" data-id="' + rowObject[0] + '" style="text-decoration: underline;"  >' + rowObject[2] + '</a>';
        }

        /**
         * Process when user click to search button
         */
        $('#searchCustomerButton').click(function (e) {
            $("#location_id, #searchCustomerForm_enquiry, .admin-button, #account_type").prop('readonly', true).button("disable");
            searchCustomers();
            e.preventDefault();
        });

        /**
         * Process when user click to add group button
         */
        $('#addCustomerButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#addCustomer').openDialog({
                autoOpen: false,
                height: 470,
                width: 550,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>customers/admin/add", function () {
                        $('#addEditCustomerForm_email').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        saveCustomer();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#addCustomer').dialog('option', 'position', 'center');
            $('#addCustomer').dialog('open');
            return false;
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function () {
            var customer_id = $(this).attr('data-id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#editCustomer').openDialog({
                autoOpen: false,
                height: 500,
                width: 920,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>customers/admin/edit?id=" + customer_id, function () {
                        $('#addEditCustomerForm_email').focus();
                    });
                },
                buttons: {
                    'Save': function () {
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
         * Process when user click to edit icon.
         */
        $('.managetables-icon-change-pass').live('click', function () {
            var customer_id = $(this).attr('data-id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#editCustomer').openDialog({
                autoOpen: false,
                height: 300,
                width: 450,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>customers/admin/change_pass?id=" + customer_id, function () {
                    });
                },
                buttons: {
                    'Save': function () {
                        resetPasswordCustomer();
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
        $('.managetables-icon-delete').live('click', function () {
            var customer_id = $(this).attr('data-id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('customers_view_admin_index_DeleteCustomerConfirmationMessage'); ?>',
                yes: function () {
                    // Show confirm dialog
                    $.confirm({
                        message: '<?php admin_language_e('customers_view_admin_index_ChargOpenBalanceConfirmationMessage'); ?>',
                        yes: function () {
                            // Show confirm dialog
                            $.confirm({
                                message: '<?php admin_language_e('customers_view_admin_index_AddBalckListConfirmationMessage'); ?>',
                                yes: function () {
                                    // add to blacklist
                                    deleteCustomer(customer_id, '1', '1');
                                },
                                no: function () {
                                    // does not add to blacklist.
                                    deleteCustomer(customer_id, '0', '1');
                                }
                            });
                        },
                        no: function () {
                            // Show confirm dialog
                            $.confirm({
                                message: '<?php admin_language_e('customers_view_admin_index_AddBalckListConfirmationMessage'); ?>',
                                yes: function () {
                                    // add to blacklist
                                    deleteCustomer(customer_id, '1', '0');
                                },
                                no: function () {
                                    // does not add to blacklist.
                                    deleteCustomer(customer_id, '0', '0');
                                }
                            });
                        }
                    });
                }
            });
        });

        /**
         * Delete customer
         */
        function deleteCustomer(customer_id, add_blacklist_flag, direct_charge) {
            var submitUrl = '<?php echo base_url()?>customers/admin/delete?id=' + customer_id + "&add_blacklist_flag=" + add_blacklist_flag+"&charge="+direct_charge;
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    if (data.status) {
                        if(direct_charge == '1'){
                            var message = '<?php admin_language_e('customers_view_admin_index_DeleteSuccessPaymentFailMessage'); ?>';
                            if(data.data.charge_success_flag == 1){
                                message = "<?php admin_language_e('customers_view_admin_index_DeleteSuccessPaymentSuccessMessage'); ?>";
                            }
                            $.infor({
                                message: message,
                                ok: function(){
                                    // Reload data grid
                                    searchCustomers();
                                }
                            });
                        }else{
                            // Reload data grid
                            searchCustomers();
                        }
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Save Customer
         */
        function saveCustomer() {
            var submitUrl = $('#addEditCustomerForm').attr('action');
            var action_type = $('#h_action_type').val();
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditCustomerForm',
                success: function (data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#addCustomer').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#editCustomer').dialog('close');
                        }
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            searchCustomers();
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
                success: function (data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#addCustomer').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#editCustomer').dialog('close');
                        }
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            searchCustomers();
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
        $('#generateInvoiceButton').live('click', function () {
            var customer_id = $(this).attr('data-id');
            var submitUrl = '<?php echo base_url()?>customers/admin/generate_invoice_code?id=' + customer_id;
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    if (data.status) {
                        // Reload data grid
                        $('#addEditCustomerForm_invoice_code').val(data.data.invoice_code);
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
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

        /**
         * change location.
         */
        $("#location_id, #account_type").live("change", function () {
            searchCustomers();
        });
    });
</script>