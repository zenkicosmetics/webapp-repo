<?php language_e('mailbox_view_part_confirm_collect_shipment_ConfirmCollectWindowHeader')?>
<div  style="width:100%;border: 1px solid #d3d3d3">
    <div style="width:100%;height: 150px;overflow:auto;">
        <table style="width:100%;">
            <tr>
                <th style="border-top: none;width: 25%"><?php language_e('mailbox_view_part_confirm_collect_shipment_TableColumnDate')?></th>
                <th style="border-top: none;width: 25%"><?php language_e('mailbox_view_part_confirm_collect_shipment_TableColumnFrom')?></th>
                <th style="border-top: none;width: 25%"><?php language_e('mailbox_view_part_confirm_collect_shipment_TableColumnType')?></th>
                <th style="border-top: none;width: 25%"><?php language_e('mailbox_view_part_confirm_collect_shipment_TableColumnWeight')?></th>
            </tr>
            <?php $total_weight_collect = 0;
            $total_weight_storage = 0;
            $unit = ''; ?>
            <?php foreach ($envelopes as $envelope) { 
                // Check declare custom status.
                $isPendingDeclareCustomsDirect = APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '1');
                $isPendingDeclareCustomsCollect = APUtils::isPendingForDeclareCustoms($envelope->id, $list_pending_envelope_customs, '2');
                
                if (($envelope->collect_shipping_flag === '0') && ($envelope->package_id == 0 || $envelope->package_id == null) 
                        && !$isPendingDeclareCustomsCollect) { ?>
                    <tr class="item-collect-id green-item-collect-id" data-id="<?php echo $envelope->id ?>">
                        <td><?php echo APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y'); ?></td>
                        <td><?php echo $envelope->from_customer_name; ?></td>
                        <td><?php echo $setting_label_list[$envelope->id] ?></td>
                        <td><?php echo number_format($envelope->weight, 0) . $envelope->weight_unit ?> </td>
                    <?php $total_weight_collect += $envelope->weight;
                    $unit = $envelope->weight_unit; ?>
                    </tr>
                <?php } else if (( ($envelope->storage_flag == 1 && $envelope->current_storage_charge_fee_day > 0)  || $envelope->collect_shipping_flag != 1 ) 
                        && ($envelope->package_id == 0 || $envelope->package_id == null) 
                        && !$isPendingDeclareCustomsCollect
                        && !$isPendingDeclareCustomsDirect
                        && ($envelope->direct_shipping_flag == NULL || $envelope->direct_shipping_flag == '')) {?>

                    <tr class="storage-item-collect item-collect-id" data-id="<?php echo $envelope->id ?>" style="display:none;">
                        <td><?php echo APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y'); ?></td>
                        <td><?php echo $envelope->from_customer_name; ?></td>
                        <td><?php echo $setting_label_list[$envelope->id] ?></td>
                        <td><?php echo number_format($envelope->weight, 0) . $envelope->weight_unit ?> </td>
                    <?php $total_weight_storage += $envelope->weight;
                    $unit = $envelope->weight_unit; ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
    <div>
        <table style="width:100%;">
            <tr>
                <th style="width: 25%">&nbsp;</th>
                <th style="width: 25%">&nbsp;</th>
                <th style="width: 25%">&nbsp;</th>
                <th style="width: 25%" id="collectShipmentWindow_totalWeight"> <?php echo number_format($total_weight_collect, 0) . $unit ?></th>
            </tr>
        </table>
    </div>
</div>
<br />
<table style="width: 100%">
    <tr>
        <td>
            <button id="collectShipmentWindow_includeAllStorageButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only btn-yellow" type="button" role="button" 
                    aria-disabled="false"><?php language_e('mailbox_view_part_confirm_collect_shipment_IncludeAllItemButton')?></button>
            <button id="collectShipmentWindow_calculateShippingRateButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only btn-yellow" type="button" role="button" 
                    aria-disabled="false"><?php language_e('mailbox_view_part_confirm_collect_shipment_SelectShippingServiceButton')?></button>
            <button id="collectShipmentWindow_manageForwardingAddressButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only btn-yellow" type="button" role="button" 
                    aria-disabled="false"><?php language_e('mailbox_view_part_confirm_collect_shipment_SelectAddressButton')?></button>
            <input type="hidden" id="collectShipmentWindow_includeAllStorage" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_shipping_rate_id" name="shipping_rate_id" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_shipping_rate" name="shipping_rate" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_raw_postal_charge" name="raw_postal_charge" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_raw_customs_handling" name="raw_customs_handling" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_raw_handling_charges" name="raw_handling_charges" value="0" />
            <input type="hidden" id="collectShipmentWindow_selected_number_parcel" name="number_parcel" value="0" />
        </td>
        <td style="text-align: right">
            <button id="collectShipmentWindow_YesButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only  btn-yellow" 
                    title="confirm to ship this with standard shipping service"
                    type="button" role="button" aria-disabled="false"><?php language_e('mailbox_view_part_confirm_collect_shipment_ConfirmButton')?></button>
        </td>
    </tr>
</table>
