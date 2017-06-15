var ShippingServices = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        searchShippingServices: null,
        addShippingService: null,
        editShippingService: null,
        deleteShippingService: null
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
        $('#mailbox').css('margin', '20px 0 0 20px');
        $('button').button();

        // Init data
        this.initAjaxUrls(baseUrl);
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;

        // Call search method
        this.searchShippingServices();

        // Event listeners
        // Process when user click to search button
        $('#searchCustomerBlackListButton').click(function (e) {
            ShippingServices.searchShippingServices();
            e.preventDefault();
        });

        // Process when user click to add shipping service button
        $('#btnAddShippingService').click(function (e) {
            e.preventDefault();
            ShippingServices.addShippingService();
            return false;
        });

        // Process when user click to edit icon.
        $('.managetables-icon-edit').live('click', function () {
            ShippingServices.editShippingService(this);
        });

        // Process when user click to delete icon.
        $('.managetables-icon-delete').live('click', function () {
            ShippingServices.deleteShippingService(this);
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.searchShippingServices = baseUrl + 'admin/products/shipping_services';
        this.ajaxUrls.addShippingService = baseUrl + 'products/admin/add_shipping_service';
        this.ajaxUrls.editShippingService = baseUrl + 'products/admin/edit_shipping_service';
        this.ajaxUrls.deleteShippingService = baseUrl + 'products/admin/delete_shipping_service';
    },

    /**
     * Process when user click to search button
     */
    searchShippingServices: function () {
        var tableH = $.getTableHeight();

        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: ShippingServices.ajaxUrls.searchShippingServices,
            postData: $('#shippingServiceSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space,
            rowNum: ShippingServices.configs.rowNum,
            rowList: ShippingServices.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Name', 'Description', 'Text', 'Logo', 'API', 'Factor A', 'Factor B', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'name', index: 'name', width: 250, sortable: true},
                {name: 'description', index: 'description', width: 340, sortable: false},
                {name: 'text', index: 'text', width: 500, sortable: false},
                {name: 'logo', index: 'logo', width: 300, formatter: ShippingServices.toLogoFormatter},
                {name: 'api', index: 'api', width: 105, sortable: false},
                {name: 'factor_a', index: 'factor_a', width: 100, sortable: false},
                {name: 'factor_b', index: 'factor_b', width: 100, sortable: false},
                {
                    name: 'id',
                    index: 'id',
                    width: 80,
                    sortable: false,
                    align: "center",
                    formatter: ShippingServices.actionFormatter
                }
            ],
            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
                // var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
            },
            loadComplete: function () {
                $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    },

    toLogoFormatter: function (cellvalue, options, rowObject) {
        if (cellvalue == null) {
            return '';
        }
        return cellvalue;
    },

    actionFormatter: function (cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
            + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
    },

    /**
     * Process when user click to add shipping service
     */
    addShippingService: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addShippingService').openDialog({
            autoOpen: false,
            height: 585,
            width: 1050,
            modal: true,
            open: function () {
                $(this).load(ShippingServices.ajaxUrls.addShippingService, function () {
                    $('#addEditShippingServiceForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingServices.saveShippingService();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addShippingService').dialog('option', 'position', 'center');
        $('#addShippingService').dialog('open');
    },

    /**
     * Process when user click to edit icon.
     */
    editShippingService: function (elem) {
        var id = $(elem).attr('data-id');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editShippingService').openDialog({
            autoOpen: false,
            height: 585,
            width: 1050,
            modal: true,
            open: function () {
                var loadUrl = ShippingServices.ajaxUrls.editShippingService + '?id=' + id;
                $(this).load(loadUrl, function () {
                    $('#addEditShippingAPIForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingServices.saveShippingService();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $('#editShippingService').dialog('option', 'position', 'center');
        $('#editShippingService').dialog('open');
    },

    /**
     * Save Shipping Service
     */
    saveShippingService: function () {
        var submitUrl = $('#addEditShippingServiceForm').attr('action');
        var action_type = $('#h_action_type').val();
        var shippingApiCodeData = [];
        var shippingApiCredentialData = [];
        
        var shippingApis = $("#shipping_api_code tbody" ).find('tr');
        if (shippingApis){
            $.each(shippingApis, function (index, dataRow) {
                $dataRow = $(dataRow);
                var api_id = $dataRow.find('td.api_id').html();
                var service_code = $dataRow.find('td.service_code').html()
                shippingApiCodeData.push({api_id : api_id, service_code : service_code});
            
            });
        }
        
        var shippingCredentials = $("#shipping_api_credential tbody" ).find('tr');
        if (shippingCredentials){
            $.each(shippingCredentials, function (index, dataRow) {
                $dataRow = $(dataRow);
                var api_id = $dataRow.find('td.api_id').html();
                var credential_id = $dataRow.find('td.credential_id').html()
                shippingApiCredentialData.push({api_id : api_id, credential_id : credential_id});
            
            });
        }
        
        var formData = $("#addEditShippingServiceForm").serializeArray();
        formData.push({name: 'shippingApiCode', value: JSON.stringify(shippingApiCodeData)});
        formData.push({name: 'shippingApiCredential', value: JSON.stringify(shippingApiCredentialData)});
        
        $.ajaxExec({
            url: submitUrl,
            data: formData,
            success: function (data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#addShippingService').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#editShippingService').dialog('close');
                    }

                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        ShippingServices.searchShippingServices();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
        
    },

    /**
     * Process when user click to delete icon.
     */
    deleteShippingService: function (elem) {
        var id = $(elem).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this shipping service?',
            yes: function () {
                var submitUrl = ShippingServices.ajaxUrls.deleteShippingService + '?id=' + id;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            ShippingServices.searchShippingServices();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }
}