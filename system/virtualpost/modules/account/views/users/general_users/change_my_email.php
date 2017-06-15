<?php
$submit_url = base_url() . 'account/users/change_my_email';
?>
<form id="changeMyEmailForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>New E-mail <span class="required">*</span></th>
            <td><input type="text" id="changeMyEmailForm_email" name="email"
                       value="<?php echo $user->email ?>"
                       class="input-width" maxlength=50 /></td>
        </tr>
    </table>
    <input type="hidden" id="changeMyEmailForm_id" name="customer_id" value="<?php echo $user->customer_id; ?>" />
</form>