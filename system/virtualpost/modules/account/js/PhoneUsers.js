var PhoneUsers = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        user: '',
        searchUser: '',
        addUser: '',
        editUser: '',
        deleteUser: '',
        changePassword: '',
        change_email_address: '',
        resend_email_confirm: '',
        assign_postbox: ''
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
        can_not_change_postbox_account_not_activated: 'You can not change your postbox type.<br/>Please complete registration process.'
    },
    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.user = baseUrl + 'account/users/general_users';
        this.ajaxUrls.searchUser = baseUrl + 'account/users/search_users?product_type=phone';
        this.ajaxUrls.addUser = baseUrl + 'account/users/add_general_users?product_type=phone';
        this.ajaxUrls.editUser = baseUrl + 'account/users/edit_phone_users?product_type=phone';
        this.ajaxUrls.deleteUser = baseUrl + 'account/users/delete_general_users';
        this.ajaxUrls.changePassword = baseUrl + 'account/users/change_password_general_users';
        this.ajaxUrls.change_email_address = baseUrl + 'account/users/change_my_email';
        this.ajaxUrls.resend_email_confirm = baseUrl + 'account/users/resend_email_confirm';
        this.ajaxUrls.assign_postbox = baseUrl + 'account/users/assign_postbox?product_type=phone';
        this.ajaxUrls.load_phonenumber_users = baseUrl + 'account/users/load_phonenumber_users';
        this.ajaxUrls.load_phones_users = baseUrl + 'account/users/load_phones_users';
        this.ajaxUrls.change_location_area = baseUrl + 'account/users/change_location_area';
        this.ajaxUrls.change_call_setting_phone_users = baseUrl + 'account/users/change_call_setting_phone_users';
        this.ajaxUrls.change_outgoing = baseUrl + 'account/users/change_outgoing';
        this.ajaxUrls.assign_phone_number = baseUrl + 'account/users/assign_phone_number';
        this.ajaxUrls.assign_phones = baseUrl + 'account/users/assign_phones';
        this.ajaxUrls.delete_assign_phone_number = baseUrl + 'account/users/delete_assign_phone_number';
        this.ajaxUrls.delete_assign_phones = baseUrl + 'account/users/delete_assign_phones';
        this.ajaxUrls.load_handling_rules = baseUrl + 'account/users/load_handling_rules';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        PhoneUsers.initAjaxUrls(baseUrl);

        // init config.
        PhoneUsers.configs.baseUrl = baseUrl;
        PhoneUsers.configs.rowList = rowList.split(',');
        PhoneUsers.configs.rowNum = rowNum;

        // 4. Change user location (Don't Use)
        $('#changeUserLocationLink, #changeUserAreaLink').live('click', function () {
            PhoneUsers.changeLocationArea();
            return false;
        });
        
        // User click to call to setting
        $('.handling_rule_action').live('click', function(){
            var phone_user_id = $(this).attr('data-id');
            PhoneUsers.changeCallToSetting(phone_user_id);
            return false;
        });
        
        // User click to call to setting (Don't Use)
        $('#changeUserCallThruActionLink').live('click', function(){
            PhoneUsers.changeCallOutGoing();
            return false;
        });
        
        // User click to add phone number
        $('#addNewPhoneNumberLink').live('click', function(){
            console.log('Click to addNewPhoneNumberLink');
            PhoneUsers.assignPhoneNumber();
            return false;
        });
        
        // User click to add phone number
        $('#addNewPhonesLink').live('click', function(){
            console.log('Click to addNewPhonesLink');
            PhoneUsers.assignPhones();
            return false;
        });
        
        // User click to call to setting
        $('.handling_rule_show_target').live('click', function(){
            var phone_user_id = $(this).attr('data-id');
            PhoneUsers.changeCallOutGoing(phone_user_id);
            return false;
        });
        
        // Delete phone number
        $('.managetables-icon-delete-phonenumber').live('click', function() {
            var phone_number = $(this).attr('data-id');
            var number_id = $(this).attr('data-number-id');
            PhoneUsers.deleteAssignPhoneNumber(phone_number, number_id); 
        });
        
        // Delete phone
        $('.managetables-icon-delete-phones').live('click', function() {
            var phone_id = $(this).attr('data-id');
            PhoneUsers.deleteAssignPhones(phone_id); 
        });
    },
    addNewUser: function () {
        // Clear control of all dialog form
        $('#addUser, #editUser').html('');

        // Open new dialog
        $('#addUser').openDialog({
            autoOpen: false,
            height: 620,
            width: 920,
            modal: true,
            open: function () {
                $(this).load(PhoneUsers.ajaxUrls.addUser, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PhoneUsers.saveUser();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#addUser').dialog('option', 'position', 'center');
        $('#addUser').dialog('open');
        return false;
    },
    editUser: function (user_id) {
        // Clear control of all dialog form
        $('#addUser, #editUser').html('');

        // Open new dialog
        $('#editUser').openDialog({
            autoOpen: false,
            height: 620,
            width: 920,
            modal: true,
            open: function () {
                $(this).load(PhoneUsers.ajaxUrls.editUser + "&customer_id=" + user_id, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PhoneUsers.saveUser();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editUser').dialog('option', 'position', 'center');
        $('#editUser').dialog('open');
    },
    reloadEditUserScreen: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        if (user_id == null || user_id == '') {
            return;
        }
        $('#editUser').load(PhoneUsers.ajaxUrls.editUser + "&customer_id=" + user_id, function () {
            $('#addEditUserForm_email').focus();
        });
    },
    saveUser: function () {
        var submitUrl = $('#addEditUserForm').attr('action');
        var action_type = $('#h_action_type').val();
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditUserForm',
            success: function (data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#addUser').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#editUser').dialog('close');
                    }
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        PhoneUsers.searchUser();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteUser: function (user_id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this user? ',
            yes: function () {
                $.ajaxExec({
                    url: PhoneUsers.ajaxUrls.deleteUser + "/" + user_id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            PhoneUsers.searchUser();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },
    // Search phone number
    searchPhoneNumberProducts: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        var url = PhoneUsers.ajaxUrls.load_phonenumber_users + '?customer_id=' + user_id;
        $("#dataGridPhoneNumberResult").jqGrid('GridUnload');
        $("#dataGridPhoneNumberResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 420,
            height: 90,
            rowNum: PhoneUsers.configs.rowNum,
            rowList: PhoneUsers.configs.rowList,
            pager: "#dataGridPhoneNumberPager",
            sortname: '',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Number', 'Location', 'Action', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'number', index: 'number', width: 120, sortable: false},
                {name: 'location', index: 'location', width: 120, sortable: false},
                {name: 'action', index: 'action', width: 100, align: "left", sortable: false},
                {name: 'number', index: 'number', width: 50, sortable: false, align: "center", formatter: PhoneUsers.phoneNumberActionFormater}
            ],
            loadComplete: function () {
            }
        });
    },
    phoneNumberActionFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete managetables-icon-delete-phonenumber" data-id="' + cellvalue + '" data-number-id="' + rowObject[0] + '" title="Delete"></span></span>';
            // return '';
        } else {
            return '';
        }
    },
    // Search phone number
    searchPhonesProducts: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        var url = PhoneUsers.ajaxUrls.load_phones_users + '?customer_id=' + user_id;
        $("#dataGridPhonesResult").jqGrid('GridUnload');
        $("#dataGridPhonesResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 420,
            height: 90,
            rowNum: PhoneUsers.configs.rowNum,
            rowList: PhoneUsers.configs.rowList,
            pager: "#dataGridPhonesPager",
            sortname: '',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Phone Name', 'Type', 'Number', ''],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'name', index: 'name', width: 140, sortable: false},
                {name: 'type', index: 'type', width: 100, sortable: false},
                {name: 'number', index: 'number', width: 100, align: "left", sortable: false},
                {name: 'id', index: 'id', width: 50, sortable: false, align: "center", formatter: PhoneUsers.phonesActionFormater}
            ],
            loadComplete: function () {
            }
        });
    },
    phonesActionFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete managetables-icon-delete-phones" data-id="' + cellvalue + '" title="Delete"></span></span>';
            // return '';
        } else {
            return '';
        }
    },
    // Search handling rule
    searchHandlingRules: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        var url = PhoneUsers.ajaxUrls.load_handling_rules + '?customer_id=' + user_id;
        $("#dataGridHandlingRuleResult").jqGrid('GridUnload');
        $("#dataGridHandlingRuleResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 860,
            height: 90,
            rowNum: PhoneUsers.configs.rowNum,
            rowList: PhoneUsers.configs.rowList,
            pager: "#dataGridHandlingRulePager",
            sortname: '',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Phone User ID', 'Number', '1st Action', '2nd Action', 'Show to target', 'Status'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'phone_user_id', index: 'phone_user_id', hidden: true},
                {name: 'number', index: 'number', width: 120, sortable: false},
                {name: 'first_action', index: 'first_action', width: 200, sortable: false, formatter: PhoneUsers.handlingRuleActionFormater},
                {name: 'second_action', index: 'second_action', width: 200, align: "left", sortable: false, formatter: PhoneUsers.handlingRuleActionFormater},
                {name: 'show_to_target', index: 'show_to_target', width: 150, sortable: false, align: "left", formatter: PhoneUsers.handlingRuleShowTargetFormater},
                {name: 'status', index: 'status', width: 100, sortable: false, align: "center"}
            ],
            loadComplete: function () {
            }
        });
    },
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
    changeLocationArea: function () {
        var user_id = $('#addEditUserForm_customer_id').val();
        // Clear control of all dialog form
        $('#changeLocationAreaWindow').html('');

        // Open new dialog
        $('#changeLocationAreaWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(PhoneUsers.ajaxUrls.change_location_area + '?customer_id=' + user_id, function () {
                });
            },
            buttons: {
                'Save': function () {
                    PhoneUsers.saveChangeLocationArea();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changeLocationAreaWindow').dialog('option', 'position', 'center');
        $('#changeLocationAreaWindow').dialog('open');
        return false;
    },
    saveChangeLocationArea: function () {
        var submitUrl = $('#changeLocationAreaUserForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeLocationAreaUserForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, '', function (){
                        // Change the label
                        $('#changeUserLocationText').html($('#country_code_3 option:selected').text());
                        $('#changeUserAreaText').html($('#area_code option:selected').text());
                        PhoneUsers.reloadEditUserScreen();
                    });
                    $('#changeLocationAreaWindow').dialog('close');
                    
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    // Change CallToSetting
    changeCallToSetting: function(phone_user_id) {
        // Clear control of all dialog form
        $('#callToSettingWindow').html('');
        var userId = $('#addEditUserForm_customer_id').val();
        var changeCallToSettingUrl = PhoneUsers.ajaxUrls.change_call_setting_phone_users + '?customer_id='+userId + '&phone_user_id=' + phone_user_id;
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
                    PhoneUsers.saveChangeCallToSetting();
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
                        PhoneUsers.reloadEditUserScreen();
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
        var userId = $('#addEditUserForm_customer_id').val();
        var changeCallOutgoingUrl = PhoneUsers.ajaxUrls.change_outgoing + '?customer_id='+userId+ '&phone_user_id=' + phone_user_id;
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
                    PhoneUsers.saveChangeCallOutGoing();
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
                        PhoneUsers.reloadEditUserScreen();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    // Add Phone Number
    assignPhoneNumber: function() {
        // Clear control of all dialog form
        $('#assignPhoneNumberWindow').html('');
        var userId = $('#addEditUserForm_customer_id').val();
        var assignPhoneNumberUrl = PhoneUsers.ajaxUrls.assign_phone_number + '?customer_id='+userId;
        // Open new dialog
        $('#assignPhoneNumberWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(assignPhoneNumberUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    PhoneUsers.saveAssignPhoneNumber();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#assignPhoneNumberWindow').dialog('option', 'position', 'center');
        $('#assignPhoneNumberWindow').dialog('open');
    },
    // Save change call to setting
    saveAssignPhoneNumber: function() {
        var submitUrl = $('#assginPhoneNumberToUserForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'assginPhoneNumberToUserForm',
            success: function (data) {
                if (data.status) {
                    $('#assignPhoneNumberWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        var user_id = $('#addEditUserForm_customer_id').val();
                        if (user_id != '') {
                            PhoneUsers.reloadEditUserScreen();
                        } else {
                            PhoneUsers.searchPhoneNumberProducts();
                            PhoneUsers.searchHandlingRules();
                        }
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteAssignPhoneNumber: function (phone_number, number_id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to remove this phone number? ',
            yes: function () {
                var user_id = $('#addEditUserForm_customer_id').val();
                $.ajaxExec({
                    url: PhoneUsers.ajaxUrls.delete_assign_phone_number + '?phone_number=' + phone_number + '&customer_id=' + user_id + '&number_id=' + number_id,
                    success: function (data) {
                        if (data.status) {
                            $.displayInfor(data.message, null, function() {
                                if (user_id != '') {
                                    PhoneUsers.reloadEditUserScreen();
                                } else {
                                    PhoneUsers.searchPhoneNumberProducts();
                                    PhoneUsers.searchHandlingRules();
                                }
                            });
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },
    deleteAssignPhones: function (phone_id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to remove this phones? ',
            yes: function () {
                var user_id = $('#addEditUserForm_customer_id').val();
                $.ajaxExec({
                    url: PhoneUsers.ajaxUrls.delete_assign_phones + '?phone_id=' + phone_id + '&customer_id=' + user_id,
                    success: function (data) {
                        if (data.status) {
                            $.displayInfor(data.message, null, function() {
                                if (user_id != '') {
                                    PhoneUsers.reloadEditUserScreen();
                                } else {
                                    PhoneUsers.searchPhonesProducts();
                                }
                            });
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },
    // Add Phone Number
    assignPhones: function() {
        // Clear control of all dialog form
        $('#assignPhonesWindow').html('');
        var userId = $('#addEditUserForm_customer_id').val();
        var assignPhonesUrl = PhoneUsers.ajaxUrls.assign_phones + '?customer_id='+userId;
        // Open new dialog
        $('#assignPhonesWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load(assignPhonesUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    PhoneUsers.saveAssignPhones();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#assignPhonesWindow').dialog('option', 'position', 'center');
        $('#assignPhonesWindow').dialog('open');
    },
    // Save change call to setting
    saveAssignPhones: function() {
        var submitUrl = $('#assginPhonesToUserForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'assginPhonesToUserForm',
            success: function (data) {
                if (data.status) {
                    $('#assignPhonesWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        var userId = $('#addEditUserForm_customer_id').val();
                        if (userId != '') {
                            PhoneUsers.reloadEditUserScreen();
                        } else {
                            // PhoneUsers.searchPhoneNumberProducts();
                            PhoneUsers.searchPhonesProducts();
                        }
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    searchUser: function () {
        var product_label = "Phone Number";
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: PhoneUsers.ajaxUrls.searchUser,
            postData: $('#searchUserForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: PhoneUsers.configs.rowNum,
            rowList: PhoneUsers.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Name', 'Email', 'Status', 'Created Date', product_label, 'Rights', 'Action'],
            colModel: [
                {name: 'user_id', index: 'user_id', hidden: true},
                {name: 'name', index: 'name', width: 120},
                {name: 'email', index: 'email', width: 200},
                {name: 'status', index: 'status', width: 100, align: "center", sortable: false},
                {name: 'created_date', index: 'created_date', width: 100, align: "center", sortable: false},
                {name: 'products', index: 'products', width: 200, align: "left"},
                {name: 'rights', index: 'rights', width: 70, align: "left"},
                {name: 'user_id', index: 'user_id', width: 120, sortable: false, align: "center", formatter: PhoneUsers.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete managetables-icon-delete-user" data-id="' + cellvalue + '" title="Delete"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-change-pass" data-id="' + cellvalue + '" title="Change Password"></span></span>';
        } else {
            return '';
        }
    },
    activatedFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === 1) {
            return 'Activated';
        }
        return 'Not Activated';
    }
};