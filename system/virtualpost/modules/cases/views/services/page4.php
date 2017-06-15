<?php
    $customer_id = APContext::getCustomerCodeLoggedIn();
    $vat_obj = APUtils::getVatRateOfCustomer($customer_id);
    $vat = $vat_obj->rate;
?>

<div class="ym-grid content services"  id="case-body-wrapper">
	<div id="go-back"><span><a id="backButton" href="#" ><?php language_e('cases_view_services_page4_Back'); ?></a></span></div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="header">
		<h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_services_page4_DeutscheBankAGClevverBusinessA'); ?></h2>
	</div>
	<div class="ym-clearfix"></div>
	<div class="ym-grid">
	    <table  style="width: 100%">
	        <tr>
	            <th style="width: 80%">&nbsp;</th>
	            <th style="width: 19%">Onetime charge:</th>
	        </tr>
	        <tr>
	            <td>
	                <input type="checkbox" name="check1" checked="checked" disabled="disabled" /> <span>Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum
					Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum</span>
	            </td>
	            <td>495,00 EUR</td>
	        </tr>
	    </table>
	</div>
	<div class="ym-clearfix"></div>
	<br />
	<div class="ym-grid">
	    <table style="width: 100%">
	        <tr>
	            <th style="width: 80%"><?php language_e('cases_view_services_page4_OptionalServices'); ?>:</th>
	            <th style="width: 19%"><?php language_e('cases_view_services_page4_OneTimeCharge'); ?>:</th>
	        </tr>
	        <tr>
	            <td>
	                <input type="checkbox" id="checkOPtion1" class="option_service_check" data-price="495" name="check1" /> <span>Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum
					Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum</span>
	            </td>
	            <td>495,00 EUR</td>
	        </tr>
	        <tr>
	            <td>
	                <input type="checkbox" id="checkOPtion2" class="option_service_check" data-price="495" name="check1" /> <span>Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum
					Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum</span>
	            </td>
	            <td>495,00 EUR</td>
	        </tr>
	        <tr>
	            <td>
	                <input type="checkbox" id="checkOPtion3" class="option_service_check" data-price="495" name="check1" /> <span>Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum
					Ipsum Dolorum, Lorum Ipsum Dolorum, Lorum Ipsum Dolorum</span>
	            </td>
	            <td>495,00 EUR</td>
	        </tr>
	    </table>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid subtotal">
		<div class="ym-gl ym-g80">
			<div class="description ym-gr"><?php language_e('cases_view_services_page4_Subtotal'); ?>:</div>
		</div>
		<div class="ym-gr ym-g20 ">
			<div class="description" id="subTotal">495,00 EUR</div>
		</div>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid subtotal">
		<div class="ym-gl ym-g80">
			<div class="description  ym-gr">
				<?php language_e('cases_view_services_page4_SpanChangeVATSettingsegEUCompa'); ?>:
			</div>
		</div>
		<div class="ym-gr ym-g20 ">
			<div class="description" id="vat">94,05 EUR</div>
		</div>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid subtotal">
		<div class="ym-gl ym-g80">
			<div class="description  ym-gr"><?php language_e('cases_view_services_page4_Total'); ?>:</div>
		</div>
		<div class="ym-gr ym-g20 ">
			<div class="description" id="total">585,05 EUR</div>
		</div>
	</div>

	<br />
	<div class="ym-grid">
		<input type="checkbox"> <?php language_e('cases_view_services_page4_IConfirmDataPrivacyRegulations'); ?>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid">
		<input type="checkbox"> <?php language_e('cases_view_services_page4_IHaveReadAndUnderstoodTheTerms'); ?>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid">
		<div class="ym-gl ym-g70">&nbsp;</div>
		<div class="ym-gr ym-g30">
			<a class="input-btn" id="paymentButton" href="#"><?php language_e('cases_view_services_page4_ContinueWithPayment'); ?></a>
		</div>
	</div>
</div>

<div style="display: none">
    <div id="divPaymentBoxWindow" title="<?php language_e('cases_view_services_page4_Payment'); ?>" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$("#backButton").click(function(){
	history.back(-1);
	return  false;
});

var vat_rate = <?php echo $vat?>;
var totalPrice = 0;
var totalGrossPrice = 0;
var vatPrice = 0;
// When change
$('.option_service_check').live('change', function(){
	totalPrice = 495;
	$('input:checkbox.option_service_check').each(function () {
	    var thisPrice = (this.checked ? $(this).data('price') : 0);
	    totalPrice += thisPrice;
	});
	vatPrice = totalPrice * vat_rate;
	totalGrossPrice = totalPrice + vatPrice;
	$('#subTotal').html(totalPrice.toFixed(2).replace('.', ',') + ' EUR');
	$('#vat').html(vatPrice.toFixed(2).replace('.', ',') + ' EUR');
	$('#total').html(totalGrossPrice.toFixed(2).replace('.', ',') + ' EUR');
});

/*
 * payment button click.
 */
$("#paymentButton").click(function(e){
	e.preventDefault();

	// Clear control of all dialog form
    $('.dialog-form').html('');

    // get total
    var total = $("#total").html();
    total = total.replace('EUR','');
    total = encodeURI(total);
    var url = "<?php echo base_url() ?>cases/services/payment?total=" + total;

    // Open new dialog
    $('#divPaymentBoxWindow').openDialog({
        autoOpen: false,
        height: 200,
        width: 600,
        modal: true,
        open: function() {
            $(this).load(url, function() {

            });
        },
        buttons: {

        }
    });
    $('#divPaymentBoxWindow').dialog('option', 'position', 'center');
    $('#divPaymentBoxWindow').dialog('open');
});


</script>