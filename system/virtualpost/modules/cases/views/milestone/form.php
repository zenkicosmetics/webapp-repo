<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'cases/milestone/add';
} else {
    $submit_url = base_url() . 'cases/milestone/edit';
}
?>
<form id="addEditMilestoneForm" method="post"
    action="<?php echo $submit_url ?>" autocomplete="on">
    <table>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_MilestoneName'); ?> <span class="required">*</span></th>
            <td><input type="text" class="input-width "
                id="milestone_name" name="milestone_name"
                value="<?php echo $milestone->milestone_name ?>"
                class="input-width" maxlength="250" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_TypeOfMilestone'); ?> <span class="required">*</span></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_base_taskname,
                    "value_key" => 'base_taskname',
                    "label_key" => 'taskname',
                    // "value" => APContext::getLocationUserSetting (),
                    "value" => $task->base_taskname,
                    "name" => 'base_taskname',
                    "id" => 'base_taskname_id',
                    "clazz" => 'input-width',
                    "style" => 'width:260px',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_DependencyMilestone'); ?></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_milestone,
                    "value_key" => 'id',
                    "label_key" => 'milestone_name',
                    // "value" => APContext::getLocationUserSetting(),
                    "value" => $milestone->depend_milestone_id,
                    "name" => 'depend_milestone_id',
                    "id" => 'depend_milestone_id',
                    "clazz" => 'input-width',
                    "style" => 'width:260px',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_milestone_form_ServicePartner'); ?> <span class="required">*</span></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_service_partner,
                    "value_key" => 'partner_id',
                    "label_key" => 'partner_name',
                    // "value" => APContext::getLocationUserSetting (),
                    "value" => $milestone->partner_id,
                    "name" => 'partner_id',
                    "id" => 'partner_id',
                    "clazz" => 'input-width',
                    "style" => 'width:260px',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr class="usps">
            <th></th>
            <td><input type="checkbox" name="xx"
                <?php echo empty($milestone->cmra)?"":"checked=checked" ?>><?php admin_language_e('cases_view_milestone_form_NbspifApplicableCMRA'); ?></td>
        </tr>
        <tr class="usps">
            <th>CMRA <span class="required">*</span></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_service_partner,
                    "value_key" => 'partner_id',
                    "label_key" => 'partner_name',
                    // "value" => APContext::getLocationUserSetting (),
                    "value" => $milestone->cmra,
                    "name" => 'cmra',
                    "id" => 'cmra_id',
                    "clazz" => 'input-width',
                    "style" => 'width:260px',
                    "has_empty" => false
                ));
                ?></td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type"
        value="<?php echo $action_type ?>" /> <input type="hidden"
        id="h_id" name="id" value="<?php echo $milestone->id ?>" /> <input
        type="hidden" id="h_product_id" name="product_id"
        value="<?php echo $milestone->product_id ?>" />
</form>
<script type="text/javascript">
$(function(){
    if($(base_taskname_id).val() == "verification_special_form_PS1583"
            || $(base_taskname_id).val() == "verification_General_CMRA"
            || $(base_taskname_id).val() == "verification_california_mailbox"){
        $(".usps").show();
        $('#divMilestone').openDialog({
            height: 400,
        });
    }else{
        $(".usps").hide();
        $('#divMilestone').openDialog({
            height: 320,
        });
        $("#cmra_id").attr("disabled","disabled");
        $("#addEditMilestoneForm input[name='xx']").attr("checked",false);
    }

    $("#base_taskname_id").change(function(){
        if($(this).val() == "verification_special_form_PS1583"
                || $(base_taskname_id).val() == "verification_General_CMRA"
                || $(base_taskname_id).val() == "verification_california_mailbox"){
            $(".usps").show();
            $('#divMilestone').openDialog({
                height: 400,
            });
        }else{
            $(".usps").hide();
            $('#divMilestone').openDialog({
                height: 320,
            });
            $("#cmra_id").attr("disabled","disabled");
            $("#addEditMilestoneForm input[name='xx']").attr("checked",false);
        }
    });

    $("#addEditMilestoneForm input[name='xx']").click(function(){
        if($(this).is(":checked")){
            $("#cmra_id").removeAttr("disabled");
        }else{
            $("#cmra_id").attr("disabled","disabled");
        }
    });
})
</script>