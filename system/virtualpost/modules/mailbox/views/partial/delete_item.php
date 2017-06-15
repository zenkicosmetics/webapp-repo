<?php
$delete_message = language('mailbox_view_part_delete_item_ConfirmDeleteItemInTrash');
$delete_flag = '';
if ($current_method != 'trash') {
    $delete_key_sign = APUtils::build_delete_sign($envelope->envelope_scan_flag, $envelope->item_scan_flag, $envelope->direct_shipping_flag, $envelope->collect_shipping_flag, $envelope->package_id);
    $delete_flag = lang("delete_" . $delete_key_sign);
    if (APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '1')) {
        $delete_flag = 3;
    } else if (APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '2')) {
        $delete_flag = 3;
    }
    switch ($delete_flag) {
        case "1":
            $delete_message = language('mailbox_view_part_delete_item_ConfirmDeleteItemHasBeenSent');
            break;
        case "2":
            $delete_message = language('mailbox_view_part_delete_item_ConfirmDeleteItemHasScan');
            break;
        case "3":
            $delete_message = language('mailbox_view_part_delete_item_CanNotDeleteItemHasSendRequest');
            break;
        case "4":
            $delete_message = language('mailbox_view_part_delete_item_ConfirmDeleteDigitalItemHasBeenSent');
            break;
        case "5":
            $delete_message = language('mailbox_view_part_delete_item_ConfirmDeleteItemPhysically');
            break;
    }
}
?>
<div class="delete wrap icon_popup_container">
    <div class="scan-popup scan-popup-new">
        <h2><?php echo $delete_message; ?></h2>
        <div class="ym-clearfix"></div>
        <?php if ($delete_flag != '3') { ?>
                <div class="popup-button"><a class="yes yes_deletemail" data-delete_type="2" id="yes_deletemail_<?php echo $envelope->id ?>" data-id="<?php echo $envelope->id ?>" 
                        data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('mailbox_view_part_delete_item_Yes') ?></a>
                </div>
        <?php } ?>
    </div>
</div>	