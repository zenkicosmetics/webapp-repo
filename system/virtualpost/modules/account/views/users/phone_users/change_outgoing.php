<?php
    $submit_url = base_url().'account/users/change_outgoing?customer_id='.$customer_id;
    if (!empty($sonetel_user)) {
        $outgoing = $sonetel_user->call->outgoing;
    } else {
        $outgoing = new stdClass();
        $outgoing->show = '';
    }
?>
<form id="changeOutGoingUserForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th style="width: 150px;">Show</th>
            <td style="width: 250px;">
                <select name="show" id="changeOutGoingUserForm_show" class="input-width" style="width: 200px;">
                    <option value="auto" <?php if ($outgoing->show == 'auto') {?> selected="selected" <?php } ?> >Auto</option>
                    <option value="none" <?php if ($outgoing->show == 'none') {?> selected="selected" <?php } ?> >None</option>
                    <option value="inum" <?php if ($outgoing->show == 'inum') {?> selected="selected" <?php } ?> >Inum</option>
                    <option value="other" <?php if (($outgoing->show != 'auto') && ($outgoing->show != 'none') && ($outgoing->show != 'inum')) {?> selected="selected" <?php } ?> >Other</option>
                </select>
            </td>
        </tr>
        <tr id="tr_changeOutGoingUserForm_phonenumber" <?php if (($outgoing->show == 'auto') || ($outgoing->show == 'none') || ($outgoing->show == 'inum')) {?> style="display: none" <?php } ?>>
            <th>User phone number</th>
            <td>
                <?php 
                // #472: added
                echo my_form_dropdown(array(
                        "data" => $list_user_phonenumber,
                        "value_key"=> 'phone_number',
                        "label_key"=> 'phone_number',
                        "value"=> '',
                        "name" => 'phone_number',
                        "id"    => 'changeOutGoingUserForm_phonenumber',
                        "clazz" => 'input-width',
                        "style" => 'width: 200px',
                        "has_empty" => false
                ));
                ?>
            </td>
        </tr>
    </table>
    <input type="hidden" id="changeCallToSettingForm_phone_user_id" name="phone_user_id"
           value="<?php echo $phone_user_id; ?>" />
</form>
<script>
jQuery(document).ready(function () {
    // User change the country code
    $('#changeOutGoingUserForm_show').live('change', function() {
        var show = $('#changeOutGoingUserForm_show').val();
        if (show == 'other') {
            $('#tr_changeOutGoingUserForm_phonenumber').show();
        } else {
            $('#tr_changeOutGoingUserForm_phonenumber').hide();
        }
    });
});
</script>