<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('scan_view_completed_index_CompletedList'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="completedListSearchForm" action="#" method="post">
        <div class="ym-g50 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_view_completed_index_SearchText'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <div class="ym-grid input-item">
                        <input type="text" id="completedListForm_enquiry" name="enquiry" style="width: 350px"
                               value="" class="input-txt" maxlength=255  placeholder="<?php admin_language_e('scan_view_completed_index_SearchPlaceHolderText'); ?>"  />
                    </div>
                </div>
            </div>

            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"><label><?php admin_language_e('scan_view_completed_index_Location'); ?></label></div>
                <div class="ym-g40 ym-gl">
                    <?php
                    // check access for supper admin and instance admin.
                    if (APContext::isAdminParner() || APContext::isAdminUser()) {
                        echo my_form_dropdown(array(
                            "data" => $list_access_location,
                            "value_key" => 'id',
                            "label_key" => 'location_name',
                            "value" => $location_id,
                            "name" => 'location_id',
                            "id" => 'location_id',
                            "clazz" => 'input-width',
                            "style" => 'width:350px',
                            "has_empty" => true
                        ));
                    } else {
                        echo my_form_dropdown(array(
                            "data" => $list_access_location,
                            "value_key" => 'id',
                            "label_key" => 'location_name',
                            "value" => $location_id,
                            "name" => 'location_id',
                            "id" => 'location_id',
                            "clazz" => 'input-width readonly',
                            "style" => '',
                            "has_empty" => false,
                            "html_option" => '',
                        ));
                    }
                    ?>
                </div>
            </div>
            <!--#1318 add a filter to the completed list-->
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"><label><?php admin_language_e('scan_view_completed_index_ActivityFilter'); ?></label></div>
                <div class="ym-g90 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $activity_list,
                        "value_key" => 'id',
                        "label_key" => 'activity_name',
                        "value" => '',
                        "name" => 'activity_id',
                        "id" => 'activity_id',
                        "clazz" => 'input-width',
                        "style" => 'width:350px',
                        "has_empty" => true
                    ));
                    ?>   
                </div>
            </div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_view_completed_index_From'); ?></label>
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
                <div class="ym-g10 ym-gl" style="width:28px">
                    <label style="text-align: left;"><?php admin_language_e('scan_view_completed_index_To'); ?></label>
                </div>
                <div class="ym-g5 ym-gl">
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
                <button  style="margin-left: 10px" id="searchCompletedListButton" class="admin-button"><?php admin_language_e('scan_view_completed_index_Search'); ?></button>
                <button  style="margin-right: -150px"id="completedListExportCSVButton" class="admin-button"><?php admin_language_e('scan_view_completed_index_Export'); ?></button>
            </div>
        </div>
        <!--end #1318-->
        <input type="hidden" name="location_available_id" id="location_available_id" value="">
    </form>
</div>
<div id="searchTableResult" style="margin: 10px 10px 10px 0;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="scanEnvelopeWindow" title="<?php admin_language_e('scan_view_completed_index_ScanEnvelope'); ?>" class="input-form dialog-form">
    </div>
    <div id="viewDetailCustomer" title="<?php admin_language_e('scan_view_completed_index_ViewCustomerDetails'); ?>" class="input-form dialog-form">
    </div>
    <div id="viewCustomsDetail" title="<?php admin_language_e('scan_view_completed_index_ViewCustomsDetail'); ?>" class="input-form dialog-form">
    </div>
    <div id="viewDetailItems" title="<?php admin_language_e('scan_view_completed_index_ViewItemDetail'); ?>" class="input-form dialog-form">
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#"><?php admin_language_e('scan_view_completed_index_DisplayProformaInvoice'); ?></a>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();
        $('#display_pdf_invoice').fancybox({
            width: 900,
            height: 700,
            'onClosed': function () {
                $("#fancybox-inner").empty();
            }
        });
        /**
         * Process when user click to search button
         */
        $('#searchCompletedListButton').click(function (e) {
            searchCompleted();
            return false;
        });

        // Call search method
