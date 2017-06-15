<?php
$submit_url = base_url() . 'cases/admin_case_setting/edit';
?>
<form id="addEditCaseSettingForm" method="post" class="dialog-form"    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th><?php admin_language_e('cases_view_admin_case_setting_form_CaseType'); ?>:</th>
            <td>
            <?php
            echo my_form_dropdown(array(
                "data" => $products,
                "value_key" => 'id',
                "label_key" => 'product_name',
                "value" => $case_setting->product_id,
                "name" => 'product_id',
                "id" => 'addEditCaseSettingForm_product_id',
                "clazz" => 'input-width',
                "style" => 'width: 362px;',
                "has_empty" => false
            ));
            ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('cases_view_admin_case_setting_form_CaseName'); ?>:</th>
            <td><input type="text" id="case_instance_name"
                name="case_instance_name"
                value="<?php echo $case_setting->case_instance_name?>"
                style="width: 350px;" class="input-width" maxlength=250 /></td>
        </tr>
        <?php if ($action_type == 'edit') {?>
        <tr>
            <th><?php admin_language_e('cases_view_admin_case_setting_form_Milestone'); ?></th>
            <td>
                <div style="float: left; width: 150px">
                    <?php
            echo my_form_dropdown(array(
                "data" => $list_available_milestone,
                "value_key" => 'id',
                "label_key" => 'milestone_name',
                "value" => '',
                "name" => 'list_available_milestone',
                "id" => 'list_available_milestone',
                "clazz" => 'input-txt-none',
                "style" => 'width: 150px;height:80px',
                "has_empty" => false,
                "html_option" => 'multiple = "true"'
            ));
            ?>
                 </div>
                <div style="float: left; width: 25px; padding: 20px;">
                    <button type="button" id="addButton">&gt;&gt;</button>
                    <br />
                    <button type="button" id="removeButton">&lt;&lt;</button>
                </div>
                <div style="float: left; width: 150px">
                    <?php
            echo my_form_dropdown(array(
                "data" => $list_selected_milestone,
                "value_key" => 'id',
                "label_key" => 'milestone_name',
                "value" => '',
                "name" => 'list_milestone_id[]',
                "id" => 'list_milestone_id',
                "clazz" => 'input-txt-none',
                "style" => 'width: 150px;height:80px',
                "has_empty" => false,
                "html_option" => 'multiple = "true"'
            ));
            ?>
                </div>
            </td>
            <?php }?>
        </tr>
    </table>
    <input type="hidden" id="id" name="id"
        value="<?php echo $case_setting->id?>" />
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
    <?php if ($action_type == 'edit') {?>
    $('#addEditCaseSettingForm_product_id').attr("disabled", true);
    <?php }?>

    $("#addButton").live("click", function(){
        $("#list_available_milestone > option:selected").each(function(){
            $(this).remove().appendTo("#list_milestone_id");
        });
    });

    $("#removeButton").live("click", function(){
        $("#list_milestone_id > option:selected").each(function(){
            $(this).remove().appendTo("#list_available_milestone");
        });
    });
});
</script>
