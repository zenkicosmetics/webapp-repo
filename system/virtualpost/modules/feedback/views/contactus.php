<h2 class="header-title">Contact Us</h2>
<?php
$submit_url = base_url() . 'feedback/contactus/index';
?>
<?php if ($this->session->flashdata('error')): ?>
<div class="alert error">
	<?php echo $this->session->flashdata('error'); ?>
</div>
<?php endif; ?>
<?php if (validation_errors()): ?>
<div class="alert error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<?php if ($this->session->flashdata('success')): ?>
<div class="alert success">
	<?php echo $this->session->flashdata('success'); ?>
</div>
<?php endif; ?>
<div class="button_container">
	<div class="input-form">
		<form id="addFeedbackForm" method="post"
			action="<?php echo $submit_url?>">
			<table>
				<tr>
					<th>Your Name: <span class="required">*</span></th>
					<td><input type="text" id="YourName" name="YourName"
						value="<?php echo $contactus->YourName?>"
						class="input-width custom_autocomplete" maxlength=255 /></td>
				</tr>
				<tr>
					<th>Your Email: <span class="required">*</span></th>
					<td><input type="text" id="YourEmail" name="YourEmail"
						value="<?php echo $contactus->YourEmail?>"
						class="input-width custom_autocomplete" maxlength=255 /></td>
				</tr>
				<tr>
					<th>Your Company Name: <span class="required">*</span></th>
					<td><input type="text" id="CompanyName" name="CompanyName"
						value="<?php echo $contactus->CompanyName?>"
						class="input-width custom_autocomplete" maxlength=255 /></td>
				</tr>
				<tr>
					<th>Phone Number: <span class="required">*</span></th>
					<td><input type="text" id="PhoneNumber" name="PhoneNumber"
						value="<?php echo $contactus->PhoneNumber?>"
						class="input-width custom_autocomplete" maxlength=15 /></td>
				</tr>
				<tr>
					<th>Message: <span class="required">*</span></th>
					<td><textarea rows="6" cols="40" name="Message" class="input-width" style="width: 600px"></textarea>
					</td>
				</tr>
				<tr>
					<th>&nbsp;</th>
					<td><button class="main-button-dialog button-dialog-width-160">Submit Message</button></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $('#YourName').focus();
});
</script>
