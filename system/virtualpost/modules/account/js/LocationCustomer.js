var LocationCustomer = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        number: '',
        searchLocation: '',
        addLocation: '',
        editLocation: '',
        deleteLocation: '',
        uploadImageLocationUrl: '',
        locations_office:""
    },
    configs: {
        baseUrl: '',
        rowNum: 0,
        rowList: ''
    },
    /*
     *  Messages
     */
    messages: {
    },
    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.number = baseUrl + 'account/location';
        this.ajaxUrls.searchLocation = baseUrl + 'account/location';
        this.ajaxUrls.addLocation = baseUrl + 'account/location/add';
        this.ajaxUrls.editLocation = baseUrl + 'account/location/edit';
        this.ajaxUrls.deleteLocation = baseUrl + 'account/location/delete';
        this.ajaxUrls.uploadImageLocationUrl = baseUrl + 'account/location/upload_image_location';
        this.ajaxUrls.loadStandardShippingServicesUrl = baseUrl + 'account/location/load_standard_shipping_services';
        this.ajaxUrls.displayLocationConfirmUrl = baseUrl + 'account/location/display_confirmation_add_location';
        this.ajaxUrls.locations_office = baseUrl + 'account/location/location_office';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        LocationCustomer.initAjaxUrls(baseUrl);

        // init config.
        LocationCustomer.configs.baseUrl = baseUrl;
        LocationCustomer.configs.rowList = rowList.split(',');
        LocationCustomer.configs.rowNum = rowNum;

        // init screen
        LocationCustomer.searchLocation();

        // add new user
        $("#btnAddNewLocation").click(function () {
            LocationCustomer.addNewLocation();
        });
        
        // Process when user click to edit icon.
        $('.managetables-edit').live('click', function () {
            LocationCustomer.editLocation($(this).data('id'));
        });

        $('.managetables-delete').live('click', function () {
            var location_id = $(this).attr('data-id');
            LocationCustomer.deleteLocation(location_id);
        });
        
        // Event of add, remove shipping services
        $("#btnAddShippingService").live('click', function () {
            LocationCustomer.addShippingService();
        });
        $("#btnRemoveShippingService").live('click', function () {
            LocationCustomer.removeShippingService();
        });
        $("#btnAddType").live('click', function () {
            LocationCustomer.addType();
        });
        $("#btnRemoveType").live('click', function () {
            LocationCustomer.removeType();
        });
        
        $("#location_office").live('click',function(){
            LocationCustomer.open_location_office();
        });
        
        // Event: change location image
        $('#imagepath').live('change', function () {
            LocationCustomer.changeLocationImage(this);
        });
        
        $('#ConfirmationAddLocationWindow_confirmAddNewLocationButton').live('click', function () {
            if($("#display_confirmation_add_location_terms").prop('checked') != true){
                $.displayError("In order to use our services, you must agree to ClevverMail's Terms of Service.");
                return;
            }
            LocationCustomer.saveLocation();
        });
        
        $("#addDigitalPanelLink").live('click',function(){
            LocationCustomer.showDigitalPanelConditionPopup();
            
            return false;
        });
    },
    searchLocation: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: LocationCustomer.ajaxUrls.searchLocation,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: LocationCustomer.configs.rowNum,
            rowList: LocationCustomer.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Location Name', 'User', 'Postbox', 'Open for external', 'Panel', 'Active', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'location_name', index: 'location_name', width: 270},
                {name: 'user', index: 'user', width: 125, sortable: false},
                {name: 'postbox', index: 'postbox', width: 125, sortable: false},
                {name: 'share_external_flag', index: 'share_external_flag', width: 125, align: "center",  sortable: false, formatter: LocationCustomer.shareFormater},
                {name: 'panel', index: 'panel', width: 100, align: "center",  sortable: false},
                {name: 'public_flag', index: 'public_flag', width: 100,  align: "center", sortable: false, formatter: LocationCustomer.activeFormater},
                {name: 'id', index: 'id', width: 120, sortable: false, align: "center", formatter: LocationCustomer.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(1000);
            }
        });
    },
    activeFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === '1') {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
        } else {
            return '';
        }
    },
    shareFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === '1') {
            return 'Yes';
        } else {
            return 'No';
        }
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-edit" data-id="' + cellvalue + '" title="Edit"></span></span>';
        } else {
            return '';
        }
    },
    addNewLocation: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addLocationWindow').openDialog({
            autoOpen: false,
            height: 600,
            width: 1000,
            modal: true,
            open: function () {
                $(this).load(LocationCustomer.ajaxUrls.addLocation, function () {
                    $('#addEditLocationForm_email').focus();
                });
            },
            buttons: {
                'Save & Start new Location': function () {
                    //LocationCustomer.saveLocation();
                    LocationCustomer.displayLocationConfirm();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addLocationWindow').dialog('option', 'position', 'center');
        $('#addLocationWindow').dialog('open');
        return false;
    },
    editLocation: function (location_id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editLocationWindow').openDialog({
            autoOpen: false,
            height: 600,
            width: 1000,
            modal: true,
            open: function () {
                var submitUrl = LocationCustomer.ajaxUrls.editLocation + '?id=' + location_id;
                $(this).load(submitUrl, function () {
                    $('#addEditLocationForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function () {
                    LocationCustomer.saveLocation();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editLocationWindow').dialog('option', 'position', 'center');
        $('#editLocationWindow').dialog('open');
    },
    displayLocationConfirm: function() {
        var windowId = '#ConfirmationAddLocationWindow';
        var loadUrl = this.ajaxUrls.displayLocationConfirmUrl;
        $.openDialog(windowId, {
            height: 240,
            width: 500,
            openUrl: loadUrl,
            title: "Confirm your new location",
            show_only_close_button: false,
            buttons: [{
                'text': 'Confirm',
                'id': 'confirmAddNewLocationButton'
            }],
            callback: function(){
                //location.reload();
            }
        });
    },
    saveLocation: function () {
        var submitUrl = $('#addEditLocationForm').attr('action');
        var action_type = $('#h_action_type').val();
        var data = $('#addEditLocationForm').serializeObject();
        //var image_file_name = $('#imagepath_id').val();
        $.ajaxExec({
            url: submitUrl,
            data: data,
            success: function (data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#addLocationWindow').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#editLocationWindow').dialog('close');
                    }
                    $('#ConfirmationAddLocationWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        LocationCustomer.searchLocation();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteLocation: function (location_id) {
        // Show confirm dialog
        $.confirm({
            message: "Do you want to delete all postboxes from this location?",
            yes: function () {
                
            }
        });
    },
    addShippingService: function () {
        $("#all_shipping_services > option:selected").each(function () {
            $(this).remove().appendTo("#available_shipping_services").attr("selected", true);
        });
        LocationCustomer.loadStandardShippingService();
    },

    removeShippingService: function () {
        $("#available_shipping_services > option:selected").each(function () {
            $(this).remove().appendTo("#all_shipping_services").attr("selected", false);
        });
        LocationCustomer.loadStandardShippingService();
    },

    addType: function () {
        $("#list_type > option:selected").each(function () {
            $(this).remove().appendTo("#list_type_available").attr("selected", true);
        });
    },

    removeType: function () {
        $("#list_type_available > option:selected").each(function () {
            $(this).remove().appendTo("#list_type").attr("selected", false);
        });
    },
    changeLocationImage: function (elem) {
        var myFile = $(elem).val();
        var ext = myFile.split('.').pop();

        ext = ext.toUpperCase();
        if (ext != "PNG" && ext != "JPG") {
            $.displayError('Please select PNG or JPG file to upload.', null, function () {
                $('#imagepath_id').val('');
            });
            return;
        }
        $('#imagepath_id').val(myFile);

        $.ajaxFileUpload({
           id: 'imagepath',
           data: {
               location_id: $("#h_location_id").val(),  
               input_file_client_name: 'imagepath'
           },
           url: LocationCustomer.ajaxUrls.uploadImageLocationUrl,
           resetFileValue:true,
           success: function(obj) {
                if(obj.status){
                    $("#h_location_id").val(obj.message);
                }
           }
        });
    },
    loadStandardShippingService: function() {
        var shipping_service_ids = '';
        var list_shipping_service_ids = [];
        $('#available_shipping_services option::selected').each(function(i, selected){ 
            list_shipping_service_ids[i] = $(selected).val(); 
        });
        shipping_service_ids = list_shipping_service_ids.join();
        $.bindSelect(this.ajaxUrls.loadStandardShippingServicesUrl, 'type=1&shipping_service_ids=' + shipping_service_ids, 
            'primary_letter_shipping', '', $('#h_primary_letter_shipping').val(), function() {});
        $.bindSelect(this.ajaxUrls.loadStandardShippingServicesUrl, 'type=2&shipping_service_ids=' + shipping_service_ids, 
            'primary_international_letter_shipping', '', $('#h_primary_international_letter_shipping').val(), function() {});
        $.bindSelect(this.ajaxUrls.loadStandardShippingServicesUrl, 'type=3&shipping_service_ids=' + shipping_service_ids, 
            'standard_national_parcel_service', '', $('#h_standard_national_parcel_service').val(), function() {});
        $.bindSelect(this.ajaxUrls.loadStandardShippingServicesUrl, 'type=4&shipping_service_ids=' + shipping_service_ids, 
            'standard_international_parcel_service', '', $('#h_standard_international_parcel_service').val(), function() {});
    },
    showDigitalPanelConditionPopup: function(){
        $.openDialog("#showDigitalPanelConditionWindow", {
            height: 500,
            width: 900,
            openUrl: LocationCustomer.configs.baseUrl + 'account/location/show_digital_panel',
            title: "The digital touch Panel",
            show_only_close_button: false,
            callback: function(){
                // do  nothing.
            }
        });
    },
    open_location_office: function() {
        var location_id = $("#h_location_id").val();
        $('#window_location_office').html('');
        //$('#window_location_office').load( LocationCustomer.ajaxUrls.locations_office+"?location_id="+ location_id);
        // Open new dialog
        $('#window_location_office').openDialog({
            autoOpen: false,
            height: 520,
            width: 600,
            modal: true,
            open: function () {
                $(this).load( LocationCustomer.ajaxUrls.locations_office+"?location_id="+ location_id, function () { });
            },
            buttons: {
                'Save': function () {
                    if(location_id > 0){
                        var submitUrl = LocationCustomer.ajaxUrls.locations_office;
                        $.ajaxSubmit({
                            url: submitUrl,
                            formId: 'locationOfficeForm',
                            success: function (data) {
                                if (data.status) {
                                    $('#window_location_office').dialog('close');
                                    $.infor({
                                        message: data.message
                                    });

                                } else {
                                    //$('#forward_address').dialog('close');
                                    $.displayError(data.message);
                                }
                            }
                        });
                    }
                   
                    $("#business_concierge_flag").val($("#temp_business_concierge_flag").val());
                    $("#video_conference_flag").val($("#temp_video_conference_flag").val());
                    $("#meeting_rooms_flag").val($("#temp_meeting_rooms_flag").val());

                    if($("#temp_business_concierge_flag").val() == '1'){
                        $("#temp_business_concierge_flag").attr("checked",true);
                    }
                    if($("#temp_video_conference_flag").val() == '1'){
                        $("#temp_video_conference_flag").attr("checked",true);
                    }
                    if($("#temp_meeting_rooms_flag").val() == '1'){
                        $("#temp_meeting_rooms_flag").attr("checked",true);
                    }
                   
                    $("#office_feature_1").val($("#temp_office_feature_1").val());
                    $("#office_feature_2").val($("#temp_office_feature_2").val());
                    $("#office_feature_3").val($("#temp_office_feature_3").val());
                    $("#office_feature_4").val($("#temp_office_feature_4").val());
                    $("#office_feature_5").val($("#temp_office_feature_5").val());
                    $("#office_feature_6").val($("#temp_office_feature_6").val());
                    $('#window_location_office').dialog('close');
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#window_location_office').dialog('option', 'position', 'center');
        $('#window_location_office').dialog('open');
    },
    
};