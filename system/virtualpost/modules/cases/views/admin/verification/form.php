<?php
$submit_url = base_url() . 'cases/admin_verification/edit';
$enable_invoice_address_field = true;
$enable_postbox_field = true;
$enable_phone_number_field = true;
if ($action_type == 'edit') {
    if ($country->setting_type == '1') {
        $enable_invoice_address_field = true;
        $enable_postbox_field = false;
        $enable_phone_number_field = false;
    } else if($country->setting_type == '2') {
        $enable_invoice_address_field = false;
        $enable_postbox_field = true;
        $enable_phone_number_field = false;
    } else if($country->setting_type == '3'){
        $enable_invoice_address_field = false;
        $enable_postbox_field = false;
        $enable_phone_number_field = true;
    }
} elseif ($action_type == 'add') {
        if ($country->setting_type == '1') {
            $enable_invoice_address_field = true;
            $enable_postbox_field = false;
            $enable_phone_number_field = false;
        } else if($country->setting_type == '2') {
            $enable_invoice_address_field = false;
            $enable_postbox_field = true;
            $enable_phone_number_field = false;
        } else if($country->setting_type == '3'){
            $enable_invoice_address_field = false;
            $enable_postbox_field = false;
            $enable_phone_number_field = true;
        }
}
?>
<form id="addEditCountryForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr <?php if ($action_type == 'edit') {?> style="display: none;"
            <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_TriggerType'); ?></th>
            <td><select class="input-width" id="setting_type"
                name="setting_type" style="width: 262px;">
                    <option value="1"
                        <?php if ($country->setting_type == '1') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_InvoiceAddress'); ?></option>
                    <option value="2"
                        <?php if ($country->setting_type == '2') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_Postbox'); ?></option>
                    <option value="3"
                        <?php if ($country->setting_type == '3') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_PhoneNumber'); ?></option>
            </select></td>
        </tr>

        <div id="divSettingContainer"></div>


        <tr class="enable_invoice_address_field"
            <?php if (!$enable_invoice_address_field) {?>
            style="display: none;" <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_CountryName'); ?></th>
            <td>
            <?php
            echo my_form_dropdown(array(
                "data" => $countries,
                "value_key" => 'country_code',
                "label_key" => 'country_name',
                "value" => $country->country_code,
                "name" => 'country_code',
                "id" => 'addEditCountryForm_country_code',
                "clazz" => 'input-width',
                "style" => 'width: 262px;',
                "has_empty" => true
            ));
            ?>
            </td>
        </tr>
        <tr class="enable_postbox_field"
            <?php if (!$enable_postbox_field) {?> style="display: none;"
            <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_LocationName'); ?></th>
            <td>
            <?php
            echo my_form_dropdown(array(
                "data" => $locations,
                "value_key" => 'id',
                "label_key" => 'location_name',
                "value" => $country->location_id,
                "name" => 'location_id',
                "id" => 'addEditCountryForm_location_id',
                "clazz" => 'input-width',
                "style" => 'width: 262px;',
                "has_empty" => true
            ));
            ?>
            </td>
        </tr>
        <tr class="enable_invoice_address_field"
            <?php if (!$enable_invoice_address_field) {?>
            style="display: none;" <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_CountryRiskClass'); ?></th>
            <td><select class="input-width" id="risk_class"
                name="risk_class" style="width: 262px;">
                    <option value="0"
                        <?php if ($country->risk_class == '0') {?>
                        selected="selected" <?php }?>></option>
                    <option value="1"
                        <?php if ($country->risk_class == '1') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_LowRisk'); ?></option>
                    <option value="2"
                        <?php if ($country->risk_class == '2') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_MediumRisk'); ?></option>
                    <option value="3"
                        <?php if ($country->risk_class == '3') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_HighRisk'); ?></option>
                    <option value="4"
                        <?php if ($country->risk_class == '4') {?>
                        selected="selected" <?php }?>><?php admin_language_e('cases_view_admin_verification_form_NoService'); ?></option>
            </select></td>
        </tr>
        <tr class="enable_invoice_address_field"
            <?php if (!$enable_invoice_address_field) {?>
            style="display: none;" <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_InvoiceAddressIsFilled'); ?></th>
            <td><select class="input-width"
                id="invoice_address_verification"
                name="invoice_address_verification"
                style="width: 262px;">
                    <option value="-1"
                        <?php if ($country->invoice_address_verification == '-1') {?>
                        selected="selected" <?php }?>></option>
                    <option value="0"
                        <?php if ($country->invoice_address_verification == '0') {?>
                        selected="selected" <?php }?>>No</option>
                    <option value="1"
                        <?php if ($country->invoice_address_verification == '1') {?>
                        selected="selected" <?php }?>>Yes</option>
            </select></td>
        </tr>
        <tr class="enable_postbox_field"
            <?php if (!$enable_postbox_field) {?> style="display: none;"
            <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_NameIsEnteredInPostboxName'); ?></th>
            <td><select class="input-width"
                id="postbox_name_filled"
                name="postbox_name_filled"
                style="width: 262px;">
                    <option value="-1"
                        <?php if (isset($country->postbox_name_filled) && $country->postbox_name_filled == '-1') {?>
                        selected="selected" <?php }?>></option>
                    <option value="0"
                        <?php if (isset($country->postbox_name_filled) && $country->postbox_name_filled == '0') {?>
                        selected="selected" <?php }?>>No</option>
                    <option value="1"
                        <?php if (isset($country->postbox_name_filled) && $country->postbox_name_filled == '1') {?>
                        selected="selected" <?php }?>>Yes</option>
            </select></td>
        </tr>
        <tr class="enable_postbox_field"
            <?php if (!$enable_postbox_field) {?> style="display: none;"
            <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_PostboxNameOrPostboxCompany'); ?></th>
            <td><select class="input-width"
                id="private_postbox_verification"
                name="private_postbox_verification"
                style="width: 262px;">
                    <option value="-1"
                        <?php if ($country->private_postbox_verification == '-1') {?>
                        selected="selected" <?php }?>></option>
                    <option value="0"
                        <?php if ($country->private_postbox_verification == '0') {?>
                        selected="selected" <?php }?>>No</option>
                    <option value="1"
                        <?php if ($country->private_postbox_verification == '1') {?>
                        selected="selected" <?php }?>>Yes</option>
            </select></td>
        </tr>
        <tr class="enable_postbox_field"
            <?php if (!$enable_postbox_field) {?> style="display: none;"
            <?php }?>>
            <th><?php admin_language_e('cases_view_admin_verification_form_CompanyNameForPostboxIsFilled'); ?></th>
            <td><select class="input-width"
                id="business_postbox_verification"
                name="business_postbox_verification"
                style="width: 262px;">
                    <option value="-1"
                        <?php if ($country->business_postbox_verification == '-1') {?>
                        selected="selected" <?php }?>></option>
                    <option value="0"
                        <?php if ($country->business_postbox_verification == '0') {?>
                        selected="selected" <?php }?>>No</option>
                    <option value="1"
                        <?php if ($country->business_postbox_verification == '1') {?>
                        selected="selected" <?php }?>>Yes</option>
            </select></td>
        </tr>

        <!-- ++ phone number setting--->
        <tr class="enable_phone_number_field" <?php if (!$enable_phone_number_field) {?> style="display: none;" <?php }?>>
            <th>Country</th>
            <td><?php
            echo my_form_dropdown(array(
                "data" => $phone_countries,
                "value_key" => 'country_code',
                "label_key" => 'country_name',
                "value" => $country->country_code,
                "name" => 'phone_country_code',
                "id" => 'addEditCountryForm_phone_country_code',
                "clazz" => 'input-width',
                "style" => 'width: 262px;',
                "has_empty" => true
            ));
            ?></td>
        </tr>

        <tr class="enable_phone_number_field" <?php if (!$enable_phone_number_field) {?> style="display: none;" <?php }?>>
            <th>Account type</th>
            <td>
                <select class="input-width" id="is_user_company" name="is_user_company" style="width: 262px;">
                    <option value="0" <?php if ($country->is_user_company == '0') { ?> selected="selected" <?php } ?>><?php admin_language_e('cases_view_admin_verification_form_Any'); ?></option>
                    <option value="1" <?php if ($country->is_user_company == '1') { ?> selected="selected" <?php } ?>><?php admin_language_e('cases_view_admin_verification_form_Standard'); ?></option>
                    <option value="2" <?php if ($country->is_user_company == '2') { ?> selected="selected" <?php } ?>><?php admin_language_e('cases_view_admin_verification_form_Enterprise'); ?></option>
                </select>
            </td>
        </tr>

        <!-- --  phone number setting--->

        <tr>
            <th>Case Number</th>
            <td>
                <div style="float: left; width: 200px">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_case_config,
                        "value_key" => 'id',
                        "label_key" => 'name',
                        "value" => '',
                        "name" => 'list_case_config1',
                        "id" => 'list_case_config1',
                        "clazz" => 'input-txt-none',
                        "style" => 'width: 200px;height:80px',
                        "has_empty" => false,
                        "html_option" => 'multiple = "true"'
                    ));
                    ?>
                 </div>
                <div style="float: left; width: 25px; padding: 20px;">
                    <button type="button" id="addButton">&gt;&gt;</button>
                    <br />
                    <button type="button" id="removeButton">&lt;&lt;</button>
                </div>
                <div style="float: left; width: 200px">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_cases,
                        "value_key" => 'id',
                        "label_key" => 'name',
                        "value" => '',
                        "name" => 'list_case_number[]',
                        "id" => 'list_case_number',
                        "clazz" => 'input-txt-none',
                        "style" => 'width: 200px;height:80px',
                        "has_empty" => false,
                        "html_option" => 'multiple = "true"'
                    ));
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <input type="hidden" id="id" name="id"
        value="<?php echo $country->id?>" />
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
    $("#addButton").live("click", function(){
        $("#list_case_config1 > option:selected").each(function(){
            $(this).remove().appendTo("#list_case_number");
        });
    });

    $("#removeButton").live("click", function(){
        $("#list_case_number > option:selected").each(function(){
            $(this).remove().appendTo("#list_case_config1");
        });
    });

    $('#setting_type').live("change", function(){
        var setting_type = $('#setting_type').val();
        if (setting_type == '1') {
            $('.enable_invoice_address_field').show();
            $('.enable_postbox_field').hide();
            $('.enable_phone_number_field').hide();
        } else if (setting_type == '2') {
            $('.enable_invoice_address_field').hide();
            $('.enable_postbox_field').show();
            $('.enable_phone_number_field').hide();
        } else if (setting_type == '3') {
            $('.enable_invoice_address_field').hide();
            $('.enable_postbox_field').hide();
            $('.enable_phone_number_field').show();
        }
    });


});
</script>