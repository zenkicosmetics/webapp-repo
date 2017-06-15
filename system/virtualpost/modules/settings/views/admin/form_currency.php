<?php
if ($action_type == 'add') {
    $submit_url = base_url().'admin/settings/add_currency';
} else {
    $submit_url = base_url().'admin/settings/edit_currency';
}
?>

<form id="addEditCurrencyForm" method="post" action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcurrency_Name'); ?><span class="required">*</span></th>
            <td><input type="text" id="currency_name" name="currency_name" value="<?php echo $currency->currency_name; ?>" class="input-width" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcurrency_Short'); ?><span class="required">*</span></th>
            <td><input type="text" id="currency_short" name="currency_short" value="<?php echo $currency->currency_short; ?>" class="input-width" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcurrency_Sign'); ?><span class="required">*</span></th>
            <td><input type="text" id="currency_sign" name="currency_sign" value="<?php echo $currency->currency_sign; ?>" class="input-width" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcurrency_RateEUR'); ?><span class="required">*</span></th>
            <td><input type="text" id="currency_rate" name="currency_rate" value="<?php echo $currency->currency_rate; ?>" class="input-width" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcurrency_Active'); ?></th>
            <td><input type="checkbox" id="currency_active" name="currency_active" value="1"
                       <?php if($currency->active_flag){echo "checked='checked'";} ?>/></td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
    <input type="hidden" id="h_currency_id" name="currency_id" value="<?php echo $currency->currency_id; ?>" />
</form>
