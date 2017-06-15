<?php
    $submit_url = base_url () . 'price/admin/edit_price_phone_number';
?>
<form id="addEditPricePhoneNumberForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th>Upcharge 1(abs) <span class="required">*</span></th>
            <td><input type="text" id="addEditPricePhoneNumberForm_one_time_fee_upcharge" name="one_time_fee_upcharge" 
                       value="<?php echo APUtils::number_format($price_phone_number->one_time_fee_upcharge, 2)?>" class="input-width" maxlength="50" /></td>
        </tr>
        <tr>
            <th>Upcharge 2(%) <span class="required">*</span></th>
            <td><input type="text" id="addEditPricePhoneNumberForm_recurring_fee_upcharge" name="recurring_fee_upcharge" 
                       value="<?php echo APUtils::number_format($price_phone_number->recurring_fee_upcharge, 0)?>" class="input-width" maxlength="50" /></td>
        </tr>
        <tr>
            <th>Upcharge 3(abs) <span class="required">*</span></th>
            <td><input type="text" id="addEditPricePhoneNumberForm_per_min_fee_upcharge" name="per_min_fee_upcharge" 
                       value="<?php echo APUtils::number_format($price_phone_number->per_min_fee_upcharge, 0)?>" class="input-width" maxlength="50" /></td>
        </tr>
    </table>
    <input type="hidden" id="addEditPricePhoneNumberForm_id" name="id" value="<?php echo $price_phone_number->id?>" /> 
</form>