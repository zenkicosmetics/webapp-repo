var LocationList = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        index: null,
        addLocation: null,
        editLocation: null,
        deleteLocation: null,
        deleteAllPostboxesLocation: null
    },

    configs: {
        rowNum: null,
        rowList: null
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        $('#mailbox').css('margin', '20px 0 0 20px');
        $('#addLocationButton').button();
		
        LocationList.initAjaxUrls(baseUrl);
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;

        LocationList.searchLocations();

        // Process when user click to add group button
        $('#addLocationButton').click(function () {
            LocationList.addLocation();
        });

        // Process when user click to edit icon.
        $('.managetables-icon-edit').live('click', function () {
            LocationList.editLocation($(this).data('id'));
        });

        // Process when user click to delete icon.
        $('.managetables-icon-delete').live('click', function () {
            LocationList.deleteLocation($(this).data('id'));
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.index = baseUrl + 'settings/locations/index';
        this.ajaxUrls.addLocation = baseUrl + 'settings/locations/add';
        this.ajaxUrls.editLocation = baseUrl + 'settings/locations/edit';
        this.ajaxUrls.deleteLocation = baseUrl + 'settings/locations/delete';
        this.ajaxUrls.deleteAllPostboxesLocation = baseUrl + 'settings/locations/delete_all_postbox';
    },

    /**
     * Search data
     */
    searchLocations: function () {
        
        $("#dataGridResult").jqGrid('GridUnload');
        
        //#1297 check all tables in the system to minimize wasted space
        var tableH = $.getTableHeight() - 31;
        
        $("#dataGridResult").jqGrid({
            url: LocationList.ajaxUrls.index,
            postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: LocationList.configs.rowNum,
            rowList: LocationList.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'location_name',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['ID', 'Location Name', 'Partner Code', 'Location Type', 'Partner Name', 'Country', 'City', 'Region', 'Street', 'Post Code', 'Available', 'Rev share (%)',  'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'Location Name', index: 'location_name', width: 160},
                {name: 'Partner Code', index: 'partner_code', width: 150},
                {name: 'location_type', index: 'location_type', width: 150, sortable: false},
                {name: 'Partner Name', index: 'partner_name', width: 250},
                {name: 'Country', index: 'country_name', width: 150},
                {name: 'City', index: 'city', width: 150},
                {name: 'Region', index: 'region', width: 150},
                {name: 'Street', index: 'street', width: 350},
                {name: 'Post Code', index: 'postcode', width: 120},
                {
                    name: 'Available',
                    index: 'Public_Flag',
                    width: 100,
                    align: "center",
                    formatter: LocationList.activeFormater
                },
                {name: 'rev_share', index: 'rev_share', width: 100, align: "center"},
                {
                    name: 'id',
                    index: 'id',
                    width: 100,
                    sortable: false,
                    align: "center",
                    formatter: LocationList.actionFormater
                }
            ],

            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                console.log(data_row);
            },
            loadComplete: function () {
                $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
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

    actionFormater: function (cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
            + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
    },

    addLocation: function () {
        // Clear control of all dialog form
        
        $('.dialog-form').html('');
        // Open new dialog
        $('#addLocation').openDialog({
            autoOpen: false,
            height: 620,
            width: 1000,
            modal: true,
            open: function () {
                $(this).load(LocationList.ajaxUrls.addLocation, function () {
                    $('#addEditLocationForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function () {
                    LocationList.saveLocation();
                },
                'Cancel': function () {
                    $(this).dialog('dispose');
                    $(this).dialog('close');
                }
            },
            close: function() {
                location.reload();
            }
        });
        $('#addLocation').dialog('option', 'position', 'center');
        $('#addLocation').dialog('open');
    },

    editLocation: function (location_id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editLocation').openDialog({
            autoOpen: false,
            height: 620,
            width: 1000,
            modal: true,
            open: function () {
                var submitUrl = LocationList.ajaxUrls.editLocation + '?id=' + location_id;
                $(this).load(submitUrl, function () {
                    $('#addEditLocationForm_LocationName').focus();
                });
            },
            buttons: {
                'Save': function () {
                    LocationList.saveLocation();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editLocation').dialog('option', 'position', 'center');
        $('#editLocation').dialog('open');
    },

    deleteLocation: function (location_id) {
        // Show confirm dialog
        $.confirm({
            message: "Do you want to delete all postboxes from this location?",
            yes: function () {
                $.ajaxExec({
                    url: LocationList.ajaxUrls.deleteAllPostboxesLocation,
                    data: {id: location_id},
                    success: function (data) {
                        if(data.status){
                            // Reload data grid
                            LocationList.searchLocations();
                        }else{
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },

    /**
     * Save Location
     */
    saveLocation: function () {
        // selected all options
        $('#available_shipping_services > option').prop('selected', true);
        $('#pricing_template_id > option').prop('selected', true);
        var submitUrl = $('#addEditLocationForm').attr('action');
        var action_type = $('#h_action_type').val();
        var data = $('#addEditLocationForm').serializeObject();
        var list_type_available_arr = []; 
        $('#list_type_available option').each(function() {
            list_type_available_arr.push($(this).val());
        });
        
        // validate offical phone nnumber
        var message = "";
        var phone_flag = $("#phone_number_flag").is(":checked");
        var phone_number = $("#phone_number").val();
        if(phone_flag == true && $.isEmpty(phone_number)){
            message += "The offical phone number is required<br/>";
        }

        var office_space_flag = $("#office_space_active_flag").is(":checked");
        var booking_email_address = $("#booking_email_address").val();
        if(office_space_flag == true && $.isEmpty(booking_email_address)){
            message += "The email address is required<br/>";
        }
        
        if(message != ""){
            $.displayError(message);
            return;
        }
        
        //var image_file_name = $('#imagepath_id').val();
        $.ajaxExec({
            url: submitUrl,
            data: $.extend({}, data, {list_type_available2 : list_type_available_arr}),
            success: function (data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#addLocation').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#editLocation').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        LocationList.searchLocations();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
        
    }
}
