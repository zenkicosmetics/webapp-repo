<?php
$submit_url = base_url().'payment/add';
$isEnterpriseCustomer = APContext::isPrimaryCustomerUser();
?>
<div class="ym-grid" style="margin-left: 20px; width: 500px">
    <div id="invoice-body-wrapper" style="margin: 10px 0 0 0px;width: 500px">
        <div  style="width:500px;"> 
            <h4 >
                <?php if($isEnterpriseCustomer){?>
                You need to start your enterprise account with a deposit payment. Later, you can set up an automatism to charge a credit card whenever your deposit balance drops below a certain limit.
                <?php }else {?>
                You can run your ClevverMail account with deposit payments (bank transfer, PayPal, credit card) or by saving a credit card to your account.
                <?php }?>
            </h4>
        </div>
        <div class="ym-g100 ym-gl" style="height: 50px;">
            <h2 id="input_payment_tipsy_tooltip" class="" >Please enter your payment details.
                <?php if(!$isEnterpriseCustomer){?>
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="input_payment_tipsy_tooltip"
                      title="Credit card charges are monthly, based on the activities of each month. When selecting the invoice option, you will need to have a positive balance in your account by making a deposit payment via Paypal, credit card or bank transfer. You can always edit your payment method within your account settings"></span>
                <?php }?>
            </h2>
        </div>
	</div>
