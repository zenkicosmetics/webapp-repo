<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('customers_view_admin_blacklist_CustomerBlackListManagement'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerBlackListSearchForm" action="<?php echo base_url() ?>admin/customers/blacklist" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <input type="text" id="searchCustomerForm_enquiry" name="enquiry" style="width: 250px"
                       value="" class="input-txt" maxlength=255 />
                <button id="searchCustomerBlackListButton" class="admin-button"><?php admin_language_e('customers_view_admin_blacklist_Search'); ?></button>
                <button id="addCustomerBlackListButton" class="admin-button"><?php admin_language_e('customers_view_admin_blacklist_Add'); ?></button>
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
    <div id="addCustomerBlackList" title="<?php admin_language_e('customers_view_admin_blacklist_AddCustomerBlackList'); ?>" class="input-form dialog-form">
    </div>
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();

        // Call search method
        searchCustomersBackList();
        /**
         * Search data
         */
        function searchCustomersBackList() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/customers/blacklist';
            var tableH = $.getTableHeight();
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#customerBlackListSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($(window).width() - 30), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting(); //Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
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
                    '<?php admin_language_e('customers_view_admin_blacklist_Email'); ?>',
                    '',
                    '<?php admin_language_e('customers_view_admin_blacklist_CustomerID'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_DateAdded'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_DateRegister'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_DurationMonth'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_CreditNote'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_DeletedBy'); ?>',
                    '<?php admin_language_e('customers_view_admin_blacklist_Action'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'email', index: 'email', width: 495, sortable: true},
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'customer_code', index: 'customer_code', width: 250, formatter: toCustomerFormater02},
                    {name: 'created_date', index: 'created_date', width: 200, sortable: true, align: "center"},
                    {name: 'register_date', index: 'register_date', width: 200, sortable: true, align: "center"},
                    {name: 'duration', index: 'duration', width: 200, sortable: false, align: "center"},
                    {name: 'credit_note', index: 'credit_note', width: 200, sortable: false, align: "center"},
                    {name: 'deleted_by', index: 'deleted_by', width: 150, sortable: true, align: "center"},
                    {
                        name: 'email',
                        index: 'email',
                        width: 100,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater
                    }
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    $.autoFitScreen(($(window).width() - 40));  //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-email="' + rowObject[1] + '" title="<?php admin_language_e('customers_view_admin_blacklist_Delete'); ?>"></span>';

        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            if (cellvalue == null) {
                return '';
            }
            return '<a class="access_customer_site" data-id="' + rowObject[2] + '" style="text-decoration: underline;"  >' + rowObject[3] + '</a>';
        }

        /**
         * Process when user click to search button
         */
        $('#searchCustomerBlackListButton').click(function (e) {
            searchCustomersBackList();
            e.preventDefault();
        });

        /**
         * Access the customer site
         */
        $('.access_customer_site').live('click', function () {
            var customer_id = $(this).attr('data-id');
            $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
            $('#hiddenAccessCustomerSiteForm').submit();
        });

        /**
         * Process when user click to add group button
         */
        $('#addCustomerBlackListButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#addCustomerBlackList').openDialog({
                autoOpen: false,
                height: 200,
                width: 550,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>customers/admin/add_customer_blacklist", function () {
                        $('#addEditCustomerBlackListForm_email').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        saveCustomerBlackList();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#addCustomerBlackList').dialog('option', 'position', 'center');
            $('#addCustomerBlackList').dialog('open');
            return false;
        });

        /**
         * Save Customer black list
         */
        function saveCustomerBlackList() {
            var submitUrl = $('#addEditCustomerBlackListForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditCustomerBlackListForm',
                success: function (data) {
                    if (data.status) {
                        $('#addCustomerBlackList').dialog('close');
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            searchCustomersBackList();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var email = $(this).attr('data-email');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('customers_view_admin_blacklist_DeleteCustomerConfirmMessage'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>customers/admin/delete_customer_blacklist?email=' + encodeURIComponent(email);
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchCustomersBackList();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

    });
</script>