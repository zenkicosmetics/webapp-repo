var Phones = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        number: '',
        searchPhone: '',
        addPhone: '',
        deletePhone: ''
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
        this.ajaxUrls.number = baseUrl + 'account/phones';
        this.ajaxUrls.searchPhone = baseUrl + 'account/phones/search';
        this.ajaxUrls.addPhone = baseUrl + 'account/phones/add';
        this.ajaxUrls.deletePhone = baseUrl + 'account/phones/delete';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        Phones.initAjaxUrls(baseUrl);

        // init config.
        Phones.configs.baseUrl = baseUrl;
        Phones.configs.rowList = rowList.split(',');
        Phones.configs.rowNum = rowNum;

        // init screen
        Phones.searchPhone();

        // add new user
        $("#btnAddNewPhone").click(function () {
            Phones.addNewPhone();
        });

        $('.managetables-delete').live('click', function () {
            var id = $(this).attr('data-id');
            Phones.deletePhone(id);
        });
    },
    searchPhone: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: Phones.ajaxUrls.searchPhone,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: Phones.configs.rowNum,
            rowList: Phones.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Name', 'Type', 'Phone number', 'Created Date', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'phone_name', index: 'phone_name', width: 200},
                {name: 'phone_type', index: 'phone_type', width: 100, sortable: false},
                {name: 'phone_number', index: 'phone_number', width: 200, sortable: false},
                {name: 'created_date', index: 'created_date', width: 120},
                {name: 'id', index: 'id', width: 120, sortable: false, align: "center", formatter: Phones.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-times managetables-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
        } else {
            return '';
        }
    },
    addNewPhone: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addPhoneWindow').openDialog({
            autoOpen: false,
            height: 310,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(Phones.ajaxUrls.addPhone, function () {
                    $('#addEditPhoneForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    Phones.savePhone();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addPhoneWindow').dialog('option', 'position', 'center');
        $('#addPhoneWindow').dialog('open');
        return false;
    },
    savePhone: function () {
        var submitUrl = $('#addEditPhoneForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditPhoneForm',
            success: function (data) {
                if (data.status) {
                    $('#addPhoneWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        Phones.searchPhone();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deletePhone: function (id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this phone? ',
            yes: function () {
                $.ajaxExec({
                    url: Phones.ajaxUrls.deletePhone + "/" + id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            Phones.searchPhone();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }

};