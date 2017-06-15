<?php
$submit_url = base_url() . 'admin/users/myaccount';
?>
<div class="button_container">
	<div class="input-form">
		<form id="myAccountForm" method="post"
			action="<?php echo $submit_url?>">
			<table>
        		<tr>
        			<th>User Name <span class="required">*</span></th>
        			<td><input type="text" id="UserName" name="UserName"
        				value="<?php echo $user->UserName?>"
        				class="input-width custom_autocomplete" maxlength=50 /></td>
        		</tr>
        		<tr>
        			<th>E-mail <span class="required">*</span></th>
        			<td><input type="text" id="Email" name="Email"
        				value="<?php echo $user->Email?>"
        				class="input-width custom_autocomplete" maxlength=50 /></td>
        		</tr>
        		<tr>
        			<th>Password</th>
        			<td><input type="password" id="Password" name="Password"
        				value=""
        				class="input-width custom_autocomplete" maxlength=100/></td>
        		</tr>
        		<tr>
        			<th>PasswordConf</th>
        			<td><input type="password" id="PasswordConf" name="PasswordConf"
        				value=""
        				class="input-width custom_autocomplete" maxlength=100/></td>
        		</tr>
        		<tr>
        			<th>Display Name <span class="required">*</span></th>
        			<td><input type="text" id="DisplayName" name="DisplayName"
        				value="<?php echo $user->DisplayName?>"
        				class="input-width custom_autocomplete" maxlength=100 /></td>
        		</tr>
        		<tr>
        			<th>First Name <span class="required">*</span></th>
        			<td><input type="text" id="FirstName" name="FirstName"
        				value="<?php echo $user->FirstName?>"
        				class="input-width custom_autocomplete" maxlength=100 /></td>
        		</tr>
        		<tr>
        			<th>Last Name <span class="required">*</span></th>
        			<td><input type="text" id="LastName" name="LastName"
        				value="<?php echo $user->LastName?>"
        				class="input-width custom_autocomplete" maxlength=100 /></td>
        		</tr>
				<tr>
					<th>&nbsp;</th>
					<td><button id="buttonSaveMyAccount" class="admin-button" type="submit">Save</button></td>
				</tr>
				
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('#UserName').focus();
});
</script>
