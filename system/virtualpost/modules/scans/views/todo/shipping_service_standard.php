<form id="shippingCalculatorForm" action="<?php echo base_url(); ?>scans/todo/shipping_calculator" method="post">
    <?php if(isset($error_message) && !empty($error_message)){?>
    <div style="color:red;font-style: bold;"><?php echo $error_message; ?></div>
    <?php }?>
    <table class="shipping_table" style="width: 100%; background-color: #dadada; margin-top: 0em">
        <tr>
            <td>
                Number of parcels:
                <input class="input-txt-none" name="number_of_parcels" id="shippingEnvelopeForm_number_of_parcels" 
                       type="text" style="width: 50px;margin-right: 7px;margin-left: 13px;" value="<?php echo $number_of_parcels; ?>" />

                Customs/Insurance value (EUR):
                <input class="input-txt-none" name="customs_insurance_value" id="shippingEnvelopeForm_customs_insurance_value" 
                       type="text" style="width: 60px" value="<?php if (!empty($total_customs_cost)) {  echo $total_customs_cost; }?>" />
            </td>
        </tr>
        <tr>
            <td>
                Length/Width/Height:
                <input class="input-txt-none" name="length" id="length" 
                       type="text" style="width: 50px" value="<?php if (!empty($dimension_l)) { echo $dimension_l; } ?>" />
                <input class="input-txt-none" name="width" id="width" 
                       type="text" style="width: 50px" value="<?php if (!empty($dimension_w)) { echo $dimension_w; }?>" />
                <input class="input-txt-none" name="height" id="height" 
                       type="text" style="width: 50px; margin-right: 38px;" value="<?php if (!empty($dimension_h)) { echo $dimension_h; }?>" />
                Weight:
                <input class="input-txt-none" name="weight" id="shippingEnvelopeForm_weight" 
                       type="text" style="width: 60px" value="<?php if (!empty($dimension_we)) { echo $dimension_we; } ?>" /> <?php echo $weight_unit;?>
                <a href="#" id="shippingEnvelopeForm_edit_parcels" style="text-decoration: underline; color: #0000FF">Edit</a>
            </td>
        </tr>

    </table>
    <table class="shipping_table" style="width: 100%;margin-top: 5px;">
        <tr>
            <td>
                <input type="checkbox" id="shippingEnvelopeForm_order_pickup_checkbox" />
                Order Pickup
            </td>
            <td style="text-align: right" colspan="4">
                <button id="resetButton" style="width: 100px">Reset</button>
                <button id="calculateButton" style="width: 120px">Recalculate</button>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                Complete charge that has been approved by the customers:
            </td>
            <td style="width: 60px">
                <input class="input-txt-none" name="charge_fee_approved" id="shippingEnvelopeForm_charge_fee_approved" 
                       type="text" style="width: 60px" value="<?php if (!empty($shipping_fee) && $shipping_fee > 0) { echo APUtils::number_format($shipping_fee); }?>" disabled="disabled" />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                Charge for customs process:
            </td>
            <td style="width: 60px">
                <input class="input-txt-none" name="charge_customs_process" id="shippingEnvelopeForm_charge_customs_process" 
                       type="text" style="width: 60px" value="" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px; vertical-align: middle">
                Postal Charge
            </td>
            <td style="width: 60px;vertical-align: middle">
                <input class="input-txt-none" name="postal_charge" id="shippingEnvelopeForm_postal_charge" 
                       type="text" style="width: 50px" value="<?php if (!empty($postal_charge) && $postal_charge > 0) { echo APUtils::number_format($postal_charge); }?>" />
                <input type="hidden" id="shippingEnvelopeForm_customs_handling" name="customs_handling" class="input-width" value="<?php echo APUtils::number_format($customs_handling); ?>"/>
            </td>
            <td colspan="2" style="vertical-align: middle">
                <?php $customer_vat_rate = APUtils::getVatRateOfCustomer($customer_id)->rate; ?>
                Customer Charge (inc. <span id="shippingDisplayVAT"><?php echo $customer_vat_rate * 100; ?></span>% VAT):
            </td>
            <td style="vertical-align: middle">
                <input class="input-txt-none" name="cost_for_customer_charge" id="shippingEnvelopeForm_cost_for_customer_charge" 
                       type="text" style="width: 60px" value="<?php if (!empty($shipping_fee) && $shipping_fee > 0) { echo APUtils::number_format($shipping_fee); }?>" disabled="disabled" />
            </td>
        </tr>
        <tr>
            <td colspan="3" style="vertical-align: middle">
                Customs process necessary:
                <select id="shippingEnvelopeForm_customs_process_flag" class="input-width" style="width: 60px" name="customs_process_flag">
                    <option value="1" <?php if ($is_pending_declare_customs) { ?>selected="selected" <?php }?>>Yes</option>
                    <option value="0" <?php if (!$is_pending_declare_customs) { ?>selected="selected" <?php }?>>No</option>
                </select>
                
            </td>
            <td colspan="2" style="vertical-align: middle">
                <div id="shippingEnvelopeForm_profoma_invoice_container" 
                     <?php if (!$is_pending_declare_customs) { ?> style="display: none"<?php }?>>
                    Profoma Invoice: 
                    <a href="#" id="shippingEnvelopeForm_profoma_invoice_open" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Edit</a>
                    <a href="#" id="shippingEnvelopeForm_profoma_invoice_print" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Print</a>
               </div>
            </td>
        </tr>
    </table>
    <input type="hidden" id="multiple_quantity" name="multiple_quantity" class="input-width" value=""/>
    <input type="hidden" id="multiple_number_shipment" name="multiple_number_shipment" class="input-width" value=""/>
    <input type="hidden" id="multiple_length" name="multiple_length" class="input-width" value=""/>
    <input type="hidden" id="multiple_width" name="multiple_width" class="input-width" value=""/>
    <input type="hidden" id="multiple_height" name="multiple_height" class="input-width" value="" />
    <input type="hidden" id="multiple_weight" name="multiple_weight" class="input-width" value=""/>
    <input type="hidden" id="volumn_weight" name="volumn_weight" class="input-width" value="<?php echo round($volumn_weight, 0);?>"/>
    
    <input type="hidden" id="shippingCalculatorForm_envelope_id" name="envelope_id" class="input-width" value="<?php echo $envelope_id;?>"/>
    <input type="hidden" id="shippingCalculatorForm_customer_id" name="customer_id" class="input-width" value="<?php echo $customer_id;?>"/>
    <input type="hidden" id="shippingCalculatorForm_shipment_type_id" name="shipment_type_id" class="input-width" value="<?php echo $shipment_type_id;?>"/>
    <input type="hidden" id="shippingCalculatorForm_shipment_service_id" name="shipment_service_id" class="input-width" value="<?php echo $shipment_service_id;?>"/>
    
    <input type="hidden" id="shippingCalculatorForm_shipment_street" name="shipment_street" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_postcode" name="shipment_postcode" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_city" name="shipment_city" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_region" name="shipment_region" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_country" name="shipment_country" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_address_name" name="shipment_address_name" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_company" name="shipment_company" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipment_phone_number" name="shipment_phone_number" class="input-width" value=""/>
    
    <input type="hidden" id="shippingCalculatorForm_charge_fee_approved_hidden" name="" class="input-width" value="<?php echo $shipping_fee;?>"/>
    <input type="hidden" id="shippingCalculatorForm_total_not_include_customs_handing" name="" class="input-width" value=""/>
    
    <input class="input-txt-none" name="handling_charge" id="shippingEnvelopeForm_handling_charge" type="hidden" value="" />
    <input class="input-txt-none" name="handling_charge_gross" id="shippingEnvelopeForm_handling_charge_gross" type="hidden" value="" />
    <input type="hidden" id="shippingCalculatorForm_shipping_api_id" name="shipping_api_id" class="input-width" value=""/>
    <input type="hidden" id="shippingCalculatorForm_shipping_credential_id" name="shipping_credential_id" class="input-width" value=""/>

    <div class="hide">
        <div id="inputParcelsInfo" title="Enter the parcel information for the outgoing shipment here" class="input-form dialog-form"></div>
    </div>
