<style>
    .button_upgrade, .button_upgrade:hover{
        margin-left:30px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        font-size:12px;
        padding: 10px 10px 10px 10px;
        text-decoration:none;
        display:inline-block;
        font-weight:bold;
        width: 367px;
        text-align: center;
    }
    .error {
        background:#c88 !important;
        font-weight: bold;
    }
    #left-account{
        min-height: 860px;
    }
    .invoice_address_table th, .invoice_address_table td{
        vertical-align: middle;
        padding: 0.3em 0.5em;
    }
</style>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2><?php language_e('account_view_postbox_setting_PostboxSeting'); ?></h2>
        <div class="ym-clearfix" style="height:1px;"></div>
        <?php
        if ($info->plan_delete_date != null) {
            $delete_message = lang('delete_success02');
            $delete_date = APUtils::displayDate($info->plan_delete_date);
            // $delete_date = $info->plan_delete_date;
            $delete_message = sprintf($delete_message, $delete_date);
            ?>
            <div style="color: red;">
                <h3 style="color: red;font-size: 16px; font-weight: bold; margin-bottom: 10px; line-height: normal;"><?php echo $delete_message ?></h3>
            </div>
        <?php } ?>
    </div>
</div>
<div id="account-body-wrapper">
    <div class="ym-grid">
        <div class="ym-g50 ym-gl">
            <div id="left-account">
                <div style="margin:15px 0px;margin-bottom:10px;">
                    <div style="margin-bottom: 20px;">
                        <h4 class="COLOR_063"><?php
                        $postbox_location_name = $postbox ? $postbox->location_name : "";
                        language_e('account_view_postbox_setting_YourPrimaryLocation', ['location' => $postbox_location_name]); ?></h4>
                    </div>
                    <div class="ym-clearfix"></div>
                </div>
                <div class="ym-clearfix"></div>

                <div style="margin:0px 0px; <?php if ($customer_product_setting['postbox_name_flag'] != '1') { ?> visibility: hidden;  <?php } ?>">
                    <div class="ym-gl left-3" style="width:24%;line-height:50px;">
                        <?php language_e('account_view_postbox_setting_NumberOfPost'); ?>:
                    </div>
                    <div class="ym-gl" style="width:47%;line-height: 50px; margin-left: 5px;">
                        <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { ?>
                        <?php
                        foreach ($acct_type as $item) {
                            ?>
                            <div style="width:30%;margin-left:3%;" class="ym-gl" >
                                <?php echo $item->LabelValue; ?>
                                <div class="ym-clearfix"></div>
                                <input type="text" class="input-txt center-align" style="text-indent: 0; font-size: 16px; height: 40px;" readonly="readonly" <?php if (array_key_exists($item->ActualValue, $postbox_count)) { ?> value="<?php echo $postbox_count[$item->ActualValue]; ?>" <?php } ?> />
                            </div>
                        <?php } ?>
                        <?php } else {
                            $total_postbox = 0;
                            if (array_key_exists(APConstants::ENTERPRISE_CUSTOMER, $postbox_count)) {
                                $total_postbox += $postbox_count[APConstants::ENTERPRISE_CUSTOMER];
                            }
                            ?>
                            <div style="width:30%;margin-left:3%;" class="ym-gl" >
                                <div class="ym-clearfix"></div>
                                <input type="text" class="input-txt center-align" style="text-indent: 0; font-size: 16px; height: 40px;" readonly="readonly" value="<?php echo $total_postbox;?>" />
                            </div>
                        <?php } ?>
                    </div>
                    <div class="ym-gl left-2" style="width:26%;line-height:25px; <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { echo "margin-top: 50px;"; } else { echo "margin-top: 10px;";} ?>">
                        <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { ?>
                         <a id="changeMyAccountTypeLink" class="main_link_color"><?php language_e('account_view_postbox_setting_ChangePostboxType'); ?></a>
                        <?php } ?>
                        <input type="hidden" value="<?php echo $customer->activated_flag ?>" id="activatedFlagId" name="activatedFlag" />
                        <a id="delPostboxLink" class="main_link_color"><?php language_e('account_view_postbox_setting_SelectPostDelete'); ?></a>
                    </div>
                    <div class="ym-clearfix"></div>
                </div>
                <div class="ym-clearfix"></div>

                <div style="margin:0px 0px; <?php if ($customer_product_setting['postbox_name_flag'] != '1' && $customer->activated_flag == APConstants::OFF_FLAG) { ?> visibility: hidden;  <?php } ?>">
                    <div class="ym-gl left-3" style="width:24%;line-height:25px;text-align:right;">
                        <?php language_e('account_view_postbox_setting_Add'); ?>:
                    </div>
                    <div class="ym-gl" style="width:45%;">
                        <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER) { ?>
                            <?php
                            foreach ($acct_type as $item) {
                                ?>
                                <div style="width:30%;margin-left:3%;" class="ym-gl">
                                    <a class="add" rel="<?php echo $item->ActualValue; ?>"><?php language_e('account_view_postbox_setting_Add'); ?></a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div style="width:30%;margin-left:3%;" class="ym-gl">
                                <a class="add" rel="5"><?php language_e('account_view_postbox_setting_Add'); ?></a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="ym-gl left-2" style="width:28%;line-height:50px;">
                        &nbsp;
                    </div>
                    <div class="ym-clearfix"></div>
                </div>
                <div class="ym-clearfix"></div>

                <!-- added #471 -->
                <?php if ($customer->account_type == APConstants::NORMAL_CUSTOMER
                        && $business_postbox_count == 0 && $customer->activated_flag == 1): ?>
                    <div class="ym-gl">
                        <?php if ($postbox && !empty($postbox->business_postbox_text)) { ?>
                            <a id="btnUpgradePostbox" style="width: 400px" type="button" class="button_upgrade btn-yellow"><?php echo $postbox->business_postbox_text; ?></a>
                        <?php } else { ?>
                            <a id="btnUpgradePostbox" style="width: 400px" type="button" class="button_upgrade btn-yellow"><?php language_e('account_view_postbox_setting_UpgradePostbox'); ?></a>
                        <?php } ?>
                    </div>
                    <div class="ym-clearfix"></div>
                    <br />
                <?php endif; ?>

                <?php if ($customer->account_type == APConstants::ENTERPRISE_CUSTOMER): ?>
                <div class="ym-gl">
                    <a rel="3" style="position: absolute; width: 400px; text-align: center;" id="btnAddPostboxAdvanced" type="button" class="button_upgrade  btn-yellow"><?php language_e('account_view_postbox_setting_AddAnEnterpriseBoxGuesst'); ?></a>
                </div>
                <?php endif; ?>
                <div class="ym-clearfix" style="clear:both"></div>
                <br/>
                <br/>
                <div class="ym-gl">
                    <h4 class="COLOR_063"><?php language_e('account_view_postbox_setting_ForwardingAddress'); ?></h4>
                <div >
                    <form id="saveAddressForm" action="<?php echo base_url() . 'account/save_address'; ?>" method="post">
                        <div class="ym-grid" >
                            <table border="0" style="border:none; width: 100%" class="invoice_address_table">
                                <tr>
                                    <td width="100px;vertical-align: middle;" valign ="middle"><label><?php language_e('account_view_postbox_setting_Name'); ?>:</label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_address_name" id="shipment_address_name" type="text" value="<?php if ($address) {
                                            echo $address->shipment_address_name;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_Company'); ?>:</label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_company" id="shipment_company" type="text" value="<?php if ($address) {
                                            echo $address->shipment_company;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_Street'); ?>: <span class="required">*</span></label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_street" id="shipment_street" type="text" value="<?php if ($address) {
                                            echo $address->shipment_street;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_PostCode'); ?>: <span class="required">*</span></label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_postcode" id="shipment_postcode" type="text" value="<?php if ($address) {
                                            echo $address->shipment_postcode;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_City'); ?>: <span class="required">*</span></label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_city" id="shipment_city" type="text" value="<?php if ($address) {
                                            echo $address->shipment_city;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_Region'); ?>: <span class="required">*</span></label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_region" id="shipment_region" type="text" value="<?php if ($address) {
                                            echo $address->shipment_region;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_Country'); ?>: <span class="required">*</span></label></td>
                                    <td>
                                        <select id="shipment_country" name="shipment_country" class="input-text" style="width: 99%; margin-left: 0px;">
                                            <?php
                                            if (!empty($address)) :
                                                foreach ($countries as $country) :
                                                    ?>
                                                <option value="<?php echo $country->id ?>" <?php if ($address->shipment_country == $country->id): ?> selected="selected" <?php endif; ?>>
                                                <?php echo $country->country_name ?>
                                                </option>
                                                <?php endforeach;
                                            else: ?>
                                                <?php
                                                $geo_df = Geolocation::getCountryCode();
                                                foreach ($countries as $country) :
                                                    ?>
                                                <option value="<?php echo $country->id ?>" <?php if (!empty($country->country_code) && strtoupper($country->country_code) == strtoupper($geo_df)): ?> selected="selected" <?php endif; ?>>
                                                <?php echo $country->country_name ?>
                                                                                </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label><?php language_e('account_view_postbox_setting_PhoneNumber'); ?>: </label></td>
                                    <td>
                                        <input class="input-txt" name="shipment_phone_number" id="shipment_phone_number" type="text" value="<?php if ($address) {
                                            echo $address->shipment_phone_number;
                                        } ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <a style="float: left; margin-top: 10px;" class="main_link_color" id="manage_multi_address" href="#"><?php language_e('account_view_postbox_setting_MultipleForwardingAddresses'); ?></a>
                                        <!--<input type="button" id="copyAddressButton" class="input-btn" value="Copy" style="float: right" />-->
                                        <input type="button" id="saveAddressButton" class="input-btn btn-yellow" value="Save" style="float: right" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <div class="ym-g50 ym-gr">
            <form id="saveSettingForm" action="<?php echo base_url().'account/save_settings';?>" method="post">
                <div id="right-account">
                    <table class="border">
                        <thead>
                            <tr>
                                <th colspan="2" class="left-align">
                                    <span style="float: left; width: 100px"><?php language_e('account_view_postbox_setting_Settings'); ?></span>
                                    <span style="float: right;">
                                        <?php if ($main_postbox_setting != null) { ?>
                                        <?php echo my_form_dropdown(array(
                                             "data" => $postboxs,
                                             "value_key" => 'postbox_id',
                                             "label_key" => 'postbox_name',
                                             "value" => $main_postbox_id,
                                             "name" => 'postbox_setting_id',
                                             "id"    => 'postbox_setting_id',
                                             "clazz" => 'input-width',
                                             "style" => 'width: 200px',
                                             "has_empty" => false
                                         ));?>
                                         <?php  } else {?>
                                             <a href="<?php echo APContext::getFullBasePath()?>/mailbox" class="main_link_color" style="font-size: 12px; font-weight: normal;"><?php language_e('account_view_postbox_setting_CreatePostbox'); ?></a>
                                         <?php } ?>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="n">
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysScanEnvelopes'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_scan_envelope" name="always_scan_envelope" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_envelope==1){ ?> checked="checked" <?php }?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysScanIncoming'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_scan_incomming" name="always_scan_incomming" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_incomming==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysScanEnvalopeIfAvaiable'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="envelope_scan" name="envelope_scan" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_envelope_vol_avail==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysScanIncomingIfAvailable'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="scans" name="scans" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_incomming_vol_avail==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_NotifyByEmailWhenScanReady'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox" id="email_scan_notification" name="email_scan_notification" <?php if ($main_postbox_setting && $main_postbox_setting->email_scan_notification==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysForwardItemsDirectly'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_forward_directly" name="always_forward_directly" <?php if ($main_postbox_setting && $main_postbox_setting->always_forward_directly==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysMarkedForwarding'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_forward_collect" name="always_forward_collect" <?php if ($main_postbox_setting && $main_postbox_setting->always_forward_collect==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
        					    <td>
        						    <?php language_e('account_view_postbox_setting_StandardServiceForNationalLetters'); ?>
        					    </td>
        					    <td class="right-align" id="standard_service_national_letter_dropdownlist">
                                    <?php if (!empty($main_postbox_setting->standard_service_national_letter_dropdownlist)) { ?>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $main_postbox_setting->standard_service_national_letter_dropdownlist,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => $main_postbox_setting->standard_service_national_letter,
                                            "name" => 'standard_service_national_letter',
                                            "id"    => 'standard_service_national_letter',
                                            "clazz" => 'input-width',
                                            "style" => 'width: 100%',
                                            "has_empty" => true,
                                            "option_default" => '---Select shipping service---',
                                        ));?>
                                    <?php  } ?>
                                </td>
                            </tr>
                            <tr>
        					    <td>
        						    <?php language_e('account_view_postbox_setting_StandardServiceForInternationalLetters'); ?>
        					    </td>
        					    <td class="right-align" id="standard_service_international_letter_dropdownlist">
                                    <?php if (!empty($main_postbox_setting->standard_service_international_letter_dropdownlist)) { ?>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $main_postbox_setting->standard_service_international_letter_dropdownlist,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => $main_postbox_setting->standard_service_international_letter,
                                            "name" => 'standard_service_international_letter',
                                            "id"    => 'standard_service_international_letter',
                                            "clazz" => 'input-width',
                                            "style" => 'width: 100%',
                                            "has_empty" => true,
                                            "option_default" => '---Select shipping service---',
                                        ));?>
                                    <?php  } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_StandardServiceNationalPackages'); ?>
                                </td>
                                <td class="right-align" id="standard_service_national_package_dropdownlist">
        						    <?php if (!empty($main_postbox_setting->standard_service_national_package_dropdownlist)) { ?>

                                        <?php echo my_form_dropdown(array(
                                            "data" => $main_postbox_setting->standard_service_national_package_dropdownlist,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => $main_postbox_setting->standard_service_national_package,
                                            "name" => 'standard_service_national_package',
                                            "id"    => 'standard_service_national_package',
                                            "clazz" => 'input-width',
                                            "style" => 'width: 100%',
                                            "has_empty" => true,
                                            "option_default" => '---Select shipping service---',
                                        ));?>
                                     <?php  } ?>
                                </td>
                            </tr>
                            <tr>
        					    <td>
        						    <?php language_e('account_view_postbox_setting_StandardServiceInternationalPackages'); ?>
        					    </td>
        					    <td class="right-align" id="standard_service_international_package_dropdownlist">
        						    <?php if (!empty($main_postbox_setting->standard_service_international_package_dropdownlist)) { ?>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $main_postbox_setting->standard_service_international_package_dropdownlist,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => $main_postbox_setting->standard_service_international_package,
                                            "name" => 'standard_service_international_package',
                                            "id"    => 'standard_service_international_package',
                                            "clazz" => 'input-width',
                                            "style" => 'width: 100%',
                                            "has_empty" => true,
                                            "option_default" => '---Select shipping service---',
                                        ));?>
                                     <?php  } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_AlwaysMarkItemsEmailAddress'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input <?php echo empty($main_postbox_setting->accounting_email) ? 'disabled' : '' ?> data-accounting_email="<?php echo empty($main_postbox_setting->accounting_email) ? '' : $main_postbox_setting->accounting_email ?>" type="checkbox" class="customCheckbox tipsy_tooltip" id="always_mark_invoice" name="always_mark_invoice" <?php if ($main_postbox_setting && $main_postbox_setting->always_mark_invoice==1){ ?> checked="checked" <?php }?> /></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_InformMeByEmailWhenItemTrashed'); ?>
                                </td>
                                <td class="right-align" style="padding: 13px"><input type="checkbox" class="customCheckbox" id="inform_email_when_item_trashed" name="inform_email_when_item_trashed" <?php if ($main_postbox_setting && $main_postbox_setting->inform_email_when_item_trashed==1){ ?> checked="checked" <?php }?>/></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_EmailNotificationForIncoming'); ?>
                                </td>
                                <td class="right-align">
                                    <?php echo code_master_form_dropdown(array(
                                                                    "code" => APConstants::EMAIL_NOTIFICATION_CODE,
                                                                    "value" => $main_postbox_setting != null ? $main_postbox_setting->email_notification: '',
                                                                    "name" => 'email_notification',
                                                                    "id"    => 'email_notification',
                                                                    "clazz" => 'input-width',
                                                                    "style" => 'width: 110px',
                                                                    "has_empty" => false
                                                             ));?>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td>
                                    <?php language_e('account_view_postbox_setting_InvoicingCycle'); ?>
                                </td>
                                <td class="right-align">
                                    <?php echo code_master_form_dropdown(array(
                                                                "code" => APConstants::INVOICING_CYCLE_CODE,
                                                                "value" => $main_postbox_setting != null ? $main_postbox_setting->invoicing_cycle : '',
                                                                "name" => 'invoicing_cycle',
                                                                "id"    => 'invoicing_cycle',
                                                                "clazz" => 'input-width',
                                                                "style" => 'width: 110px',
                                                                "has_empty" => false
                                     ));?>
                                </td>
                            </tr>
                            <tr>
                            <td>
                                <?php language_e('account_view_postbox_setting_AutomaticallyActivateForwardingMmarkedForwarding'); ?>
                            </td>
                            <td class="right-align">
                                <?php echo code_master_form_dropdown(array(
                                     "code" => APConstants::COLLECT_ITEMS_SHIPPING_CODE,
                                     "value" => $main_postbox_setting != null ? $main_postbox_setting->collect_mail_cycle: '2',
                                     "name" => 'collect_mail_cycle',
                                     "id"    => 'collect_mail_cycle',
                                     "clazz" => 'input-width',
                                     "style" => 'width: 110px',
                                     "has_empty" => false
                                 ));?>
                            </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php language_e('account_view_postbox_setting_WeekdayShipping'); ?>
                                </td>
                                <td class="right-align">
                                    <?php echo code_master_form_dropdown(array(
                                                                    "code" => APConstants::WEEKDAY_SHIPPING_CODE,
                                                                    "value" => $main_postbox_setting != null ? $main_postbox_setting->weekday_shipping : '',
                                                                    "name" => 'weekday_shipping',
                                                                    "id"    => 'weekday_shipping',
                                                                    "clazz" => 'input-width',
                                                                    "style" => 'width: 110px',
                                                                    "has_empty" => false
                                                                ));?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php language_e('account_view_postbox_setting_YourShipmentTriggeredOn'); ?> <span id="next_collect_shipping"><?php if(is_object($main_postbox_setting) && !empty($main_postbox_setting)){ echo $main_postbox_setting->next_collect_date;}?></span>
                                </td>
                            </tr>
                                           <?php if($customer->auto_trash_flag == "1"){?>
                                            <tr>
                                <td>
                                                        <?php language_e('account_view_postbox_setting_AutoTrashAllIncomingItemsAfter'); ?> <input id="trash_after_day" name="trash_after_day" style="width: 56px;height: 30px; background: #FFF;" value="<?php echo (!empty($main_postbox_setting)) ? $main_postbox_setting->trash_after_day: ''; ?>" class="input-txt" maxlength="3" type="text"> <?php language_e('account_view_postbox_setting_days'); ?>:
                                </td>
                                <td class="right-align">
                                <input type="checkbox" class="customCheckbox tipsy_tooltip" id="auto_trash_flag" name="auto_trash_flag" <?php if ($main_postbox_setting && $main_postbox_setting->auto_trash_flag==1){ ?> checked="checked" <?php }?>/>
                                </td>
                            </tr>
                                           <?php } ?>
                        </tbody>
                    </table>
                    <div class="ym-gr ym-g80" style="text-align: right; margin-top: 10px; <?php if($customer->auto_trash_flag == "1"){?> margin-bottom: 50px; <?php } ?>">
                        <input type="button" id="saveSettingButton"  class="input-btn  btn-yellow" value="Save" /><br/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="ym-clearfix"></div>
