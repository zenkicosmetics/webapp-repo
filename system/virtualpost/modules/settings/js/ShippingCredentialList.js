var ShippingCredentialList = {

    /*
     * Ajax URLs
     */
    ajaxUrls: {
        ShippingCredentialList: null,
        addShippingCredential: null,
        editShippingCredential: null,
        deleteShippingCredential: null
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
        ShippingCredentialList.initAjaxUrls(baseUrl);
        ShippingCredentialList.initConfigs(rowNum, rowList);
        ShippingCredentialList.initScreen();
        ShippingCredentialList.initEventListeners();
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.ShippingCredentialList = baseUrl + 'settings/api/shipping_credentials';
        this.ajaxUrls.addShippingCredential = baseUrl + 'settings/api/add_shipping_credential';
        this.ajaxUrls.editShippingCredential = baseUrl + 'settings/api/edit_shipping_credential';
        this.ajaxUrls.deleteShippingCredential = baseUrl + 'settings/api/delete_shipping_credential';
    },

    initConfigs: function (rowNum, rowList) {
        this.configs.rowNum = rowNum;
        this.configs.rowList = rowList;
    },

    initScreen: function () {
        $('button').button();

        // Call search method
        ShippingCredentialList.searchShippingCredentialList();
    },

    initEventListeners: function () {
        // Process when user click to search button
        $('#searchShippingButton').click(function(e) {
            ShippingCredentialList.searchShippingCredentialList();
            e.preventDefault();
        });

        // Process when user click to add group button
        $('#addShippingButton').click(function(e) {
            ShippingCredentialList.addShippingCredential();
            e.preventDefault();
        });

        // Process when user click to edit icon.
        $('.managetables-icon-edit').live('click', function(e) {
            ShippingCredentialList.editShippingCredential(this);
            e.preventDefault();
        });

        // Process when user click to delete icon.
        $('.managetables-icon-delete').live('click', function(e) {
            ShippingCredentialList.deleteShippingCredential(this);
            e.preventDefault();
        });
    },

    searchShippingCredentialList: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        
        var tableH = $.getTableHeight(); //#1297 check all tables in the system to minimize wasted space

        $("#dataGridResult").jqGrid({
            url: ShippingCredentialList.ajaxUrls.ShippingCredentialList,
            postData: $('#shippingCredentialSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH,
            rowNum: ShippingCredentialList.configs.rowNum,
            rowList: ShippingCredentialList.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: false,
            rownumbers: true,
            captions: '',
            colNames: ['', 'Name', 'Description', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'name', index: 'name', width: 640},
                {name: 'description', index: 'description', width: 990},
                {name: 'action', index: 'action', sortable: false, align: "center", width: 200, formatter: ShippingCredentialList.actionFormatter}
            ],

            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
            },

            loadComplete: function () {
                $.autoFitScreen($( window ).width()- 40);
            }
        });
    },

    actionFormatter: function (cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + rowObject[0] + '" title="Edit"></span></span>'
            + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + rowObject[0] + '" title="Delete"></span></span>';
    },

    addShippingCredential: function () {
        var $addShippingCredential = $('#addShippingCredential');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $addShippingCredential.openDialog({
            autoOpen: false,
            height: 490,
            width: 1050,
            modal: true,
            open: function () {
                $(this).load(ShippingCredentialList.ajaxUrls.addShippingCredential, function () {
                    $('#addEditShippingCredentialForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingCredentialList.saveShippingCredential();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $addShippingCredential.dialog('option', 'position', 'center');
        $addShippingCredential.dialog('open');

        return false;
    },

    editShippingCredential: function (elem) {
        var id = $(elem).attr('data-id');
        var $editShippingCredential = $('#editShippingCredential');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $editShippingCredential.openDialog({
            autoOpen: false,
            height: 490,
            width: 1050,
            modal: true,
            open: function () {
                var loadURL = ShippingCredentialList.ajaxUrls.editShippingCredential + '?id=' + id;
                $(this).load(loadURL, function () {
                    $('#addEditShippingCredentialForm_name').focus();
                });
            },
            buttons: {
                'Save': function () {
                    ShippingCredentialList.saveShippingCredential();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });

        $editShippingCredential.dialog('option', 'position', 'center');
        $editShippingCredential.dialog('open');
    },

    deleteShippingCredential: function (elem) {
        var id = $(elem).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this shipping Credential?',
            yes: function () {
                var submitUrl = ShippingCredentialList.ajaxUrls.deleteShippingCredential + '?id=' + id;
                $.ajaxExec({
                    url: submitUrl,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            //ShippingCredentialList.searchShippingCredentialList();
                            document.location = ShippingCredentialList.ajaxUrls.ShippingCredentialList;
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },

    saveShippingCredential: function () {
        var submitUrl = $('#addEditShippingCredentialForm').attr('action');
        var actionType = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditShippingCredentialForm',
            success: function (data) {
                if (data.status) {
                    if (actionType == 'add') {
                        $('#addShippingCredential').dialog('close');
                    } else if (actionType == 'edit') {
                        $('#editShippingCredential').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        //ShippingCredentialList.searchShippingCredentialList();
                        document.location = ShippingCredentialList.ajaxUrls.ShippingCredentialList;
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
}