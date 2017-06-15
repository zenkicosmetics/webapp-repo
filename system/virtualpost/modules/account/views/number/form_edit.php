<?php
    $submit_url = base_url() . 'account/number/edit';
?>
<form id="addEditNumberForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th>Phone Number</th>
            <td><div style="margin-left: 10px"><?php echo $number->phone_number; ?></div></td>
        </tr>
        <tr>
            <th>Auto Renewal</th>
            <td>
                <input type="checkbox" id="addEditNumberForm_auto_renewal" name="auto_renewal" value="1" <?php if($number->auto_renewal == '1') { ?>checked="checked"<?php } ?> />
            </td>
        </tr>
    </table>
    <input type="hidden" value="<?php echo $number->id;?>" name="id" id="addEditNumberForm_id" />
</form>
<script>
jQuery(document).ready(function () {
    
});
</script>