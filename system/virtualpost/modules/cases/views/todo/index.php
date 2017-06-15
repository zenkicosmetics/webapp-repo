<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_todo_index_YourChecklistyourToDos'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerSearchForm"
          action="<?php echo base_url() ?>cases/todo/index" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"
                     style="width: 100px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('cases_view_todo_index_Search'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="searchCustomerForm_enquiry"
                           name="enquiry" style="width: 248px" value=""
                           class="input-txt" maxlength=255/>
                    <button id="searchCaseButton" class="admin-button"><?php admin_language_e('cases_view_todo_index_Search'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="clear-height"></div>
<div id="caseItemCheckList">
    <div id="ChecklistTableResult">
        <div class="clear-height"></div>
        <table id="dataGridResult2"></table>
        <div id="dataGridPager2"></div>
    </div>
    <div class="clear-height"></div>
</div>
<div style="display: none;">
    <form id="xx" method="post"></form>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        //#1297 check all tables in the system to minimize wasted space
        var tableH = $.getTableHeight();

        // Call search method
        showAllTaskList();

        $('#searchCaseButton').button().live('click', function () {
            showAllTaskList();
        });

        /**
         * Search data
         * function searchCases()
         */
        function searchCases() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>cases/todo/index';

            $("#dataGridResult").jqGrid({
                url: url,
                mtype: 'POST',
                datatype: "json",
                height:tableH, //#1297 check all tables in the system to minimize wasted space
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: [
                    'id',
                    '<?php admin_language_e('cases_view_todo_index_OpeningDate'); ?>', ,
                    '<?php admin_language_e('cases_view_todo_index_CaseIdentifier'); ?>', ,
                    '<?php admin_language_e('cases_view_todo_index_Description'); ?>', ,
                    '<?php admin_language_e('cases_view_todo_index_Product'); ?>', ,
                    '<?php admin_language_e('cases_view_todo_index_Country'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Status'); ?>', ,
                    '<?php admin_language_e('cases_view_todo_index_Action'); ?>',
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'opening date', index: 'created_date', width: 100},
                    {name: 'case identifier', index: 'case_identifier', width: 100},
                    {name: 'description', index: 'description', width: 200},
                    {name: 'Partner', index: 'partner', width: 100},
                    {name: 'country name', index: 'country_name', width: 100},
                    {name: 'status', index: 'status', width: 100},
                    {
                        name: 'action',
                        index: 'action',
                        width: 100,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater
                    }
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    searchChecklist(data_row.id);
                    $('#caseItemCheckList').show();
                },
                loadComplete: function () {
                    //$.autoFitScreen(1100);
                     $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            if (cellvalue == '1') {
                return 'Completed';
            } else {
                return 'Processing';
            }
        }

        function showAllTaskList() {
            $("#dataGridResult2").jqGrid('GridUnload');

            // Defined url
            var url = '<?php echo base_url() ?>cases/todo/show_tasklist';

            // search
            $("#dataGridResult2").jqGrid({
                url: url,
                postData: {},
                mtype: 'POST',
                datatype: "json",
                height:tableH, //#1297 check all tables in the system to minimize wasted space,
                width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager2",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: [
                    '<?php admin_language_e('cases_view_todo_index_CaseID'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_BaseTaskName'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Milestone'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_CustomerCode'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_CustomerEmail'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Status'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Responsible'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Contact'); ?>',
                    '<?php admin_language_e('cases_view_todo_index_Yourtask'); ?>'
                ],
                colModel: [

                    {name: 'case_id', index: 'case_id', width: 100, align:"center", hidden: false},
                    {name: 'bas', index: 'case_id', hidden: true},
                    {name: 'milestone_name', index: 'milestone_name', width: 300},
                    {name: 'Customer code', index: 'customer_code', width: 200},
                    {name: 'Email', index: 'email', width: 400},
                    {name: 'status', index: 'status', width: 150, align:"center"},
                    {name: 'responsible', index: 'responsible', width: 200, align:"center"},
                    {name: 'contact', index: 'contact', width: 340},
                    {
                        name: 'action',
                        index: 'action',
                        width: 150,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater2
                    }
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult2').jqGrid("getRowData", row_id);
                },
                loadComplete: function () {
                    //$.autoFitScreen(1100);
                     $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
                    $("a.xx").click(function () {
                        var _this = $(this);
                        $("form#xx").attr('action', _this.data("action"));
                        $('<input>').attr({
                            type: 'hidden',
                            value: _this.data("caseId"),
                            name: 'case_id'
                        }).appendTo('form#xx');
                        $('<input>').attr({
                            type: 'hidden',
                            value: _this.data("op"),
                            name: 'op'
                        }).appendTo('form#xx');
                        $("form#xx").submit();
                    });
                }
            });
        }

        function actionFormater2(cellvalue, options, rowObject) {
            var case_id = rowObject[0];
            var action_name = rowObject[1];
            action = '<?php echo base_url() ?>cases/todo/review_' + action_name;

            return '<a class="xx" data-action="' + action + '" data-case-id="' + case_id + '" data-op="' +
                cellvalue + '" style="text-decoration:underline; color: #336699">' + cellvalue + '</a>';
        }
    });
</script>