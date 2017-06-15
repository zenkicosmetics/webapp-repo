<?php 
if(empty($account_type)){
    $account_type = '';
}
if(empty($is_location_admin_page)){
    $is_location_admin_page = '';
}

?>
<style>
    #priceSettingFormContainer .readonly{
        background: #d3d3d3 !important;
    }
    #priceSettingFormContainer .red-bottom{
        border-bottom: 2px solid red;
    }
    #priceSettingFormContainer input.input-txt-none,#priceSettingFormContainer select.input-txt-none{
        width: 100%;
    }
    #priceSettingFormContainer th, #priceSettingFormContainer td {
        padding: 5px;
    }
    input.input-txt-none{
        margin-left: 0px;
    }
</style>
<div id="priceSettingFormContainer">
    <form id="priceSettingForm" method="post" action="<?php echo base_url()?>admin/addresses/location_pricing">
        <div class="input-form">
            <table class="priceSettingFormTable" style="width: 1080px">
                <tr style="background: rgb(68,84,106); color: white;">
                    <td style="min-width: 490px;"><?php admin_language_e('address_view_admin_price_partial_PostboxType'); ?></td>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <th <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?> style="<?php if ($account_type == '1') { ?> border-top: 2px solid red; <?php } ?> color: #FFFFFF">
                        <?php admin_language_e('address_view_admin_price_partial_PostboxType1'); ?></th>
                    <th <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?> style="<?php if ($account_type == '2') { ?> border-top: 2px solid red; <?php } ?> color: #FFFFFF">
                        <?php admin_language_e('address_view_admin_price_partial_PostboxType2'); ?></th>
                    <th <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?> style="<?php if ($account_type == '3') { ?> border-top: 2px solid red; <?php } ?> color: #FFFFFF">
                        <?php admin_language_e('address_view_admin_price_partial_PostboxType3'); ?></th>
                    <?php }?>
                    <th <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?> style="<?php if ($account_type == '5') { ?> border-top: 2px solid red; <?php } ?> color: #FFFFFF">
                        <?php admin_language_e('address_view_admin_price_partial_PostboxType4'); ?></th>
                    <th style="width: 160px; color: #FFFFFF"><?php admin_language_e('address_view_admin_price_partial_Dimension'); ?></th>
                </tr>
                <?php if($pricing_map[1]['postbox_fee']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_PostboxFeeLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['postbox_fee_as_you_go']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['postbox_fee']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['postbox_fee']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['postbox_fee']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_PostboxFeeDimension'); ?></td>
                </tr>
                <?php }?>
                
                <tr style="background: rgb(217,217,217);">
                    <th ><?php admin_language_e('address_view_admin_price_partial_IncludedFeature'); ?>Included Feature</th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <th <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <?php }?>
                    <th <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th >&nbsp;</th>    
                </tr>
                <?php if($pricing_map[1]['name_on_the_door']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_NameDoorLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="name_on_the_door_1" value="<?php echo @$pricing_map[1]['name_on_the_door']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="name_on_the_door_2" value="<?php echo @$pricing_map[2]['name_on_the_door']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="name_on_the_door_3" value="<?php echo @$pricing_map[3]['name_on_the_door']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="name_on_the_door_5" value="<?php echo @$pricing_map[5]['name_on_the_door']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_NameDoorDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['included_incomming_items']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_IncludedIncomingItemsLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_1" value="<?php echo @$pricing_map[1]['included_incomming_items']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_2" value="<?php echo @$pricing_map[2]['included_incomming_items']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_3" value="<?php echo @$pricing_map[3]['included_incomming_items']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_5" value="<?php echo @$pricing_map[5]['included_incomming_items']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_IncludedIncomingItemsDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storage']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StorageLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_1"
                        value="<?php echo @$pricing_map[1]['storage']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_2"
                        value="<?php if (@$pricing_map[2]['storage']->item_value === '0') {echo 'Unlimited';} else {echo @$pricing_map[2]['storage']->item_value;} ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_3"
                        value="<?php if (@$pricing_map[3]['storage']->item_value === '0') {echo 'Unlimited';} else {echo @$pricing_map[3]['storage']->item_value;} ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_5"
                        value="<?php if (@$pricing_map[5]['storage']->item_value === '0') {echo 'Unlimited';} else {echo @$pricing_map[5]['storage']->item_value;} ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StorageDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['hand_sorting_of_advertising']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_HandSortingOfAdvertisingLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_1" value="<?php echo @$pricing_map[1]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_2" value="<?php echo @$pricing_map[2]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_3" value="<?php echo @$pricing_map[3]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_5" value="<?php echo @$pricing_map[5]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_HandSortingOfAdvertisingDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['envelope_scanning_front']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_EnvelopeScanningFrontLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_1"
                        value="<?php echo @$pricing_map[1]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_2"
                        value="<?php echo @$pricing_map[2]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_3"
                        value="<?php echo @$pricing_map[3]['envelope_scanning_front']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_5"
                        value="<?php echo @$pricing_map[5]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_EnvelopeScanningFrontDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['included_opening_scanning']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_IncludedOpeningScanningLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_1" value="<?php echo @$pricing_map[1]['included_opening_scanning']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_2" value="<?php echo @$pricing_map[2]['included_opening_scanning']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_3" value="<?php echo @$pricing_map[3]['included_opening_scanning']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_5" value="<?php echo @$pricing_map[5]['included_opening_scanning']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_IncludedOpeningScanningDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storing_items_letters']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StoringItemsLettersLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_1"
                        value="<?php echo @$pricing_map[1]['storing_items_letters']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_2"
                        value="<?php echo @$pricing_map[2]['storing_items_letters']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_3"
                        value="<?php echo @$pricing_map[3]['storing_items_letters']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_5"
                        value="<?php echo @$pricing_map[5]['storing_items_letters']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StoringItemsLettersDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storing_items_packages']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StoringItemsPackagesLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_1"
                        value="<?php echo @$pricing_map[1]['storing_items_packages']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_2"
                        value="<?php echo @$pricing_map[2]['storing_items_packages']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_3"
                        value="<?php echo @$pricing_map[3]['storing_items_packages']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_5"
                        value="<?php echo @$pricing_map[5]['storing_items_packages']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StoringItemsPackagesDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storing_items_digitally']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StoringItemsDigitallyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_1"
                        value="<?php echo @$pricing_map[1]['storing_items_digitally']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_2"
                        value="<?php echo @$pricing_map[2]['storing_items_digitally']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_3"
                        value="<?php echo @$pricing_map[3]['storing_items_digitally']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_5"
                        value="<?php echo @$pricing_map[5]['storing_items_digitally']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StoringItemsDigitallyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['trashing_items']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_TrashingItemsLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_1"
                        value="<?php if (@$pricing_map[1]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo @$pricing_map[1]['trashing_items']->item_value;} ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_2"
                        value="<?php if (@$pricing_map[2]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo @$pricing_map[2]['trashing_items']->item_value;} ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_3"
                        value="<?php if (@$pricing_map[3]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo @$pricing_map[3]['trashing_items']->item_value;} ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_5"
                        value="<?php if (@$pricing_map[5]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo @$pricing_map[5]['trashing_items']->item_value;} ?>" /></td>
                     <td><?php admin_language_e('address_view_admin_price_partial_TrashingItemsDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['cloud_service_connection']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CloudServiceConnectionLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_1" value="<?php echo @$pricing_map[1]['cloud_service_connection']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_2" value="<?php echo @$pricing_map[2]['cloud_service_connection']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_3" value="<?php echo @$pricing_map[3]['cloud_service_connection']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_5" value="<?php echo @$pricing_map[5]['cloud_service_connection']->item_value; ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CloudServiceConnectionDimension'); ?></td>
                </tr>
                <?php }?>
                
                <?php if($pricing_map[1]['additional_included_page_opening_scanning']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_AdditionalIncludedPageOpeningScanningLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_1"
                        value="<?php echo @$pricing_map[1]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_2"
                        value="<?php echo @$pricing_map[2]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_3"
                        value="<?php echo @$pricing_map[3]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_5"
                        value="<?php echo @$pricing_map[5]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                     <td><?php admin_language_e('address_view_admin_price_partial_AdditionalIncludedPageOpeningScanningDimension'); ?></td>
                </tr>
                <?php }?>
                
                <tr style="background: rgb(217,217,217);">
                    <th ><?php admin_language_e('address_view_admin_price_partial_AdditionalActivities'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <th <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <?php }?>
                    <th <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
                <?php if($pricing_map[1]['additional_incomming_items']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_AdditionalIncommingItemsLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_1" value="<?php echo APUtils::number_format(@$pricing_map[1]['additional_incomming_items']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_2" value="<?php echo APUtils::number_format(@$pricing_map[2]['additional_incomming_items']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_3" value="<?php echo APUtils::number_format(@$pricing_map[3]['additional_incomming_items']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_5" value="<?php echo APUtils::number_format(@$pricing_map[5]['additional_incomming_items']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_AdditionalIncommingItemsDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['envelop_scanning']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_EnvelopScanningLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['envelop_scanning']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['envelop_scanning']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['envelop_scanning']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['envelop_scanning']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_EnvelopScanningDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['opening_scanning']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_OpeningScanningLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['opening_scanning']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['opening_scanning']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['opening_scanning']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['opening_scanning']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_OpeningScanningDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['send_out_directly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_SendOutDirectlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['send_out_directly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['send_out_directly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['send_out_directly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['send_out_directly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_SendOutDirectlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['shipping_plus']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_ShippingPlusLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_1"
                        value="<?php echo @$pricing_map[1]['shipping_plus']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_2"
                        value="<?php echo @$pricing_map[2]['shipping_plus']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_3"
                        value="<?php echo @$pricing_map[3]['shipping_plus']->item_value; ?>%" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_5"
                        value="<?php echo @$pricing_map[5]['shipping_plus']->item_value; ?>%" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_ShippingPlusDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['send_out_collected']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_SendOutCollectedLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['send_out_collected']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['send_out_collected']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['send_out_collected']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['send_out_collected']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_SendOutCollectedDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['collect_shipping_plus']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CollectShippingPlusLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_1"
                        value="<?php echo @$pricing_map[1]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_2"
                        value="<?php echo @$pricing_map[2]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_3"
                        value="<?php echo @$pricing_map[3]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_5"
                        value="<?php echo @$pricing_map[5]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CollectShippingPlusDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storing_items_over_free_letter']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StoringItemsOverFreeLetterLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_1" value="<?php echo APUtils::number_format(@$pricing_map[1]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_2" value="<?php echo APUtils::number_format(@$pricing_map[2]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_3" value="<?php echo APUtils::number_format(@$pricing_map[3]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_5" value="<?php echo APUtils::number_format(@$pricing_map[5]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StoringItemsOverFreeLetterDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['storing_items_over_free_packages']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_StoringItemsOverFreePackagesLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_StoringItemsOverFreePackagesDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['paypal_transaction_fee']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_PaypalTransactionFeeLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_1"
                        value="<?php echo @$pricing_map[1]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_2"
                        value="<?php echo @$pricing_map[2]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_3"
                        value="<?php echo @$pricing_map[3]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_5"
                        value="<?php echo @$pricing_map[5]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_PaypalTransactionFeeDimension'); ?></td>
                </tr>
                <?php }?>
                
                <?php if($pricing_map[1]['custom_declaration_outgoing_01']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CustomDeclarationOutgoing01Label'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CustomDeclarationOutgoing01Dimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['custom_declaration_outgoing_02']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CustomDeclarationOutgoing02Label'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CustomDeclarationOutgoing02Dimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['custom_handling_import']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CustomHandlingImportLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_1"
                        value="<?php echo @$pricing_map[1]['custom_handling_import']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_2"
                        value="<?php echo @$pricing_map[2]['custom_handling_import']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_3"
                        value="<?php echo @$pricing_map[3]['custom_handling_import']->item_value; ?>%" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_5"
                        value="<?php echo @$pricing_map[5]['custom_handling_import']->item_value; ?>%" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CustomHandlingImportDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['cash_payment_on_delivery_percentage']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CashPaymentOnDeliveryPercentageLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_1"
                        value="<?php echo @$pricing_map[1]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_2"
                        value="<?php echo @$pricing_map[2]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_3"
                        value="<?php echo @$pricing_map[3]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_5"
                        value="<?php echo @$pricing_map[5]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                     <td><?php admin_language_e('address_view_admin_price_partial_CashPaymentOnDeliveryPercentageDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['cash_payment_on_delivery_mini_cost']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_CashPaymentOnDeliveryMiniCostLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_CashPaymentOnDeliveryMiniCostDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['pickup_charge']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_PickupChargeLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['pickup_charge']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['pickup_charge']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['pickup_charge']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['pickup_charge']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_PickupChargeDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['additional_pages_scanning_price']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_AdditionalPagesScanningPriceLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_AdditionalPagesScanningPriceDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['special_requests_charge_by_time']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_SpecialRequestsChargeByTimeLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_SpecialRequestsChargeByTimeDimension'); ?></td>
                </tr>
                <?php }?>
                <tr style="background: rgb(217,217,217);">
                    <th><?php admin_language_e('address_view_admin_price_partial_Services'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <th <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <?php }?>
                    <th <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
                <?php if($pricing_map[1]['lease_of_workplace_for_own_location_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationQuarterlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationQuarterlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[5]['lease_of_workplace_for_own_location_yearly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationYearlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForOwnLocationYearlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationQuarterlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationQuarterlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationYearlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfWorkplaceForClevverMailLocationYearlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_receptionist_own_location_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_receptionist_own_location_quarterly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationQuarterlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationQuarterlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_receptionist_own_location_yearly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationYearlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistOwnLocationYearlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if($pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationQuarterlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationQuarterlyDimension'); ?></td>
                </tr>
                <?php }?>
                <tr style="background: rgb(217,217,217);">
                    <th ><?php admin_language_e('address_view_admin_price_partial_Enterprise'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <th <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <?php }?>
                    <th <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>>&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
                <?php if(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationYearlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_LeaseOfReceptionistClevverMailLocationYearlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if(@$pricing_map[5]['own_location_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_OwnLocationMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_location_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_OwnLocationMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_TouchPanelAtOwnLocationQuarterlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_TouchPanelAtOwnLocationQuarterlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if(@$pricing_map[5]['own_mobile_app_monthly']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_OwnMobileAppMonthlyLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red" <?php } ?>>&nbsp;</td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_mobile_app_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_mobile_app_monthly']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_OwnMobileAppMonthlyDimension'); ?></td>
                </tr>
                <?php }?>
                <?php if(@$pricing_map[5]['api_access']->show_customer_flag == '1' || !empty($is_location_admin_page)){ ?>
                <tr>
                    <th><?php admin_language_e('address_view_admin_price_partial_ApiAccessLabel'); ?></th>
                    <?php if(!empty($is_location_admin_page)  || (empty($is_location_admin_page)&& $account_type != 5 )){ ?>
                    <td <?php if ($account_type == '1') { ?> class="cell_red red-bottom" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '2') { ?> class="cell_red red-bottom" <?php } ?>>&nbsp;</td>
                    <td <?php if ($account_type == '3') { ?> class="cell_red red-bottom" <?php } ?>>&nbsp;</td>
                    <?php }?>
                    <td <?php if ($account_type == '5') { ?> class="cell_red red-bottom" <?php } ?>><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="api_access_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['api_access']->item_value); ?>" /></td>
                    <td><?php admin_language_e('address_view_admin_price_partial_ApiAccessDimension'); ?></td>
                </tr>
                <?php }?>
            </table>
        </div>
    </form>
</div>
<div class="clear-height"></div>
<br />
