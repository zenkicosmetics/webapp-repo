<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_verification_index_CaseTrigger'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="countrySearchForm"
        action="<?php echo base_url()?>cases/admin_verification/index"
        method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"
                    style="width: 100px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('cases_view_admin_verification_index_SearchText'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" maxlength="255" class="input-txt"
                        value="" style="width: 248px" name="enquiry"
                        id="searchCustomerForm_enquiry">
                    <button id="searchCountryButton"
                        class="admin-button"><?php admin_language_e('cases_view_admin_verification_index_Search'); ?></button>
                    <button id="addCountryButton" class="admin-button"><?php admin_language_e('cases_view_admin_verification_index_Add'); ?></button>
                </div>
            </div>
        </div>
        <input type="hidden" id="countrySearchForm_setting_type"
            name="setting_type" value="" />
    </form>
</div>
<h2 style="font-size: 16px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_verification_index_InvoiceAddress'); ?></h2>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>

<div class="clear-height"></div>
<h2 style="font-size: 16px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_verification_index_Postbox'); ?></h2>
<div id="gridwraper02" style="margin: 0px;">
    <div id="searchTableResult02" style="margin-top: 10px;">
        <table id="dataGridResult02"></table>
        <div id="dataGridPager02"></div>
    </div>
</div>
<div class="clear-height"></div>

