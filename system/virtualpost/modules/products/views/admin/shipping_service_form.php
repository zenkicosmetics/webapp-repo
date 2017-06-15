<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/products/add_shipping_service';
} else {
    $submit_url = base_url() . 'admin/products/edit_shipping_service';
}
?>
<style>
   .custom-input-width {width: 327px;}
</style>
<form id="addEditShippingServiceForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <td>
                <table>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_Name'); ?><span class="required">*</span></th>
                        <td><input type="text" id="addEditShippingServiceForm_name" name="name" value="<?php echo $shipping_service->name ?>" class="input-width custom-input-width" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_Description'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_short_desc" name="short_desc" value="<?php echo $shipping_service->short_desc ?>" class="input-width custom-input-width"/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_LongDescription'); ?></th>
                        <td><textarea id="addEditShippingServiceForm_long_desc" name="long_desc" class="input-width" style="width: 327px; height: 67px;"><?php echo $shipping_service->long_desc ?></textarea></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_WeightLimit'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_weight_limit" name="weight_limit" value="<?php echo $shipping_service->weight_limit ?>" class="input-width custom-input-width"/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_DimensionLimit'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_dimension_limit" name="dimension_limit" value="<?php echo $shipping_service->dimension_limit ?>" class="input-width custom-input-width"/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_ShippingTemplate'); ?><span class="required">*</span></th>
                        <td>
                            <select name="shipping_service_template" id="shipping_service_template" class="input-width" style="width: 340px;">
                                <option value=""></option>
                                <option value="1" <?php if ($shipping_service->shipping_service_template == '1') { ?> selected="selected" <?php } ?>><?php echo lang('shipping_service_template_1');?></option>
                                <option value="2" <?php if ($shipping_service->shipping_service_template == '2') { ?> selected="selected" <?php } ?>><?php echo lang('shipping_service_template_2');?></option>
                                <option value="3" <?php if ($shipping_service->shipping_service_template == '3') { ?> selected="selected" <?php } ?>><?php echo lang('shipping_service_template_3');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_NoTrackingInformation'); ?></th>
                        <td><input type="checkbox" id="addEditShippingServiceForm_tracking_information_flag" name="tracking_information_flag" value="0" class="" <?php if ($shipping_service->tracking_information_flag == '0') {?> checked="checked" <?php } ?> /></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_ShippingApi'); ?></th>
                        <td>
                            <div id="shipping_api_codes">
                                <button id="btnAddShippingApi" class="admin-button"><?php admin_language_e('product_view_admin_shippingserviceform_AddApiBtn'); ?></button>
                                <table id="shipping_api_code">
                                    <thead>
                                        <tr>
                                            <td style="width: 40%;">
                                                <?php admin_language_e('product_view_admin_shippingserviceform_ColumnAPI'); ?>
                                            </td>
                                            <td style="width: 40%;">
                                                <?php admin_language_e('product_view_admin_shippingserviceform_ColumnCode'); ?>
                                            </td>
                                            <td style="width: 20%;">
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($shipping_service->shipping_api_codes)){
                                            foreach ($shipping_service->shipping_api_codes as $shipping_api_code) {
                                                $row = '<tr><td class="api_id" style="display: none;">' . $shipping_api_code->api_id . '</td>';
                                                $row .= '<td class="api_name">' . $shipping_api_code->api_name . '</td>';
                                                $row .= '<td class="service_code">' . $shipping_api_code->service_code .'</td>';
                                                $row .=  '<td><button class="btnEditShippingApi icon-edit"></button><button class="btnRemoveShippingApi icon-delete"></button></td></tr>';
                                            
                                                echo $row;
                                            }
                                            
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_ApiCredentials'); ?></th>
                        <td>
                            <div id="shipping_api_credentials">
                                <button id="btnAddApiCredential" class="admin-button"><?php admin_language_e('product_view_admin_shippingserviceform_AddCredentialBtn'); ?></button>
                                <table id="shipping_api_credential">
                                    <thead>
                                        <tr>
                                            <td style="width: 40%;">
                                                <?php admin_language_e('product_view_admin_shippingserviceform_ColumnAPI'); ?>
                                            </td>
                                            <td style="width: 40%;">
                                                <?php admin_language_e('product_view_admin_shippingserviceform_ColumnCredential'); ?>
                                            </td>
                                            <td style="width: 20%;">
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($shipping_service->shipping_api_credentials)){
                                            foreach ($shipping_service->shipping_api_credentials as $shipping_api_credential) {
                                                $row = '<tr><td class="api_id" style="display: none;">' . $shipping_api_credential->api_id . '</td>';
                                                $row .= '<td class="credential_id" style="display: none;">' . $shipping_api_credential->credential_id .'</td>';
                                                $row .= '<td class="api_name">' . $shipping_api_credential->api_name . '</td>';
                                                $row .= '<td class="credential_name">' . $shipping_api_credential->credential_name .'</td>';
                                                $row .=  '<td><button class="btnEditShippingCredential icon-edit"></button><button class="btnRemoveShippingCredential icon-delete"></button></td></tr>';
                                            
                                                echo $row;
                                            }
                                            
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_Carrier'); ?><span class="required">*</span></th>
                        <td>
                            <?php
                            echo my_form_dropdown(array(
                                "data" => $list_carriers,
                                "value_key" => 'id',
                                "label_key" => 'name',
                                "value" => $shipping_service->carrier_id,
                                "name" => 'carrier_id',
                                "id" => 'carrier_id',
                                "clazz" => 'input-width',
                                "style" => 'width: 340px;',
                                "has_empty" => true
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_ShowShippingServiceWhenCalculationFail'); ?><span class="required">*</span></th>
                        <td>
                            <select name="show_calculation_fails" id="show_calculation_fails" class="input-width" style="width: 340px;">
                                <option value=""></option>
                                <option value="1" <?php if ($shipping_service->show_calculation_fails == '1') { ?> selected="selected" <?php } ?>><?php echo lang('show_calculation_fails_1');?></option>
                                <option value="0" <?php if ($shipping_service->show_calculation_fails == '0') { ?> selected="selected" <?php } ?>><?php echo lang('show_calculation_fails_0');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_Logo'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_logo" name="logo" value="<?php echo $shipping_service->logo ?>" 
                                   class="input-width custom-input-width" style="width: 240px;"/>
                            <button type="button" class="tooltip admin-button" id="selectShippingServiceLogoButton"><?php admin_language_e('product_view_admin_shippingserviceform_SelectFileBtn'); ?></button>
                            <input type="file" id="imagepath03" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_FactorA'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_factor_a" name="factor_a" value="<?php echo $shipping_service->factor_a ?>" class="input-width custom-input-width"/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_FactorB'); ?></th>
                        <td><input type="text" id="addEditShippingServiceForm_factor_b" name="factor_b" value="<?php echo $shipping_service->factor_b ?>" class="input-width custom-input-width"/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_ServiceType'); ?><span class="required">*</span></th>
                        <td>
                            <?php
                            echo code_master_form_dropdown(array(
                                "code" => APConstants::SHIPPING_SERVICE_TYPE,
                                "value" => $shipping_service->service_type,
                                "name" => 'service_type',
                                "id" => 'service_type',
                                "clazz" => 'input-width',
                                "style" => 'width: 340px;',
                                "has_empty" => true
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('product_view_admin_shippingserviceform_PackagingType'); ?><span class="required">*</span></th>
                        <td>
                            <?php
                            echo code_master_form_dropdown(array(
                                "code" => APConstants::SHIPPING_PACKAGING_TYPE,
                                "value" => $shipping_service->packaging_type,
                                "name" => 'packaging_type',
                                "id" => 'packaging_type',
                                "clazz" => 'input-width',
                                "style" => 'width: 340px;',
                                "has_empty" => true
                            ));
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" /> <input type="hidden" id="id" name="id" value="<?php echo $shipping_service->id ?>" />
</form>
<!-- Content for dialog -->
<div class="hide">
    <!-- Shipping api service code -->
    <div id="shippingApi" class="input-form dialog-form"></div>
    <!-- Shipping api credential -->
    <div id="apiCredential" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
jQuery(document).ready(function ($) {
    
    $('.admin-button').button();
    $("#selectShippingServiceLogoButton").button().click(function() {
        $('#imagepath03').val('');
        $('#imagepath03').click();
        return false;
    });
    
    $("#imagepath03").change(function (){
        // Upload data here
        $.ajaxFileUpload({
                id: 'imagepath03',
                data: {},
                url: '<?php echo base_url()?>admin/products/upload_shipping_services_logo',
                success: function(data) {
                    $('#addEditShippingServiceForm_logo').val(data.message);
                }
        });
    });
    
    function openShippingApiWindow(title, mode, row) {
    
        $('#shippingApi').openDialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            title: title,
            open: function () {
                
                if (mode === 'add'){
                    $(this).load('<?php echo base_url()?>admin/products/load_shipping_api_form', function () {});        
                } else if (mode === 'edit'){
                    var api_id = row.children('td.api_id').html();
                    var service_code = row.children('td.service_code').html();
                    var params = {
                        api_id : api_id,
                        service_code : service_code
                    };
                    $(this).load('<?php echo base_url()?>admin/products/load_shipping_api_form?' + $.param(params), function () {});
                }
                
            },
            buttons: {
                'Save': function () {
                    
                     var api_name = $('#shipping_service_code :selected').text(),
                        service_code = $('#api_service_code').val();
             
                    if (!api_name || !service_code) {
                        $.displayError("<?php admin_language_e('product_view_admin_shippingserviceform_RequiredShippingApiAndCodeMessage'); ?>");
                        return false;
                    }
                    
                    if (mode === 'add') {
                        $( "#shipping_api_code tbody" ).append( "<tr>" +
                               "<td class='api_id' style='display: none;'>" + $('#shipping_service_code').val() + "</td>" +
                               "<td class='api_name'>" + $('#shipping_service_code :selected').text() + "</td>" +
                               "<td class='service_code'>" + $('#api_service_code').val() + "</td>" +
                               "<td>" + '<button class="btnEditShippingApi icon-edit"></button><button class="btnRemoveShippingApi icon-delete"></button>' + "</td>" +
                             "</tr>" );
                    } else if (mode === 'edit'){
                        row.children('td.api_id').html($('#shipping_service_code :selected').val());
                        row.children('td.api_name').html($('#shipping_service_code :selected').text());
                        row.children('td.service_code').html($('#api_service_code').val());
                    }
                    $(this).dialog( "close" );
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#shippingApi').dialog('option', 'position', 'center');
        $('#shippingApi').dialog('open');
    };
    
    $('#btnAddShippingApi').die("click").live("click", function (e) {
        e.preventDefault();
        openShippingApiWindow('<?php admin_language_e('product_view_admin_shippingserviceform_AddApiPopupTitle'); ?>', 'add');
    });
    
    $('.btnRemoveShippingApi').die("click").live( "click", function(e) {
        var self = $(this);
        e.preventDefault();
        $.confirm({
                message: '<?php admin_language_e('product_view_admin_shippingserviceform_ConfirmDeleteMessage'); ?>',
                yes: function() {
                   self.closest('tr').remove();
                }
            });
    });

    $('.btnEditShippingApi').die('click').live( "click", function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        openShippingApiWindow('Edit shipping api', 'edit', row);
    });
    
    function openApiCredentialWindow(title, mode, row) {
    
        $('#apiCredential').openDialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            title: title,
            open: function () {

                if (mode === 'add'){
                    $(this).load('<?php echo base_url()?>admin/products/load_shipping_credential_form', function () {}); 
                } else if (mode === 'edit'){
                    
                    var api_id = row.children('td.api_id').html();
                    var credential_id = row.children('td.credential_id').html();
                    
                    var params = {
                        api_id : api_id,
                        credential_id : credential_id
                    };
                    $(this).load('<?php echo base_url()?>admin/products/load_shipping_credential_form?' + $.param(params), function () {});
                }
                
            },
            buttons: {
                'Save': function () {
                    
                    var api_name = $('#shipping_credential :selected').text(),
                        credential_name = $('#api_credential :selected').text();
            
                    if (!api_name || !credential_name) {
                         $.displayError("Please input shipping api and credential");
                        return false;
                    }
                    
                    if (mode === 'add') {
                        $( "#shipping_api_credential tbody" ).append( "<tr>" +
                               "<td class='api_id' style='display: none;'>" + $('#shipping_credential').val() + "</td>" +
                               "<td class='credential_id' style='display: none;'>" + $('#api_credential').val() + "</td>" +
                               "<td class='api_name'>" + $('#shipping_credential :selected').text() + "</td>" +
                               "<td class='credential_name'>" + $('#api_credential :selected').text() + "</td>" +
                               "<td>" + '<button class="btnEditShippingCredential icon-edit"></button><button class="btnRemoveShippingCredential icon-delete"></button>' + "</td>" +
                             "</tr>" );
                    } else if (mode === 'edit'){
                        row.children('td.api_id').html($('#shipping_credential :selected').val());
                        row.children('td.api_name').html($('#shipping_credential :selected').text());
                        row.children('td.credential_id').html($('#api_credential :selected').val());
                        row.children('td.credential_name').html($('#api_credential :selected').text());
                    }
                    $(this).dialog( "close" );
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#apiCredential').dialog('option', 'position', 'center');
        $('#apiCredential').dialog('open');
    };
    
    $('#btnAddApiCredential').die("click").live("click", function (e) {
        e.preventDefault();
        openApiCredentialWindow('<?php admin_language_e('product_view_admin_shippingserviceform_AddApiPopupTitle'); ?>', 'add');
    });
    
    $('.btnRemoveShippingCredential').die("click").live( "click", function(e) {
        var self = $(this);
        e.preventDefault();
        $.confirm({
                message: '<?php admin_language_e('product_view_admin_shippingserviceform_ConfirmDeleteMessage'); ?>',
                yes: function() {
                   self.closest('tr').remove();
                }
            });
    });
    
    $('.btnEditShippingCredential').die("click").live( "click", function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        openApiCredentialWindow('<?php admin_language_e('product_view_admin_shippingserviceform_EditApiPopupTitle'); ?>', 'edit', row);
    });
        
});  
</script>
