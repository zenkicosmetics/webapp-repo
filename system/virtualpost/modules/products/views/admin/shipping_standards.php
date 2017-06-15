<div class="header"><h2 style="font-size: 20px; margin-bottom: 10px">Services Per Location</h2></div>
<div class="button_container">
    <form id="locationServiceForm" method="post" action="<?php echo base_url() ?>products/admin/shipping_standards">
        <div class="input-form">
            <table class="settings" style="width: 1000px">
                <tr style="background: rgb(68,84,106); color: white;">
                    <th class="input-width-200">&nbsp;</th>
                    <th class="input-width-200"><?php admin_language_e('product_view_admin_shippingstandard_NationalLetter'); ?></th>
                    <th class="input-width-250"><?php admin_language_e('product_view_admin_shippingstandard_InternationalLetter'); ?></th>
                    <th class="input-width-200"><?php admin_language_e('product_view_admin_shippingstandard_NationalParcel'); ?></th>
                    <th class="input-width-250"><?php admin_language_e('product_view_admin_shippingstandard_InternationalParcel'); ?></th>
                </tr>
                <?php
                foreach ($list_location_shipping_services as $location_shipping_services) {
                    $location = $location_shipping_services['location'];
                    $shipping_services = $location_shipping_services['shipping_services'];
                    $shipping_service_nation_letter = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1));
                    $shipping_service_internation_letter = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));
                    $shipping_service_nation_package = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1));
                    $shipping_service_internation_package = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 2));
                ?>
                    <tr>
                        <th><?php echo $location->location_name; ?></th>
                        <td>
                            <select name="primary_letter_shipping_services[<?php echo $location->id; ?>]" class="input-width" style="width:200px">
                                <option value="0" <?php if (empty($location->primary_letter_shipping)): ?>selected="selected"<?php endif; ?>></option>
                                <?php foreach($shipping_service_nation_letter as $index => $shipping_service): ?>
                                    <?php if ($location->primary_letter_shipping == $shipping_service->id): ?>
                                        <option value="<?php echo $shipping_service->id; ?>" selected="selected"><?php echo $shipping_service->name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $shipping_service->id; ?>"><?php echo $shipping_service->name; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="primary_international_letter_shipping_services[<?php echo $location->id; ?>]" class="input-width" style="width:200px">
                                <option value="0" <?php if (empty($location->primary_international_letter_shipping)): ?>selected="selected"<?php endif; ?>></option>
                                <?php foreach($shipping_service_internation_letter as $index => $shipping_service): ?>
                                    <?php if ($location->primary_international_letter_shipping == $shipping_service->id): ?>
                                        <option value="<?php echo $shipping_service->id; ?>" selected="selected"><?php echo $shipping_service->name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $shipping_service->id; ?>"><?php echo $shipping_service->name; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="standard_national_parcel_services[<?php echo $location->id; ?>]" class="input-width" style="width:200px">
                                <option value="0" <?php if (empty($location->standard_national_parcel_service)): ?>selected="selected"<?php endif; ?>></option>
                                <?php foreach($shipping_service_nation_package as $index => $shipping_service): ?>
                                    <?php if ($location->standard_national_parcel_service == $shipping_service->id): ?>
                                        <option value="<?php echo $shipping_service->id; ?>" selected="selected"><?php echo $shipping_service->name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $shipping_service->id; ?>"><?php echo $shipping_service->name; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="standard_international_parcel_services[<?php echo $location->id; ?>]" class="input-width" style="width:200px">
                                <option value="0" <?php if (empty($location->standard_international_parcel_service)): ?>selected="selected"<?php endif; ?>></option>
                                <?php foreach($shipping_service_internation_package as $index => $shipping_service): ?>
                                    <?php if ($location->standard_international_parcel_service == $shipping_service->id): ?>
                                        <option value="<?php echo $shipping_service->id; ?>" selected="selected"><?php echo $shipping_service->name; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $shipping_service->id; ?>"><?php echo $shipping_service->name; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>&nbsp;</th>
                    <td colspan="3">
                        <button id="submitButton">Submit</button>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
<div class="clear-height"></div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/products/js/ShippingStandards.js"></script>
<script>
    jQuery(document).ready(function() {
        ShippingStandards.init();
    });
</script>