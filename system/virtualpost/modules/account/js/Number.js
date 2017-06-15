var Number = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        number: '',
        searchNumber: '',
        addNumber: '',
        deleteNumber: ''
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
        this.ajaxUrls.number = baseUrl + 'account/Number';
        this.ajaxUrls.searchNumber = baseUrl + 'account/number/search';
        this.ajaxUrls.addNumber = baseUrl + 'account/number/add';
        this.ajaxUrls.editNumber = baseUrl + 'account/number/edit';
        this.ajaxUrls.deleteNumber = baseUrl + 'account/number/delete';
    },
    /*
     *  Initialize interface
     */
    init: function (baseUrl, rowNum, rowList) {
        // init data
        Number.initAjaxUrls(baseUrl);

        // init config.
        Number.configs.baseUrl = baseUrl;
        Number.configs.rowList = rowList.split(',');
        Number.configs.rowNum = rowNum;

        // init screen
        Number.searchNumber();

        // add new user
        $("#btnAddNewNumber").live('click', function () {
            Number.addNewNumber();
        });
        
        // edit existing user
        $(".managetables-edit").live('click', function () {
            var number_id = $(this).attr('data-id');
            console.log(number_id);
            Number.editPhoneNumber(number_id);
        });

        $('.managetables-delete').live('click', function () {
            var number_id = $(this).attr('data-id');
            Number.deleteNumber(number_id);
        });
        
        // Save phone number
        $('#bookSelectedNumberButton').live('click', function(){
            if($("#addEditNumberForm_confirm_terms_condition").prop('checked') != true){
                $.error({message: "In order to use our services, you must agree to ClevverMail's Terms of Service."});
                return;
            }
            Number.saveNumber();
            return false;
        });
    },
    searchNumber: function () {
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            url: Number.ajaxUrls.searchNumber,
            mtype: 'POST',
            datatype: "json",
            width: 1000,
            height: '100%',
            rowNum: Number.configs.rowNum,
            rowList: Number.configs.rowList,
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Number', 'Country', 'Location', 'End Point', 'Auto Renewal', 'Created Date', 'End Contract Date', 'Action'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'phone_number', index: 'phone_number', width: 100},
                {name: 'country', index: 'country', width: 150, sortable: false},
                {name: 'area', index: 'area', width: 200, sortable: false},
                {name: 'end_point', index: 'end_point', width: 125, sortable: false},
                {name: 'auto_renewal', index: 'auto_renewal', width: 75, sortable: false},
                {name: 'created_date', index: 'created_date', width: 110},
                {name: 'end_contract_date', index: 'end_contract_date', width: 120},
                {name: 'id', index: 'id', width: 80, sortable: false, align: "center", formatter: Number.actionFormater}
            ],
            loadComplete: function () {
                $.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    },
    actionFormater: function (cellvalue, options, rowObject) {
        if (cellvalue !== -1) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-edit" data-id="' + cellvalue + '" title="Edit"></span></span>'
                + '<span style="display:inline-block;"><span class="fa fa-times managetables-delete" data-id="' + cellvalue + '" title="Delete"></span></span>';
        } else {
            return '';
        }
    },
    addNewNumber: function () {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addNumberWindow').openDialog({
            autoOpen: false,
            height: 550,
            width: 850,
            modal: true,
            open: function () {
                $(this).load(Number.ajaxUrls.addNumber, function () {
                    $('#addEditNumberForm_email').focus();
                });
            }
            /**
            buttons: {
                'Save': function () {
                    Number.saveNumber();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
            */
        });
        $('#addNumberWindow').dialog('option', 'position', 'center');
        $('#addNumberWindow').dialog('open');
        return false;
    },
    editPhoneNumber: function (id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#editNumberWindow').openDialog({
            autoOpen: false,
            height: 220,
            width: 400,
            modal: true,
            open: function () {
                $(this).load(Number.ajaxUrls.editNumber + '?id=' + id, function () {
                });
            },
            buttons: {
                'Save': function () {
                    Number.savePhoneNumber();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editNumberWindow').dialog('option', 'position', 'center');
        $('#editNumberWindow').dialog('open');
        return false;
    },
    savePhoneNumber: function () {
        var submitUrl = $('#addEditNumberForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditNumberForm',
            success: function (data) {
                if (data.status) {
                    $('#editNumberWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        Number.searchNumber();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    saveNumber: function () {
        var $selRadio = $('input[name=radio_dataGridResult_phonelist]:checked');
        if ($selRadio.length  === 0) {
            $.displayError('Please select phone number in the list.');
            return;
        }
        var selectPhoneNumber = $selRadio.val();
        var range = $selRadio.attr('data-range');
        var initial_amount = $selRadio.attr('data-initial_amount'); 
        $('#addEditNumberForm_phone_number').val(selectPhoneNumber);
        $('#addEditNumberForm_range').val(initial_amount);
        $('#addEditNumberForm_initial_amount').val(range);

        var submitUrl = $('#addEditNumberForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEditNumberForm',
            success: function (data) {
                if (data.status) {
                    $('#addNumberWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        // Reload data grid
                        Number.searchNumber();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    },
    deleteNumber: function (number_id) {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this number? ',
            yes: function () {
                $.ajaxExec({
                    url: Number.ajaxUrls.deleteNumber + "/" + number_id,
                    success: function (data) {
                        if (data.status) {
                            // Reload data grid
                            Number.searchNumber();
                        } else {
                            $.displayError(data.message);
                        }
                    }
                });
            }
        });
    }

};