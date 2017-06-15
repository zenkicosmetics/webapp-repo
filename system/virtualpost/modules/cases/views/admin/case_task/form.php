<?php
if ($action_type == 'add') {
    $submit_url = base_url () . 'cases/admin_case_task/add';
} else {
    $submit_url = base_url () . 'cases/admin_case_task/edit';
}
?>
<form id="addEditMilestoneForm" method="post" action="<?php echo $submit_url ?>" autocomplete="on">
    <table>
        <tr>
            <th><?php admin_language_e('cases_view_admin_case_task_form_TypeOfMilestone'); ?><span class="required">*</span></th>
            <td><input type="text" class="input-width " id="taskname" name="taskname" value="<?php echo $casetask->taskname ?>" maxlength="250" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_admin_case_task_form_Activated'); ?></th>
            <td><input type="checkbox" class="" id="activate_flag" name="activate_flag" value="1" 
                    <?php if($casetask->activate_flag == 1) {echo 'checked="checked"';}?> /></td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" /> 
    <input type="hidden" id="h_id" name="id" value="<?php echo $casetask->id ?>" />
    <input type="hidden" id="h_product_id" name="product_id" value="<?php echo $casetask->product_id ?>" />
</form>
