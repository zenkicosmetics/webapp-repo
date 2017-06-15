var TodoList = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        searchToDoList: null,
        todo: null,
        scan: null,
        updateRemarkedFlag: null,
        previewScanImage: null,
        checkScanPending: null,
        shipping: null,
        checkShipping: null,
        getStamp: null,
        completed: null,
        searchShipping: null,
        commentDetail: null,
        saveMarkedLines: null
    },

    /*
     * Paging configurations
     */
    configs: {
        rowNum: null,
        rowList: null
    },

    /*
     * Shipping types
     */
    shippingTypes: {
//        DIRECT_SHIPPING: '1',
//        COLLECT_SHIPPING: '2'
    	 DIRECT_SHIPPING: '3',
    	 COLLECT_SHIPPING: '4'
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        TodoList.initAjaxUrls(baseUrl);
        TodoList.configs.rowNum = rowNum;
        TodoList.configs.rowList = rowList;

        // init screen
        TodoList.initScreen();

        // Call search method
        TodoList.searchToDoList();

        // Event listeners

        // Change location id
        $('#location_id').live('change', function () {
            TodoList.searchToDoList();
        });

        // When user click to radio button (deprecated)
        $('.managetables-selectitem').on('click', function () {
            TodoList.selectItemToScan($(this).val());
        });

        // When user click shipping envelope button
        $('#shippingEnvelopeButton').click(function () {
            TodoList.checkScanPending();
        });

        // Process when user click mark completed button.
        $('#markCompletedButton').click(function () {
            var row_id = $('#nextToDoForm_current_row_id').val();
            var current_scan_type = $('#current_scan_type').val();
            TodoList.markCompleted(row_id, current_scan_type);
        });

        // Process when user click scan envelope.
        $('#dynaScanLink').on('click', function () {
            TodoList.scanEnvelop();
            return false;
        });

        // Click to input text file.
        $('#imagepath_banner_input').on('click', function () {
            //TodoList.uploadFile();
        });

        // Process when user click add button to add incoming envelope
        $('#scanEnvelopeButton').click(function () {
            TodoList.scanIncomingEnvelop();
            return false;
        });

        // Process when user click add button to add incoming envelope
        $('#scanItemButton').click(function () {
            TodoList.scanIncomingItem();
            return false;
        });
        
        // Access the customer site
        $('.access_customer_site').live('click', function () {
            var customer_id = $(this).attr('data-id');
            $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
            $('#hiddenAccessCustomerSiteForm').submit();
        });

        // View & Save comment detail
        $('.view_envelope_comment_detail').live('click', function () {
            TodoList.viewCommentDetail(this);
            return false;
        });
        
        // View & Delete comment detail
        $('.del_envelope_comment_detail').live('click', function () {
            TodoList.delCommentDetail(this);
            return false;
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.searchToDoList = baseUrl + 'scans/todo/search';
        this.ajaxUrls.todo = baseUrl + 'scans/todo';
        this.ajaxUrls.scan = baseUrl + 'scans/todo/scan';
        this.ajaxUrls.updateRemarkedFlag = baseUrl + 'scans/todo/update_remarked_flag';
        this.ajaxUrls.previewScanImage = baseUrl + 'scans/todo/preview_image';
        this.ajaxUrls.checkScanPending = baseUrl + 'scans/todo/shipping_check_scan_pending';
        this.ajaxUrls.shipping = baseUrl + 'scans/todo/shipping';
        this.ajaxUrls.checkShipping = baseUrl + 'scans/todo/shipping_check';
        this.ajaxUrls.getStamp = baseUrl + '/scans/todo/get_stamp';
        this.ajaxUrls.completed = baseUrl + 'scans/todo/completed';
        this.ajaxUrls.searchShipping = baseUrl + 'scans/todo/search_shipping';
        this.ajaxUrls.commentDetail = baseUrl + 'scans/todo/comment_detail';
        this.ajaxUrls.saveMarkedLines = baseUrl + 'scans/todo/save_marked_items_for_shipping';
        this.ajaxUrls.list_shipping_service_available = baseUrl + 'scans/todo/get_list_shipping_service_available';
        this.ajaxUrls.get_info_item = baseUrl + 'scans/todo/get_info_item';
        this.ajaxUrls.save_tracking_number = baseUrl + 'scans/todo/save_tracking_number';
        
    },

    initScreen: function () {
        $('#display_document_full').fancybox({
            width: 1100,
            height: 800
        });
        $('#display_envelope_full').fancybox({
            width: 1100,
            height: 800
        });
        // var scan_url = $('#dynaScanLink').attr('href');
        // $('#scanEnvelopeWindow').load(scan_url, function() {});
        $('.only_for_scan_item').hide();

        // Apply checkbox style
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '30px');
    },

    searchToDoList: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        var tableH = $.getTableHeight() + 3;

        $("#dataGridResult").jqGrid({
            url: TodoList.ajaxUrls.searchToDoList,
            datatype: "json",
            postData: {location_available_id: $("#location_id").val()},
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: TodoList.configs.rowNum,
            rowList: TodoList.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'incomming_date,to_customer_id',
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: true,
            multiSort: true,
            altRows: true,
            multiselect: false,
            multiselectWidth: 40,
            altclass: 'jq-background',
            captions: '',
            colNames: ['ID', 'Envelope ID', 'From', '', '', 'To', '', 'Type', 'Weight', 'CategoryType', 'Category', 'Account Status', 'Verified', '', 'Date and Time', 'Duration', '', 'Open balance due', 'Open balance this month', 'Activity', 'Fraud', 'Comment', '', '', '', '', '', '', '', '', '', '','Tracking Number Flags'],
            colModel: [
                {name: 'id', index: 'id', width: 50, hidden: true},
                {name: 'envelope_code', index: 'envelope_code', width: 200,sortable: true, formatter: TodoList.toCustomerFormater02},
                {name: 'from_customer_name', index: 'from_customer_name',sortable: true, width: 100},
                {name: 'to_customer_id_h', index: 'to_customer_id_h', hidden: true},
                {name: 'to_customer_id_view', index: 'to_customer_id_view', hidden: true},
                {name: 'to_customer_id', index: 'to_customer_id', width: 120,sortable: true, formatter: TodoList.toCustomerFormater},
                {name: 'type_id', index: 'type_id', hidden: true},
                {name: 'envelope_type_id', index: 'envelope_type_id',sortable: true, width: 60},
                {name: 'weight', index: 'weight', width: 60,sortable: true, align: "right"},
                {name: 'category_type', index: 'category_type', hidden: true},
                {name: 'category', index: 'category', sortable: false, hidden: true, width: 100},
                {name: 'account_status', index: 'account_status', width: 60, sortable: false, align: "center"},
                {name: 'account_verified', index: 'account_verified',sortable: false, width: 50, align: "center"},
                {name: 'invoice_flag', index: 'invoice_flag', width: 75, hidden: true},
                {name: 'last_updated_date', index: 'last_updated_date',sortable: true, width: 115},
                {name: 'registration_month', index: 'registration_month', width: 50,sortable: true},
                {name: 'row_id', index: 'row_id', hidden: true},
                {name: 'open_balance_due', index: 'open_balance_due', sortable: false, width: 140},
                {name: 'curr_open_balance', index: 'curr_open_balance', sortable: false, width: 150},
                {name: 'activity', index: 'activity', sortable: true, width: 80},
                {name: 'remarked_flag', index: 'remarked_flag', sortable: true, width: 40, formatter: TodoList.flagFormater},
                {name: 'comment', index: 'comment', sortable: true, width: 70, align: "center", formatter: TodoList.commentFormater},                                                                                                                                                                                                                                                                            //#1297 check all tables in the system to minimize wasted space },
                {name: 'envelope_scan_flag', index: 'envelope_scan_flag', hidden: true},
                {name: 'item_scan_flag', index: 'item_scan_flag', hidden: true},
                {name: 'direct_shipping_flag', index: 'direct_shipping_flag', hidden: true},
                {name: 'collect_shipping_flag', index: 'collect_shipping_flag', hidden: true},
                {name: 'trash_flag', index: 'trash_flag', hidden: true},
                {name: 'package_id', index: 'package_id', hidden: true},
                {name: 'postbox_id', index: 'postbox_id', hidden: true},
                {name: 'completed_flag', index: 'completed_flag', hidden: true},
                {name: 'status', index: 'status', hidden: true},
                {name: 'incomming_date', index: 'incomming_date', hidden: true},
                {name: 'tracking_number_flag', index: 'tracking_number_flag', hidden: true}
            ],

            // When double click to row
            onSelectRow: function (row_id) {
                
                $("#tracking_number").val("");
                $("#shipping_services").val(0);

                var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                var scan_url = TodoList.ajaxUrls.scan + '?envelope_id=' + row_id + '&customer_id=' + data_row.to_customer_id_h;
                $(".wrap_tracking_number").hide();
                if((data_row.collect_shipping_flag == '1' )){
                    $(".wrap_tracking_number").css({"margin-left":"20px"});
                }
                else {
                    $(".wrap_tracking_number").css({"margin-left":"0px"});
                }

                $("#scanButtonContainerSub").css({"margin-top":"0px"});
                /* Admin had markcompleted but not yet fill tracking number */
                if ( ( (data_row.direct_shipping_flag == '1') || (data_row.collect_shipping_flag == '1' )  && (data_row.tracking_number_flag == '0'))  && ( (data_row.envelope_scan_flag != '0') && (data_row.item_scan_flag != '0') ) ) {
                    $("#detail_item_was_forward").html("");
                    $(".tracking_disable").attr("disabled", true);
                    $.ajax({
                      
                      url: TodoList.ajaxUrls.get_info_item,
                      type: 'POST',
                      dataType: 'json',
                      data: {envelope_id: row_id}

                    }).done(function ( data ) {
                        $("#detail_item_was_forward").text("Item was forwarded: "+data.shipping_date+"  by "+data.completed_by+" ");
                    });

                    $("#tr_no_tracking_number").hide();
                    $(".item_was_forward").show();
                    $(".no_save_tracking").show();

                    $(".wrap_tracking_number").show();
                    
                    $.ajax({
                      url: TodoList.ajaxUrls.list_shipping_service_available,
                      type: 'POST',
                      dataType: 'html',
                      data: {envelope_id: row_id},
                    }).done(function ( data ) {
                       
                        $("#list_shipping_service_available").html(data);
                        $(".tracking_disable").attr("disabled",false);
                    });
                    
                }
                else if ( ( (data_row.direct_shipping_flag == '0') || (data_row.collect_shipping_flag == '0' ) ) && ( (data_row.envelope_scan_flag != '0') && (data_row.item_scan_flag != '0') ) ) {
                    $("#scanButtonContainerSub").css({"margin-top":"110px"});

                    $(".item_was_forward").hide();
                    $(".no_save_tracking").hide();
                    $("#tr_no_tracking_number").show();
                    $(".tracking_disable").attr("disabled", true);
                    $.ajax({
                      url: TodoList.ajaxUrls.list_shipping_service_available,
                      type: 'POST',
                      dataType: 'html',
                      data: {envelope_id: row_id}
                    }).done(function ( data ) {
                        $("#list_shipping_service_available").html(data);
                        $(".item_was_forward").hide();
                    });
                }
                else if ( ( (data_row.direct_shipping_flag == '0') || (data_row.collect_shipping_flag == '0' ) ) && ( (data_row.envelope_scan_flag != '0') && (data_row.item_scan_flag != '0') ) ) {
                    $("#scanButtonContainerSub").css({"margin-top":"0px"});
                    $(".item_was_forward").hide();
                    $(".no_save_tracking").hide();
                    $("#tr_no_tracking_number").show();
                    $(".tracking_disable").attr("disabled", true);
                    $.ajax({
                        url: TodoList.ajaxUrls.list_shipping_service_available,
                        type: 'POST',
                        dataType: 'html',
                        data: {envelope_id: row_id}
                    }).done(function ( data ) {
                        $("#list_shipping_service_available").html(data);
                        $(".item_was_forward").hide();
                        $(".wrap_tracking_number").show();
                    });
                }
               
                if( (data_row.envelope_scan_flag == '0') || (data_row.item_scan_flag == '0') || (data_row.trash_flag == '0') || (data_row.trash_flag == '5') ) {
                    if((data_row.trash_flag == '0') || (data_row.trash_flag == '5')){
                        $("#scanButtonContainerSub").css({"margin-top":"176px"});
                    }
                    
                    $(".wrap_tracking_number").hide();
                }

                if( (data_row.envelope_scan_flag == '0') && (data_row.item_scan_flag != '0') ) {
                    $("#scanButtonContainerSub").css({"margin-top":"130px"});
                }
                if( (data_row.envelope_scan_flag != '0') && (data_row.item_scan_flag == '0') ) {
                    $("#scanButtonContainerSub").css({"margin-top":"130px"});
                }
                if( (data_row.envelope_scan_flag == '0') && (data_row.item_scan_flag == '0') ) {
                    $("#scanButtonContainerSub").css({"margin-top":"85px"});
                }

                console.log("Test: "+( (data_row.envelope_scan_flag == '0') || (data_row.item_scan_flag == '0') ));
                console.log("data_row.direct_shipping_flag: "+data_row.direct_shipping_flag);
                console.log("data_row.collect_shipping_flag: "+data_row.collect_shipping_flag);
                console.log("data_row.envelope_scan_flag: "+data_row.envelope_scan_flag);
                console.log("data_row.item_scan_flag: "+data_row.item_scan_flag);
                console.log("data_row.tracking_number_flag: "+data_row.tracking_number_flag);
                
                $('#dynaScanLink').attr('href', scan_url);
                $('#from_ID').val(data_row.from_customer_name);
                $('#to_name_ID').val(data_row.to_customer_id_view);
                $('#envelope_ID').val(row_id);
                $('#to_ID').val(data_row.to_customer_id_h);
                $('#type_ID').val(data_row.type_id);
                $('#type_id_ID').val(data_row.envelope_type_id);
                $('#Weight_ID').val(data_row.weight);
                $('#nextToDoForm_current_row_id').val(row_id);
                $('#nextToDoForm_postbox_id').val(data_row.postbox_id);
                $('#nextToDoForm_package_id').val(data_row.package_id);

                if (data_row.invoice_flag === 1) {
                    $('#invoice_flag_ID').prop('checked', true);
                } else {
                    $('#invoice_flag_ID').prop('checked', false);
                }
                $('#category_type').val(data_row.category_type);

                // Se load file pdf vua duoc scan
                var scanItemFlag = $('#scanItemTemporaryFlag_id').val();

                if (data_row.direct_shipping_flag || (data_row.collect_shipping_flag === '0' && data_row.package_id > 0)) {

                    if (scanItemFlag == '1' || scanItemFlag == '2') {
                        //console.log('Load envelope scan image of envelope id: ' + row_id);
                        TodoList.previewScanImage();
                    } else {
                        //console.log('Preview shipping item of envelope id: ' + row_id);
                        TodoList.previewShippingItem();
                    }
                } else {
                    //console.log('Load envelope scan image of envelope id: ' + row_id);
                    TodoList.previewScanImage();
                }

                // Change status
                $('#markCompletedButton').removeClass('yl');
                $('#markCompletedButton').removeClass('input-btn');
                $('#markCompletedButton').addClass('input-btn-disable');
                $('#markCompletedButton').prop('disabled', true);

                var marginTop = 150;
                $('#scanButtonContainer').show();
                $('.only_for_scan_item').hide();

                var isShowScanEnvelopeButton = false;
                if (data_row.envelope_scan_flag === '0') {
                    if ($('#has_scan_image_id').val() === '1') {
                        $('#scan_type_id').val('1');
                    }
                    $('#scanEnvelopeButton').show();
                    $('#markCompletedButton').show();
                    isShowScanEnvelopeButton = true;
                    marginTop -= 30;
                } else {
                    $('#scanEnvelopeButton').hide();
                    $('#markCompletedButton').hide();
                }

                var isShowScanItemButton = false;
                if (data_row.item_scan_flag === '0') {
                    if ($('#has_scan_image_id').val() === '1') {
                        $('#scan_type_id').val('2');
                    }
                    $('.only_for_scan_item').show();
                    $('#scanItemButton').show();
                    $('#markCompletedButton').show();
                    isShowScanItemButton = true;
                    marginTop -= 30;
                } else {
                    $('#scanItemButton').hide();
                    if (!isShowScanEnvelopeButton) {
                        $('#markCompletedButton').hide();
                    }
                }
                if ((data_row.direct_shipping_flag === '0' || (data_row.collect_shipping_flag === '0' && data_row.package_id > 0 )) &&
                    (data_row.item_scan_flag != '0') && (data_row.envelope_scan_flag != '0')) {
                    
                    
                    $('#shippingEnvelopeButton').show();
                    $('#markCompletedButton').css({"margin-left":"0px"}).show();
                    

                    marginTop -= 140;
                } else {

                    $("#markCompletedButton").css({"margin-left":"20px"});
                    $('#shippingEnvelopeButton').hide();
                    if (!isShowScanEnvelopeButton && !isShowScanItemButton) {
                        $('#markCompletedButton').hide();
                    }
                }

                // When user request delete
                if (data_row.trash_flag === '0') {
                    if (data_row.item_scan_flag === '0') {
                        if ($('#has_scan_image_id').val() === '1') {
                            $('#scan_type_id').val('2');
                        }
                        $('.only_for_scan_item').show();
                        $('#scanItemButton').show();
                        $('#markCompletedButton').show();
                        marginTop = 135;
                    } else if (data_row.envelope_scan_flag === '0') {
                        if ($('#has_scan_image_id').val() === '1') {
                            $('#scan_type_id').val('1');
                        }
                        $('#scanEnvelopeButton').show();
                        $('#markCompletedButton').show();
                        marginTop -= 30;
                    } else {
                        if (data_row.envelope_scan_flag == 1) {
                            $('#scanItemTemporaryFlag_id').val("1");
                            TodoList.previewScanImage();
                            $('#markCompletedButton').show();
                        } else {
                            marginTop = 170;
                            $('#scanEnvelopeButton, #scanItemButton, #shippingEnvelopeButton').hide();

                            // Change status
                            $('#markCompletedButton').addClass('yl');
                            $('#markCompletedButton').addClass('input-btn');
                            $('#markCompletedButton').removeClass('input-btn-disable');
                            $('#markCompletedButton').prop('disabled', false);
                            //TodoList.previewShippingItem();
                            $('#markCompletedButton').show();
                        }
                        $('#current_scan_type').val('5');
                    }
                } else if (data_row.trash_flag === '5') { // added trashed process
                    marginTop = 170;
                    $('#scanEnvelopeButton, #scanItemButton, #shippingEnvelopeButton').hide();
                    $('#current_scan_type').val('5');

                    // Change status
                    $('#markCompletedButton').addClass('yl');
                    $('#markCompletedButton').addClass('input-btn');
                    $('#markCompletedButton').removeClass('input-btn-disable');
                    $('#markCompletedButton').prop('disabled', false);
                    $('#markCompletedButton').show();
                }

                //$('#scanButtonContainerSub').css({'margin-top': marginTop});

                if (data_row.status == "1") {
                    $('#scanEnvelopeButton').removeClass('yl');
                    $('#scanEnvelopeButton').removeClass('input-btn');
                    $('#scanEnvelopeButton').addClass('input-btn-disable');
                    $('#scanEnvelopeButton').prop('disabled', true);

                    $('#scanItemButton').removeClass('yl');
                    $('#scanItemButton').removeClass('input-btn');
                    $('#scanItemButton').addClass('input-btn-disable');
                    $('#scanItemButton').prop('disabled', true);

                    $('#shippingEnvelopeButton').removeClass('yl');
                    $('#shippingEnvelopeButton').removeClass('input-btn');
                    $('#shippingEnvelopeButton').addClass('input-btn-disable');
                    $('#shippingEnvelopeButton').prop('disabled', true);
                } else {
                    $('#scanEnvelopeButton').addClass('yl');
                    $('#scanEnvelopeButton').addClass('input-btn');
                    $('#scanEnvelopeButton').removeClass('input-btn-disable');
                    $('#scanEnvelopeButton').prop('disabled', false);

                    $('#scanItemButton').addClass('yl');
                    $('#scanItemButton').addClass('input-btn');
                    $('#scanItemButton').removeClass('input-btn-disable');
                    $('#scanItemButton').prop('disabled', false);

                    $('#shippingEnvelopeButton').addClass('yl');
                    $('#shippingEnvelopeButton').addClass('input-btn');
                    $('#shippingEnvelopeButton').removeClass('input-btn-disable');
                    $('#shippingEnvelopeButton').prop('disabled', false);
                }

                // Hide mark completed button if this item already completed
                if (data_row.completed_flag === '1') {
                    // $('#markCompletedButton').hide();
                }
                $('#scanItemTemporaryFlag_id').val('0');



            },

            loadComplete: function () {
                var selected_row_id = $('#envelope_ID').val();
                $('#dataGridResult').jqGrid("setSelection", selected_row_id);
                $.autoFitScreen(($( window ).width()- 50)); //#1297 check all tables in the system to minimize wasted space

                // #590: handle change remarked flag
                $(".change_flag").click(function () {
                    var id = $(this).data('id');
                    var value = $(this).data('value');
                    var new_value = 0;

                    // change class
                    switch (value) {
                        case "1":
                            $(this).find('span').removeClass("managetables-icon-yellow-flag");
                            $(this).find('span').addClass("managetables-icon-red-flag");
                            $(this).data('value', "2");
                            $(this).attr('title', 'FRAUD: please do not handle this activity, high probability of fraud case');
                            new_value = 2;
                            break;
                        case "2":
                            $(this).find('span').removeClass("managetables-icon-red-flag");
                            $(this).find('span').addClass("managetables-icon-green-flag");
                            $(this).data('value', "3");
                            $(this).attr('title', 'Please proceed to handle this activity, activity is verified to be rightful');
                            new_value = 3;
                            break;
                        case "3":
                            $(this).find('span').removeClass("managetables-icon-green-flag");
                            $(this).find('span').addClass("managetables-icon-flag");
                            $(this).attr('title', 'click to set caution alert for this activity');
                            $(this).data('value', "0");
                            new_value = 0;
                            break;
                        default:
                            $(this).find('span').removeClass("managetables-icon-flag");
                            $(this).find('span').addClass("managetables-icon-yellow-flag");
                            $(this).attr('title', 'CAUTION: please handle this activity with highest caution, this might be fraud');
                            $(this).data('value', "1");
                            new_value = 1;
                            break;
                    }

                    setTimeout(function () {
                        // update flag.
                        $.ajaxExec({
                            url: TodoList.ajaxUrls.updateRemarkedFlag,
                            data: {id: id, value: new_value},
                            success: function (data) {
                                // do nothing.
                            }
                        });
                    }, 200);

                });
            }
        });
    },

    selectedFormatter: function (cellvalue, options, rowObject) {
        return '<input type="radio" name="selectEnvelopeRadio" class="managetables-selectitem" value="' + cellvalue + '"  />';
    },

    commentFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == '' || cellvalue == null) {
            return '<a class="view_envelope_comment_detail" data-id="' + rowObject[0] + '" style="display:inline-block;"><span class="managetables-icon managetables-icon-comment-add">View Comment</span></a>';
        } else {
            return '<a class="del_envelope_comment_detail" data-id="' + rowObject[0] + '" style="display:inline-block;"><span class="managetables-icon managetables-icon-comment">View Comment</span></a>';
        }
    },

    flagFormater: function (cellvalue, options, rowObject) {
        console.log("cellvalue", cellvalue);

        switch (cellvalue) {
            case "1":
                return '<a class="change_flag" title="CAUTION: please handle this activity with highest caution, this might be fraud" data-id="' + rowObject[0] + '" data-value="' + cellvalue + '" style="display:inline-block;"><span class="managetables-icon managetables-icon-yellow-flag">View Comment</span></a>';
                break;
            case "2":
                return '<a class="change_flag" title="FRAUD: please do not handle this activity, high probability of fraud case" data-id="' + rowObject[0] + '" data-value="' + cellvalue + '"  style="display:inline-block;"><span class="managetables-icon managetables-icon-red-flag">View Comment</span></a>';
                break;
            case "3":
                return '<a class="change_flag" title="Please proceed to handle this activity, activity is verified to be rightful" data-id="' + rowObject[0] + '" data-value="' + cellvalue + '"  style="display:inline-block;"><span class="managetables-icon managetables-icon-green-flag">View Comment</span></a>';
                break;
            default:
                return '<a class="change_flag" title="Click to set caution alert for this activity" data-id="' + rowObject[0] + '" data-value="' + cellvalue + '"  style="display:inline-block;"><span class="managetables-icon managetables-icon-flag">View Comment</span></a>';
                break;
        }
    },

    toCustomerFormater: function (cellvalue, options, rowObject) {
        return '<a class="view_customer_detail" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + rowObject[5] + '</a>';
    },

    toCustomerFormater02: function (cellvalue, options, rowObject) {
        var fullEnvelopeCode = rowObject[1];
        var linkEnvelopeCode = fullEnvelopeCode.substr(0, 9);
        var remainEnvelopeCode = fullEnvelopeCode.substr(9);

        return '<a class="access_customer_site" data-id="' + rowObject[3] + '" style="text-decoration: underline;"  >' + linkEnvelopeCode + '</a>' + remainEnvelopeCode;
    },

    /**
     * Close scan window and load image
     */
    previewScanImage: function () {
        // Load image
        var envelope_id = $('#envelope_ID').val();
        var customer_id = $('#to_ID').val();
        var scanItemFlag = $('#scanItemTemporaryFlag_id').val();
        var preview_url = TodoList.ajaxUrls.previewScanImage + '?customer_id=' + customer_id + '&envelope_id=' + envelope_id + '&has_scan_item_type=' + scanItemFlag;

        // Load preview scan image
        //console.log('Load preview scan image: ' + envelope_id);

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

    selectItemToScan: function (row_id) {
        var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
        $('#dataGridResult').jqGrid("setSelection", row_id);
        var scan_url = TodoList.ajaxUrls.scan + '?envelope_id=' + row_id + '&customer_id=' + data_row.to_customer_id_h;
        $('#dynaScanLink').attr('href', scan_url);

        $('#from_ID').val(data_row.from_customer_name);
        $('#to_name_ID').val(data_row.to_customer_id_view);
        $('#envelope_ID').val(row_id);
        $('#to_ID').val(data_row.to_customer_id_h);
        $('#type_ID').val(data_row.type_id);
        $('#type_id_ID').val(data_row.envelope_type_id);
        $('#weight').val(data_row.weight);

        TodoList.previewScanImage();
    },

    checkScanPending: function () {
        var row_id = $('#nextToDoForm_current_row_id').val();
        var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
        var submitUrl = TodoList.ajaxUrls.checkScanPending + '?customer_id=' + data_row.to_customer_id_h + '&package_id=' + data_row.package_id;
console.log("data_row======", data_row);
        // Check and display message if all collect shipping button is completed scan item and scan envelope
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'shippingEnvelopeForm',
            success: function (data) {
                if (data.status) {
                    TodoList.openShippingWindow(row_id);
                } else {
                    $.displayError('Please complete scan for envelope: ' + data.data);
                    return false;
                }
            }
        });
    },

    saveMarkedLines: function () {
        var markedItems = [], unmarkedItems = [];
        var rows = $("table#popupShippingItemDataGridResult > tbody > tr[id]");
        $.each(rows, function (index, row) {
            var itemId = $(row).attr('id');
            if ($(row).attr('aria-selected') == 'true') {
                markedItems.push(itemId);
            } else {
                unmarkedItems.push(itemId);
            }
        });
        if (markedItems.length > 0 || unmarkedItems.length > 0) {
            $.ajaxExec({
                url: TodoList.ajaxUrls.saveMarkedLines,
                data: {marked_envelope_ids: markedItems.toString(), unmarked_envelope_ids: unmarkedItems.toString()},
                success: function (data) {
                    if (data.status) {
                        console.log('saveMarkedLines >>> OK');
                    } else {
                        console.log('saveMarkedLines >>> FAIL');
                    }
                }
            });
        }
    },

    openShippingWindow: function (row_id) {
        $('#shippingEnvelopeWindow').html('');
        var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);

        var shipping_url = TodoList.ajaxUrls.shipping + '?customer_id=' + data_row.to_customer_id_h + '&envelope_id=' + data_row.id;
        shipping_url += '&package_id=' + data_row.package_id;
        shipping_url += '&postbox_id=' + data_row.postbox_id;

        if (data_row.direct_shipping_flag == 0 && data_row.direct_shipping_flag != '') {
            // Direct shipping
            shipping_url += '&shipping_type=' + TodoList.shippingTypes.DIRECT_SHIPPING;
        } else if (data_row.collect_shipping_flag == 0 && data_row.collect_shipping_flag != '') {
            // Collect shipping
            shipping_url += '&shipping_type=' + TodoList.shippingTypes.COLLECT_SHIPPING;
        }

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
            close: function(){
                //$("#scanButtonContainerSub").css({"margin-top":"0px"});
                $(this).dialog('destroy');
            }, 
            buttons: {
                'Save & Exit': function () {
                    // Validate
                    var valid = TodoList.validateShipping();
                    if (!valid) {
                        return;
                    }
                    
                    $('#shippingEnvelopeForm').attr('action', TodoList.ajaxUrls.checkShipping);
                    // Check shipping address
                    $.ajaxSubmit({
                        url: TodoList.ajaxUrls.checkShipping,
                        formId: 'shippingEnvelopeForm',
                        success: function (data) {
                            if (data.status) {
                                // show tracking number after completed preview shipping.
                                $(".wrap_tracking_number").show();

                                $("#scanButtonContainerSub").css({"margin-top":"0px"});
                                // update shipping service
                                $("#shipping_services").val($("#shippingEnvelopeForm_shipping_service_id").val());
                                if($("#shippingEnvelopeForm_shipping_service_id").val() == "147"){
                                    $("#tracking_number").val("was picked up");
                                }else{
                                    //$("#tracking_number").val($("#shippingEnvelopeForm_tracking_number").val());
									$("#tracking_number").val(data.data.tracking_number);
                                }

                                TodoList.printLabel(data_row, '0');
                                $('#shippingEnvelopeForm').attr('action', TodoList.ajaxUrls.shipping);
                                TodoList.saveMarkedLines();

                                // show or hide tracking number
                                TodoList.showOrHideTrackingNumber();
                            } else {
                                $.displayError(data.message);
                                return false;
                            }
                        }
                    });
                },
                //'Print Label': function () {
                //    $('#shippingEnvelopeForm').attr('action', TodoList.ajaxUrls.checkShipping);
                //    TodoList.printLabel(data_row, '1');
                //    $('#shippingEnvelopeForm').attr('action', TodoList.ajaxUrls.shipping);
                //    //$("#scanButtonContainerSub").css({"margin-top":"0px"});
                //    TodoList.saveMarkedLines();
                //},
                'Cancel': function () {
                    //$("#scanButtonContainerSub").css({"margin-top":"0px"});
                    TodoList.saveMarkedLines();
                    $(this).dialog('destroy');
                }
            }
        });
        $('#shippingEnvelopeWindow').dialog('option', 'position', 'center');
        $('#shippingEnvelopeWindow').dialog('open');

        return false;
    },
    
    /**
     * Validate at shipping UI screen
     * @returns {undefined}
     */
    validateShipping: function() {
        var shipping_service_id = $('#shippingEnvelopeForm_shipping_service_id').val();
        var shipping_service_template = PrepareShipping.getShippingServiceTemplate(shipping_service_id);
        // shipping_service_template = 1 -- Standard
        if (shipping_service_template == 1) {
            var input_fee1 = $('#shippingEnvelopeForm_other_package_price_fee').val();
            input_fee = input_fee1.replace(',', '.');
            if (!$.isValidNumber(input_fee) || (input_fee1.indexOf(',') >= 0 && input_fee1.indexOf('.') >=0 )) {
                $.displayError('Other shipping fee should be numeric value.');
                return false;
            }
        }
        // shipping_service_template = 6 -- DP Brief
        else if (shipping_service_template == 3) {
            if ($('#shippingEnvelopeForm_other_package_price_flag').attr('checked')) {
                var input_fee1 = $('#shippingEnvelopeForm_other_package_price_fee').val();
                input_fee = input_fee1.replace(',', '.');
                if (!$.isValidNumber(input_fee) || (input_fee1.indexOf(',') >= 0 && input_fee1.indexOf('.') >=0 )) {
                    $.displayError('Other shipping fee should be numeric value.');
                    return false;
                }
            }
        } else {
            var customs_handling1 = $('#shippingEnvelopeForm_charge_customs_process').val();
            customs_handling = customs_handling1.replace(',', '.');
            if ((customs_handling !== '' && !$.isValidNumber(customs_handling)) || (customs_handling1.indexOf(',') >= 0 && customs_handling1.indexOf('.') >=0 )) {
                $.displayError('The customs handing fee should be numeric value.');
                return false;
            }
            
            var input_fee1 = $('#shippingEnvelopeForm_postal_charge').val();
            input_fee = input_fee1.replace(',', '.');
            if (!$.isValidNumber(input_fee) || (input_fee1.indexOf(',') >= 0 && input_fee1.indexOf('.') >=0 )) {
                $.displayError('The postal charge should be numeric value.');
                return false;
            }
            
            var input_fee2 = $("#shippingEnvelopeForm_customs_insurance_value").val();
            input_fee = input_fee2.replace(',', '.');
            if (input_fee2 != '' && !$.isValidNumber(input_fee) || (input_fee2.indexOf(',') >= 0 && input_fee2.indexOf('.') >=0 )) {
                $.displayError('The custom insurance value should be numeric value.');
                return false;
            }
        }
        return true;
    },

    /**
     *
     * print_flag = 1: Print
     * print_flag = 0: Save & Exit
     */
    printLabel: function (data_row, print_flag) {
        // Check and submit data
        var current_scan_type = $('#current_scan_type').val();
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

        if (data_row.direct_shipping_flag == 0) {
            // Direct shipping
            $('#current_scan_type').val(TodoList.shippingTypes.DIRECT_SHIPPING);
        } else if (data_row.collect_shipping_flag == 0) {
            // Collect shipping
            $('#current_scan_type').val(TodoList.shippingTypes.COLLECT_SHIPPING);
        }

        // Change status
        $('#markCompletedButton').addClass('yl');
        $('#markCompletedButton').addClass('input-btn');
        $('#markCompletedButton').removeClass('input-btn-disable');
        $('#markCompletedButton').prop('disabled', false);
        $(".tracking_disable").attr("disabled",false);
        
        if (print_flag == '1') {
            if ($('#previewEstamp_iframe').length) {
                window.frames["previewEstamp_iframe"].focus();
                window.frames["previewEstamp_iframe"].print();
            } else {
                $.displayError('Please click to Create preview of stamp or Buy stamp button.');
                return false;
            }
        } else {
            $('#shippingEnvelopeWindow').dialog('close');
        }
    },

    /**
     * Get estamp (Not user this method)
     */
    getStamp: function (data_row) {
        var package_price = $('#shippingEnvelopeForm_package_price_id').val();
        var ppl = $('#package_letter_size').val();

        if ($('#shippingEnvelopeForm_include_estamp').prop("checked")) {
            if ($('#shippingEnvelopeForm_include_estamp_img').attr('src') == '') {
                var submitUrl = TodoList.ajaxUrls.getStamp + '?ppl=' + ppl + '&package_price=' + package_price;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        $('#shippingEnvelopeForm_estamp_url').val(data.message);
                        $('#shippingEnvelopeForm_include_estamp_img').attr('src', data.message);
                        $('#estamp_container').show();
                        $.delayTime(2000);
                        TodoList.printLabel(data_row, '1');
                    }
                });
            }
        } else {
            TodoList.printLabel(data_row, '1');
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
    markCompleted: function (row_id, current_scan_type) {
        if (current_scan_type === '1' || current_scan_type === '2' || current_scan_type === '5') {
            var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
            var completed_url = TodoList.ajaxUrls.completed + '?customer_id=' + data_row.to_customer_id_h + '&envelope_id=' + data_row.id + '&current_scan_type=' + current_scan_type;
            if ($("#invoice_flag_ID").prop('checked') == true) {
                completed_url += '&invoice_flag=' + $('#invoice_flag_ID').val();
            }
            completed_url += '&category_type=' + $('#category_type').val();
            //console.log(completed_url);
            $.ajaxExec({
                url: completed_url,
                success: function (data) {
                    if (data.status) {
                        // Reload data grid
                        document.location.href = TodoList.ajaxUrls.todo;
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        } else {
            
            var tracking_number    = $("#tracking_number").val() ;
            var no_tracking_number = $("#no_tracking_number").val();
            var shipping_services  = $("#shipping_services").val();
            
            if( no_tracking_number == "0" && (tracking_number == "" || shipping_services == "0") ){

                //$('.dialog-form').html('');
                
                $("#preShippingWindow").html("<p style='color: #0089c8;font-weight: bold; margin-top: 16px;'>Please add a tracking number and shipping service, otherwise an activity is created to enter the tracking number later</p>");
                
                $('#preShippingWindow').openDialog({
                    autoOpen: false,
                    height: 200,
                    width: 400,
                    modal: false,
                    open: function () {},
                    buttons: {
                        'Cancel': function () {
                            $(this).dialog('close');
                        },
                        'I Understand': function () {
                            $(this).dialog('close');
                             // #1022: mark complete for collect shipping with large volume
                             TodoList.shippingDone(0,0,1,true);
                        }
                    }
                });
                
                $('#preShippingWindow').dialog('option', 'position', 'center');
                
                $('#preShippingWindow').dialog('open');
            
            }
            else {
                TodoList.shippingDone(0,0,1,true);
            }
        
        }
        return false;
    },
    shippingDone: function (start, limit, total, first_round) {

        var current_scan_type = $('#shippingEnvelopeForm_current_scan_type').val();
        var current_view_type = $('#shippingEnvelopeForm_current_view_type').val();
        var customer_id = $('#shippingEnvelopeForm_customer_id').val();
        var envelope_id = $('#shippingEnvelopeForm_envelope_id').val();
        var package_id = $('#shippingEnvelopeForm_package_id').val();
        var package_letter_size = $('#package_letter_size').val();
        var package_price = $('#shippingEnvelopeForm_package_price_id').val();
        var package_size = $('#shippingEnvelopeForm_package_size').val();
        var postbox_id = $('#shippingEnvelopeForm_postbox_id').val();
        var shipment_address_name = $('#shipment_address_name').val();
        var shipment_city = $('#shipment_city').val();
        var shipment_street = $('#shipment_street').val();
        var shipment_region = $('#shipment_region').val();
        var shipment_company = $('#shipment_company').val();
        var shipment_country = $('#shipment_country').val();
        var shipment_postcode = $('#shipment_postcode').val();
        var shipping_type_id = $('#shipping_type_id').val();
        var shipping_phone_number = $('#shipping_phone_number').val();
        var estamp_url = $('#shippingEnvelopeForm_estamp_url').val();
        var lable_size = $('#lable_size').val();
        var other_package_price_flag = $('#shippingEnvelopeForm_other_package_price_flag').val();
        var shipping_api_id = $('#shippingCalculatorForm_shipping_api_id').val();
        var shipping_credential_id = $('#shippingCalculatorForm_shipping_credential_id').val();
        
        // get number input
        var customs_handling = $('#shippingEnvelopeForm_charge_customs_process').val();
        var handling_charge = $('#shippingEnvelopeForm_handling_charge').val();
        var special_service_fee = $('#shippingEnvelopeForm_special_service_fee').val();
        
        if(customs_handling != '' && customs_handling != undefined){
            customs_handling = customs_handling.replace(',', '.');
            if (customs_handling != '' && !$.isValidNumber(customs_handling)) {
                $.displayError('The customs handing fee should be numeric value.');
                return;
            }
        }

        if(handling_charge != '' && handling_charge != undefined){
            handling_charge = handling_charge.replace(',', '.');
        }
        if (handling_charge == '' || handling_charge == undefined) {
            handling_charge = 0;
        }

        if(special_service_fee != '' && special_service_fee != undefined){
            special_service_fee = special_service_fee.replace(',', '.');
            if (special_service_fee != '' && !$.isValidNumber(special_service_fee)) {
                $.displayError('The special service fee should be numeric value.');
                return;
            }
        }
        if (special_service_fee == '' || special_service_fee == undefined) {
            special_service_fee = 0;
        }
       
        var insurance_customs_cost = $('#shippingEnvelopeForm_customs_insurance_value').val();
        var no_tracking_number = $("#no_tracking_number").val();
        var shipping_services  = $("#shipping_services").val();
        var tracking_number    = $("#tracking_number").val() ;

        //alert($('#shippingEnvelopeForm_current_scan_type').val());
        var shipping_service_id = $('#shippingEnvelopeForm_shipping_service_id').val();
        var shipping_service_template = PrepareShipping.getShippingServiceTemplate(shipping_service_id);
        
        var other_package_price_fee = '0';
        // shipping_service_id = 0 -- Standard
        if (shipping_service_template == 1) {
            var input_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
            input_fee = input_fee.replace(',', '.');
            other_package_price_fee = input_fee;
            if (!$.isValidNumber(input_fee)) {
                $.displayError('Other shipping fee should be numeric value.');
                return;
            }
            other_package_price_flag = '1';
        }
        // shipping_service_id = 6 -- DP Brief
        else if (shipping_service_template == 3) {
            if ($('#shippingEnvelopeForm_other_package_price_flag').attr('checked')) {
            	other_package_price_flag = '1';
                var input_fee = $('#shippingEnvelopeForm_other_package_price_fee').val();
                input_fee = input_fee.replace(',', '.');
                other_package_price_fee = input_fee;
                if (!$.isValidNumber(input_fee)) {
                    $.displayError('Other shipping fee should be numeric value.');
                    return;
                }
            } else {
                other_package_price_flag = '0';
            }
        } else {
        	other_package_price_flag = '1';
            var input_fee = $('#shippingEnvelopeForm_postal_charge').val();
            input_fee = input_fee.replace(',', '.');
            other_package_price_fee = input_fee;
            if (!$.isValidNumber(input_fee)) {
                $.displayError('The postal charge should be numeric value.');
                return;
            }
        }
        if (start >= total) {
            return;
        }

        if (first_round) {
            $("#progressbarShippingProcess").progressbar("option", "value", 0);
            $('#dialogShippingProcess').openDialog({
                height: 150,
                width: 450,
                modal: true,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                }
            });
            $('#dialogShippingProcess').dialog('option', 'position', 'center');
            $('#dialogShippingProcess').dialog('open');

        } else {
            var progressVal = (start / total) * 100;
            // Update progress status
            $("#progressbarShippingProcess").progressbar("option", "value", progressVal);
        }

        $.ajaxExec({
            url: TodoList.ajaxUrls.shipping,
            data: {
                start: start,
                limit: limit,
                total: total,
                customer_id: customer_id,
                envelope_id: envelope_id,
                package_id: package_id,
                current_scan_type: current_scan_type,
                current_view_type: current_view_type,
                package_letter_size: package_letter_size,
                package_price: package_price,
                package_size: package_size,
                postbox_id: postbox_id,
                shipment_address_name: shipment_address_name,
                shipment_company: shipment_company,
                shipment_city: shipment_city,
                shipment_street: shipment_street,
                shipment_region: shipment_region,
                shipment_country: shipment_country,
                shipment_postcode: shipment_postcode,
                shipping_type_id: shipping_type_id,
                estamp_url: estamp_url,
                lable_size: lable_size,
                other_package_price_flag: other_package_price_flag,
                other_package_price_fee: other_package_price_fee,
                no_tracking_number: no_tracking_number,
                shipping_services: shipping_services,
                tracking_number: tracking_number,
                shipping_service_id: shipping_service_id,
                shipping_phone_number: shipping_phone_number,
                customs_handling: customs_handling,
                insurance_customs_cost: insurance_customs_cost,
                handling_charge: handling_charge,
                special_service_fee: special_service_fee,
                shipping_api_id: shipping_api_id,
                shipping_credential_id: shipping_credential_id
            },
            showDialog: false,
            success: function (data) {
                if (data.status) {
                    if (data.data.start < data.data.total) {
                    	TodoList.shippingDone(data.data.start, data.data.limit, data.data.total, false);
                    } else {
                        $('#dialogShippingProcess').dialog('close');
                        // Reload data grid
                        document.location.href = TodoList.ajaxUrls.todo;
                    }
                    
                    // Change status
                    $('#markCompletedButton').addClass('yl');
                    $('#markCompletedButton').addClass('input-btn');
                    $('#markCompletedButton').removeClass('input-btn-disable');
                    $('#markCompletedButton').prop('disabled', false);
                } else {
                    $.displayError(data.message);
                    $('#dialogShippingProcess').dialog('close');
                }
            }
        });
    },
    scanEnvelop: function () {
        if ($('#to_name_ID').val() == '') {
            $.displayError('Please double click to select envelope before scan.');
            return false;
        }
        TodoList.prepareScanWindow();
        $('#scanEnvelopeWindow').dialog('open');
        return false;
    },

    /**
     * Prepare scan window.
     */
    prepareScanWindow: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');
        var scan_url = $('#dynaScanLink').attr('href');
        var scan_type = $('#scan_type_id').val();
        scan_url = scan_url + '&scan_type=' + scan_type;

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
                    TodoList.scanFile();
                },
                'Save & Exit': function () {
                    TodoList.saveFile();
                    $(this).dialog('close');
                },
                'Save & Exit (Without OCR)': function () {
                    document.getElementById('scanForm_UseOCRFlag').value = '0';
                    TodoList.saveFile();
                    $(this).dialog('close');
                },
                'Close': function () {
                    $(this).dialog('close');
                    TodoList.previewScanImage();
                }
            }
        });
        $('#scanEnvelopeWindow').dialog('option', 'position', 'center');
    },

    /**
     * Scan & upload file to server
     */
    scanFile: function () {
        var documentType = $('#documentType').val();
        if (documentType == '1') {
            var scan_url = $('#dynaScanLink').attr('href');
            $('#scanEnvelopeWindow').load(scan_url, function () {
                $('#buttonUploadPdfFile').button();
                $('#DW_PreviewMode').val('1');

                DWObject.SetViewMode(parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1), parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1));

                // Scan file
                $('#documentType').val('2'); //'1': upload file; '2': scan file

                // Waiting 10 seconds
                setTimeout(function () {
                    acquireImage();
                }, 5000);
            });
        } else {
            DWObject.SetViewMode(parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1), parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1));
            // Scan file
            $('#documentType').val('2'); //'1': upload file; '2': scan file
            acquireImage();
        }
    },

    saveFile: function () {
        var documentType = $('#documentType').val();
        //'1': upload file; '2': scan file
        // if (documentType == '1') {
        //	if(!confirm('Are you sure you want to proceed without OCR scan? (Item will not be searchable)')) {
        //		return;
        //	}
        //}

        // Only check for scan file
        if (documentType == '2') {
            // Upload file
            btnUpload_onclick();
            // Change status
            $('#markCompletedButton').addClass('yl');
            $('#markCompletedButton').addClass('input-btn');
            $('#markCompletedButton').removeClass('input-btn-disable');
            $('#markCompletedButton').prop('disabled', false);
            var selected_row_id = $('#envelope_ID').val();
            var current_scan_type = $('#current_scan_type').val();
            if (current_scan_type == '2') {
                $('#scanItemTemporaryFlag_id').val('2');
            } else {
                $('#scanItemTemporaryFlag_id').val('1');
            }
            $('#dataGridResult').jqGrid("setSelection", selected_row_id);
        }
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

        // Load preview shipping item
        console.log('Load preview shipping item: ' + envelope_id);

        $("#shippingItemDataGridResult").jqGrid('GridUnload');

        $("#shippingItemDataGridResult").jqGrid({
            url: TodoList.ajaxUrls.searchShipping,
            postData: {
                customer_id: customer_id,
                postbox_id: postbox_id,
                envelope_id: envelope_id,
                package_id: package_id
            },
            datatype: "json",
            height: 142,
            width: 475,
            rowNum: TodoList.configs.rowNum,
            rowList: TodoList.configs.rowList,
            pager: "#shippingItemDataGridPager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: false,
            altRows: true,
            altclass: 'jq-background',
            captions: '',
            colNames: ['ID', 'Envelope ID', 'Date Arrived', 'Type', 'Weight', '', 'Scan', '', '', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'item_no', index: 'item_no', sortable: false, width: 200, align: 'center'},
                {name: 'date_arrived', index: 'date_arrived', sortable: false, width: 90, align: 'center'},
                {name: 'size', index: 'size', width: 50, sortable: false, align: 'center'},
                {name: 'weight_label', index: 'weight_label', sortable: false, width: 50, align: 'center'},
                {name: 'weight', index: 'weight', sortable: false, hidden: true},
                {name: 'scan', index: 'scan', sortable: false, width: 60, align: 'left'},
                {name: 'customs_flag', index: 'customs_flag', hidden: true},
                {name: 'item_scan_flag', index: 'item_scan_flag', hidden: true},
                {name: 'envelope_scan_flag', index: 'envelope_scan_flag', hidden: true}
            ],

            // When double click to row
            onSelectRow: function (row_id) {

            },
            loadComplete: function () {
                // Gets screen width
                var screen_width = 500;
                var totalRecord = $("#shippingItemDataGridResult").jqGrid('getGridParam', 'records');
                if (totalRecord > 3) {
                    $("#shippingItemDataGridResult").jqGrid('setGridWidth', screen_width);
                }
            }
        });
        $('#previewEnvelopeScanContainer').addClass('hide');
        $('#previewShippingItemContainer').removeClass('hide');
    },

    /**
     * Process upload file.
     */
    uploadFile: function () {
        // $('#imagepath_banner').val('');
        // $('#imagepath_banner').click();
        return false;
    },

    scanIncomingEnvelop: function () {
        $('#scan_type_id').val('1');
        $('#current_scan_type').val('1');
        $('#dynaScanLink').click();

        return false;
    },

    scanIncomingItem: function () {
        $('#scan_type_id').val('2');
        $('#current_scan_type').val('2');
        $('#dynaScanLink').click();

        return false;
    },

    /**
     * View & Save comment detail
     */
    viewCommentDetail: function (elem) {
        var $link = $(elem);
        var envelope_id = $(elem).attr('data-id');
        var comment_url = TodoList.ajaxUrls.commentDetail + '?envelope_id=' + envelope_id;
        // Open new dialog
        $('#envelopeCommentWindow').html('');
        $('#envelopeCommentWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 600,
            modal: true,
            open: function () {
                $(this).load(comment_url, function () {
                  
                });
            },
            buttons: {
                'Save': function () {
                    TodoList.saveEnvelopeComment($link);
                    TodoList.searchToDoList();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#envelopeCommentWindow').dialog('option', 'position', 'center');
        $('#envelopeCommentWindow').dialog('open');

        return false;
    },
    
    /**
     * View & Save comment detail
     */
    delCommentDetail: function (elem) {
        var $link = $(elem);
        var envelope_id = $(elem).attr('data-id');
        var comment_url = TodoList.ajaxUrls.commentDetail + '?envelope_id=' + envelope_id;
        // Open new dialog
        $('#envelopeCommentWindow').html('');
        $('#envelopeCommentWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 600,
            modal: true,
            open: function () {
                $(this).load(comment_url, function () {
                });
            },
            buttons: {
                'Save': function () {
                    TodoList.saveEnvelopeComment($link);
                    TodoList.searchToDoList();
                },
                'Delete': function () {
                    TodoList.delEnvelopeComment($link,envelope_id);
                    TodoList.searchToDoList();
                    $(this).empty();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#envelopeCommentWindow').dialog('option', 'position', 'center');
        $('#envelopeCommentWindow').dialog('open');

        return false;
    },

    /**
     * Save envelope comment
     */
    saveEnvelopeComment: function ($link) {
        var submitUrl = $('#addEditEnvelopeCommentForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditEnvelopeCommentForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        $('#envelopeCommentWindow').dialog('close');
                    });
                    $link.find("span").removeClass("managetables-icon-comment-add");
                    $link.find("span").addClass("managetables-icon-comment");
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
     /**
     * Delete envelope comment
     */
    delEnvelopeComment: function ($link,envelope_id) {
        var v_url = $('#addEditEnvelopeCommentForm').attr('action');
        var v_del = 'del';
        var v_text = $('#addEditEnvelopeCommentForm_txt').val();
        $.ajaxExec({
            url: v_url,
            data: {envelope_id: envelope_id, del: v_del, text: v_text},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        $('#envelopeCommentWindow').dialog('close');
                    });
                    $link.find("span").addClass("managetables-icon-comment-add");
                    $link.find("span").removeClass("managetables-icon-comment");
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
    showOrHideTrackingNumber: function () {
        if ($("#no_tracking_number").is(':checked')) {
            $("#no_tracking_number").val(1);
            $("#shipping_services").css({"background":"#ebebeb"}).attr("disabled", true).prop("readonly", true);
            $("#tracking_number").css({"background":"#ebebeb"}).prop("readonly", true);
        } else {
            $("#no_tracking_number").val(0);
            $("#shipping_services").css({"background":"#ffffff"}).attr("disabled", false).prop("readonly", false);
            $("#tracking_number").css({"background":"#ffffff"}).prop("readonly", false);
        }
    }

}



$(function(){

    $("#btn_save_tracking_number").click(function(){

        var tracking_number   = $("#tracking_number").val();
        var shipping_services = $("#shipping_services").val();
        var envelope_id       = $('#envelope_ID').val();
        var type = "tracking";
        
        if( tracking_number == ""){
            $.displayError("Please input tracking number.");
            $("#tracking_number").focus();
            return;
        }
        if( shipping_services == "0"){
            $.displayError("Please select shipping services.");
            return;
        }
        else {

            $.ajaxExec({
                     
              url: TodoList.ajaxUrls.save_tracking_number,
              type: 'POST',
              dataType: 'json',
              data: {envelope_id: envelope_id, shipping_services: shipping_services, tracking_number:tracking_number, type: type},
              success: function (data) {
                $.infor({
                    message:"Save items sucessfull.",
                    ok:function(){
                        location.reload();
                    }
                });
              }

            });
        }

    });

    $("#btn_no_save_tracking_number").click(function(){

        var tracking_number   = $("#tracking_number").val();
        var shipping_services = $("#shipping_services").val();
        var envelope_id       = $('#envelope_ID').val();
        var type = "no_tracking";
        
        $.ajaxExec({
            url: TodoList.ajaxUrls.save_tracking_number,
            type: 'POST',
            dataType: 'json',
            data: {envelope_id: envelope_id, shipping_services: shipping_services, tracking_number:tracking_number, type: type},
            success: function (data) {

                $.infor({
                    message:"Save items sucessfull.",
                    ok:function(){
                        location.reload();
                    }
                });
            }

        });

    });

    $("#no_tracking_number").click(function () {
        TodoList.showOrHideTrackingNumber();
    }); 
});

// Handler progress bar
$("#progressbarShippingProcess").progressbar({
    max: 100,
    value: 0,
    change: function () {
        $('#progressbarLabel').text("Current Progress: " + parseInt($("#progressbarShippingProcess").progressbar("value")) + "%");
    },
    complete: function () {
        $('#progressbarLabel').text("Complete!");
        dialog.dialog({
        	closeOnEscape: false,
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            }
        });
        $(".ui-dialog button").last().focus();
    }
});
