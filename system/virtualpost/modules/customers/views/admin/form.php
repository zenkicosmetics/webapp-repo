<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/customers/add';
} else {
    $submit_url = base_url() . 'admin/customers/edit';
}
?>

<form id="addEditCustomerForm" method="post" class="dialog-form" action="<?php echo $submit_url?>">
	<?php if ($action_type == 'add') {?>
		<table>
			<tr>
				<th>E-mail <span class="required">*</span></th>
				<td><input type="text" id="addEditCustomerForm_email" name="email" value="<?php echo $customer->email?>" class="input-width" maxlength=50 /></td>
			</tr>
			<tr>
				<th>Password <span class="required">*</span></th>
				<td><input type="password" id="addEditCustomerForm_password" name="password" value="<?php echo $customer->password?>" class="input-width custom_autocomplete" maxlength=50 /></td>
			</tr>
			<tr>
				<th>Retype Password <span class="required">*</span></th>
				<td><input type="password" id="addEditCustomerForm_repeat_password" name="repeat_password" value="<?php echo $customer->repeat_password?>"	class="input-width custom_autocomplete" maxlength=50 /></td>
			</tr>
			<tr>
				<th>Charge Fee</th>
				<td>
					<select class="input-width" id="charge_fee_flag" name="charge_fee_flag" style = "width: 262px;">
						<option value="0" <?php if ($customer->charge_fee_flag == '0') {?> selected="selected" <?php }?>>No Charge</option>
						<option value="1" <?php if ($customer->charge_fee_flag == '1') {?> selected="selected" <?php }?>>Charge</option>
					</select>
				</td>
			</tr>
                        
			<tr>
				<th>Verification</th>
				<td>
					<select class="input-width" id="required_verification_flag" name="required_verification_flag"  style = "width: 262px;">
						<option value="0" <?php if ($customer->required_verification_flag == '0') {?> selected="selected" <?php }?>>No need to verify</option>
						<option value="1" <?php if ($customer->required_verification_flag == '1') {?> selected="selected" <?php }?>>Require to verify</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Shipping Factor FC</th>
				<td><input id="shipping_factor_fc" name="shipping_factor_fc" value="<?php echo $customer->shipping_factor_fc; ?>" type="text" class="input-width" /></td>
			</tr>
                        <tr>
				<th>Pre-Payment</th>
				<td>
					<select class="input-width" id="required_prepayment_flag" name="required_prepayment_flag"  style = "width: 262px;">
						<option value="0" <?php if ($customer->required_prepayment_flag == '0') {?> selected="selected" <?php }?>>No</option>
						<option value="1" <?php if ($customer->required_prepayment_flag == '1') {?> selected="selected" <?php }?>>Yes</option>
					</select>
				</td>
			</tr>
		</table>

	<?php } else { ?>

		<table>
			<tr>
				<td>
					<table>
						<tr>
							<th>E-mail <span class="required">*</span></th>
							<td><input type="text" id="addEditCustomerForm_email" name="email" value="<?php echo $customer->email?>" class="input-width custom_autocomplete" maxlength=50 /></td>
						</tr>
						<tr>
							<th>Status</th>
							<td>
								<select class="input-width" id="status_flag" name="status_flag" style = "width: 262px;">
                                    <?php $customer_status = customers_api::getCustomerStatus($customer);?>
									<option value="0" <?php if ($customer_status == lang('customer.status.deleted')) { $status_flag = 0; ?> selected="selected" <?php }?>><?php echo lang('customer.status.deleted')?></option>
									<option value="1" <?php if ($customer_status == lang('customer.activated')) { $status_flag = 1; ?> selected="selected" <?php }?>><?php echo lang('customer.activated')?></option>
									<option value="2" <?php if ($customer_status == lang('customer.auto_deactivated')) { $status_flag = 2; ?> selected="selected" <?php }?>><?php echo lang('customer.auto_deactivated')?></option>
									<option value="3" <?php if ($customer_status == lang('customer.manu_deactivated')) { $status_flag = 3; ?> selected="selected" <?php }?>><?php echo lang('customer.manu_deactivated')?></option>
									<option value="4" <?php if ($customer_status == lang('customer.never_activated')) { $status_flag = 4; ?> selected="selected" <?php }?>><?php echo lang('customer.never_activated')?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Charge Fee</th>
							<td>
								<select class="input-width" id="charge_fee_flag" name="charge_fee_flag" style = "width: 262px;">
									<option value="0" <?php if ($customer->charge_fee_flag == '0') {?> selected="selected" <?php }?>>No Charge</option>
									<option value="1" <?php if ($customer->charge_fee_flag == '1') {?> selected="selected" <?php }?>>Charge</option>
								</select>
							</td>
						</tr>
                                                <?php if ($isEnterpriseCustomer) { ?>
                                                <tr>
                                                    <th>Pricing</th>
                                                    <td>
                                                        <select class="input-width" id="pricing_type" name="pricing_type" style = "width: 262px;">
                                                            <option value="<?php echo APConstants::CUSTOMER_PRICING_TYPE_NORMAL;?>" <?php if ($pricing_type == APConstants::CUSTOMER_PRICING_TYPE_NORMAL) {?> selected="selected" <?php }?>>Normal pricing </option>
                                                            <option value="<?php echo APConstants::CUSTOMER_PRICING_TYPE_SPECIAL;?>" <?php if ($pricing_type == APConstants::CUSTOMER_PRICING_TYPE_SPECIAL) {?> selected="selected" <?php }?>>Special pricing</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php } ?>
						<tr>
							<th>Partner code</th>
							<td>
								<input value="<?php echo $partner_code;?>" type="text" class="input-width readonly" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th>Primary location</th>
							<td>
								<?php
                                                                //echo "<pre>";print_r($location_list);exit;
								$primary_location = '';
								if (!empty($first_location)) {
									$primary_location = $first_location->location_available_id;
								}
								echo my_form_dropdown(array(
									"data" => $location_list,
									"value_key" => 'location_id',
									"label_key" => 'location_name',
									"value" => $primary_location,
									"name" => 'location_id',
									"id"    => 'location_id',
									"clazz" => 'input-width',
									"style" => 'width: 262px;',
									"has_empty" => false
								));
								?>
							</td>
						</tr>
                                                <tr>
                                                    <td colspan="2"><input id="view_verification_detail" rel="<?php echo base_url()?>cases/todo/view_verification_detail?cid=<?php echo $customer->customer_id; ?>" style="width: auto;" type="button" class="input-btn c yl" value="View verification details" /></td>
						</tr>
					</table>
				</td>
				<td>
					<table>
						<tr>
							<th>Invoice Type</th>
							<td>
								<select class="input-width" id="invoice_type" name="invoice_type"  style = "width: 262px;">
									<option value="1" <?php if ($customer->invoice_type == '1') {?> selected="selected" <?php }?>><?php echo lang('customer.credit_card');?></option>
									<option value="2" <?php if ($customer->invoice_type == '2') {?> selected="selected" <?php }?>><?php echo lang('customer.invoice_payment');?></option>
									<option value="3" <?php if ($customer->invoice_type == '3') {?> selected="selected" <?php }?>><?php echo lang('customer.paypal'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Invoice Code</th>
							<td>
								<input type="text" id="addEditCustomerForm_invoice_code" name="invoice_code" value="<?php echo $customer->invoice_code?>" class="input-width custom_autocomplete" maxlength=10 />
								<button id="generateInvoiceButton" data-id="<?php echo $customer->customer_id ?>">Generate Invoice Code</button>
							</td>
						</tr>
						<tr>
							<th>Verification</th>
							<td>
								<select class="input-width" id="required_verification_flag" name="required_verification_flag"  style = "width: 262px;">
									<option value="0" <?php if ($customer->required_verification_flag == '0') {?> selected="selected" <?php }?>>No need to verify</option>
									<option value="1" <?php if ($customer->required_verification_flag == '1') {?> selected="selected" <?php }?>>Require to verify</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Shipping Factor FC</th>
							<td><input id="shipping_factor_fc" name="shipping_factor_fc" value="<?php echo $customer->shipping_factor_fc; ?>" type="text" class="input-width" /></td>
						</tr>
                                                <tr>
                                                    <th>Pre-Payment</th>
                                                    <td>
                                                            <select class="input-width" id="required_prepayment_flag" name="required_prepayment_flag"  style = "width: 262px;">
                                                                    <option value="0" <?php if ($customer->required_prepayment_flag == '0') {?> selected="selected" <?php }?>>No</option>
                                                                    <option value="1" <?php if ($customer->required_prepayment_flag == '1') {?> selected="selected" <?php }?>>Yes</option>
                                                            </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="vertical-align: middle;">Allow auto-trash</th>
                                                    <td>
                                                        <select class="input-width" id="auto_trash_flag" name="auto_trash_flag"  style = "width: 262px;">
                                                            <option value="0" <?php if ($customer->auto_trash_flag == '0') {?> selected="selected" <?php }?>>No</option>
                                                            <option value="1" <?php if ($customer->auto_trash_flag == '1') {?> selected="selected" <?php }?>>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
					</table>
				</td>
			</tr>
		</table>

        <input type="hidden" id="customer_history_log" name="customer_history_log" value='<?php
            echo json_encode([
                'email' => $customer->email,
                'status_flag' => $status_flag,
            ]);
        ?>' />
	<?php } ?>

	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
	<input type="hidden" id="id" name="id" value="<?php echo $customer->customer_id?>" />
</form>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/customers/js/admin/CustomerForm.js"></script>
<script>
jQuery(document).ready(function() {
    CustomerForm.init('<?php echo base_url(); ?>');

    $("#view_verification_detail").click(function(){

            window.open($(this).attr('rel'));
    });

});
</script>
