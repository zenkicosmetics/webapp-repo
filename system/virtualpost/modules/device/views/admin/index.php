<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('device_view_admin_index_DevicesManagement'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 0px;">
    <button id="addDeviceButton" class="admin-button"><?php admin_language_e('device_view_admin_index_AddBtn'); ?></button>
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divAddDevice" title="<?php admin_language_e('device_view_admin_index_AddDevice'); ?>" class="input-form dialog-form">
    </div>
    <div id="divEditDevice" title="<?php admin_language_e('device_view_admin_index_EditDevice'); ?>" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //#1297 check all tables in the system to minimize wasted space
         var tableH = $.getTableHeight() - 18;
         
        // Button 
        $('#addDeviceButton').button();

        // Call search method
        searchDevice();
        
        /**
         * Search data device 
         * function searchDevice()
         */
        function searchDevice() {
            $("#dataGridResult").jqGrid('GridUnload');
            
            // url defined 
            var url = '<?php echo base_url() ?>admin/device';
           
           // Search 
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#usesrSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height: tableH, //#1297 check all tables in the system to minimize wasted space
                width: ($(window).width() - 60), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'panel_code',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: [
                    '<?php admin_language_e('device_view_admin_index_ID'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_ID'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_Type'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_AssignedLocation'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_Description'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_Timezone'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_CurrentRevision'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_LastDataUpdate'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_Status'); ?>', 
                    '<?php admin_language_e('device_view_admin_index_Action'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'ID', index: 'panel_code', width: 130},
                    {name: 'Type', index: 'type', width: 150, align: "center"},
                    {name: 'Assigned Location', index: 'location_name', width: 150},
                    {name: 'Description', index: 'description', width: 360},
                    {name: 'Timezone', index: 'timezone', width: 200, align: "center"},
                    {name: 'Current Revision', index: 'current_revision', width: 150, align: "center"},
                    {name: 'Last Data Update', index: 'last_data_update', width: 250, align: "center"},
                    {name: 'Status', index: 'status', width: 250, align: "center"},
                    {name: 'id', index: 'id', width: 150, sortable: false, align: "center", formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    console.log(data_row);
                },
                loadComplete: function () {
                    $.autoFitScreen(($(window).width() - 40)); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function activeFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('device_view_admin_index_Check'); ?></span></span>';
            } else {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete"><?php admin_language_e('device_view_admin_index_UnCheck'); ?></span></span>';
            }
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('device_view_admin_index_Edit'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('device_view_admin_index_Delete'); ?>"></span></span>';
        }

        /**
         * Process when user click to add group button
         */
        $('#addDeviceButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');
            // Open new dialog
            $('#divAddDevice').openDialog({
                autoOpen: false,
                height: 450,
                width: 950,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>device/admin/add", function () {
                    });
                },
                buttons: {
                    'Save': function () {
                        saveDevice();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#divAddDevice').dialog('option', 'position', 'center');
            $('#divAddDevice').dialog('open');
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function () {
            var location_id = $(this).data('id');

            // Clear control of all dialog form
            $('.dialog-form').html('');

            // Open new dialog
            $('#divEditDevice').openDialog({
                autoOpen: false,
                height: 450,
                width: 950,
                modal: true,
                open: function () {
                    $(this).load("<?php echo base_url() ?>device/admin/edit?id=" + location_id, function () {
                    });
                },
                buttons: {
                    'Save': function () {
                        saveDevice();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#divEditDevice').dialog('option', 'position', 'center');
            $('#divEditDevice').dialog('open');
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var location_id = $(this).data('id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('device_view_admin_index_ConfirmDeleteMessage'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>device/admin/delete?id=' + location_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchDevice();
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
        function saveDevice() {
            var submitUrl = $('#addEditDeviceForm').attr('action');
            var action_type = $('#h_action_type').val();

            $.ajaxSubmit({
                url: submitUrl,
                formId: "addEditDeviceForm",
                success: function (data) {
                    if (data.status) {
                        if (action_type == 'add') {
                            $('#divAddDevice').dialog('close');
                        } else if (action_type == 'edit') {
                            $('#divEditDevice').dialog('close');
                        }
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            searchDevice();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>