
<form id="saveForward_AddressForm" rel="<?php echo base_url().'addresses/save_forward_address';?>" action="<?php echo base_url().'addresses/save_forward_address';?>"
	method="post">
	<div class="ym-clearfix"></div>

	<div id="wrap_forward_add" class="ym-grid" style="margin-left: 20px; width: 860px">
		<div class="ym-gl input-cols" style="width: 400px">		
			<div class="ym-grid input-item">
				<h2><?php language_e('customer_view_manage_forward_address_StandardAddress') ?></h2>
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Name') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input id="shipment_address_name_id" class="input-txt-none" name="shipment_address_name" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_address_name;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Company') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_company" id="shipment_company" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_company;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Street') ?> <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_street" id="shipment_street"  type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_street;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PostCode') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_postcode" id="shipment_postcode" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_postcode;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_City') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_city" id="shipment_city" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_city;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Region') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_region" id="shipment_region" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_region;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Country') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
				    <select id="shipment_country" name="shipment_country" class="input-text" style="width:101%;margin-top: 2px;padding: 3px;margin-left: 0px">
                            <?php if(!empty($customer_address)):?>
                                 <?php foreach ($countries as $country) {?>
                                 <option value="<?php echo $country->id?>" <?php if ( $customer_address->shipment_country == $country->id) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
                                 <?php }?>
                            <?php else: $geo_df = Geolocation::getCountryCode();?>
                                 <?php foreach ($countries as $country) {?>
                                      <option value="<?php echo $country->id?>" <?php if (!empty($country->country_code) && strtoupper($geo_df) == strtoupper($country->country_code)) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
                                 <?php }?>
                            <?php endif;?>
				    </select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PhoneNumber') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_phone_number" id="shipment_phone_number" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_phone_number;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
			
		</div>

		<?php $i=1; if(count($address_alt)): foreach($address_alt as $row):?>
		<div class="ym-gl input-cols shipping_address_<?php if (!empty($row->id)) { echo $row->id;}?>" style="width: 400px;">
			<input type="hidden" name="shipment_id_alt[]" value="<?php if (!empty($row->id)) { echo $row->id;}?>" />
			<div class="ym-grid input-item">
				<h2>Alternative <?php echo $i; ?><div rel="<?php if (!empty($row->id)) { echo $row->id;}?>" class="deleteAddress" id="deleteAddress">&nbsp;</div></h2>
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Name') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_address_name_alt[]" id="shipment_address_name_alt_<?php echo $row->id; ?>"
						value="<?php if (!empty($row->shipment_address_name)) { echo $row->shipment_address_name;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Company') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_company_alt[]" id="shipment_company_alt_<?php echo $row->id;?>"
						value="<?php if (!empty($row->shipment_company)) { echo $row->shipment_company;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Street') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_street_alt[]" id="shipment_street_alt_<?php echo $row->id;?>"
						value="<?php if (!empty($row->shipment_street)) { echo $row->shipment_street;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PostCode') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_postcode_alt[]" id="shipment_postcode_alt_<?php echo $row->id;?>"
						value="<?php if (!empty($row->shipment_postcode)) { echo $row->shipment_postcode;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_City') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_city_alt[]" id="shipment_city_alt_<?php echo $row->id; ?>"
						value="<?php if (!empty($row->shipment_city)) { echo $row->shipment_city;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Region') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_region_alt[]" id="shipment_region_alt_<?php echo $row->id; ?>"
						value="<?php if (!empty($row->shipment_region)) { echo $row->shipment_region;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Country') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
				    <select id="shipment_country_alt" name="shipment_country_alt[]" class="input-text" style="width:101%;margin-top: 2px;padding: 3px;margin-left: 0px">
				         <?php if(!empty($row->shipment_country)):?>
                                  <?php foreach ($countries as $country) {?>
                                       <option value="<?php echo $country->id?>" <?php if ( $row->shipment_country == $country->id) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
                                  <?php }?>
                             <?php else: $geo_df = Geolocation::getCountryCode();?>
                                  <?php foreach ($countries as $country) {?>
                                       <option value="<?php echo $country->id?>" <?php if (!empty($country->country_code) && strtoupper($geo_df) == strtoupper($country->country_code)) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
                                  <?php }?>
                             <?php endif;?>
				    </select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PhoneNumber') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_phone_number_alt[]" id="shipment_phone_number_alt_<?php echo $row->id; ?>" type="text"
						value="<?php if (!empty($row->shipment_phone_number)) { echo $row->shipment_phone_number;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>
		<?php $i++; endforeach; else: ?>
		
		<div class="ym-gl input-cols" style="width: 400px;">
			<div class="ym-grid input-item">
				<input type="hidden" name="shipment_id_alt[]" value="0" />
				<h2><?php language_e('customer_view_manage_forward_address_Alternative') ?><?php echo $i; ?></h2>
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Name') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_address_name_alt_1" type="text" name="shipment_address_name_alt[]" id="shipment_address_name_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Company') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_company_alt_1" type="text" name="shipment_company_alt[]" id="shipment_company_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Street') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input rel="1" class="input-txt-none shipment_street_alt shipment_street_alt_1" type="text" name="shipment_street_alt[]" id="shipment_street_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PostCode') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input rel="1" class="input-txt-none shipment_postcode_alt shipment_postcode_alt_1" type="text" name="shipment_postcode_alt[]" id="shipment_postcode_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_City') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input rel="1" class="input-txt-none shipment_city_alt shipment_city_alt_1" type="text" name="shipment_city_alt[]" id="shipment_city_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Region') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_region_alt_1" type="text" name="shipment_region_alt[]" id="shipment_region_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Country') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
				    <select id="shipment_country_alt" name="shipment_country_alt[]" class="input-text shipment_country_alt_1" style="width: 101%;margin-top: 2px;padding: 3px;margin-left: 0px">
						 <?php $geo_df = Geolocation::getCountryCode();?>
						 <?php foreach ($countries as $country) {?>
							  <option value="<?php echo $country->id?>" <?php if (!empty($country->country_code) && strtoupper($geo_df) == strtoupper($country->country_code)) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
						 <?php }?>
				    </select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PhoneNumber') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_phone_number_alt_1" name="shipment_phone_number_alt[]" id="shipment_phone_number_alt" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>	
			
		<?php $i=2; endif; ?>
