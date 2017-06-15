<?php
$submit_url = base_url() . 'feedback/add';
?>

<form id="addFeedbackForm" method="post"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>Name: <span class="required">*</span></th>
			<td><input type="text" id="Name" name="Name"
				value="<?php echo $feedback->Name?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Subject: <span class="required">*</span></th>
			<td><input type="text" id="Subject" name="Subject"
				value="<?php echo $feedback->Subject?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr>
			<th>Current Page: <span class="required">*</span></th>
			<td><input type="text" id="CurrentPage" name="CurrentPage"
				value="<?php echo $feedback->CurrentPage?>"
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<?php if (!empty($vehicle)) {?>
		<tr>
			<th>Vehicle ID:</th>
			<td><input type="text" id="VehicleID" name="VehicleID"
				value="<?php echo $vehicle->VehicleID?>"
				class="input-width readonly" readonly="readonly" maxlength=100 /></td>
		</tr>
		<tr>
			<th>Make:</th>
			<td><input type="text" id="Make" name="Make"
				value="<?php echo $vehicle->Make?>"
				class="input-width readonly" readonly="readonly" maxlength=100 /></td>
		</tr>
		<tr>
			<th>Model:</th>
			<td><input type="text" id="Model" name="Model"
				value="<?php echo $vehicle->Model?>"
				class="input-width readonly" readonly="readonly" maxlength=100 /></td>
		</tr>
		<tr>
			<th>Series:</th>
			<td><input type="text" id="Series" name="Series"
				value="<?php echo $vehicle->Series?>"
				class="input-width readonly" readonly="readonly" maxlength=100 /></td>
		</tr>
		<?php } ?>
		<tr>
			<th>Message: <span class="required">*</span></th>
			<td><textarea rows="6" cols="40" name="Message" class="input-width" style="width: 500px"></textarea> </td>
		</tr>
	</table>
</form>