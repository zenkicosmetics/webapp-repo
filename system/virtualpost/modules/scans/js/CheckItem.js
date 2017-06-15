var CheckItem = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        searchItemUrl: null,
        updateItemUrl: null,
        checkItemUrl: null,
        shippingUrl: null,
        shippingCheckUrl: null,
        previewImageUrl: null,
        completedCheckPageUrl: null,
        cancelItemUrl: null,
        executeScanUrl: null,
        deleteItemUrl: null,
        disablePrepaymentUrl:null,
        /*
         * #1363 BUG: BUG 48805 - we cannot get the customs declaration by pressing 'yes' on the check item page, 
         * it takes me to the customer's Postbox
         */
        viewCustomsPdfInvoice:null
    },

    /*
     * Paging configurations
     */
    configs: {
        rowNum: null,
        rowList: null
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        
        // init data
        CheckItem.initAjaxUrls(baseUrl);
        CheckItem.configs.rowNum = rowNum;
        CheckItem.configs.rowList = rowList;
        //$("#tracking_number, #shipping_services").css({"background":"#ebebeb"}).attr("disabled",true);
        // init screen
        CheckItem.initScreen();

        // Event listeners
        // trigger input item: activate the search button after 26 characters (length of ID) have been entered
        $("#item_id").on('input', function (e) {
            var value = $(this).val();
            if (value.length == 26) {
                $("#searchButton").click();
            }
        });

        $("#item_update").click(function (e) {
            e.preventDefault();
            CheckItem.updateItem();
        });

        // click search button
        $("#searchButton").click(function (e) {
            e.preventDefault();
            CheckItem.checkItem();
        });

        // Process when user click add button to add incomming envelope
        $('#scanEnvelopeButton').click(function () {
            $('#scan_type_id').val('1');
            $('#current_scan_type').val('1');
            $('#dynaScanLink').click();
            return false;
        });

        // Process when user click add button to add incomming envelope
        $('#scanItemButton').click(function () {
            $('#scan_type_id').val('2');
            $('#current_scan_type').val('2');
            $('#dynaScanLink').click();
            return false;
        });

        //Access the customer site
        $(document).on("click", '.access_customer_site', function () {
            var customer_id = $(this).attr('data-id');
            $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
            $('#hiddenAccessCustomerSiteForm').submit();
        });


        // Process when user click scan envelope.
        $('#dynaScanLink').on('click', function () {
            CheckItem.prepare_scan_window();

            $('#scanEnvelopeWindow').dialog('open');
            return false;
        });

        // When user click shipping envelope button
        $('#shippingEnvelopeButton').click(function () {
            // Check and display message if all collect shipping button is completed scan item and scan envelope
            // Get all record in shipping box
            var item_scan_flag = $('#item_scan_status').val();
            var envelope_scan_flag = $("#envelope_scan_status").val();
            if (item_scan_flag == '0' || envelope_scan_flag == '0') {
                $.displayError('Please complete scan for envelope: ' + envelope_id);
                return;
            }

            CheckItem.openShippingWindow();
        });

        // Trash Item
        $('#completeTrash').click(function () {
            //var current_scan_type = $('#current_scan_type').val();
            //if(current_scan_type == '') current_scan_type= '5';
            CheckItem.markCompleted('5');
        });

        // Process when user click mark completed button.
        $('#markCompletedButton').click(function () {
            var current_scan_type = $('#current_scan_type').val();

            CheckItem.markCompleted(current_scan_type);
        });

        // cancel item scan
        $("#cancelItemScanButton").click(function () {
            var envelope_id = $("#envelope_id").val();

            if (envelope_id > 0) {
                CheckItem.cancelAction(envelope_id, 1);
            }
        });

        // cancel envelop scan
        $("#cancelEnvelopeScanButton").click(function () {
            var envelope_id = $("#envelope_id").val();

            if (envelope_id > 0) {
                CheckItem.cancelAction(envelope_id, 2);
            }
        });

        // cancel direct shipping
        $("#cancelDirectShippingButton").click(function () {
            var envelope_id = $("#envelope_id").val();
            var type = $("#shipping_type").val();
            if (envelope_id > 0) {
                CheckItem.cancelAction(envelope_id, type);
            }
        });

        $('#linkViewUploadFile').fancybox({
            width: 1100,
            height: 700,
            hideOnOverlayClick: false,
            opacity: true
        });

        $("#change_envelope").click(function () {
            $("#changeItemTypeId").val(1);
            var envelope_scan_flag = $("#envelope_scan_status").val();

            if (envelope_scan_flag == 1) {
                $('#imagepath').click();
            } else {
                $.displayError('This envelope is not completed.', null);
            }
            return false;
        });

        $("#change_item").click(function () {
            $("#changeItemTypeId").val(2);
            var item_scan_flag = $('#item_scan_status').val();

            if (item_scan_flag == 1) {
                $('#imagepath').click();
            } else {
                $.displayError('This envelope is not completed.', null);
            }
            return false;
        });

        /**
         * When select file
         */
        $('#imagepath').on('change', function () {
            myfile = $(this).val();
            var ext = myfile.split('.').pop();

            if (ext.toUpperCase() != "PDF") {
                $('#container').css('visibility', 'hidden');
                $.displayError('Please select pdf file to upload.', null, function () {
                    $('#container').css('visibility', '');
                });

                return;
            }
            $('#loading').show();
            // Upload data here
            $.ajaxFileUpload({
                id: 'imagepath',
                data: {
                    customer_token_key: $("#token_key").val(),
                    envelope_id: $("#envelope_id").val(),
                    scan_type: $("#changeItemTypeId").val(),
                    action_type: 'upload',
                    number_page: 1
                },
                url: CheckItem.ajaxUrls.executeScanUrl,
                success: function (data) {
                    $('#loading').hide();
                    if (data && data.status) {
                        var linkUrl = data.data.private_path;
                        $('#linkViewUploadFile').attr('href', linkUrl);
                        $('#linkViewUploadFile').show();
                        $('#linkViewUploadFile').click();
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        });
        
        $('.managetables-icon-delete').live('click', function(){
            var id = $(this).data('id');
            CheckItem.deleteItem(id);
        });
        
        $("#disablePrepaymentButton").live('click',function(){
            var envelope_id = $("#envelope_id").val();
            CheckItem.disablePrepayment(envelope_id);
        });
        // init item
        $("#item_id").focus();
    },

    // unit url base
    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.searchItemUrl = baseUrl + 'scans/completed/search_complated_activities_check_item';
        this.ajaxUrls.updateItemUrl = baseUrl + 'scans/completed/save_item_info';
        this.ajaxUrls.checkItemUrl = baseUrl + 'scans/completed/check_item';
        this.ajaxUrls.shippingUrl = baseUrl + 'scans/todo/shipping';
        this.ajaxUrls.shippingCheckUrl = baseUrl + 'scans/todo/shipping_check';
        this.ajaxUrls.previewImageUrl = baseUrl + 'scans/todo/preview_image';
        this.ajaxUrls.completedCheckPageUrl = baseUrl + 'scans/todo/completed';
        this.ajaxUrls.cancelItemUrl = baseUrl + 'scans/completed/cancel_request';
        this.ajaxUrls.executeScanUrl = baseUrl + 'scans/todo/execute_scan';
        this.ajaxUrls.deleteItemUrl = baseUrl + 'scans/completed/delete';
        this.ajaxUrls.disablePrepaymentUrl = baseUrl + 'scans/completed/disable_prepayment';
        this.ajaxUrls.list_shipping_service_available = baseUrl + 'scans/todo/get_list_shipping_service_available';
        /*
         * #1363 BUG: BUG 48805 - we cannot get the customs declaration by pressing 'yes' on the check item page, 
         * it takes me to the customer's Postbox
         */
        this.ajaxUrls.viewCustomsPdfInvoice = baseUrl + 'scans/completed/view_customs_pdf_invoice?envelope_id=';
    },

    initScreen: function () {
        // Apply checkbox style
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '30px');
        $('button').button();
    },

    checkItem: function () {
        if ($("#item_id").val().trim() == '') {
            return false;
        }
        $("#tracking_number, #shipping_services").css({"background":"#ffffff"}).attr("disabled",false);
        // reset label items.
        CheckItem.resetItemLabel();

        $.ajaxExec({
            url: CheckItem.ajaxUrls.checkItemUrl,
            data: {item_id: $("#item_id").val().trim()},
            success: function (data) {
                if (data.status) {
                    var envelope = data.data.envelope;
                    var envelope_completed = data.data.envelope_completed;
                    var envelope_info = data.data.envelope_info;
                    var weight = data.data.weight;
                    var width = data.data.width;
                    var height = data.data.height;
                    var length = data.data.length;

                    // Set data envelope.
                    $(".status_item").show();
                    $("#markCompletedButton").show();
                    $("#envelope_id").val(envelope.id);
                    $("#envelope_ID").val(envelope.id);
                    $("#customer_id").val(envelope.to_customer_id);
                    $("#to_ID").val(envelope.to_customer_id);
                    $("#item_scan_status").val(envelope.item_scan_flag);
                    $("#envelope_scan_status").val(envelope.envelope_scan_flag);
                    $("#package_id").val(envelope.package_id);
                    $("#postbox_id").val(envelope.postbox_id);
                    $("#token_key").val(data.data.token_key);
                    $("#item_update_id").val(envelope.id);
                    $('#nextToDoForm_postbox_id').val(envelope.postbox_id);
                    $('#nextToDoForm_package_id').val(envelope.package_id);
                    
                    if (envelope.direct_shipping_flag == "0" || envelope.direct_shipping_flag == "2") {
                        $("#shipping_type").val("3");
                    } else if (envelope.collect_shipping_flag == "0" || envelope.collect_shipping_flag == "2") {
                        $("#shipping_type").val("4");
                    }

                    // set activity list.
                    CheckItem.searchItem();
                    
                    // show envelope information
                    CheckItem.showEnvelopeInformation(envelope_info,weight,width,height,length);

                    // Show envelope status
                    CheckItem.showEnvelopeStatus(envelope, envelope_completed);
                    $("#last_activity").html(envelope_completed.last_activity);

                    // Show account status
                    $("#account_status").html(data.data.account_status);

                    // Show Postbox verified status
                    $("#verified_status").html(data.data.verified_status);
                    var tracking_number = data.data.tracking_number;
                    var shipping_services_id = data.data.shipping_services_id;
                    $("#shipping_services").html('<option value="0">&nbsp;</option>');
                    $.ajax({
                        url: CheckItem.ajaxUrls.list_shipping_service_available,
                        type: 'POST',
                        dataType: 'html',
                        data: {envelope_id: envelope.id},
                    }).done(function ( data ) {
                        if(envelope.direct_shipping_flag == "1" || envelope.collect_shipping_flag == "1"){
                            $("#list_shipping_service_available").html(data);
                            $("#tracking_number, #shipping_services").css({"background":"#ffffff"}).attr("disabled",false);
                        }
                        else{
                            $("#tracking_number, #shipping_services").css({"background":"#ebebeb"}).attr("disabled",true);
                        }
                        $("#tracking_number").val(tracking_number);
                        $("#shipping_services").val(shipping_services_id);
                    });
                   
                } else {
                    CheckItem.showNotInStorageLabel(data.message, "");
                }
                
                // auto select search text item.
                $("#item_id").select();

                return false;
            }
        });
        $('#shipping_services').live('change', function() {
            $('#shipping_services, #tracking_number').css({"background":"#ffffff"}).attr("disabled",false);
        });
    },

    searchItem: function () {
        $("#dataGridResult").jqGrid('GridUnload');

        // Gets page width
//        var pageWidth = 1300;
        //pageWidth = $(window).width() - 40;
        var tableH = $.getTableHeight() - 10;
        $("#dataGridResult").jqGrid({
            url: CheckItem.ajaxUrls.searchItemUrl,
            datatype: "json",
            postData: {item_id: $("#item_id").val()},
            height: tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: CheckItem.configs.rowNum,
            rowList: CheckItem.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'completed_date',
            sortorder: 'DESC',
            multiSort: true,
            viewrecords: true,
            shrinkToFit: false,
            altRows: true,
            multiselect: false,
            multiselectWidth: 40,
            altclass: 'jq-background',
            captions: '',
            colNames: ['ID', 'Activity ID', 'From', '', 'To', 'Invoicing', 'Invoicing Company', 'Shipment', 'Shipment Company', '', 'Type', 'Weight', 'Date and Time', 'Date of last activity', '', 'Activity', 'Postbox name', 'Postbox company name', '', 'Completed By', 'Cost', 'VAT', 'Customs', 'Action', '', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'activity_code', index: 'activity_code', width: 250, formatter: CheckItem.toCustomerFormater02},
                {name: 'from_customer_name', index: 'from_customer_name', width: 100},
                {name: 'to_customer_id_h', index: 'to_customer_id_h', hidden: true},
                {name: 'to_customer_id', index: 'to_customer_id', width: 170, formatter: CheckItem.toCustomerFormater},
                {name: 'invoicing_address_name', index: 'invoicing_address_name', width: 100},
                {name: 'invoicing_company', index: 'invoicing_company', width: 100},
                {name: 'shipment_address_name', index: 'shipment_address_name', width: 100},
                {name: 'shipment_company', index: 'shipment_company', width: 100},
                {name: 'type_id', index: 'type_id', hidden: true},
                {name: 'envelope_type_id', index: 'envelope_type_id', width: 50},
                {name: 'weight', index: 'weight', width: 70, align: "right"},
                {name: 'last_updated_date', index: 'last_updated_date', width: 115},
                {name: 'completed_date', index: 'completed_date', width: 150},
                {name: 'row_id', index: 'row_id', hidden: true},
                {name: 'activity', index: 'activity', sortable: false, width: 200},
                {name: 'postbox_name', index: 'postbox_name', sortable: false, width: 120},
                {name: 'postbox_company_name', index: 'postbox_company_name', sortable: false, width: 150},
                {name: 'completed_by', index: 'completed_by', sortable: false, hidden: true},
                {name: 'completed_name', index: 'completed_name', sortable: false, width: 100},
                {name: 'cost', index: 'cost', width: 45, sortable: false},
                {name: 'vat', index: 'vat', width: 35, sortable: false},
                {name: 'customs', index: 'customs', width: 40, align: "center", formatter: CheckItem.customsFormater},
                {name: 'row_id', index: 'row_id', width: 50, align: "center", formatter: CheckItem.actionFormater},
                {name: 'envelope_id', index: 'envelope_id', width: 55, hidden: true},
                {name: 'incomming_date', index: 'incomming_date', width: 55, hidden: true},
            ],
            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
            },
            loadComplete: function (data) {
                // auto fit screen.
                $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    },

    toCustomerFormater02: function (cellvalue, options, rowObject) {
        var fullEnvelopeCode = rowObject[1];
        var linkEnvelopeCode = fullEnvelopeCode.substr(0, 9);
        var remainEnvelopeCode = fullEnvelopeCode.substr(9);
        return '<a class="access_customer_site" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + linkEnvelopeCode + '</a>' + remainEnvelopeCode;
    },

    toCustomerFormater: function (cellvalue, options, rowObject) {
        return '<a class="view_customer_detail" data-envelope_id="' + rowObject[22] + '" data-id="' + rowObject[3] + '" style="text-decoration: underline;" >' + rowObject[4] + '</a>';
    },
    
   /*
    * #1363 BUG: BUG 48805 - we cannot get the customs declaration by pressing 'yes' on the check item page, 
    * it takes me to the customer's Postbox
    */
    customsFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            var view_customsPdfInvoice_url = CheckItem.ajaxUrls.viewCustomsPdfInvoice;
            var envelope_id = rowObject[24];
            return '<a href="'+ view_customsPdfInvoice_url + envelope_id +'" class="view_detail_customs" style="text-decoration: underline;" data-id="' + rowObject[0] + '" title="View Customs">Yes</a>';
        } else {
            return 'No';
        }
    },

    actionFormater: function (cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
    },

    updateItem: function () {
        var item_update_url = CheckItem.ajaxUrls.updateItemUrl;
        data_update = $("#item_update_extra").serialize();
        var shipping_services = $("#shipping_services").val();
        $.ajaxExec({
            url: item_update_url,
            data: data_update+"&shipping_services="+shipping_services,
            success: function (data) {
                
                if (data.status) {
                    $('#msg').show().css({'color': 'green'}).html('Item has been updated successfully!').fadeOut(10000);
                }
                else {
                    $('#msg').show().css({'color': 'red'}).html('Update fail !').fadeOut(10000);
                }
                
                $("#searchButton").click();
            }
        });
    },

    prepare_scan_window: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');
        var scan_url = $('#dynaScanLink').attr('href');
        var scan_type = $('#scan_type_id').val();
        scan_url = scan_url + '?check_page=1&scan_type=' + scan_type + "&envelope_id=" + $("#envelope_id").val() + "&customer_id=" + $('#customer_id').val();

        var popup_height = 650;
        if (window.innerHeight < popup_height) {
            popup_height = window.innerHeight - 10;
        }
        var popup_width = 1100;
        if (window.innerWidth < popup_width) {
            popup_width = window.innerWidth - 10;
        }
        // Open new dialog
        $('#scanEnvelopeWindow').openDialog({
            autoOpen: false,
            height: popup_height,
            width: popup_width,
            modal: true,
            open: function () {
                $(this).load(scan_url, function () {
                    $('#buttonUploadPdfFile').button();
                    $('#DW_PreviewMode').val('1');
                });
            },
            buttons: {
                'Scan': function () {
                    CheckItem.scanFile();
                },
                'Save & Exit': function () {
                    CheckItem.saveFile();
                    $(this).dialog('close');
                },
                'Save & Exit (Without OCR)': function () {
                    document.getElementById('scanForm_UseOCRFlag').value = '0';
                    CheckItem.saveFile();
                    $(this).dialog('close');
                },
                'Close': function () {
                    $(this).dialog('close');
                    CheckItem.previewScanImage();
                }
            }
        });
        $('#scanEnvelopeWindow').dialog('option', 'position', 'center');
    },

    scanFile: function () {
        // Scan file
        var documentType = $('#documentType').val();
        if (documentType == '1') {
            var scan_url = $('#dynaScanLink').attr('href');
            $('#scanEnvelopeWindow').load(scan_url, function () {
                $('#buttonUploadPdfFile').button();
                $('#DW_PreviewMode').val('1');

                DWObject.SetViewMode(parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1), parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1));

                // Scan file: //'1': upload file; '2': scan file
                $('#documentType').val('2');

                // Watining 10 seconds
                setTimeout(function () {
                    acquireImage();
                }, 5000);
            });
        } else {
            DWObject.SetViewMode(parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1), parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1));
            // Scan file: '1': upload file; '2': scan file
            $('#documentType').val('2');
            acquireImage();
        }
    },

    saveFile: function () {
        // Change status
        $("#markCompleteButtonContainer").show();
        $('#markCompletedButton').addClass('yl');
        $('#markCompletedButton').addClass('input-btn');
        $('#markCompletedButton').removeClass('input-btn-disable');
        $('#markCompletedButton').prop('disabled', false);

        var documentType = $('#documentType').val();
        if (documentType == '1') {
            //'1': upload file; '2': scan file
            if (!confirm('Are you sure you want to proceed without OCR scan? (Item will not be searchable)')) {
                return;
            }
        }

        if (documentType == '2') {
            // Upload file
            btnUpload_onclick();

            var current_scan_type = $('#current_scan_type').val();
            if (current_scan_type == '2') {
                $('#scanItemTemporaryFlag_id').val('2');
            } else {
                $('#scanItemTemporaryFlag_id').val('1');
            }
        }
    },

    previewScanImage: function () {
        // Load image
        var envelope_id = $('#envelope_id').val();
        var customer_id = $('#customer_id').val();
        var scanItemFlag = $('#scanItemTemporaryFlag_id').val();
        var preview_url = CheckItem.ajaxUrls.previewImageUrl + '?customer_id=' + customer_id + '&envelope_id=' + envelope_id;
        preview_url += '&has_scan_item_type=' + scanItemFlag;

        // Load preview scan image
        $('#previewEnvelopeScan').html("<iframe id='previewEnvelopeScan_iframe' src='" + preview_url + "' style='height:210px; width:100%'><iframe>");
        var exist_document_file = true;
        if (exist_document_file) {
            $('#has_scan_image_id').val('1');
            // Remove hidden class
            $('#previewEnvelopeScanContainer').removeClass('hide');
            $('#previewShippingItemContainer').addClass('hide');
        } else {
            $('#has_scan_image_id').val('');
            // Add hidden class
            $('#previewShippingItemContainer').addClass('hide');
            $('#previewEnvelopeScanContainer').addClass('hide');
        }
    },
    
    openShippingWindow: function () { 
        $('#shippingEnvelopeWindow').html('');
        var to_customer_id = $("#customer_id").val();
        var envelope_id = $("#envelope_id").val();

        var shipping_url = CheckItem.ajaxUrls.shippingUrl + '?customer_id=' + to_customer_id + '&envelope_id=' + envelope_id;
        shipping_url += '&package_id=' + $("#package_id").val();
        shipping_url += '&postbox_id=' + $("#postbox_id").val();
        shipping_url += '&shipping_type=' + $("#shipping_type").val();

        var popup_height = 650;
        if (window.innerHeight < popup_height) {
            popup_height = window.innerHeight - 10;
        }
        // Open new dialog
        $('#shippingEnvelopeWindow').openDialog({
            autoOpen: false,
            height: popup_height,
            width: 1040,
            modal: true,
            open: function () {
                $(this).load(shipping_url, function () {
                });
            },
            buttons: {
                'Save & Exit': function () {
                    $('#shippingEnvelopeForm').attr('action', CheckItem.ajaxUrls.shippingCheckUrl);
                    $.ajaxSubmit({
                        url: CheckItem.ajaxUrls.shippingCheckUrl,
                        formId: 'shippingEnvelopeForm',
                        success: function (data) {
                            if (data.status) {
                                // Call to get estamp before print
                                CheckItem.print_label('0');
                                $("#shipping_services, #tracking_number").css({"background":"#ebebeb"}).attr("disabled",true);
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                    $('#shippingEnvelopeForm').attr('action', CheckItem.ajaxUrls.shippingUrl);
                    //$("#shipping_services").val($("#shippingEnvelopeForm_shipping_service_id").val());
                },
                'Print Label': function () {
                    $('#shippingEnvelopeForm').attr('action', CheckItem.ajaxUrls.shippingCheckUrl);
                    $.ajaxSubmit({
                        url: CheckItem.ajaxUrls.shippingCheckUrl,
                        formId: 'shippingEnvelopeForm',
                        success: function (data) {
                            if (data.status) {
                                // Call to get estamp before print
                                CheckItem.print_label('1');
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                    $('#shippingEnvelopeForm').attr('action', CheckItem.ajaxUrls.shippingUrl);
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#shippingEnvelopeWindow').dialog('option', 'position', 'center');
        $('#shippingEnvelopeWindow').dialog('open');
        return false;
    },

    print_label: function (print_flag) {
        // Check and submit data
        $('#shippingEnvelopeForm_current_scan_type').val(current_scan_type);
        if ($('#shippingEnvelopeForm_other_package_price_flag').attr('checked')) {
            var input_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
            input_fee = input_fee.replace(',', '.');
            if (!$.isValidNumber(input_fee)) {
                $.displayError('Other shipping fee should be numberic value.');
                return;
            }
            if (parseFloat(input_fee) < 0) {
                $.displayError('Other shipping fee should be greater than 0.');
                return;
            }
            $('#shippingEnvelopeForm_other_package_price_fee').val(input_fee);
        }

        $('#current_scan_type').val($("#shipping_type").val());

        // Change status
        $("#markCompleteButtonContainer").show();
        $('#markCompletedButton').addClass('yl');
        $('#markCompletedButton').addClass('input-btn');
        $('#markCompletedButton').removeClass('input-btn-disable');
        $('#markCompletedButton').prop('disabled', false);

        if (print_flag == '1') {
            if ($('#previewEstamp_iframe').length) {
                window.frames["previewEstamp_iframe"].focus();
                window.frames["previewEstamp_iframe"].print();
            } else {
                $.displayError('Please click to Create preview of stamp or Buy stamp button.');
                return;
            }
        } else {
            $('#shippingEnvelopeWindow').dialog('close');
        }
    },

    /**
     * Mark completed.
     * current_scan_type = 1: Envelope scan
     * current_scan_type = 2: Item scan
     * current_scan_type = 3: Direct shipping
     * current_scan_type = 4: Collect shipping
     * current_scan_type = 5: Trash
     */
    markCompleted: function (current_scan_type) {
        if (current_scan_type === '1' || current_scan_type === '2' || current_scan_type === '5') {
            var completed_url = CheckItem.ajaxUrls.completedCheckPageUrl + '?check_page_flag=1&customer_id=' + $('#customer_id').val() + '&envelope_id=' + $("#envelope_id").val();
            completed_url += '&current_scan_type=' + current_scan_type;
            completed_url += '&category_type=' + $('#category_type').val();
            $.ajaxExec({
                url: completed_url,
                success: function (data) {
                    if (data.status) {
                        // Reload data grid
                        $("#searchButton").click();
                    } else {
                        $.displayError(data.message);
                    }
                    $("#item_id").select();
                }
            });
            return false;
        } else {
            CheckItem.shippingDone(current_scan_type);
        }
        return false;
    },

    shippingDone: function (current_scan_type) {
        var submitUrl = $('#shippingEnvelopeForm').attr('action');
        $('#shippingEnvelopeForm_current_scan_type').val(current_scan_type);
        if ($('#shippingEnvelopeForm_other_package_price_flag').attr('checked')) {
            var input_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
            input_fee = input_fee.replace(',', '.');
            $('#shippingEnvelopeForm_other_package_price_fee').val(input_fee);
            if (!$.isValidNumber(input_fee)) {
                $.displayError('Other shipping fee should be numberic value.');
                return;
            }
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'shippingEnvelopeForm',
            success: function (data) {
                if (data.status) {
                    $('#shippingEnvelopeWindow').dialog('close');

                    // Change status
                    $("#markCompleteButtonContainer").show();
                    $('#markCompletedButton').addClass('yl');
                    $('#markCompletedButton').addClass('input-btn');
                    $('#markCompletedButton').removeClass('input-btn-disable');
                    $('#markCompletedButton').prop('disabled', false);
                } else {
                    $.displayError(data.message);
                }
                
                $("#searchButton").click();
                $("#item_id").select();
            }
        });
    },

    cancelAction: function (envelope_id, type) {
        $.ajaxExec({
            url: CheckItem.ajaxUrls.cancelItemUrl,
            data: {id: envelope_id, type: type},
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $("#searchButton").click();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    displayImageIconCheck: function (envelope) {

        if (envelope.activity_id2 == 5 || envelope == '' || envelope.trash_flag == 6  || envelope.trash_flag == 1 || envelope.direct_shipping_flag == 1 || envelope.collect_shipping_flag == 1) {

            $('#storage').addClass("img_cancel").css({"background-position":"-244px -78px"}).show();
            CheckItem.resetItemLabel();
        } else if (envelope.trash_flag == 0 || envelope.trash_flag == 5
            || envelope.direct_shipping_flag == 0 || (envelope.collect_shipping_flag == 0  && envelope.package_id > 0)
            || envelope.envelope_scan_flag == 0 || envelope.item_scan_flag == 0) {
            $('#storage').addClass("img_todo").css({"background-position":"-244px -26px"}).show();
        } else if (envelope.direct_shipping_flag == 1 || envelope.collect_shipping_flag == 1 
                || envelope.completed_flag == "1" || envelope.envelope_scan_flag == 1 || envelope.item_scan_flag == 1
                || (envelope.direct_shipping_flag == null && envelope.collect_shipping_flag == null 
                    && envelope.envelope_scan_flag == null && envelope.item_scan_flag == null)
                || (envelope.collect_shipping_flag == 0  && ( envelope.package_id == 0 || envelope.package_id == null))) {
            $('#storage').addClass("img_check").css({"background-position":"-244px -52px"}).show();
        }
        
        $("#storage_label").html("in storage");
        if(envelope.activity_id2 == "5" || envelope == '' || envelope == null 
                || (envelope.trash_flag == '' && envelope.trash_flag == null)
                || envelope.direct_shipping_flag == "1" || envelope.collect_shipping_flag == "1" || envelope.trash_flag != null || envelope.status_envelope == "not_in_storage"){
            $("#storage_label").html("Not in storage");
            $("#completeTrash").attr("disabled", "disabled");
            $('#storage').css({"background-position":"-244px -78px"}).show();
        }
    },
    
    showNotInStorageLabel: function(message, last_activity){
        $("#scanButtonContainerSub, #cancelButtonContainerSub, #markCompletedButton, #trash_class").attr("disabled", "disabled").hide();
        $("#items_update").hide();
        $("#item_from").val("");
        $("#item_weight").val("");
        $("#item_width").val('');
        $("#item_height").val('');
        $("#item_length").val('');
        $("#dataGridResult").jqGrid('clearGridData');
        $("#tracking_number, #shipping_services").val('');
        $('#storage').addClass("img_cancel").css({"background-position":"-244px -78px"}).show();
        $("#searchTableResult").css({"margin-top": "0px"});
        $("#divErrorMessage").html(message);
        $("#last_activity").html(last_activity);
        $("#storage_label").html("Not in storage");
        $(".status_item").hide();
    },
    
    resetItemLabel: function(){
        $("#last_activity").html("");
        $("#completed_list").hide();
        $("#divErrorMessage").html("");
       
        // clear class
        $("#envelope_class").removeClass('envelop').removeClass('envelop-yellow').removeClass('envelop-blue').removeClass('envelop-orange');
        $("#scan_class").removeClass('scan_email').removeClass('scan_email-yellow').removeClass('scan_email-blue').removeClass('scan_email-orange');
        $("#cloud_class").removeClass('cloud-yellow').removeClass('cloud-blue');
        $("#send_class").removeClass('send-yellow').removeClass('send-blue').removeClass('send-orange');
        $("#collect_class").removeClass('collect-green').removeClass('collect-yellow').removeClass('collect-blue').removeClass('collect-orange');
        $("#trash_class").removeClass("trash-yellow");
        
        $("#scanEnvelopeButton").addClass("input-btn-disable");
        $("#scanEnvelopeButton").attr("disabled", "disabled");
        $("#scanItemButton").addClass("input-btn-disable");
        $("#scanItemButton").attr("disabled", "disabled");
        $("#shippingEnvelopeButton").addClass("input-btn-disable");
        $("#shippingEnvelopeButton").attr("disabled", "disabled");
        $("#markCompletedButton").addClass("input-btn-disable");
        $("#markCompletedButton").attr("disabled", "disabled");
        $("#completeTrash").removeClass("input-btn");
        $("#completeTrash").addClass("actionButton");
        $("#completeTrash").addClass("input-btn-disable");
        $("#completeTrash").attr("disabled", "disabled");

        $("#change_envelope").hide();
        $("#change_item").hide();
    },

    showEnvelopeInformation: function(envelope,weight,width,height,length){
        $("#items_update").show();
        
        $("#item_from").val(envelope.from_customer_name);

        if (!isNaN(parseInt(weight))) {
            $("#item_weight").val(weight);
        }
        else {
            $("#item_weight").val('');
        }

        if (!isNaN(parseInt(width))) {
            $("#item_width").val(width);
        }
        else {
            $("#item_width").val('');
        }

        if (!isNaN(parseInt(height))) {
            $("#item_height").val(height);
        }
        else {
            $("#item_height").val('');
        }

        if (!isNaN(parseInt(length))) {
            $("#item_length").val(length);
        }
        else {
            $("#item_length").val('');
        }
    },
    
    showEnvelopeStatus: function(envelope){
        // init status
        envelope_class = 'envelop';
        item_class = 'scan_email';
        cloud_class = 'cloud';
        send_class = 'send';
        collect_class = 'collect';
        flag_send_out = false;

        // Setting class for envelope icon
        if (envelope.envelope_scan_flag == null) {
            envelope_class = 'envelop';
        } else if (envelope.envelope_scan_flag == '0') {
            envelope_class = 'envelop-yellow';
        } else if (envelope.envelope_scan_flag == '1') {
            envelope_class = 'envelop-blue';
            $("#change_envelope").show();
        } else if (envelope.envelope_scan_flag == '2') {
            envelope_class = 'envelop-orange';
        }

        // Setting class for item scan
        if (envelope.item_scan_flag == null) {
            item_class = 'scan_email';
        } else if (envelope.item_scan_flag == '0') {
            item_class = 'scan_email-yellow';
        } else if (envelope.item_scan_flag == '1') {
            item_class = 'scan_email-blue';
            $("#change_item").show();
        } else if (envelope.item_scan_flag == '2') {
            item_class = 'scan_email-orange';
        }

        // Setting class for cloud icon
        if (envelope.sync_cloud_flag == '1') {
            cloud_class = 'cloud-blue';
        } else {
            cloud_class = 'cloud';
        }

        // Setting class for send icon
        if (envelope.direct_shipping_flag == null) {
            send_class = 'send';
        } else if (envelope.direct_shipping_flag == '0') {
            send_class = 'send-yellow';
        } else if (envelope.direct_shipping_flag == '1') {
            send_class = 'send-blue';
            flag_send_out = true;
        } else if (envelope.direct_shipping_flag == '2') {
            send_class = 'send-orange';
        } 

        // Setting class for collect shipping
        if (envelope.collect_shipping_flag == null) {
            collect_class = 'collect';
        } else if (envelope.collect_shipping_flag == '0') {
            if (envelope.package_id != null) {
                collect_class = 'collect-yellow';
            }
            else {
                collect_class = 'collect-green';
            }
        } else if (envelope.collect_shipping_flag == '1') {
            collect_class = 'collect-blue';
            flag_send_out = true;
        } else if (envelope.collect_shipping_flag == '2') {
            collect_class = 'collect-orange';
        }

        if (envelope.trash_flag == 1 || envelope.trash_flag == 0
            || envelope.direct_shipping_flag == 1
            || envelope.collect_shipping_flag == 1) {
            flag_send_out = true;
        }

        if (flag_send_out) {
            $("#change_envelope").hide();
            $("#change_item").hide();
        }

        // display image icon.
        CheckItem.displayImageIconCheck(envelope);

        // set style status
        $("#envelope_class").addClass(envelope_class);
        $("#scan_class").addClass(item_class);
        $("#cloud_class").addClass(cloud_class);
        $("#send_class").addClass(send_class);
        $("#collect_class").addClass(collect_class);
        if( (envelope.trash_flag == 0 || envelope.trash_flag == 5) && envelope.activity_id != 5 ){
            $("#trash_class").addClass("trash-yellow");
        }else{
            $("#trash_class").addClass("trash");
        }
        $("#trash_class").show();
        
        // check enable 
        CheckItem.checkActivity(envelope, flag_send_out);
    },
    
    checkActivity: function (envelope, flag_send_out) {
        $('.actionButton').removeClass('input-btn');
        $('.actionButton').addClass('input-btn-disable');
        $('.actionButton').prop('disabled', true);

        if ( (envelope.trash_flag == "0" || envelope.trash_flag == "5") && (envelope.activity_id2 != "5") ) {
            $('#scanButtonContainerSub,  #markCompleteButtonContainer, #cancelButtonContainerSub').show();
            
			if(envelope.status_envelope != "not_in_storage"){
					
				$("#completeTrash").show();
				$('#completeTrash').removeClass('input-btn-disable');
				$('#completeTrash').addClass('input-btn');
				$('#completeTrash').prop('disabled', false);
			}	

        } else if (envelope.trash_flag == 1) {
            $('#scanButtonContainerSub, #markCompleteButtonContainer').hide();
        } else {
            $('#scanButtonContainerSub, #markCompleteButtonContainer, #cancelButtonContainerSub').show();

            if (envelope.envelope_scan_flag == "0") {
                $("#scanEnvelopeButton").show();
                $('#scanEnvelopeButton').removeClass('input-btn-disable');
                $('#scanEnvelopeButton').addClass('input-btn');
                $('#scanEnvelopeButton').prop('disabled', false);

                $("#cancelEnvelopeScanButton").show();
                $("#cancelEnvelopeScanButton").removeClass('input-btn-disable');
                $("#cancelEnvelopeScanButton").addClass('input-btn');
                $("#cancelEnvelopeScanButton").prop('disabled', false);
            }

            if (envelope.item_scan_flag == "0") {
                $("#scanItemButton").show();
                $('#scanItemButton').removeClass('input-btn-disable');
                $('#scanItemButton').addClass('input-btn');
                $('#scanItemButton').prop('disabled', false);

                $("#cancelItemScanButton").show();
                $("#cancelItemScanButton").removeClass('input-btn-disable');
                $("#cancelItemScanButton").addClass('input-btn');
                $("#cancelItemScanButton").prop('disabled', false);
            }

            // show cancel and forwading button.
            if (envelope.direct_shipping_flag == 0 || (envelope.collect_shipping_flag == '0' && envelope.package_id > 0 )) {
                $('#shippingEnvelopeButton').show();
                $('#shippingEnvelopeButton').removeClass('input-btn-disable');
                $('#shippingEnvelopeButton').addClass('input-btn');
                $('#shippingEnvelopeButton').prop('disabled', false);

                $("#cancelDirectShippingButton").show();
                $("#cancelDirectShippingButton").removeClass('input-btn-disable');
                $("#cancelDirectShippingButton").addClass('input-btn');
                $("#cancelDirectShippingButton").prop('disabled', false);
            }

            //show cancel collect shipping
            if(envelope.collect_shipping_flag == '0' && envelope.package_id > 0 && envelope.package_id != null){
                $("#cancelDirectShippingButton").show();
                $("#cancelDirectShippingButton").removeClass('input-btn-disable');
                $("#cancelDirectShippingButton").addClass('input-btn');
                $("#cancelDirectShippingButton").prop('disabled', false);
            }
            
            if(envelope.envelope_scan_flag == "2"){
                $("#cancelEnvelopeScanButton").show();
                $("#cancelEnvelopeScanButton").removeClass('input-btn-disable');
                $("#cancelEnvelopeScanButton").addClass('input-btn');
                $("#cancelEnvelopeScanButton").prop('disabled', false);
                
                CheckItem.enablePrepaymentButton();
            }
            
            if (envelope.item_scan_flag == "2") {
                $("#cancelItemScanButton").show();
                $("#cancelItemScanButton").removeClass('input-btn-disable');
                $("#cancelItemScanButton").addClass('input-btn');
                $("#cancelItemScanButton").prop('disabled', false);
                
                CheckItem.enablePrepaymentButton();
            }
            
            if(envelope.collect_shipping_flag == '2' || envelope.direct_shipping_flag == 2){
                $("#cancelDirectShippingButton").show();
                $("#cancelDirectShippingButton").removeClass('input-btn-disable');
                $("#cancelDirectShippingButton").addClass('input-btn');
                $("#cancelDirectShippingButton").prop('disabled', false);
                
                CheckItem.enablePrepaymentButton();
            }
        }
    },
    
    deleteItem: function(id){
        var url = CheckItem.ajaxUrls.deleteItemUrl + "?id=" + id;
        $.ajaxExec({
            url: url,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $("#searchButton").click();
                } else {
                    $.displayError(data.message);
                }
                $("#item_id").select();
            }
        });
    },
    
    enablePrepaymentButton: function(){
        $("#disablePrepaymentButton").show();
        $("#disablePrepaymentButton").removeClass('input-btn-disable');
        $("#disablePrepaymentButton").addClass('input-btn');
        $("#disablePrepaymentButton").prop('disabled', false);
    },
    
    disablePrepayment: function(id){
        var url = CheckItem.ajaxUrls.disablePrepaymentUrl+ "?id=" + id;
        $.ajaxExec({
            url: url,
            success: function (data) {
                if (data.status) {
                    // Reload data grid
                    $("#searchButton").click();
                } else {
                    $.displayError(data.message);
                }
                $("#item_id").select();
            }
        });
    }
}