</div>
<input type="hidden" name="current_order_forward_address" id="current_order_forward_address" value="<?php echo $i; ?>" />
<input type="hidden" name="envelope_id" id="envelope_id" value="<?php echo $envelope_id ?>" />
<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id ?>" />
</form>

<div style="display: none;" id="clone">
<div class="ym-gl input-cols shipping_address_0" style="width: 400px;">
			<div class="ym-grid input-item">
				<input type="hidden" name="shipment_id_alt[]" value="0" />
				<h2><?php language_e('customer_view_manage_forward_address_Alternative') ?><div rel="0" class="deleteAddress" id="deleteAddress">&nbsp;</div></h2>
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Name') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_address_name_alt[]" id="shipment_address_name_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Company') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_company_alt[]" id="shipment_company_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_Street') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_street_alt" type="text" name="shipment_street_alt[]" id="shipment_street_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PostCode') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_postcode_alt" type="text" name="shipment_postcode_alt[]" id="shipment_postcode_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_City') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none shipment_city_alt"  type="text" name="shipment_city_alt[]" id="shipment_city_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Region') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" type="text" name="shipment_region_alt[]" id="shipment_region_alt" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label><?php language_e('customer_view_manage_forward_address_Country') ?><span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
				    <select id="shipment_country_alt" name="shipment_country_alt[]" class="input-text" style="width: 101%;margin-top: 2px;padding: 3px;width: 277px;margin-left: 0px">
						 <?php $geo_df = Geolocation::getCountryCode();?>
						 <?php foreach ($countries as $country) {?>
							  <option value="<?php echo $country->id?>" <?php if (!empty($country->country_code) && strtoupper($geo_df) == strtoupper($country->country_code)) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
						 <?php }?>
				    </select>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label ><?php language_e('customer_view_manage_forward_address_PhoneNumber') ?></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_phone_number_alt[]" id="shipment_phone_number_alt" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>
