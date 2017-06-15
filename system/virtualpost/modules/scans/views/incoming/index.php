<style>
    #dimesion_div {
        float: left;
        text-align: right;
        display: inline
    }
    #button_div {
        float: left;
        text-align: left;
        margin: 0px 580px auto;
        display: inline
    }
    #addIncommingEnvelopeButton {
        width: 125px; 
        margin-left: 20px;

    }
    #searchIncommingEnvelopeButton{
        width: 125px; 
        margin-left: 10px;
    }

</style>
<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('scan_views_incoming_index_IncomingList'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="locationForm" action="<?php echo base_url() ?>scans/incoming" method="post">
        <div class="ym-g50 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"><label><?php admin_language_e('scan_views_incoming_index_Location'); ?></label></div>
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
                            "style" => 'width:220px',
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
                            "style" => 'width:220px;',
                            "has_empty" => false,
                            "html_option" => '',
                        ));
                    }
                    ?>
                </div>
            </div>
        </div>
    </form>
    <div class="ym-clearfix"></div>

    <form id="addIncommingEnvelopeForm" action="<?php echo base_url() ?>scans/incoming/add" method="post">
        <div class="ym-g50 ym-gl">

            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_views_incoming_index_From'); ?><span class="required">*</span></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="From_ID" name="from_customer_name" value="" class="input-txt" maxlength=255 />
                </div>
            </div>
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_views_incoming_index_To'); ?><span class="required">*</span></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="customer_id_auto" name="customer_id_auto" value="" class="input-txt" maxlength=255 />
                    <input type="hidden" id="customer_id" name="customer_id" value="" class="input-txt" maxlength=255 />
                    <input type="hidden" id="postbox_id" name="postbox_id" value="" class="input-txt" maxlength=255 />
                    <span class="required" id="warning_message" style="margin-top: 5px; margin-bottom: 5px;display: none">This customer was not activated.</span>
                    <span id="view_verify_info" style="position: absolute;top: 6px;display: none;"><a style='font-size: 14px;' target="_blank" href=""></a></span>
                </div>

            </div>
            <div class="ym-clearfix"></div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_views_incoming_index_Type'); ?><span class="required">*</span></label>
                </div>
                <div class="ym-g40 ym-gl">
                    <?php
                    $data = array(
                        "code" => APConstants::ENVELOPE_TYPE_CODE,
                        "value" => '',
                        "name" => 'type',
                        "id" => 'type',
                        "clazz" => 'input-text',
                        "style" => '',
                        "has_empty" => true
                    );
                    if (!empty($location_id) && (count($checkTypeAvailable)))
                        $data['location_id'] = $location_id;
                    echo code_master_form_dropdown($data);
                    ?>	
                </div>
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_views_incoming_index_Weight'); ?><span class="required">*</span></label>
                </div>
                <div class="ym-gl" style="width:17%;">
                    <input type="text" id="Weight_ID" name="weight" value="" class="input-txt" maxlength=10 />
                </div>
                <p class="ym-gr" style="height: 30px; text-align: center; line-height: 30px; color: #6c6c6c; font-size: 15px;"><?php echo $weight_unit ?></p>
            </div>
            <div class="ym-clearfix"></div>

            <div class="ym-grid input-item" id="dimesion_div" style="display:none">
                <div class="ym-g20 ym-gl">
                    <label><?php admin_language_e('scan_views_incoming_index_Dimensions'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <div class="ym-g10 ym-gl">
                        <label><?php admin_language_e('scan_views_incoming_index_Length'); ?><span class="required">*</span></label>
                    </div>
                    <div class="ym-g10 ym-gl">
                        <input type="text" id="length_ID" name="length" value="" class="input-txt" maxlength=10 />
                    </div>
                    <div class="ym-g10 ym-gl">
                        <label style="text-align: right;"><?php echo $length_unit ?></label>
                    </div>
                    <div class="ym-g10 ym-gl">
                        <label><?php admin_language_e('scan_views_incoming_index_Height'); ?><span class="required">*</span></label>
                    </div>
                    <div class="ym-g10 ym-gl">
                        <input type="text" id="height_ID" name="height" value="" class="input-txt" maxlength=10 />
                    </div>
                    <div class="ym-g10 ym-gl">
                        <label style="text-align: right;"><?php echo $length_unit ?></label>
                    </div>
                    <div class="ym-g10 ym-gl">
                        <label><?php admin_language_e('scan_views_incoming_index_Width'); ?><span class="required">*</span></label>
                    </div>
                    <div class="ym-g10 ym-gl">
                        <input type="text" id="width_ID" name="width" value="" class="input-txt" maxlength=10 />
                    </div>
                    <div class="ym-g10 ym-gl">
                        <label><?php echo $length_unit ?></label>
                    </div>
                </div>
            </div>
            <div class="ym-g100 ym-gl" id="button_div">
                <div id="copy-envelope-code" class="copy-envelope-code" data-clipboard-text="Copy Me!" title="Click to copy." style="position: fixed; top: -1000px; left: -1000px;">
                    <?php admin_language_e('scan_views_incoming_index_CopyToClipboard'); ?></div>
                <input type="button" id="addIncommingEnvelopeButton" class="input-btn c yl" value="<?php admin_language_e('scan_views_incoming_index_Add'); ?>"  />
                <input type="button" id="searchIncommingEnvelopeButton" class="input-btn c yl" value="<?php admin_language_e('scan_views_incoming_index_Search'); ?>" />
            </div>
            <div class="ym-clearfix"></div>
        </div>
        <input type="hidden" name="location_available_id" id="location_available_id" value="">
    </form>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <!-- Hidden form -->
    <form id="nextToDoForm" action="<?php echo base_url() ?>scans/todo" method="post">
        <input type="hidden" id="nextToDoForm_from" name="from" value="" />
        <input type="hidden" id="nextToDoForm_to_customer_id" name="to_customer_id" value="" />
        <input type="hidden" id="nextToDoForm_to_customer_name" name="to_customer_name" value="" />
        <input type="hidden" id="nextToDoForm_type_id" name="type_id" value="" />
        <input type="hidden" id="nextToDoForm_type" name="type" value="" />
        <input type="hidden" id="nextToDoForm_weight" name="weight" value="" />
    </form>
    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url() ?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
    </form>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="viewDetailCustomer" class="input-form dialog-form"></div>
    <div id="createDirectCharge" class="input-form dialog-form"></div>
    <div id="recordExternalPayment" class="input-form dialog-form"></div>
    <div id="recordRefundPayment" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
    <div id="createDirectInvoice" class="input-form dialog-form"></div>
    <div id="envelopeCommentWindow" title="<?php admin_language_e('scan_views_incoming_index_Comment'); ?>" class="input-form dialog-form"></div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/scans/js/ZeroClipboard.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var pathToFlashFile = "<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/scans/js/ZeroClipboard.swf";
        ZeroClipboard.config({swfPath: pathToFlashFile});

        // Apply checkbox style
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '30px');

        // Call search method
        searchIncomming();

        /**
         * Search data
         */
        function searchIncomming() {
            $("#location_available_id").val($("#location_id").val());
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>scans/incoming/search';
            var tableH = $.getTableHeight() + 3;
            $("#dataGridResult").jqGrid({
                url: url,
                mtype: 'POST',
                datatype: "json",
                postData: $('#addIncommingEnvelopeForm').serializeObject(),
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting(); //Settings::get(APConstants::NUMBER_RECORD_PER_PAGE_CODE); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: true,
                altRows: true,
                multiselect: false,
                multiselectWidth: 40,
                altclass: 'jq-background',
                captions: '',
                colNames: [
                    '<?php admin_language_e('scan_views_incoming_index_ColumnID'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnEnvelopeID'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnFrom'); ?>',
                    '',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnTo'); ?>',
                    '',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnType'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnWeight'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnCategoryType'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnCategory'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnInvoice'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnDateTime'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnAutoScan'); ?>',
                    '<?php admin_language_e('scan_views_incoming_index_ColumnAction'); ?>', 
                    ''
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'envelope_code', index: 'envelope_code', width: 190, formatter: toCustomerFormater02},
                    {name: 'from_customer_name', index: 'from_customer_name', width: 180},
                    {name: 'to_customer_id_h', index: 'to_customer_id_h', hidden: true},
                    {name: 'to_customer_id', index: 'to_customer_id', width: 180, formatter: toCustomerFormater},
                    {name: 'type_id', index: 'type_id', hidden: true},
                    {name: 'envelope_type_id', index: 'envelope_type_id', width: 80},
                    {name: 'weight', index: 'weight', width: 100, align: "right"},
                    {name: 'category_type', index: 'category_type', hidden: true},
                    {name: 'category', index: 'category', sortable: false, hidden: true, width: 150},
                    {name: 'invoice', index: 'invoice', width: 70, sortable: false, hidden: true, align: "center", formatter: activeFormater},
                    {name: 'incomming_date', index: 'incomming_date', width: 120},
                    {name: 'auto_scan', index: 'auto_scan', width: 50, sortable: false},
                    {name: 'row_id', index: 'row_id', width: 75, sortable: false, align: "center", formatter: actionFormater},
                    {name: 'activated_flag', index: 'activated_flag', hidden: true}

                ],
                loadComplete: function () {
                    // var total_record = $('#dataGridResult').getGridParam("records");
                    $('#warning_message').hide();
                    $.autoFitScreen(($( window ).width()- 50)); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function activeFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('scan_views_incoming_index_Check'); ?></span></span>';
            } else {
                return '';
            }
        }

        function actionFormater(cellvalue, options, rowObject) {
            if (rowObject[14] != '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                        + '" title="<?php admin_language_e('scan_views_incoming_index_Delete'); ?>"></span></span>';
            } else {
                // return '';
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                        + '" title="<?php admin_language_e('scan_views_incoming_index_Delete'); ?>"></span></span>';
            }
        }

        function toCustomerFormater(cellvalue, options, rowObject) {
            return '<a class="view_customer_detail" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + rowObject[4] + '</a>';
        }

        function toCustomerFormater02(cellvalue, options, rowObject) {
            var fullEnvelopeCode = rowObject[1];
            var linkEnvelopeCode = fullEnvelopeCode.substr(0, 9);
            var remainEnvelopeCode = fullEnvelopeCode.substr(9);
            return '<a class="access_customer_site" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + linkEnvelopeCode + '</a>' + remainEnvelopeCode;
        }

        /**
         * Click search button
         */
        $('#searchIncommingEnvelopeButton').live('click', function () {
            searchIncomming();
        });

        // Auto completed
        $("#customer_id_auto").autocomplete({
            source: '<?php echo base_url() ?>scans/incoming/auto_postbox?location_id=' + $("#location_id").val(),
            delay: 1000,
            select: function (event, ui) {
                $("#customer_id").val(ui.item.customer_id);
                $("#postbox_id").val(ui.item.postbox_id);
                var postbox_verify = ui.item.postbox_verify;
                var verify_info_url = '<?php echo base_url() ?>cases/todo/view_verification_detail?cid=' + ui.item.customer_id;
                if (postbox_verify) {
                    $("#view_verify_info a").text('<?php admin_language_e('scan_views_incoming_index_ViewCustomerVerification'); ?>')
                            .attr('href', verify_info_url).attr('target', '_blank').css({'text-decoration': 'underline', 'color': '#0e76bc'});
                    $("#view_verify_info").css({'right': '-188px'}).show();
                } else {

                    $("#view_verify_info a").css({"color": "red", "text-decoration": "none"}).text("<?php admin_language_e('scan_views_incoming_index_CustomerNotVerified'); ?>")
                            .attr('href', verify_info_url).attr('target', '_balnk');
                    $("#view_verify_info").css({"right": "-84px"}).show();
                }
                var activated_flag = ui.item.activated_flag;
                var cusomter_id = ui.item.cusomter_id;
                if (activated_flag == 0) {
                    // Display message
                    $('#warning_message').text('<?php admin_language_e('scan_views_incoming_index_CustomerNotActivated'); ?>').show();

                } else if (ui.item.customer_status == '1') {
                    $('#warning_message').text('<?php admin_language_e('scan_views_incoming_index_CustomerDeleted'); ?>').show();
                } else if (activated_flag == 1) {
                    // Clear message
                    $('#warning_message').hide();
                }
            },
            minLength: 3
        });
        $("#customer_id_auto").live("keyup", function () {
            if ($(this).val() == '') {
                $("#view_verify_info").hide();
            }
        });
        /**
         * Process when user click add button to add incomming envelope
         */
        $('#addIncommingEnvelopeButton').click(function () {

            var input_weight = $('#Weight_ID').val();
            input_weight = input_weight.replace(',', '');
            $('#Weight_ID').val(input_weight);
            var labelValue = $('#type option:selected').text();
            var submitUrl = $('#addIncommingEnvelopeForm').attr('action') + '?labelValue=' + labelValue;
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addIncommingEnvelopeForm',
                success: function (data) {
                    if (data.status) {
                        // Clear input data
                        $('#From_ID').val('');
                        $('#customer_id_auto').val('');
                        $('#customer_id').val('');
                        $('#postbox_id').val('');
                        $('#type').val('');
                        $('#Weight_ID').val('');
                        $('#length_ID').val('');
                        $('#height_ID').val('');
                        $('#width_ID').val('');
                        $('#dimesion_div').hide();
                        $('#button_div').attr('style', 'margin-top:0px;');
                        //$('#copy-envelope-code').attr('data-clipboard-text', data.data.envelope_code);
                        //$('#copy-envelope-code').text(data.data.envelope_code);

                        // Refresh the incoming list
                        searchIncomming();
                        $("#view_verify_info").hide();
                        //window.prompt("Copy Envelope Code to clipboard: Ctrl+C, Enter", data.data.envelope_code);
                        //var client = new ZeroClipboard($('#copy-envelope-code'));
                    } else {
                        $.displayError(data.message);
                    }
                }
            });

            return false;
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var envelope_id = $(this).attr('data-id');

            // Show confirm dialog
            $.confirm({
                message: 'Are you sure you want to delete?',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>scans/incoming/delete?id=' + envelope_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchIncomming();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });


        /**
         * Process when type change.
         */
        $('#type').change(function () {
            var actualValue = $(this).val();
            var labelValue = $('#type option:selected').text();
            var loadUrl = '<?php echo base_url() ?>scans/incoming/get_type?actualValue=' + actualValue + '&labelValue=' + labelValue;
            $.ajaxExec({
                url: loadUrl,
                success: function (data) {
                    if (data.status) {
                        var objResponse = data.data;
                        if (objResponse.Alias02 === 'Package') {
                            $('#dimesion_div').show();
                            $('#button_div').attr('style', 'margin-top:-33px;');
                        } else {
                            $('#dimesion_div').hide();
                            $('#button_div').attr('style', 'margin-top:0px;');
                        }
                    }
                }
            });
        });

        /**
         * change location.
         */
        $("#location_id").live("change", function () {
            $("#locationForm").submit();
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