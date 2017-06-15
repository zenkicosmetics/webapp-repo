<?php
$customer_id = APContext::getCustomerCodeLoggedIn();
$enable_create_case_button = (CaseUtils::isEnableCreateCase('1', $customer_id)
    || CaseUtils::isEnableCreateCase('2', $customer_id)
    || CaseUtils::isEnableCreateCase('3', $customer_id)
    || CaseUtils::isEnableCreateCase('4', $customer_id)
    || CaseUtils::isEnableCreateCase('5', $customer_id));

?>

<div class="ym-grid content services" id="case-body-wrapper">
    <div class="header">
        <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_index_CasesSystem'); ?></h2>
    </div>
    <div class="ym-clearfix"></div>

    <br/>
    <div class="ym-grid">
        <?php language_e('cases_view_index_FilterBy'); ?>: <select id="product_id" name="product_id"
                           class="input-txt" style="width: 200px;">
           <!-- <option value="">All</option> -->
            <?php //foreach ($products as $product) : ?>
                <option value="<?php echo $products[4]->id ?>"
                    <?php if (APConstants::OFF_FLAG == $products[4]->flag) {
                        echo "disabled";
                    } ?>
                    <?php if ($product_id == $products[4]->id) { ?>
                        selected="selected" <?php } ?>> <?php echo $products[4]->product_name ?></option>
            <?php //endforeach; ?>
        </select>
        <?php if ($enable_create_case_button) { ?>
            <button type="button" id="createNewCaseButton"
                    style="margin-left: 5px">+ <?php language_e('cases_view_index_CreateNewCase'); ?>
            </button>
        <?php } ?>
    </div>
    <div class="ym-clearfix"></div>

    <br/>
    <div id="searchTableResult">
        <div class="clear-height"></div>
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
    <div class="clear-height"></div>
    <div id="caseItemCheckList" style="display: none;">
        <br/>
        <h3><?php language_e('cases_view_index_YourChecklistyourToDos'); ?></h3>
        <br/>
        <div id="ChecklistTableResult">
            <div class="clear-height"></div>
            <table id="dataGridResult2"></table>
            <div id="dataGridPager2"></div>
        </div>
    </div>
    <div class="clear-height"></div>
    <div id="caseFullfillmentProcess" style="display: none;">
        <br/>
        <h3><?php language_e('cases_view_index_FullfillmentProcess'); ?></h3>
        <br/>
        <div id="ProcesslistTableResult">
            <div class="clear-height"></div>
            <table id="dataGridResult3"></table>
            <div id="dataGridPager3"></div>
        </div>
    </div>
    <div class="clear-height"></div>
</div>
<!-- Content for dialog -->
<div class="hide">
    <div id="createNewCaseWindow" title="Create New Case"
         class="input-form dialog-form"></div>
