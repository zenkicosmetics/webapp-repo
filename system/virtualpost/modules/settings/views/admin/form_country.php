<?php
if ($action_type == 'add') {
    $submit_url = base_url().'admin/settings/add_country';
} else {
    $submit_url = base_url().'admin/settings/edit_country';
}

// Risk class
if (isset($country)) {
    $risk_class = $country->risk_class;
} else {
    $risk_class = 0;
}

// Decimal separator
if (isset($country)) {
    $decimal_separator = $country->decimal_separator;
} else {
    $decimal_separator = ',';
}
?>

<form id="addEditCountryForm" method="post" action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_CountryName'); ?><span class="required">*</span></th>
            <td><input type="text" id="country_name" name="country_name" value="<?php echo $country->country_name; ?>" class="input-width custom_autocomplete" style="width: 160px;" /></td>
            <th><?php admin_language_e('settings_view_admin_formcountry_Language'); ?></th>
            <td>
                <?php   echo my_form_dropdown(array(
                    "data" => $languages,
                    "value_key" => 'id',
                    "label_key" => 'code',
                    "value" => $country->language,
                    "name" => 'language_code',
                    "id" => 'language_code',
                    "clazz" => 'input-txt-none select_right',
                    "style" => 'width: 200px',
                    "has_empty" => false ,
                )); ?>
            </td>
<!--            <td><input type="text" id="language" name="language" value="--><?php //echo $country->language; ?><!--" class="input-width custom_autocomplete" style="width: 160px;" /></td>-->
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_CountryCode'); ?><span class="required">*</span></th>
            <td><input type="text" id="country_code" name="country_code" value="<?php echo $country->country_code; ?>" class="input-width custom_autocomplete" style="width: 160px;" /></td>
            <th><?php admin_language_e('settings_view_admin_formcountry_Currency'); ?><span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $currencies,
                    "value_key" => 'currency_id',
                    "label_key" => 'currency_short',
                    "value" => $country->currency_id,
                    "name" => 'currency_id',
                    "id"    => 'currency_id',
                    "clazz" => 'input-txt-none',
                    "style" => 'width: 170px;',
                    "has_empty" => true,
                    "option_default" => ''
                ));?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_IsEUMember'); ?></th>
            <td><input type="checkbox" id="eu_member_flag" name="eu_member_flag" value="1" class="custom_autocomplete" <?php if ($country->eu_member_flag == '1') {?> checked="checked" <?php }?> /></td>
            <th><?php admin_language_e('settings_view_admin_formcountry_DecimalSeparator'); ?></th>
            <td>
                <select name="decimal_separator" class="input-txt-none" style="width: 170px;">
                    <option value="," <?php echo ($decimal_separator == ',')? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_Comma'); ?></option>
                    <option value="." <?php echo ($decimal_separator == '.')? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_Dot'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_RiskClass'); ?></th>
            <td>
                <select name="risk_class" class="input-width" style="width: 160px;">
                    <option value="0" <?php echo ($risk_class == 0)? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_NoService'); ?></option>
                    <option value="1" <?php echo ($risk_class == 1)? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_LowRisk'); ?></option>
                    <option value="2" <?php echo ($risk_class == 2)? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_MediumRisk'); ?></option>
                    <option value="3" <?php echo ($risk_class == 3)? 'selected' : ''; ?>><?php admin_language_e('settings_view_admin_formcountry_HighRisk'); ?></option>
                </select>
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_LetterNational'); ?><span class="required">*</span></th>
            <td><input type="text" id="letter_national_price" name="letter_national_price" value="<?php echo APUtils::number_format($country->letter_national_price); ?>" class="input-width" style="width: 160px;" /></td>
            <th><?php admin_language_e('settings_view_admin_formcountry_LetterInternational'); ?><span class="required">*</span></th>
            <td><input type="text" id="letter_international_price" name="letter_international_price" value="<?php echo APUtils::number_format($country->letter_international_price); ?>" class="input-width" style="width: 160px;" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('settings_view_admin_formcountry_PackageNational'); ?><span class="required">*</span></th>
            <td><input type="text" id="package_national_price" name="package_national_price" value="<?php echo APUtils::number_format($country->package_national_price); ?>" class="input-width" style="width: 160px;" /></td>
            <th><?php admin_language_e('settings_view_admin_formcountry_PackageInternational'); ?><span class="required">*</span></th>
            <td><input type="text" id="package_international_price" name="package_international_price" value="<?php echo APUtils::number_format($country->package_international_price); ?>" class="input-width" style="width: 160px;" /></td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
    <input type="hidden" id="h_country_id" name="country_id" value="<?php echo $country->id; ?>" />
</form>
<script type="text/javascript">
    $(document).ready( function() {

    });
</script>
