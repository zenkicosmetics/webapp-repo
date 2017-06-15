<style>
    #standard_service_national_letter, #standard_service_international_letter, #standard_service_national_package, #standard_service_international_package
    {
        width: 50% !important;
        float: right;
    }
</style>
<table width="1011" height="4616" border="0" align="center" cellpadding="0" cellspacing="0" id="tbl_wrapper">
	<tr>
		<td class="tdbox" style="padding-top: 0px;">
		
        <table id="Table_01" width="1011" border="0" cellpadding="0" cellspacing="0">

            <tr>
                <td style="padding-top: 6px;">
                    <h2 class="title_h2"><?php language_e('info_view_howitwork_HowItWorkTitle'); ?></h2>
                    <p style="margin-top:4px;"><?php language_e('info_view_howitwork_HowItWorkDescription'); ?></p>
                    <!----------------------Start listpoxtbox ------------------------>       
                    <p>

                        <table class="listPostbox">
                            <thead class="mn">
                                <tr>
                                    <th class="center-align" style="display: none;border-left: solid 1px #dedede;"><?php language_e('info_view_howitwork_ColumnID'); ?></th>
                                    <th style="border-left: solid 1px #dedede;" class="center-align"><?php language_e('info_view_howitwork_ColumnPostboxID'); ?></th>
                                    <th class="center-align"><?php language_e('info_view_howitwork_ColumnType'); ?></th>
                                    <th class="center-align"><?php language_e('info_view_howitwork_ColumnName'); ?></th>
                                    <th class="center-align"><?php language_e('info_view_howitwork_ColumnCompany'); ?></th>
                                    <th class="center-align"><?php language_e('info_view_howitwork_ColumnLocation'); ?></th>
                                    <th style="border-right: solid 1px #dedede;" class="center-align"></th>
                                </tr>
                            </thead>
                            <tbody class="nm">
                                    <?php
                                    if (count($list_postbox) > 0) {
                                        $k=0;
                                        foreach ($list_postbox as $p) {
                                            $verification_flag = 1;
                                            if ($p->name_verification_flag == 0 || $p->company_verification_flag == 0) {
                                                $verification_flag = 0;
                                            }
                                    ?>
                                    <?php if($k==0){ ?>
                                    <tr><td style="border-left: solid 1px #dedede;border-right: solid 1px #dedede;" colspan="7"></td>
                                    </tr>
                                    <?php } ?>
                                    <tr style="">
                                        <td style="display: none;border-left: solid 1px #dedede;">
                                            <input class="input-txt-none" <?php if ($verification_flag == 0) { ?> style="margin-top: 18px;" <?php } ?> type="text" 
                                                   name="postbox_id<?php echo $p->postbox_id; ?>" value="<?php echo $p->postbox_id; ?>" />
                                        </td>
                                        <td class="center-align" style="padding-bottom:13px;border-left: solid 1px #dedede;">
                                            <input class="input-txt-none" <?php if ($verification_flag == 0) { ?> style="" <?php } ?> type="text" maxlength="35" 
                                                   name="postbox_name<?php echo $p->postbox_id; ?>" value="<?php echo $p->postbox_name; ?>" />
                                        </td>
                                        <td class="center-align" style="padding-bottom:13px;">
                                            <div class="slb-custom" <?php if ($verification_flag == 0) { ?> style="" <?php } ?>>
                                            <?php
                                                echo code_master_form_dropdown(
                                                        array(
                                                            "code" => APConstants::ACCOUNT_TYPE,
                                                            "value" => $p->type,
                                                            "name" => 'type' . $p->postbox_id,
                                                            "id" => 'type' . $p->postbox_id,
                                                            "clazz" => '',
                                                            "style" => 'height: 25px',
                                                            "has_empty" => false
                                                ));
                                            ?>
                                            </div>
                                        </td>
                                        <td style="width: 176px;" class="center-align">
                                            <?php if (($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '')) { ?>
                                                <div class="link_verify_name"><?php language_e('info_view_howitwork_NeedsVerification'); ?> – 
                                                    <a href="<?php echo base_url() ?>cases/services?case=verification"><?php language_e('info_view_howitwork_VerifyNow'); ?></a></div>
                                            <?php } ?>
                                            <?php
                                            $checkMarginName = false;
                                            if ((!(($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '') )) 
                                                    && ( ($customer->required_verification_flag == 1) && ($p->company_verification_flag != 1) && ($p->company != '') ) || ($customer->required_verification_flag == 0)
                                            ) {
                                                $checkMarginName = true;
                                            }

                                            $checkMarginCompany = false;
                                            if (((($customer->required_verification_flag == 1) && ($p->name_verification_flag != 1) && ($p->name != '') )) && (!( ($customer->required_verification_flag == 1) 
                                                    && ($p->company_verification_flag != 1) && ($p->company != '') ))
                                            ) {
                                                $checkMarginCompany = true;
                                            }
                                            ?>
                                            <?php  if (($customer->required_verification_flag == 1) && ($p->name_verification_flag == 1) && ($p->name != '')) { ?>
                                            <div class="wrapper_name">
                                                <span class=""><img title="<?php language_e('info_view_howitwork_ThisPostboxHasBeenVerified'); ?>" class="tipsy_tooltip" src="<?php echo APContext::getImagePath() ?>/checkmark.png" /></span>
                                            <?php } ?>
                                                <input class="input-txt-none name"  style="background: #daebee;" type="text" name="name<?php echo $p->postbox_id; ?>" rel="<?php echo $p->postbox_id; ?>" value="<?php echo $p->name; ?>" />
                                            </div> <!-- end div.warraper-->
                                        </td>
                                        <td style="width: 180px; <?php if ($customer->required_verification_flag == 0) echo "padding-bottom: 13px;" ?>" class="left-align">
                                        <?php  if (($customer->required_verification_flag == 1) && ($p->company_verification_flag != 1) && ($p->company != '')) { ?>
                                                    <div class="link_verify_company"><?php language_e('info_view_howitwork_NeedsVerification'); ?> – <a href="<?php echo base_url() ?>cases/services?case=verification"> <?php language_e('info_view_howitwork_VerifyNow'); ?></a></div>
                                                <?php } ?>
                                                    <div class="wrapper_company">
                                                    <?php
                                                    if (($customer->required_verification_flag == 1) && ($p->company_verification_flag == 1) && ($p->company != '')) {
                                                        ?>
                                                        <span><img title="<?php language_e('info_view_howitwork_ThisPostboxHasBeenVerified'); ?>" class="tipsy_tooltip" src="<?php echo APContext::getImagePath() ?>/checkmark.png" /></span>
                                        <?php } ?>
                                                        <input class="input-txt-none company" type="text" name="company<?php echo $p->postbox_id; ?>" rel="<?php echo $p->postbox_id; ?>" value="<?php echo $p->company; ?>" style="width: 80%;background: #daebee;"/>
                                                    </div>
                                        </td>
                                        <td style="" class="center-align">
                                            <div>
                                        <?php
                                        $location_name = "";
                                        // Gets location
                                        foreach ($locate as $l) {
                                            if ($l->id == $p->location_available_id) {
                                                $location_name = $l->location_name;
                                                break;
                                            }
                                        }
                                        ?>
                                                <input style="background: #e3e3e3;" type="text" readonly="readonly" class="input-txt-none readonly" disabled="disabled" value="<?php echo $location_name; ?>" />
                                            </div>
                                        </td>
                                        <td style="border-right: solid 1px #dedede;">
                                            <input style="" type="button" title="<?php language_e('info_view_howitwork_ShowMailingAddress'); ?>" class="input-btn show_mailing_address" value="Show" data-id="<?php echo $p->postbox_id ?>" data-location_available_id="<?php echo $p->location_available_id ?>" />
                                        </td>
                                    </tr>
                                    <?php $k++;} // end foreach ?>

                                <?php } ?>
                            </tbody>
                        </table>
                    </p>

                    <p><?php language_e('info_view_howitwork_HowItWorkDescription2'); ?></p>
                    <p><b><a href="./addresses"> <?php language_e('info_view_howitwork_ChangeSetting'); ?></a></b></p>
                </td>
            </tr>

        </table>
        
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
        	<table id="Table_03" width="1011" height="256" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p><?php language_e('info_view_howitwork_PostboxActivitiesTitle'); ?></p>
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/mailbox.jpg" alt="">
                        <p style="line-height: 24px; margin-top: 8px;"><?php language_e('info_view_howitwork_PostboxActivitiesDescription'); ?></p>
                    </td>
                </tr>
            </table>
        
        </td>
	</tr>
	<tr>
		<td class="tdseparate">
		</td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="Table_01" width="1011" height="338" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="line-height: 22px;"><?php language_e('info_view_howitwork_ActivityButtonsTitle'); ?></td>
                </tr>
                <tr>
                    <td>
                        <table id="Table_04" width="1011" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="64">
                                    <img src="<?php echo APContext::getAssetPath(); ?>images/info/envelope.jpg" alt=""></td>
                                <td class="tdright_box_activit" valign="middle">
                                    <p><?php language_e('info_view_howitwork_ActivityScanEnvelope'); ?></p></td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?php echo APContext::getAssetPath(); ?>images/info/item.jpg" alt=""></td>
                                <td class="tdright_box_activit">
                                    <p><?php language_e('info_view_howitwork_ActivityScanItem'); ?></p></td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?php echo APContext::getAssetPath(); ?>images/info/direct.jpg"  alt=""></td>
                                <td class="tdright_box_activit">
                                <p><?php language_e('info_view_howitwork_ActivityDirectShipment'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?php echo APContext::getAssetPath(); ?>images/info/collect.jpg" alt=""></td>
                                <td>
                                    <p><?php language_e('info_view_howitwork_ActivityCollectShipment'); ?></p>
                                    </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="<?php echo APContext::getAssetPath(); ?>images/info/trash.jpg" alt=""></td>
                                <td>
                                    <p><?php language_e('info_view_howitwork_TrashItem'); ?></p>
                                    </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox" style="padding-left: 20px; padding-top: 18px;">
			<p><?php language_e('info_view_howitwork_TriggerCollectShipmentTitle'); ?></p>
            <p style="margin-top: -6px;"><?php language_e('info_view_howitwork_TriggerCollectShipmentDescription1'); ?></p>
            <p>
                <img src="<?php echo APContext::getAssetPath(); ?>images/info/auto_mark_collect.jpg" ><br/>
                <img src="<?php echo APContext::getAssetPath(); ?>images/info/auto_active1.jpg" >
            </p>
            <p><?php language_e('info_view_howitwork_TriggerCollectShipmentDescription2'); ?></p>
            <input id="btn_collect_shipping" class="input-btn c yl" value="Collect forwarding" style="width: 194px; margin-left: 20px;margin-bottom: 0px;" type="button">
            <p><?php language_e('info_view_howitwork_TriggerCollectShipmentDescription3'); ?></p>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="Table_09" width="1011" height="375" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                    <b><?php language_e('info_view_howitwork_IconColors'); ?></b>
                  </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/direct_yellow.jpg"  alt=""></td>
                            <td style="padding-top: 10px;">
                    <p><?php language_e('info_view_howitwork_IconColorsYellow'); ?></p>
                  </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/item_blue.jpg" alt="">
                    </td>
                    <td style="padding-top: 24px;">
                        <p><?php language_e('info_view_howitwork_IconColorsBlue'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/direct_red.jpg" ></td>
                    <td style="padding-top: 24px;">
                        <p><?php language_e('info_view_howitwork_IconColorsRed'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/collect_green.jpg" >
                    </td>
                    <td>
                        <p><?php language_e('info_view_howitwork_IconColorsGreen'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/direct_orange.jpg">
                    </td>
                    <td style="padding-top: 16px;">
                        <p><?php language_e('info_view_howitwork_IconColorsOrange'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate">
		</td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="Table_01" width="1011" height="248" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                    <p><b><?php language_e('info_view_howitwork_OtherFunctions'); ?></b></p></td>
                </tr>
                <tr>
                    <td>
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/cloud.jpg">
                    </td>
                    <td>
                        <p><?php language_e('info_view_howitwork_OtherCloud'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/category.jpg">
                    </td>
                    <td>
                        <p><?php language_e('info_view_howitwork_OtherCategory'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo APContext::getAssetPath(); ?>images/info/@.jpg">
                    </td>
                    <td valign="middle">
                        <p><?php language_e('info_view_howitwork_OtherEmailInterface'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="tbl_automation_rules" width="1011" height="882" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <b><?php language_e('info_view_howitwork_AutomationRuleTitle'); ?></b><br/>
                        <p style="margin-top: 6px; margin-bottom: 0px;"><?php language_e('info_view_howitwork_AutomationRuleDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td width="476">
                                <div class="wrap_postbox_setting">
                                    <b style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_PostboxSetting'); ?></b>
                                    <span style="float: right;">
                                    <?php if ($main_postbox_setting != null) { 
                                        echo my_form_dropdown(array(
                                                "data" => $postboxs,
                                                "value_key" => 'postbox_id',
                                                "label_key" => 'postbox_name',
                                                "value" => $main_postbox_id,
                                                "name" => 'postbox_setting_id',
                                                "id"    => 'postbox_setting_id',
                                                "clazz" => 'input-width',
                                                "style" => 'margin-top: 6px; margin-right: 18px; width: 130px; border-radius: 0px;',
                                                "has_empty" => false
                                        )); 
                                    } else {?>
                                        <a href="<?php echo APContext::getFullBasePath()?>/mailbox" style="color: #0E76BC;font-size: 12px; font-weight: normal;">
                                            <?php language_e('info_view_howitwork_CreatePostbox'); ?></a>
                                    <?php } ?>
                                    </span>
                                </div>

                    </td>
                    <td class="td_right_tbl_automation_rules" style="padding-top: 18px;">
                        <p><?php language_e('info_view_howitwork_SelectPostboxToChangeSetting'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_AlwaysScanEnvelopes'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_scan_envelope" 
                                name="always_scan_envelope" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_envelope==1){ ?> checked="checked" <?php }?> /></span>
                        </div>    
                     </td>
                    <td class="td_right_tbl_automation_rules" style="padding-top: 18px;">
                        <p><?php language_e('info_view_howitwork_AlwaysScanEnvelopesDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_AlwaysScanItem'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_scan_incomming" 
                                name="always_scan_incomming" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_incomming==1){ ?> checked="checked" <?php }?>/></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules" style="padding-top: 18px;">
                        <p><?php language_e('info_view_howitwork_AlwaysScanItemDesciption'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_AlwaysScanEnvelopeVolAvail'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="envelope_scan" 
                                name="envelope_scan" <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_envelope_vol_avail==1){ ?> checked="checked" <?php }?> /></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_AlwaysScanEnvelopeVolAvailDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"> <?php language_e('info_view_howitwork_AlwaysScanItemVolAvail'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="scans" name="scans" 
                                <?php if ($main_postbox_setting && $main_postbox_setting->always_scan_incomming_vol_avail==1){ ?> checked="checked" <?php }?>/></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_AlwaysScanItemVolAvailDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"> <?php language_e('info_view_howitwork_NotifyWhenScanItem'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox" id="email_scan_notification" name="email_scan_notification"
                                <?php if ($main_postbox_setting && $main_postbox_setting->email_scan_notification==1){ ?> checked="checked" <?php }?>/></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules" style="padding-top: 18px;">
                        <p><?php language_e('info_view_howitwork_NotifyWhenScanItemDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                           <span style="position: absolute; margin-top: 12px; margin-left: 10px;"> <?php language_e('info_view_howitwork_AlwaysDirectForwarding'); ?></span>
                           <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_forward_directly" name="always_forward_directly" 
                               <?php if ($main_postbox_setting && $main_postbox_setting->always_forward_directly==1){ ?> checked="checked" <?php }?>/></span>
                       </div>
                   </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_AlwaysDirectForwardingDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"> <?php language_e('info_view_howitwork_AlwaysMarkCollectForwarding'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_forward_collect" 
                                name="always_forward_collect" <?php if ($main_postbox_setting && $main_postbox_setting->always_forward_collect==1){ ?> checked="checked" <?php }?>/></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_AlwaysMarkCollectForwardingDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_NationalLetterStandardService'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;" id="standard_service_national_letter_dropdownlist">
                                <?php if (!empty($main_postbox_setting->standard_service_national_letter_dropdownlist)) { ?>
                                    <?php echo my_form_dropdown(array(
                                                "data" => $main_postbox_setting->standard_service_national_letter_dropdownlist,
                                                "value_key" => 'id',
                                                "label_key" => 'name',
                                                "value" => $main_postbox_setting->standard_service_national_letter,
                                                "name" => 'standard_service_national_letter',
                                                "id"    => 'standard_service_national_letter',
                                                "clazz" => 'input-width',
                                                "style" => 'width: 50%; float: right;',
                                                "has_empty" => true,
                                                "option_default" => language('info_view_howitwork_SelectShippingServiceDdl'),
                                    ));?>
                                <?php  } ?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_NationalLetterStandardServiceDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_InternationalLetterStandardService'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;" id="standard_service_international_letter_dropdownlist">
                                <?php if (!empty($main_postbox_setting->standard_service_international_letter_dropdownlist)) { ?>
                                    <?php echo my_form_dropdown(array(
                                                "data" => $main_postbox_setting->standard_service_international_letter_dropdownlist,
                                                "value_key" => 'id',
                                                "label_key" => 'name',
                                                "value" => $main_postbox_setting->standard_service_international_letter,
                                                "name" => 'standard_service_international_letter',
                                                "id"    => 'standard_service_international_letter',
                                                "clazz" => 'input-width',
                                                "style" => 'width: 50%; float: right;',
                                                "has_empty" => true,
                                                "option_default" => language('info_view_howitwork_SelectShippingServiceDdl'),
                                    ));?>
                                <?php  } ?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_InternationalLetterStandardServiceDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_NationalPackageStandardService'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;" id="standard_service_national_package_dropdownlist">
                                 <?php if (!empty($main_postbox_setting->standard_service_national_package_dropdownlist)) { ?>
                                    <?php echo my_form_dropdown(array(
                                                "data" => $main_postbox_setting->standard_service_national_package_dropdownlist,
                                                "value_key" => 'id',
                                                "label_key" => 'name',
                                                "value" => $main_postbox_setting->standard_service_national_package,
                                                "name" => 'standard_service_national_package',
                                                "id"    => 'standard_service_national_package',
                                                "clazz" => 'input-width',
                                                "style" => 'width: 50%; float: right;',
                                                "has_empty" => true,
                                                "option_default" => language('info_view_howitwork_SelectShippingServiceDdl'),
                                    ));?>
                                <?php  } ?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_NationalPackageStandardServiceDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_InternationalPackageStandardService'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;" id="standard_service_international_package_dropdownlist">
                                 <?php if (!empty($main_postbox_setting->standard_service_international_package_dropdownlist)) { ?>
                                    <?php echo my_form_dropdown(array(
                                                "data" => $main_postbox_setting->standard_service_international_package_dropdownlist,
                                                "value_key" => 'id',
                                                "label_key" => 'name',
                                                "value" => $main_postbox_setting->standard_service_international_package,
                                                "name" => 'standard_service_international_package',
                                                "id"    => 'standard_service_international_package',
                                                "clazz" => 'input-width',
                                                "style" => 'width: 50%; float: right;',
                                                "has_empty" => true,
                                                "option_default" => language('info_view_howitwork_SelectShippingServiceDdl'),
                                    ));?>
                                <?php  } ?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_InternationalPackageStandardServiceDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_MarkEmailInterface'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox tipsy_tooltip" id="always_mark_invoice" 
                                name="always_mark_invoice" <?php if ($main_postbox_setting && $main_postbox_setting->always_mark_invoice==1){ ?> checked="checked" <?php }?>/></span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_MarkEmailInterfaceDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                           <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_NotifyWhenTrash'); ?></span>
                           <span style="float: right; margin-right: 14px; margin-top: 8px;"><input type="checkbox" class="customCheckbox" id="inform_email_when_item_trashed" 
                                name="inform_email_when_item_trashed" <?php if ($main_postbox_setting && $main_postbox_setting->inform_email_when_item_trashed==1){ ?> checked="checked" <?php }?>/></span>
                       </div>
                   </td>
                    <td class="td_right_tbl_automation_rules" style="padding-top: 18px;">
                        <p><?php language_e('info_view_howitwork_NotifyWhenTrashDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_NotifyIncomingItem'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;">
                                <?php echo code_master_form_dropdown(array(
                                        "code" => APConstants::EMAIL_NOTIFICATION_CODE,
                                        "value" => $main_postbox_setting != null ? $main_postbox_setting->email_notification: '',
                                        "name" => 'email_notification',
                                        "id"	=> 'email_notification',
                                        "clazz" => 'input-width',
                                        "style" => 'width: 110px',
                                        "has_empty" => false
                                 ));?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_NotifyIncomingItemDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 2px; margin-left: 10px;"><?php language_e('info_view_howitwork_AutoTriggerCollectForwarding'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;">
                                <?php echo code_master_form_dropdown(array(
                                        "code" => APConstants::COLLECT_ITEMS_SHIPPING_CODE,
                                        "value" => $main_postbox_setting != null ? $main_postbox_setting->collect_mail_cycle: '2',
                                        "name" => 'collect_mail_cycle',
                                        "id"	=> 'collect_mail_cycle',
                                        "clazz" => 'input-width',
                                        "style" => 'width: 110px',
                                        "has_empty" => false
                                    ));?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_AutoTriggerCollectForwardingDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="wrap_postbox_setting">
                            <span style="position: absolute; margin-top: 12px; margin-left: 10px;"><?php language_e('info_view_howitwork_WeekdayForShipping'); ?></span>
                            <span style="float: right; margin-right: 14px; margin-top: 5px;">
                                <?php echo code_master_form_dropdown(array(
                                            "code" => APConstants::WEEKDAY_SHIPPING_CODE,
                                            "value" => $main_postbox_setting != null ? $main_postbox_setting->weekday_shipping : '',
                                            "name" => 'weekday_shipping',
                                            "id"	=> 'weekday_shipping',
                                            "clazz" => 'input-width',
                                            "style" => 'width: 110px',
                                            "has_empty" => false
                                        ));?>
                            </span>
                        </div>
                    </td>
                    <td class="td_right_tbl_automation_rules">
                        <p><?php language_e('info_view_howitwork_WeekdayForShippingDescription'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox box_manage_postbox">
            <table id="Table_01" width="1011" height="322box_manage_postbox" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2"><b><?php language_e('info_view_howitwork_ManageYourAccount'); ?></b></td>
                </tr>
                <tr>
                    <td width="432" style="padding-top: 0px;">
                        <table id="Table_01" width="432"  border="0" cellpadding="0" cellspacing="0">
                            <tr style="position: absolute; margin-top: -8px;">
                                <td style="width: 152px;padding-top: 20px;"><?php language_e('info_view_howitwork_Username'); ?></td>
                                <td style="width: 270px;">
                                    <input style="margin-left: 0px;" type="text" value="<?php echo $info->email;?>" readonly="readonly" class="input-txt readonly" />
                                    <div class="ym-gl" style="margin-top: 2px;">
                                        <a id="changeMyEmailAddressLink"><?php language_e('info_view_howitwork_ChangeEmailAddress'); ?></a>
                                        <br/>
                                        <a id="changeMyPasswordLink"><?php language_e('info_view_howitwork_ChangePassword'); ?></a>
                                        <?php if ($info->email_confirm_flag == '0') {?>
                                        <br/>
                                        <a id="resendEmailConfirm"><?php language_e('info_view_howitwork_ResendEmailConfirm'); ?></a>
                                        <?php }?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-top: 0px;">
                        <p style="margin-top: -4px;"><?php language_e('info_view_howitwork_ChangeEmailDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 0px;padding-top: 20px;">
                        <table id="Table_01"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-top: 0px;">
                                    <p style="margin-bottom: 0px;"><?php language_e('info_view_howitwork_SelectCurrency'); ?><p>
                                </td>
                                <td style="padding-right: 0px;">
                                    <?php echo my_form_dropdown(array(
                                            "data" => $currencies,
                                            "value_key" => 'currency_id',
                                            "label_key" => 'currency_short',
                                            "value" => $selected_currency_id,
                                            "name" => 'currency_id',
                                            "id"	=> 'currency_id',
                                            "clazz" => 'input-width',
                                            "style" => 'float:left; width: 110px;',
                                            "has_empty" => false
                                    )); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td valign="top" style="padding-top: 12px;">
                        <p><?php language_e('info_view_howitwork_SelectCurrencyDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 0px;padding-top: 0px; padding-bottom: 0px;">
                        <table id="Table_01"  width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-top: 6px;">
                                        <p><?php language_e('info_view_howitwork_DecimalSeparator'); ?></p>
                                </td>
                                <td style="padding-right: 0px;padding-top: 0px;">
                                    <select id="decimal_separator" name="decimal_separator" class="input-width" style="float:left; width: 110px;float: right;">
                                        <option value="," <?php echo ($decimal_separator == APConstants::DECIMAL_SEPARATOR_COMMA)? 'selected' : ''; ?>><?php language_e('info_view_howitwork_Comma'); ?></option>
                                        <option value="." <?php echo ($decimal_separator == APConstants::DECIMAL_SEPARATOR_DOT)? 'selected' : ''; ?>><?php language_e('info_view_howitwork_Dot'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-top: 6px;padding-bottom: 0px;"><p><?php language_e('info_view_howitwork_DecimalSeparatorDescription'); ?></p></td>
                </tr>
                <tr>
                    <td>
                        <table id="Table_01"  border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width: 32px;padding-left: 0px;">
                                        <input id="auto_send_invoice_flag" type="checkbox" class="customCheckbox" value="1" <?php if ($info->auto_send_invoice_flag == '1') {?> checked="checked"<?php }?> > 
                                    </td>
                                    <td style="padding-top: 15px; padding-left: 0px;">
                                       <p><?php language_e('info_view_howitwork_AutoSendPDFInvoice'); ?></p>
                                    </td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-top: 12px;">
                        <p><?php language_e('info_view_howitwork_AutoSendPDFInvoiceDescription'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="Table_17" width="1011" height="275" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="490" style="">
                        <p style="margin-bottom: 8px;"><b><?php language_e('info_view_howitwork_ManageYourPostboxes'); ?></b></p>
                        <div class="wrap_add_postbox">
                            <div>
                                <div id="primay_location">
                                    <h4 style=" font-size: 14pt;"><?php language_e('info_view_howitwork_YourPrimaryLocation'); ?><?php echo $postbox? $postbox->location_name: "";?></h4>
                                </div>
                                <div class="ym-clearfix"></div>
                                <div class="ym-gl left-3" style="width: 130px; line-height: 57px; padding-top: 9px;padding-left: 6px;">
                                    <?php language_e('info_view_howitwork_CurrentAccountType'); ?>
                                </div>
                                <div class="ym-gl" style="width:46%; width: 130px; line-height: 57px; padding-top: 9px;padding-left: 6px;">
                                    <?php
                                        $user_type = '';
                                        if ($customer->account_type == APConstants::NORMAL_CUSTOMER) {
                                            $user_type = 'STANDARD';
                                        } elseif ($customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
                                            $user_type = 'ENTERPRISE';
                                        }
                                        echo $user_type;
                                    ?>

                                </div>
                            </div>
                            <div class="ym-clearfix"></div>

                            <div style="margin:0px 0px; <?php if ($customer_product_setting['postbox_name_flag'] != '1') { ?> visibility: hidden;  <?php }?> padding-bottom: 10px;">
                                    <div class="ym-gl left-3" style="width: 130px;line-height:50px;padding-left: 6px;">
                                        <?php language_e('info_view_howitwork_NumberOfPostboxes'); ?>
                                    </div>
                                    <div class="ym-gl" style="width:46%; ">
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
<!--                                    <div class="ym-gl left-2" style="width:28%;line-height:23px;margin-left: 2px;padding-left: 6px;margin-left: -22px;">
                                        <a id="changeMyAccountTypeLink">Change postbox type</a>
                                        <input type="hidden" value="<?php //echo $customer->activated_flag?>" id="activatedFlagId" name="activatedFlag" />
                                        <a id="delPostboxLink"><?php //language_e('info_view_howitwork_SelectPostboxToDelete'); ?></a>
                                    </div>-->
                                    <div class="ym-clearfix"></div>
                            </div>
<!--                            <div style="margin:0px 0px; <?php if ($info->postbox_name_flag != '1') {?> visibility: hidden;  <?php }?>">
                                    <div class="ym-gl left-3" style="width:24%;line-height:25px;text-align:right;">
                                            <?php language_e('info_view_howitwork_Add'); ?>
                                    </div>
                                    <div class="ym-gl" style="width:45%;">
                                        <?php //$k=0;
                                            //foreach($acct_type as $item){
                                        ?>
                                                        <div style="width:30%; <?php //if($k==0){ ?>margin-left:14px;<?php //} ?>" class="ym-gl">
                                                                <a class="add" rel="<?php //echo $item->ActualValue; ?>"><?php //language_e('info_view_howitwork_Add'); ?></a>
                                                        </div>
                                        <?php //$k++; } ?>
                                    </div>
                                    <div class="ym-gl left-2" style="width:28%;line-height:50px;">
                                            &nbsp;
                                    </div>
                                    <div class="ym-clearfix"></div>
                            </div>-->
                        </div><!-- div.wrap_add_postbox -->
                    </td>
                    <td valign="top" style="padding-top:30px;">
                        <p><?php language_e('info_view_howitwork_PrimaryLocationDescription'); ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p><?php 
                        $invoice_link = "./invoices";
                        language_e('info_view_howitwork_DeletePostboxDescription', array('invoice_link' => $invoice_link)); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
            <table id="Table_19" width="1011" height="292" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <?php language_e('info_view_howitwork_MyAccountInvoices'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="how_it_work_dataGridResult" ></table>
                        <div id="navGrid_dataGridPager"></div>
                    </td>
                    <td valign="top">
                        <p><?php language_e('info_view_howitwork_MyAccountInvoicesDescription', array('invoice_link' => $invoice_link)); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
			<table id="Table_21" width="1011" height="280" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                    <b><?php language_e('info_view_howitwork_SummaryCurrentMonth'); ?></b><br/>
                    <?php language_e('info_view_howitwork_NextInvoicingDate', array('next_invoice_date' => APUtils::displayDate(APUtils::getLastDayOfCurrentMonth()))); ?>
                    </td>
                </tr>
                <tr>
                    <td width="380">
                        <div id="left-content" class="ym-gl">
                                <?php  if($customer->status == APConstants::ON_FLAG){?>
                                <table style="width: 380px;">
                                    <tbody>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnPostboxes'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnEnvelopeScanning'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnScanning'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnAdditionalItem'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnScanAdditionalPages'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnShippingHandling'); ?></td>
                                            <td class="right-align" ><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnStoringItems'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="left-align"><?php language_e('info_view_howitwork_ColumnCurrentNetTotal'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?> 0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php } else {?>
                                <?php if($next_invoices){?>
                                <table style="width: 380px;">
                                    <tbody>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnPostboxes'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->postboxes_amount) { echo APUtils::convert_currency($next_invoices->postboxes_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnEnvelopeScanning'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->envelope_scanning_amount) {echo APUtils::convert_currency($next_invoices->envelope_scanning_amount, $currency->currency_rate, 2, $decimal_separator); } else { echo sprintf('0%s00', $decimal_separator); }?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnScanning'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->scanning_amount) {echo APUtils::convert_currency($next_invoices->scanning_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnAdditionalItem'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->additional_items_amount) {echo APUtils::convert_currency($next_invoices->additional_items_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnScanAdditionalPages'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->additional_pages_scanning_amount) {echo APUtils::convert_currency($next_invoices->additional_pages_scanning_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnShippingHandling'); ?></td>
                                            <td class="right-align" ><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->shipping_handing_amount) {echo APUtils::convert_currency($next_invoices->shipping_handing_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                                        </tr>
                                        <tr>
                                            <td><?php language_e('info_view_howitwork_ColumnStoringItems'); ?></td>
                                            <td class="right-align"><?php echo $currency->currency_sign.' '; ?>
                                            <?php if($next_invoices->storing_amount) {echo APUtils::convert_currency($next_invoices->storing_amount, $currency->currency_rate, 2, $decimal_separator);}else{echo sprintf('0%s00', $decimal_separator);}?></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="left-align"><?php language_e('info_view_howitwork_ColumnCurrentNetTotal'); ?></td>
                                            <td class="right-align">
                                                <?php
                                                echo $currency->currency_sign.' ';
                                                $total = 0;
                                                $total += $next_invoices->postboxes_amount;
                                                $total += $next_invoices->envelope_scanning_amount;
                                                $total += $next_invoices->scanning_amount;
                                                $total += $next_invoices->additional_items_amount;
                                                $total += $next_invoices->shipping_handing_amount;
                                                $total += $next_invoices->storing_amount;
                                                $total += $next_invoices->additional_pages_scanning_amount;

                                                echo APUtils::convert_currency($total, $currency->currency_rate, 2, $decimal_separator);
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php }?>
                               <?php }?>
                            </div>
                    </td>
                    <td valign="top">
                        <p><?php language_e('info_view_howitwork_SummaryCurrentMonthDescription', array('invoice_link' => $invoice_link)); ?></p>
                    </td>
                </tr>
            </table>
        
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
			<table id="Table_23" width="1011" height="150" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 380px;">
                        <b><?php language_e('info_view_howitwork_YourOpenBalance'); ?></b>
                        <?php
                            $sign = "";
                            if($open_balance > 0){
                                $sign = "+";
                            }
                        ?>
                        <div class="wrap_open_balance">
                            <b><?php 
                                language_e('info_view_howitwork_OpenBalanceDue', 
                                    array('open_balance_due'  => $sign.APUtils::convert_currency($open_balance, $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short)); ?>
                            </b><br/>
                            <?php
                                $sign = "";
                                if($open_balance_this_month > 0){
                                    $sign = "+";
                                }
                            ?>
                            <b><?php language_e('info_view_howitwork_BalanceCurrentMonth', 
                                array('balance_current_month' => $currency->currency_sign . ' ' . $sign.APUtils::convert_currency($open_balance_this_month, $currency->currency_rate, 2, $decimal_separator))); ?></b>
                        </div>
                    </td>
                    <td valign="top" style="padding-top: 18px;">
                        <p style="line-height: 24px;text-align: justify;"><?php language_e('info_view_howitwork_YourOpenBalanceDescription'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
			<table id="Table_25" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding: 20px 5px 20px 5px;">
                        <a id="bankTranferButton" style="text-decoration: none">
                            <img alt="Bank transfer" src="<?php echo APContext::getImagePath()?>/bank-tranfer.png" style="width: 80px; height: 44px;" />
                        </a>
                         <a id="paymentPayoneButton" style="text-decoration: none">
                            <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                            <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                        </a>
                        <a id="paymentPayPalButton" style="text-decoration: none">
                            <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 80px; height: 44px;" />
                        </a>
                    </td>
                    <td style="padding: 20px 5px 20px 5px;">
                        <p><?php language_e('info_view_howitwork_DepositDescription'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
	<tr>
		<td class="tdseparate"></td>
	</tr>
	<tr>
		<td class="tdbox">
			<table id="Table_27" width="1011" height="238" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <b><?php language_e('info_view_howitwork_OldInvoicesCharges'); ?></b>
                    </td>
                </tr>
                <tr>
                    <td width="600">
                                <table id="how_it_work_dataGridResult2" ></table>
                                <div id="navGrid_dataGridPager2"></div>
                            </td>
                    <td valign="top">
                    <p><?php language_e('info_view_howitwork_OldInvoicesChargesDescription'); ?></p>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
</table>
<!-- End Save for Web Slices -->
<br/>
<!--#1329 add payment functionality and bank transfer info in how it works page--> 
<div class="hide" style="display: none;">
    <!--<div id="window_how_it_works" title="How it works" class="input-form dialog-form"></div>-->
    <div id="paymentWithPaypalWindow" title="<?php language_e('info_view_howitwork_PaypalPopupTitle'); ?>" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" title="<?php language_e('info_view_howitwork_DirectChargeWithoutPopupTitle'); ?>" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#"><?php language_e('info_view_howitwork_GotoPaymentView'); ?></a>
    <div id="bankTranferDivContainer"  class="input-form dialog-form" title="<?php language_e('info_view_howitwork_BankTransferPopupTitle'); ?>">
    <div style="text-align: center">
        <div style="margin-top: 10px"><?php language_e('info_view_howitwork_BankTransferPopupDescription'); ?></div>
        <div  style="margin-top: 20px"><strong><?php language_e('info_view_howitwork_BankTransferPopupAccountHolder'); ?><?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?></strong></div>
        <div><strong><?php language_e('info_view_howitwork_BankTransferPopupIBAN'); ?><?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?></strong></div>
        <div><strong><?php language_e('info_view_howitwork_BankTransferPopupBIC'); ?><?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?></strong></div>
        <div><strong><?php language_e('info_view_howitwork_BankTransferPopupBankName'); ?><?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?></strong></div>
        <div><strong><?php language_e('info_view_howitwork_BankTransferPopupEmail'); ?></strong></div>
        <div style="font-style: italic;font-size:12px;margin-top: 20px;"><?php language_e('info_view_howitwork_BankTransferPopupBottomDescription'); ?></div>
    </div>
    </div>
        
<!--    <a id="view_verification_file" class="iframe" href="">Preview file</a>
    <div id="setupAutomaticChargeWindow" title="Setup an automatic deposit charge to your credit card" class="input-form dialog-form"></div>
    <div id="showSetupFeeWindow" title="Phone pricing" class="input-form dialog-form"></div>-->
</div>
<style type="text/css">
    table#tbl_wrapper a {
        color: #0e76bc !important;
    }
</style>
<script type="text/javascript">
$(document).ready(function(){
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    
    searchCurrInvoice();
    searchOldInvoices();

	/**
	 * Search data
	 */
	function searchCurrInvoice() {

		$("#how_it_work_dataGridResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>invoices/load_current_activities';

        $("#how_it_work_dataGridResult").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: 180,
            width: 600,
            rowNum: 1000,
            rowList: [],
            pager: "#navGrid_dataGridPager",
            sortname: 'activity_date',
            viewrecords: true,
            shrinkToFit:false,
            sortorder: "desc",
            captions: '',
            colNames:[
                '<?php language_e('info_view_howitwork_ColumnID'); ?>ID',
                '<?php language_e('info_view_howitwork_ColumnActivity'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnLocationName'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnActivityDate'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnNetPrice'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnVAT'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnGrossTotal'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'activity',index:'activity', width:140},
               {name:'location_name',index:'location_name', width:110},
               {name:'activity_date',index:'activity_date', width:102},
               {name:'item_amount',index:'item_amount', width:80},
               {name:'vat',index:'vat', width:52},
               {name:'gross_total',index:'gross_total', width:85}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            }
        });
	}
        
        /**
	 * Search data
	 */
	function searchOldInvoices() {
		$("#how_it_work_dataGridResult2").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>invoices/load_old_invoice';

        $("#how_it_work_dataGridResult2").jqGrid({
        	url: url,
        	postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: 190,
            width: 600,
            rowNum: 1000,
            rowList: [],
            pager: "#navGrid_dataGridPager2",
            viewrecords: true,
            shrinkToFit:false,
            captions: '',
            colNames:[
                '<?php language_e('info_view_howitwork_ColumnID'); ?>',
                '<?php language_e('info_view_howitwork_ColumnTransaction'); ?>',
                '<?php language_e('info_view_howitwork_ColumnDate'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnTransactionID'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnNetTotal'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnGrossTotal'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnStatus'); ?>', 
                '<?php language_e('info_view_howitwork_ColumnPDF'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'invoice_name',index:'invoice_name', width:120, sortable: false},
               {name:'date',index:'date', width:80, sortable: false},
               {name:'txid',index:'txid', width:105, sortable: false},
               {name:'net_total',index:'net_total', width:70, sortable: false},
               {name:'brutto_total',index:'brutto_total', width:80, sortable: false},
               {name:'status',index:'status', width:50, sortable: false},
               {name:'id',index:'id', width:40, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		var data_row = $('#how_it_work_dataGridResult').jqGrid("getRowData",row_id);
        		console.log(data_row);
            }
        }) ;
         
	}

    function actionFormater(cellvalue, options, rowObject) {
	    if (rowObject[1] == '<?php language_e('info_view_howitwork_Invoice'); ?>') {
		    return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath()?>invoices/export/'+cellvalue+'?type=invoice" id="'+cellvalue+'">&nbsp;</a>';
		}else if(rowObject[1] == '<?php language_e('info_view_howitwork_CreditNote'); ?>'){
		    return '<a class="pdf" target="_blank" href="<?php echo APContext::getFullBasePath()?>invoices/export/'+cellvalue+'?type=credit" id="'+cellvalue+'">&nbsp;</a>';
		}else {
		    return '';
		}
	}
        $('#postbox_setting_id').change(function () {
            
            $('#always_scan_envelope').attr("checked", false);
            $('#envelope_scan').attr("checked", false);
            $('#always_scan_incomming').attr("checked", false);
            $('#scans').attr("checked", false);
            $('#email_scan_notification').attr("checked", false);
            $('#always_forward_directly').attr("checked", false);
            $('#always_forward_collect').attr("checked", false);
            $('#auto_trash_flag').attr("checked", false);
            $('#inform_email_when_item_trashed').attr("checked", false);
            $('#email_notification').val(1);
            $('#collect_mail_cycle').val(2);
            $('#weekday_shipping').val(2);
            $('#always_mark_invoice').attr("checked", false);
            
            var postbox_setting_id = $(this).val();
            $.ajaxExec({
                url: '<?php echo base_url() ?>account/load_postbox_setting',
                data: {postbox_setting_id: postbox_setting_id},
                success: function (data) {
                    if (data.status) {

                        var objResponse = data.data;
                        $('#always_scan_envelope').attr("checked", objResponse.always_scan_envelope === '1');
                        $('#envelope_scan').attr("checked", objResponse.always_scan_envelope_vol_avail === '1');
                        $('#always_scan_incomming').attr("checked", objResponse.always_scan_incomming === '1');
                        $('#scans').attr("checked", objResponse.always_scan_incomming_vol_avail === '1');
                        $('#email_scan_notification').attr("checked", objResponse.email_scan_notification === '1');
                        $('#always_forward_directly').attr("checked", objResponse.always_forward_directly === '1');
                        $('#always_forward_collect').attr("checked", objResponse.always_forward_collect === '1');
                        $('#auto_trash_flag').attr("checked", objResponse.auto_trash_flag === '1');
                        $('#email_notification').val(objResponse.email_notification);
                        $('#invoicing_cycle').val(objResponse.invoicing_cycle);
                        $('#collect_mail_cycle').val(objResponse.collect_mail_cycle);
                        $('#weekday_shipping').val(objResponse.weekday_shipping);
                        //$('#trash_after_day').val(objResponse.trash_after_day);
                        //$('#next_collect_shipping').html(objResponse.next_collect_date);
                        $('#always_mark_invoice').attr("checked", objResponse.always_mark_invoice === '1');
                        $('#standard_service_national_letter_dropdownlist').html(objResponse.standard_service_national_letter_dropdownlist);
                        $('#standard_service_international_letter_dropdownlist').html(objResponse.standard_service_international_letter_dropdownlist);
                        $('#standard_service_national_package_dropdownlist').html(objResponse.standard_service_national_package_dropdownlist);
                        $('#standard_service_international_package_dropdownlist').html(objResponse.standard_service_international_package_dropdownlist);

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        
        });
        
    /*
    *  #1329 add payment functionality and bank transfer info in how it works page 
    */
        
    /**
     * Bank Tranfer
     */
     $('#bankTranferButton').click(function() {
        // Open new dialog
        $('#bankTranferDivContainer').openDialog({
            autoOpen: true,
            height: 250,
            width: 400,
            modal: true,
            closeOnEscape: true
        });

        $('#bankTranferDivContainer').dialog('option', 'position', 'center');
        $('#bankTranferDivContainer').dialog('open');
        return false;
    });
       
    /**
     * Payone payment( visa card, master card)
     */
    $('#paymentPayoneButton').live('click', function() {
        createDirectCharge();
    });

    /**
     * Create direct charge
     */
    function createDirectCharge() {
         // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice').openDialog({
            autoOpen: false,
            height: 400,
            width: 720,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice", function() {});
            },
            buttons: {
                'Submit': function () {
                    saveDirectChargeWithoutInvoice();
                }
            }
        });
        $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice').dialog('open');
    };

    /**
     * Save direct charge without invoice
     */
    function saveDirectChargeWithoutInvoice() {
        var submitUrl = "<?php echo base_url() ?>customers/save_direct_charge_without_invoice";
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'createDirectChargeWithoutInvoiceForm',
            success: function(data) {
                if (data.status) {
                    if (data.redirect) {
                    var submitUrl = data.message;
                        $('#display_payment_confirm').attr('href', submitUrl);
                        $('#display_payment_confirm').click();
                    } else {
                        $('#createDirectChargeWithoutInvoice').dialog('close');
                        $.displayInfor(data.message, null,  function() {
                        });
                    }
                } else {
                        $.displayError(data.message);
                }
            }
        });
    }
    
     /**
     * Paypal payment
     */
    $('#paymentPayPalButton').live('click', function() {
        //var display_paypal_url = '<?php echo base_url()?>customers/paypal_payment_invoice';
        //$('#display_paypal_invoice').attr('href', display_paypal_url);
        //$('#display_paypal_invoice').click();
        
        // Open new dialog
        $('#paymentWithPaypalWindow').openDialog({
            autoOpen: false,
            height: 332,
            width: 710,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice", function() {
                });
            }
        });
        
        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');
    });
       
});
</script>