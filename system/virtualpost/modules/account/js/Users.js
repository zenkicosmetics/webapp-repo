var Users = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        user: '',
        searchUser: '',
        addUser: '',
        editUser: '',
        deleteUser: '',
        changePassword: ''
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
        this.ajaxUrls.user = baseUrl + 'account/users/phone_users';
        this.ajaxUrls.searchUser = baseUrl + 'account/users/search_phone_users';
        this.ajaxUrls.addUser = baseUrl + 'account/users/add_phone_users';
        this.ajaxUrls.editUser = baseUrl + 'account/users/edit_phone_users';
        this.ajaxUrls.deleteUser = baseUrl + 'account/users/delete_phone_users';
        this.ajaxUrls.changePassword = baseUrl + 'account/users/change_password_phone_users';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        Users.initAjaxUrls(baseUrl);

        // init config.
        Users.configs.baseUrl = baseUrl;
        Users.configs.rowList = rowList.split(',');
        Users.configs.rowNum = rowNum;

        // init screen
        Users.searchUser();

        // add new user
        $("#btnAddNewUser").click(function () {
            Users.addNewUser();
        });

        $('.managetables-icon-edit').live('click', function () {
            var user_id = $(this).attr('data-id');
            Users.editUser(user_id);
        });

        $('.managetables-change-pass').live('click', function () {
            var user_id = $(this).attr('data-id');
            Users.changePassword(user_id);
        });

        $('.managetables-icon-delete').live('click', function () {
            var user_id = $(this).attr('data-id');
            Users.deleteUser(user_id);
        });
    },
    searchUser: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: Users.ajaxUrls.searchUser,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: 300,
            rowNum: Users.configs.rowNum,
            rowList: Users.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Name', 'Email', 'Activated', 'Created Date', 'Deleted', 'Action'],
            colModel: [
                {name: 'user_id', index: 'user_id', hidden: true},
                {name: 'name', index: 'name', width: 150},
                {name: 'email', index: 'email', width: 220},
                {name: 'activated', index: 'activated', width: 100, align: "center", sortable: false, formatter: Users.activatedFormater},
                {name: 'created_date', index: 'created_date', width: 120, align: "center", sortable: false},
                {name: 'deleted', index: 'deleted', width: 100, align: "center", formatter: Users.deletedFormater},
                {name: 'user_id', index: 'user_id', width: 120, sortable: false, align: "center", formatter: Users.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-times managetables-icon-delete" data-id="' + cellvalue + '" title="Delete"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-lock managetables-change-pass" data-id="' + cellvalue + '" title="Change Password"></span></span>';
        } else {
            return '';
        }
    },
    activatedFormater: function (cellvalue, options, rowObject) {
        if (cellvalue == 1) {
            return 'Yes';
        }
        return 'No';
    },
    deletedFormater: function (cellvalue, options, rowObject) {
        if (cellvalue === 1) {
            return 'Yes';
        }
        return 'No';
    },
    addNewUser: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addUser').openDialog({
            autoOpen: false,
            height: 500,
            width: 900,
            modal: true,
            open: function () {
                $(this).load(Users.ajaxUrls.addUser, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    Users.saveUser();
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
        $('.dialog-form').html('');

        // Open new dialog
        $('#editUser').openDialog({
            autoOpen: false,
            height: 500,
            width: 900,
            modal: true,
            open: function () {
                $(this).load(Users.ajaxUrls.editUser + "/" + user_id, function () {
                    $('#addEditUserForm_email').focus();
                });
            },
            buttons: {
                'Save': function () {
                    Users.saveUser();
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
                        Users.searchUser();
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
                    url: Users.ajaxUrls.deleteUser + "/" + user_id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            Users.searchUser();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    },
    changePassword: function (user_id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');
        var url = Users.ajaxUrls.changePassword + '?id=' + user_id;

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
                    Users.resetPasswordUser();
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
                        Users.searchUser();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

};