</div>
<form id="addEditPaymentMethodForm" method="post" action="<?php echo $submit_url?>">
    <div class="ym-grid" style="margin-left: 20px; width: 500px">
        <div class="ym-grid input-item" style="display: none;">
            <div class="ym-gl ym-g33 register_label">
				<label >Invoice Type <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
				<select id="addEditPaymentMethod_invoice_type" name="invoice_type" class="input-txt-none" style="line-height: 24px">
    			    <option value="1">Standard payment</option>
    			    <option value="2">Invoice payment</option>
    			</select>
			</div>
		</div>
		<div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_invoice_code" class="ym-grid input-item" style="display: none;">
            <div class="ym-gl ym-g33 register_label">
				<label >Invoice Code <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
				<input type="text" id="addEditPaymentMethod_invoice_code" name="invoice_code"
				value="" class="input-txt-none" maxlength=255 />
			</div>
		</div>
		<div class="ym-clearfix"></div>
        <div id="traddEditPaymentMethod_account_type" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >Payment selection: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
				<select id="addEditPaymentMethod_account_type" name="account_type" class="input-txt-none" style="line-height: 24px">
    			     <option value="<?php echo APConstants::PAYMENT_CREDIT_CARD_ACCOUNT?>">Credit Card Account</option>
    			     <option value="<?php echo APConstants::PAYMENT_DIRECT_DEBIT_ACCOUNT?>">Invoice with deposit</option>
    			</select>
			</div>
		</div>
		<!-- Display for paypal account only -->
		<div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_paypal_information" class="ym-grid input-item" style="display: none; text-align: center;margin: 30px 0px;">
		    If you chose to pay us manually, you will have to make a deposit payment of at least 10 EUR into your account. You can do this by bank transfer or PayPal.
            <br/>
            Your account will be able to receive incoming items immediately. However your activity will be limited if your account runs into a negative balance.
            <div style="width: 514px; margin-left:  -14px;color:grey;margin-top: 8px;">
                <?php if($isEnterpriseCustomer){?>
                <strong>* please note, that your enterprise account will temporarily stop working for all your users, if the account has no deposit anymore</strong>
                <?php } else{?>
                <strong>* Please note that your account can only be re-activated, if all open balance is paid</strong>
                <?php }?>
            </div>
		</div>
		<div id="traddEditPaymentMethod_paypal_account" class="ym-grid input-item" style="display: none">
			<div class="ym-gl ym-g33 register_label">
				<label >Paypal Account: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
			    <input type="text" id="addEditPaymentMethod_paypal_account" name="paypal_account"
				value="<?php if (!empty($payment)) {echo $payment->card_number;}?>"
				class="input-txt-none" maxlength=255 />
			</div>
		</div>
		
        <div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_card_number" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >Card Number: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
			    <input type="text" id="addEditPaymentMethod_card_number" name="card_number"
				value="<?php if (!empty($payment)) {echo $payment->card_number;}?>"
				class="input-txt-none" maxlength=255 />
			</div>
		</div>
        <div class="ym-clearfix"></div>
        <div id="traddEditPaymentMethod_card_type" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >Card Type: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
			    <select id="addEditPaymentMethod_card_type" name="card_type" class="input-txt-none" style="line-height: 24px;">
    			    <option value="V" <?php if (!empty($payment) && $payment->card_type == 'V') {?>selected="selected" <?php }?>>VISA</option>
    			    <option value="M" <?php if (!empty($payment) && $payment->card_type == 'M') {?>selected="selected" <?php }?>>MasterCard</option>
    			    <option value="J" <?php if (!empty($payment) && $payment->card_type == 'J') {?>selected="selected" <?php }?>>JCB</option>
    			</select>
			</div>
		</div>
        <div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_card_name" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >Name of Cardholder: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
                <?php
                $cardname = '';
                if(!empty($payment)){
                    $cardname = $payment->card_name;
                }else if(!empty($invoice_address)){
                    $cardname = $invoice_address->invoicing_address_name;
                }
                ?>
			    <input type="text" id="addEditPaymentMethod_card_name" name="card_name"
				value="<?php echo $cardname;?>"
				class="input-txt-none" maxlength=255 />
			</div>
		</div>
		<div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_expired" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >Expiration date: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
			    Year
			    <select id="addEditPaymentMethod_expired_year" name="expired_year" class="input-txt-none" style="line-height: 24px; width: 75px">
    			    <?php $cur_date = now();?>
    			    <?php for ($i = $cur_date; $i < $cur_date + 20 * (365 * 24 * 60 * 60); $i = $i + (365 * 24 * 60 * 60)) {
    			        $val_year = date('y', $i);
    			    ?>
    			    <option value="<?php echo $val_year?>" <?php if (!empty($payment) && $val_year == $payment->expired_year) {?> selected="selected" <?php }?>><?php echo date('Y', $i);?></option>
    			    <?php }?>
    			</select>
    			Month
    			<select id="addEditPaymentMethod_expired_month" name="expired_month" class="input-txt-none" style="line-height: 24px; width: 75px">
    			    <?php for ($i = 1; $i < 13; $i++) {
    			        $val_temp = $i;
    			        if ($i < 10) {$val_temp = '0'.$i;}
    			    ?>
    			    <option value="<?php echo $val_temp;?>" <?php if (!empty($payment) && $val_temp == $payment->expired_month) {?> selected="selected" <?php }?> ><?php echo $i?></option>
    			    <?php }?>
    			</select>
			</div>
		</div>
		<div class="ym-clearfix"></div>
		<div id="traddEditPaymentMethod_cvc" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >CVC/CVV: <span class="required">*</span></label>
			</div>
			<div class="ym-gl ">
			   <input type="text" id="addEditPaymentMethod_cvc" name="cvc"
				value="<?php if (!empty($payment)) {echo $payment->cvc;}?>" style="width: 100px"
				class="input-txt-none" maxlength=4 />
			</div>
		</div>
		<div class="ym-clearfix"></div>
        <br />
		<div id="traddEditPaymentMethod_trustwareimage" class="ym-grid input-item">
			<div class="ym-gl ym-g33 register_label">
				<label >&nbsp;</label>
			</div>
            <div class="ym-gl ym-g33" style="width: 30%">
               <img id="trustwaveSealImage" src="https://sealserver.trustwave.com/seal_image.php?customerId=9b197218d48f4172a58c080d0e223214&size=105x54&style=invert" border="0" style="cursor:pointer;margin-top: 5px;" oncontextmenu="javascript:alert('Copying Prohibited by Law - Trusted Commerce is a Service Mark of TrustWave Holdings, Inc.'); return false;" alt="This site protected by Trustwave's Trusted Commerce program" title="This site protected by Trustwave's Trusted Commerce program" />
            </div>
            <div class="ym-gr ym-g33" style="width: 35%; margin-top: 10px;">
                This Trustwave seal certifies the security of our system . You can click it to validate it. 
            </div>
		</div>
    </div>
    <input type="hidden" id="addEditPaymentMethod_pseudocardpan" name="pseudocardpan" value="" />
	<input type="hidden" id="addEditPaymentMethod_truncatedcardpan" name="truncatedcardpan" value="" />
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.tipsy_tooltip').tipsy({gravity: 'nw', html: true});
	$(".tipsy_tooltip" ).each(function( index ) {
		$(this).tipsy("show");
	});
	setTimeout(function() {
		$(".tipsy_tooltip" ).each(function( index ) {
			$(this).tipsy("hide");
		});
	},2000);

    // Account type change (20: Paypal | 30: Credit card)
	$('#addEditPaymentMethod_account_type').change(function() {
	    var accountType = $('#addEditPaymentMethod_account_type').val();
	    if (accountType == '10') {
		    $('#traddEditPaymentMethod_paypal_information').show();
	        $('#traddEditPaymentMethod_paypal_account').hide();
	        $('#traddEditPaymentMethod_card_type').hide();
	        $('#traddEditPaymentMethod_card_number').hide();
	        $('#traddEditPaymentMethod_card_name').hide();
	        $('#traddEditPaymentMethod_expired').hide();
	        $('#traddEditPaymentMethod_cvc').hide();
	        
		} else if (accountType == '20') {
			$('#traddEditPaymentMethod_paypal_information').hide();
	        $('#traddEditPaymentMethod_paypal_account').show();
	        $('#traddEditPaymentMethod_card_type').hide();
	        $('#traddEditPaymentMethod_card_number').hide();
	        $('#traddEditPaymentMethod_card_name').hide();
	        $('#traddEditPaymentMethod_expired').hide();
	        $('#traddEditPaymentMethod_cvc').hide();
	        
		} else if (accountType == '30') {
			$('#traddEditPaymentMethod_paypal_information').hide();
			$('#traddEditPaymentMethod_paypal_account').hide();
			$('#traddEditPaymentMethod_card_type').show();
	        $('#traddEditPaymentMethod_card_number').show();
	        $('#traddEditPaymentMethod_card_name').show();
	        $('#traddEditPaymentMethod_expired').show();
	        $('#traddEditPaymentMethod_cvc').show();
		}
	});
	
	$('#trustwaveSealImage').live('click', function(){
		javascript:window.open('https://sealserver.trustwave.com/cert.php?customerId=9b197218d48f4172a58c080d0e223214&size=105x54&style=invert', 'c_TW', 'location=no, toolbar=no, resizable=yes, scrollbars=yes, directories=no, status=no, width=615, height=720'); 
        return false;
	});
    
    $("#addEditPaymentMethod_card_number").bind('change', function(){
        var number = $.trim($(this).val());
        var card_type = GetCardType(number);

        if(card_type == 'Visa'){
            $("#addEditPaymentMethod_card_type").val("V");
        }else if(card_type == 'Mastercard'){
            $("#addEditPaymentMethod_card_type").val("M");
        } else if(card_type == 'JCB'){
            $("#addEditPaymentMethod_card_type").val("J");
        }
    });
    
    /**
     * Gets card type.
     * 
     * @param {type} number
     * @returns {String}
     */
    function GetCardType(number) {
        // visa
        var re = new RegExp("^4");
        if (number.match(re) != null)
            return "Visa";

        // Mastercard
        re = new RegExp("^5[1-5]");
        if (number.match(re) != null)
            return "Mastercard";

        // AMEX
        re = new RegExp("^3[47]");
        if (number.match(re) != null)
            return "AMEX";

        // Discover
        re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
        if (number.match(re) != null)
            return "Discover";

        // Diners
        re = new RegExp("^36");
        if (number.match(re) != null)
            return "Diners";

        // Diners - Carte Blanche
        re = new RegExp("^30[0-5]");
        if (number.match(re) != null)
            return "Diners - Carte Blanche";

        // JCB
        re = new RegExp("^35(2[89]|[3-8][0-9])");
        if (number.match(re) != null)
            return "JCB";

        // Visa Electron
        re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
        if (number.match(re) != null)
            return "Visa Electron";

        return "";
    }

});
</script>
