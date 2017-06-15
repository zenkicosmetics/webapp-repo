<form id="shippingApiForm" method="post" action="" class="dialog-form">
    <table>
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingserviceformapi_API'); ?></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_apis,
                    "value_key" => 'id',
                    "label_key" => 'name',
                    "value" => $api_id,
                    "name" => 'shipping_service_code',
                    "id" => 'shipping_service_code',
                    "clazz" => 'input-width',
                    "style" => 'width: 250px;',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('product_view_admin_shippingserviceformapi_WebserviceCode'); ?></th>
            <td><input type="text" id="api_service_code" name="api_service_code" class="input-width" style="width: 237px;" value="<?php echo $service_code ?>"/></td>
        </tr>
    </table>
</form>

