<div class="ym-grid" style="margin-left: 20px; width: 860px">
    <div id="invoice-body-wrapper" style="margin: 10px 0 0 0px;width: 850px">
	<div class="ym-g50 ym-gl" style="width:430px; height: 50px;">
	    <h2 style="width:290px" id="shipping_address_tipsy_tooltip" class="" >
	        Forwarding address
	        <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="shipping_address_tipsy_tooltip" title="Forwarding address is the address we will use to send you your mail upon request. It can be changed at all times in your account settings, where you can also add multiple forwarding addresses."></span>
	    </h2>
	</div>
	<div class="ym-g50 ym-gl diff-invoice-address" style="width:410px; height: 50px; display: none;">
	    <h2 style="width:290px" id="invoicing_address_tipsy_tooltip" class="" >
	        Invoicing address
	        <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="invoicing_address_tipsy_tooltip" title="Please enter here your invoicing address. You will receive invoices from us either in PDF format in our system or as an Email. We do not send invoices by postal mail."></span>
	     </h2>
	</div>
	</div>
</div>
<form id="saveAddressForm"
	action="<?php echo base_url().'addresses/save_address';?>"
	method="post">
	<div class="ym-clearfix"></div>

	<div class="ym-grid" style="margin-left: 20px; width: 860px">
		<div class="ym-gl input-cols" style="width: 430px">
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Name:</label>
				</div>
				<div class="ym-gl ym-g100">
					<input id="shipment_address_name_id" class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_address_name" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_address_name;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Company:</label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_company" id="shipment_company" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_company;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Street: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_street" id="shipment_street"  type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_street;}?>" />
                    <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="shipping_address_tipsy_tooltip" title="Please enter a real street address. This information is needed so we can forward mail to you."></span>
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Post Code: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_postcode" id="shipment_postcode" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_postcode;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >City: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_city" id="shipment_city" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_city;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Region: </label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_region" id="shipment_region" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_region;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Country: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
				    <select id="shipment_country" name="shipment_country" class="input-text saveAddressForm_changeInvoiceAddress" style="margin-top: 10px;padding: 3px;width: 250px;margin-left: 0px">
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
				<div class="ym-gl ym-g25 register_label">
					<label >Phone Number:  <span style="color:black">**</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none saveAddressForm_changeInvoiceAddress" name="shipment_phone_number" id="shipment_phone_number" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_phone_number;}?>" /><span class="managetables-icon icon_help tipsy_tooltip"
                        data-tooltip="shipping_address_tipsy_tooltip" title="Phone numbers are often required by courier services when packages are sent. By inputting a phone number here, you make sure to receive your forwarded mail. Of course you can always change and delete this information, and your information remains completely confidential with us."></span>
                    <p style="color:black;font-size:10.3px;margin-top:3px;"><span>**required for parcel shipments to this address</span></p>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
			    <!--<div class="ym-gl ym-g25"><label>&nbsp;</label></div>
				<div class="ym-gl ym-g100" style="text-align: right; width: 105%">
					<input type="button" id="copyAddressButton"  class="input-btn tipsy_tooltip" value="Copy to invocing address" style="margin-right: -68px;margin-top:18px;" title="You can copy the forwarding address here, if it‘s the same as your invoicing address." />
				</div>
                -->
			</div>
		</div>
        
        <!-- invoice address --->
		<div class="ym-gl input-cols diff-invoice-address" style="width: 430px; display:none;">
			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Name:</label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_address_name" id="invoicing_address_name"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_address_name;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Company:</label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_company" id="invoicing_company"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_company;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Street: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_street" id="invoicing_street"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_street;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >Post Code: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_postcode" id="invoicing_postcode"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_postcode;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label >City: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_city" id="invoicing_city"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_city;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label>Region: </label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" type="text" name="invoicing_region" id="invoicing_region"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_region;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>

			<div class="ym-grid input-item">
				<div class="ym-gl ym-g25 register_label">
					<label>Country: <span class="required">*</span></label>
				</div>
				<div class="ym-gl ym-g100">
				    <select id="invoicing_country" name="invoicing_country" class="input-text" style="margin-top: 10px;padding: 3px;width: 250px;margin-left: 0px">
				         <?php if(!empty($customer_address)):?>
                                  <?php foreach ($countries as $country) {?>
                                       <option value="<?php echo $country->id?>" <?php if ( $customer_address->invoicing_country == $country->id) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
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
				<div class="ym-gl ym-g25 register_label">
					<label >Phone Number: </label>
				</div>
				<div class="ym-gl ym-g100">
					<input class="input-txt-none" name="invoicing_phone_number" id="invoicing_phone_number" type="text"
						value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_phone_number;}?>" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
		</div>
	</div>
    
    <br />
    <div class="ym-clearfix"></div>
    <div class="ym-grid">
        <div class="ym-g50 ym-gl">
            <input type="button" id="copy_invoicing_addresss" class="input-btn btn-yellow" value="This is also my invoicing address" style="position: relative; left: 20px; width: 365px;" />
        </div>
        <div class="ym-g50 ym-gl">
            <input type="button" id="differ_invoicing_addresss" class="input-btn" value="My invoicing address differs" style="position: relative; left: 20px; width: 365px;" />
        </div>
    </div>
</form>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('.tipsy_tooltip').tipsy({gravity: 'sw'});
	$(".tipsy_tooltip" ).each(function( index ) {
		$(this).tipsy("show");
	});
	setTimeout(function() {
		$(".tipsy_tooltip" ).each(function( index ) {
			$(this).tipsy("hide");
		});
	},2000);
	
    /**
     * When user click to copy button
     */
    $('#copyAddressButton, #copy_invoicing_addresss').click(function() {
        $("#differ_invoicing_addresss").removeClass("btn-yellow");
        $("#copy_invoicing_addresss").addClass("btn-yellow");
        copyAddress();
        
        $(".diff-invoice-address").hide();
    });
    
    
    $("#differ_invoicing_addresss").click(function(){
        $("#copy_invoicing_addresss").removeClass("btn-yellow");
        $("#differ_invoicing_addresss").addClass("btn-yellow");
        
        $(".diff-invoice-address").show();
    });
    
    $(".saveAddressForm_changeInvoiceAddress").bind('change', function(){
        copyAddress();
    });
    
    function copyAddress(){
        $('#invoicing_address_name').val($('#shipment_address_name_id').val());
        $('#invoicing_company').val($('#shipment_company').val());
        $('#invoicing_street').val($('#shipment_street').val());
        $('#invoicing_postcode').val($('#shipment_postcode').val());
        $('#invoicing_city').val($('#shipment_city').val());
        $('#invoicing_region').val($('#shipment_region').val());
        $('#invoicing_country').val($('#shipment_country').val());
        $('#invoicing_phone_number').val($('#shipment_phone_number').val());
    }
});
</script>