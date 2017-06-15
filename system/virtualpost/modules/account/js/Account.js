var Account = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        account: null,
        change_email_address: null,
        change_password: null,
        change_currency: null,
        change_language: null,
        change_decimal_separator: null,
        change_auto_send_invoice_flag: null,
        change_my_account_type: null,
        delete_postbox: null,
        check_current_balance: null,
        delete_last_postbox: null,
        payment_box: null,
        confirm_delete_postbox: null,
        delete_free_postbox: null,
        delete_private_business_postbox: null,
        add_postbox: null,
        reactivate_delete_postbox: null,
        load_postbox_setting: null,
        direct_payment: null,
        estimate_fee_pre_payment: null,
        term_and_condition_history_url: null,
        save_term_and_condition_url: null,
        add_term_and_condition_url: null
    },

    /*
     * Postbox types
     */
    postboxTypes: {
        FREE_TYPE: '1',
        PRIVATE_TYPE: '2',
        BUSINESS_TYPE: '3'
    },

    flags: {
        OFF_FLAG: '0',
        ON_FLAG: '1'
    },

    /*
     *  Messages
     */
    messages: {
        can_not_change_postbox_account_not_activated: 'You can not add or change your postbox.<br/>Please complete registration process.'
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.mailbox = baseUrl + 'mailbox';
        this.ajaxUrls.account = baseUrl + 'account';
        this.ajaxUrls.change_email_address = baseUrl + 'account/change_my_email';
        this.ajaxUrls.change_password = baseUrl + 'account/change_my_pass';
        this.ajaxUrls.resend_email_confirm = baseUrl + 'customers/resend_email_confirm';
        this.ajaxUrls.change_currency = baseUrl + 'account/change_currency';
        this.ajaxUrls.change_language = baseUrl + 'account/change_language';
        this.ajaxUrls.change_decimal_separator = baseUrl + 'account/change_decimal_separator';
        this.ajaxUrls.change_auto_send_invoice_flag = baseUrl + 'account/change_auto_send_invoice_flag';
        this.ajaxUrls.change_my_account_type = baseUrl + 'account/change_my_account_type';
        this.ajaxUrls.delete_postbox = baseUrl + 'account/delete_postbox';
        this.ajaxUrls.check_current_balance = baseUrl + 'account/check_current_balance';
        this.ajaxUrls.delete_last_postbox = baseUrl + 'account/delete_last_postbox';
        this.ajaxUrls.payment_box = baseUrl + 'account/payment_box';
        this.ajaxUrls.confirm_delete_postbox = baseUrl + 'account/confirm_delete_postbox';
        this.ajaxUrls.delete_free_postbox = baseUrl + 'account/delete_private_business_postbox';
        this.ajaxUrls.delete_private_business_postbox = baseUrl + 'account/delete_private_business_postbox';
        this.ajaxUrls.add_postbox = baseUrl + 'account/add_postbox';
        this.ajaxUrls.reactivate_delete_postbox = baseUrl + 'account/reactivate_delete_postbox';
        this.ajaxUrls.load_postbox_setting = baseUrl + 'account/load_postbox_setting';
        this.ajaxUrls.postbox_setting = baseUrl + 'account/postbox_setting';
        this.ajaxUrls.direct_payment = baseUrl + 'account/direct_payment';
        this.ajaxUrls.estimate_fee_pre_payment = baseUrl + 'customers/estimate_fee_pre_payment';
        this.ajaxUrls.upgrade_customer_type = baseUrl + 'account/upgrade_customer_type';
        this.ajaxUrls.term_and_condition_history_url = baseUrl + 'account/setting/hitory_term_condition';
        this.ajaxUrls.save_term_and_condition_url = baseUrl + 'account/setting/save_term_condition_setting';
        this.ajaxUrls.add_term_and_condition_url = baseUrl + 'account/setting/add_term_condition_enterprise';
        this.ajaxUrls.save_api_access_setting_url = baseUrl + 'account/setting/save_api_access_setting';
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl) {
        // init data
        Account.initAjaxUrls(baseUrl);

        // Event listeners
        // 1. Change email address
        $('#changeMyEmailAddressLink').click(function () {
            Account.showChangeMyEmailLightBox();
        });

        // 2. Change password
        $('#changeMyPasswordLink').click(function () {
            Account.showChangePasswordLightBox();
        });

        // 3. Resend email confirm
        $('#resendEmailConfirm').click(function () {
            Account.resendEmailConfirm();
        });

        // 4. The customer change currency
        $('#currency_id').change(function () {
            Account.changeCurrency($(this).val());
        });
        
         // 4. The customer change currency
        $('#language').change(function () {
            Account.changeLanguage($(this).val());
        });

        // 5. The customer change decimal separator
        $("#decimal_separator").change(function () {
            Account.changeDecimalSeparator($(this).val());
        });

        // 6.Auto send invoice pdf at the month end
        $('#auto_send_invoice_flag').change(function () {
            Account.autoSendInvoiceFlag();
        });

        // 7. Change postbox/account type
        $('#changeMyAccountTypeLink').click(function () {
            Account.showChangeMyAccountTypeLightBox();
        });

        // 8. Select postbox to delete
        $('#delPostboxLink').click(function () {
            Account.showDeletePostboxLightBox();
        });

        // 9.Click on the "Add" link to add a new postbox
        $('a.add').click(function () {
            var account_type = $(this).attr('rel');
            var location_id  = null;
            var advanced     = 0;
            Account.showAddPostBoxLightBox(account_type,location_id, advanced);
        });

        $('strong.add').click(function () {
            var location_id = $(this).attr('rel');
            Account.showAddPostBoxLightBox(1, location_id, 0);
        });
        
        $('#addNewPostboxAddressButton').click(function() {
            var location_id = 1;
            Account.showAddPostBoxLightBox(1, location_id, 0);
        });


        $('a#btnAddPostboxAdvanced').click(function () {
            var account_type = $(this).attr('rel');
            var advanced = 1;
            var location_id = 1;
            
            Account.showAddPostBoxLightBox(account_type,location_id,advanced);
        });

        // 10. Click the button upgrade postbox
        $("#btnUpgradePostbox").click(function () {
            Account.showChangeMyAccountTypeLightBox();
        });

        // 11.Click the "Save" button
        $('#saveSettingButton').click(function () {
            Account.saveSettingButton();
        });

        // Reactivate account.
        $('#reactivate_account').live('click', function () {
            Account.reactivateAccount();
        });

        // Delete account
        $('#delete_account').live('click', function () {
            Account.deleteAccount();
        });

        // Process when postbox setting change.
        $('#postbox_setting_id').change(function () {
            Account.changePostboxSetting($(this).val());
        });

        $("#addPostboxForm_company").live("keyup",function(){
            Account.checkCompanyName();
        });
        $("#addPostboxForm_name").live("keyup",function(){
            Account.checkCompanyName();
        });
        
        $('#btnUpgradeEnterpriseCustomer').live('click', function () {
            Account.upgradeEnterpriseCustomer();
        });
        
        // support and feedback for enterprise
        $("#saveSupportSettingButton").click(function(e){
             Account.saveSupportFeedbackSetting();
             return false;
        });
        
        Account.checkSupportEmail(true);
        $("#active_support_email_user_checkbox").click(function(e){
            Account.checkSupportEmail(false);
        });
        
        Account.checkSupportPhone(true);
        $("#active_support_phone_user_checkbox").click(function(e){
            Account.checkSupportPhone(false);
        });
        
        $("#active_api_access_checkbox").change(function(e){
            Account.checkAPIAccess();
        });
        
        // enterprise customer term and condition setting.
        $("#CUSTOMER_TERM_CONDITION_SETTING").click(function(e){
            Account.saveTermConditionSetting();
        });
        
        Account.checkOwnDomainSetting(true);
        // enterprise own domain setting.
        $("#own_domain_checkbox").click(function(e){
            Account.checkOwnDomainSetting(false);
        });
        
        // Click to enable access
        $("#saveOwnDomainButton").click(function(){
            Account.saveOwnDomainSetting();
        });
        
        // click see history term & condition of enterprise customer
        $('#see_term_condition_history').click(function (e) {
            e.preventDefault();
            Account.showHistoryOfTermCondition();
            return false;
        });
        
        // click upload term and condition button.
        $("#uploadTermConditionBtn").click(function(){
            Account.openUploadTermConditionWindow();
        });
        
        // Click to enable access
        $("#buttonConfirmEnableAPIAccess").click(function(){
            Account.saveAPIAccessSetting('enable');
        });
        
        // Click to enable access
        $("#buttonConfirmDisableAPIAccessEndContract").click(function(){
            Account.saveAPIAccessSetting('disable_end_contract');
        });
        
        // Click to enable access
        $("#buttonConfirmDisableAPIAccessImmediately").click(function(){
            Account.saveAPIAccessSetting('disable_end_immediately');
        });
    },
    
    upgradeEnterpriseCustomer: function() {
        var message = $("#upgradeEnterpriseCustomerConfirmDivContainer").html();
        // Show confirm dialog
        $.confirm({
            message: message,
            yes: function () {
                $.ajaxExec({
                    url: Account.ajaxUrls.check_current_balance,
                    data: {add: "add"},
                    success: function (data) {
                        if (data.status === false) {
                            // show confirmation popup
                            Account.openPaymentBox('add');
                        } else {
                            Account.openUpgradeEnterpriseCustomerConfirm();
                        }
                    }
                });
            }
        });
        return false;
    },
    // Open form to confirm
    openUpgradeEnterpriseCustomerConfirm: function() {
        // Clear control of all dialog form
        $('#upgradeEnterpriseCustomerConfirmWindow').html('');
        // Open new dialog
        $('#upgradeEnterpriseCustomerConfirmWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 700,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.upgrade_customer_type, function () {
                });
            }
        });
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('option', 'position', 'center');
        $('#upgradeEnterpriseCustomerConfirmWindow').dialog('open');
    },
    // Submit the change
    saveUpgradeEnterpriseCustomer: function() {
        var separatePostboxType = $('input[name="separatePostboxType"]:checked').val();
        $.ajaxExec({
            url: Account.ajaxUrls.upgrade_customer_type + '?separatePostboxType=' + separatePostboxType,
            data: {},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        document.location = Account.ajaxUrls.mailbox;
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    changeCurrency: function (currencyId) {
        $.ajaxExec({
            url: Account.ajaxUrls.change_currency,
            data: {currency_id: currencyId},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
    changeLanguage: function (language) {
        $.ajaxExec({
            url: Account.ajaxUrls.change_language,
            data: {language: language},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    changeDecimalSeparator: function (decimalSeparator) {
        $.ajaxExec({
            url: Account.ajaxUrls.change_decimal_separator,
            data: {decimal_separator: decimalSeparator},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    /**
     * Change setting auto send invoice flag
     */
    autoSendInvoiceFlag: function () {
        var auto_send_invoice_flag = ($("#auto_send_invoice_flag").prop('checked') == true) ? '1' : '0';
        $.ajaxExec({
            url: Account.ajaxUrls.change_auto_send_invoice_flag,
            data: {auto_send_invoice_flag: auto_send_invoice_flag},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    showChangeMyEmailLightBox: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#changeMyEmailWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.change_email_address, function () {
                    $('#changeMyEmailForm_email').focus();
                });
            },
            buttons: {
                'Submit': function () {
                    Account.submitChangeMyEmail();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changeMyEmailWindow').dialog('option', 'position', 'center');
        $('#changeMyEmailWindow').dialog('open');
    },
   
    submitChangeMyEmail: function () {
        var submitUrl = $('#changeMyEmailForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeMyEmailForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, '', function (){
                        document.location.reload();
                    });
                    $('#changeMyEmailWindow').dialog('close');
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    showChangePasswordLightBox: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#changeMyPassWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.change_password, function () {
                    $('#changeMyPasswordForm_current_password').focus();
                });
            },
            buttons: {
                'Submit': function () {
                    Account.submitChangeMyPass();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changeMyPassWindow').dialog('option', 'position', 'center');
        $('#changeMyPassWindow').dialog('open');
    },

    resendEmailConfirm: function () {
        $.ajaxExec({
            url: Account.ajaxUrls.resend_email_confirm,
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    submitChangeMyPass: function () {
        var submitUrl = $('#changeMyPasswordForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeMyPasswordForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                    $('#changeMyPassWindow').dialog('close');
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    showChangeMyAccountTypeLightBox: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#changeMyAccountTypeWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 550,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.change_my_account_type, function () {
                    $('#changeMyAccountTypeForm_account_type').focus();
                });
            },
            buttons: {
                'Submit': function () {
                    Account.submitChangeMyAccountType();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changeMyAccountTypeWindow').dialog('option', 'position', 'center');
        $('#changeMyAccountTypeWindow').dialog('open');
    },

    /**
     * Submit change account type
     */
    submitChangeMyAccountType: function () {
        var account_type = $('#changeMyAccountTypeForm_account_type').val();
        if ($.isEmpty(account_type)) {
            return;
        }
        var submitUrl = $('#changeMyAccountTypeForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeMyAccountTypeForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        location.reload();
                    });
                    $('#changeMyAccountTypeWindow').dialog('close');
                } else {
                	// #1012 Pre-payment process
                	if (data.prepayment == true) {
                		var new_postbox_type = $('#changeMyAccountTypeForm_account_type').val();
                		var postbox_id = $('#postbox_id').val();
                		Account.openEstimateCostDialog('change_postbox_type', '', postbox_id, new_postbox_type);
                    } else {
                    	$.displayError(data.message);
                    }
                }
            }
        });
    },

    showDeletePostboxLightBox: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#delPostboxWindow').openDialog({
            autoOpen: false,
            height: 180,
            width: 320,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.delete_postbox, function () {
                    $('#delPostboxForm_sltPostbox').focus();
                });
            },
            buttons: {
                'Submit': function () {
                    // Validate postbox required
                    var postboxSelected = $('#delPostboxForm_sltPostbox').val();
                    if (postboxSelected == '') {
                        $.error({message: "Postbox name field is required."});
                        return;
                    }

                    Account.submitDelPostbox();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#delPostboxWindow').dialog('option', 'position', 'center');
        $('#delPostboxWindow').dialog('open');
    },

    /**
     * Submit delete postbox
     */
    submitDelPostbox: function () {
        // Check current balance.
        $.ajaxExec({
            url: Account.ajaxUrls.check_current_balance,
            data: {postbox_id: $("#delPostboxForm_sltPostbox").val()},
            success: function (data) {
                if (data.status == false) {
                    // #479: check delete last postbox.
                    if (data.data.delete_flag == 1) {
                        if (data.data.payment_method == 'Credit Card' || data.data.payment_method == 'Paypal') {
                            // show confirmation popup
                            $.confirm({
                                message: data.message,
                                yes: function () {
                                    $.ajaxSubmit({
                                        url: Account.ajaxUrls.delete_last_postbox,
                                        success: function (data) {
                                            if (data.status) {
                                                Account.openConfirmDeleteBox();
                                            } else {
                                                $.displayError(data.message);
                                            }
                                        }
                                    });
                                }
                            });

                        } else {
                            Account.openPaymentBox('del');
                        }
                    } else {
                        // show confirmation popup
                        Account.openPaymentBox('del');
                    }
                } else {
                    if (data.data.delete_flag == 1) {
                        Account.openConfirmDeleteBox();
                    } else {
                        $("#direct_delete").val("");
                        Account.deletePostboxSubmit();
                    }
                }
            }
        });
    },

    showAddPostBoxLightBox: function (account_type, location_id, advanced) {
        
        // START fixbig #335: Check account activated
        // #659: Never-activated account should not allow to add more postbox.
        if ($("#activatedFlagId").val() != Account.flags.ON_FLAG) {
            $.displayError(Account.messages.can_not_change_postbox_account_not_activated);
            return;
        }
        Account.addPostbox(account_type, location_id, advanced);
    },

    /**
     * Process when user click to add postbox
     */
    addPostbox: function (type,location_id, advanced) {
        var loadUrl = Account.ajaxUrls.add_postbox + '/' + type+'/'+location_id+'/'+advanced;
        $.openDialog('#addPostboxWindow', {
            height: 450,
            width: 600,
            openUrl: loadUrl,
            title: "Add Postbox",
            closeButtonLabel: "Cancel",
            callback: function(){
                location.reload();
            },
            buttons:[
                {
                    id: "saveBtn",
                    text: "Submit"
                }
            ]
        });
        
        return;
    },

    /**
     * Open confirm delete posbox when customer want to delete main account.
     */
    openPaymentBox: function (method) {
        // Clear control of all dialog form
        $('#delPostboxConfirmWindow').html('');
        $('#delPostboxWindow').dialog('close');

        // Open new dialog
        $('#delPostboxConfirmWindow').openDialog({
            autoOpen: false,
            height: 200,
            width: 600,
            modal: true,
            title: "Confirmation",
            open: function () {
                $(this).load(Account.ajaxUrls.payment_box, {"method": method}, function () {
                });
            },
            buttons: {}
        });
        $('#delPostboxConfirmWindow').dialog('option', 'position', 'center');
        $('#delPostboxConfirmWindow').dialog('open');
    },

    /**
     * Open confirm delete posbox when customer want to delete main account.
     */
    openConfirmDeleteBox: function () {
        // Clear control of all dialog form
        $('#delPostboxConfirmWindow').html('');
        $('#delPostboxWindow').dialog('close');

        // Open new dialog
        $('#delPostboxConfirmWindow').openDialog({
            autoOpen: false,
            height: 330,
            width: 800,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.confirm_delete_postbox, function () {
                    $('#delPostboxConfirmForm_sltPostbox').focus();
                });
            },
            buttons: {}
        });
        $('#delPostboxConfirmWindow').dialog('option', 'position', 'center');
        $('#delPostboxConfirmWindow').dialog('open');
    },

    deletePostboxSubmit: function () {
        var submitUrl = $('#delPostboxForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }

        var postbox_id = $("#delPostboxForm_sltPostbox").val();
        var title = "Do you really want to delete this postbox: " + $("#delPostboxForm_sltPostbox").find(":selected").text();

        $.ajaxSubmit({
            url: submitUrl,
            formId: 'delPostboxForm',
            success: function (res) {
                var data = res.data;
                if (res.status == false) {
                    // if last postbox
                    if (data.last_postbox == true) {
                        Account.openConfirmDeleteBox();
                    } else {
                        // delete free postbox
                        if (data.postbox_type == Account.postboxTypes.FREE_TYPE) {
                            $.confirm({
                                message: title,
                                yes: function () {
                                    Account.setDeletePostbox(postbox_id);
                                }
                            });
                        } else {
                            // delete business or private postbox
                            $('#delPostboxConfirmWindow').openDialog({
                                autoOpen: false,
                                height: 300,
                                width: 530,
                                title: title,
                                modal: true,
                                open: function () {
                                    $(this).load(Account.ajaxUrls.delete_private_business_postbox, {"p": postbox_id}, function () {
                                        $('#delPostboxConfirmForm_sltPostbox').focus();
                                    });
                                },
                                buttons: {}
                            });
                            $('#delPostboxConfirmWindow').dialog('option', 'position', 'center');
                            $('#delPostboxConfirmWindow').dialog('open');
                        }
                    }
                } else {
                    $.confirm({
                        message: title,
                        yes: function () {
                            Account.setDeletePostbox(postbox_id);
                        }
                    });
                }
            }
        });
    },

    setDeletePostbox: function (postbox_id) {
        $.ajaxExec({
            url: Account.ajaxUrls.account + '/set_delete_postbox',
            data: {postbox_id: postbox_id},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        document.location = Account.ajaxUrls.account;
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    saveSettingButton: function () {
        var submitUrl = $('#saveSettingForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'saveSettingForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, '', function(){
                        location.reload();
                    });
                    
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    },

    /**
     * Reactivate account.
     */
    reactivateAccount: function () {
        $.ajaxSubmit({
            url: Account.ajaxUrls.reactivate_delete_postbox,
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        document.location = Account.ajaxUrls.account;
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    },

    /**
     * Delete account.
     */
    deleteAccount: function () {
        // Show confirm dialog
        $.confirm({
            message: 'Are you sure you want to delete?',
            yes: function () {
                $.ajaxExec({
                    url: Account.ajaxUrls.confirm_delete_postbox,
                    data: {delete_type: '2'},
                    success: function (data) {
                        if (data.status) {
                            $.displayInfor(data.message, null, function () {
                                document.location = Account.ajaxUrls.account;
                            });
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
        return false;
    },

    /**
     * Process when postbox setting change.
     */
    changePostboxSetting: function (postbox_setting_id) {
        $.ajaxExec({
            url: Account.ajaxUrls.load_postbox_setting,
            data: {postbox_setting_id: postbox_setting_id},
            success: function (data) {
                if (data.status) {
                    var objResponse = data.data;
                    $('#always_scan_envelope').attr("checked", objResponse.always_scan_envelope === '1');
                    $('#envelope_scan').attr("checked", objResponse.always_scan_envelope_vol_avail === '1');
                    $('#always_scan_incomming').attr("checked", objResponse.always_scan_incomming === '1');
                    $('#scans').attr("checked", objResponse.always_scan_incomming_vol_avail === '1');
                    $('#email_scan_notification').attr("checked", objResponse.email_scan_notification === '1');
                    $('#always_forward_directly').attr("checked", objResponse.always_forward_directly === '1');
                    $('#always_forward_collect').attr("checked", objResponse.always_forward_collect === '1');
                    $('#always_mark_invoice').attr("checked", objResponse.always_mark_invoice === '1');
                    $('#inform_email_when_item_trashed').attr("checked", objResponse.inform_email_when_item_trashed === '1');
                    $('#auto_trash_flag').attr("checked", objResponse.auto_trash_flag === '1');
                    $('#email_notification').val(objResponse.email_notification);
                    $('#invoicing_cycle').val(objResponse.invoicing_cycle);
                    $('#collect_mail_cycle').val(objResponse.collect_mail_cycle);
                    $('#weekday_shipping').val(objResponse.weekday_shipping);
                    $('#trash_after_day').val(objResponse.trash_after_day);
                    $('#next_collect_shipping').html(objResponse.next_collect_date);
                    $('#standard_service_national_letter_dropdownlist').html(objResponse.standard_service_national_letter_dropdownlist);
                    $('#standard_service_international_letter_dropdownlist').html(objResponse.standard_service_international_letter_dropdownlist);
                    $('#standard_service_national_package_dropdownlist').html(objResponse.standard_service_national_package_dropdownlist);
                    $('#standard_service_international_package_dropdownlist').html(objResponse.standard_service_international_package_dropdownlist);
                    $('#always_mark_invoice').data("accounting_email", objResponse.accounting_email);
                    objResponse.accounting_email ? $('#always_mark_invoice').prop( "disabled", false ) : $('#always_mark_invoice').prop( "disabled", true );
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    directPayment: function () {
        $.ajaxExec({
            url: Account.ajaxUrls.direct_payment,
            success: function (data) {
                if (data.status) {
                    document.location = Account.ajaxUrls.account;
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },

    checkCompanyName: function() {
        var companyName = $("#addPostboxForm_company").val();
        var name = $("#addPostboxForm_name").val();
        if(companyName.toLowerCase() == name.toLowerCase()) {
            $("#addPostboxForm_company").addClass("error");
        } else {
            $("#addPostboxForm_company").removeClass("error");
        }
    },
    
    /**
     *  Open estimated cost dialog.
     *  Action_Type: add_more_postbox
     */
    openEstimateCostDialog: function(type, location_id, postbox_id, postbox_type){
        
        var url = Account.ajaxUrls.estimate_fee_pre_payment;
        url += "?type=" + type;
        url += "&postbox_type=" + postbox_type;
        url += "&location_id=" + location_id;
        url += "&postbox_id=" + postbox_id;
        
        // Open new dialog
        $('#make_prepayment_dialog').openDialog({
            autoOpen: false,
            height: 475,
            width: 660,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load(url, function() {
                });
            }
        });

        $('#make_prepayment_dialog').dialog('option', 'position', 'center');
        $('#make_prepayment_dialog').dialog('open');
    },
    
    /**
     * check email support validation
     * 
     * @param {type} init_flag
     * @returns {undefined}
     */
    checkSupportEmail: function(init_flag){
        var is_checked = $("#active_support_email_user_checkbox").is(":checked");
        console.log('checkSupportEmail click: ' + is_checked);
        if((is_checked && init_flag) || (!is_checked && !init_flag ) ){
            $("#supportEmailDivContainer").show();
            //$('#saveSupportSettingButton').addClass('btn-yellow');
        }else{
            $("#supportEmailDivContainer").hide();
            var is_phone_checked = $("#active_support_phone_user_checkbox").is(":checked");
            if (!is_phone_checked) {
                //$('#saveSupportSettingButton').removeClass('btn-yellow');
            }
        }
    },
    /**
     * check phone support validation
     * 
     * @param {type} init_flag
     * @returns {undefined}
     */
    checkSupportPhone: function(init_flag){
        var is_checked = $("#active_support_phone_user_checkbox").is(":checked");
        if((is_checked && init_flag) || (!is_checked && !init_flag ) ){
            $("#supportPhoneDivContainer").show();
           // $('#saveSupportSettingButton').addClass('btn-yellow');
        }else{
            $("#supportPhoneDivContainer").hide();
            var is_email_checked = $("#active_support_email_user_checkbox").is(":checked");
            if (!is_email_checked) {
                //$('#saveSupportSettingButton').removeClass('btn-yellow');
            }
        }
    },
    /**
     * check email support validation
     * 
     * @param {type} init_flag
     * @returns {undefined}
     */
    checkAPIAccess: function(){
        var is_checked = $("#active_api_access_checkbox").is(":checked");
        var api_access_flag = $("#api_access_flag_hidden").val();
        if (is_checked) {
            $('#active_api_access_selected_hidden').val('1');
            console.log('Enable API Access');
            Account.openEnableAPIAccessWindow();
        } else if (api_access_flag == '1') {
            $('#active_api_access_selected_hidden').val('0');
            Account.openDisableAPIAccessWindow();
        }
        return true;
    },
    
    /**
     * save support feedback setting of enterprise customer.
     * @returns {undefined}
     */
    saveSupportFeedbackSetting: function(){
        var submitUrl = $('#saveSupportSettingForm').attr('action');

        var email = $.trim($("#active_support_email_user").val());
        var phone = $.trim($("#active_support_phone_user").val());
        var is_email_checked = $("#active_support_email_user_checkbox").is(":checked");
        var is_phone_checked = $("#active_support_phone_user_checkbox").is(":checked");

        if(email == '' && is_email_checked){
            $.displayError("Email field is required!", function(){
                $("#active_support_email_user").focus();
            });

            return;
        }

        if(phone == '' && is_phone_checked){
            $.displayError("Phone field is required!", function(){
                $("#active_support_phone_user").focus();
            });

            return;
        }

        $.ajaxSubmit({
            url: submitUrl,
            formId: 'saveSupportSettingForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        //document.location.href = '<?php echo base_url() ?>account';
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    /**
     * save term condition setting of enterprise customer.
     * @returns {undefined}
     */
    saveTermConditionSetting: function(){
        var is_checked = $("#CUSTOMER_TERM_CONDITION_SETTING").is(":checked") ? 0 : 1;
        
        if(is_checked == 0){
            //$("#uploadTermConditionDiv").hide();
            //$('#uploadTermConditionBtn').removeClass('btn-yellow');
        }else{
            //$("#uploadTermConditionDiv").show();
            //$('#uploadTermConditionBtn').addClass('btn-yellow');
        }

        $.ajaxExec({
            url: Account.ajaxUrls.save_term_and_condition_url,
            data: {is_checked: is_checked},
            success: function (data) {
                if (data.status) {
                    //$.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
        
    checkOwnDomainSetting: function(init_flag){
        var is_checked = $("#own_domain_checkbox").is(":checked");
        if((is_checked && init_flag) || (!is_checked && !init_flag ) ){
            $("#ownDomainDivContainer").show();
            //$('#saveOwnDomainButton').addClass('btn-yellow');
        }else{
            $("#ownDomainDivContainer").hide();
            //$('#saveOwnDomainButton').removeClass('btn-yellow');
        }
    },
    
    saveOwnDomainSetting: function(){
        var submitUrl = $('#saveOwnDomainForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'saveOwnDomainForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    
    /**
     * show term and condition of enteprise customer.
     * @returns {undefined}
     */
    showHistoryOfTermCondition: function(){
        // Clear control of all dialog form
        $('#showHistoryTermConditionWindow').html('');

        // Open new dialog
        $('#showHistoryTermConditionWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 800,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.term_and_condition_history_url, function () {
                });
            },
            buttons: {
                'Close': function () {
                    $(this).dialog('destroy');
                }
            }
        });
        $('#showHistoryTermConditionWindow').dialog('option', 'position', 'center');
        $('#showHistoryTermConditionWindow').dialog('open');
        return false;
    },
    
    /**
     * open term and condition for add/edit
     * @returns {undefined}
     */
    openUploadTermConditionWindow: function(){
        var is_checked = $("#CUSTOMER_TERM_CONDITION_SETTING").is(":checked") ? 0 : 1;
        
        if(is_checked == 0){
            return;
        }
        // Clear control of all dialog form
        $('#showAddEditTermConditionWindow').html('');

        // Open new dialog
        $('#showAddEditTermConditionWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 950,
            modal: true,
            open: function () {
                $(this).load(Account.ajaxUrls.add_term_and_condition_url, function () {
                });
            },
            buttons: {
                'Deploy Term & condition now': function(){
                    Account.saveUploadTermConditionEnterprise();
                },
                'Close': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#showAddEditTermConditionWindow').dialog('option', 'position', 'center');
        $('#showAddEditTermConditionWindow').dialog('open');
        return false;
    },
    /**
     * save term and condition of enterprise customer.
     * @returns {undefined}
     */
    saveUploadTermConditionEnterprise: function(){
        var submitUrl = $('#addTermAndConditionForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        
        var editorText = CKEDITOR.instances.content_temp.getData();
        $('#content').val(editorText);
        
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addTermAndConditionForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, '', function(){
                        location.reload();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    },
    /**
     * save term condition setting of enterprise customer.
     * @returns {undefined}
     */
    saveAPIAccessSetting: function(type){
        var is_checked = $('#active_api_access_selected_hidden').val();
        if(is_checked == '0'){
            $("#apiAccessDivContainer").hide();
        }else{
            $("#apiAccessDivContainer").show();
        }

        $.ajaxExec({
            url: Account.ajaxUrls.save_api_access_setting_url + '?type=' + type,
            data: {is_checked: is_checked},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                    if (type == 'enable') {
                        $('#enableAPIAccessConfirmationWindow').dialog('close');
                    } else {
                        $('#disableAPIAccessConfirmationWindow').dialog('close');
                    }
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    // Open form to confirm
    openEnableAPIAccessWindow: function() {
        // Open new dialog
        $('#enableAPIAccessConfirmationWindow').openDialog({
            autoOpen: false,
            height: 200,
            width: 600,
            modal: true,
            create: function() {
                $(this).closest('div.ui-dialog')
                       .find('.ui-dialog-titlebar-close')
                       .click(function(e) {
                           $("#active_api_access_checkbox").attr("checked", false);
                       });
            }
        });
        $('#enableAPIAccessConfirmationWindow').dialog('option', 'position', 'center');
        $('#enableAPIAccessConfirmationWindow').dialog('open');
    },
    // Open form to confirm
    openDisableAPIAccessWindow: function() {
        // Open new dialog
        $('#disableAPIAccessConfirmationWindow').openDialog({
            autoOpen: false,
            height: 220,
            width: 720,
            modal: true
        });
        $('#disableAPIAccessConfirmationWindow').dialog('option', 'position', 'center');
        $('#disableAPIAccessConfirmationWindow').dialog('open');
    }
};