<?php
    $submit_url = base_url () . 'price/admin/edit_price_phone_number_remark';
?>
<form id="addEditPricePhoneNumberRemarkForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <th>Remarks</th>
            <td>
                <textarea name="remarks" style="width: 300px; height: 150px"><?php echo $price_phone_number->remarks;?></textarea>
            </td>
        </tr>
    </table>
    <input type="hidden" id="addEditPricePhoneNumberRemarkForm_id" name="id" value="<?php echo $price_phone_number->id?>" /> 
</form>