</div>
<style>
    <!--
    .state-processing {
        background-color: #ffea82;
    }

    -->
     .ui-jqgrid tr.jqgrow td {
        white-space: normal !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {

        // Call search method
        searchCases();

        // When change product id
        $('#product_id').change(function () {
            searchCases();
            $('#caseItemCheckList').hide();
            $('#caseFullfillmentProcess').hide();
        });

        /**
         * Search data
         */
        function searchCases() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>cases';
            var product_id = $('#product_id').val();
            $("#dataGridResult").jqGrid({
                url: url,
                postData: {product_id: product_id},
                mtype: 'POST',
                datatype: "json",
                height: 250,
                width: 1000,
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: [
                    '<?php language_e('cases_view_index_ID'); ?>',
                    '<?php language_e('cases_view_index_OpeningDate'); ?>',
                    '<?php language_e('cases_view_index_CaseIdentifier'); ?>',
                    '<?php language_e('cases_view_index_Description'); ?>',
                    '<?php language_e('cases_view_index_User'); ?>',
                    '<?php language_e('cases_view_index_Partner'); ?>',
                    '<?php language_e('cases_view_index_Country'); ?>',
                    '<?php language_e('cases_view_index_Status'); ?>',
                    '<?php language_e('cases_view_index_Action'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', width: 40, hidden: false},
                    {name: 'opening_date', index: 'opening_date', width: 80, sortable: false},
                    {name: 'case_identifier', index: 'case_identifier', width: 150, sortable: false},
                    {name: 'description', index: 'description', width: 150, sortable: false},
                    {name:'user_name ', index:'user_name', width: 100, sortable: false},
                    {name: 'partner ', index: 'partner', width: 100, sortable: false},
                    {name: 'country_name', index: 'country_name', width: 120, sortable: false},
                    {name: 'status', index: 'status', width: 90, sortable: false},
                    {
                        name: 'action',
                        index: 'action',
                        width: 125,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater
                    }
                ],
                afterInsertRow: function (id, data) {
                    var trElement = $("#" + id, $('#dataGridResult'));
                    if (data.action != '2') {
                        trElement.addClass('state-processing');
                    }
                },
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {

                },
                onSelectRow: function (row_id) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    searchChecklist(data_row.id);
                    $('#caseItemCheckList').show();
                    $('#caseFullfillmentProcess').show();
                },
                loadComplete: function () {
                    //$.autoFitScreen(1100);
                    <?php if(empty($case_id)){ ?>
                    var top_rowid = $('#dataGridResult tbody tr:first').next().attr('id');
                    <?php } else {?>
                    var top_rowid = <?php echo $case_id;?>;
                    <?php }?>
                    if (top_rowid) {
                        $("#dataGridResult").setSelection(top_rowid, true);
                    }
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            if (cellvalue == '2') {
                return 'Completed';
            } else {
                return 'Processing';
            }
        }

        function searchChecklist(case_id) {
            $("#dataGridResult2").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>cases/show_checklist';

            $("#dataGridResult2").jqGrid({
                url: url,
                postData: {case_id: case_id},
                mtype: 'POST',
                datatype: "json",
                height: 250,
                width: 1000,
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager2",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: [
                    '<?php language_e('cases_view_index_ProductID'); ?>',
                    '<?php language_e('cases_view_index_CaseID'); ?>',
                    '',
                    '<?php language_e('cases_view_index_Milestone'); ?>',
                    '<?php language_e('cases_view_index_Status'); ?>',
                    '<?php language_e('cases_view_index_YourTask'); ?>',
                    '<?php language_e('cases_view_index_Comment'); ?>',
                    ''
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'case_id', index: 'case_id', hidden: true},
                    {name: 'status', index: 'status', hidden: true},
                    {name: 'milestone_name', index: 'task_name', width: 280},
                    {name: 'status_value', index: 'status_value', width: 100},
                    {
                        name: 'your_task',
                        index: 'your_task',
                        width: 100,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater2
                    },
                    {
                        name: 'comment_content',
                        index: 'comment_content',
                        width: 500,
                        sortable: false,
                        title:false,
                        formatter: comment2
                    },
                    {name: 'base_task_name', index: 'base_task_name', hidden: true}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult2').jqGrid("getRowData", row_id);
                },
                loadComplete: function () {
                    //$.autoFitScreen(1100);
                    if ($("#dataGridResult2").getGridParam("records") == 0) {
                        var _temp = $("#caseItemCheckList").find("div.ui-jqgrid-bdiv:first");
                        _temp.height(30);
                        _temp.append("<div style='margin: 8px auto auto 10px;font-weight: bold;font-size: 1.1em;'>You currently have no task to perform for this case.</div>");
                    }
                }
            });

            $("#dataGridResult3").jqGrid('GridUnload');
            url = '<?php echo base_url() ?>cases/show_tasklist';

            $("#dataGridResult3").jqGrid({
                url: url,
                postData: {case_id: case_id},
                mtype: 'POST',
                datatype: "json",
                height: 250,
                width: 1000,
                rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager3",
                viewrecords: true,
                shrinkToFit: false,
                captions: '',
                colNames: [
                    '',
                    '<?php language_e('cases_view_index_CaseID'); ?>',
                    '<?php language_e('cases_view_index_ProductID'); ?>',
                    '<?php language_e('cases_view_index_Milestone'); ?>',
                    '<?php language_e('cases_view_index_BaseTaskName'); ?>',
                    '<?php language_e('cases_view_index_Status'); ?>',
                    '',
                    '<?php language_e('cases_view_index_Responsible'); ?>',
                    '<?php language_e('cases_view_index_Contact'); ?>',
                    '<?php language_e('cases_view_index_YourTask'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'case_id', index: 'case_id', width: 50, hidden: true},
                    {name: 'product_id', index: 'product_id', hidden: true},
                    {name: 'task_name', index: 'task_name', width: 250},
                    {name: 'base_task_name', index: 'base_task_name', hidden: true, sortable: false},
                    {name: 'status', index: 'status', width: 100},
                    {name: 'status_value', index: 'status_value', hidden: true, sortable: false},
                    {name: 'responsible', index: 'responsible', width: 150},
                    {name: 'contact', index: 'contact', width: 250, formatter: emailFormater3},
                    {
                        name: 'action',
                        index: 'action',
                        width: 225,
                        sortable: false,
                        align: "center",
                        formatter: actionFormater3
                    }
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult2').jqGrid("getRowData", row_id);
                },
                loadComplete: function () {
                    //$.autoFitScreen(1100);
                }
            });
        }

        function comment2(cellvalue, options, rowObject) {
            var product_id = rowObject[0];
            var case_id = rowObject[1];
            var status = rowObject[2];

            if (status != '3') {
                return '';
            } else {
                return cellvalue;
            }
        }

        function actionFormater2(cellvalue, options, rowObject) {
            var product_id = rowObject[0];
            var case_id = rowObject[1];
            var status = rowObject[2];
            var action_name = rowObject[7];

            if (product_id == '1') {
                linkUrl = '<?php echo base_url() ?>cases/bankaccount/' + action_name + '?case_id=' + case_id;
            } else if (product_id == '5') {
                linkUrl = '<?php echo base_url() ?>cases/verification/' + action_name + '?case_id=' + case_id;
            }

            if ((status == 0) || (status == 3)) {
                return '<a class="main_link_color" href="' + linkUrl + '" style="text-decoration:underline;">' + cellvalue + '</a>';
            } else {
                return cellvalue;
            }
        }

        function emailFormater3(cellvalue, options, rowObject) {
            return '<a href="mailto:' + cellvalue + '" style="text-decoration:underline;" class="main_link_color" >' + cellvalue + '</a>';
        }

        function actionFormater3(cellvalue, options, rowObject) {
            var case_id = rowObject[1];
            var product_id = rowObject[2];
            var action_name = rowObject[4];
            var status = rowObject[6];

            if (product_id == '1') {
                linkUrl = '<?php echo base_url() ?>cases/bankaccount/' + action_name + '?case_id=' + case_id;
            } else if (product_id == '5') {
                linkUrl = '<?php echo base_url() ?>cases/verification/' + action_name + '?case_id=' + case_id;
            }

            if ((status == 0) || (status == 3)) {
                return '<a href="' + linkUrl + '" style="text-decoration:underline;" class="main_link_color" >' + cellvalue + '</a>';
            } else {
                return cellvalue;
            }
        }

        /**
         * Process when user click to add group button
         */
        $('#createNewCaseButton').click(function () {
            // Clear control of all dialog form
            $('.dialog-form').html('');
            var product_id = $('#product_id').val();

            // Open new dialog
            $('#createNewCaseWindow').openDialog({
                autoOpen: false,
                height: 200,
                width: 450,
                modal: true,
                open: function () {
                    $(this).load('<?php echo base_url()?>cases/create?product_id=' + product_id, function () {

                    });
                },
                buttons: {
                    'Save': function () {
                        saveCases($(this));
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#createNewCaseWindow').dialog('option', 'position', 'center');
            $('#createNewCaseWindow').dialog('open');
            return false;
        });

        /**
         * Save Customer
         */
        function saveCases(obj) {
            var submitUrl = $('#addEditCaseForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addEditCaseForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            obj.dialog('close');
                            // Reload data grid
                            //searchCases();
                            document.location = '<?php echo base_url()?>cases?product_id=' + data.data["product_id"];
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>