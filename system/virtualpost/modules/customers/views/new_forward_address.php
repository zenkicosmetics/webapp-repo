<form id="save_New_Forward_AddressForm"
	action="<?php echo base_url().'addresses/save_new_forward_address';?>"
	method="post">
	<div class="ym-clearfix"></div>

	<div id="wrap_forward_add" class="ym-grid" style="margin-left: 20px; width: 460px">
		<div class="ym-gl input-cols" style="width: 450px">		
			<div class="ym-grid input-item">
				<h2 style="font-size: 20px;margin-top: 4px;">Please enter the address for this shipment here:</h2>
				<div class="ym-gl ym-g30">
					<label >Name:</label>
				</div>
				<div class="ym-gl ym-g70">
					<input id="shipment_address_name_id" class="input-txt-none" name="shipment_address_name" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >Company:</label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_company" id="shipment_company" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >Street: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none new_shipment_street" name="shipment_street" id="shipment_street"  type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >Post Code: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none new_shipment_postcode" name="shipment_postcode" id="shipment_postcode" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >City: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none new_shipment_city" name="shipment_city" id="shipment_city" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >Region: </label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_region" id="shipment_region" type="text" value="" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g30">
					<label >Country: <span class="required">*</span></label>
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
					<label>Phone Number:</label>
				</div>
				<div class="ym-gl ym-g70">
					<input class="input-txt-none" name="shipment_phone_number" id="shipment_phone_number" type="text" value="" />
				</div>
			</div> 
<!--			<div class="ym-grid input-item" >
					<input id="standard_address_flag" class="" name="active_flag" id="" type="checkbox" value="1" />
					<label id="wrab_standard_address_flag" style="cursor:pointer;">Save this address to my forwarding address book</label>
			</div> -->
		</div>
	</div>
	<input type="hidden" value="<?php echo $envelope_id; ?>" name="envelope_id" />
    <input type="hidden" value="<?php echo $customer_id; ?>" name="customer_id" />
</form>
<style type="text/css">
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
.error {
    background: #c88 none repeat scroll 0 0 !important;
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
</style>
<script type="text/javascript">

jQuery(document).ready(function($){
   $("#wrab_standard_address_flag").click(function(){
		if($("#standard_address_flag").prop('checked')) {

			$("#standard_address_flag").prop('checked',false);
		} 
		else {
			$("#standard_address_flag").prop('checked',true);
		}
   });
   
   $("#shipment_street").on("keyup",function(){
   		if($(this).val() != '') $(this).removeClass('error');
   		else $(this).addClass('error');
   });
   $("#shipment_postcode").on("keyup",function(){
   		if($(this).val() != '') $(this).removeClass('error');
   		else $(this).addClass('error');
   });
   $("#shipment_city").on("keyup",function(){
   		if($(this).val() != '') $(this).removeClass('error');
   		else $(this).removeClass('error');
   });

});


</script>