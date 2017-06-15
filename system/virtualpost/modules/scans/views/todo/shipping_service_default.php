<!-- Default -->
<style type="text/css">
.readonly{
    background: #f2f2f2;
}
</style>
<div class="ym-clearfix"></div>
<div class="ym-grid input-item" style="margin-top: 10px;">
    <table class="shipping_table" style="width: 100%;margin-top: 5px;">
        <tr>
            <td>VAT for this customer</td>
            <td style="width: 65px">
                <input class="input-txt-none readonly" name="vat_rate" id="shippingEnvelopeForm_vat_rate" 
                       type="text" style="width: 58px" readonly="readonly"
                       value="<?php echo APUtils::getVatRateOfCustomer($customer_id)->rate * 100; ?>%" />
            </td>
            <td></td>
        </tr>
        <tr>
            <td>Customs process necessary:</td>
            <td style="width: 65px">
                <select id="shippingEnvelopeForm_customs_process_flag" class="input-width" style="width: 60px" name="customs_process_flag">
                    <option value="1" <?php if ($is_pending_declare_customs) { ?>selected="selected" <?php }?>>Yes</option>
                    <option value="0" <?php if (!$is_pending_declare_customs) { ?>selected="selected" <?php }?>>No</option>
                </select>
            </td>
            <td>
                <div id="shippingEnvelopeForm_profoma_invoice_container" 
                     <?php if (!$is_pending_declare_customs) { ?> style="display: none"<?php }?>>
                    Profoma Invoice: 
                    <a href="#" id="shippingEnvelopeForm_profoma_invoice_open" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Edit</a>
                    <a href="#" id="shippingEnvelopeForm_profoma_invoice_print" data-id="<?php echo $envelope_id; ?>" style="text-decoration: underline; color: #0000FF">Print</a>
               </div>
            </td>
        </tr>
    </table>
    <table class="shipping_table" style="width: 100%;margin-top: 5px;">
        <tr>
            <td></td>
            <td>Net</td>
            <td>Gross</td>
            <td></td>
        </tr>
        <tr>
            <td>
                Charge for customs process:
            </td>
            <td>
                <input class="input-txt-none  readonly" name="charge_customs_process" id="shippingEnvelopeForm_charge_customs_process" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>
            </td>
            <td>
                <input class="input-txt-none readonly" name="charge_customs_process_gross" id="shippingEnvelopeForm_charge_customs_process_gross" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>                
            </td>
            <td>
                <?php echo $currency_short ?>
            </td>
        </tr>
        <tr>
            <td>
                Charge for special service:
            </td>
            <td>
                <input class="input-txt-none" name="special_service_fee" id="shippingEnvelopeForm_special_service_fee" 
                       type="text" style="width: 60px; background: #d3f5d9" value="" />
            </td>
            <td>
                <input class="input-txt-none readonly" name="special_service_fee_gross" id="shippingEnvelopeForm_special_service_fee_gross" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>                
            </td>
            <td>
                <?php echo $currency_short ?>
            </td>
        </tr>
        <tr>
            <td>
                Charge for shipment:
            </td>
            <td>
                <input class="input-txt-none" name="other_package_price_fee" id="shippingEnvelopeForm_other_package_price_fee" 
                       type="text" style="width: 60px; background: #d3f5d9" value="" />
            </td>
            <td>
                <input class="input-txt-none readonly" name="other_package_price_fee_gross" id="shippingEnvelopeForm_other_package_price_fee_gross" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>                
            </td>
            <td>
                <?php echo $currency_short ?>
            </td>
        </tr>
        <tr>
            <td>
                Handling charge:
            </td>
            <td>
                <input class="input-txt-none  readonly" name="handling_charge" id="shippingEnvelopeForm_handling_charge" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>
            </td>
            <td>
                <input class="input-txt-none readonly" name="handling_charge_gross" id="shippingEnvelopeForm_handling_charge_gross" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>                
            </td>
            <td>
                <?php echo $currency_short ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"><hr style="border:1px none; margin-top:15px;padding: 0px; background: #0078a3" /></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">
                Total charge to the customer:
            </td>
            <td>
                <input class="input-txt-none readonly" name="total_shipment_charge" id="shippingEnvelopeForm_total_shipment_charge" 
                       type="text" style="width: 60px" value="" readonly="readonly" />
            </td>
            <td>
                <input class="input-txt-none readonly" name="total_shipment_charge_gross" id="shippingEnvelopeForm_total_shipment_charge_gross" 
                       type="text" style="width: 60px" value="" readonly="readonly"/>                
            </td>
            <td>
                <?php echo $currency_short ?>
            </td>
        </tr>
    </table>
</div>
<!-- End footer -->
<script type="text/javascript">
    $('button').button();
    PrepareShipping.changeCustomsDropdown();
    PrepareShipping.getShippingCost();
</script>