<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">Paypal IPN Manual</h2>
</div>
<div class="ym-grid mailbox">
    <form id="paypalIPNManualForm" action="<?php echo base_url()?>payment/payment_paypal_ipn" method="post">
        <div class="ym-g70 ym-gl">
        	<div class="ym-grid input-item">
        	    <div class="ym-g20 ym-gl"><label>Paypal Transaction ID:</label></div>
        	    <div class="ym-g40 ym-gl">
                    <input type="text" id="paypalIPNManualForm_txn_id" name="txn_id" style="width: 250px"
                                value="" class="input-txt" maxlength=255 />
                </div>
        	</div>
        </div>
        <div class="ym-g70 ym-gl">
        	<div class="ym-grid input-item">
        	    <div class="ym-g20 ym-gl"><label>Paypal Payment Status:</label></div>
        	    <div class="ym-g40 ym-gl">
                    <input type="text" id="paypalIPNManualForm_payment_status" name="payment_status" style="width: 250px"
                                value="Completed" class="input-txt" maxlength=255 />
                     <input type="hidden" id="paypalIPNManualForm_payment_manual" name="payment_manual" style="width: 250px"
                                value="1" class="input-txt" maxlength=255 />
                    <button id="submitPaypalManual" class="admin-button">Submit</button>
                </div>
        	</div>
        </div>
	</form>
</div>
<script type="text/javascript">
$(document).ready( function() {
	$('button').button();
});
</script>