<?php
    $submit_url = base_url () . 'price/admin/edit_price_outbound_call_remark';
?>
<form id="addEditPriceOutboundCallRemarkForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th>Remarks</th>
            <td>
                <textarea name="remarks" style="width: 300px; height: 150px"><?php echo $price_outbound_call->remarks;?></textarea>
            </td>
        </tr>
    </table>
    <input type="hidden" id="addEditPriceOutboundCallRemarkForm_id" name="id" value="<?php echo $price_outbound_call->id?>" /> 
</form>