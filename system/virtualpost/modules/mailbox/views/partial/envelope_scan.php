<div class="<?php echo $envelope_class ?> wrap icon_popup_container">
    &nbsp;
    <?php if ($enable_action) { ?>
        <!-- envelope scan is grey -->
        <?php if ($envelope->envelope_scan_flag === null) { ?> 
            <?php if ($envelope->collect_shipping_flag === '1' ||  $envelope->direct_shipping_flag === '1') { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_envelope_scan_YourItemHasBeen') ?></h2>
                    <div class="ym-clearfix"></div>	
                </div>
        <?php } else if ($envelope->direct_shipping_flag === '0' || ($envelope->collect_shipping_flag === '0' && !empty($envelope->package_id))) { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_envelope_scan_YourItemWillBeSent') ?></h2>
                    <div class="ym-clearfix"></div>
                </div>
        <?php } else if ($postbox_verification_flag == '0') { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_envelope_scan_YouMustVerifyYour') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button">
                        <a href="<?php echo base_url() ?>cases/services?case=verification" class="yes" style="font-weight: normal;">
                            <?php language_e('mailbox_view_part_envelope_scan_VerifyNow') ?></a>
                    </div>
                </div>
        <?php } else { ?>
                <div class="scan-popup scan-popup-new">
                    <h2><?php language_e('mailbox_view_part_envelope_scan_DoYouWantToScan') ?></h2>
                    <div class="ym-clearfix"></div>	
                    <div class="popup-button">
                        <a class="yes yes_envelope_scan" id="yes_envelope_scan_<?php echo $envelope->id ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>"
                               data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"><?php language_e('mailbox_view_part_envelope_scan_Yes') ?></a>
                    </div>
                </div>
            <?php } ?>
    <?php } else if ($envelope->envelope_scan_flag === '0') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_envelope_scan_YourItemWillBeScanned') ?></h2>
                <div class="ym-clearfix"></div>
            </div>
    <?php } else if ($envelope->envelope_scan_flag === '1') { ?>
            <div class="scan-popup scan-popup-new">
                <h2><?php language_e('mailbox_view_part_envelope_scan_DoYouWantToOpenScan') ?></h2>
                <div class="ym-clearfix"></div>	
                <div class="popup-button">
                    <a class="yes yes_open_envelope_scan" id="yes_envelope_scan_<?php echo $envelope->id ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>"
                           data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"><?php language_e('mailbox_view_part_envelope_scan_Yes') ?></a>
                </div>
            </div>
    <?php } else if ($envelope->envelope_scan_flag === '2') { ?>
            <div class="scan-popup scan-popup-new">
                <h2>
                    <?php language_e('mailbox_view_part_envelope_scan_YouHasRequestedScan') ?>
                </h2>
                <div class="ym-clearfix"></div>	
                <div class="popup-button"><a class="yes yes_envelope_scan" id="yes_envelope_scan_<?php echo $envelope->id ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>"
                    data-packagetype="<?php echo $setting_alias_code_list[$envelope->id] ?>"><?php language_e('mailbox_view_part_envelope_scan_Yes') ?></a>
                </div>
            </div>
        <?php } ?>
<?php } ?>
</div>