</form>
<?php if (!empty($shipping_service)) { ?>
<div class="ym-gl" style="margin-top: 0px">
    <table class="shipping_table" style="width: 100%; margin-top: 0em;">
        <tr>
            <td>
                Select label size:
            </td>
            <td style="text-align: right">
                
            </td>
        </tr>
        <tr>
            <td style="text-align: right">
               <div id="lable_size_dropdown_list">
                   <!-- Build by ajax after call shipping api -->
                </div>
            </td>
            <td style="text-align: right; padding-left: 20px;">
                <button id="createPreviewLabelButton" style="width: 200px">Create preview of label</button>
            </td>
        </tr>
    </table>
</div>
<?php } ?>
<script src="<?php echo APContext::getAssetPath(); ?>system/virtualpost/modules/scans/js/ShippingCalculator.js"></script>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    $('#buyStampButton').attr("disabled", "disabled");
    $('#createPreviewStampButton').attr("disabled", "disabled");
    $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
    
    var over_weight_error_message = '<?php echo lang('over_weight_error_message') ?>';
    
    ShippingCalculator.init('<?php echo base_url(); ?>', '.', 'g');
    sessionStorage.setItem(ShippingCalculator.sessionStorageItemKey, JSON.stringify([]));
    var parcels = [], parcel = null;
    var quantity = 0, weight = 0, length = 0, width = 0, height = 0;
    <?php 
    $number_shipment = 0;
    if (!empty($number_collect_shippment) && count($number_collect_shippment) > 0 && $shipment_type_id == '4') {
        foreach($number_collect_shippment as $index => $item) {
            $number_shipment++;
    ?>
        quantity = 1;
        weight = <?php if (!empty($item['Weight'])) {echo $item['Weight']; } else {echo '0';}?>;
        number_shipment = <?php if (!empty($item['NumberShipment'])) {echo $item['NumberShipment']; } else {echo $number_shipment;}?>;
        length = <?php if (!empty($item['Length'])) {echo $item['Length']; } else {echo '0';}?>;
        width = <?php if (!empty($item['Width'])) {echo $item['Width']; } else {echo '0';}?>;
        height = <?php if (!empty($item['Height'])) {echo $item['Height']; } else {echo '0';}?>;
        parcel = {quantity: quantity, number_shipment: number_shipment, weight: weight, length: length, width: width, height: height};
        parcels.push(parcel);
        <?php } ?>
    <?php } ?>
    sessionStorage.setItem(ShippingCalculator.sessionStorageItemKey, JSON.stringify(parcels));
    PrepareShipping.changeCustomsDropdown();
    if (parcels.length > 0) {
        ShippingCalculator.apply(parcels);
    } else {
        // Trigger recalculate
        console.log('trigger recalculate shipping cost');
        $('#calculateButton').click();
    }
});
</script>