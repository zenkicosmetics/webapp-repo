<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('address_view_admin_locationdevices_LocationDevicesManagerment'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divAddDevice" title="<?php admin_language_e('address_view_admin_locationdevices_AddDevice'); ?>" class="input-form dialog-form">
    </div>
    <div id="divEditDevice" title="<?php admin_language_e('address_view_admin_locationdevices_EditDevice'); ?>" class="input-form dialog-form">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Call search method
        searchDevice();
        /**
         * Search data
         */
        function searchDevice() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>addresses/admin/devices';
            var tableH = $.getTableHeight() + 3;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#usesrSearchForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height: tableH, //#1297 check all tables in the system to minimize wasted space,
                width: ($(window).width() - 40), //#1297 check all tables in the system to minimize wasted space
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
                    '<?php admin_language_e('address_view_admin_locationdevices_ID'); ?>', 
                    '<?php admin_language_e('address_view_admin_locationdevices_PanelID'); ?>',
                    '<?php admin_language_e('address_view_admin_locationdevices_AssignedLocation'); ?>', 
                    '<?php admin_language_e('address_view_admin_locationdevices_Description'); ?>', 
                    '<?php admin_language_e('address_view_admin_locationdevices_Status'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'Panel ID', index: 'panel_code', width: 200},
                    {name: 'Assigned Location', index: 'location_name', width: 250},
                    {name: 'Description', index: 'description', width: 450},
                    {name: 'Status', index: 'status', width: 350}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    //console.log(data_row);
                },
                loadComplete: function () {
                    $.autoFitScreen($(window).width() - 50);  //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function activeFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick"><?php admin_language_e('address_view_admin_locationdevices_Check'); ?></span></span>';
            } else {
                return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete"><?php admin_language_e('address_view_admin_locationdevices_UnCheck'); ?></span></span>';
            }
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('address_view_admin_locationdevices_Edit'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('address_view_admin_locationdevices_Delete'); ?>"></span></span>';
        }
    });
</script>