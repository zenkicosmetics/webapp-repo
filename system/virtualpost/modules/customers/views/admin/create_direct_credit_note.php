<label style="color:red;font-size: 16pt;"><?php echo $message;?></label>
<form id="addEditCustomerForm" method="post" class="dialog-form" action="<?php echo base_url()?>admin/customers/create_direct_credit_note">
	<table>
	    <tr>
			<th>Customer Id <span class="required">*</span></th>
			<td><input type="text" name="customer_id" required="required" value="" class="input-width" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Open balance (included VAT) <span class="required">*</span></th>
			<td><input type="text" name="open_balance" required="required" class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		
		<tr>
		      <th>&nbsp;</th>
			 <td><button>Submit</button></td>
		</tr>
	</table>
</form>

