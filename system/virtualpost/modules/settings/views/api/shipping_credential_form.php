<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'settings/api/add_shipping_credential';
} else {
    $submit_url = base_url() . 'settings/api/edit_shipping_credential';
}
?>
<style>
	div.api-info-container {margin: 20px 15px;}
	div.api-info-container p.left { float: left; display: block; text-align: left; vertical-align: middle; width: 100px; font-weight: bold; height: 100%; padding-right: 20px; }
	div.api-info-container p.right { float: left; display: block; text-align: left; vertical-align: middle; width: 220px; font-weight: bold; height: 100%; padding-right: 20px; }
	div.api-info-container div.common-info div.common-field { margin-top: 20px;}
	div.api-info-container div.specific-info div.left-specific-info {float: left; margin-right: 70px;}
	div.api-info-container div.specific-info div.row {margin-top: 20px;}
</style>
<form id="addEditShippingCredentialForm" method="post" class="dialog-form" action="<?php echo $submit_url; ?>">
	<div class="api-info-container">
		<div class="common-info">
			<div class="common-field">
				<p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_Name'); ?><span class="required">*</span></p>
				<input type="text" id="addEditShippingAPIForm_name" name="name"	value="<?php echo $shipping_credential->name; ?>" class="input-width" style="width: 820px;" />
			</div>
			<div class="common-field">
				<p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_Description'); ?></p>
				<textarea id="addEditShippingAPIForm_description" name="description" class="input-width" rows="5" style="width: 820px; height: 46px;"><?php echo $shipping_credential->description; ?></textarea>
			</div>
		</div>
		<div class="specific-info">
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_AccountNo'); ?></p>
					<input type="text" id="addEditShippingAPIForm_account_no" name="account_no"	value="<?php echo $shipping_credential->account_no; ?>" class="input-width" />
				</div>
				<div>
					<p class="right"><?php admin_language_e('setting_view_api_shippingcredentialform_EStampPartnerSignature'); ?></p>
					<input type="text" id="estamp_partner_signature" name="estamp_partner_signature" value="<?php echo $shipping_credential->estamp_partner_signature; ?>" class="input-width estamp" />
				</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_MeterNo'); ?></p>
					<input type="text" id="addEditShippingAPIForm_meter_no" name="meter_no"	value="<?php echo $shipping_credential->meter_no; ?>" class="input-width" />
				</div>
				<div>
					<p class="right"><?php admin_language_e('setting_view_api_shippingcredentialform_EStampNamespace'); ?></p>
					<input type="text" id="estamp_namespace" name="estamp_namespace" value="<?php echo $shipping_credential->estamp_namespace; ?>" class="input-width" />
				</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_AuthKey'); ?></p>
					<input type="text" id="addEditShippingCredentialForm_auth_key" name="auth_key" value="<?php echo $shipping_credential->auth_key; ?>" class="input-width" />
				</div>
				<div>
                    <p class="right"><?php admin_language_e('setting_view_api_shippingcredentialform_PartnerToAssociate'); ?></p>
                    <?php
                    echo my_form_dropdown(array(
                            "data" => $list_partner,
                            "value_key" => 'partner_id',
                            "label_key" => 'partner_name',
                            "value" => $shipping_credential->partner_id,
                            "name" => 'partner_id',
                            "id"    => 'partner_id',
                            "clazz" => 'input-width carrier',
                            "style" => 'width: 262px;',
                            "has_empty" => true
                    ));
                    ?>
                </div>
			</div>
			<div class="row">
				<div class="left-specific-info">
                    <p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_Username'); ?></p>
                    <input type="text" id="addEditShippingCredentialForm_username" name="username" value="<?php echo $shipping_credential->username; ?>" class="input-width" />
                </div>
				<div>
                    <p class="right"><?php admin_language_e('setting_view_api_shippingcredentialform_PercentalUpcharge'); ?></p>
                    <input type="text" id="addEditShippingCredentialForm_percental_partner_upcharge" name="percental_partner_upcharge" value="<?php echo $shipping_credential->percental_partner_upcharge; ?>" class="input-width" />
                </div>
			</div>
			<div class="row">
                <div class="left-specific-info">
                    <p class="left"><?php admin_language_e('setting_view_api_shippingcredentialform_Password'); ?></p>
                    <input type="text" id="addEditShippingCredentialForm_password" name="password" value="<?php echo $shipping_credential->password; ?>" class="input-width" />
                </div>
                <div>&nbsp;</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type; ?>" />
	<input type="hidden" id="id" name="id" value="<?php echo $shipping_credential->id; ?>" />
</form>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/settings/js/ShippingCredentialForm.js"></script>
<script type="text/javascript">
	$(document).ready( function($) {
		var mode = '<?php echo $action_type; ?>';
		ShippingCredentialForm.init(mode);
	});
</script>