</div>

<style type="text/css">
	.error {
    background: #c88 none repeat scroll 0 0 !important;
}

.input-cols {
	margin-left: 15px;
	margin-right: 15px;
}
.input-item {
	line-height: 32px;
	margin:2px 0px;
}
#wrap_forward_add h2 {
border-bottom: 1px solid;
    color: #336699;
    font-size: 23px;
    line-height: 16px;
    margin-bottom: 12px;
	margin-top: 20px;
    padding-bottom: 10px;

}
.ym-g30 {
    width: 30%;
}
.ym-g70 {
    width: 70%;
}
select.input-text {
	background: rgba(0, 0, 0, 0) url("<?php echo base_url('/system/virtualpost/themes/account_setting2/images/input-bg.png')?>") repeat-x scroll 0 0;
    border: 1px solid #dadada;
    border-radius: 3px;
    font-size: 13px;
    height: 30px;
    line-height: 25px;
    margin-left: 2%;
    padding-bottom: 5px;
    padding-top: 5px;
    text-indent: 2px;
    width: 81%;
}
input.input-txt-none, select.input-txt-none{
margin-top:0px;
background: rgba(0, 0, 0, 0) url("<?php echo base_url('/system/virtualpost/themes/account_setting2/images/input-bg.png')?>") repeat-x scroll 0 0;
height:28px;
width: 100%;
margin-left:0px;
}

.deleteAddress {
	background: transparent url("<?php echo base_url('/system/virtualpost/themes/account_setting2/images/icon-set_1.png')?>") no-repeat scroll -141px -61px;
    height: 21px;
    margin: 0 auto;
    width: 20px;
	float: right;
	cursor: pointer;
}
</style>



<script type="text/javascript">

jQuery(document).ready(function(){
   
   $("#wrap_forward_add #shipment_street").live("keyup",function(){
   		if($(this).val() != '')$(this).removeClass('error');
   		else $(this).addClass('error');
   });

   $("#wrap_forward_add #shipment_postcode").live("keyup",function(){
   		if($(this).val() != '')$(this).removeClass('error');
   		else $(this).addClass('error');
   });

   $("#wrap_forward_add #shipment_city").live("keyup",function(){
   		if($(this).val() != '') $(this).removeClass('error');
   		else $(this).addClass('error');
   });


   // $(".shipment_street_alt").hide();
   $("#wrap_forward_add .shipment_street_alt").live("keyup",function(){
	   	var rel = $(this).attr('rel');
	   	if($(".shipment_street_alt_"+rel).val() != '') $(this).removeClass('error'); 
	   	else $(this).addClass('error'); 
   });

   $("#wrap_forward_add .shipment_postcode_alt").live("keyup",function(){
	   	var rel = $(this).attr('rel');
	   	if($(".shipment_postcode_alt_"+rel).val() != '') $(this).removeClass('error');
	   	else $(this).addClass('error');
   });

   $("#wrap_forward_add .shipment_city_alt").live("keyup",function(){
	   	var rel = $(this).attr('rel');
	   	if($(".shipment_city_alt_"+rel).val() != '') $(this).removeClass('error');
	   	else $(this).addClass('error');
   });

   $(".deleteAddress").click(function(){

   		var shipping_address_id = $(this).attr('rel');
   		/*
   		if(shipping_address_id == 0){
   			$("#wrap_forward_add div.shipping_address_"+shipping_address_id).remove();
   		}*/
   		
   		var submitUrl = '<?php base_url()?>addresses/deleteAlternativeAddress?shipping_address_id='+shipping_address_id;
   		$.ajaxExec({
            url: submitUrl,
            success: function(data) {
                if (data.status) {
                	$("#wrap_forward_add div.shipping_address_"+shipping_address_id).remove();
                	
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
   });



});
function deleteAddressClick(){

 	$("#wrap_forward_add div.shipping_address_0").remove();
 	var currentAddress = $("#current_order_forward_address").val();
 	$("#current_order_forward_address").val(currentAddress-1);     
 } 

</script>