<label style="color:red;font-size: 16pt;"><?php echo $message;?></label>
<form id="addEditCustomerForm" method="post" class="dialog-form" action="<?php echo base_url()?>admin/customers/change_postbox_private">
	<table>
	    <tr>
			<th>Customer Id <span class="required">*</span></th>
			<td><input type="text" name="customer_id" required="required" value="" class="input-width" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Postbox Id <span class="required">*</span></th>
			<td><input type="text" name="postbox_id" required="required" class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Current Account type</th>
			<td>
				<select name="current_type" class="input-width">
					<option value="<?php echo APConstants::FREE_TYPE?>">AS YOU GO</option>
					<option value="<?php echo APConstants::PRIVATE_TYPE?>">PRIVATE</option>
					<option value="<?php echo APConstants::BUSINESS_TYPE?>">BUSINESS</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>New Account type</th>
			<td>
				<select name="new_type"  class="input-width">
					<option value="<?php echo APConstants::FREE_TYPE?>">AS YOU GO</option>
					<option value="<?php echo APConstants::PRIVATE_TYPE?>">PRIVATE</option>
					<option value="<?php echo APConstants::BUSINESS_TYPE?>">BUSINESS</option>
				</select>
			</td>
		</tr>
		<tr>
		      <th>&nbsp;</th>
			 <td><button>Change</button></td>
		</tr>
	</table>
</form>

