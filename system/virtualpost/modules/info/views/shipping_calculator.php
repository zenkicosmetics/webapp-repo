<?php

// Customer's currency setting
$currency_id = $standard_currency->currency_id;
$currency_rate = $standard_currency->currency_rate;
$currency_short = $standard_currency->currency_short;

$postal_charge = sprintf('0%s00', $decimal_separator);
$customs_handling = sprintf('0%s00', $decimal_separator);
$handling_charges = sprintf('0%s00', $decimal_separator);
$total_vat = sprintf('0%s00', $decimal_separator);
$total_charge = sprintf('0%s00', $decimal_separator);

?>
<style>
    th, td { vertical-align: middle; }
    div.shipment-calculator-result { margin-bottom: 8px; }
    span.currency_short { float: right; margin-right: 12px; }
    span.currency_amount { float: right; margin-right: 5px; }

    /*Override jquery ui's CSS*/
    div.ui-dialog-buttonset { width: 100%;}
    button.ui-button { color: #ffffff !important;}
    button.ui-button:first-child { float: left !important; margin-left: 7px !important; width: 230px !important; margin-right: 20px !important;}
    button.ui-button:nth-child(2) { float: left !important; width: 230px !important; }
    button.ui-button:last-child { float: right !important; width: 150px !important; margin-right: 15px !important;}
    button.ui-state-default:first-child, button.ui-state-default:nth-child(2) { background: #a6a6a6 url("../images/ui-bg_glass_100_f6f6f6_1x400.png") repeat-x scroll 50% 50% !important; }
    button.ui-state-default:last-child { background: #fdc500 url("../images/ui-bg_glass_100_f6f6f6_1x400.png") repeat-x scroll 50% 50% !important; }
</style>
<h2 style="margin: 10px 10px 0px 10px; font-size: 16px;font-weight: bold;">Shipping Calculator</h2>
<div style="margin: 20px 10px 10px;">
    Warning: please note that this calculator can currently only calculate prices for a few shipping services in selected countries. If you need a quotation for an expected shipment, please contact <a href="mailto:mail@clevvermail.com">mail@clevvermail.com</a>. Please note, that the research for a price estimate can take some time.
</div>


    <div id="service_errors" style="color: red; margin: 20px 10px 10px; display: none;"></div>
<?php if (validation_errors()): ?>
    <div style="color: red;margin: 20px 10px 10px;">
        <?php echo validation_errors(); ?>
    </div>
<?php endif; ?>
<form id="shippingCalculatorForm" action="<?php echo base_url(); ?>info/shipping_calculator" method="post">
    <table style="width: 920px;">
        <tr>
            <td style="vertical-align: top;padding-top: 0;">
                <table class="displayInfo displayInfo02" style="width: 440px;">
                    <tr><th colspan="2" style="text-align: left;">From: (item location)</th></tr>
                    <tr>
                        <td>Location:</td>
                        <td>
                            <?php
                            echo my_form_dropdown(array(
                                "data" => $locations,
                                "value_key" => 'id',
                                "label_key" => 'location_name',
                                "value" => 0,
                                "name" => 'location_id',
                                "id" => 'location_id',
                                "clazz" => 'input-width',
                                "style" => 'width: 310px;margin-top:10px',
                                "has_empty" => true
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr><th colspan="2" style="text-align: left;">To: (your forwarding address)</th></tr>
                    <tr>
                        <td>Street:</td>
                        <td><input class="input-width" name="shipment_street" id="shipment_street" type="text" style="width: 300px" value="<?php echo $shipment_street; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Post Code:</td>
                        <td><input class="input-width" name="shipment_postcode" id="shipment_postcode" type="text" style="width: 300px" value="<?php echo $shipment_postcode; ?>"/></td>
                    </tr>
                    <tr>
                        <td>City:</td>
                        <td><input class="input-width" name="shipment_city" id="shipment_city" type="text" style="width: 300px" value="<?php echo $shipment_city; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Region:</td>
                        <td><input class="input-width" name="shipment_region" id="shipment_region" type="text" style="width: 300px" value="<?php echo $shipment_region; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Country:</td>
                        <td>
                            <select id="shipment_country_id" name="shipment_country_id" class="input-width" style="width: 310px;">
                                <?php foreach ($countries as $country) { ?>
                                    <?php if (isset($shipment_country_id) && $shipment_country_id == $country->id) { ?>
                                        <option value="<?php echo $country->id; ?>" selected="selected"><?php echo $country->country_name; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $country->id; ?>"><?php echo $country->country_name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;padding-top: 0px;">
                <table class="displayInfo displayInfo02" style="width: 550px;">
                    <tr><th colspan="2">&nbsp;</th></tr>
                    <tr>
                        <td style="width: 160px;">Shipping Service:</td>
                        <td>
                            <select id="shipment_service_id" name="shipment_service_id" class="input-width" style="width: 100%;margin-top:10px">
                                <option value=""></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding-top: 0 !important;">Service:</td>
                        <td style="vertical-align: top; padding-top: 0 !important;">
                            <p id="shipment_service_description" name="shipment_service_description" style="width: 350px; min-height: 35px; padding: 0 5px; font-size: 95%; text-align: left;"></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Shipping Type:</td>
                        <td>
                            <select id="shipment_type_id" name="shipment_type_id" class="input-width" style="width: 100%;">
                                <option value="1" selected="selected" >Direct forwarding</option>
                                <option value="2">Collect forwarding</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Number of parcels:</td>
                        <td style="">
                            <input id="number_of_parcels" name="number_of_parcels" type="text" class="input-width" style="width: 50px; font-size: 12px; font-style: italic; padding: 6px;" value=""/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input id="total_insured_value" name="total_insured_value" type="text" class="input-width" style="width: 55px; font-size: 12px; font-style: italic; padding: 6px; float: right;" value="" />
                            <span style="float: right; padding-top:5px; padding-right: 15px;">customs/insurance value (EUR):</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Length/Width/Height:</td>
                        <td>
                            <input type="text" id="length" name="length" class="input-width" style="width: 50px; font-size: 12px; font-style: italic; padding: 6px;" value="" placeholder="L (cm)"/>
                            <input type="text" id="width" name="width" class="input-width" style="width: 50px; font-size: 12px; font-style: italic; padding: 6px;" value="" placeholder="W (cm)"/>
                            <input type="text" id="height" name="height" class="input-width" style="width: 50px; font-size: 12px; font-style: italic; padding: 6px;" value="" placeholder="H (cm)"/>
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weight:&nbsp;&nbsp;</span>
                            <input type="text" id="weight" name="weight" class="input-width" style="width: 50px; font-size: 12px; font-style: italic; padding: 6px;" value="" placeholder="W (kg)"/>&nbsp;
                            <a id="editMultiPackagesInfo" href="#">edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="display: block; width: 168px; height: 30px;">Currency:</span></td>
                        <td>
                            <?php
                            echo my_form_dropdown(array(
                                "data" => $currencies,
                                "value_key" => 'currency_id',
                                "label_key" => 'currency_short',
                                "value" => $currency_id,
                                "name" => 'currency_id',
                                "id" => 'currency_id',
                                "clazz" => 'input-width',
                                "style" => 'width: 80px;',
                                "has_empty" => false
                            ));
                            ?>
                            <input type="button" id="calculateButton" name="calculateButton" value="Calculate" class="input-btn btn-yellow" style="float: right; width: 152px; height: 30px; font-size: 15px; padding: 0 15px;"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div style="border: solid 2px #CCCCCC; margin-left: 454px; width: 536px; margin-bottom: 10px; padding-left: 10px; padding-right: 10px; background-color: white;">
        <div class="shipment-calculator-result" style="margin-top: 10px;">
            <span>Postal Charge:</span>
            <span class="currency_short"><?php echo $currency_short; ?></span>
            <span class="currency_amount" id="cal_postal_charge"><?php echo $postal_charge; ?></span>
        </div>
        <div class="shipment-calculator-result">
            <span>Customs Handling:</span>
            <span class="currency_short"><?php echo $currency_short; ?></span>
            <span class="currency_amount" id="cal_customs_handling"><?php echo $customs_handling; ?></span>
        </div>
        <div class="shipment-calculator-result" style="border-bottom: solid 1px black; padding-bottom: 4px;">
            <span>Handling Charges:</span>
            <span class="currency_short"><?php echo $currency_short; ?></span>
            <span class="currency_amount" id="cal_handling_charges"><?php echo $handling_charges; ?></span>
        </div>
        <div class="shipment-calculator-result" style="border-bottom: solid 1px black; padding-bottom: 4px;">
            <span>VAT (<?php echo $VAT; ?>%):</span>
            <span class="currency_short"><?php echo $currency_short; ?></span>
            <span class="currency_amount" id="cal_total_VAT"><?php echo $total_vat; ?></span>
        </div>
        <div class="shipment-calculator-result">
            <span style="font-weight: bold;">Total Charge:</span>
            <span class="currency_short" style="font-weight: bold; margin-right: 13px;"><?php echo $currency_short; ?></span>
            <span class="currency_amount" style="font-weight: bold;" id="cal_total_charge" ><?php echo $total_charge; ?></span>
        </div>
    </div>
    <p style="margin-left: 464px; width: 536px; text-align: justify; text-justify: inter-word;">
        *Final shipment charge can deviate with specific conditions of parcel (size, repacking, bulk goods, special goods, etc…). If shipping price will deviate, we will contact you for confirmation.<br>
        *Customs cost and import VAT are not included in this calculation and have to be paid separately to the parcel service company.
    </p>
    <input type="hidden" id="multiple_quantity" name="multiple_quantity" class="input-width" value=""/>
    <input type="hidden" id="multiple_length" name="multiple_length" class="input-width" value=""/>
    <input type="hidden" id="multiple_width" name="multiple_width" class="input-width" value=""/>
    <input type="hidden" id="multiple_height" name="multiple_height" class="input-width" value="" />
    <input type="hidden" id="multiple_weight" name="multiple_weight" class="input-width" value=""/>
</form>
<!-- Content for dialog -->
<div class="hide">
    <div id="inputParcelsInfo" title="Enter the parcel information for the outgoing shipment here" class="input-form dialog-form"></div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/info/js/ShippingCalculator.js"></script>
<script>
    jQuery(document).ready(function($) {
        ShippingCalculator.init('<?php echo base_url(); ?>', '<?php echo $decimal_separator; ?>');
    });
</script>