// 	 searchCompleted();
        var tableH = $.getTableHeight() + 27;
        $("#dataGridResult").jqGrid({
            width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            datatype: '{"page":"1","total":0,"records":0}',
            colNames: [
                '<?php admin_language_e('scan_view_completed_index_ColumnID'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnActivityID'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnFrom'); ?>',
                '',
                '<?php admin_language_e('scan_view_completed_index_ColumnTo'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnInvoicing'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnInvoicingCompany'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnShipment'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnShipmentCompany'); ?>',
                '',
                '<?php admin_language_e('scan_view_completed_index_ColumnType'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnWeight'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnDateTime'); ?>',
                '',
                '<?php admin_language_e('scan_view_completed_index_ColumnActivity'); ?>',
                '',
                '<?php admin_language_e('scan_view_completed_index_ColumnCompletedBy'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnCompletedDate'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnCost'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnPostalCharge'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnVAT'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnCustoms'); ?>',
                '<?php admin_language_e('scan_view_completed_index_ColumnAction'); ?>',
                '',
                ''
            ],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'activity_code', index: 'activity_code', width: 150},
                {name: 'from_customer_name', index: 'from_customer_name', width: 100},
                {name: 'to_customer_id_h', index: 'to_customer_id_h', hidden: true},
                {name: 'to_customer_id', index: 'to_customer_id', width: 170, formatter: toCustomerFormater},
                {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 100},
                {name: 'invoicing_company', index: 'invoicing_company', width: 100},
                {name: 'shipment_address_name', index: 'shipment_address_name', width: 100},
                {name: 'shipment_company', index: 'shipment_company', width: 100},
                {name: 'type_id', index: 'type_id', hidden: true},
                {name: 'envelope_type_id', index: 'envelope_type_id', width: 30},
                {name: 'weight', index: 'weight', width: 70, align: "right"},
                {name: 'last_updated_date', index: 'last_updated_date', width: 115},
                {name: 'row_id', index: 'row_id', hidden: true},
                {name: 'activity', index: 'activity', sortable: false, width: 100, formatter: detail_activity},
                {name: 'completed_by', index: 'completed_by', sortable: false, hidden: true},
                {name: 'completed_name', index: 'completed_name', sortable: false, width: 100},
                {name: 'completed_date', index: 'completed_date', width: 115},
                {name: 'cost', index: 'cost', width: 45, sortable: false},
                {name: 'postal_charge', index: 'postal_charge', width: 80, sortable: false},
                {name: 'vat', index: 'vat', width: 35, sortable: false},
                {name: 'customs', index: 'customs', width: 40, align: "center", formatter: customsFormater},
                {name: 'row_id', index: 'row_id', width: 50, align: "center", formatter: actionFormater},
                {name: 'envelope_id', index: 'envelope_id', width: 55, hidden: true},
                {name: 'incomming_date', index: 'incomming_date', width: 55, hidden: true},
            ]
        });
        /**
         * Search data
         */
        function searchCompleted() {
            $("#location_available_id").val($("#location_id").val());
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>scans/completed/search';

            // Gets page width
//		var pageWidth = 1240;
//		pageWidth = $(window).width() - 60;
            var tableH = $.getTableHeight() + 34;
            $("#dataGridResult").jqGrid({
                url: url,
                datatype: "json",
                postData: $('#completedListSearchForm').serializeObject(),
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting(); //Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'completed_date',
                sortorder: 'desc',
                multiSort: true,
                viewrecords: true,
                shrinkToFit: false,
                altRows: true,
                multiselect: false,
                multiselectWidth: 40,
                altclass: 'jq-background',
                captions: '',
                colNames: [
                    '<?php admin_language_e('scan_view_completed_index_ColumnID'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnActivityID'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnFrom'); ?>',
                    '',
                    '<?php admin_language_e('scan_view_completed_index_ColumnTo'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnInvoicing'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnInvoicingCompany'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnShipment'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnShipmentCompany'); ?>',
                    '', 
                    '<?php admin_language_e('scan_view_completed_index_ColumnType'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnWeight'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnDateTime'); ?>',
                    '',
                    '<?php admin_language_e('scan_view_completed_index_ColumnActivity'); ?>',
                    '',
                    '<?php admin_language_e('scan_view_completed_index_ColumnCompletedBy'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnCompletedDate'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnCost'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnPostalCharge'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnVAT'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnCustoms'); ?>',
                    '<?php admin_language_e('scan_view_completed_index_ColumnAction'); ?>',
                    '',
                    ''
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'activity_code', index: 'activity_code', width: 180},
                    {name: 'from_customer_name', index: 'from_customer_name', width: 100},
                    {name: 'to_customer_id_h', index: 'to_customer_id_h', hidden: true},
                    {name: 'to_customer_id', index: 'to_customer_id', width: 100, formatter: toCustomerFormater},
                    {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 150},
                    {name: 'invoicing_company', index: 'invoicing_company', width: 150},
                    {name: 'shipment_address_name', index: 'shipment_address_name', width: 190},
                    {name: 'shipment_company', index: 'shipment_company', width: 100},
                    {name: 'type_id', index: 'type_id', hidden: true},
                    {name: 'envelope_type_id', index: 'envelope_type_id', width: 50},
                    {name: 'weight', index: 'weight', width: 70, align: "right"},
                    {name: 'last_updated_date', index: 'last_updated_date', width: 115},
                    {name: 'row_id', index: 'row_id', hidden: true},
                    {name: 'activity', index: 'activity', sortable: false, width: 100, formatter: detail_activity},
                    {name: 'completed_by', index: 'completed_by', sortable: false, hidden: true},
                    {name: 'completed_name', index: 'completed_name', sortable: false, width: 100},
                    {name: 'completed_date', index: 'completed_date', width: 115},
                    {name: 'cost', index: 'cost', width: 45, sortable: false},
                    {name: 'postal_charge', index: 'postal_charge', width: 80, sortable: false},
                    {name: 'vat', index: 'vat', width: 35, sortable: false},
                    {name: 'customs', index: 'customs', width: 40, align: "center", formatter: customsFormater},
                    {name: 'row_id', index: 'row_id', width: 50, align: "center", formatter: actionFormater},
                    {name: 'envelope_id', index: 'envelope_id', width: 55, hidden: true},
                    {name: 'incomming_date', index: 'incomming_date', width: 55, hidden: true},
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                },
                loadComplete: function () {
                    // 20141014 Start fixbug #258
                    //$.autoFitScreen(1240);
                    $.autoFitScreen(($(window).width() - 40)); //#1297 check all tables in the system to minimize wasted space
                    // 20141014 End fixbug #258
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="<?php admin_language_e('scan_view_completed_index_Delete'); ?>"></span></span>';
        }

        function detail_activity(cellvalue, options, rowObject) {
            return '<a data_envelope_id="' + rowObject[23] + '" data_customer_id="' + rowObject[3] + '" id="detail_activity">' + cellvalue + '</a>';
        }

        function activeFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('scan_view_completed_index_Check'); ?></span></span>';
            } else {
                return '';
            }
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="view_customer_detail" data-envelope_id="' + rowObject[23] + '" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + rowObject[4] + '</a>';
        }

        /**
         * Action format for view detail cusoms
         */
        function customsFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                var envelope_id = rowObject[23];
                return '<a class="view_detail_customs" style="text-decoration: underline;" target="_blank" href="<?php echo base_url() ?>scans/completed/view_customs_pdf_invoice?envelope_id=' 
                        + envelope_id + '"><?php admin_language_e('scan_view_completed_index_Yes'); ?></a>';
            } else {
                return '<?php admin_language_e('scan_view_completed_index_No'); ?>';
            }
        }

        /**
         * View detail cusoms
         */
        $('.view_detail_customs').live('click', function () {
            var invoices_href = this.href;
            $('#display_pdf_invoice').attr('href', invoices_href);
            $('#display_pdf_invoice').click();
            return false;
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var envelope_id = $(this).attr('data-id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('scan_view_completed_index_DeleteConfirmationMessage'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>scans/completed/delete?id=' + envelope_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchCompleted();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

        $('#detail_activity').live('click', function () {

            var customer_id = $(this).attr('data_customer_id');
            var envelope_id = $(this).attr('data_envelope_id');
            console.log("customer_id:" + customer_id);
            console.log("envelope_id:" + envelope_id);
            // Clear control of all dialog form
            $('.dialog-form').html('');
            // Open new dialog
            $('#viewDetailItems').openDialog({
                autoOpen: false,
                height: 300,
                width: 400,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>scans/completed/view_detail_items?envelope_id=" + envelope_id + "&customer_id=" + customer_id, function (data) {});
                }
                /*,
                 buttons: {
                 'Cancel': function () {
                 $(this).dialog('close');
                 }
                 }*/
            });
            $('#viewDetailItems').dialog('option', 'position', 'center');
            $('#viewDetailItems').dialog('open');
        });

        /**
         * Process when user click to view detail customer information
         */
        $('.view_customer_detail').live('click', function () {
            var customer_id = $(this).attr('data-id');
            var envelope_id = $(this).attr('data-envelope_id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#viewDetailCustomer').openDialog({
                autoOpen: false,
                height: 600,
                width: 1000,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>customers/admin/view_detail_customer?id=" + customer_id + '&envelope_id=' + envelope_id, function (data) {
                        $('#viewDetailCustomer').html(data);
                        $('#addEditCustomerForm_email').focus();
                    });
                },
                buttons: {
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#viewDetailCustomer').dialog('option', 'position', 'center');
            $('#viewDetailCustomer').dialog('open');
        });

        /**
         * change location.
         */
        $("#location_id").live("change", function () {
            searchCompleted();
        });

        /*
         *  #1318 add a filter to the completed list
         */
        $('#completedListExportCSVButton').live('click', function () {
            $('#completedListSearchForm').attr('action', '<?php echo base_url() ?>scans/completed/completed_list_export');
            $('#completedListSearchForm').submit();
        });
    });
</script>