var VoiceApp = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        VoiceApp: '',
        searchVoiceApp: '',
        addVoiceApp: '',
        deleteVoiceApp: '',
        changeVoiceAppResponse: ''
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
        this.ajaxUrls.VoiceApp = baseUrl + 'account/voiceapp';
        this.ajaxUrls.searchVoiceApp = baseUrl + 'account/voiceapp/search';
        this.ajaxUrls.addVoiceApp = baseUrl + 'account/voiceapp/add';
        this.ajaxUrls.deleteVoiceApp = baseUrl + 'account/voiceapp/delete';
        this.ajaxUrls.changeVoiceAppResponse = baseUrl + 'account/voiceapp/edit_setting';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        VoiceApp.initAjaxUrls(baseUrl);

        // init config.
        VoiceApp.configs.baseUrl = baseUrl;
        VoiceApp.configs.rowList = rowList.split(',');
        VoiceApp.configs.rowNum = rowNum;

        // init screen
        VoiceApp.searchVoiceApp();

        // add new user
        $("#btnAddNewVoiceApp").click(function () {
            VoiceApp.addNewVoiceApp();
        });

        $('.managetables-delete').live('click', function () {
            var user_id = $(this).attr('data-id');
            VoiceApp.deleteVoiceApp(user_id);
        });
        
        $('.managetables-edit').live('click', function () {
            var voice_app_id = $(this).attr('data-id');
            VoiceApp.changeVoiceAppResponse(voice_app_id);
        });
    },
    searchVoiceApp: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: VoiceApp.ajaxUrls.searchVoiceApp,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: VoiceApp.configs.rowNum,
            rowList: VoiceApp.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'app_type',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Application', 'Name', 'In Use', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'app_type', index: 'app_type', width: 150},
                {name: 'name', index: 'name', width: 175, sortable: false},
                {name: 'use_flag', index: 'use_flag', width: 120, align: "center", formatter: VoiceApp.useFlagFormater},
                {name: 'id', index: 'id', width: 120, sortable: false, align: "center", formatter: VoiceApp.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return  '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-edit" data-id="' + cellvalue + '" title="Edit"></span></span>' +
                    '<span style="display:inline-block;"><span class="fa fa-times managetables-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
        } else {
            return '';
        }
    },
    useFlagFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === 1) {
            return 'Yes';
        }
        return 'No';
    },
    addNewVoiceApp: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addVoiceAppWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(VoiceApp.ajaxUrls.addVoiceApp, function () {
                    $('#addEditVoiceAppForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    VoiceApp.saveVoiceApp();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addVoiceAppWindow').dialog('option', 'position', 'center');
        $('#addVoiceAppWindow').dialog('open');
        return false;
    },
    saveVoiceApp: function () {
        var submitUrl = $('#addEditVoiceAppForm').attr('action');
        var action_type = $('#h_action_type').val();
        var voice_app_id = $('#addEditVoiceAppForm_id').val();
        if (action_type === 'edit_setting') {
            submitUrl += "/" + voice_app_id;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditVoiceAppForm',
            success: function (data) {
                if (data.status) {
                    if (action_type === 'add') {
                        $('#addVoiceAppWindow').dialog('close');
                    } else {
                        $('#changeVoiceAppResponseWindow').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        VoiceApp.searchVoiceApp();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    changeVoiceAppResponse: function (voice_app_id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#changeVoiceAppResponseWindow').openDialog({
            autoOpen: false,
            height: 570,
            width: 850,
            modal: true,
            open: function () {
                $(this).load(VoiceApp.ajaxUrls.changeVoiceAppResponse + "/" + voice_app_id, function () {
                });
            },
            buttons: {
                'Save': function () {
                    VoiceApp.saveVoiceApp();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changeVoiceAppResponseWindow').dialog('option', 'position', 'center');
        $('#changeVoiceAppResponseWindow').dialog('open');
    },
    deleteVoiceApp: function (id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this application? ',
            yes: function () {
                $.ajaxExec({
                    url: VoiceApp.ajaxUrls.deleteVoiceApp + "/" + id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            VoiceApp.searchVoiceApp();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }

};