var PostboxUsers = {
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
        this.ajaxUrls.searchUser = baseUrl + 'account/users/search_users?product_type=postbox';
        this.ajaxUrls.addUser = baseUrl + 'account/users/add_general_users?product_type=postbox';
        this.ajaxUrls.editUser = baseUrl + 'account/users/edit_general_users?product_type=postbox';
        this.ajaxUrls.deleteUser = baseUrl + 'account/users/delete_general_users';
        this.ajaxUrls.changePassword = baseUrl + 'account/users/change_password_general_users';
        this.ajaxUrls.change_email_address = baseUrl + 'account/users/change_my_email';
        this.ajaxUrls.resend_email_confirm = baseUrl + 'account/users/resend_email_confirm';
        this.ajaxUrls.assign_postbox = baseUrl + 'account/users/assign_postbox?product_type=postbox';
        this.ajaxUrls.unassign_postbox = baseUrl + 'account/users/unassign_postbox?product_type=postbox';
        this.ajaxUrls.search_product = baseUrl + 'account/users/search_product';
        this.ajaxUrls.add_new_postbox = baseUrl + 'account/add_postbox?product_type=postbox';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        PostboxUsers.initAjaxUrls(baseUrl);

        // init config.
        PostboxUsers.configs.baseUrl = baseUrl;
        PostboxUsers.configs.rowList = rowList.split(',');
        PostboxUsers.configs.rowNum = rowNum;
        
        // Add product
        $('#btnAddPostboxToUser').live('click', function () {
            var user_id = $('#addEditUserForm_customer_id').val();
            PostboxUsers.addPostboxToUser(user_id);
            return false;
        });
        // Add new product
        $('#btnAddNewPostboxToUser').live('click', function () {
            var user_id = $('#addEditUserForm_customer_id').val();
            PostboxUsers.addNewPostboxToUser(user_id);
            return false;
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
                $(this).load(PostboxUsers.ajaxUrls.addUser, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PostboxUsers.saveUser();
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
                $(this).load(PostboxUsers.ajaxUrls.editUser + "&customer_id=" + user_id, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    PostboxUsers.saveUser();
                },
                'Cancel': function () {
                    $(this).dialog('close');
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
                        PostboxUsers.searchUser();
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
                    url: PostboxUsers.ajaxUrls.deleteUser + "/" + user_id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            PostboxUsers.searchUser();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },
    addPostboxToUser: function(user_id) {
        // Clear control of all dialog form
        $('#assignPostboxUser').html('');

        // Open new dialog
        $('#assignPostboxUser').openDialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            open: function () {
                $(this).load(PostboxUsers.ajaxUrls.assign_postbox + "&customer_id=" + user_id, function () {
                });
            },
            buttons: {
                'Save': function () {
                    var submitUrl = $('#assginPostboxToUserForm').attr('action');
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'assginPostboxToUserForm',
                        success: function (data) {
                            if (data.status) {
                                $('#assignPostboxUser').dialog('close');
                                PostboxUsers.searchPostboxProducts();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#assignPostboxUser').dialog('option', 'position', 'center');
        $('#assignPostboxUser').dialog('open');
        
    },
    addNewPostboxToUser: function(user_id) {
        var loadUrl = PostboxUsers.ajaxUrls.add_new_postbox + "&customer_id=" + user_id;
        $.openDialog('#addPostboxWindow', {
            height: 450,
            width: 600,
            openUrl: loadUrl,
            title: "Add Postbox to user",
            closeButtonLabel: "Cancel",
            callback: function(){
                GeneralUsers.searchProducts();
            },
            buttons:[
                {
                    id: "saveBtn",
                    text: "Submit"
                }
            ]
        });        
    },
    // Search products
    searchPostboxProducts: function() {
        var user_id = $('#addEditUserForm_customer_id').val();
        var url = this.ajaxUrls.search_product + '?product_type=postbox' + '&customer_id=' + user_id;
        $("#dataGridProductResult").jqGrid('GridUnload');
        $("#dataGridProductResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 850,
            height: 110,
            rowNum: PostboxUsers.configs.rowNum,
            rowList: PostboxUsers.configs.rowList,
            pager: "#dataGridProductPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'ID', 'Postbox', 'Location', 'Type', 'Name', 'Company Name', 'Created Date', 'Status', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'code', index: 'code', width: 120, sortable: false},
                {name: 'postbox_name', index: 'postbox_name', width: 100, sortable: false},
                {name: 'location', index: 'location', width: 120, sortable: false},
                {name: 'type', index: 'type', width: 70, align: "left", sortable: false},
                {name: 'name', index: 'name', sortable: false},
                {name: 'company_name', index: 'company_name', sortable: false},
                {name: 'created_date', index: 'created_date', width: 100, align: "center", sortable: false},
                {name: 'status', index: 'status', width: 100, align: "left", sortable: false},
                {name: 'id', index: 'id', width: 50, sortable: false, align: "center", formatter: PostboxUsers.produdctActionFormater}
            ],
            loadComplete: function () {
            }
        });
    },
    produdctActionFormater: function(cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-times managetables-icon-delete-product" data-id="' + cellvalue + '" title="Delete"></span></span>';
        } else {
            return '';
        }
    },
    searchUser: function () {
        var product_label = "Postbox";
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: PostboxUsers.ajaxUrls.searchUser,
            postData: $('#searchUserForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: PostboxUsers.configs.rowNum,
            rowList: PostboxUsers.configs.rowList,
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
                {name: 'user_id', index: 'user_id', width: 120, sortable: false, align: "center", formatter: PostboxUsers.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-edit-user" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-times managetables-icon-delete managetables-icon-delete-user" data-id="' + cellvalue + '" title="Delete"></span></span>'
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
    }
};