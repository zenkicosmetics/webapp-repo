<?php
$wrap_marked_collect_shipping = "";
if (empty($envelope->package_id) && ($envelope->collect_shipping_flag === '0')) {
    $wrap_marked_collect_shipping = "wrap_marked_collect_shipping";
}
?>
<div class="<?php echo $wrap_marked_collect_shipping . " " . $collect_class ?> wrap icon_popup_container">
    &nbsp;
    <?php if ($enable_action) { ?>
        <?php if ($envelope->collect_shipping_flag == null) { ?>
            <?php if ($envelope->direct_shipping_flag == null && !$isPendingDeclareCustomsDirectShipment) { ?>
                <?php if ($isPendingDeclareCustomsCollectShipment) { ?>
                    <div class="scan-popup scan-popup-new">
                        <h2><?php language_e('mailbox_view_part_collect_shippment_RequireDeclareCustom') ?></h2>
                        <div class="ym-clearfix"></div>	
                        <div class="popup-button">
                            <a class="yes item_declare_customs" id="item_declare_customs_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                                   data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"
                                   data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_collect_shippment_DeclareCustom') ?></a>
                        </div>

                    </div>
                <?php } else if ($postbox_verification_flag == '0') { ?>
                    <div class="scan-popup scan-popup-new">
                        <h2><?php language_e('mailbox_view_part_collect_shippment_RequireVerifyAddress') ?></h2>
                        <div class="ym-clearfix"></div>	
                        <div class="popup-button">
                            <a href="<?php echo base_url() ?>cases/services?case=verification" class="yes" style="font-weight: normal;"><?php language_e('mailbox_view_part_collect_shippment_Verify') ?></a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="scan-popup scan-popup-new">
                        <h2><?php language_e('mailbox_view_part_collect_shippment_CollectItemForNextShipment') ?></h2>
                        <div class="ym-clearfix"></div>	
                        <div class="popup-button"><a class="yes yes_collectmail" id="yes_collectmail_<?php echo $envelope->id ?>"
                                   data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>" data-id="<?php echo $envelope->id ?>" 
                                   data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_collect_shippment_Yes') ?></a>
                        </div>
                        <div class="separated_popup"></div>
                        <div class="text-desc"><?php language_e('mailbox_view_part_collect_shippment_OtherForwardingOption') ?></div>
                        <div class="" style="margin: 2px 0 10px 0;">
                            <a href="javascript:void(0);" style="color: #336699;font-size: 12px;text-decoration: underline;" class="collect_change_fw_address" 
                               rel="<?php echo $envelope->id; ?>"><?php language_e('mailbox_view_part_collect_shippment_ChangeForwardingAddress') ?></a>
                        </div>
                    </div>
                <?php } ?>
                <!-- END: Collect shipping popup -->
            <?php } else if ($envelope->direct_shipping_flag === '0' || $envelope->direct_shipping_flag === '2' || $isPendingDeclareCustomsDirectShipment) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_collect_shippment_ItemAreWaitingSendingProcess') ?></h2>
                    <div class="ym-clearfix"></div>
                </div>
            <?php } else if ($envelope->direct_shipping_flag === '1') { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_collect_shippment_ItemHasBeenSent') ?></h2>
                    <div class="ym-clearfix"></div>
                </div>
            <?php } ?>
        <?php } else if ($envelope->collect_shipping_flag === '0') { ?>
            <div class="scan-popup scan-popup-new" <?php if (empty($envelope->package_id) && !$isPendingDeclareCustomsCollectShipment) { ?>style="display: none; "<?php } ?>>
                <?php if (empty($envelope->package_id) && !$isPendingDeclareCustomsCollectShipment) { ?>
                    <h2><?php language_e('mailbox_view_part_collect_shippment_ItemHasBeenMarkedCollectForwarding') ?></h2>
                    <p style="text-align: justify;padding-left: 10px;padding-right: 10px;font-weight: normal; margin-bottom: 10px;">
                        <?php language_e('mailbox_view_part_collect_shippment_TriggerSendingRequestNotification') ?></p>
                    <div class="popup-button"><a id="unmark-collect-shipping-<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" class="yes unmark-collect-shipping" 
                                                 style="font-weight: normal;"><?php language_e('mailbox_view_part_collect_shippment_UnMarkedCollectForwarding') ?></a>
                    </div>
                <?php } else if ($isPendingDeclareCustomsCollectShipment) { ?>
                    <h2><?php language_e('mailbox_view_part_collect_shippment_RequireDeclareCustom') ?></h2>
                    <div class="popup-button">
                        <a class="yes item_declare_customs" id="item_declare_customs_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                               data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"
                               data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_collect_shippment_DeclareCustom') ?></a>
                    </div>
                <?php } else{?>
                    <h2><?php language_e('mailbox_view_part_collect_shippment_ItemAreWaitingSendingProcess') ?></h2>
                <?php }?>
                <div class="ym-clearfix"></div>
            </div>
        <?php } else if ($envelope->collect_shipping_flag === '1') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_collect_shippment_ItemHasBeenSent') ?></h2>
                <p class="text_info_tracking"><?php echo (empty($envelope->tracking_number)) ? language('mailbox_view_part_collect_shippment_NoTrackingNumber')  
                : language('mailbox_view_part_collect_shippment_TrackingNumber'). 
                        (( !empty($tracking_number_url) ) ? "<a target='_blank' href= '".$tracking_number_url."'>".$envelope->tracking_number."</a>" : $envelope->tracking_number); ?></p>
                <?php if (!empty($envelope->shipping_service_name)) { ?>
                    <p class="text_info_tracking"><?php language_e('mailbox_view_part_collect_shippment_ShippingService') ?><?php echo $envelope->shipping_service_name; ?></p>
                <?php } ?>
                    <p class="text_info_tracking"><?php language_e('mailbox_view_part_collect_shippment_ShippingDate') ?><?php echo APUtils::convert_timestamp_to_date($envelope->collect_shipping_date) ?></p>
                <div class="ym-clearfix"></div>
            </div>
        <?php } else if ($envelope->collect_shipping_flag === '2') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_collect_shippment_RequirePrePayment') ?></h2>
                <div class="ym-clearfix"></div>
                <div class="popup-button"><a class="yes yes_collectmail_requestprepayment" id="yes_collectmail_<?php echo $envelope->id ?>"
                           data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>" data-id="<?php echo $envelope->id ?>" 
                           data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_collect_shippment_Yes') ?></a>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>  
