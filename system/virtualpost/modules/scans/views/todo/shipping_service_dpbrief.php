<!-- Deutsche Post -->
<div class="ym-gl" style="width: 130px">
    <label>Include e-stamp:</label>
</div>
<div class="ym-gl" style="width: 320px">
    <input type="checkbox" id="shippingEnvelopeForm_include_estamp" name="include_estamp" value="1" class="customCheckbox"/>
</div>
<div class="ym-clearfix"></div>
<div id="estamp_container" class="ym-gl hide" style="width: 440px; height:117px;overflow: hidden; text-align: center; border: 1px solid #D5D5D5; margin-top: 5px;">
    <img id="shippingEnvelopeForm_include_estamp_img" style="text-align: center; width: 120%; margin-left: -130px; margin-top: -10px" src="<?php if (!empty($estamp_link)) {
    echo $estamp_link;
} ?>" />
</div>
<div class="ym-clearfix"></div>
<div class="ym-gl" style="width: 350px; text-align: left;">
    <label>Select package/letter size:</label>
</div>
<div class="ym-gl" style="width: 350px">
    <select name="package_letter_size" id="package_letter_size" class="input-width" style="width: 300px">
        <?php foreach ($ppl as $item) { ?>
            <option value="<?php echo $item[0] ?>" data-package-size="<?php echo $item[2] ?>" data-package-price="<?php echo $item[3] ?>"><?php echo $item[1] ?></option>
<?php } ?>
    </select>
</div>

<!-- Select other package -->
<div class="ym-clearfix"></div>
<div class="ym-grid input-item" style="margin-top: 10px;">
    <div class="ym-gl" style="width: 30px">
        <input type="checkbox" id="shippingEnvelopeForm_other_package_price_flag" name="other_package_price_flag" value="1" class="customCheckbox"/>
    </div>
    <div class="ym-gl" style="width:440px">
        Other shipping fee: 
        <input class="input-txt-none" name="other_package_price_fee" id="shippingEnvelopeForm_other_package_price_fee" 
                                   type="text" style="width: 55px" value="" /> 
        <?php echo $currency_short ?> ;Cost (inc. <span id="shippingDisplayVAT"><?php echo APUtils::getVatRateOfCustomer($customer_id)->rate * 100; ?></span>% VAT): <input class="input-txt-none" name="cost_for_customer" id="shippingEnvelopeForm_cost_for_customer" 
                                                                                                                                              type="text" style="width: 55px" value="" disabled="disabled" /> <?php echo $currency_short ?>
    </div>
</div>
<div class="ym-grid input-item" style="margin-top: 10px;">
    <div class="ym-gl" style="width:450px">
        Customs process necessary: <?php if ($is_pending_declare_customs) {
    echo 'Yes';
} else {
    echo 'No';
} ?>
<?php if ($is_pending_declare_customs) { ?>
    Profoma Invoice: 
    <a href="#" id="shippingEnvelopeForm_profoma_invoice_open" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Open</a>
    <a href="#" id="shippingEnvelopeForm_profoma_invoice_print" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Print</a>
<?php } ?>
    </div>
</div>
<!-- Start footer -->
<div class="ym-clearfix"></div>
<div class="ym-gl" style="margin-top: 0px">
    <table class="shipping_table" style="width: 100%; margin-top: 1em;">
        <tr>
            <td>
                Select label size:
            </td>
            <td style="text-align: right">
                <button id="createPreviewStampButton" style="width: 200px">Create preview of stamp</button>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo code_master_form_dropdown(array(
                    "code" => APConstants::SHIPPING_TYPE,
                    "value" => '',
                    "name" => 'lable_size',
                    "id"    => 'lable_size',
                    "clazz" => 'input-width',
                    "style" => 'width: 200px',
                    "has_empty" => false
                ));?>
            </td>
            <td style="text-align: right">
                <button id="buyStampButton" style="width: 200px">Buy stamp</button>
            </td>
        </tr>
    </table>
</div>

<input class="input-txt-none" name="handling_charge" id="shippingEnvelopeForm_handling_charge" 
       type="hidden" style="width: 60px" value="" />
<input class="input-txt-none" name="handling_charge_gross" id="shippingEnvelopeForm_handling_charge_gross" 
       type="hidden" style="width: 60px" value="" />

<!-- End footer -->
<div class="hide">
    <div id="inputParcelsInfo" title="Enter your individual parcel information here:" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
    $('button').button();
    $('#buyStampButton').attr("disabled", "disabled");
    $('#createPreviewStampButton').attr("disabled", "disabled");
    $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
    $("#shippingEnvelopeForm_other_package_price_fee").attr("disabled", "disabled");
</script>