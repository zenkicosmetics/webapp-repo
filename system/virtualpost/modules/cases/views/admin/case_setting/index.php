<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_case_setting_index_CaseSetting'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="caseSearchForm"   action="<?php echo base_url()?>cases/admin_case_setting/index"    method="post">
        <button id="addCaseButton" class="admin-button"><?php admin_language_e('cases_view_admin_case_setting_index_Add'); ?></button>
    </form>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divEditCaseSetting" title="<?php admin_language_e('cases_view_admin_case_setting_index_EditCaseSetting'); ?>"
        class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();

    // Call search method
    searchCaseSetting();

    /**
     * When user click search button
     */
    $('#searchCountryButton').live('click', function() {
        searchCaseSetting();
        return false;
    });

    /**
     * Search data
     */
    function searchCaseSetting() {
        $('#caseSearchForm_setting_type').val('1');
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/admin_case_setting/index';
        var tableH = $.getTableHeight() + 10;
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#caseSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'product_id',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '<?php admin_language_e('cases_view_admin_case_setting_index_CaseID'); ?>',
                '<?php admin_language_e('cases_view_admin_case_setting_index_CaseType'); ?>',
                '<?php admin_language_e('cases_view_admin_case_setting_index_ProductID'); ?>',
                '<?php admin_language_e('cases_view_admin_case_setting_index_CaseName'); ?>', 
                '<?php admin_language_e('cases_view_admin_case_setting_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', width:200, align:"center", hidden: false},
               {name:'product_name',index:'product_name', width:350, align:"center"},
               {name:'product_id',index:'product_id', width:250, sortable: false, hidden: true},
               {name:'case_name',index:'case_name', width:1000},
               {name:'action',index:'action', width:250, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40)); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    function yesNoFormater(cellvalue, options, rowObject) {
        if (cellvalue == 1) {
            return "<?php admin_language_e('cases_view_admin_case_setting_index_Yes'); ?>";
        } else if (cellvalue == 0) {
            return "<?php admin_language_e('cases_view_admin_case_setting_index_No'); ?>" ;
        } else {
            return "";
        }
    }

    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-icon-edit-milestone" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('cases_view_admin_case_setting_index_Edit'); ?>"></span></span>'
        + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
        + '" title="<?php admin_language_e('cases_view_admin_case_setting_index_Delete'); ?>"></span></span>';
    }

    /**
     * When user click search button
     */
    $('#addCaseButton').live('click', function() {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditCaseSetting').openDialog({
            autoOpen: false,
            height: 240,
            width: 495,
            modal: true,
            title: '<?php admin_language_e('cases_view_admin_case_setting_index_AddCasesSetting'); ?>',
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/admin_case_setting/edit", function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveCaseSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditCaseSetting').dialog('option', 'position', 'center');
        $('#divEditCaseSetting').dialog('open');
        return false;
    });


    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit-milestone').live('click', function() {
        var case_id = $(this).data('id');

        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditCaseSetting').openDialog({
            autoOpen: false,
            height: 325,
            width: 530,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/admin_case_setting/edit?id=" + case_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveCaseSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditCaseSetting').dialog('option', 'position', 'center');
        $('#divEditCaseSetting').dialog('open');
    });


    /**
     * Save group
     */
    function saveCaseSetting() {
        var submitUrl = $('#addEditCaseSettingForm').attr('action');
        var template = $("#list_milestone_id");
        $('option', template).prop('selected', true);
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditCaseSettingForm",
            success: function(data) {
                if (data.status) {
                    $('#divEditCaseSetting').dialog('close');
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        searchCaseSetting();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * Process when user click to delete icon.
     */
    $('.managetables-icon-delete').live('click', function() {
        var condition_id = $(this).attr('data-id');

        // Show confirm dialog
        $.confirm({
            message: '<?php admin_language_e('cases_view_admin_case_setting_index_ConfirmDeleteMessage'); ?>',
            yes: function() {
                deleteCase(condition_id);
            }
        });
    });

    /**
     * Delete customer
     */
    function deleteCase(case_id) {
        var submitUrl = '<?php echo base_url()?>cases/admin_case_setting/delete?id=' + case_id;
        $.ajaxExec({
             url: submitUrl,
             success: function(data) {
                 if (data.status) {
                     // Reload data grid
                     searchCaseSetting();
                 } else {
                     $.displayError(data.message);
                 }
             }
         });
    }

});
</script>