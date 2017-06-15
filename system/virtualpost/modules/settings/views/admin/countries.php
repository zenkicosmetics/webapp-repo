<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('settings_view_admin_countries_Countries'); ?></h2>
</div>
<div class="button_container">
    <div class="button-func">
        <button id="addCountryButton" class="admin-button"><?php admin_language_e('settings_view_admin_countries_AddBtn'); ?></button>
    </div>
</div>
<div id="searchTableResult" style="margin-top: 10px;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="addCountry" title="<?php admin_language_e('settings_view_admin_countries_AddCountry'); ?>" class="input-form dialog-form">
    </div>
    <div id="editCountry" title="<?php admin_language_e('settings_view_admin_countries_EditCountry'); ?>" class="input-form dialog-form">
    </div>
</div>

<script>
    $(document).ready(function() {
        $('button').button();
        searchCountries(); // Call search method

        /**
         * Process when user click to search button
         */
        $('#countrySearchForm').click(function(e) {
            searchCountries();
            e.preventDefault();
        });

        /**
         * Process when user click to add group button
         */
        $('#addCountryButton').click(function() {

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#addCountry').openDialog({
                autoOpen: false,
                height: 425,
                width: 725,
                modal: true,
                open: function() {
                    $(this).load("<?php echo base_url() ?>admin/settings/add_country", function() {
                        $('#country_name').focus();
                    });
                },
                buttons: {
                    '<?php admin_language_e('settings_view_admin_countries_SaveBtn'); ?>': function() {
                        saveCountry();
                    },
                    '<?php admin_language_e('settings_view_admin_countries_CancelBtn'); ?>': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#addCountry').dialog('option', 'position', 'center');
            $('#addCountry').dialog('open');
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function() {
            var country_id = $(this).data('id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#editCountry').openDialog({
                autoOpen: false,
                height: 425,
                width: 725,
                modal: true,
                open: function() {
                    $(this).load("<?php echo base_url() ?>admin/settings/edit_country?country_id=" + country_id, function() {
                        $('#country_name').focus();
                    });
                },
                buttons: {
                    'Save': function() {
                        saveCountry();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#editCountry').dialog('option', 'position', 'center');
            $('#editCountry').dialog('open');
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function() {
            var country_id = $(this).data('id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('settings_view_admin_countries_ConfirmDeleteMessage'); ?>',
                yes: function() {
                    var submitUrl = '<?php echo base_url()?>admin/settings/delete_country?country_id=' + country_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function(data) {
                            if (data.status) {
                                // Reload data grid
                                searchCountries();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

        /**
         * Save country
         */
        function saveCountry() {
            var submitUrl = $('#addEditCountryForm').attr('action');
            var action_type = $('#h_action_type').val();
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditCountryForm',
                success: function(data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#addCountry').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#editCountry').dialog('close');
                        }
                        $.displayInfor(data.message, null,  function() {
                            // Reload data grid
                            searchCountries();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });

    function searchCountries() {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>admin/settings/countries';
        var tableH = $.getTableHeight()- 30;
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#countrySearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height:tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'country_code',
            viewrecords: true,
            shrinkToFit: true,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('settings_view_admin_countries_ID'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_CountryName'); ?>',
                '<?php admin_language_e('settings_view_admin_countries_CountryCode'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_IsEUMember'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_Language'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_Currency'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_DecimalSeparator'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_RiskClass'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_LetterNational'); ?>',
                '<?php admin_language_e('settings_view_admin_countries_LetterInternational'); ?>',
                '<?php admin_language_e('settings_view_admin_countries_PackageNational'); ?>',
                '<?php admin_language_e('settings_view_admin_countries_PackageInternational'); ?>', 
                '<?php admin_language_e('settings_view_admin_countries_Action'); ?>'
            ],
            colModel:[
                {name:'id',index:'id', hidden: true},
                {name:'Country Name',index:'country_name', width:100, sortable:true},
                {name:'Country Code',index:'country_code', width:50, sortable:true, align:"center"},
                {name:'Is EU Member?',index:'eu_member_flag', width:50, sortable:true, align:"center", formatter: yesNoFormater},
                {name:'Language',index:'language', width:50, sortable:true, align:"center"},
                {name:'Currency',index:'currency_id', width:50, sortable:true, align:"center"},
                {name: 'Decimal Separator', index: 'decimal_separator', width:60, sortable:false, align:"center", formatter: decimalSeparatorFormatter},
                {name:'Risk Class',index:'risk_class', width:50, sortable:true, align:"center", formatter: riskClassFormater},
                {name:'letter_national_price',index:'letter_national_price', width:50, sortable:false, align:"center"},
                {name:'letter_international_price',index:'letter_international_price', width:50, sortable:false, align:"center"},
                {name:'package_national_price',index:'package_national_price', width:50, sortable:false, align:"center"},
                {name:'package_international_price',index:'package_international_price', width:50, sortable:false, align:"center"},
                {name:'Action',index:'id', width:75, sortable:false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('settings_view_admin_countries_Edit'); ?>"></span></span>'
            + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
            + '" title="<?php admin_language_e('settings_view_admin_countries_Delete'); ?>"></span></span>';
    }

    function yesNoFormater(cellvalue, options, rowObject) {
        if (cellvalue === '1') {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('settings_view_admin_countries_Check'); ?></span></span>';
        } else {
            return '';
        }
    }

    function riskClassFormater (cellvalue, options, rowObject) {
        if (cellvalue == '1') {
            return '<?php admin_language_e('settings_view_admin_countries_LowRisk'); ?>';
        } else  if (cellvalue == '2') {
            return '<?php admin_language_e('settings_view_admin_countries_MediumRisk'); ?>';
        } else  if (cellvalue == '3') {
            return '<?php admin_language_e('settings_view_admin_countries_HighRisk'); ?>';
        }  else  if (cellvalue == '4') {
            return '<?php admin_language_e('settings_view_admin_countries_NoService'); ?>';
        }
        return '';
    }

    function decimalSeparatorFormatter(cellvalue, options, rowObject) {
        if (cellvalue == ',') {
            return '<?php admin_language_e('settings_view_admin_countries_Comma'); ?>';
        } else if (cellvalue == '.') {
            return '<?php admin_language_e('settings_view_admin_countries_Dot'); ?>';
        } else {
            return '<?php admin_language_e('settings_view_admin_countries_Comma'); ?>'; // by default
        }
    }

</script>