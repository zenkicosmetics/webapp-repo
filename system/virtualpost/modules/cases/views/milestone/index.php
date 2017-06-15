<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_milestone_index_MilestoneMangement'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="milestoneSearchForm"  action="<?php echo base_url()?>cases/milestone/index"   method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"
                    style="width: 100px; text-align: left; display: none">
                    <label style="text-align: left;"><?php admin_language_e('cases_view_milestone_index_Cases'); ?></label>
                </div>
                <div class="ym-g80 ym-gl">
                    <!-- ?php
                    echo my_form_dropdown(array(
                        "data" => $list_products,
                        "value_key" => 'id',
                        "label_key" => 'product_name',
                        "value" => APContext::getLocationUserSetting(),
                        "name" => 'product_id',
                        "id" => 'product_id',
                        "clazz" => 'input-width',
                        "style" => '',
                        "has_empty" => false
                    ));
                    ?-->
                    <!--
                    <select id="product_id" name="product_id"
                        class="input-width">
                        <option value="" <?php if ($product_id == '') {?>selected="selected" <?php }?>>All</option>
                        <?php foreach ( $list_products as $product ) :?>
                        <option value="<?php echo $product->id?>"
                            <?php if(APConstants::OFF_FLAG== $product->flag){ echo "disabled";}?>
                            <?php if ($product_id == $product->id) {?>
                            selected="selected" <?php }?>> <?php echo $product->product_name?></option>
                        <?php endforeach;?>
                    </select>
                    -->
                    <!--                     <button id="searchmilestoneButton" -->
                    <!--                         class="admin-button">Search</button> -->
                    <button id="addMilestoneButton" class="admin-button"><?php admin_language_e('cases_view_milestone_index_Add'); ?></button>
                </div>
            </div>
        </div>
        <input type="hidden" id="selected_milestone_id" value="" />
    </form>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<div id="milestoneTaskListContainer" style="display: none;">
    <h3 style="font-size: 15px; font-weight: bold;">Detail Task List: <span id="milestone_name_detail"></span>
    </h3>
    <br />
    <!--     <button id="searchMilestoneTaskButton" -->
    <!--                         class="admin-button">Search</button> -->
    <button id="addMilestoneTaskButton" class="admin-button"><?php admin_language_e('cases_view_milestone_index_Add'); ?></button>
    <div id="MilestoneTaskListTableResult">
        <div class="clear-height"></div>
        <table id="dataGridResult2"></table>
        <div id="dataGridPager2"></div>
    </div>
    <div class="clear-height"></div>
</div>

