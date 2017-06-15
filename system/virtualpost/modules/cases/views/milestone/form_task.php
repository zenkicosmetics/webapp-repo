<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'cases/milestone/add_task';
}
else {
    $submit_url = base_url() . 'cases/milestone/edit_task';
}
?>
<form id="addEditMilestoneTaskForm" method="post"
    action="<?php echo $submit_url ?>" autocomplete="on">
    <table>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_task_TaskName'); ?> <span class="required">*</span></th>
            <td><input type="text" class="input-width " id="task_name"
                name="task_name"
                value="<?php echo $task->task_name ?>" class="input-width"
                maxlength="250" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_task_TypeOfMilestone'); ?> <span class="required">*</span></th>
            <td>
                <select class="input-width " id="base_task_name"
                    name="base_task_name">
                    <?php foreach ($list_base_taskname as $item ) {?>
                        <option value="<?php echo $item->base_taskname?>" <?php if ($task->base_task_name == $item->base_taskname) {?>selected="selected"<?php }?>><?php echo $item->taskname?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type"
        value="<?php echo $action_type ?>" /> <input type="hidden" id="h_id"
        name="id" value="<?php echo $task->id ?>" />
    <input type="hidden" id="h_milestone_id"
        name="milestone_id" value="<?php echo $task->milestone_id ?>" />
</form>