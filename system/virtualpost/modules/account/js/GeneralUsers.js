var GeneralUsers = {
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
        confirmDeleteUser: ""
    },
    configs: {
        baseUrl: '',
        rowNum: 0,
        rowList: '',
        g_product_type: ''
    },
    /*
     *  Messages
     */
    messages: {
        can_not_change_postbox_account_not_activated: 'You can not change your postbox type.<br/>Please complete registration process.'
    },
    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.user = baseUrl + 'account/users/general_users';
        this.ajaxUrls.searchUser = baseUrl + 'account/users/search_users';
        this.ajaxUrls.addUser = baseUrl + 'account/users/add_general_users?product_type=' + GeneralUsers.configs.g_product_type;
        this.ajaxUrls.editUser = baseUrl + 'account/users/edit_general_users?product_type=' + GeneralUsers.configs.g_product_type;
        this.ajaxUrls.editPhoneUser = baseUrl + 'account/users/edit_phone_users?product_type=' + GeneralUsers.configs.g_product_type;
        this.ajaxUrls.deleteUser = baseUrl + 'account/users/delete_general_users';
        this.ajaxUrls.changePassword = baseUrl + 'account/users/change_password_general_users';
        this.ajaxUrls.change_email_address = baseUrl + 'account/change_my_email';
        this.ajaxUrls.resend_email_confirm = baseUrl + 'customers/resend_email_confirm';
        this.ajaxUrls.search_product = baseUrl + 'account/users/search_product';
        this.ajaxUrls.unassign_postbox = baseUrl + 'account/users/unassign_postbox?product_type=' + GeneralUsers.configs.g_product_type;
        this.ajaxUrls.assign_postbox = baseUrl + 'account/users/assign_postbox?product_type=' + GeneralUsers.configs.g_product_type;
        this.ajaxUrls.confirmDeleteUser = baseUrl + 'account/users/confirm_delete_user';
        this.ajaxUrls.changePostboxUserLocation = baseUrl + 'account/users/change_postbox_location';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList, g_product_type) {
        // init config.
        GeneralUsers.configs.baseUrl = baseUrl;
        GeneralUsers.configs.rowList = rowList.split(',');
        GeneralUsers.configs.rowNum = rowNum;
        GeneralUsers.configs.g_product_type = g_product_type;
        
        // init data
        GeneralUsers.initAjaxUrls(baseUrl);

        // init screen
        GeneralUsers.searchUser();

        // Search user
        $('#btnSearchUser').click(function(){
            GeneralUsers.searchUser();
        });

        // add new user
        $("#btnAddNewUser").click(function () {
            GeneralUsers.addNewUser();
        });

        $('.managetables-icon-edit-user').live('click', function () {
            var user_id = $(this).attr('data-id');
            GeneralUsers.editUser(user_id);
        });

        $('.managetables-change-pass').live('click', function () {
            var user_id = $(this).attr('data-id');
            GeneralUsers.changePassword(user_id);
        });

        $('.managetables-icon-delete-user').live('click', function () {
            var user_id = $(this).attr('data-id');
            GeneralUsers.deleteUser(user_id);
        });
        
        // 2. Change email
        $('#addEditUserForm_changeEmailLink').live('click', function () {
            GeneralUsers.showChangeMyEmailLightBox();
            return false;
        });

        // 3. Resend email confirm
        $('#addEditUserForm_resendEmailLink').live('click', function () {
            GeneralUsers.resendEmailConfirm();
            return false;
        });
        
        // change password.
        $("#addEditUserForm_changePasswordLink").live('click', function(e){
            e.preventDefault();
            var user_id = $('#addEditUserForm_customer_id').val();
            GeneralUsers.changePassword(user_id);
            return false;
        });
        
        // Delete product
        $('.managetables-icon-delete-product').live('click', function() {
            var postbox_user_id = $(this).attr('data-id');
            GeneralUsers.deletePostboxToUser(postbox_user_id); 
        });
        
        $('.managetables-icon-edit-product').live('click', function() {
            var postbox_user_id = $(this).attr('data-id');
            GeneralUsers.editPostboxToUser(postbox_user_id); 
        });
        
        // link verification
        $(".verification-link").live("click", function(){
            location.href = GeneralUsers.configs.baseUrl + "cases/verification";
        });
    },
    searchUser: function () {
        var product_label = "Products";
        var current_product_type = $('#searchUserForm_current_product_type').val();
        if(current_product_type == 'postbox'){
            product_label = "Postbox";
        }else if(current_product_type == 'phone'){
            product_label = "Phone Number";
        }
        console.log('current_product_type:' + current_product_type);
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: GeneralUsers.ajaxUrls.searchUser + "?product_type="+ current_product_type,
            postData: $('#searchUserForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: GeneralUsers.configs.rowNum,
            rowList: GeneralUsers.configs.rowList,
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
                {name: 'user_id', index: 'user_id', width: 120, sortable: false, align: "center", formatter: GeneralUsers.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-edit-user" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-times managetables-icon-delete-user" data-id="' + cellvalue + '" title="Delete"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-lock managetables-change-pass" data-id="' + cellvalue + '" title="Change Password"></span></span>';
        } else {
            return '';
        }
    },
    activatedFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === 1) {
            return 'Activated';
        }
        return 'Not Activated';
    },
    // Search products
    searchProducts: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        var product_type = $('#product_type').val();
        var url = this.ajaxUrls.search_product + '?product_type=' + product_type + '&customer_id=' + user_id;
        $("#dataGridProductResult").jqGrid('GridUnload');
        $("#dataGridProductResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 850,
            height: 110,
            rowNum: GeneralUsers.configs.rowNum,
            rowList: GeneralUsers.configs.rowList,
            pager: "#dataGridProductPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'ID', 'Category', 'Product', 'Description', 'Created Date', 'Status', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'code', index: 'code', width: 140, sortable: false},
                {name: 'category', index: 'category', width: 100, sortable: false},
                {name: 'product', index: 'product', width: 120, sortable: false},
                {name: 'description', index: 'status', width: 100, align: "left", sortable: false},
                {name: 'created_date', index: 'created_date', width: 100, align: "left", sortable: false},
                {name: 'status', index: 'status', width: 100, align: "left", sortable: false, formatter: GeneralUsers.verificationFormater},
                {name: 'id', index: 'id', width: 90, sortable: false, align: "center", formatter: GeneralUsers.produdctActionFormater}
            ],
            loadComplete: function () {
            }
        });
    },
    produdctActionFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-edit-product" data-id="' + cellvalue + '" title="Edit Location"></span></span>' 
                + '<span style="display:inline-block;"><span class="fa fa-times managetables-icon-delete-product" data-id="' + cellvalue + '" title="Delete"></span></span>';
        } else {
            return '';
        }
    },
    deletePostboxToUser: function(postbox_user_id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to remove this postbox? ',
            yes: function () {
                $.ajaxExec({
                    url: GeneralUsers.ajaxUrls.unassign_postbox + '&postbox_user_id=' + postbox_user_id,
                    success: function (data) {
                        if (data.status) {
                            $.displayInfor(data.message);
                            GeneralUsers.searchProducts();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
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
                $(this).load(GeneralUsers.ajaxUrls.addUser, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    GeneralUsers.saveUser();
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
        var url = GeneralUsers.ajaxUrls.editUser;
        if(GeneralUsers.configs.g_product_type == 'phone'){
            url = GeneralUsers.ajaxUrls.editPhoneUser;
        }

        // Open new dialog
        $('#editUser').openDialog({
            autoOpen: false,
            height: 620,
            width: 920,
            modal: true,
            open: function () {
                $(this).load(url + "&customer_id=" + user_id, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    GeneralUsers.saveUser();
                },
                'Cancel': function () {
                    $(this).dialog('destroy');
                }
            }
        });
        $('#editUser').dialog('option', 'position', 'center');
        $('#editUser').dialog('open');
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
                        GeneralUsers.searchUser();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteUser: function (user_id) {
        var submitUrl = GeneralUsers.ajaxUrls.confirmDeleteUser + "?user_id=" + user_id;
        
        $.openDialog("#confirmDeleteUserWindow", {
            height: 570,
            width: 920,
            openUrl: submitUrl,
            title: "Confirm delete user",
            closeButtonLabel: "Cancel",
            buttons:[
                {
                    id: "saveBtn",
                    text: "Confirm selection & save"
                }
            ]
        });
        return false;
    },
    changePassword: function (user_id) {
        // Clear control of all dialog form
        $('#changePasswordUser').html('');
        var url = GeneralUsers.ajaxUrls.changePassword + '?id=' + user_id;

        // Open new dialog
        $('#changePasswordUser').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(url, function () {
                });
            },
            buttons: {
                'Save': function () {
                    GeneralUsers.resetPasswordUser();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#changePasswordUser').dialog('option', 'position', 'center');
        $('#changePasswordUser').dialog('open');
    },
    resetPasswordUser: function () {
        var submitUrl = $('#resetPasswordUserForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'resetPasswordUserForm',
            success: function (data) {
                if (data.status) {
                    $('#changePasswordUser').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        GeneralUsers.searchUser();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    showChangeMyEmailLightBox: function () {
        var user_id = $('#addEditUserForm_customer_id').val();
        // Open new dialog
        $('#changeMyEmailWindow').html('');
        $('#changeMyEmailWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            open: function () {
                $(this).load(GeneralUsers.ajaxUrls.change_email_address + '?customer_id=' + user_id, function () {
                    $('#changeMyEmailForm_email').focus();
                });
            },
            buttons: {
                'Submit': function () {
                    GeneralUsers.submitChangeMyEmail();
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
                        $('#changeMyEmailWindow').dialog('destroy'); 
                        var user_id = $("#addEditUserForm_customer_id").val();
                        $('#addUser, #editUser').dialog("destroy");
                        GeneralUsers.editUser(user_id);
                    }); 
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    resendEmailConfirm: function () {
        var user_id = $('#addEditUserForm_customer_id').val();
        $.ajaxExec({
            url: GeneralUsers.ajaxUrls.resend_email_confirm + '?customer_id=' + user_id,
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    editPostboxToUser: function(postbox_user_id) {
        var windowId = '#ChangeUserPostboxLocationWindow';
        var loadUrl = this.ajaxUrls.changePostboxUserLocation + "?postbox_user_id=" + postbox_user_id ;
        $.openDialog(windowId, {
            height: 420,
            width: 700,
            openUrl: loadUrl,
            title: "Change Postbox Location",
            show_only_close_button: false,
            buttons: [{
                'text': 'Save',
                'id': 'changePostboxLocationButton'
            }],
            callback: function(){
                //location.reload();
            }
        });
    },
    
    verificationFormater: function(cellvalue, options, rowObject){
        if(cellvalue != '' && rowObject[2] == 'Postbox' ){
            return '<a href="#" class="main_link_color verification-link" target="_blank">'+cellvalue+'</a>';
        }else{
            return "";
        }
    }
};