<h2 style="font-size: 16px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_verification_index_PhoneNumber'); ?></h2>
<div id="gridwraper03" style="margin: 0px;">
    <div id="searchTableResult03" style="margin-top: 10px;">
        <table id="dataGridResult03"></table>
        <div id="dataGridPager03"></div>
    </div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divEditCountry" title="<?php admin_language_e('cases_view_admin_verification_index_EditVerification'); ?>Edit Verification"
        class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();

    init();
    function init(){
        // Call search method
        searchCountryForInvoiceAddress();
        searchCountryForPostbox();
        searchCountryForPhoneNumber();
    }


    /**
     * When user click search button
     */
    $('#searchCountryButton').live('click', function() {
        init();
        return false;
    });

    /**
     * Search data
     */
    function searchCountryForInvoiceAddress() {
        $('#countrySearchForm_setting_type').val('1');
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/admin_verification/index';
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#countrySearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: '100%',
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'country_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_admin_verification_index_CountryName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_IsUserCompany'); ?>' ,
                '<?php admin_language_e('cases_view_admin_verification_index_LocationName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_RiskClass'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CompanyAddressField'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxName'); ?> ',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxNameOrPostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_SettingType'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CasesName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country_name',index:'country_name', width:250},
               {name:'is_user_company',index:'is_user_company', width:250,  hidden: true},
               {name:'location_name',index:'location_name', width:250, sortable: false, hidden: true},
               {name:'risk_class',index:'risk_class', width:155, formatter: riskClassFormater},
               {name:'invoice_address_verification',index:'invoice_address_verification', width:200, formatter: yesNoFormater, align:"center"},
               {name:'postbox_name_filled',index:'postbox_name_filled', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'private_postbox_verification',index:'private_postbox_verification', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'business_postbox_verification',index:'business_postbox_verification', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'setting_type',index:'setting_type', width:140, formatter: settingTypeFormater, hidden: true},
               {name:'list_case_number',index:'list_case_number', width:550, sortable: false},
               {name:'action',index:'action', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreenL(($( window ).width()- 40), $(this)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    /**
     * Search data
     */
    function searchCountryForPostbox() {
        $('#countrySearchForm_setting_type').val('2');
        $("#dataGridResult02").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/admin_verification/index';
        $("#dataGridResult02").jqGrid({
            url: url,
            postData: $('#countrySearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 350,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager02",
            sortname: 'location_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_admin_verification_index_CountryName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_IsUserCompany'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_LocationName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_RiskClass'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CompanyAddressField'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxNameOrPostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_SettingType'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CasesName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country_name',index:'country_name', width:150, hidden: true},
               {name:'is_user_company',index:'is_user_company', width:250,  hidden: true},
               {name:'location_name',index:'location_name', width:300, sortable: false},
               {name:'risk_class',index:'risk_class', width:100, formatter: riskClassFormater, hidden: true},
               {name:'invoice_address_verification',index:'invoice_address_verification', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'postbox_name_filled',index:'postbox_name_filled', width:150, formatter: yesNoFormater, align:"center", hidden: false},
               {name:'private_postbox_verification',index:'private_postbox_verification', width:150, formatter: yesNoFormater, align:"center", hidden: false},
               {name:'business_postbox_verification',index:'business_postbox_verification', width:150, formatter: yesNoFormater, align:"center", hidden: false},
               {name:'setting_type',index:'setting_type', width:140, formatter: settingTypeFormater, hidden: true},
               {name:'list_case_number',index:'list_case_number', width:940},
               {name:'action',index:'action', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreenL(($( window ).width()- 40), $(this)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    /**
     * Search data
     */
    function searchCountryForPhoneNumber() {
        $('#countrySearchForm_setting_type').val('3');
        $("#dataGridResult03").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/admin_verification/index';
        $("#dataGridResult03").jqGrid({
            url: url,
            postData: $('#countrySearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 250,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager03",
            sortname: 'country_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_admin_verification_index_CountryName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_IsUserCompany'); ?>' ,
                '<?php admin_language_e('cases_view_admin_verification_index_LocationName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_RiskClass'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CompanyAddressField'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxNameOrPostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_PostboxCompanyName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_SettingType'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_CasesName'); ?>',
                '<?php admin_language_e('cases_view_admin_verification_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country_name',index:'country_name', width:150},
               {name:'is_user_company',index:'is_user_company', width:250},
               {name:'location_name',index:'location_name', width:250, sortable: false, hidden: true},
               {name:'risk_class',index:'risk_class', width:100, formatter: riskClassFormater, hidden: true},
               {name:'invoice_address_verification',index:'invoice_address_verification', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'postbox_name_filled',index:'postbox_name_filled', width:140, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'private_postbox_verification',index:'private_postbox_verification', width:200, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'business_postbox_verification',index:'business_postbox_verification', width:200, formatter: yesNoFormater, align:"center", hidden: true},
               {name:'setting_type',index:'setting_type', width:140, formatter: settingTypeFormater, hidden: true},
               {name:'list_case_number',index:'list_case_number', width:360},
               {name:'action',index:'action', width:100, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreenL(($( window ).width()- 40), $(this)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    function riskClassFormater(cellvalue, options, rowObject) {
        if (cellvalue == 1) {
            return "<?php admin_language_e('cases_view_admin_verification_index_LowRisk'); ?>";
        } else if (cellvalue == 2) {
            return "<?php admin_language_e('cases_view_admin_verification_index_MediumRisk'); ?>";
        } else if (cellvalue == 3) {
            return "<?php admin_language_e('cases_view_admin_verification_index_HighRisk'); ?>";
        } else if (cellvalue == 4) {
            return "<?php admin_language_e('cases_view_admin_verification_index_NoService'); ?>";
        } else {
            return "";
        }
    }

    function settingTypeFormater(cellvalue, options, rowObject) {
        if (cellvalue == 1) {
            return "<?php admin_language_e('cases_view_admin_verification_index_InvoiceAddress'); ?>";
        } else if (cellvalue == 2) {
            return "<?php admin_language_e('cases_view_admin_verification_index_Postbox'); ?>";
        }
        return "";
    }

    function yesNoFormater(cellvalue, options, rowObject) {
        if (cellvalue == 1) {
            return "<?php admin_language_e('cases_view_admin_verification_index_Yes'); ?>";
        } else if (cellvalue == 0) {
            return "<?php admin_language_e('cases_view_admin_verification_index_No'); ?>" ;
        } else {
            return "";
        }
    }

    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-icon-edit-milestone" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('cases_view_admin_verification_index_Edit'); ?>"></span></span>'
        + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
        + '" title="<?php admin_language_e('cases_view_admin_verification_index_Delete'); ?>"></span></span>';
    }


    /**
     * When user click search button
     */
    $('#addCountryButton').live('click', function() {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditCountry').openDialog({
            autoOpen: false,
            height: 500,
            width: 850,
            modal: true,
            title: 'Add Verification',
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/admin_verification/edit?setting_type=1", function() {
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
        $('#divEditCountry').dialog('option', 'position', 'center');
        $('#divEditCountry').dialog('open');
        return false;
    });

    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit-milestone').live('click', function() {
        var country_id = $(this).data('id');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditCountry').openDialog({
            autoOpen: false,
            height: 500,
            width: 900,
            modal: true,
            title: 'Edit Verification',
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/admin_verification/edit?id=" + country_id, function() {
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
        $('#divEditCountry').dialog('option', 'position', 'center');
        $('#divEditCountry').dialog('open');
    });


    /**
     * Process when user click to delete icon.
     */
    $('.managetables-icon-delete').live('click', function() {
        var condition_id = $(this).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: 'Do you want to delete this condition?',
            yes: function() {
                deleteCondition(condition_id);
            }
        });
    });

    /**
     * Delete customer
     */
    function deleteCondition(condition_id) {
        var submitUrl = '<?php echo base_url()?>cases/admin_verification/delete?id=' + condition_id;
        $.ajaxExec({
             url: submitUrl,
             success: function(data) {
                 if (data.status) {
                     // Reload data grid
                     init();
                 } else {
                     $.displayError(data.message);
                 }
             }
         });
    }

    /**
     * Save group
     */
    function saveCountry() {
        var submitUrl = $('#addEditCountryForm').attr('action');
        var template = $("#list_case_number");
        $('option', template).prop('selected', true);
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditCountryForm",
            success: function(data) {
                if (data.status) {
                    $('#divEditCountry').dialog('close');
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        init();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
});
</script>