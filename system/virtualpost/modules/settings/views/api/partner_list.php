<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_partnerlist_BreadCrumb'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <h2><?php admin_language_e('setting_view_api_partnerlist_PartnerManagement'); ?></h2>
    <br />
    <button id="addPartnerButton" class="admin-button"><?php admin_language_e('setting_view_api_partnerlist_AddBtn'); ?></button>
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divAddPartner"  class="input-form dialog-form">
    </div>
    <div id="divEditPartner" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#addPartnerButton').button();

        // Call search method
        searchPartner();
        /**
         * Search data
         */
        function searchPartner() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>settings/api/partners';
            var tableH = $.getTableHeight() - 60;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#usesrSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                width: ($( window ).width()- 50), //#1297 check all tables in the system to minimize wasted space
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'id',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: [
                    '<?php admin_language_e('setting_view_api_partnerlist_ID'); ?>', 
                    '<?php admin_language_e('setting_view_api_partnerlist_AppCode'); ?>', 
                    '<?php admin_language_e('setting_view_api_partnerlist_AppName'); ?>', 
                    '<?php admin_language_e('setting_view_api_partnerlist_Key'); ?>', 
                    '<?php admin_language_e('setting_view_api_partnerlist_Version'); ?>',
                    '<?php admin_language_e('setting_view_api_partnerlist_Action'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'app_code', index: 'app_code', width: 500},
                    {name: 'app_name', index: 'app_name', width: 500},
                    {name: 'app_key', index: 'app_key', width: 500},
                    {name: 'version', index: 'version', width: 100, align: "center"},
                    {name: 'id', index: 'id', width: 200, sortable: false, align: "center", formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    console.log(data_row);
                },
                loadComplete: function () {
                    $.autoFitScreen($( window ).width()- 50);
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon" ></span>&nbsp;&nbsp;&nbsp;&nbsp;</span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('setting_view_api_partnerlist_Edit'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('setting_view_api_partnerlist_Delete'); ?>"></span></span>';
        }

        /**
         * Process when user click to add group button
         */
        $('#addPartnerButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');
            // Open new dialog
            $('#divAddPartner').openDialog({
                autoOpen: false,
                height: 370,
                width: 400,
                modal: true,
                title: '<?php admin_language_e('setting_view_api_partnerlist_AddPartnerTitle'); ?>',
                open: function () {
                    $(this).load("<?php echo base_url() ?>settings/api/edit_partners", function () {
                        $('#addEditPartnerForm_LocationName').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        savePartner();
                    },
                    'Cancel': function () {
                        $(this).dialog('destroy');
                    }
                }
            });
            $('#divAddPartner').dialog('option', 'position', 'center');
            $('#divAddPartner').dialog('open');
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function () {
            var id = $(this).data('id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#divAddPartner').openDialog({
                autoOpen: false,
                height: 370,
                width: 400,
                title: '<?php admin_language_e('setting_view_api_partnerlist_EditPartnerTitle'); ?>',
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>settings/api/edit_partners?id=" + id, function () {
                        $('#addEditPartnerForm_LocationName').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        savePartner();
                    },
                    'Cancel': function () {
                        $(this).dialog('destroy');
                    }
                }
            });
            $('#divAddPartner').dialog('option', 'position', 'center');
            $('#divAddPartner').dialog('open');
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var location_id = $(this).data('id');
            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('setting_view_api_partnerlist_ConfirmDeleteMessage'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>settings/api/delete_partners';
                    $.ajaxExec({
                        url: submitUrl,
                        data:{id: location_id},
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchPartner();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });

        /**
         * Save group
         */
        function savePartner() {
            var submitUrl = $('#addEditPartnerForm').attr('action');

            $.ajaxSubmit({
                url: submitUrl,
                formId: "addEditPartnerForm",
                success: function (data) {
                    if (data.status) {
                        $('#divAddPartner').dialog('destroy');
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            searchPartner();
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>