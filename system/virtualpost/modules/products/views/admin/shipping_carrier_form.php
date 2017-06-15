<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/products/add_shipping_carrier';
} else {
    $submit_url = base_url() . 'admin/products/edit_shipping_carrier';
}
?>
<style>
    .custom-input-width {width: 327px;}
</style>
<form id="addEditShippingCarrierForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table width="100%">
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingcarrierform_Code'); ?><span class="required">*</span></th>
            <td><input type="text" id="addEditShippingCarrierForm_code" name="code" value="<?php echo $shipping_carrier->code ?>" class="input-width custom-input-width"/></td>
        </tr>
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingcarrierform_Name'); ?> <span class="required">*</span></th>
            <td><input type="text" id="addEditShippingCarrierForm_name" name="name" value="<?php echo $shipping_carrier->name ?>" class="input-width custom-input-width" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingcarrierform_Description'); ?></th>
            <td><textarea id="addEditShippingCarrierForm_description" name="description" class="input-width" style="width: 327px; height: 100px;"><?php echo $shipping_carrier->description ?></textarea></td>
        </tr>
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingcarrierform_TrackingNumberURL'); ?></th>
            <td><input type="text" id="addEditShippingCarrierForm_tracking_number_url" name="tracking_number_url" value="<?php echo $shipping_carrier->tracking_number_url ?>" class="input-width custom-input-width" /></td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" /> 
    <input type="hidden" id="id" name="id" value="<?php echo $shipping_carrier->id ?>" />
</form>

