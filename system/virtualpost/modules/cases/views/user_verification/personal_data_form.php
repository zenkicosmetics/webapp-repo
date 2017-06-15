<div class="header">
	<h2 style="font-size:  20px; margin-bottom: 10px; margin-left: 40px;"><?php language_e('cases_view_user_verification_personal_data_form_Case'); ?></h2>
</div>
<div class="ym-grid">
	<div id="invoice-body-wrapper" style="margin: 10px 0 0 40px;">
		<div class="ym-g50 ym-gl" style="width:480px"><h2><?php language_e('cases_view_user_verification_personal_data_form_ForwardingAddress'); ?></h2></div>
		<div class="ym-g50 ym-gl" style="width:420px"><h2><?php language_e('cases_view_user_verification_personal_data_form_InvoicingAddress'); ?></h2></div>
	</div>
</div>
<div id="user_verification-body-wrapper">
<div class="ym-grid user_verification-wrapper">
<form id="user_verification_step1" action="<?php echo base_url().'cases/'.$case_id;?>" method="post">
	<div class="ym-clearfix"></div>

	<div class="ym-grid">
		<div class="ym-gl input-cols">
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Firstname'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="firstname" id="firstname" type="text" value="<?php if($case_data) {echo $case_data->firstname; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Lastname'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="lastname" id="lastname" type="text" value="<?php if($case_data) {echo $case_data->lastname; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Street'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="street" id="street" type="text" value="<?php if($case_data) {echo $case_data->street; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_PostCode'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="postcode" id="postcode" type="text" value="<?php if($case_data) {echo $case_data->postcode; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_City'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="city" id="city" type="text" value="<?php if($case_data) {echo $case_data->city; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Region'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="region" id="region"  type="text" value="<?php if($case_data) {echo $case_data->region; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Country'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<select id="country" name="country" class="input-text">
						<?php
						if (!empty($case_data)) {
							foreach ($countries as $country):?>
								<option value="<?php echo $country->id?>"
									<?php if ($case_data->country == $country->id):?>
										selected="selected"
									<?php endif;?>>
									<?php echo $country->country_name?>
								</option>
							<?php endforeach;?>
						<?php } else { ?>
							<?php $geo_df = Geolocation::getCountryCode();
							foreach ($countries as $country):?>
								<option value="<?php echo $country->id?>"
									<?php if( !empty($country->country_code) && strtoupper($country->country_code) == strtoupper( $geo_df ) ):?>
										selected="selected"
									<?php endif;?>>
									<?php echo $country->country_name?>
								</option>
							<?php endforeach;?>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_PhoneNumber'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="phone_number" id="phone_number"  type="text" value="<?php if($address) {echo $address->shipment_phone_number; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>
		<div class="ym-gl input-cols" style="width:480px;">
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Name'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_address_name" id="invoicing_address_name" value="<?php if($address) {echo $address->invoicing_address_name; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Company'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_company" id="invoicing_company" value="<?php if($address) {echo $address->invoicing_company; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Street'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_street" id="invoicing_street"  value="<?php if($address) {echo $address->invoicing_street; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_PostCode'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_postcode" id="invoicing_postcode" value="<?php if($address) {echo $address->invoicing_postcode; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_City'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_city" id="invoicing_city" value="<?php if($address) {echo $address->invoicing_city; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Region'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" type="text" name="invoicing_region" id="invoicing_region"  value="<?php if($address) {echo $address->invoicing_region; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_Country'); ?><span class="required">*</span></label></div>
				<div class="ym-gl ym-g80">
					<select id="invoicing_country" name="invoicing_country" class="input-text">
						<?php foreach ($countries as $country) {?>
							<option value="<?php echo $country->id?>" <?php if (!empty($address) && $address->invoicing_country == $country->id) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
						<?php }?>
					</select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g20"><label><?php language_e('cases_view_user_verification_personal_data_form_PhoneNumber'); ?></label></div>
				<div class="ym-gl ym-g80">
					<input class="input-txt" name="invoicing_phone_number" id="invoicing_phone_number"  type="text" value="<?php if($address) {echo $address->invoicing_phone_number; }?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>
	</div>
	<div class="ym-grid" style="margin-top:-20px;">
		<div class="ym-gl input-cols" style="width:480px">
			<div class="ym-gl ym-g20"><label>&nbsp;</label></div>
			<div class="ym-gl ym-g80" style="text-align: right; width: 100%">
				<input type="button" id="copyAddressButton"  class="input-btn" value="Copy" style="margin-right: 64px;" />
			</div>
		</div>
		<div class="ym-gl input-cols" style="width:480px;">
			<div class="ym-gl ym-g20" style=""><label>&nbsp;</label></div>
			<div class="ym-gr ym-g80" style="text-align: right; padding-right:65px;">
				<input type="button" id="saveAddressButton"  class="input-btn" value="Save" />
			</div>
		</div>
	</div>
</form>

<div class="ym-clearfix" style="height:15px;"></div>

<div class="ym-grid wrapper-box">
	<div class="wrapper-content">
		<div class="ym-grid ym-g40 ym-gl">
			<?php $customer = APContext::getCustomerLoggedIn();?>
			<input type="checkbox" class="customCheckbox" id="checkedVatNumber" <?php if (!empty($customer->vat_number)) {?> checked="checked"<?php }?> />&nbsp;<span><?php language_e('cases_view_user_verification_personal_data_form_IAmABusinessAndHaveAEuropeanVATNumb'); ?>:</span>
		</div>
		<div class="ym-grid ym-g60 ym-gl" style="margin-top:-10px;">
			<div style="width:320px;" class="ym-gl">
				<input class="input-txt" type="text" <?php if (!empty($customer->vat_number)) {?> readonly="readonly" <?php }?>id="vatnumber" value="<?php if (!empty($customer->vat_number)) { echo $customer->vat_number; }?>"/>
			</div>
			<div style="width:160px;margin-left:5px;" class="ym-gl">
				<input class="input-btn" type="button" id="checkVATButton" value="Check VAT" />
				<?php if (!empty($customer->vat_number)) {?>
					<img alt="VAT Number valid" style="width: 32px; float: right;" src="<?php echo APContext::getImagePath()?>/checkmark.png"  />
				<?php }?>
			</div>
		</div>
	</div>
</div>

<div class="ym-clearfix" style="height:35px;"></div>

<div class="ym-grid wrapper-box2 no-border">
	<form id="savePostboxAddressForm" action="<?php echo base_url().'addresses/save_postbox_address';?>" method="post">
		<div class="ym-grid ym-gl">
			<h2 class="title"><?php language_e('cases_view_user_verification_personal_data_form_PostboxAddresses'); ?></h2>
			<div class="ym-clearfix"></div>
			<table class="border">
				<thead class="mn">
				<tr>
					<th class="center-align" style="display: none;">ID</th>
					<th class="center-align"><?php language_e('cases_view_user_verification_personal_data_form_PostboxID'); ?></th>
					<th class="center-align"><?php language_e('cases_view_user_verification_personal_data_form_Type'); ?></th>
					<th class="center-align"><?php language_e('cases_view_user_verification_personal_data_form_Name'); ?> </th>
					<th class="center-align"><?php language_e('cases_view_user_verification_personal_data_form_Company'); ?></th>
					<th class="center-align"><?php language_e('cases_view_user_verification_personal_data_form_Location'); ?></th>
					<th class="center-align"></th>
				</tr>
				</thead>
				<tbody class="nm">
				<?php if(count($postbox)>0){ ?>
					<?php  foreach($postbox as $p){?>
						<tr>
							<td style="display: none;"><input class="input-txt-none" type="text" name="postbox_id<?php echo $p->postbox_id; ?>" value="<?php echo $p->postbox_id; ?>"/></td>
							<td class="center-align"><input class="input-txt-none" type="text" maxlength="35" name="postbox_name<?php echo $p->postbox_id; ?>" value="<?php echo $p->postbox_name; ?>"/></td>
							<td class="center-align">
								<div class="slb-custom">
									<?php echo code_master_form_dropdown(array(
										"code" => APConstants::ACCOUNT_TYPE,
										"value" => $p->type,
										"name" => 'type'.$p->postbox_id,
										"id"    => 'type'.$p->postbox_id,
										"clazz" => '',
										"style" => '',
										"has_empty" => false
									));?>
								</div>
							</td>
							<td class="center-align"><input class="input-txt-none" type="text" name="name<?php echo $p->postbox_id; ?>" value="<?php echo $p->name; ?>"/></td>
							<td class="center-align"><input class="input-txt-none" type="text" name="company<?php echo $p->postbox_id; ?>" value="<?php echo $p->company; ?>"/></td>
							<td class="center-align">
								<div>
									<?php
									$location_name = "";
									// Gets location
									foreach($locate as $l){
										if($l->id == $p->location_available_id){
											$location_name = $l->location_name;
											break;
										}
									}
									?>
									<input type="text" readonly="readonly" class="input-txt-none readonly" disabled="disabled" value="<?php echo $location_name;?>" />
									<?php /*echo my_form_dropdown(array(
                                         "data" => $locate,
                                         "value_key" => 'id',
                                         "label_key" => 'location_name',
                                         "value" => $p->location_available_id,
                                         "name" => 'location_available_id'.$p->postbox_id,
                                         "id"    => 'cust_location'.$p->postbox_id,
                                         "clazz" => '',
                                         "style" => '',
                                         "has_empty" => true,
                                         "html_option"=>'disabled="disabled"'
                                     ));*/?>
								</div>
							</td>
							<td>
								<input type="button" title="Show Mailing Address" class="input-btn show_mailing_address" value="Show"
								       data-id="<?php echo $p->postbox_id?>" data-location_available_id="<?php echo $p->location_available_id?>" />
							</td>
						</tr>
					<?php } ?>

				<?php } ?>
				</tbody>
			</table>

			<div class="ym-clearfix"></div>
			<div class="ym-gl ym-g60" style="color: red;margin-top: 10px;padding-top: 12px">&nbsp;</div>
			<div class="ym-gr ym-g40" style="text-align: right;margin-top: 10px;">
				<input type="button" id="savePostboxAddressButton" class="input-btn" value="Save" />
			</div>
		</div>
	</form>
</div>

<div class="ym-clearfix" style="height:35px;"></div>

<div class="ym-grid wrapper-box2 no-border">
	<h2 class="title"><?php language_e('cases_view_user_verification_personal_data_form_LocationsAvailable'); ?></h2>
	<div class="ym-clearfix" style="height:25px;"></div>

	<div >
		<?php
		$cnt = 0;
		for($i = 0; $i < count($locate); $i++){
			$cnt ++;
			$lc = $locate[$i];
			if($cnt ==1){
				?>
				<div class="ym-grid">
				<div class="ym-g33 ym-gl" style=" width:32%; border:2px solid #dadada;margin-top: 10px;">
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->location_name, 30);} ?>
					</div>
					<div>
						<?php if (empty($lc->image_path)) {?>
							<img src="<?php echo APContext::getAssetPath()?>uploads/images/location/default_location.png"  style="width:100%; height:100px;" >
						<?php } else {?>
							<img src="<?php echo APContext::getAssetPath().$lc->image_path?>"  style="width:100%; height:100px;" >
						<?php }?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->street, 30);} ?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->postcode, 30);} ?>
					</div style="padding: 10px 5px">
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->region, 30);} ?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->country, 30);} ?>
					</div>
				</div>
			<?php }else{?>
				<div class="ym-g33 ym-gl" style="width:32%; border:2px solid #dadada;margin-top: 10px;margin-left:10px;">
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->location_name, 30);} ?>
					</div>
					<div>
						<?php if (empty($lc->image_path)) {?>
							<img src="<?php echo APContext::getAssetPath() ?>uploads/images/location/default_location.png"  style="width:100%; height:100px;" >
						<?php } else {?>
							<img src="<?php echo APContext::getAssetPath().$lc->image_path?>"  style="width:100%; height:100px;" >
						<?php }?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->street, 30);} ?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->postcode, 30);} ?>
					</div style="padding: 10px 5px">
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->region, 30);} ?>
					</div>
					<div style="padding: 10px 5px">
						<?php if($lc) {echo APUtils::autoHidenText($lc->country, 30);} ?>
					</div>
				</div>
			<?php }
			// reset $cnt
			if($cnt == 3){
				$cnt = 0;
				?>
				<!-- close row -->
				</div>
			<?php
			}
			?>
		<?php }?>

	</div>
</div>

<div class="ym-clearfix" style="height:35px;"></div>
</div>
</div>