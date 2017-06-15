<div class="<?php echo $cloud_class ?> wrap icon_popup_container">
    <?php
    $cloud_confirm_message = language('mailbox_views_part_cloud_sync_SavePopup');
    $enable_cloud_action = true;
    if ($customer->auto_save_cloud === '1' || $envelope->sync_cloud_flag === '1') {
        $cloud_confirm_message = language('mailbox_views_part_cloud_sync_Saved');
        $enable_cloud_action = false;
    }
    $dropbox_setting_flag = '0';
    if (empty($dropbox_setting) || empty($dropbox_setting['access_token'])) {
        $cloud_confirm_message = language('mailbox_views_part_cloud_sync_NoCloud');
        $dropbox_setting_flag = '1';
    }
    ?>
    &nbsp;
    <?php if ($enable_action) { ?>
        <div class="scan-popup">
            <?php if ($postbox_verification_flag == '0') { ?>
                <h2 style="margin-top: 30px;">You must verify your address before you can save this item in your cloud driver.</h2>
                <div class="ym-clearfix"></div>	
                <ul>
                    <li>
                        <a href="<?php echo base_url() ?>cases/services?case=verification" class="yes" style="font-weight: normal;">Verify now</a>
                    </li>
                </ul>

            <?php } else if ($dropbox_setting_flag == '1') { ?>
                <h2 style="margin-top: 30px;"><?php echo $cloud_confirm_message; ?></h2>
                <div class="ym-clearfix"></div>	
                <ul>
                    <li><a class="yes yes_setting_cloud" id="yes_setting_cloud_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>">Yes</a></li>
                </ul>
            <?php } else if (($customer->auto_save_cloud !== '1' || $envelope->sync_cloud_flag === '0') && $enable_cloud_action) { ?>
                <h2 style="margin-top: 30px;"><?php echo $cloud_confirm_message; ?></h2>
                <div class="ym-clearfix"></div>	
                <ul>
                    <li><a class="yes yes_cloud" id="yes_cloud_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" data-postbox_id="<?php echo $envelope->postbox_id ?>">Yes</a></li>
                </ul>
            <?php } else { ?>
                <span class="text_only"><?php echo $cloud_confirm_message; ?></span>
                <div class="ym-clearfix"></div>	
            <?php } ?>
        </div>
    <?php } ?>
</div>	