<?php
    $submit_url = base_url () . 'price/admin/edit_price_outbound_call';
?>
<form id="addEditPriceOutboundCallForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th>Upcharge(%) <span class="required">*</span></th>
            <td><input type="text" id="addEditPriceOutboundCallForm_usage_fee_upcharge" name="usage_fee_upcharge" 
                       value="<?php echo APUtils::number_format($price_outbound_call->usage_fee_upcharge, 0)?>" class="input-width" maxlength="50" /></td>
        </tr>
    </table>
    <input type="hidden" id="addEditPriceOutboundCallForm_id" name="id" value="<?php echo $price_outbound_call->id?>" /> 
</form>