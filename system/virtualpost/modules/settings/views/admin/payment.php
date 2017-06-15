<div class="button_container">
	<form id="usesrSearchForm" method="post"
		action="<?php echo base_url()?>admin/settings/payment">
		<div class="input-form">
			<div id="tabs">
				<ul class="tab-menu ui-tabs">
					<li><a href="#page-paypal-tab"><span>Paypal</span></a></li>
					<li><a href="#page-eway-tab"><span>Eway</span></a></li>
				</ul>
				<div class="form_inputs" id="page-paypal-tab">
					<table class="settings">
						<tr>
							<th class="input-width-200">User Name</th>
							<td><input type="text" id="PAYMENT_PAYPAL_USERNAME_CODE"
								name="PAYMENT_PAYPAL_USERNAME_CODE"
								value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_USERNAME_CODE)?>"
								class="input-width input-width-400" /><br /> <small>All e-mails
									from users, guests and the site will go to this e-mail address.</small></td>
						</tr>
						<tr>
							<th class="input-width-200">Password</th>
							<td><input type="password" id="PAYMENT_PAYPAL_PASSWORD_CODE"
								name="PAYMENT_PAYPAL_PASSWORD_CODE"
								value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE)?>"
								class="input-width input-width-400" /><br /> <small>All e-mails
									from users, guests and the site will go to this name.</small></td>
						</tr>
						<tr>
							<th class="input-width-200">API Key</th>
							<td><input type="text" id="PAYMENT_PAYPAL_SIGNATURE_CODE"
								name="PAYMENT_PAYPAL_SIGNATURE_CODE"
								value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE)?>"
								class="input-width input-width-400" /><br /> <small>All e-mails
									from users, guests and the site will go to this name.</small></td>
						</tr>
					</table>
				</div>
				<div class="form_inputs" id="page-eway-tab">
					<table class="settings">
						<tr>
							<th class="input-width-200">Customer ID</th>
							<td><input type="text" id="PAYMENT_EWAY_CUSTOMERID_CODE"
								name="PAYMENT_EWAY_CUSTOMERID_CODE"
								value="<?php echo Settings::get(APConstants::PAYMENT_EWAY_CUSTOMERID_CODE)?>"
								class="input-width input-width-400" /><br /> <small>All e-mails
									from users, guests and the site will go to this e-mail address.</small></td>
						</tr>

					</table>
				</div>
			</div>
		</div>
		<div class="clear-height"></div>
		<button id="saveButton" class="admin-button" type="submit">Save</button>
		<button id="resetButton" class="admin-button" type="reset">Reset</button>
	</form>
</div>
<div class="clear-height"></div>

<script type="text/javascript">
$(document).ready( function() {
	$('#tabs').tabs();
	
});
</script>