<br />
<br />
<!-- Content for dialog -->
<div class="hide">
    <div id="changeMyPassWindow" title="Change My Password" class="input-form dialog-form">
    </div>
    <div id="changeMyEmailWindow" title="Change My Email" class="input-form dialog-form">
    </div>
    <div id="changeMyAccountTypeWindow" title="Change Postbox Type" class="input-form dialog-form">
    </div>
    <div id="addPostboxWindow" title="Add Postbox" class="input-form dialog-form">
    </div>
    <div id="delPostboxWindow" title="Delete Postbox" class="input-form dialog-form">
    </div>
    <div id="delPostboxConfirmWindow" title="Confirm Delete Postbox" class="input-form dialog-form">
    </div>
    <div id="deletePrivateAndBusinessPostboxConfirmDialog" title="confirmation" class="input-form dialog-form">
    </div>
    <div id="make_prepayment_dialog" title="Make a Deposit/Pre-Payment" class="input-form dialog-form"></div>
    <div id="priceInfoWindow" title="Price Information" class="input-form dialog-form"></div>

    <div id="forward_address" title="Forwarding Address Book" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/Account.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});
        var free_postbox_price = '<?php echo $pricing_map[1]['postbox_fee']->item_value; ?>';
        var private_postbox_price = '<?php echo $pricing_map[2]['postbox_fee']->item_value; ?>';
        var business_postbox_price = '<?php echo $pricing_map[3]['postbox_fee']->item_value; ?>';
		$('#display_payment_confirm').fancybox({
            width: 500,
            height: 300
        });
        Account.init('<?php echo base_url(); ?>');

        // add additional fowarding address event
        $("#manage_multi_address").click(function (e) {
            e.preventDefault();
            openManageAddressWindow();

            return false;
        });

        /** START SOURCE TO manage address */
        <?php include 'system/virtualpost/modules/addresses/js/js_manage_address.php'; ?>
        /** START SOURCE TO manage address */


        $('.tipsy_tooltip').tipsy({gravity: 'sw'});
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('span.jquery-safari-checkbox').css('height', '15px');

        // save address button
        $('#saveAddressButton').click(function () {
            var submitUrl = $('#saveAddressForm').attr('action');
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'saveAddressForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            // Reload data grid
                            document.location.href = '<?php echo base_url() ?>account/postbox_setting';
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        });

        $('#always_mark_invoice').on('click', function(){
            var accountingEmail = $('#always_mark_invoice').data('accounting_email');
            if (!accountingEmail) {
                $.displayInfor("<?php language_e('account_view_postbox_setting_InvoicingCycleMessage'); ?>");
            }
        });
    });
</script>