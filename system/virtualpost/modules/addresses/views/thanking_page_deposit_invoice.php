<?php $customer = APContext::getCustomerLoggedIn(); ?>
<?php if(empty($customer->deactivated_type)){ ?>
<h2 style="font-size: 20px; text-align: center;">Thank you for completing the setup process.</h2>
    <div style="text-align: center; margin-top: 5px;margin-bottom: 5px;">
        <p>You can receive mail now at the following address:</p>
        <div style="text-align: left; margin-left: 250px; margin-top: 10px;">
            <p><?php if (!empty($customer_postbox->name)) {echo $customer_postbox->name;}?></p>
            <p><?php if (!empty($customer_postbox->company)) {echo $customer_postbox->company;}?></p>

            <p><?php if (!empty($location)) { echo $location->street;}?></p>
            <p><?php if (!empty($location)) { echo $location->postcode.' '.$location->city;}?></p>
            <p><?php if (!empty($location) && !empty($location->region)) { echo $location->region;}?></p>
            <p><?php if (!empty($country)) { echo $country->country_name;}?></p>
            <p><?php if (!empty($location) && !empty($location->phone_number)) { echo $location->phone_number;}?></p>
            <p><?php if (!empty($location) && !empty($location->email)) { echo $location->email;}?></p>

        </div>
    </div>
    <div style="width: 100%; height: 1px; background-color: #eee;margin-bottom: 10px" ></div>
    <div>
    <?php if(APContext::isStandardCustomer() || APContext::isAdminCustomerUser() || APContext::isPrimaryCustomerUser()){ ?>
    <div style="margin: auto 0px;">
        <div style="width: 100%; text-align: center;">
            Please make a deposit payment now with Paypal
        </div>
        <div style="width: 100%; text-align: center; margin-top: 10px;">
            <a href="#" id="paypalPaymentForm_paynowWithPaypal" >
                <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
            </a>
        </div>
    </div>
    <div style="width: 100%; height: 1px; background-color: #eee; margin: auto 0px; margin-top: 10px" ></div>
    <?php }?>
<?php }?>
<div style="margin: auto 0px; width: 100%;">
    <table style="border: 0px none; width: 100%">
    	<tr>
    		<th colspan="2" style="border: 0px none;">You can also make a deposit payment with bank transfer. Our bank details are as follows:</th>
    	</tr>
    	<tr>
    		<td style="text-align: right;">Account holder</td>
    		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
    			id="invoicePaymentForm_iban" name="iban"
    			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?>"
    			class="input-txt-none" /></td>
    	</tr>
    	<tr>
    		<td style="text-align: right;">Bank name</td>
    		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
    			id="invoicePaymentForm_bankname" name="bankname"
    			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?>"
    			class="input-txt-none" /></td>
    	</tr>
    	<tr>
    		<td style="text-align: right;">IBAN</td>
    		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
    			id="invoicePaymentForm_iban" name="iban"
    			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?>"
    			class="input-txt-none" /></td>
    	</tr>
    	<tr>
    		<td style="text-align: right;">BIC</td>
    		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
    			id="invoicePaymentForm_bic" name="bic"
    			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?>"
    			class="input-txt-none" /></td>
    	</tr>
    </table>
</div>
</div>
<br/>
<div style="margin: auto 0px; width: 100%; text-align: center;">
    <p><strong>Please make sure to verify your postbox to enable scanning and forwarding.</strong></p>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
    //$('#paypalPaymentForm_paynowWithPaypal').button();
    $('#paypalPaymentForm_paynow').button();


    $('#paypalPaymentForm_paynowWithPaypal').live('click', function() {
    	// Open new dialog
		$('#paymentWithPaypalWindow').openDialog({
			autoOpen: false,
			height: 300,
			width: 500,
			modal: true,
			closeOnEscape: false,
			open: function(event, ui) {
				$(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice?thank_page=1", function() {
				});
			}
		});
		
		$('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
		$('#paymentWithPaypalWindow').dialog('open');
		
		$('#paypalPaymentWindow').dialog('close');
    	return false;
    	
    });
    
    var makeDepositDivContainerFlag = false;
    $("#makeDepositDivContainer").hide();
    $("#thankingWindow_make_deposit_payment").bind('click', function(e){
        e.preventDefault();
        if(makeDepositDivContainerFlag == false){
            $("#makeDepositDivContainer").show();
            makeDepositDivContainerFlag = true;
        } else{
            $("#makeDepositDivContainer").hide();
            makeDepositDivContainerFlag = false;
        }
        
        return false;
    });
});
</script>