// Declare an object literal (an instance/object)
var LocationForm = {
    
    location_id: '',
    
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        check_active_pricing_template: null,
        uploadImageLocationUrl: null,
        loadStandardShippingServicesUrl: null
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.check_active_pricing_template = baseUrl + 'settings/locations/check_active_pricing_template';
        this.ajaxUrls.uploadImageLocationUrl = baseUrl + 'settings/locations/upload_image_location';
        this.ajaxUrls.locations_office = baseUrl + 'settings/locations/location_office';
        this.ajaxUrls.loadStandardShippingServicesUrl = baseUrl + 'settings/locations/load_standard_shipping_services';
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, location_id) {
        // Init AJAX URLs
        this.initAjaxUrls(baseUrl);
        //console.log('Input Location ID: ' + location_id);
        LocationForm.location_id = location_id;
        // Event listeners

        // When select file
        LocationForm.selectFile();

        // Event: change location image
         $('#imagepath').live('change', function () {
             LocationForm.changeLocationImage(this);
         });

         $('#shared_office_image_path').live('change', function () {
             LocationForm.chang_seshared_office_image(this);
         });

        // Event: add, remove a pricing template
        $("#addButton").live("click", function () {
            LocationForm.addPricingTemplate();
        });
        $("#removeButton").live("click", function () {
            LocationForm.removePricingTemplate();
        });

        $("#location_office").click(function(){
            LocationForm.open_location_office();
        });

        // Event of add, remove shipping services
        $("#btnAddShippingService").bind('click', function () {
            LocationForm.addShippingService();
        });
        $("#btnRemoveShippingService").bind('click', function () {
            LocationForm.removeShippingService();
        });
        $("#btnAddType").bind('click', function () {
            LocationForm.addType();
        });
        $("#btnRemoveType").bind('click', function () {
            LocationForm.removeType();
        });
    },

    selectFile: function () {
        var autocomplete;
        autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('addEditLocationForm_LocationName')),
            {types: ['geocode']}
        );
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            var componentForm = {
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };
            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }
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
           url: LocationForm.ajaxUrls.uploadImageLocationUrl,
           resetFileValue:true,
           success: function(obj) {
                if(obj.status){
                    $("#h_location_id").val(obj.message);
                }
           }
        });
    },

    chang_seshared_office_image: function (elem) {
        var myFile = $(elem).val();
        var ext = myFile.split('.').pop();

        ext = ext.toUpperCase();
        if (ext != "PNG" && ext != "JPG") {
            $.displayError('Please select PNG or JPG file to upload.', null, function () {
                $('#shared_office_image_path_id').val('');
            });
            return;
        }
        $('#shared_office_image_path_id').val(myFile);

        $.ajaxFileUpload({
           id: 'shared_office_image_path',
           data: {
               location_id: $("#h_location_id").val(),  
               input_file_client_name: 'shared_office_image_path'
           },
           url: LocationForm.ajaxUrls.uploadImageLocationUrl,
           resetFileValue:true,
           success: function(obj) {
                if(obj.status){
                    $("#h_location_id").val(obj.message);
                }
           }
        });
    },

    open_location_office: function() {
        var location_id = LocationForm.location_id;
        console.log('Location ID: ' + location_id);
        $('#window_location_office').html('');
        //$('#window_location_office').load( LocationForm.ajaxUrls.locations_office+"?location_id="+ LocationForm.location_id);
        // Open new dialog
        $('#window_location_office').openDialog({
            autoOpen: false,
            height: 520,
            width: 600,
            modal: true,
            open: function () {
                $(this).load( LocationForm.ajaxUrls.locations_office+"?location_id="+ LocationForm.location_id, function () { });
            },
            buttons: {
                'Save': function () {
                    
                    if($("#location_id").val() > 0){
                        
                        var submitUrl = $('#locationOfficeForm').attr('action');
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

    addPricingTemplate: function () {
        $("#pricing_template_id1 > option:selected").each(function () {
            $(this).remove().appendTo("#pricing_template_id");
        });
    },

    removePricingTemplate: function () {
        var locationID = $("#h_location_id").val();
        var pricingTemplateID = null, pricingTemplateIDs = [];

        $("#pricing_template_id > option:selected").each(function () {
            pricingTemplateID = $(this).val();
            pricingTemplateIDs.push(pricingTemplateID);
        });

        if (pricingTemplateIDs.length > 0) {
            if (parseInt(locationID) > 0) { // Case of Edit location
                $("#pricing_template_id > option:selected").each(function () {
                    $(this).remove().appendTo("#pricing_template_id1");
                });
                
            } else { // Case of Add location
                // Remove all selected pricing templates
                $("#pricing_template_id > option:selected").each(function () {
                    $(this).remove().appendTo("#pricing_template_id1");
                });
            }
        }
    },

    addShippingService: function () {
        $("#all_shipping_services > option:selected").each(function () {
            $(this).remove().appendTo("#available_shipping_services").attr("selected", true);
        });
    },

    removeShippingService: function () {
        $("#available_shipping_services > option:selected").each(function () {
            $(this).remove().appendTo("#all_shipping_services").attr("selected", false);
        });
    },

    addType: function () {
        $("#list_type > option:selected").each(function () {
            $(this).remove().appendTo("#list_type_available").attr("selected", true);
        });
        //$('#list_type_available > option').prop('selected', true);
    },

    removeType: function () {
        $("#list_type_available > option:selected").each(function () {
            $(this).remove().appendTo("#list_type").attr("selected", false);
        });
    },
    loadStandardShippingService: function() {
        var shipping_service_ids = '';
        var list_shipping_service_ids = [];
        $('#available_shipping_services option::selected').each(function(i, selected){ 
            list_shipping_service_ids[i] = $(selected).val(); 
        });
        shipping_service_ids = list_shipping_service_ids.join();
        $.bindSelect(LocationForm.ajaxUrls.loadStandardShippingServicesUrl, 'type=1&shipping_service_ids=' + shipping_service_ids, 
            'primary_letter_shipping', '', $('#h_primary_letter_shipping').val(), function() {});
        $.bindSelect(LocationForm.ajaxUrls.loadStandardShippingServicesUrl, 'type=2&shipping_service_ids=' + shipping_service_ids, 
            'primary_international_letter_shipping', '', $('#h_primary_international_letter_shipping').val(), function() {});
        $.bindSelect(LocationForm.ajaxUrls.loadStandardShippingServicesUrl, 'type=3&shipping_service_ids=' + shipping_service_ids, 
            'standard_national_parcel_service', '', $('#h_standard_national_parcel_service').val(), function() {});
        $.bindSelect(LocationForm.ajaxUrls.loadStandardShippingServicesUrl, 'type=4&shipping_service_ids=' + shipping_service_ids, 
            'standard_international_parcel_service', '', $('#h_standard_international_parcel_service').val(), function() {});
    },
}
