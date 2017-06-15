<?php
if ($action_type == 'add') {
    $submit_url = base_url () . 'price/admin/add';
} else {
    $submit_url = base_url () . 'price/admin/edit';
}
?>
<form id="addEditPartnerForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th>Template Name <span class="required">*</span></th>
            <td><input type="text" id="template_name" name="name" value="<?php echo $price_template->name?>" class="input-width" maxlength="50" /></td>
        </tr>
        <tr>
            <th>Type<span class="required">*</span></th>
            <td>
                <select name="pricing_type" class="input-width">
                    <option <?php if($price_template->pricing_type == "Clevver"){echo 'selected="selected"';} ?>  value="Clevver" ><?php admin_language_e('price_view_admin_index_Clevver'); ?></option>
                    <option <?php if($price_template->pricing_type == "Enterprise"){echo 'selected="selected"';} ?> value="Enterprise" ><?php admin_language_e('price_view_admin_index_Enterprise'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Description <span class="required">*</span></th>
            <td>
            <textarea rows="3" id="description" name="description" maxlength="1000" class="input-width"><?php echo $price_template->description?></textarea>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" /> 
    <input type="hidden" id="h_template_id" name="id" value="<?php echo $price_template->id?>" /> 
</form>