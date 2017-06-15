var ShippingAPIList = {

    /*
     * Ajax URLs
     */
    ajaxUrls: {
        ShippingAPIList: null,
        addShippingAPI: null,
        editShippingAPI: null,
        deleteShippingAPI: null
    },

    /*
     * Configuration values
     */
    configs: {
        rowNum: null,
        rowList: null
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        ShippingAPIList.initAjaxUrls(baseUrl);
        ShippingAPIList.initConfigs(rowNum, rowList);
        ShippingAPIList.initScreen();
        ShippingAPIList.initEventListeners();
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.ShippingAPIList = baseUrl + 'settings/api/shipping_apis';
        this.ajaxUrls.addShippingAPI = baseUrl + 'settings/api/add_shipping_api';
        this.ajaxUrls.editShippingAPI = baseUrl + 'settings/api/edit_shipping_api';
        this.ajaxUrls.deleteShippingAPI = baseUrl + 'settings/api/delete_shipping_api';
    },

    initConfigs: function (rowNum, rowList) {
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;
    },

    initScreen: function () {
        $('button').button();

        // Call search method
        ShippingAPIList.searchShippingAPIList();
    },

    initEventListeners: function () {
        // Process when user click to search button
        $('#searchShippingButton').click(function(e) {
            ShippingAPIList.searchShippingAPIList();
            e.preventDefault();
        });

        // Process when user click to add group button
        $('#addShippingButton').click(function(e) {
            ShippingAPIList.addShippingAPI();
            e.preventDefault();
        });

        // Process when user click to edit icon.
        $('.managetables-icon-edit').live('click', function(e) {
            ShippingAPIList.editShippingAPI(this);
            e.preventDefault();
        });

        // Process when user click to delete icon.
        $('.managetables-icon-delete').live('click', function(e) {
            ShippingAPIList.deleteShippingAPI(this);
            e.preventDefault();
        });
    },

    searchShippingAPIList: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        
        var tableH = $.getTableHeight(); //#1297 check all tables in the system to minimize wasted space

        $("#dataGridResult").jqGrid({
            url: ShippingAPIList.ajaxUrls.ShippingAPIList,
            postData: $('#ShippingAPIListearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            rowNum: ShippingAPIList.configs.rowNum,
            rowList: ShippingAPIList.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: false,
            rownumbers: true,
            captions: '',
            colNames: ['', 'Name', 'Description', 'Account No', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'name', index: 'name', width: 500},
                {name: 'description', index: 'description', width: 900},
                {name: 'account_no', index: 'account_no', width: 300},
                {name: 'action', index: 'action', sortable: false, align: "center", width: 120, formatter: ShippingAPIList.actionFormatter}
            ],

            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
            },

            loadComplete: function () {
                $.autoFitScreen($( window ).width()- 40);  //#1297 check all tables in the system to minimize wasted space
            }
        });
    },

    actionFormatter: function (cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
            + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
    },

    addShippingAPI: function () {
        var $addShippingAPI = $('#addShippingAPI');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $addShippingAPI.openDialog({
            autoOpen: false,
            height: 550,
            width: 1100,
            modal: true,
            open: function () {
                $(this).load(ShippingAPIList.ajaxUrls.addShippingAPI, function () {
                    $('#addEditShippingAPIForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingAPIList.saveShippingAPI();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $addShippingAPI.dialog('option', 'position', 'center');
        $addShippingAPI.dialog('open');

        return false;
    },

    editShippingAPI: function (elem) {
        var id = $(elem).attr('data-id');
        var $editShippingAPI = $('#editShippingAPI');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $editShippingAPI.openDialog({
            autoOpen: false,
            height: 550,
            width: 1100,
            modal: true,
            open: function () {
                var loadURL = ShippingAPIList.ajaxUrls.editShippingAPI + '?id=' + id;
                $(this).load(loadURL, function () {
                    $('#addEditShippingAPIForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingAPIList.saveShippingAPI();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $editShippingAPI.dialog('option', 'position', 'center');
        $editShippingAPI.dialog('open');
    },

    deleteShippingAPI: function (elem) {
        var id = $(elem).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this shipping api?',
            yes: function () {
                var submitUrl = ShippingAPIList.ajaxUrls.deleteShippingAPI + '?id=' + id;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            //ShippingAPIList.searchShippingAPIList();
                            document.location = ShippingAPIList.ajaxUrls.ShippingAPIList;
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },

    saveShippingAPI: function () {
        var submitUrl = $('#addEditShippingAPIForm').attr('action');
        var actionType = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditShippingAPIForm',
            success: function (data) {
                if (data.status) {
                    if (actionType == 'add') {
                        $('#addShippingAPI').dialog('close');
                    } else if (actionType == 'edit') {
                        $('#editShippingAPI').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        //ShippingAPIList.searchShippingAPIList();
                        document.location = ShippingAPIList.ajaxUrls.ShippingAPIList;
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
}