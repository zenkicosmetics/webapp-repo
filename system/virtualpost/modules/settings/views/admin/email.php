<div class="button_container">
	<form id="usesrSearchForm" method="post"
		action="<?php echo base_url()?>admin/settings/email">
		<div class="input-form">

			<table class="settings">
				<tr>
					<th class="input-width-200">Contact E-mail</th>
					<td><input type="text" id="MAIL_CONTACT_CODE"
						name="MAIL_CONTACT_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_CONTACT_CODE)?>"
						class="input-width input-width-400" /><br /> <small>All e-mails
							from users, guests and the site will go to this e-mail address.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">Sendmail Alias Name</th>
					<td><input type="text" id="MAIL_ALIAS_NAME_CODE"
						name="MAIL_ALIAS_NAME_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_ALIAS_NAME_CODE)?>"
						class="input-width input-width-400" /><br /> <small>All e-mails
							from users, guests and the site will go to this name.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">Server E-mail</th>
					<td><input type="text" id="MAIL_SERVER_CODE"
						name="MAIL_SERVER_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SERVER_CODE)?>"
						class="input-width input-width-400" /><br /> <small>All e-mails to
							users will come from this e-mail address.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">Mail Protocol</th>
					<td><input type="text" id="MAIL_PROTOCOL_CODE"
						name="MAIL_PROTOCOL_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_PROTOCOL_CODE)?>"
						class="input-width input-width-400" /><br /> <small>Select desired
							email protocol.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">SMTP Host</th>
					<td><input type="text" id="MAIL_SMTP_HOST_CODE"
						name="MAIL_SMTP_HOST_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SMTP_HOST_CODE)?>"
						class="input-width input-width-400" /><br /> <small>The host name
							of your smtp server.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">SMTP password</th>
					<td><input type="password" id="MAIL_SMTP_PASS_CODE"
						name="MAIL_SMTP_PASS_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SMTP_PASS_CODE)?>"
						class="input-width input-width-400" /><br /> <small>SMTP password.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">SMTP Port</th>
					<td><input type="text" id="MAIL_SMTP_PORT_CODE"
						name="MAIL_SMTP_PORT_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SMTP_PORT_CODE)?>"
						class="input-width input-width-400" /><br /> <small>SMTP port
							number.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">SMTP User Name</th>
					<td><input type="text" id="MAIL_SMTP_USER_CODE"
						name="MAIL_SMTP_USER_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SMTP_USER_CODE)?>"
						class="input-width input-width-400" /><br /> <small>SMTP user
							name.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">Sendmail Path</th>
					<td><input type="text" id="MAIL_SENDMAIL_PATH_CODE"
						name="MAIL_SENDMAIL_PATH_CODE"
						value="<?php echo Settings::get(APConstants::MAIL_SENDMAIL_PATH_CODE)?>"
						class="input-width input-width-400" /><br /> <small>Path to server
							sendmail binary.</small></td>
				</tr>
				<tr>
					<th class="input-width-200">&nbsp;</th>
					<td style="padding:0 0 0 13px;">
					    <button id="saveButton" type="submit" class="admin-button">Save</button>
		                <button id="resetButton" type="reset" class="admin-button">Reset</button>
					</td>
				</tr>

			</table>

		</div>
	</form>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
$(document).ready( function() {
	
	
});
</script>