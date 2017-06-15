var PrepareShipping = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        search_shipping_popup: null,
        sumWeightEnvelope: null,
        view_customs: null,
        edit_customs_url: null,
        save_declare_customs_url: null,
        request_export_customs_pdf_invoice: null,
        export_customs_pdf_invoice: null,
        view_customs_pdf_invoice: null,
        package_letter_size: null,
        get_package_letter_size: null,
        get_shipping_cost: null,
        get_stamp: null,
        buyEstampRequest: null,
        previewEstampRequest: null,
        preview_label_file: null,
        inputParcelsInfo: null,
        shipping_calculator: null,
        create_label: null,
        shipping_service_form: null,
        preview_fedex_file_url: null,
        getCustomerInfo: null,
        setPrePaymmentForShipment: null
    },

    /*
     * Paging configurations
     */
    configs: {
        rowNum: null,
        rowList: null
    },
    /*
     *  Modes
     */
    modes: {
        create: 'create', // Enter parcels' information
        edit: 'edit' // Modify parcels' information
    },
    sessionStorageItemKey: 'parcelsInfoData',
    last_special_service_fee: '',
    last_charge_for_shipment: '',
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        PrepareShipping.initAjaxUrls(baseUrl);
        PrepareShipping.initConfigs(rowNum, rowList);

        // init screen
        PrepareShipping.initScreen();

        PrepareShipping.previewShippingItem();
        
        // Load shipping service form
        PrepareShipping.loadShippingServiceForm();

        // Event listeners

        // View detail customs
        $(".view_detail_customs").die( "click" );
        $('.view_detail_customs').live('click', function () {
            PrepareShipping.viewCommentCustoms($(this).data('id'));
            return false;
        });
        
        $('#shippingEnvelopeForm_shipping_service_id').die('change');
        $('#shippingEnvelopeForm_shipping_service_id').live('change', function() {
            PrepareShipping.loadShippingServiceForm();
            $('#shippingLabelPreview').html('');
            return false;
        });

		$("#shipping_services_hidden").val($("#shipping_services").val());
		$('#shipping_services').live('change', function() {
			$("#shipping_services_hidden").val($("#shipping_services").val());
			var shipping_service_id = $('#shipping_services_hidden').val();
            var tracking_information_flag = mappingShippingServiceToNoTracking[shipping_service_id];
			if (tracking_information_flag == '0') {
				$('#no_tracking_number').attr("checked",true);
				//.todo_list_shipping_service_available >
				//.todo_tracking_number > 
				$('#shipping_services, #tracking_number').css({"background":"#ebebeb"}).attr("disabled",true);
			} else {
				$('#no_tracking_number').attr("checked",false);
				//.todo_list_shipping_service_available > 
				//.todo_tracking_number > 
				$('#shipping_services, #tracking_number').css({"background":"#ffffff"}).attr("disabled",false);
			}
            return false;
        });
        
        // View detail customs
        $('#shippingEnvelopeForm_profoma_invoice_open').die('click');
        $('#shippingEnvelopeForm_profoma_invoice_open').live('click', function () {
            PrepareShipping.editCommentCustoms($(this).data('id'));
            return false;
        });
        
        // View detail customs
        $('#shippingEnvelopeForm_profoma_invoice_print').die('click');
        $('#shippingEnvelopeForm_profoma_invoice_print').live('click', function () {
            PrepareShipping.viewPDF($(this).data('id'));
            return false;
        });

        // Change package letter size
        $('#package_letter_size').die('change');
        $('#package_letter_size').live('change', function (e) {
            PrepareShipping.changePackageLetterSize(this);
        });

        // When user click to select other package
        $('#shippingEnvelopeForm_other_package_price_flag').die('change');
        $("#shippingEnvelopeForm_other_package_price_fee").attr("disabled", "disabled");
        $('#shippingEnvelopeForm_other_package_price_flag').live('change', function () {
            PrepareShipping.changeOtherPackagePriceFlag(this);
        });

        // Get shipping cost.
        $('#shippingEnvelopeForm_other_package_price_fee, #shippingEnvelopeForm_special_service_fee').die('change');
        $('#shippingEnvelopeForm_other_package_price_fee, #shippingEnvelopeForm_special_service_fee').live('change', function () {
            if (PrepareShipping.last_special_service_fee != $('#shippingEnvelopeForm_special_service_fee').val()
                || PrepareShipping.last_charge_for_shipment != $('#shippingEnvelopeForm_other_package_price_fee').val()) {
                PrepareShipping.getShippingCost();
            }
        });
        $('#shippingEnvelopeForm_other_package_price_fee, #shippingEnvelopeForm_special_service_fee').die('keypress');
        $('#shippingEnvelopeForm_other_package_price_fee, #shippingEnvelopeForm_special_service_fee').live('keypress', function (e) {
             /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                if (PrepareShipping.last_special_service_fee != $('#shippingEnvelopeForm_special_service_fee').val()
                || PrepareShipping.last_charge_for_shipment != $('#shippingEnvelopeForm_other_package_price_fee').val()) {
                    PrepareShipping.getShippingCost();
                }
            }
        });

        // When user click to estamp checkbox
        $('#shippingEnvelopeForm_include_estamp').die('change');
        $('#shippingEnvelopeForm_include_estamp').live('change', function () {
            PrepareShipping.clickEstampCheckbox(this);
        });

        // User click preview estamp button
        $('#buyStampButton').die('click');
        $('#buyStampButton').live('click', function () {
            PrepareShipping.buyEstamp();
            return false;
        });

        // User click preview estamp button
        $('#createPreviewStampButton').die('click');
        $('#createPreviewStampButton').live('click', function () {
            PrepareShipping.clickPreviewEstampButton();
            return false;
        });

        // User click preview estamp button
        $('#lable_size').die('change');
        $('#lable_size').live('change', function () {
            PrepareShipping.changeLabelSize();
            return false;
        });
        
        // 6. Change customs dropdown list
        $('#shippingEnvelopeForm_customs_process_flag').die('change');
        $('#shippingEnvelopeForm_customs_process_flag').live('change', function() {
            PrepareShipping.changeCustomsDropdown();
            var shipping_service_id = $('#shippingEnvelopeForm_shipping_service_id').val();
            var shipping_service_template = PrepareShipping.getShippingServiceTemplate(shipping_service_id);
            if (shipping_service_template == '1') {
                PrepareShipping.getShippingCost();
            }
            // Fedex
            else if (shipping_service_template == '2') {
                ShippingCalculator.calculate();
            }
            return false;
        });
        
        // Click "Create Label" button
        $('#createPreviewLabelButton').die('click');
        $("#createPreviewLabelButton").live('click', function (e) {
            PrepareShipping.createLabel();
            return false;
        });
        
        // 6. Click the "Reset" button
        $('#resetButton').die('click');
        $("#resetButton").live('click', function (e) {
            PrepareShipping.loadShippingServiceForm();
            return false;
        });
        
        // Get shipping cost.
        $('#shippingEnvelopeForm_postal_charge').die('blur');
        $('#shippingEnvelopeForm_postal_charge').live('blur', function () {
            PrepareShipping.getShippingCost();
        });
        
        // init fedex stamp.
        var envelope_id = $("#shippingEnvelopeForm_envelope_id").val();
        var url = PrepareShipping.ajaxUrls.preview_fedex_file_url + '?envelope_id=' + envelope_id;
        $("#shippingLabelPreviewIframe").attr('src', url);
        
        $("#customer_info").live('click', function (e) {
            PrepareShipping.getCustomerInfo();
            return false;
        });
        
        $("#set_pre_payment").on('click', function (e) {
            PrepareShipping.setPrepaymentForShipment();
            return false;
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.search_shipping_popup = baseUrl + 'scans/todo/search_shipping_popup';
        this.ajaxUrls.sumWeightEnvelope = baseUrl + 'scans/todo/sumWeightEnvelope';
        this.ajaxUrls.view_customs = baseUrl + 'scans/todo/view_customs';
        this.ajaxUrls.edit_customs_url = baseUrl + 'scans/todo/edit_customs';
        this.ajaxUrls.request_export_customs_pdf_invoice = baseUrl + 'scans/completed/request_export_customs_pdf_invoice';
        this.ajaxUrls.export_customs_pdf_invoice = baseUrl + 'scans/completed/export_customs_pdf_invoice';
        this.ajaxUrls.view_customs_pdf_invoice = baseUrl + 'scans/completed/view_customs_pdf_invoice';
        this.ajaxUrls.package_letter_size = baseUrl + 'scans/todo/package_letter_size';
        this.ajaxUrls.get_package_letter_size = baseUrl + 'scans/todo/get_package_letter_size';
        this.ajaxUrls.get_shipping_cost = baseUrl + 'scans/todo/get_shipping_cost';
        this.ajaxUrls.get_stamp = baseUrl + 'scans/todo/get_stamp';
        this.ajaxUrls.buyEstampRequest = baseUrl + 'scans/todo/buyEstampRequest';
        this.ajaxUrls.previewEstampRequest = baseUrl + 'scans/todo/previewEstampRequest';
        this.ajaxUrls.preview_label_file = baseUrl + 'scans/todo/preview_label_file';
        this.ajaxUrls.inputParcelsInfo = baseUrl + 'info/input_parcels_info';
        this.ajaxUrls.shipping_calculator = baseUrl + 'info/shipping_calculator';
        this.ajaxUrls.create_label = baseUrl + 'scans/todo/create_label';
        this.ajaxUrls.shipping_service_form = baseUrl + 'scans/todo/shipping_service_form';
        this.ajaxUrls.preview_fedex_file_url = baseUrl + 'scans/todo/preview_fedex_file';
        this.ajaxUrls.save_declare_customs_url = baseUrl + 'scans/todo/save_declare_customs';
        this.ajaxUrls.getCustomerInfo = baseUrl + 'admin/customers/getCustomerInfo';
        this.ajaxUrls.setPrePaymmentForShipment = baseUrl + 'admin/customers/setPrePaymmentForShipment';
    },

    initConfigs: function (rowNum, rowList) {
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;
    },

    initScreen: function () {
        $('button').button();
        $('#buyStampButton').attr("disabled", "disabled");
        $('#createPreviewStampButton').attr("disabled", "disabled");
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('#display_pdf_invoice').fancybox({
            width: 900,
            height: 700,
            'onClosed': function () {
                $("#fancybox-inner").empty();
            }
        });
        $('#view_custom_file').fancybox({
            width: 1000,
            height: 800
        });
    },

    /**
     * Close scan window and load image
     */
    previewShippingItem: function () {
        // Load image
        var envelope_id = $('#envelope_ID').val();
        var customer_id = $('#to_ID').val();
        var postbox_id = $('#nextToDoForm_postbox_id').val();
        var package_id = $('#nextToDoForm_package_id').val();
//        var weight_unit = 'g';

        $("#popupShippingItemDataGridResult").jqGrid('GridUnload');
        $("#popupShippingItemDataGridResult").jqGrid({
            url: PrepareShipping.ajaxUrls.search_shipping_popup,
            postData: {
                customer_id: customer_id,
                postbox_id: postbox_id,
                envelope_id: envelope_id,
                package_id: package_id
            },
            datatype: "json",
            height: 130,
            footerrow: true,
            userDataOnFooter: true,
            width: 500,
            rowNum: PrepareShipping.configs.rowNum,
            rowList: PrepareShipping.configs.rowList,
            pager: "#popupShippingItemDataGridPager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            altRows: true,
            altclass: 'jq-background',
            captions: '',
            colNames: ['ID', 'Prepare Shipping Flag', 'Item No', 'Date Arrived', 'Size', 'Weight', '', 'L', 'W', 'H', '', 'Scan', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'prepare_shipping_flag', index: 'prepare_shipping_flag', hidden: true},
                {name: 'item_no', index: 'item_no', width: 90, sortable: false, align: 'center'},
                {name: 'date_arrived', index: 'date_arrived', sortable: false, width: 80, align: 'center'},
                {name: 'size', index: 'size', width: 50, sortable: false, align: 'center'},
                {name: 'weight_label', index: 'weight_label', sortable: false, width: 70, align: 'center'},
                {name: 'weight', index: 'weight', sortable: false, hidden: true,formatter:numFormat, unformat:numUnformat},
                {name: 'dimension_l', index: 'dimension_l', width: 40, sortable: false, align: 'center'},
                {name: 'dimension_w', index: 'dimension_w', width: 40, sortable: false, align: 'center'},
                {name: 'dimension_h', index: 'dimension_h', width: 40, sortable: false, align: 'center'},
                {name: 'item_scan_flag', index: 'item_scan_flag', sortable: false, hidden: true},
                {name: 'scan', index: 'scan', width: 70, sortable: false, align: 'left'},
                {
                    name: 'id',
                    index: 'id',
                    width: 60,
                    sortable: false,
                    align: 'center',
                    formatter: PrepareShipping.actionFormater
                }
            ],
            // When double click to row
            onSelectRow: function (row_id) {
                var currentRow = $("table#popupShippingItemDataGridResult > tbody > tr[id='" + row_id + "']");
                if (currentRow.attr('aria-selected') == 'true') {
                    currentRow.css('background-color', 'rgb(215, 247, 225)');
                } else {
                    currentRow.attr('style', '');
                }
            },
            loadComplete: function () {
                $.ajaxExec({
                    url: PrepareShipping.ajaxUrls.sumWeightEnvelope,
                    data: {
                        customer_id: customer_id,
                        package_id: package_id,
                        postbox_id: postbox_id,
                        envelope_id: envelope_id
                    },
                    success: function (obj) {
                        var sum_weight = obj.total_weight;
                        // $('#shippingEnvelopeForm_package_size').val(sum_weight);
                        $("#popupShippingItemDataGridResult").jqGrid('footerData', 'set', {
                            size: 'Total Weight:',
                            weight_label: sum_weight //+ ' ' + weight_unit
                        });
                    }
                });

                // Set all envelopes id
                var envelope_ids = $("#popupShippingItemDataGridResult").jqGrid('getDataIDs');
                $('#shippingEnvelopeForm_envelope_ids').val(envelope_ids);

                //PrepareShipping.getShippingForPackageSize();
                PrepareShipping.checkMarkedLines();
            }
        });
        function numFormat( cellvalue, options, rowObject ){
            return cellvalue.replace(".",",");
        }

        function numUnformat( cellvalue, options, rowObject ){
            return cellvalue.replace(",",".");
        }
    },
    createLabel: function(){
        var envelope_id = $("#shippingEnvelopeForm_envelope_id").val();
        if ($('#shipment_phone_number').val() == '') {
            $.displayError('Please input the phone number to request shipment.');
            return;
        }
        
        var total_insurance_value = $("#shippingEnvelopeForm_customs_insurance_value").val();
        if(total_insurance_value){
            total_insurance_value = total_insurance_value.replace(',', '.');
        }
        if ($('#shippingEnvelopeForm_weight').val() != $('#volumn_weight')) {
            $('#volumn_weight').val($('#shippingEnvelopeForm_weight').val());
        }
        var weight = $("#volumn_weight").val();
        if(weight){
            weight = weight.replace(',', '.');
        }
        var customs_process_flag = $('#shippingEnvelopeForm_customs_process_flag').val();
        $.ajaxExec({
            url: PrepareShipping.ajaxUrls.create_label,
            data: {
                customer_id: $("#shippingEnvelopeForm_customer_id").val(),
                envelope_id: $("#shippingEnvelopeForm_envelope_id").val(),
                postbox_id: $("#shippingEnvelopeForm_postbox_id").val(),
                package_id: $("#shippingEnvelopeForm_package_id").val(),
                shipment_service_id: $("#shippingCalculatorForm_shipment_service_id").val(),
                shipment_type_id: $("#shippingCalculatorForm_shipment_type_id").val(),
                customs_process_flag: customs_process_flag,
                total_insured_value: total_insurance_value,
                number_of_parcels: $("#shippingEnvelopeForm_number_of_parcels").val(),
                label_size: $("#lable_size").val(),
                length: $("#length").val(),
                width: $("#width").val(),
                height: $("#height").val(),
                volumn_weight: weight,
                multiple_quantity: $("#multiple_quantity").val(),
                multiple_number_shipment: $("#multiple_number_shipment").val(),
                multiple_length: $("#multiple_length").val(),
                multiple_width: $("#multiple_width").val(),
                multiple_height: $("#multiple_height").val(),
                multiple_weight: $("#multiple_weight").val(),
                shipment_address_name: $("#shipment_address_name").val(),
                shipment_company: $("#shipment_company").val(),
                shipment_street: $("#shipment_street").val(),
                shipment_postcode: $("#shipment_postcode").val(),
                shipment_city: $("#shipment_city").val(),
                shipment_region: $("#shipment_region").val(),
                shipment_country: $("#shipment_country").val(),
                shipment_phone_number: $('#shipment_phone_number').val()
            },
            success: function (response) {
                if (response.status) {
                    // show pdf to left panel.
                    $("#shippingEnvelopeForm_tracking_number").val(response.data.tracking_number);
                    var url = PrepareShipping.ajaxUrls.preview_fedex_file_url + '?envelope_id=' + envelope_id;
                    $('#shippingLabelPreview').html("<iframe id='shippingLabelPreviewIframe' name='shippingLabelPreviewIframe' src='" + url + "' style='width: 100%; height: 100%; border: none'><iframe>");
                    //$("#shippingLabelPreviewIframe").attr('src', url);
                } else {
                    $.displayError(response.message);
                }
            }
        });
    },
    /**
     * Action format for view detail customs
     */
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            return '<a href="#" class="view_detail_customs" data-id="' + rowObject[0] + '" title="View Customs">CUSTOM</a>';
        } else {
            return '';
        }
    },

    checkMarkedLines: function () {
        var prepare_shipping_flag = null;
        var rows = $("table#popupShippingItemDataGridResult > tbody > tr[id]");
        $.each(rows, function (index, row) {
            prepare_shipping_flag = $(row).find('td[aria-describedby="popupShippingItemDataGridResult_prepare_shipping_flag"]').first().attr('title');
            if (prepare_shipping_flag == '1') {
                $(row).css('background-color', 'rgb(215, 247, 225)');
                $(row).attr('aria-selected', "true");
                $(row).find('input.cbox').prop('checked', true);
            }
        });
    },

    load_printer_name: function () {
        DWObject = document.getElementById(DW_ObjectName);
        console.log(DWObject);
        // If source list need to be displayed, fill in the source items.
        if (DW_DWTSourceContainerID != "") {
            document.getElementById(DW_DWTSourceContainerID).options.length = 0;
            DWObject.OpenSourceManager();
            for (var i = 0; i < DWObject.SourceCount; i++) {
                document.getElementById('select_printer').options.add(new Option(DWObject.SourceNameItems(i), i));
            }
        }
    },

    viewCommentCustoms: function (envelope_id) {
        $('#viewCustomsDetail').html("");
        
        // Open popup allow customer declare customs information
        var submitUrl = PrepareShipping.ajaxUrls.view_customs + '?envelope_id=' + envelope_id + '&t='+Date.now();
        // Open new dialog
        $('#viewCustomsDetail').openDialog({
            autoOpen: false,
            height: 490,
            width: 900,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(submitUrl, function () {
                });
            },
            buttons: {
                'View PDF': function () {
                    PrepareShipping.viewPDF(envelope_id);
                },
                'Close': function () {
                    $(this).dialog('destroy');
                }
            }
        });

        $('#viewCustomsDetail').dialog('option', 'position', 'center');
        $('#viewCustomsDetail').dialog('open');
    },
    
    editCommentCustoms: function (envelope_id) {
        $('#viewCustomsDetail').html("");
        
        // Open popup allow customer declare customs information
        var submitUrl = PrepareShipping.ajaxUrls.edit_customs_url + '?envelope_id=' + envelope_id + '&t='+Date.now();
        // Open new dialog
        $('#viewCustomsDetail').openDialog({
            autoOpen: false,
            height: 550,
            width: 900,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(submitUrl, function () {
                });
            },
            buttons: {
                'Save custom': function () {
                    PrepareShipping.saveDeclareCustom(envelope_id);
                },
                'View PDF': function () {
                    PrepareShipping.viewPDF(envelope_id);
                },
                'Close': function () {
                    $(this).dialog('destroy');
                }
            }
        });

        $('#viewCustomsDetail').dialog('option', 'position', 'center');
        $('#viewCustomsDetail').dialog('open');
    },
    
    validateData: function() {
        var gridData = $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('getGridParam','data');
        var submitData = [];
        var lengthData = gridData.length;
        for (var i=0; i < lengthData; i++) {
            var data_row = gridData[i];
            if (data_row.material_name != '') {
                var valid_data = PrepareShipping.validateDataRow(data_row, i+1);
                if (valid_data) {
                    submitData.push(data_row);
                } else {
                    $.displayError('Data row ' + (i+1) + ' is invalid. Please correct it before submit.');
                    return '';
                }
            }
        }
        if (submitData.length == 0) {
            $.displayError('Please declare customs information.');
            return '';
        }
        return submitData;
    },

    validateDataRow: function(data_row, row_index) {
        var row_error = false;
        var column_error = false;
        // For each data column

        var selected_column = ["material_name", "quantity", "cost"];
        var meta_data_column = {
                material_name: {data_type: 'string', allow_null: false, max_length: 255, display_name: 'Material name'},
                quantity: {data_type: 'integer', allow_null: false, max_length: 0, display_name: 'Quantity'},
                cost: {data_type: 'double', allow_null: false, max_length: 0, display_name: 'Cost'}
        };
        $.each (selected_column, function(i, column) {
            var data_type = meta_data_column[column].data_type;
            var allow_null = meta_data_column[column].allow_null;
            var max_length = meta_data_column[column].max_length;
            var cell_value = data_row[column];
            column_error = false;

            // Validate required
            if (!allow_null) {
                if ($.isEmpty(cell_value)) {
                    // Log message
                    row_error = true;
                    column_error = true;

                    // Highlight cell color
                    PrepareShipping.highlightError(row_index, column);
                }
            }

            // Validate data type
            if ($.isNotEmpty(cell_value)) {
                if (data_type == "integer") {
                    if (!$.isValidInt(cell_value)) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        PrepareShipping.highlightError(row_index, column);
                    }
                } else if (data_type == "double") {
                    if (!$.isValidNumber(cell_value)) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        PrepareShipping.highlightError(row_index, column);
                    }
                }
            }

            // Validate max length
            if ($.isNotEmpty(cell_value) && max_length > 0) {
               if (data_type == "string") {
                   if (cell_value.length > max_length) {
                        // Log message
                        row_error = true;
                        column_error = true;

                        // Highlight cell color
                        PrepareShipping.highlightError(row_index, column);
                   }
               }
            }

            // Remove cell hightlight if cell ok
            if (!column_error) {
                    $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('setCell', row_index, column,"",{color:'#000'});
            } else {
                    $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('setCell', row_index, column,"",{color:'red'});
            }
        });

        // Remove highlight if no error occur
        if (!row_error) {
            PrepareShipping.removeHighlightError(row_index, '');
        } else {
            PrepareShipping.highlightError(row_index, '');
        }
        return !row_error;
    },
    
    highlightError: function (row_id, column_name) {
        $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('setCell', row_id, column_name,"",{color:'red'});
        $('#' + (row_id)).addClass('ui-state-error');
    },
    
    removeHighlightError: function (row_id, column_name) {
        $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('setCell', row_id , column_name,"",{color:'#000'});
        $('#' + (row_id )).removeClass('ui-state-error');
    },
    
    saveDeclareCustom: function(envelope_id){
        var submitData = PrepareShipping.validateData();
       
        var customData = JSON.stringify(submitData);
        $.ajaxExec({
            url: this.ajaxUrls.save_declare_customs_url,
            data: {customs_data: customData, envelope_id: envelope_id},
            success: function(data) {
                if (data.status) {
                    // calculate total cost.
                    var total_cost = 0;
                    for(i=0; i< submitData.length; i++){
                        total_cost += submitData[i].cost * submitData[i].quantity;
                    }
                    
                    var estimate_cost_custom = 0;
                    if(total_cost > 1000){
                        estimate_cost_custom = $("#declareCustomForm_custom_outgoing_01").val();
                    }else if(total_cost > 0 && total_cost <= 1000){
                        estimate_cost_custom = $("#declareCustomForm_custom_outgoing_02").val();
                    }
                    
                    $("#shippingEnvelopeForm_charge_customs_process").val(estimate_cost_custom);
                    
                    $("#shippingEnvelopeForm_customs_insurance_value").val(total_cost);
                    $("#insurance_customs_cost").val(total_cost);
                    PrepareShipping.getShippingCost();
                    
                    $('#viewCustomsDetail').dialog('destroy');
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
    changePackageLetterSize: function (target) {
        // Get current ppl
        var package_price = $(target).attr('data-package-price');
        $('#shippingEnvelopeForm_package_price_id').val(package_price);
        var package_size = $(target).attr('data-package-size');
        $('#shippingEnvelopeForm_package_size').val(package_size);
    },

    /**
     * View PDF file. Generate PDF first and open it in fancybox
     */
    viewPDF: function (envelope_id) {
        var trackingNumber = $("#shippingEnvelopeForm_tracking_number").val();
        var numberOfParcel = $('#shippingEnvelopeForm_number_of_parcels').val();
        var message = "This shipment is split into more than one shipments. To get the information for each shipment included in the proforma invoices please first create the shipping label before you print out the proforma invoices.";
        if (trackingNumber == '' && numberOfParcel > 1) {
            $.displayError(message);
            return false;
        }
        var submitUrl = PrepareShipping.ajaxUrls.request_export_customs_pdf_invoice + '?view=1&envelope_id=' + envelope_id+ '&t='+Date.now();
        
        $('#view_custom_file').attr('href', submitUrl);
        $('#view_custom_file').click();
        return false;
    },

    /**
     * Generate customs invoice PDF.
     */
    createPDF: function (envelope_id) {
        var submitUrl = PrepareShipping.ajaxUrls.request_export_customs_pdf_invoice + '?envelope_id=' + envelope_id+ '&t='+Date.now();
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                $.displayInfor(data.message, null, function () {
                    // Reload data grid
                    $('#viewCustomsDetail').dialog('close');
                });
            }
        });
    },

    openPackageLetterSize: function () {
        // Open new dialog
        $('#packageLetterSizeWindow').openDialog({
            autoOpen: false,
            height: 470,
            width: 570,
            modal: true,
            open: function () {
                $(this).load(PrepareShipping.ajaxUrls.package_letter_size, function () {
                });
            },
            buttons: {
                'Select': function () {
                    PrepareShipping.selectPackageLetterSize();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $('#packageLetterSizeWindow').dialog('option', 'position', 'center');
        $('#packageLetterSizeWindow').dialog('open');
    },

    /**
     * Get default shipping package/letter size
     */
    getShippingForPackageSize: function () {
        var weight = $('#shippingEnvelopeForm_package_size').val();
        var shipping_package_url = PrepareShipping.ajaxUrls.get_package_letter_size + '?weight=' + weight;
        $.ajaxExec({
            url: shipping_package_url,
            success: function (data) {
                // $('#shippingEnvelopeForm_package_price_id').val(data.package_id);
                // $('#package_letter_size').val(data.package_text);
            }
        });
    },

    /**
     * Process when user click select button
     */
    selectPackageLetterSize: function () {
        var row_id = $('#select_package_letter_size_id').val();
        if ($.isEmpty(row_id)) {
            $.displayError('Please select package letter size.');
            return;
        }

        // $('#shippingEnvelopeForm_package_price_id').val(row_id);
        var data_row = $('#packageLetterSizeDataGridResult').jqGrid("getRowData", row_id);
        var package_text = data_row.name + ', ' + data_row.weight + ', ' + data_row.size + ', ' + data_row.price;
        $('#package_letter_size').val(package_text);
        $('#packageLetterSizeWindow').dialog('close');
    },

    changeOtherPackagePriceFlag: function (elem) {
        if (elem.checked) {
            $('#shippingEnvelopeForm_include_estamp').prop("checked", false);
            $("#shippingEnvelopeForm_other_package_price_fee").removeAttr("disabled");
            $('#buyStampButton').attr("disabled", "disabled");
            $('#createPreviewStampButton').attr("disabled", "disabled");
            $('#shippingLabelPreview').html('');
        } else {
            $("#shippingEnvelopeForm_other_package_price_fee").attr("disabled", "disabled");
        }
    },

    getShippingCost: function () {
        var customer_id = $('#shippingEnvelopeForm_customer_id').val();
        var envelope_id = $('#shippingEnvelopeForm_envelope_id').val();
        var shipping_service_id = $('#shippingEnvelopeForm_shipping_service_id').val();
        var shipping_service_template = PrepareShipping.getShippingServiceTemplate(shipping_service_id);
        var customs_process_flag = $('#shippingEnvelopeForm_customs_process_flag').val();
        var shipping_fee = 0;
        var insurance_customs_cost = 0;
        var special_service_fee = 0;
        if (shipping_service_template == '1') {
            shipping_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
            special_service_fee = $('#shippingEnvelopeForm_special_service_fee').val();
            special_service_fee = special_service_fee == '' ? 0: special_service_fee;
            shipping_fee = shipping_fee == '' ? 0: shipping_fee;
            PrepareShipping.last_special_service_fee = $('#shippingEnvelopeForm_special_service_fee').val();
            PrepareShipping.last_charge_for_shipment = $('#shippingEnvelopeForm_other_package_price_fee').val();
        } else if (shipping_service_template == '2') {
            shipping_fee = $('#shippingEnvelopeForm_postal_charge').val();
            insurance_customs_cost = $('#shippingEnvelopeForm_customs_insurance_value').val();
        } else if (shipping_service_template == '3') {
            shipping_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
        }
        $.ajaxExec({
            url: PrepareShipping.ajaxUrls.get_shipping_cost,
            data: {
                customer_id: customer_id,
                envelope_id: envelope_id,
                shipping_fee: shipping_fee,
                special_service_fee: special_service_fee,
                customs_process_flag: customs_process_flag,
                insurance_customs_cost: insurance_customs_cost
            },
            success: function (obj) {
                if (shipping_service_template == '1') {
                    $('#shippingEnvelopeForm_charge_customs_process').val(obj.customs_handling_net);
                    $('#shippingEnvelopeForm_charge_customs_process_gross').val(obj.customs_handling_gross);
                    
                    //$('#shippingEnvelopeForm_special_service_fee').val(obj.special_service_fee_net);
                    $('#shippingEnvelopeForm_special_service_fee_gross').val(obj.special_service_fee_gross);
                    
                    //$('#shippingEnvelopeForm_other_package_price_fee').val(obj.charge_for_shipment_net);
                    $('#shippingEnvelopeForm_other_package_price_fee_gross').val(obj.charge_for_shipment_gross);
                    
                    $('#shippingEnvelopeForm_handling_charge').val(obj.handling_charge_net);
                    $('#shippingEnvelopeForm_handling_charge_gross').val(obj.handling_charge_gross);
                    
                    $('#shippingEnvelopeForm_total_shipment_charge').val(obj.total_shipping_cost_net);
                    $('#shippingEnvelopeForm_total_shipment_charge_gross').val(obj.total_shipping_cost_gross);
                    
                } else if (shipping_service_template == '2') {
                    $('#shippingEnvelopeForm_cost_for_customer_charge').val(obj.data);
                    $('#shippingEnvelopeForm_handling_charge').val(obj.handling_charge_net);
                    $('#shippingEnvelopeForm_handling_charge_gross').val(obj.handling_charge_gross);
                } else if (shipping_service_template == '3') {
                    $('#shippingEnvelopeForm_cost_for_customer').val(obj.data);
                    $('#shippingEnvelopeForm_handling_charge').val(obj.handling_charge_net);
                    $('#shippingEnvelopeForm_handling_charge_gross').val(obj.handling_charge_gross);
                }

                // Display VAT number
                $('#shippingDisplayVAT').html(obj.vat);
               
                $("#loading-icon").css("display", "none");
                $("#shippingEnvelopeForm_shippingServiceForm").css("display", "block");
            }
        });
    },

    clickEstampCheckbox: function (elem) {
        if (elem.checked) {
            $('#buyStampButton').removeAttr("disabled");
            $('#createPreviewStampButton').removeAttr("disabled");
            $('#shippingEnvelopeForm_other_package_price_flag').prop("checked", false);
            $("#shippingEnvelopeForm_other_package_price_fee").attr("disabled", "disabled");
        } else {
            $('#buyStampButton').attr("disabled", "disabled");
            $('#createPreviewStampButton').attr("disabled", "disabled");
        }
    },

    /**
     * Get estamp
     */
    get_estamp: function () {
        var package_price = $('#shippingEnvelopeForm_package_price_id').val();
        var ppl = $('#package_letter_size').val();
        var submitUrl = PrepareShipping.ajaxUrls.get_stamp + '?ppl=' + ppl + '&package_price=' + package_price;

        if ($('#shippingEnvelopeForm_include_estamp_img').attr('src') == '') {
            $.ajaxExec({
                url: submitUrl,
                success: function (data) {
                    $('#shippingEnvelopeForm_estamp_url').val(data.message);
                    $('#shippingEnvelopeForm_include_estamp_img').attr('src', data.message);
                    $('#estamp_container').show();
                }
            });
        }
    },

    /**
     * User click buy estamp button
     */
    buyEstamp: function () {
        var customer_id = $('#to_ID').val();
        var submitUrl = PrepareShipping.ajaxUrls.buyEstampRequest;
        $('#shippingEnvelopeForm_current_view_type').val('2');

        submitUrl += '?customer_id=' + customer_id;
        submitUrl += '&shipment_address_name=' + encodeURIComponent($('#shipment_address_name').val());
        submitUrl += '&shipment_company=' + encodeURIComponent($('#shipment_company').val());
        submitUrl += '&shipment_postcode=' + encodeURIComponent($('#shipment_postcode').val());
        submitUrl += '&shipment_street=' + encodeURIComponent($('#shipment_street').val());
        submitUrl += '&shipment_city=' + encodeURIComponent($('#shipment_city').val());
        submitUrl += '&shipment_country=' + encodeURIComponent($('#shipment_country option:selected').text());
        submitUrl += '&shipping_type=' + encodeURIComponent($('#lable_size').val());

        var package_price = $('#shippingEnvelopeForm_package_price_id').val();
        var ppl = $('#package_letter_size').val();
        submitUrl += '&ppl=' + ppl;
        submitUrl += '&package_price=' + package_price;

        // Submit envelope_id and package_id to get unique estamp
        var envelope_id = $('#envelope_ID').val();
        var package_id = $('#nextToDoForm_package_id').val();
        submitUrl += '&envelope_id=' + envelope_id;
        submitUrl += '&package_id=' + package_id;

        // Send request to get estamp button
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                PrepareShipping.previewEstamp(data.message);
            }
        });
    },

    clickPreviewEstampButton: function () {
        var customer_id = $('#to_ID').val();
        var submitUrl = PrepareShipping.ajaxUrls.previewEstampRequest;
        $('#shippingEnvelopeForm_current_view_type').val('1');

        submitUrl += '?customer_id=' + customer_id;
        submitUrl += '&shipment_address_name=' + encodeURIComponent($('#shipment_address_name').val());
        submitUrl += '&shipment_company=' + encodeURIComponent($('#shipment_company').val());
        submitUrl += '&shipment_postcode=' + encodeURIComponent($('#shipment_postcode').val());
        submitUrl += '&shipment_city=' + encodeURIComponent($('#shipment_city').val());
        submitUrl += '&shipment_street=' + encodeURIComponent($('#shipment_street').val());
        submitUrl += '&shipment_country=' + encodeURIComponent($('#shipment_country option:selected').text());
        submitUrl += '&shipping_type=' + encodeURIComponent($('#lable_size').val());

        var envelope_id = $('#envelope_ID').val();
        var package_id = $('#nextToDoForm_package_id').val();
        submitUrl += '&envelope_id=' + envelope_id;
        submitUrl += '&package_id=' + package_id;

        // Send request to get estamp button
        $.ajaxExec({
            url: submitUrl,
            success: function (data) {
                PrepareShipping.previewEstamp(data.message);
            }
        });
    },

    /**
     * Close scan window and load image
     */
    previewEstamp: function (filePath) {
        printLabelEnable = true;
        // Load image
        var preview_url = PrepareShipping.ajaxUrls.preview_label_file + '?filePath=' + filePath;
        $('#shippingLabelPreview').html("<iframe id='previewEstamp_iframe' name='previewEstamp_iframe' src='" + preview_url + "' style='height:210px; width:100%'><iframe>");
    },

    changeLabelSize: function () {
        // 1: Preview type | 2: Buy stamp
        var current_view_type = $('#shippingEnvelopeForm_current_view_type').val();
        if (current_view_type == 1) {
            $('#createPreviewStampButton').click();
        } else {
            $('#buyStampButton').click();
        }
    },
    getShippingServiceTemplate: function(shipping_service_id) {
        return mappingShippingServiceToTemplate[shipping_service_id];
    },
    loadShippingServiceForm: function() {
        var customer_id = $('#shippingEnvelopeForm_customer_id').val();
        var envelope_id = $('#shippingEnvelopeForm_envelope_id').val();
        var shipping_service_id = $('#shippingEnvelopeForm_shipping_service_id').val();
        var shipping_service_template = PrepareShipping.getShippingServiceTemplate(shipping_service_id);
        var shipment_type_id = $('#shippingEnvelopeForm_shipment_type').val();
        var tracking_information_flag = mappingShippingServiceToNoTracking[shipping_service_id];
        if (tracking_information_flag == '0') {
            $('#shipping_service_no_tracking').show();
			$('#no_tracking_number').attr("checked",true);
			$('#shipping_services, #tracking_number').css({"background":"#ebebeb"}).attr("disabled",true);
        } else {
            $('#shipping_service_no_tracking').hide();
			$('#no_tracking_number').attr("checked",false);
			$('#shipping_services, #tracking_number').css({"background":"#ffffff"}).attr("disabled",false);
        }
        
        var submitUrl = PrepareShipping.ajaxUrls.shipping_service_form;
        submitUrl += '?customer_id=' + customer_id;
        submitUrl += '&envelope_id=' + envelope_id;
        submitUrl += '&shipping_service_id=' + shipping_service_id;
        submitUrl += '&shipment_type_id=' + shipment_type_id;
        submitUrl += '&shipping_service_template=' + shipping_service_template;
        
        // Load form
        $("#input_parcels_info_form").remove();
        $('#shippingEnvelopeForm_shippingServiceForm').html('');
        $('#shippingEnvelopeForm_shippingServiceForm').load(submitUrl, function(){
            var value = $("#shippingEnvelopeForm_customs_insurance_value").val();
            if(value != null){
                $("#shippingEnvelopeForm_customs_insurance_value").val(value.replace('.', ''));
            }
            
            value = $("#shippingEnvelopeForm_weight").val();
            if(value){
                $("#shippingEnvelopeForm_weight").val(value.replace('.', ''));
            }
        });
    },
    
    changeCustomsDropdown: function() {
        var selected_customs_flag = $('#shippingEnvelopeForm_customs_process_flag').val();
        var customs_handling = $('#shippingEnvelopeForm_customs_handling').val();
        if (selected_customs_flag == '1') {
            $('#shippingEnvelopeForm_profoma_invoice_container').show();
            if (customs_handling != '0,00') {
                $('#shippingEnvelopeForm_charge_customs_process').val(customs_handling);
            }
            $("#shippingEnvelopeForm_charge_customs_process").removeAttr("readonly");
        } else {
            $('#shippingEnvelopeForm_profoma_invoice_container').hide();
            $('#shippingEnvelopeForm_charge_customs_process').val('');
            $("#shippingEnvelopeForm_charge_customs_process").attr("readonly", "readonly");
        }
    },
    
    getCustomerInfo: function(){
        
        var customer_id = $("#shippingEnvelopeForm_customer_id").val();
        var url = PrepareShipping.ajaxUrls.getCustomerInfo + '?customer_id=' + customer_id;    
        $('#windowCustomerInfo').openDialog({
            autoOpen: false,
            height: 320,
            width: 600,
            modal: true,
            closeOnEscape: false,
            open: function () {
                 $(this).load(url, function () {});
            },
            buttons: {
                'Close': function () {
                    $(this).dialog('destroy');
                }
            }
        });
        $('#windowCustomerInfo').dialog('option', 'position', 'center');
        $('#windowCustomerInfo').dialog('open');
    },
    setPrepaymentForShipment: function(){
        $.ajaxExec({
            url: PrepareShipping.ajaxUrls.setPrePaymmentForShipment,
            data: {
                envelope_id: $("#shippingEnvelopeForm_envelope_id").val()
            },
            success: function (response) {
                if (response.status) {
                    $.infor({
                        message: response.message
                    });
                } else {
                    $.displayError(response.message);
                }
            }
        });
    }
    
       
}