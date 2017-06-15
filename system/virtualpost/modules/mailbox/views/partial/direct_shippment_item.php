<div class="<?php echo $send_class ?> wrap icon_popup_container"> 
    &nbsp;
    <?php if ($enable_action) { ?>
        <?php if ($envelope->direct_shipping_flag == null) { ?>
            <?php if ($envelope->collect_shipping_flag === '1') { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_ItemHasBeenSent') ?></h2>
                    <div class="ym-clearfix"></div>	
                </div>
            <?php } else if ($isPendingDeclareCustomsDirectShipment) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_RequireDeclareCustom') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button"><a class="yes item_declare_customs" id="item_declare_customs_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                               data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"
                               data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_direct_shippment_DeclareCustom') ?></a>
                    </div>

                </div>
            <?php } else if ($isPendingDeclareCustomsCollectShipment || ($envelope->collect_shipping_flag == 0 && $envelope->package_id > 0)) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_ItemAreWaitingSendingProcess') ?></h2>
                    <div class="ym-clearfix"></div>
                </div>
            <?php } else if ($postbox_verification_flag == '0') { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_RequireVerifyAddress') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button">
                            <a href="<?php echo base_url() ?>cases/services?case=verification" class="yes" style="font-weight: normal;">
                                <?php language_e('mailbox_view_part_direct_shippment_Verify') ?></a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="scan-popup scan-popup-new">
                    <h2 style="margin-top: 25px;"><?php language_e('mailbox_view_part_direct_shippment_RequestForwardingDirectly') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button">
                            <a class="yes yes_sendmail" id="yes_sendmail_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                               data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>">
                                   <?php language_e('mailbox_view_part_direct_shippment_Yes') ?></a>
                    </div>
                    <?php if ($enable_fedex_shipping_func) { ?>
                        <p style="text-align: center;" class="text-desc"><?php language_e('mailbox_view_part_direct_shippment_StandardService') ?></p>
                        <div id='link-calculate-<?php echo $envelope->id; ?>' class="link-calculate" style="position: relative;margin-bottom: 9px">
                            <span class="icon-process" style="display: none;position: absolute;left: 46%;bottom: 10px;"></span>
                            <a href="#" class="calculate_shipping_rate_link" data-shipping_type="1" data-id="<?php echo $envelope->id ?>">
                                <?php language_e('mailbox_view_part_direct_shippment_SelectShippingService') ?></a>
                        </div>
                    <?php } ?>
                    <div class="separated_popup"></div>
                    <div class="text-desc"><?php language_e('mailbox_view_part_direct_shippment_OtherForwardingOption') ?></div>
                    <div class="" style="margin: 2px 0 10px 0;">
                        <a href="javascript:void(0);" style="color: #336699;font-size: 12px;text-decoration: underline;" class="change_fw_address" rel="<?php echo $envelope->id; ?>">
                            <?php language_e('mailbox_view_part_direct_shippment_ChangeForwardingAddress') ?></a>
                    </div>
                </div>
            <?php } ?>

        <?php } else if ($envelope->direct_shipping_flag === '0') { ?>
            <?php if ($isPendingDeclareCustomsDirectShipment) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_RequireDeclareCustom') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button"><a class="yes item_declare_customs" id="item_declare_customs_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                               data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"
                               data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_direct_shippment_DeclareCustom') ?></a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_direct_shippment_ItemAreWaitingSendingProcess') ?></h2>
                    <div class="ym-clearfix"></div>
                </div>
            <?php } ?>
        <?php } else if ($envelope->direct_shipping_flag === '1') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_direct_shippment_ItemHasBeenSent') ?></h2>
                <p class="text_info_tracking"><?php echo ( !empty($envelope->tracking_number) ) ? language('mailbox_view_part_direct_shippment_TrackingNumber')
                .( !empty($tracking_number_url) ? "<a target='_blank' href= '".$tracking_number_url."'>".$envelope->tracking_number."</a>" :  $envelope->tracking_number) 
                : language('mailbox_view_part_direct_shippment_NoTrackingNumber') ?></p>

                <?php if (!empty($envelope->shipping_service_name)) { ?>
                    <p class="text_info_tracking"><?php language_e('mailbox_view_part_direct_shippment_ShippingService') ?><?php echo $envelope->shipping_service_name; ?></p>
                <?php } ?>
                <p class="text_info_tracking"><?php language_e('mailbox_view_part_direct_shippment_ShippingDate') ?><?php echo APUtils::convert_timestamp_to_date($envelope->direct_shipping_date) ?></p>
                <div class="ym-clearfix"></div>
            </div>
        <?php } else if ($envelope->direct_shipping_flag === '2') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_direct_shippment_RequirePrePayment') ?></h2>
                <div class="ym-clearfix"></div>
                <div class="popup-button">
                        <a class="yes yes_sendmail" id="yes_sendmail_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                           data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_direct_shippment_Yes') ?></a>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>	