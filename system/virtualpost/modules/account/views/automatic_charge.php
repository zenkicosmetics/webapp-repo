<form id="saveAutomaticChargeSettingForm" action="<?php echo base_url() . 'account/setting/automatic_charge_account_setting'; ?>" method="post" >
    <?php if($is_valid_payment_method){?>
        <div class="ym-grid">
            <div class="ym-gl">
                <h4 style="<?php if($this->module == 'payment'){ ?>font-weight: bold; <?php }?>" class="COLOR_063">Payment automation</h4>
            </div>
            <div class="ym-gr">
                <?php 
                $CUSTOMER_AUTOMATIC_CHARGE_SETTING = AccountSetting::get($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING);
                $automaticChargeSettingCLass = "btn-grey";
                if($CUSTOMER_AUTOMATIC_CHARGE_SETTING == 1){
                    $supportSettingClass = "input-btn btn-yellow";
                }
                ?>
            </div>
        </div>
        <div class="ym-clearfix"></div>
        <div class="ym-grid" style="padding-top: 20px">
            <input type="checkbox" <?php if (AccountSetting::get($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING) == "1") { echo "checked='checked'"; } ?>
                   class="customCheckbox" id="CUSTOMER_AUTOMATIC_CHARGE_SETTING" name="CUSTOMER_AUTOMATIC_CHARGE_SETTING" value="1" /> <label> Activate automatic deposit charges</label>
        </div>
        <div class="ym-clearfix" ></div>
        <div class="ym-grid CUSTOMER_AUTOMATIC_CHARGE_SETTING" style="padding-top: 20px">
            <label style="margin-left: 20px">If my account deposit drops below </label>
            <input type="email" style="width: 70px;" class="input-txt" name="CUSTOMER_AUTOMATIC_CHARGE_SETTING_01" id="CUSTOMER_AUTOMATIC_CHARGE_SETTING_01"
                   value="<?php echo AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING) ?>" />
            <span> EUR</span>
        </div>
        <div class="ym-clearfix"></div>
        <div class="ym-grid CUSTOMER_AUTOMATIC_CHARGE_SETTING" style="padding-top: 20px">
            <label style="margin-left: 20px">charge my credit card for the amount of </label>
            <input type="email" style="width: 70px;" class="input-txt" name="CUSTOMER_AUTOMATIC_CHARGE_SETTING_02" id="CUSTOMER_AUTOMATIC_CHARGE_SETTING_02"
                   value="<?php echo AccountSetting::get_alias03($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING) ?>" />
            <span> EUR</span>
        </div>
        <input style="margin: 1.125em 0 0 0;width: 170px;float:right" type="button" id="saveAutomaticChargeButton" class="btn-grey btn-yellow" value="Save automatic charge" />
    <?php } else if(!empty($dialog_flag)){?>
        <div class="ym-grid">
            <div class="ym-gl">
                <h4 style="font-size: 14pt;color:red;"><?php echo lang("account_setting.require_credit_card"); ?></h4>
            </div>
            <div class="ym-gr">
                <input style="margin: 1.125em 0 0 0" type="button" id="redirectPaymentButton" class="input-btn ym-gr btn-grey" value="Register new payment method" />
            </div>
        </div>
    <?php }?>
</form>
<div class="hide" style="display: none;">
<div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
<a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('#display_payment_confirm').fancybox({
        width: 500,
        height: 300
    });
    $("#saveAutomaticChargeButton").click(function(){
        var CUSTOMER_AUTOMATIC_CHARGE_SETTING = $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING").is(":checked");
        var CUSTOMER_AUTOMATIC_CHARGE_SETTING_01 = $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING_01").val();
        var CUSTOMER_AUTOMATIC_CHARGE_SETTING_02 = $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING_02").val();
        var value = $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING").is(":checked");
        if(value !== true){
            $.displayError('Please select to activate automatic deposit charges.');
            return;
        }
        $.ajaxExec({
            url: '<?php echo base_url() ?>account/setting/automatic_charge_account_setting',
            data:{
                CUSTOMER_AUTOMATIC_CHARGE_SETTING: CUSTOMER_AUTOMATIC_CHARGE_SETTING,
                CUSTOMER_AUTOMATIC_CHARGE_SETTING_01: CUSTOMER_AUTOMATIC_CHARGE_SETTING_01,
                CUSTOMER_AUTOMATIC_CHARGE_SETTING_02: CUSTOMER_AUTOMATIC_CHARGE_SETTING_02
            },
            success: function (data) {
                if (data.status) {
                    if (data.data != null && data.data.auto_deposit_flag == '1') {
                        var charge_amount = data.data.charge_amount;
                        // Show confirm dialog
                        var message = 'Your credit card now will be charged with ' + charge_amount + ' EUR, please confirm';
                        $.confirm({
                            message: message,
                            yes: function () {
                                // Open payment dialog
                                createDirectCharge(charge_amount);
                            }
                        });
                    } else {
                        $('#setupAutomaticChargeWindow').dialog('close');
                        $.displayInfor(data.message);
                    }
                } else {
                    $.displayError(data.message, '', function(){
                        location.href = "<?php echo base_url() ?>payment";
                    });
                }
            }
        });

        return false;
    });

    $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING").change(function(){
        showChargeSetting();
    });

    $("#redirectPaymentButton").click(function(){
        location.href = "<?php echo base_url() ?>payment";
    });

    showChargeSetting();
    function showChargeSetting(){
        var value = $("#CUSTOMER_AUTOMATIC_CHARGE_SETTING").is(":checked");

        if(value == 1){
            $(".CUSTOMER_AUTOMATIC_CHARGE_SETTING").show();
            //$('#saveAutomaticChargeButton').addClass('btn-yellow');
        }else{
            $(".CUSTOMER_AUTOMATIC_CHARGE_SETTING").hide();
            //$('#saveAutomaticChargeButton').removeClass('btn-yellow');
        }
    }

    /**
    * Create direct charge
    */
   function createDirectCharge(charge_amount) {
        // Clear control of all dialog form
       $('#createDirectChargeWithoutInvoice').html('');

       // Open new dialog
       $('#createDirectChargeWithoutInvoice').openDialog({
           autoOpen: false,
           height: 400,
           width: 720,
           modal: true,
           open: function() {
               $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice?prepayment=1&charge_amount=" + charge_amount, function() {});
           },
           buttons: {
               'Submit': function () {
                   saveDirectChargeWithoutInvoice();
               }
           }
       });
       $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
       $('#createDirectChargeWithoutInvoice').dialog('open');
   };

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