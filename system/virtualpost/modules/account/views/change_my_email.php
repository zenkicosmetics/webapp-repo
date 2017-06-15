<?php
    $submit_url = base_url().'account/change_my_email';
?>
<form id="changeMyEmailForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>New E-mail <span class="required">*</span></th>
			<td><input type="text" id="changeMyEmailForm_email" name="email"
				value="<?php echo $customer->email?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
        <?php if(!$is_edit_user){?>
        <tr>
			<th>Current password <span class="required">*</span></th>
                        <td><input type="password" id="current_password" name="current_password"
				value="" class="input-width" maxlength="100" /></td>
		</tr>
        <?php } else{?>
        <input type="hidden" id="current_password" name="current_password"
				value="123456" class="input-width" maxlength="100" />
        <?php }?>
	</table>
    <input type="hidden" name="customer_id" value="<?php echo $customer_id ?>" id="changeMyEmailForm_customer_id" />
</form>
<script type="text/javascript">
$(document).ready(function(){
    <?php if($is_edit_user){?>
    $("#changeMyEmailWindow").dialog("option", "height", 180);
    <?php }?>
});
</script>