var ShippingCarriers = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        searchShippingCarriers: null,
        addShippingCarrier: null,
        editShippingCarrier: null,
        deleteShippingCarrier: null
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
        this.searchShippingCarriers();

        // Process when user click to add shipping service button
        $('#btnAddShippingCarrier').click(function (e) {
            e.preventDefault();
            ShippingCarriers.addShippingCarrier();
            return false;
        });

        // Process when user click to edit icon.
        $('.managetables-icon-edit').live('click', function () {
            ShippingCarriers.editShippingCarrier(this);
        });

        // Process when user click to delete icon.
        $('.managetables-icon-delete').live('click', function () {
            ShippingCarriers.deleteShippingCarrier(this);
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.searchShippingCarriers = baseUrl + 'admin/products/shipping_carriers';
        this.ajaxUrls.addShippingCarrier = baseUrl + 'products/admin/add_shipping_carrier';
        this.ajaxUrls.editShippingCarrier = baseUrl + 'products/admin/edit_shipping_carrier';
        this.ajaxUrls.deleteShippingCarrier = baseUrl + 'products/admin/delete_shipping_carrier';
    },

    /**
     * Process when user click to search button
     */
    searchShippingCarriers: function () {
        var tableH = $.getTableHeight();

        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: ShippingCarriers.ajaxUrls.searchShippingCarriers,
            postData: $('#shippingServiceSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH,
            rowNum: ShippingCarriers.configs.rowNum,
            rowList: ShippingCarriers.configs.rowList,
            pager: "#dataGridPager",
            sortname: '',
            sortorder: '',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Code', 'Name', 'Description', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'code', index: 'code', hidden: true},
                {name: 'name', index: 'name', width: 600, sortable: false},
                {name: 'description', index: 'description', width: 950, sortable: false},
                {
                    name: 'id',
                    index: 'id',
                    width: 250,
                    sortable: false,
                    align: "center",
                    formatter: ShippingCarriers.actionFormatter
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
        if (cellvalue === null) {
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
    addShippingCarrier: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addShippingCarrier').openDialog({
            autoOpen: false,
            height: 400,
            width: 550,
            modal: true,
            open: function () {
                $(this).load(ShippingCarriers.ajaxUrls.addShippingCarrier, function () {
                    $('#addEditShippingCarrierForm_code').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingCarriers.saveShippingService();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addShippingCarrier').dialog('option', 'position', 'center');
        $('#addShippingCarrier').dialog('open');
    },

    /**
     * Process when user click to edit icon.
     */
    editShippingCarrier: function (elem) {
        var id = $(elem).attr('data-id');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editShippingCarrier').openDialog({
            autoOpen: false,
            height: 400,
            width: 550,
            modal: true,
            open: function () {
                var loadUrl = ShippingCarriers.ajaxUrls.editShippingCarrier + '?id=' + id;
                $(this).load(loadUrl, function () {
                    $('#addEditShippingCarrierForm_code').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingCarriers.saveShippingService();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $('#editShippingCarrier').dialog('option', 'position', 'center');
        $('#editShippingCarrier').dialog('open');
    },

    /**
     * Save Shipping Service
     */
    saveShippingService: function () {
        var submitUrl = $('#addEditShippingCarrierForm').attr('action');
        var action_type = $('#h_action_type').val();
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditShippingCarrierForm',
            success: function (data) {
                if (data.status) {
                    if (action_type === 'add') {
                        $('#addShippingCarrier').dialog('close');
                    } else if (action_type === 'edit') {
                        $('#editShippingCarrier').dialog('close');
                    }

                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        ShippingCarriers.searchShippingCarriers();
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
    deleteShippingCarrier: function (elem) {
        var id = $(elem).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this shipping carrier?',
            yes: function () {
                var submitUrl = ShippingCarriers.ajaxUrls.deleteShippingCarrier + '?id=' + id;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            ShippingCarriers.searchShippingCarriers();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }
}