<!-- Content for dialog -->
<div class="hide">
    <div id="divMilestone" title="<?php admin_language_e('cases_view_milestone_index_AddMilestone'); ?> "
        class="input-form dialog-form"></div>

    <div id="divAddMilestoneTask" title="<?php admin_language_e('cases_view_milestone_index_AddTask'); ?>"
        class="input-form dialog-form"></div>
    <div id="divEditMilestoneTask" title="<?php admin_language_e('cases_view_milestone_index_EditTask'); ?>"
        class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();

    // Call search method
    searchMilestone();

    /**
     * When user click search button
     */
    $('#searchmilestoneButton').live('click', function() {
        searchMilestone();
        return false;
    });

    /**
     * Search data
     */
    function searchMilestone() {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/milestone/index';
        var tableH = $.getTableHeight() + 10;
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#milestoneSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'partner_name',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_milestone_index_Cases'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_MilestoneName'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_TypeOfMilestone'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerResponsible'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerEmail'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerPhoneNumber'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'product_name',index:'product_name', sortable: false,width:100,hidden: true},
               {name:'milestone_name',index:'milestone_name', width:400},
               {name:'type_of_milestone',index:'type_of_milestone', width:300},
               {name:'main_contact_point',index:'main_contact_point', align:"center", width:250},
               {name:'email',index:'email', width:350},
               {name:'phone',index:'phone', width:350},
               {name:'action',index:'action', width:150, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                /*
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                $('#selected_milestone_id').val(data_row.id);
                searchMilestoneTask();
                $('#milestoneTaskListContainer').show();
                $('#milestone_name_detail').html(data_row.milestone_name);
                */
            },
            loadComplete: function() {
                $.autoFitScreen(($( window ).width()- 40));  //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-icon-edit-milestone" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('cases_view_milestone_index_Edit'); ?>"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete managetables-icon-delete-milestone" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('cases_view_milestone_index_Delete'); ?>"></span></span>';
    }

    /**
     * Process when user click to add group button
     */
    $('#addMilestoneButton').click(function(e) {
        e.preventDefault();
        // var product_id = $('#product_id').val();
        var product_id = 5;
        if(product_id == '' || product_id == 0){
            $.displayError("<?php admin_language_e('cases_view_milestone_index_CaseRequireNotification'); ?>");
            return;
        }
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divMilestone').openDialog({
            autoOpen: false,
            height: 320,
            width: 500,
            title: "<?php admin_language_e('cases_view_milestone_index_AddMilestone'); ?>",
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/milestone/add?product_id=" + product_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveMilestone();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divMilestone').dialog('option', 'position', 'center');
        $('#divMilestone').dialog('open');
        return false;
    });

    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit-milestone').live('click', function() {
        var service_partner_id = $(this).data('id');

         // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divMilestone').openDialog({
            autoOpen: false,
            height: 320,
            width: 500,
            title: "<?php admin_language_e('cases_view_milestone_index_EditMilestone'); ?>",
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/milestone/edit?id=" + service_partner_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveMilestone();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divMilestone').dialog('option', 'position', 'center');
        $('#divMilestone').dialog('open');
    });

    /**
     * Process when user click to delete icon.
     */
    $('.managetables-icon-delete-milestone').live('click', function() {
        var location_id = $(this).data('id');

        // Show confirm dialog
        $.confirm({
            message: '<?php admin_language_e('cases_view_milestone_index_DeleteConfirmationMessage'); ?>',
            yes: function() {
                var submitUrl = '<?php echo base_url()?>cases/milestone/delete?id=' + location_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                             searchMilestone();
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
    function saveMilestone() {
        var submitUrl = $('#addEditMilestoneForm').attr('action');
        var action_type = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditMilestoneForm",
            success: function(data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#divMilestone').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#divMilestone').dialog('close');
                    }
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        searchMilestone();
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    // ****************************************************************************************************/
    // Task of this milestone
    // ****************************************************************************************************/
    $('#searchMilestoneTaskButton').live('click', function(){
        searchMilestoneTask();
    });

    function searchMilestoneTask(){
        var milestone_id = $('#selected_milestone_id').val();
        $("#dataGridResult2").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/milestone/show_tasklist';

        $("#dataGridResult2").jqGrid({
            url: url,
            postData: {milestone_id: milestone_id},
            mtype: 'POST',
            datatype: "json",
            height: 250,
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager2",
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_milestone_index_TaskName'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerName'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerResponsible'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerEmail'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_PartnerPhoneNumber'); ?>',
                '<?php admin_language_e('cases_view_milestone_index_Action'); ?>'
            ],
            colModel:[
                {name:'id',index:'id', hidden: true},
                {name:'task_name',index:'task_name', width:200},
                {name:'partner_name',index:'partner_name', width:200},
                {name:'main_contact_point',index:'main_contact_point', width:200},
                {name:'email',index:'email', width:150},
                {name:'phone',index:'phone', width:120},
                {name:'action',index:'action', width:100, sortable: false, align:"center", formatter: actionFormater2}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
            }
        });
    }

    function actionFormater2(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-icon-edit-task" data-id="' + cellvalue
                + '" title="<?php admin_language_e('cases_view_milestone_index_Edit'); ?>"></span></span>'
              + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete managetables-icon-delete-task" data-id="' + cellvalue 
              + '" title="<?php admin_language_e('cases_view_milestone_index_Delete'); ?>"></span></span>';
    }

    /**
     * Process when user click to add group button
     */
    $('#addMilestoneTaskButton').click(function() {
        var milestone_id = $('#selected_milestone_id').val();
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divAddMilestoneTask').openDialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/milestone/add_task?milestone_id=" + milestone_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveMilestoneTask();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divAddMilestoneTask').dialog('option', 'position', 'center');
        $('#divAddMilestoneTask').dialog('open');
        return false;
    });

    /**
     * Save task
     */
    function saveMilestoneTask() {
        var submitUrl = $('#addEditMilestoneTaskForm').attr('action');
        var action_type = $('#h_action_type').val();

        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditMilestoneTaskForm",
            success: function(data) {
                if (data.status) {
                    if (action_type == 'add') {
                        $('#divAddMilestoneTask').dialog('close');
                    } else if (action_type == 'edit') {
                        $('#divEditMilestoneTask').dialog('close');
                    }
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        searchMilestoneTask();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit-task').live('click', function() {
        var task_id = $(this).data('id');

         // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#divEditMilestoneTask').openDialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cases/milestone/edit_task?id=" + task_id, function() {
                });
            },
            buttons: {
                'Save': function() {
                    saveMilestoneTask();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditMilestoneTask').dialog('option', 'position', 'center');
        $('#divEditMilestoneTask').dialog('open');
    });

    /**
     * Process when user click to delete icon.
     */
    $('.managetables-icon-delete-task').live('click', function() {
        var task_id = $(this).data('id');

        // Show confirm dialog
        $.confirm({
            message: '<?php admin_language_e('cases_view_milestone_index_DeleteConfirmationMessage'); ?>',
            yes: function() {
                var submitUrl = '<?php echo base_url()?>cases/milestone/delete_task?id=' + task_id;
                $.ajaxExec({
                     url: submitUrl,
                     success: function(data) {
                         if (data.status) {
                             // Reload data grid
                             searchMilestoneTask();
                         } else {
                             $.displayError(data.message);
                         }
                     }
                 });
            }
        });
    });

    $('#product_id').live("change", function(){
        searchMilestone();
    });
});
</script>