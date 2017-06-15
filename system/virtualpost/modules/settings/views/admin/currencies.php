<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('settings_view_admin_currencies_Currencies'); ?></h2>
</div>
<div class="button_container">
    <div class="button-func">
        <button id="addCurrencyButton" class="admin-button"><?php admin_language_e('settings_view_admin_currencies_AddBtn'); ?></button>
    </div>
</div>
<div id="searchTableResult" style="margin-top: 10px;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="addCurrency" title="<?php admin_language_e('settings_view_admin_currencies_AddCurrency'); ?>" class="input-form dialog-form">
    </div>
    <div id="editCurrency" title="<?php admin_language_e('settings_view_admin_currencies_EditCurrency'); ?>" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('button').button();
        searchCurrencies(); // Call search method

        function searchCurrencies() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/settings/currencies';
            var tableH = $.getTableHeight()- 30;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#currencySearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height:tableH, //#1297 check all tables in the system to minimize wasted space,
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                sortname: 'currency_short',
                viewrecords: true,
                shrinkToFit: true,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames:[
                    '<?php admin_language_e('settings_view_admin_currencies_ID'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_Name'); ?>',
                    '<?php admin_language_e('settings_view_admin_currencies_Short'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_Sign'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_LastUpdate'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_RateEUR'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_Active'); ?>', 
                    '<?php admin_language_e('settings_view_admin_currencies_Action'); ?>'
                ],
                colModel:[
                    {name:'id',index:'currency_id', hidden: true},
                    {name:'Name',index:'currency_name', width:100, sortable: true},
                    {name:'Short',index:'currency_short', width:50, sortable: true, align:"center"},
                    {name:'Sign',index:'currency_sign', width:50, sortable: true, align:"center"},
                    {name:'Last update',index:'last_updated_date', width:50, sortable: true, align:"center"},
                    {name:'Rate/EUR',index:'currency_rate', width:50, sortable: true, align:"center"},
                    {name:'Active',index:'currency_rate', width:30, sortable: true, align:"center", formatter: activeFormater},
                    {name:'Action',index:'currency_id', width:75, sortable: false, align:"center", formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function(row_id,iRow,iCol,e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    console.log(data_row);
                },
                loadComplete: function() {
                    $.autoFitScreen($( window ).width()- 40);
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('settings_view_admin_currencies_Edit'); ?>"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('settings_view_admin_currencies_Delete'); ?>"></span></span>';
        }

        function activeFormater(cellvalue, options, rowObject) {
            if (cellvalue === '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('settings_view_admin_currencies_Check'); ?></span></span>';
            } else {
                return '';
            }
        }

        /**
         * Process when user click to search button
         */
        $('#currencySearchForm').click(function(e) {
            searchCurrencies();
            e.preventDefault();
        });

        /**
         * Process when user click to add group button
         */
        $('#addCurrencyButton').click(function() {

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#addCurrency').openDialog({
                autoOpen: false,
                height: 350,
                width: 450,
                modal: true,
                open: function() {
                    $(this).load("<?php echo base_url() ?>admin/settings/add_currency", function() {
                        $('#currency_name').focus();
                    });
                },
                buttons: {
                    '<?php admin_language_e('settings_view_admin_currencies_SaveBtn'); ?>': function() {
                        saveCurrency();
                    },
                    '<?php admin_language_e('settings_view_admin_currencies_CancelBtn'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#addCurrency').dialog('option', 'position', 'center');
            $('#addCurrency').dialog('open');
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function() {
            var currency_id = $(this).data('id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#editCurrency').openDialog({
                autoOpen: false,
                height: 350,
                width: 450,
                modal: true,
                open: function() {
                    $(this).load("<?php echo base_url() ?>admin/settings/edit_currency?currency_id=" + currency_id, function() {
                        $('#currency_name').focus();
                    });
                },
                buttons: {
                    '<?php admin_language_e('settings_view_admin_currencies_SaveBtn'); ?>': function() {
                        saveCurrency();
                    },
                    '<?php admin_language_e('settings_view_admin_currencies_CancelBtn'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#editCurrency').dialog('option', 'position', 'center');
            $('#editCurrency').dialog('open');
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function() {
            var currency_id = $(this).data('id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('settings_view_admin_currencies_ConfirmDeleteMessage'); ?>',
                yes: function() {
                    var submitUrl = '<?php echo base_url()?>admin/settings/delete_currency?currency_id=' + currency_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function(data) {
                            if (data.status) {
                                // Reload data grid
                                searchCurrencies();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

        /**
         * Save currency
         */
        function saveCurrency() {
            var submitUrl = $('#addEditCurrencyForm').attr('action');
            var action_type = $('#h_action_type').val();
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditCurrencyForm',
                success: function(data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#addCurrency').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#editCurrency').dialog('close');
                        }
                        $.displayInfor(data.message, null,  function() {
                            // Reload data grid
                            searchCurrencies();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>
