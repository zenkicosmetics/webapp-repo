<!-- Shipping api credential -->
<div id="apiCredential" class="input-form dialog-form">
    <form id="apiCredentialForm" method="post" action="" class="dialog-form">
        <table>
            <tr>
                <th><?php admin_language_e('product_view_admin_shippingserviceformcredential_API'); ?></th>
                <td>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_apis,
                        "value_key" => 'id',
                        "label_key" => 'name',
                        "value" => $api_id,
                        "name" => 'shipping_credential',
                        "id" => 'shipping_credential',
                        "clazz" => 'input-width',
                        "style" => 'width: 250px;',
                        "has_empty" => true
                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php admin_language_e('product_view_admin_shippingserviceformcredential_Credential'); ?></th>
                <td>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_credentials,
                        "value_key" => 'id',
                        "label_key" => 'name',
                        "value" => $credential_id,
                        "name" => 'api_credential',
                        "id" => 'api_credential',
                        "clazz" => 'input-width',
                        "style" => 'width: 250px;',
                        "has_empty" => true
                    ));
                    ?>
                </td>
            </tr>
        </table>
    </form>
</div>

