var HandlingRules = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        HandlingRules: '',
        searchHandlingRules: '',
        addHandlingRules: '',
        deleteHandlingRules: '',
        changeHandlingRulesResponse: ''
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
        this.ajaxUrls.load_handling_rules = baseUrl + 'account/users/handling_rules';
        this.ajaxUrls.change_call_setting_phone_users = baseUrl + 'account/users/change_call_setting_phone_users';
        this.ajaxUrls.change_outgoing = baseUrl + 'account/users/change_outgoing';
        this.ajaxUrls.change_phone_number_setting = baseUrl + 'account/number/change_phone_number_setting';
        this.ajaxUrls.deactivate_handling_rule = baseUrl + 'account/number/deactivate_handling_rule';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        HandlingRules.initAjaxUrls(baseUrl);

        // init config.
        HandlingRules.configs.baseUrl = baseUrl;
        HandlingRules.configs.rowList = rowList.split(',');
        HandlingRules.configs.rowNum = rowNum;

        // init screen
        HandlingRules.searchHandlingRules();
        
        
        
        // User click to call to setting
        $('.managetables-icon-phone-setting-edit').live('click', function(){
            var phone_number = $(this).attr('data-id');
            HandlingRules.changePhoneNumberSetting(phone_number);
            return false;
        });
        
        $('.managetables-delete').live('click', function () {
            var number_id = $(this).attr('data-id');
            HandlingRules.deActivateHandlingRule(number_id);
        });
        
        // ---------------------------------------------------------------------
        // DON'T USE NOW
        // User click to call to setting
        $('.handling_rule_action').live('click', function(){
            var phone_user_id = $(this).attr('data-id');
            HandlingRules.changeCallToSetting(phone_user_id);
            return false;
        });
        
        // User click to call to setting
        $('.handling_rule_show_target').live('click', function(){
            var phone_user_id = $(this).attr('data-id');
            HandlingRules.changeCallOutGoing(phone_user_id);
            return false;
        });
        // ---------------------------------------------------------------------
    },
    // Search handling rule
    searchHandlingRules: function() {
        var user_id = $('#addEditUserForm_id').val();
        var url = HandlingRules.ajaxUrls.load_handling_rules + '?customer_id=' + user_id;
        $("#dataGridHandlingRuleResult").jqGrid('GridUnload');
        $("#dataGridHandlingRuleResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: '100%',
            rowNum: HandlingRules.configs.rowNum,
            rowList: HandlingRules.configs.rowList,
            pager: "#dataGridHandlingRulePager",
            sortname: '',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Number', 'Country', 'City', 'Target Type', 'Target To', 'Status', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'number', index: 'number', width: 120, sortable: false},
                {name: 'country', index: 'country', width: 150, sortable: false},
                {name: 'city', index: 'city', width: 180, sortable: false},
                {name: 'connect_to_type', index: 'connect_to_type', width: 140, sortable: false},
                {name: 'connect_to', index: 'connect_to', width: 140, align: "left", sortable: false},
                {name: 'status', index: 'status', width: 70, sortable: false, align: "center"},
                {name: 'number', index: 'number', width: 70, sortable: false, align: "center", formatter: HandlingRules.actionFormater}
            ],
            loadComplete: function () {
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-phone-setting-edit" data-id="' + cellvalue + '" title="Change Connection Setting"></span></span>'
                    + '<span style="display:inline-block;"><span class="fa fa-times managetables-delete" data-id="' + rowObject[0] + '" title="Deactivate"></span></span>';
        } else {
            return '';
        }
    },
    
    // Change CallToSetting
    changePhoneNumberSetting: function(phone_number) {
        console.log(phone_number);
        // Clear control of all dialog form
        $('#changePhoneNumberSettingWindow').html('');
        
        var changeUrl = HandlingRules.ajaxUrls.change_phone_number_setting + '?phone_number='+phone_number;
        // Open new dialog
        $('#changePhoneNumberSettingWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 600,
            modal: true,
            open: function () {
                $(this).load(changeUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    HandlingRules.saveChangePhoneNumberSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changePhoneNumberSettingWindow').dialog('option', 'position', 'center');
        $('#changePhoneNumberSettingWindow').dialog('open');
    },
    // Save change call to setting
    saveChangePhoneNumberSetting: function() {
        var submitUrl = $('#changePhoneNumberConnectionSettingForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changePhoneNumberConnectionSettingForm',
            success: function (data) {
                if (data.status) {
                    $('#callToSettingWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        $('#changePhoneNumberSettingWindow').dialog('close');
                        HandlingRules.searchHandlingRules();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
    
    // DON'T USE THIS PART 
    // -------------------------------------------------------------------------
    handlingRuleActionFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<a style="text-decoration: underline" class="handling_rule_action" data-id="' + rowObject[1] + '">' + cellvalue + '</a>';
        } else {
            return '';
        }
    },
    handlingRuleShowTargetFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<a style="text-decoration: underline" class="handling_rule_show_target" data-id="' + rowObject[1] + '">' + cellvalue + '</a>';
        } else {
            return '';
        }
    },
    // Change CallToSetting
    changeCallToSetting: function(phone_user_id) {
        // Clear control of all dialog form
        $('#callToSettingWindow').html('');
        var userId = $('#addEditUserForm_id').val();
        var changeCallToSettingUrl = HandlingRules.ajaxUrls.change_call_setting_phone_users + '?customer_id='+userId + '&phone_user_id=' + phone_user_id;
        // Open new dialog
        $('#callToSettingWindow').openDialog({
            autoOpen: false,
            height: 380,
            width: 700,
            modal: true,
            open: function () {
                $(this).load(changeCallToSettingUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    HandlingRules.saveChangeCallToSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#callToSettingWindow').dialog('option', 'position', 'center');
        $('#callToSettingWindow').dialog('open');
    },
    // Save change call to setting
    saveChangeCallToSetting: function() {
        var submitUrl = $('#changeCallToSettingForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeCallToSettingForm',
            success: function (data) {
                if (data.status) {
                    $('#callToSettingWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        HandlingRules.reloadEditUserScreen();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    // Change CallToSetting
    changeCallOutGoing: function(phone_user_id) {
        // Clear control of all dialog form
        $('#callOutGoingWindow').html('');
        var userId = $('#addEditUserForm_id').val();
        var changeCallOutgoingUrl = HandlingRules.ajaxUrls.change_outgoing + '?customer_id='+userId+ '&phone_user_id=' + phone_user_id;
        // Open new dialog
        $('#callOutGoingWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(changeCallOutgoingUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    HandlingRules.saveChangeCallOutGoing();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#callOutGoingWindow').dialog('option', 'position', 'center');
        $('#callOutGoingWindow').dialog('open');
    },
    // Save change call to setting
    saveChangeCallOutGoing: function() {
        var submitUrl = $('#changeOutGoingUserForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeOutGoingUserForm',
            success: function (data) {
                if (data.status) {
                    $('#callOutGoingWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        HandlingRules.reloadEditUserScreen();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deActivateHandlingRule: function (number_id) {
        // Show confirm dialog
        $.confirm({
            message: 'If you deactivate this handling rule, calls from this number will not work until it is activated again.',
            yes: function () {
                $.ajaxExec({
                    url: HandlingRules.ajaxUrls.deactivate_handling_rule + "/" + number_id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            HandlingRules.searchHandlingRules();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }
    // -------------------------------------------------------------------------
};