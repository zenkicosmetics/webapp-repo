<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'settings/api/add_shipping_api';
} else {
    $submit_url = base_url() . 'settings/api/edit_shipping_api';
}
?>
<style>
	div.api-info-container {margin: 20px 15px;}
	div.api-info-container p.left { float: left; display: block; text-align: left; vertical-align: middle; width: 100px; font-weight: bold; height: 100%; padding-right: 20px; }
	div.api-info-container p.right { float: left; display: block; text-align: left; vertical-align: middle; width: 220px; font-weight: bold; height: 100%; padding-right: 20px; }
	div.api-info-container div.common-info div.common-field { margin-top: 20px;}
	div.api-info-container div.specific-info div.left-specific-info {float: left; margin-right: 70px;}
	div.api-info-container div.specific-info div.row {margin-top: 20px;}
    .input-disable {
        background-color: lightgray;
    }
</style>
<form id="addEditShippingAPIForm" method="post" class="dialog-form" action="<?php echo $submit_url; ?>">
	<div class="api-info-container">
		<div class="common-info">
			<div class="common-field">
				<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_Name'); ?><span class="required">*</span></p>
				<input type="text" id="addEditShippingAPIForm_name" name="name"	value="<?php echo $shipping_api->name; ?>" class="input-width" style="width: 820px;" />
			</div>
			<div class="common-field">
				<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_Description'); ?></p>
				<textarea id="addEditShippingAPIForm_description" name="description" class="input-width" rows="5" style="width: 820px; height: 46px;"><?php echo $shipping_api->description; ?></textarea>
			</div>
		</div>
		<div class="specific-info">
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_Carrier'); ?></p>
					<?php
					echo my_form_dropdown(array(
						"data" => $list_carriers,
						"value_key" => 'id',
						"label_key" => 'name',
						"value" => $shipping_api->carrier_id,
						"name" => 'carrier_id',
						"id"    => 'carrier_id',
						"clazz" => 'input-width carrier',
						"style" => 'width: 262px;',
						"has_empty" => true
					));
					?>
				</div>
				<div>
					<p class="right"><?php admin_language_e('setting_view_api_shippingapiform_EStampParnetSignature'); ?></p>
					<input readonly type="text" id="estamp_partner_signature" name="estamp_partner_signature" value="<?php echo $shipping_api->estamp_partner_signature; ?>" class="input-width estamp input-disable" />
				</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_AccountNo'); ?></p>
                    <input type="text" readonly id="addEditShippingAPIForm_account_no" name="account_no"	value="<?php echo $shipping_api->account_no; ?>" class="input-width input-disable" />
				</div>
				<div>
					<p class="right"><?php admin_language_e('setting_view_api_shippingapiform_EStampNamespace'); ?></p>
					<input readonly type="text" id="estamp_namespace" name="estamp_namespace" value="<?php echo $shipping_api->estamp_namespace; ?>" class="input-width input-disable" />
				</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_MeterNo'); ?></p>
					<input readonly type="text" id="addEditShippingAPIForm_meter_no" name="meter_no"	value="<?php echo $shipping_api->meter_no; ?>" class="input-width input-disable" />
				</div>
				<div>
					<p class="right"><?php admin_language_e('setting_view_api_shippingapiform_PriceIncludesVAT'); ?></p>
					<input type="text" id="addEditShippingAPIForm_price_includes_vat" name="price_includes_vat"	value="<?php echo $shipping_api->price_includes_vat; ?>" class="input-width" />
				</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_AuthKey'); ?></p>
					<input readonly type="text" id="addEditShippingAPIForm_auth_key" name="auth_key" value="<?php echo $shipping_api->auth_key; ?>" class="input-width input-disable" />
				</div>
				<div>
                                    <p class="right"><?php admin_language_e('setting_view_api_shippingapiform_PartnerToAssociate'); ?></p>
                                    <?php
                                    echo my_form_dropdown(array(
                                            "data" => $list_partner,
                                            "value_key" => 'partner_id',
                                            "label_key" => 'partner_name',
                                            "value" => $shipping_api->partner_id,
                                            "name" => 'partner_id',
                                            "id"    => 'partner_id',
                                            "clazz" => 'input-width carrier input-disable',
                                            "style" => 'width: 262px;',
                                            "has_empty" => true,
                                        "html_option" => 'disabled'
                                    ));
                                    ?>
                                </div>
			</div>
			<div class="row">
                            <div class="left-specific-info">
                                    <p class="left"><?php admin_language_e('setting_view_api_shippingapiform_Username'); ?></p>
                                    <input readonly type="text" id="addEditShippingAPIForm_username" name="username" value="<?php echo $shipping_api->username; ?>" class="input-width input-disable" />
                            </div>
                            <div>
                                <p class="right"><?php admin_language_e('setting_view_api_shippingapiform_PercentalUpcharge'); ?></p>
                                <input readonly type="text" id="addEditShippingAPIForm_percental_partner_upcharge" name="percental_partner_upcharge" value="<?php echo $shipping_api->percental_partner_upcharge; ?>" class="input-width input-disable" />
                            </div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_Password'); ?></p>
					<input readonly type="text" id="addEditShippingAPIForm_password" name="password" value="<?php echo $shipping_api->password; ?>" class="input-width input-disable" />
				</div>
				<div>&nbsp;</div>
			</div>
			<div class="row">
				<div class="left-specific-info">
					<p class="left"><?php admin_language_e('setting_view_api_shippingapiform_SiteID'); ?></p>
					<input type="text" id="addEditShippingAPIForm_site_id" name="site_id" value="<?php echo $shipping_api->site_id; ?>" class="input-width" />
				</div>
				<div>&nbsp;</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type; ?>" />
	<input type="hidden" id="id" name="id" value="<?php echo $shipping_api->id; ?>" />
</form>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/settings/js/ShippingAPIForm.js"></script>
<script type="text/javascript">
	$(document).ready( function($) {
		var mode = '<?php echo $action_type; ?>';
		ShippingAPIForm.init(mode);
	});
</script>
