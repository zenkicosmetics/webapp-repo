<h2 style="font-size: 20px; text-align: center; line-height: 35px">
	Thank you for completing the setup process.</h2>
<div style="text-align: center; margin-top: 5px;margin-bottom: 5px;">
	<p>You can receive mail now at the following address:</p>
    <div id="thankingWindow_addressDivContainer" style="text-align: left; margin-left: 250px; margin-top: 10px;">
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
<div style="text-align: center;">
    <?php $isEnterpriseCustomer = APContext::isPrimaryCustomerUser();?>
    <?php if($isEnterpriseCustomer){?>
        <h2 style="font-size: 16px; text-align: center; line-height: 30px">You can now start to setup your enterprise account and all your users under "My Account" </h2>
        <br />
        <p>Please make sure that every postbox and phone number of your users is verified to enable scanning and forwarding.</p>
    <?php }else { ?>
        <div style="width: 100%; height: 1px; background-color: #eee; margin: auto 0px; margin-top: 10px; margin-bottom: 10px" ></div>
        <p>Please make sure to verify your postbox to enable scanning and forwarding</p>
        
    <?php }?>
    <br /><br />
    <div style="text-align: center"><a href="#" id="thankingWindow_make_deposit_payment" style="color: blue">Make deposit payment into account...</a></div>
</div>

<div style='display:none'>
    <div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#display_payment_confirm').fancybox({
        width: 500,
        height: 300
    });
        
    $('#thankingWindow_make_deposit_payment').live('click', function(e) {
        e.preventDefault();
        
        // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice').openDialog({
            autoOpen: false,
            height: 400,
            width: 720,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice", function() {});
            },
            buttons: {
                'Submit': function () {
                    saveDirectChargeWithoutInvoice();
                }
            }
        });
        $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice').dialog('open');
        
        return false;
    });
    
    /**
    * Save direct charge without invoice
    */
    function saveDirectChargeWithoutInvoice() {
        var submitUrl = "<?php echo base_url() ?>customers/save_direct_charge_without_invoice";
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'createDirectChargeWithoutInvoiceForm',
            success: function(data) {
                if (data.status) {
                    if (data.redirect) {
                    var submitUrl = data.message;
                        $('#display_payment_confirm').attr('href', submitUrl);
                        $('#display_payment_confirm').click();
                    } else {
                        $('#createDirectChargeWithoutInvoice').dialog('close');
                        $.displayInfor(data.message, null,  function() {
                        });
                    }
                } else {
                        $.displayError(data.message);
                }
            }
        });
    }
});
</script>
