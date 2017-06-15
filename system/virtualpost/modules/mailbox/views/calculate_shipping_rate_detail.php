<?php 
?>
<form id="calculate_shipping_rate_detail_form" action="#" method="post">
<table>
    <tr>
        <!-- Shipping Address -->
        <td style="vertical-align: top;">
            <table class="displayInfo displayInfo02" style="height: 440px">
                <tr>
                    <th colspan="2" style="background-color: #FFF;background-image:none; text-align: left;">Your shipping address</th>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td><input id="shipment_address_name_id" class="input-txt-none" name="shipment_address_name" type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_address_name;}?>" /></td>
                </tr>
                <tr>
                    <td>Company:</td>
                    <td><input class="input-txt-none" name="shipment_company" id="shipment_company" type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_company;}?>" /></td>
                </tr>
                <tr>
                    <td>Street:</td>
                    <td><input class="input-txt-none" name="shipment_street" id="shipment_street"  type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_street;}?>" /></td>
                </tr>
                <tr>
                    <td>Post Code:</td>
                    <td><input class="input-txt-none" name="shipment_postcode" id="shipment_postcode" type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_postcode;}?>" /></td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td><input class="input-txt-none" name="shipment_city" id="shipment_city" type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_city;}?>" /></td>
                </tr>
                <tr>
                    <td>Region:</td>
                    <td><input class="input-txt-none" name="shipment_region" id="shipment_region" type="text" style="width: 300px"
    						value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_region;}?>" /></td>
                </tr>
                <tr>
                    <td>Country:</td>
                    <td><select id="shipment_country" name="shipment_country" class="input-txt-none" style="width: 300px">
				        <?php foreach ($countries as $country) {?>
				        <option value="<?php echo $country->id?>" <?php if (!empty($customer_address) && $customer_address->shipment_country == $country->id) {?> selected="selected" <?php }?>><?php echo $country->country_name?></option>
				        <?php }?>
				        </select></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="#" id="calculate_shipping_rate_detail_form_change_shipping_address" style="text-decoration: underline;">Change shipping address</a>
                    </td>
                </tr>
            </table>
        </td>
        
        <!-- Shipping Service -->
        <td style="vertical-align: top;">
            <table class="displayInfo displayInfo02" style="height: 440px">
                <tr>
                    <td colspan="2">Volume-Weight of shipment:  <?php echo $envelope_weight?> kg
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Number of parcels : <?php echo $number_parcel;?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Service: <?php echo $shipping_service->name?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $shipping_service->short_desc?>
                    </td>
                </tr>
                <tr>
                    <td>Postal charge:</td>
                    <td><span id="calculate_shipping_rate_detail_form_postal_charge"><?php echo APUtils::convert_currency($postal_charge, $currency->currency_rate, 2, $decimal_separator); ?></span>&nbsp;<?php echo $currency->currency_short; ?></td>
                </tr>
                <tr>
                    <td>Customs handling:</td>
                    <td><span id="calculate_shipping_rate_detail_form_customs_handling"><?php echo APUtils::convert_currency($customs_handling, $currency->currency_rate, 2, $decimal_separator);?></span>&nbsp;<?php echo $currency->currency_short; ?></td>
                </tr>
                <tr>
                    <td>Handling charges:</td>
                    <td><span id="calculate_shipping_rate_detail_form_handling_charges"><?php echo APUtils::convert_currency($handling_charges, $currency->currency_rate, 2, $decimal_separator); ?></span>&nbsp;<?php echo $currency->currency_short; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">VAT:</td>
                    <td><?php echo APUtils::convert_currency($vat, $currency->currency_rate, 2, $decimal_separator) . ' '. $currency->currency_short; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Total charge:</td>
                    <td><?php echo APUtils::convert_currency($total, $currency->currency_rate, 2, $decimal_separator) . ' '. $currency->currency_short; ?>&nbsp;*</td>
                </tr>
                <tr>
                    <td colspan="2">* Final shipment charge can deviate with specific conditions of parcel (size, repacking, bulk goods, special goods, etc…) If shipping price will deviate, we will contact you for confirmation.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<input type="hidden" name="envelope_id" value="<?php echo $envelope_id;?>">
<input type="hidden" name="shipping_service_id" value="<?php echo $shipping_service_id;?>">
<input type="hidden" name="shipping_type" value="<?php echo $shipping_type;?>">
<input type="hidden" name="postal_charge" value="<?php echo $raw_postal_charge;?>">
<input type="hidden" name="customs_handling" value="<?php echo $raw_customs_handling;?>">
<input type="hidden" name="handling_charges" value="<?php echo $raw_handling_charges;?>">

</form>