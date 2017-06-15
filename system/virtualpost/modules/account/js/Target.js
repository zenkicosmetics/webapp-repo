var Target = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        number: '',
        searchTarget: '',
        addTarget: '',
        editTarget: '',
        deleteTarget: ''
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
        this.ajaxUrls.number = baseUrl + 'account/target';
        this.ajaxUrls.searchTarget = baseUrl + 'account/target/search';
        this.ajaxUrls.addTarget = baseUrl + 'account/target/add';
        this.ajaxUrls.editTarget = baseUrl + 'account/target/edit';
        this.ajaxUrls.deleteTarget = baseUrl + 'account/target/delete';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        Target.initAjaxUrls(baseUrl);

        // init config.
        Target.configs.baseUrl = baseUrl;
        Target.configs.rowList = rowList.split(',');
        Target.configs.rowNum = rowNum;

        // init screen
        Target.searchTarget();

        // add new user
        $("#btnAddNewPhone").click(function () {
            Target.addNewTarget();
        });

        $('.managetables-delete').live('click', function () {
            var id = $(this).attr('data-id');
            Target.deleteTarget(id);
        });
    },
    searchTarget: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: Target.ajaxUrls.searchTarget,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: Target.configs.rowNum,
            rowList: Target.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Name', 'Type', 'Target', 'Associated with handling rule', 'Created Date', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'target_name', index: 'target_name', width: 200},
                {name: 'target_type', index: 'target_type', width: 100, sortable: false},
                {name: 'target_id', index: 'target_id', width: 240, sortable: false},
                {name: 'use_flag', index: 'use_flag', width: 200, sortable: false, align: "center"},
                {name: 'created_date', index: 'created_date', width: 110},
                {name: 'id', index: 'id', width: 100, sortable: false, align: "center", formatter: Target.actionFormater}
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
    addNewTarget: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addTargetWindow').openDialog({
            autoOpen: false,
            height: 350,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(Target.ajaxUrls.addTarget, function () {
                    $('#addEditTargetForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    Target.saveTarget();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addTargetWindow').dialog('option', 'position', 'center');
        $('#addTargetWindow').dialog('open');
        return false;
    },
    saveTarget: function () {
        var submitUrl = $('#addEditTargetForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditTargetForm',
            success: function (data) {
                if (data.status) {
                    $('#addTargetWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        Target.searchTarget();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteTarget: function (id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this target? ',
            yes: function () {
                $.ajaxExec({
                    url: Target.ajaxUrls.deleteTarget + "/" + id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            Target.searchTarget();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }

};