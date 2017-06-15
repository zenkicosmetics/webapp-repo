<?php
$baseline_open_balance = APUtils::getLastDayOfPreviousMonth();
?>
<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_open_balance_report_Header'); ?>
        (<?php echo date(APConstants::DATEFORMAT_OUTPUT_PDF, strtotime($baseline_open_balance)) ?>)</h2>
</div>
<div class="ym-grid mailbox">
    <form id="openBalanceReportingSearchForm" action="#" method="post">
        <div class="ym-gl">
            <!--#1295 improve filter in open balance report-->
            <div class="ym-grid input-item ym-g100">
                <div class="ym-g20 ym-gl" style="width: 200px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_open_balance_report_LblSearch'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="openBalanceReportingSearchForm_enquiry" name="enquiry" 
                           placeholder="<?php admin_language_e('report_views_admin_open_balance_report_PlaceholderSearch'); ?>"
                           style="width: 250px" value="" class="input-txt"/>
                </div>
            </div>

            <!-- Filter By Status -->
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 200px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_open_balance_report_LblFilter'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <select id="openBalanceReportingSearchForm_status" name="filter_status" style="width: 250px"
                            class="input-txt">
                        <option value="0"><?php admin_language_e('report_views_admin_open_balance_report_OptAll'); ?></option>
                        <option value="1"><?php admin_language_e('report_views_admin_open_balance_report_OptActive'); ?></option>
                        <option value="2"><?php admin_language_e('report_views_admin_open_balance_report_OptAutoDeactivated'); ?></option>
                        <option value="3"><?php admin_language_e('report_views_admin_open_balance_report_OptManuDeactivated'); ?></option>
                        <option value="4"><?php admin_language_e('report_views_admin_open_balance_report_OptNeverActivated'); ?></option>
                        <option value="5"><?php admin_language_e('report_views_admin_open_balance_report_OptDeleted'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Filter By Balance -->
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 200px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_open_balance_report_LblFilterOpenBalance'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <select id="openBalanceReportingSearchForm_balance" name="filter_balance" style="width: 250px"
                            class="input-txt">
                        <option value="0"><?php admin_language_e('report_views_admin_open_balance_report_OptAll'); ?></option>
                        <option value="1"><?php admin_language_e('report_views_admin_open_balance_report_OptPositive'); ?></option>
                        <option value="2"><?php admin_language_e('report_views_admin_open_balance_report_OptZero'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Filter By Payment -->
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 200px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_open_balance_report_LblFilterPayment'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <select id="openBalanceReportingSearchForm_payment" name="filter_payment" style="width: 250px"
                            class="input-txt">
                        <option value="0"><?php admin_language_e('report_views_admin_open_balance_report_OptAll'); ?></option>
                        <option value="1"><?php admin_language_e('report_views_admin_open_balance_report_OptCreditCard'); ?></option>
                        <option value="2"><?php admin_language_e('report_views_admin_open_balance_report_OptInvoice'); ?></option>
                    </select>
                    <!--#1295 improve filter in open balance report--> 
                    <button style="margin-left: 20px" id="searchOpenBalanceButton" class="admin-button"><?php admin_language_e('report_views_admin_open_balance_report_LblSearch'); ?></button>
                    <button style="margin-left: 10px" id="openBalanceExportCSVButton" class="admin-button"><?php admin_language_e('report_views_admin_open_balance_report_BtnExportCsv'); ?></button>
                    <button style="margin-left: 10px" id="paymentAllOpenBalanceButton" class="admin-button"><?php admin_language_e('report_views_admin_open_balance_report_BtnPaymentAll'); ?></button>
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
<div class="hide">
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site"
          method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value=""/>
    </form>
</div>
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('report_views_admin_open_balance_report_DisPdfInv'); ?></a>
    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form">
    </div>

    <div id="dialogPaymentProcess" title="<?php admin_language_e('report_views_admin_open_balance_report_TitPayment'); ?>">
        <div id="progressbarLabel" class="progress-label"><?php admin_language_e('report_views_admin_open_balance_report_LblStarttingPayment'); ?></div>
        <div id="progressbarPaymentProcess"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('button').button();
        $('#display_pdf_invoice').fancybox({
            width: 900,
            height: 700,
            'onClosed': function () {
                $("#fancybox-inner").empty();
            }
        });

        // Handler progress bar
        $("#progressbarPaymentProcess").progressbar({
            max: 100,
            value: 0,
            change: function () {
                $('#progressbarLabel').text("<?php admin_language_e('report_views_admin_open_balance_report_LblCurrentProgress'); ?>" + parseInt($("#progressbarPaymentProcess").progressbar("value")) + "%");
            },
            complete: function () {
                $('#progressbarLabel').text("<?php admin_language_e('report_views_admin_open_balance_report_LblCompleteProgress'); ?>!");
                dialog.dialog("option", "buttons", [{
                        text: "<?php admin_language_e('report_views_admin_open_balance_report_BtnCloseDlg'); ?>",
                        click: function () {
                            $('#dialogPaymentProcess').dialog('close');
                        }
                    }]);
                $(".ui-dialog button").last().focus();
            }
        });

        // Call search method
        searchOpenBalanceReport();

        /**
         * Process when user click to search button
         */
        $('#searchOpenBalanceButton').live('click', function (e) {
            searchOpenBalanceReport();
            e.preventDefault();
        });

        /**
         * Search data
         */
        function searchOpenBalanceReport() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/report/open_balance_report_search';
            var tableH = $.getTableHeight() + 15;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#openBalanceReportingSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                loadonce: false,
                width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space 
                rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>, 500, 1000],
                pager: "#dataGridPager",
                sortname: 'open_balance',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                rownumbers: true,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: ['', '',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColChargeFeeFlag'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColCusId'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColName'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColCompany'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColEmail'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColOpenBalance'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColOpenBalanceMonth'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColPaymentMethod'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColValid'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColStatus'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColLastPaymentAttempt'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColNumInactivateDays'); ?>',
                    '<?php admin_language_e('report_views_admin_open_balance_report_ColAction'); ?>'],
                colModel: [
                    {name: 'invoice_id', index: 'invoice_id', hidden: true},
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'charge_fee_flag', index: 'charge_fee_flag', hidden: true},
                    {name: 'customer_code', index: 'customer_code', width: 150, formatter: toCustomerFormater02},
                    {name: 'name', index: 'name', width: 200},
                    {name: 'company', index: 'company', width: 200, sortable: false},
                    {name: 'email', index: 'email', width: 280, formatter: toCustomerFormater},
                    {name: 'open_balance', index: 'open_balance', width: 200, sortable: false},
                    {name: 'open_balance_this_month', index: 'open_balance_this_month', width: 200, sortable: false},
                    {name: 'payment_method', index: 'payment_method', width: 100, sortable: false},
                    {name: 'credit_card_charge', index: 'credit_card_charge', width: 60, sortable: false},
                    {name: 'status', index: 'status', width: 140, sortable: false},
                    {name: 'last_payment_attempt', index: 'last_payment_attempt', width: 80, sortable: false},
                    {name: 'number_inactive_days', index: 'number_inactive_days', width: 80, sortable: false},
                    {name: 'action', index: 'action', width: 32, align: "center", sortable: false, formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                },
                onSelectAll: function (aRowids, status) {
                    if (status) {
                        // uncheck "protected" 
                        var cbs = $("tr.jqgrow > td > input.cbox:disabled", $("#dataGridResult"));
                        cbs.removeAttr("checked");
                    }
                },
                loadComplete: function () {
                    // For each row check if rows charge_fee_flag = 0 then disable checkbox
                    var rows = $("#dataGridResult").jqGrid('getDataIDs');
                    for (var i = 0; i < rows.length; i++) {
                        var rowData = $("#dataGridResult").jqGrid('getRowData', rows[i]);
                        var charge_fee_flag = rowData['charge_fee_flag'];
                        if (charge_fee_flag == '0') {
                            $('#jqg_dataGridResult_' + rows[i]).attr("disabled", true);
                        }
                    }
                    $.autoFitScreen(($(window).width() - 40)); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            if (typeof rowObject.cell === "undefined") {
                if ($.type(rowObject) == "object") {
                    return '<a class="view_customer_detail" data-id="' + rowObject.customer_id + '" style="text-decoration: underline;"  >' + rowObject.email + '</a>';
                } else {
                    return '<a class="view_customer_detail" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[6] + '</a>';
                }
            } else {
                return '<a class="view_customer_detail" data-id="' + rowObject.cell[1] + '" style="text-decoration: underline;"  >' + rowObject.cell[6] + '</a>';
            }
        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            if (typeof rowObject.cell === "undefined") {
                if ($.type(rowObject) == "object") {
                    return '<a class="access_customer_site" data-id="' + rowObject.customer_id + '" style="text-decoration: underline;"  >' + rowObject.email + '</a>';
                } else {
                    return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[3] + '</a>';
                }
            } else {
                return '<a class="access_customer_site" data-id="' + rowObject.cell[1] + '" style="text-decoration: underline;"  >' + rowObject.cell[3] + '</a>';
            }
        }

        function actionFormater(cellvalue, options, rowObject) {
            if (cellvalue !== -1 && rowObject[11] != 'Deleted') { //if not deleted yet
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="<?php admin_language_e('report_views_admin_open_balance_report_TitDeleteCustomer'); ?>"></span></span>';
            } else {
                return '';
            }
        }

        // Export open balance to CSV file
        $('#openBalanceExportCSVButton').live('click', function () {
            $('#openBalanceReportingSearchForm').attr('action', '<?php echo base_url() ?>admin/report/export_open_balance_csv');
            $('#openBalanceReportingSearchForm').submit();
        });

        // Make payment report for all open balance
        $('#paymentAllOpenBalanceButton').live('click', function () {
            var list_customer_id = [];
            var selRowIds = $("#dataGridResult").jqGrid('getGridParam', 'selarrrow');
            list_customer_id = selRowIds.join(',');
            console.log(list_customer_id);

            // Display confirm message
            var message = '';
            if (selRowIds.length == 0) {
                message = '<?php admin_language_e('report_views_admin_open_balance_report_PaymentAllConfirm'); ?>';
            } else {
                message = '<?php admin_language_e('report_views_admin_open_balance_report_PaymentConfirm1'); ?>'
                    + selRowIds.length +
                    '<?php admin_language_e('report_views_admin_open_balance_report_PaymentConfirm2'); ?>';
            }
            // Show confirm dialog
            $.confirm({
                message: message,
                yes: function () {
                    if (list_customer_id == '' || list_customer_id == null || list_customer_id == undefined) {
                        paymentAllCustomers(0, 0, 1, true);
                    } else {
                        paymentAllList(list_customer_id);
                    }
                }
            });
            return false;
        });

        // 20141202 Start fix: limit auto payment. 
        function paymentAllList(list_customer_id) {
            var submitUrl = '<?php echo base_url() ?>admin/report/payment_open_balance';
            $.pageBlock();
            $.ajaxExec({
                url: submitUrl,
                data: {
                    list_customer_id: list_customer_id
                },
                success: function (data) {
                    if (data.status) {
                        // uncheck customer paymented.
                        uncheckCustomer(list_customer_id);

                        // Gets remain customer id list 
                        var remainList = getListCustomerId(list_customer_id);

                        if (remainList.length > 0) {
                            // conitnue payment 
                            paymentAllList(remainList);
                        } else {
                            searchOpenBalanceReport();
                        }
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

        function paymentAllCustomers(start, limit, total, first_round) {
            var submitUrl = '<?php echo base_url() ?>admin/report/payment_open_balance';
            if (start >= total) {
                return;
            }
            var message_display = '<?php admin_language_e('report_views_admin_open_balance_report_ProcessingMsg'); ?>';
            if (start > 0) {
                message_display += '(' + start + '/' + total + ')';
            }

            if (first_round) {
                $("#progressbarPaymentProcess").progressbar("option", "value", 0);
                // $.pageBlock({message:message_display});
                $('#dialogPaymentProcess').openDialog({
                    height: 150,
                    width: 450,
                    modal: true,
                    buttons: {
                        '<?php admin_language_e('report_views_admin_open_balance_report_BtnCancelDlg'); ?>': function () {
                            $(this).dialog('close');
                        }
                    }
                });
                $('#dialogPaymentProcess').dialog('option', 'position', 'center');
                $('#dialogPaymentProcess').dialog('open');

            } else {
                var progressVal = (start / total) * 100;
                // Update progress status
                $("#progressbarPaymentProcess").progressbar("option", "value", progressVal);
            }

            $.ajaxExec({
                url: submitUrl,
                data: {
                    list_customer_id: '',
                    start: start,
                    limit: limit,
                    total: total
                },
                showDialog: false,
                success: function (data) {
                    if (data.status) {
                        console.log("data==============", data);
                        if (data.data.start < data.data.total) {
                            paymentAllCustomers(data.data.start, data.data.limit, data.data.total, false);
                        } else {
                            searchOpenBalanceReport();
                            //$.pageUnblock();
                            $('#dialogPaymentProcess').dialog('close');
                        }
                    } else {
                        $.displayError(data.message);
                        //$.pageUnblock();
                        $('#dialogPaymentProcess').dialog('close');
                    }
                }
            });
        }

        function getListCustomerId(list_customer_id) {
            if (list_customer_id.length > 0) {
                var res = list_customer_id.split(",");

                if (res.length > <?php echo APConstants::PAYMENT_CUSTOMER_LIMIT ?>) {
                    // remove 10 first elements
                    res.splice(0, <?php echo APConstants::PAYMENT_CUSTOMER_LIMIT ?>);

                    return res.join(',');
                }
            }

            return "";
        }

        function uncheckCustomer(list_customer_id) {
            // Gets 10 frist element.
            var res = list_customer_id.split(",");
            if (res.length > <?php echo APConstants::PAYMENT_CUSTOMER_LIMIT ?>) {
                res.splice(<?php echo APConstants::PAYMENT_CUSTOMER_LIMIT ?>, res.length - <?php echo APConstants::PAYMENT_CUSTOMER_LIMIT ?>);
            }

            for (var i = 0; i < res.length; i++) {
                $("#jqg_dataGridResult_" + res[i]).attr("checked", false);
            }
        }

        // 20141202 End fix: limit auto payment.

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var customer_id = $(this).attr('data-id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('report_views_admin_open_balance_report_DeleteCustomerConfirm'); ?>',
                yes: function () {
                    // Show confirm dialog
                    $.confirm({
                        message: '<?php admin_language_e('report_views_admin_open_balance_report_TryCharginConfirm'); ?>',
                        yes: function () {
                            // Show confirm dialog
                            $.confirm({
                                message: '<?php admin_language_e('report_views_admin_open_balance_report_BlacklistConfirm'); ?>',
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
                                message: '<?php admin_language_e('report_views_admin_open_balance_report_BlacklistConfirm'); ?>',
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
            var submitUrl = '<?php echo base_url() ?>customers/admin/delete?id=' + customer_id + "&add_blacklist_flag=" + add_blacklist_flag + "&charge=" + direct_charge;
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    if (data.status) {
                        if (direct_charge == '1') {
                            var message = '<?php admin_language_e('report_views_admin_open_balance_report_DeleteFailedMsg'); ?>';
                            if (data.data.charge_success_flag == 1) {
                                message = "<?php admin_language_e('report_views_admin_open_balance_report_DeletedMsg'); ?>";
                            }
                            $.infor({
                                message: message,
                                ok: function () {
                                    // Reload data grid
                                    searchOpenBalanceReport();
                                }
                            });
                        } else {
                            // Reload data grid
                            searchOpenBalanceReport();
                        }
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }

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