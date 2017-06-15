<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px">
        Edit value of <strong><?php echo $pricing_template->name;?></strong>
    </h2>
    <?php if (!$valid_data) { ?>
    <span style="color: red">
        <?php echo $message;?>
    </span>
    <?php } ?>
</div>
<?php
// Check authenticate.
$readonly = '';
$readonlyClass = '';
?>
<div class="button_container">
    <form id="priceSettingForm" method="post" action="<?php echo base_url()?>price/admin/prices">
        <div class="input-form">
            <table class="settings" style="width: 950px">
                <tr style="background: rgb(68,84,106); color: white;">
                    <th class="">&nbsp;</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <th class="input-width-200">AS YOU GO</th>
                    <th class="input-width-200">PRIVATE</th>
                    <th class="input-width-200">BUSINESS</th>
                    <?php } else{?>
                    <th class="input-width-200">NORMAL PRICE</th>
                    <th class="input-width-200">OWNER PRICE</th>
                    <th class="input-width-200">SPECIAL PRICE</th>
                    <th class="input-width-200">SPECIAL OWNER PRICE</th>
                    <?php }?>
                    <th class="input-width-200">Dimension</th>
                    <th class="input-width-200">Type</th>
                    <th class="input-width-200">% Rev Share</th>
                    <th class="input-width-200">Avail To Customer</th>
                    <th class="input-width-200">Contract Term</th>
                    <th class="input-width-200">Billing Period</th>
                </tr>
                <tr>
                    <th>Postbox Fee</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_as_you_go_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['postbox_fee_as_you_go']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['postbox_fee']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['postbox_fee']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['postbox_fee']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['postbox_fee']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['postbox_fee']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['postbox_fee']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR/month</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['postbox_fee']->type,
                                "name" => 'postbox_fee_4',
                                "id"	=> 'postbox_fee_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="postbox_fee_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['postbox_fee']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['postbox_fee']->show_customer_flag,
                                "name" => 'postbox_fee_7',
                                "id"	=> 'postbox_fee_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['postbox_fee']->contract_terms,
                                "name" => 'postbox_fee_8',
                                "id"	=> 'postbox_fee_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['postbox_fee']->billing_period,
                                "name" => 'postbox_fee_9',
                                "id"	=> 'postbox_fee_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>AS YOU GO duration</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_1"
                        value="<?php echo  $pricing_map[1]['as_you_go']->item_value; ?>" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_2"
                        value="<?php //echo $pricing_map[2]['as_you_go']->item_value; ?>" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_3"
                        value="<?php //echo $pricing_map[3]['as_you_go']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_5"
                        value="<?php //echo $pricing_map[5]['as_you_go']->item_value; ?>" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_10"
                       value="<?php //echo $pricing_map[5]['as_you_go']->item_value; ?>" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_11"
                       value="<?php //echo $pricing_map[5]['as_you_go']->item_value; ?>" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_12"
                       value="<?php //echo $pricing_map[5]['as_you_go']->item_value; ?>" /></td>
                    <?php }?>
                    <td>Day(s)</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['as_you_go']->type,
                                "name" => 'as_you_go_4',
                                "id"	=> 'as_you_go_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "html_option" => 'disabled="disabled"',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_6"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_7"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_8"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="as_you_go_9"
                        value="" /></td>
                </tr>
                
                
                <tr style="background: rgb(217,217,217);">
                        <th>Included Feature</th>
                        <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php } else{?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php }?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>Name plate at the entrance</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td>
                        <select name="name_on_the_door_1" id="name_on_the_door_1" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[1]['name_on_the_door']->item_value == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[1]['name_on_the_door']->item_value == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_on_the_door_2" id="name_on_the_door_2" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[2]['name_on_the_door']->item_value == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[2]['name_on_the_door']->item_value == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_on_the_door_3" id="name_on_the_door_3" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[3]['name_on_the_door']->item_value == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[3]['name_on_the_door']->item_value == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <?php } else{?>
                    <td>
                        <select name="name_on_the_door_5" id="name_on_the_door_5" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[5]['name_on_the_door']->item_value == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[5]['name_on_the_door']->item_value == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_on_the_door_10" id="name_on_the_door_10" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[5]['name_on_the_door']->item_value_owner == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[5]['name_on_the_door']->item_value_owner == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_on_the_door_11" id="name_on_the_door_11" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[5]['name_on_the_door']->item_value_special == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[5]['name_on_the_door']->item_value_special == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <select name="name_on_the_door_12" id="name_on_the_door_12" class="input-width" style="width:110px">
                            <option value=""></option>
                            <option value="Yes" <?php if ($pricing_map[5]['name_on_the_door']->item_value_owner_special == 'Yes') { ?> selected="selected" <?php } ?>>Yes</option>
                            <option value="No" <?php if ($pricing_map[5]['name_on_the_door']->item_value_owner_special == 'No') { ?> selected="selected" <?php } ?>>No</option>
                        </select>
                    </td>
                    <?php }?>
                    <td>no/yes</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['name_on_the_door']->type,
                                "name" => 'name_on_the_door_4',
                                "id"	=> 'name_on_the_door_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="name_on_the_door_6"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="name_on_the_door_7"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="name_on_the_door_8"
                        value="" /></td>
                    <td><input disabled="disabled" class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="name_on_the_door_9"
                        value="" /></td>
                </tr>
                <tr>
                    <th>Included incoming items</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_1" value="<?php echo $pricing_map[1]['included_incomming_items']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_2" value="<?php echo $pricing_map[2]['included_incomming_items']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_3" value="<?php echo $pricing_map[3]['included_incomming_items']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_5" value="<?php echo $pricing_map[5]['included_incomming_items']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_10" value="<?php echo $pricing_map[5]['included_incomming_items']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_11" value="<?php echo $pricing_map[5]['included_incomming_items']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="included_incomming_items_12" value="<?php echo $pricing_map[5]['included_incomming_items']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>pieces</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['included_incomming_items']->type,
                                "name" => 'included_incomming_items_4',
                                "id"	=> 'included_incomming_items_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_incomming_items_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['included_incomming_items']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['included_incomming_items']->show_customer_flag,
                                "name" => 'included_incomming_items_7',
                                "id"	=> 'included_incomming_items_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['included_incomming_items']->contract_terms,
                                "name" => 'included_incomming_items_8',
                                "id"	=> 'included_incomming_items_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['included_incomming_items']->billing_period,
                                "name" => 'included_incomming_items_9',
                                "id"	=> 'included_incomming_items_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storage</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_1"
                        value="<?php echo $pricing_map[1]['storage']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_2"
                        value="<?php if ($pricing_map[2]['storage']->item_value === '0') {echo 'Unlimited';} else {echo $pricing_map[2]['storage']->item_value;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_3"
                        value="<?php if ($pricing_map[3]['storage']->item_value === '0') {echo 'Unlimited';} else {echo $pricing_map[3]['storage']->item_value;} ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_5"
                        value="<?php if ($pricing_map[5]['storage']->item_value === '0') {echo 'Unlimited';} else {echo $pricing_map[5]['storage']->item_value;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_10"
                        value="<?php if ($pricing_map[5]['storage']->item_value_owner === '0') {echo 'Unlimited';} else {echo $pricing_map[5]['storage']->item_value_owner;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_11"
                        value="<?php if ($pricing_map[5]['storage']->item_value_special === '0') {echo 'Unlimited';} else {echo $pricing_map[5]['storage']->item_value_special;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_12"
                        value="<?php if ($pricing_map[5]['storage']->item_value_owner_special === '0') {echo 'Unlimited';} else {echo $pricing_map[5]['storage']->item_value_owner_special;} ?>" /></td>
                    <?php }?>
                    <td>GB</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storage']->type,
                                "name" => 'storage_4',
                                "id"	=> 'storage_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storage_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storage']->rev_share_in_percent); ?>" /></td>
                    
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storage']->show_customer_flag,
                                "name" => 'storage_7',
                                "id"	=> 'storage_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storage']->contract_terms,
                                "name" => 'storage_8',
                                "id"	=> 'storage_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storage']->billing_period,
                                "name" => 'storage_9',
                                "id"	=> 'storage_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Hand sorting of advertising</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_1" value="<?php echo $pricing_map[1]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_2" value="<?php echo $pricing_map[2]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_3" value="<?php echo $pricing_map[3]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_5" value="<?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_10" value="<?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_11" value="<?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="hand_sorting_of_advertising_12" value="<?php echo $pricing_map[5]['hand_sorting_of_advertising']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>no/yes</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['hand_sorting_of_advertising']->type,
                                "name" => 'hand_sorting_of_advertising_4',
                                "id"	=> 'hand_sorting_of_advertising_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="hand_sorting_of_advertising_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['hand_sorting_of_advertising']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['hand_sorting_of_advertising']->show_customer_flag,
                                "name" => 'hand_sorting_of_advertising_7',
                                "id"	=> 'hand_sorting_of_advertising_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['hand_sorting_of_advertising']->contract_terms,
                                "name" => 'hand_sorting_of_advertising_8',
                                "id"	=> 'hand_sorting_of_advertising_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['hand_sorting_of_advertising']->billing_period,
                                "name" => 'hand_sorting_of_advertising_9',
                                "id"	=> 'hand_sorting_of_advertising_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Envelope scanning (front)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_1"
                        value="<?php echo $pricing_map[1]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_2"
                        value="<?php echo $pricing_map[2]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_3"
                        value="<?php echo $pricing_map[3]['envelope_scanning_front']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_5"
                        value="<?php echo $pricing_map[5]['envelope_scanning_front']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_10"
                        value="<?php echo $pricing_map[5]['envelope_scanning_front']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_11"
                        value="<?php echo $pricing_map[5]['envelope_scanning_front']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_12"
                        value="<?php echo $pricing_map[5]['envelope_scanning_front']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>pieces</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['envelope_scanning_front']->type,
                                "name" => 'envelope_scanning_front_4',
                                "id"	=> 'envelope_scanning_front_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelope_scanning_front_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['envelope_scanning_front']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['envelope_scanning_front']->show_customer_flag,
                                "name" => 'envelope_scanning_front_7',
                                "id"	=> 'envelope_scanning_front_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['envelope_scanning_front']->contract_terms,
                                "name" => 'envelope_scanning_front_8',
                                "id"	=> 'envelope_scanning_front_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['envelope_scanning_front']->billing_period,
                                "name" => 'envelope_scanning_front_9',
                                "id"	=> 'envelope_scanning_front_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Item scan</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_1" value="<?php echo $pricing_map[1]['included_opening_scanning']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_2" value="<?php echo $pricing_map[2]['included_opening_scanning']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_3" value="<?php echo $pricing_map[3]['included_opening_scanning']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_5" value="<?php echo $pricing_map[5]['included_opening_scanning']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_10" value="<?php echo $pricing_map[5]['included_opening_scanning']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_11" value="<?php echo $pricing_map[5]['included_opening_scanning']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_12" value="<?php echo $pricing_map[5]['included_opening_scanning']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>pieces</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['included_opening_scanning']->type,
                                "name" => 'included_opening_scanning_4',
                                "id"	=> 'included_opening_scanning_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="included_opening_scanning_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['included_opening_scanning']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['included_opening_scanning']->show_customer_flag,
                                "name" => 'included_opening_scanning_7',
                                "id"	=> 'included_opening_scanning_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['included_opening_scanning']->contract_terms,
                                "name" => 'included_opening_scanning_8',
                                "id"	=> 'included_opening_scanning_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['included_opening_scanning']->billing_period,
                                "name" => 'included_opening_scanning_9',
                                "id"	=> 'included_opening_scanning_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storing items free period (letters)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_1"
                        value="<?php echo $pricing_map[1]['storing_items_letters']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_2"
                        value="<?php echo $pricing_map[2]['storing_items_letters']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_3"
                        value="<?php echo $pricing_map[3]['storing_items_letters']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_5"
                        value="<?php echo $pricing_map[5]['storing_items_letters']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_10"
                        value="<?php echo $pricing_map[5]['storing_items_letters']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_11"
                        value="<?php echo $pricing_map[5]['storing_items_letters']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_12"
                        value="<?php echo $pricing_map[5]['storing_items_letters']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>days</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storing_items_letters']->type,
                                "name" => 'storing_items_letters_4',
                                "id"	=> 'storing_items_letters_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_letters_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_letters']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storing_items_letters']->show_customer_flag,
                                "name" => 'storing_items_letters_7',
                                "id"	=> 'storing_items_letters_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storing_items_letters']->contract_terms,
                                "name" => 'storing_items_letters_8',
                                "id"	=> 'storing_items_letters_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storing_items_letters']->billing_period,
                                "name" => 'storing_items_letters_9',
                                "id"	=> 'storing_items_letters_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storing items free period (packages)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_1"
                        value="<?php echo $pricing_map[1]['storing_items_packages']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_2"
                        value="<?php echo $pricing_map[2]['storing_items_packages']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_3"
                        value="<?php echo $pricing_map[3]['storing_items_packages']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_5"
                        value="<?php echo $pricing_map[5]['storing_items_packages']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_10"
                        value="<?php echo $pricing_map[5]['storing_items_packages']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_11"
                        value="<?php echo $pricing_map[5]['storing_items_packages']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_12"
                        value="<?php echo $pricing_map[5]['storing_items_packages']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>days</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storing_items_packages']->type,
                                "name" => 'storing_items_packages_4',
                                "id"	=> 'storing_items_packages_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_packages_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_packages']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storing_items_packages']->show_customer_flag,
                                "name" => 'storing_items_packages_7',
                                "id"	=> 'storing_items_packages_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storing_items_packages']->contract_terms,
                                "name" => 'storing_items_packages_8',
                                "id"	=> 'storing_items_packages_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storing_items_packages']->billing_period,
                                "name" => 'storing_items_packages_9',
                                "id"	=> 'storing_items_packages_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storing items digitally</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_1"
                        value="<?php echo $pricing_map[1]['storing_items_digitally']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_2"
                        value="<?php echo $pricing_map[2]['storing_items_digitally']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_3"
                        value="<?php echo $pricing_map[3]['storing_items_digitally']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_5"
                        value="<?php echo $pricing_map[5]['storing_items_digitally']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_10"
                        value="<?php echo $pricing_map[5]['storing_items_digitally']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_11"
                        value="<?php echo $pricing_map[5]['storing_items_digitally']->item_value_special; ?>" /></td>
                     <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_12"
                        value="<?php echo $pricing_map[5]['storing_items_digitally']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>years</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storing_items_digitally']->type,
                                "name" => 'storing_items_digitally_4',
                                "id"	=> 'storing_items_digitally_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_digitally_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_digitally']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storing_items_digitally']->show_customer_flag,
                                "name" => 'storing_items_digitally_7',
                                "id"	=> 'storing_items_digitally_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storing_items_digitally']->contract_terms,
                                "name" => 'storing_items_digitally_8',
                                "id"	=> 'storing_items_digitally_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storing_items_digitally']->billing_period,
                                "name" => 'storing_items_digitally_9',
                                "id"	=> 'storing_items_digitally_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Trashing items</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_1"
                        value="<?php if ($pricing_map[1]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo $pricing_map[1]['trashing_items']->item_value;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_2"
                        value="<?php if ($pricing_map[2]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo $pricing_map[2]['trashing_items']->item_value;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_3"
                        value="<?php if ($pricing_map[3]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo $pricing_map[3]['trashing_items']->item_value;} ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_5"
                        value="<?php if ($pricing_map[5]['trashing_items']->item_value === '-1') {echo 'Unlimited';} else {echo $pricing_map[5]['trashing_items']->item_value;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_10"
                        value="<?php if ($pricing_map[5]['trashing_items']->item_value_owner === '-1') {echo 'Unlimited';} else {echo $pricing_map[5]['trashing_items']->item_value_owner;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_11"
                        value="<?php if ($pricing_map[5]['trashing_items']->item_value_special === '-1') {echo 'Unlimited';} else {echo $pricing_map[5]['trashing_items']->item_value_special;} ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_12"
                        value="<?php if ($pricing_map[5]['trashing_items']->item_value_owner_special === '-1') {echo 'Unlimited';} else {echo $pricing_map[5]['trashing_items']->item_value_owner_special;} ?>" /></td>
                    <?php }?>
                    <td>pieces</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['trashing_items']->type,
                                "name" => 'trashing_items_4',
                                "id"	=> 'trashing_items_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="trashing_items_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['trashing_items']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['trashing_items']->show_customer_flag,
                                "name" => 'trashing_items_7',
                                "id"	=> 'trashing_items_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['trashing_items']->contract_terms,
                                "name" => 'trashing_items_8',
                                "id"	=> 'trashing_items_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['trashing_items']->billing_period,
                                "name" => 'trashing_items_9',
                                "id"	=> 'trashing_items_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Cloud service connection</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_1" value="<?php echo $pricing_map[1]['cloud_service_connection']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_2" value="<?php echo $pricing_map[2]['cloud_service_connection']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_3" value="<?php echo $pricing_map[3]['cloud_service_connection']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_5" value="<?php echo $pricing_map[5]['cloud_service_connection']->item_value; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_10" value="<?php echo $pricing_map[5]['cloud_service_connection']->item_value_owner; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_11" value="<?php echo $pricing_map[5]['cloud_service_connection']->item_value_special; ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="cloud_service_connection_12" value="<?php echo $pricing_map[5]['cloud_service_connection']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>no/yes</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['cloud_service_connection']->type,
                                "name" => 'cloud_service_connection_4',
                                "id"	=> 'cloud_service_connection_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="cloud_service_connection_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['cloud_service_connection']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['cloud_service_connection']->show_customer_flag,
                                "name" => 'cloud_service_connection_7',
                                "id"	=> 'cloud_service_connection_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['cloud_service_connection']->contract_terms,
                                "name" => 'cloud_service_connection_8',
                                "id"	=> 'cloud_service_connection_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['cloud_service_connection']->billing_period,
                                "name" => 'cloud_service_connection_9',
                                "id"	=> 'cloud_service_connection_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>Included pages for opening and scanning</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_1"
                        value="<?php echo $pricing_map[1]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_2"
                        value="<?php echo $pricing_map[2]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_3"
                        value="<?php echo $pricing_map[3]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_5"
                        value="<?php echo $pricing_map[5]['additional_included_page_opening_scanning']->item_value; ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_10"
                        value="<?php echo $pricing_map[5]['additional_included_page_opening_scanning']->item_value_owner; ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_11"
                        value="<?php echo $pricing_map[5]['additional_included_page_opening_scanning']->item_value_special; ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_included_page_opening_scanning_12"
                        value="<?php echo $pricing_map[5]['additional_included_page_opening_scanning']->item_value_owner_special; ?>" /></td>
                    <?php }?>
                    <td>pieces</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['additional_included_page_opening_scanning']->type,
                                "name" => 'additional_included_page_opening_scanning_4',
                                "id"	=> 'additional_included_page_opening_scanning_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="additional_included_page_opening_scanning_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['additional_included_page_opening_scanning']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['additional_included_page_opening_scanning']->show_customer_flag,
                                "name" => 'additional_included_page_opening_scanning_7',
                                "id"	=> 'additional_included_page_opening_scanning_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['additional_included_page_opening_scanning']->contract_terms,
                                "name" => 'additional_included_page_opening_scanning_8',
                                "id"	=> 'additional_included_page_opening_scanning_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['additional_included_page_opening_scanning']->billing_period,
                                "name" => 'additional_included_page_opening_scanning_9',
                                "id"	=> 'additional_included_page_opening_scanning_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr style="background: rgb(217,217,217);">
                    <th>Additional Activities</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php } else{?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php }?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th>Additional incoming items</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_1" value="<?php echo APUtils::number_format($pricing_map[1]['additional_incomming_items']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_2" value="<?php echo APUtils::number_format($pricing_map[2]['additional_incomming_items']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_3" value="<?php echo APUtils::number_format($pricing_map[3]['additional_incomming_items']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_5" value="<?php echo APUtils::number_format($pricing_map[5]['additional_incomming_items']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_10" value="<?php echo APUtils::number_format($pricing_map[5]['additional_incomming_items']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_11" value="<?php echo APUtils::number_format($pricing_map[5]['additional_incomming_items']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="additional_incomming_items_12" value="<?php echo APUtils::number_format($pricing_map[5]['additional_incomming_items']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['additional_incomming_items']->type,
                                "name" => 'additional_incomming_items_4',
                                "id"	=> 'additional_incomming_items_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="additional_incomming_items_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['additional_incomming_items']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['additional_incomming_items']->show_customer_flag,
                                "name" => 'additional_incomming_items_7',
                                "id"	=> 'additional_incomming_items_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['additional_incomming_items']->contract_terms,
                                "name" => 'additional_incomming_items_8',
                                "id"	=> 'additional_incomming_items_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['additional_incomming_items']->billing_period,
                                "name" => 'additional_incomming_items_9',
                                "id"	=> 'additional_incomming_items_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Envelope scanning</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['envelop_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['envelop_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['envelop_scanning']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['envelop_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['envelop_scanning']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['envelop_scanning']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['envelop_scanning']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['envelop_scanning']->type,
                                "name" => 'envelop_scanning_4',
                                "id"	=> 'envelop_scanning_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="envelop_scanning_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['envelop_scanning']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['envelop_scanning']->show_customer_flag,
                                "name" => 'envelop_scanning_7',
                                "id"	=> 'envelop_scanning_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['envelop_scanning']->contract_terms,
                                "name" => 'envelop_scanning_8',
                                "id"	=> 'envelop_scanning_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['envelop_scanning']->billing_period,
                                "name" => 'envelop_scanning_9',
                                "id"	=> 'envelop_scanning_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Opening and scanning</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['opening_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['opening_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['opening_scanning']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['opening_scanning']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['opening_scanning']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['opening_scanning']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['opening_scanning']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['opening_scanning']->type,
                                "name" => 'opening_scanning_4',
                                "id"	=> 'opening_scanning_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="opening_scanning_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['opening_scanning']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['opening_scanning']->show_customer_flag,
                                "name" => 'opening_scanning_7',
                                "id"	=> 'opening_scanning_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['opening_scanning']->contract_terms,
                                "name" => 'opening_scanning_8',
                                "id"	=> 'opening_scanning_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['opening_scanning']->billing_period,
                                "name" => 'opening_scanning_9',
                                "id"	=> 'opening_scanning_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Direct forwarding fee (charge per incident)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['send_out_directly']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['send_out_directly']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['send_out_directly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_directly']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_directly']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_directly']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_directly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['send_out_directly']->type,
                                "name" => 'send_out_directly_4',
                                "id"	=> 'send_out_directly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_directly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['send_out_directly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['send_out_directly']->show_customer_flag,
                                "name" => 'send_out_directly_7',
                                "id"	=> 'send_out_directly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['send_out_directly']->contract_terms,
                                "name" => 'send_out_directly_8',
                                "id"	=> 'send_out_directly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['send_out_directly']->billing_period,
                                "name" => 'send_out_directly_9',
                                "id"	=> 'send_out_directly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Direct forwarding fee (charge based on postal charge)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_1"
                        value="<?php echo $pricing_map[1]['shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_2"
                        value="<?php echo $pricing_map[2]['shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_3"
                        value="<?php echo $pricing_map[3]['shipping_plus']->item_value; ?>%" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_5"
                        value="<?php echo $pricing_map[5]['shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_10"
                        value="<?php echo $pricing_map[5]['shipping_plus']->item_value_owner; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_11"
                        value="<?php echo $pricing_map[5]['shipping_plus']->item_value_special; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_12"
                        value="<?php echo $pricing_map[5]['shipping_plus']->item_value_owner_special; ?>%" /></td>
                    <?php }?>
                    <td>percentage</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['shipping_plus']->type,
                                "name" => 'shipping_plus_4',
                                "id"	=> 'shipping_plus_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="shipping_plus_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['shipping_plus']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['shipping_plus']->show_customer_flag,
                                "name" => 'shipping_plus_7',
                                "id"	=> 'shipping_plus_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['shipping_plus']->contract_terms,
                                "name" => 'shipping_plus_8',
                                "id"	=> 'shipping_plus_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['shipping_plus']->billing_period,
                                "name" => 'shipping_plus_9',
                                "id"	=> 'shipping_plus_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Collect forwarding(charge per incident)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['send_out_collected']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['send_out_collected']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['send_out_collected']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_collected']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_collected']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_collected']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['send_out_collected']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['send_out_collected']->type,
                                "name" => 'send_out_collected_4',
                                "id"	=> 'send_out_collected_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="send_out_collected_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['send_out_collected']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['send_out_collected']->show_customer_flag,
                                "name" => 'send_out_collected_7',
                                "id"	=> 'send_out_collected_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['send_out_collected']->contract_terms,
                                "name" => 'send_out_collected_8',
                                "id"	=> 'send_out_collected_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['send_out_collected']->billing_period,
                                "name" => 'send_out_collected_9',
                                "id"	=> 'send_out_collected_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Collect forwarding (charge based on postal charge)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_1"
                        value="<?php echo $pricing_map[1]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_2"
                        value="<?php echo $pricing_map[2]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_3"
                        value="<?php echo $pricing_map[3]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_5"
                        value="<?php echo $pricing_map[5]['collect_shipping_plus']->item_value; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_10"
                        value="<?php echo $pricing_map[5]['collect_shipping_plus']->item_value_owner; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_11"
                        value="<?php echo $pricing_map[5]['collect_shipping_plus']->item_value_special; ?>%" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_12"
                        value="<?php echo $pricing_map[5]['collect_shipping_plus']->item_value_owner_special; ?>%" /></td>
                    <?php }?>
                    <td>percentage</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['collect_shipping_plus']->type,
                                "name" => 'collect_shipping_plus_4',
                                "id"	=> 'collect_shipping_plus_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="collect_shipping_plus_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['collect_shipping_plus']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['collect_shipping_plus']->show_customer_flag,
                                "name" => 'collect_shipping_plus_7',
                                "id"	=> 'collect_shipping_plus_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['collect_shipping_plus']->contract_terms,
                                "name" => 'collect_shipping_plus_8',
                                "id"	=> 'collect_shipping_plus_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['collect_shipping_plus']->billing_period,
                                "name" => 'collect_shipping_plus_9',
                                "id"	=> 'collect_shipping_plus_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storing items over free period (letters)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_1" value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_2" value="<?php echo APUtils::number_format($pricing_map[2]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_3" value="<?php echo APUtils::number_format($pricing_map[3]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_5" value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_letter']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_10" value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_letter']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_11" value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_letter']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_letter_12" value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_letter']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR/day</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storing_items_over_free_letter']->type,
                                "name" => 'storing_items_over_free_letter_4',
                                "id"	=> 'storing_items_over_free_letter_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_over_free_letter_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_letter']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_letter']->show_customer_flag,
                                "name" => 'storing_items_over_free_letter_7',
                                "id"	=> 'storing_items_over_free_letter_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_letter']->contract_terms,
                                "name" => 'storing_items_over_free_letter_8',
                                "id"	=> 'storing_items_over_free_letter_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_letter']->billing_period,
                                "name" => 'storing_items_over_free_letter_9',
                                "id"	=> 'storing_items_over_free_letter_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Storing items over free period (packages)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_packages']->item_value); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_packages']->item_value_owner); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_packages']->item_value_special); ?>" /></td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?>
                        name="storing_items_over_free_packages_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['storing_items_over_free_packages']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR/day</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['storing_items_over_free_packages']->type,
                                "name" => 'storing_items_over_free_packages_4',
                                "id"	=> 'storing_items_over_free_packages_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="storing_items_over_free_packages_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['storing_items_over_free_packages']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_packages']->show_customer_flag,
                                "name" => 'storing_items_over_free_packages_7',
                                "id"	=> 'storing_items_over_free_packages_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_packages']->contract_terms,
                                "name" => 'storing_items_over_free_packages_8',
                                "id"	=> 'storing_items_over_free_packages_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['storing_items_over_free_packages']->billing_period,
                                "name" => 'storing_items_over_free_packages_9',
                                "id"	=> 'storing_items_over_free_packages_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Paypal transaction fee</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_1"
                        value="<?php echo $pricing_map[1]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_2"
                        value="<?php echo $pricing_map[2]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_3"
                        value="<?php echo $pricing_map[3]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_5"
                        value="<?php echo $pricing_map[5]['paypal_transaction_fee']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_10"
                        value="<?php echo $pricing_map[5]['paypal_transaction_fee']->item_value_owner; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_11"
                        value="<?php echo $pricing_map[5]['paypal_transaction_fee']->item_value_special; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="paypal_transaction_fee_12"
                        value="<?php echo $pricing_map[5]['paypal_transaction_fee']->item_value_owner_special; ?>%" /></td>
                    <?php }?>
                    <td>percentage</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['paypal_transaction_fee']->type,
                                "name" => 'paypal_transaction_fee_4',
                                "id"	=> 'paypal_transaction_fee_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="paypal_transaction_fee_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['paypal_transaction_fee']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['paypal_transaction_fee']->show_customer_flag,
                                "name" => 'paypal_transaction_fee_7',
                                "id"	=> 'paypal_transaction_fee_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['paypal_transaction_fee']->contract_terms,
                                "name" => 'paypal_transaction_fee_8',
                                "id"	=> 'paypal_transaction_fee_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['paypal_transaction_fee']->billing_period,
                                "name" => 'paypal_transaction_fee_9',
                                "id"	=> 'paypal_transaction_fee_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>Customs declaration outgoing (value >1000 EUR)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_01']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_01']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_01']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_01_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_01']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['custom_declaration_outgoing_01']->type,
                                "name" => 'custom_declaration_outgoing_01_4',
                                "id"	=> 'custom_declaration_outgoing_01_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="custom_declaration_outgoing_01_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['custom_declaration_outgoing_01']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_01']->show_customer_flag,
                                "name" => 'custom_declaration_outgoing_01_7',
                                "id"	=> 'custom_declaration_outgoing_01_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_01']->contract_terms,
                                "name" => 'custom_declaration_outgoing_01_8',
                                "id"	=> 'custom_declaration_outgoing_01_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_01']->billing_period,
                                "name" => 'custom_declaration_outgoing_01_9',
                                "id"	=> 'custom_declaration_outgoing_01_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>Customs declaration outgoing (value <1000 EUR)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_02']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_02']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_02']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_declaration_outgoing_02_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['custom_declaration_outgoing_02']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['custom_declaration_outgoing_02']->type,
                                "name" => 'custom_declaration_outgoing_02_4',
                                "id"	=> 'custom_declaration_outgoing_02_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="custom_declaration_outgoing_02_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['custom_declaration_outgoing_02']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_02']->show_customer_flag,
                                "name" => 'custom_declaration_outgoing_02_7',
                                "id"	=> 'custom_declaration_outgoing_02_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_02']->contract_terms,
                                "name" => 'custom_declaration_outgoing_02_8',
                                "id"	=> 'custom_declaration_outgoing_02_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['custom_declaration_outgoing_02']->billing_period,
                                "name" => 'custom_declaration_outgoing_02_9',
                                "id"	=> 'custom_declaration_outgoing_02_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Customs handling import </th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_1"
                        value="<?php echo $pricing_map[1]['custom_handling_import']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_2"
                        value="<?php echo $pricing_map[2]['custom_handling_import']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_3"
                        value="<?php echo $pricing_map[3]['custom_handling_import']->item_value; ?>%" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_5"
                        value="<?php echo $pricing_map[5]['custom_handling_import']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_10"
                        value="<?php echo $pricing_map[5]['custom_handling_import']->item_value_owner; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_11"
                        value="<?php echo $pricing_map[5]['custom_handling_import']->item_value_special; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="custom_handling_import_12"
                        value="<?php echo $pricing_map[5]['custom_handling_import']->item_value_owner_special; ?>%" /></td>
                    <?php }?>
                    <td>percentage on occuring cost</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['custom_handling_import']->type,
                                "name" => 'custom_handling_import_4',
                                "id"	=> 'custom_handling_import_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="custom_handling_import_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['custom_handling_import']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['custom_handling_import']->show_customer_flag,
                                "name" => 'custom_handling_import_7',
                                "id"	=> 'custom_handling_import_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['custom_handling_import']->contract_terms,
                                "name" => 'custom_handling_import_8',
                                "id"	=> 'custom_handling_import_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['custom_handling_import']->billing_period,
                                "name" => 'custom_handling_import_9',
                                "id"	=> 'custom_handling_import_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Cash payment for item on delivery or cash expenditure (percentage)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_1"
                        value="<?php echo $pricing_map[1]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_2"
                        value="<?php echo $pricing_map[2]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_3"
                        value="<?php echo $pricing_map[3]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_5"
                        value="<?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_value; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_10"
                        value="<?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_value_owner; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_11"
                        value="<?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_value_special; ?>%" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_12"
                        value="<?php echo $pricing_map[5]['cash_payment_on_delivery_percentage']->item_value_owner_special; ?>%" /></td>
                    <?php }?>
                    <td>percentage</td>
                     <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['cash_payment_on_delivery_percentage']->type,
                                "name" => 'cash_payment_on_delivery_percentage_4',
                                "id"	=> 'cash_payment_on_delivery_percentage_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="cash_payment_on_delivery_percentage_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['cash_payment_on_delivery_percentage']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_percentage']->show_customer_flag,
                                "name" => 'cash_payment_on_delivery_percentage_7',
                                "id"	=> 'cash_payment_on_delivery_percentage_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_percentage']->contract_terms,
                                "name" => 'cash_payment_on_delivery_percentage_8',
                                "id"	=> 'cash_payment_on_delivery_percentage_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_percentage']->billing_period,
                                "name" => 'cash_payment_on_delivery_percentage_9',
                                "id"	=> 'cash_payment_on_delivery_percentage_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Cash payment for item on delivery or cash expenditure (minimum cost)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['cash_payment_on_delivery_mini_cost']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['cash_payment_on_delivery_mini_cost']->type,
                                "name" => 'cash_payment_on_delivery_mini_cost_4',
                                "id"	=> 'cash_payment_on_delivery_mini_cost_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="cash_payment_on_delivery_mini_cost_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['cash_payment_on_delivery_mini_cost']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_mini_cost']->show_customer_flag,
                                "name" => 'cash_payment_on_delivery_mini_cost_7',
                                "id"	=> 'cash_payment_on_delivery_mini_cost_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_mini_cost']->contract_terms,
                                "name" => 'cash_payment_on_delivery_mini_cost_8',
                                "id"	=> 'cash_payment_on_delivery_mini_cost_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['cash_payment_on_delivery_mini_cost']->billing_period,
                                "name" => 'cash_payment_on_delivery_mini_cost_9',
                                "id"	=> 'cash_payment_on_delivery_mini_cost_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>Pickup charge (only with confirmed appointment)</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['pickup_charge']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['pickup_charge']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['pickup_charge']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['pickup_charge']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['pickup_charge']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['pickup_charge']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="pickup_charge_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['pickup_charge']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['pickup_charge']->type,
                                "name" => 'pickup_charge_4',
                                "id"	=> 'pickup_charge_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="pickup_charge_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['pickup_charge']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['pickup_charge']->show_customer_flag,
                                "name" => 'pickup_charge_7',
                                "id"	=> 'pickup_charge_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['pickup_charge']->contract_terms,
                                "name" => 'pickup_charge_8',
                                "id"	=> 'pickup_charge_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['pickup_charge']->billing_period,
                                "name" => 'pickup_charge_9',
                                "id"	=> 'pickup_charge_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th>scan of additional pages</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['additional_pages_scanning_price']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['additional_pages_scanning_price']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['additional_pages_scanning_price']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="additional_pages_scanning_price_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['additional_pages_scanning_price']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['additional_pages_scanning_price']->type,
                                "name" => 'additional_pages_scanning_price_4',
                                "id"	=> 'additional_pages_scanning_price_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="additional_pages_scanning_price_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['additional_pages_scanning_price']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['additional_pages_scanning_price']->show_customer_flag,
                                "name" => 'additional_pages_scanning_price_7',
                                "id"	=> 'additional_pages_scanning_price_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['additional_pages_scanning_price']->contract_terms,
                                "name" => 'additional_pages_scanning_price_8',
                                "id"	=> 'additional_pages_scanning_price_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['additional_pages_scanning_price']->billing_period,
                                "name" => 'additional_pages_scanning_price_9',
                                "id"	=> 'additional_pages_scanning_price_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr style="background: rgb(217,217,217);">
                    <th>Services</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php } else{?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php }?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <th>Special requests, charged by time</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_1"
                        value="<?php echo APUtils::number_format($pricing_map[1]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_2"
                        value="<?php echo APUtils::number_format($pricing_map[2]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_3"
                        value="<?php echo APUtils::number_format($pricing_map[3]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_5"
                        value="<?php echo APUtils::number_format($pricing_map[5]['special_requests_charge_by_time']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_10"
                        value="<?php echo APUtils::number_format($pricing_map[5]['special_requests_charge_by_time']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_11"
                        value="<?php echo APUtils::number_format($pricing_map[5]['special_requests_charge_by_time']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="special_requests_charge_by_time_12"
                        value="<?php echo APUtils::number_format($pricing_map[5]['special_requests_charge_by_time']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td>EUR/hour</td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['special_requests_charge_by_time']->type,
                                "name" => 'special_requests_charge_by_time_4',
                                "id"	=> 'special_requests_charge_by_time_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="special_requests_charge_by_time_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['special_requests_charge_by_time']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['special_requests_charge_by_time']->show_customer_flag,
                                "name" => 'special_requests_charge_by_time_7',
                                "id"	=> 'special_requests_charge_by_time_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['special_requests_charge_by_time']->contract_terms,
                                "name" => 'special_requests_charge_by_time_8',
                                "id"	=> 'special_requests_charge_by_time_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['special_requests_charge_by_time']->billing_period,
                                "name" => 'special_requests_charge_by_time_9',
                                "id"	=> 'special_requests_charge_by_time_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_own_location_monthly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_monthly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_own_location_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_monthly']->type,
                                "name" => 'lease_of_workplace_for_own_location_monthly_4',
                                "id"	=> 'lease_of_workplace_for_own_location_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_own_location_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_monthly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_own_location_monthly_7',
                                "id"	=> 'lease_of_workplace_for_own_location_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_monthly']->contract_terms,
                                "name" => 'lease_of_workplace_for_own_location_monthly_8',
                                "id"	=> 'lease_of_workplace_for_own_location_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_monthly']->billing_period,
                                "name" => 'lease_of_workplace_for_own_location_monthly_9',
                                "id"	=> 'lease_of_workplace_for_own_location_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_own_location_quarterly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_quarterly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_own_location_quarterly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->type,
                                "name" => 'lease_of_workplace_for_own_location_quarterly_4',
                                "id"	=> 'lease_of_workplace_for_own_location_quarterly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_quarterly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_own_location_quarterly_7',
                                "id"	=> 'lease_of_workplace_for_own_location_quarterly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->contract_terms,
                                "name" => 'lease_of_workplace_for_own_location_quarterly_8',
                                "id"	=> 'lease_of_workplace_for_own_location_quarterly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_quarterly']->billing_period,
                                "name" => 'lease_of_workplace_for_own_location_quarterly_9',
                                "id"	=> 'lease_of_workplace_for_own_location_quarterly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_own_location_yearly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_yearly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_yearly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_own_location_yearly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_own_location_yearly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_yearly']->type,
                                "name" => 'lease_of_workplace_for_own_location_yearly_4',
                                "id"	=> 'lease_of_workplace_for_own_location_yearly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_own_location_yearly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_own_location_yearly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_yearly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_own_location_yearly_7',
                                "id"	=> 'lease_of_workplace_for_own_location_yearly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_yearly']->contract_terms,
                                "name" => 'lease_of_workplace_for_own_location_yearly_8',
                                "id"	=> 'lease_of_workplace_for_own_location_yearly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_own_location_yearly']->billing_period,
                                "name" => 'lease_of_workplace_for_own_location_yearly_9',
                                "id"	=> 'lease_of_workplace_for_own_location_yearly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_clevverMail_location_monthly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_monthly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_clevverMail_location_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->type,
                                "name" => 'lease_of_workplace_for_clevverMail_location_monthly_4',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_clevverMail_location_monthly_7',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->contract_terms,
                                "name" => 'lease_of_workplace_for_clevverMail_location_monthly_8',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_monthly']->billing_period,
                                "name" => 'lease_of_workplace_for_clevverMail_location_monthly_9',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_clevverMail_location_quarterly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_quarterly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_clevverMail_location_quarterly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->type,
                                "name" => 'lease_of_workplace_for_clevverMail_location_quarterly_4',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_quarterly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_quarterly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_clevverMail_location_quarterly_7',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_quarterly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->contract_terms,
                                "name" => 'lease_of_workplace_for_clevverMail_location_quarterly_8',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_quarterly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_quarterly']->billing_period,
                                "name" => 'lease_of_workplace_for_clevverMail_location_quarterly_9',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_quarterly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_workplace_for_clevverMail_location_yearly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_yearly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_yearly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_workplace_for_clevverMail_location_yearly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_workplace_for_clevverMail_location_yearly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->type,
                                "name" => 'lease_of_workplace_for_clevverMail_location_yearly_4',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_yearly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_workplace_for_clevverMail_location_yearly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->show_customer_flag,
                                "name" => 'lease_of_workplace_for_clevverMail_location_yearly_7',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_yearly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->contract_terms,
                                "name" => 'lease_of_workplace_for_clevverMail_location_yearly_8',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_yearly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_workplace_for_clevverMail_location_yearly']->billing_period,
                                "name" => 'lease_of_workplace_for_clevverMail_location_yearly_9',
                                "id"	=> 'lease_of_workplace_for_clevverMail_location_yearly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_own_location_monthly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_monthly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_own_location_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_monthly']->type,
                                "name" => 'lease_of_receptionist_own_location_monthly_4',
                                "id"	=> 'lease_of_receptionist_own_location_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_own_location_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_own_location_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_monthly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_own_location_monthly_7',
                                "id"	=> 'lease_of_receptionist_own_location_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_monthly']->contract_terms,
                                "name" => 'lease_of_receptionist_own_location_monthly_8',
                                "id"	=> 'lease_of_receptionist_own_location_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_monthly']->billing_period,
                                "name" => 'lease_of_receptionist_own_location_monthly_9',
                                "id"	=> 'lease_of_receptionist_own_location_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_own_location_quarterly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_quarterly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_quarterly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_quarterly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_own_location_quarterly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_quarterly']->type,
                                "name" => 'lease_of_receptionist_own_location_quarterly_4',
                                "id"	=> 'lease_of_receptionist_own_location_quarterly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_own_location_quarterly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_own_location_quarterly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_quarterly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_own_location_quarterly_7',
                                "id"	=> 'lease_of_receptionist_own_location_quarterly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_quarterly']->contract_terms,
                                "name" => 'lease_of_receptionist_own_location_quarterly_8',
                                "id"	=> 'lease_of_receptionist_own_location_quarterly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_quarterly']->billing_period,
                                "name" => 'lease_of_receptionist_own_location_quarterly_9',
                                "id"	=> 'lease_of_receptionist_own_location_quarterly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_own_location_yearly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_yearly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_yearly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_own_location_yearly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_own_location_yearly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_yearly']->type,
                                "name" => 'lease_of_receptionist_own_location_yearly_4',
                                "id"	=> 'lease_of_receptionist_own_location_yearly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_own_location_yearly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_own_location_yearly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_yearly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_own_location_yearly_7',
                                "id"	=> 'lease_of_receptionist_own_location_yearly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_yearly']->contract_terms,
                                "name" => 'lease_of_receptionist_own_location_yearly_8',
                                "id"	=> 'lease_of_receptionist_own_location_yearly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_own_location_yearly']->billing_period,
                                "name" => 'lease_of_receptionist_own_location_yearly_9',
                                "id"	=> 'lease_of_receptionist_own_location_yearly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_clevverMail_location_monthly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_monthly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_clevverMail_location_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->type,
                                "name" => 'lease_of_receptionist_clevverMail_location_monthly_4',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_clevverMail_location_monthly_7',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->contract_terms,
                                "name" => 'lease_of_receptionist_clevverMail_location_monthly_8',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_monthly']->billing_period,
                                "name" => 'lease_of_receptionist_clevverMail_location_monthly_9',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_clevverMail_location_quarterly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_quarterly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_quarterly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_quarterly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_clevverMail_location_quarterly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->type,
                                "name" => 'lease_of_receptionist_clevverMail_location_quarterly_4',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_quarterly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_quarterly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_clevverMail_location_quarterly_7',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_quarterly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->contract_terms,
                                "name" => 'lease_of_receptionist_clevverMail_location_quarterly_8',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_quarterly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_quarterly']->billing_period,
                                "name" => 'lease_of_receptionist_clevverMail_location_quarterly_9',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_quarterly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('lease_of_receptionist_clevverMail_location_yearly.label'); ?></th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_1"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_2"
                        value="<?php echo APUtils::number_format(@$pricing_map[2]['lease_of_receptionist_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_3"
                        value="<?php echo APUtils::number_format(@$pricing_map[3]['lease_of_receptionist_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <?php } else{?>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['lease_of_receptionist_clevverMail_location_yearly']->item_value_owner_special); ?>" /></td>
                    <?php }?>
                    <td><?php echo lang('lease_of_receptionist_clevverMail_location_yearly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->type,
                                "name" => 'lease_of_receptionist_clevverMail_location_yearly_4',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_yearly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="lease_of_receptionist_clevverMail_location_yearly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->show_customer_flag,
                                "name" => 'lease_of_receptionist_clevverMail_location_yearly_7',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_yearly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->contract_terms,
                                "name" => 'lease_of_receptionist_clevverMail_location_yearly_8',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_yearly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['lease_of_receptionist_clevverMail_location_yearly']->billing_period,
                                "name" => 'lease_of_receptionist_clevverMail_location_yearly_9',
                                "id"	=> 'lease_of_receptionist_clevverMail_location_yearly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <?php if($pricing_template->pricing_type == 'Enterprise'){ ?>
                <tr style="background: rgb(217,217,217);">
                    <th>Enterprise</th>
                    <?php if($pricing_template->pricing_type != 'Enterprise'){ ?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php } else{?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <?php }?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <th><?php echo lang('own_location_monthly.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="own_location_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_location_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="own_location_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_location_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="own_location_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_location_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?>" <?php echo $readonly;?> name="own_location_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_location_monthly']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('own_location_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['own_location_monthly']->type,
                                "name" => 'own_location_monthly_4',
                                "id"	=> 'own_location_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="own_location_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['own_location_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['own_location_monthly']->show_customer_flag,
                                "name" => 'own_location_monthly_7',
                                "id"	=> 'own_location_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['own_location_monthly']->contract_terms,
                                "name" => 'own_location_monthly_8',
                                "id"	=> 'own_location_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['own_location_monthly']->billing_period,
                                "name" => 'own_location_monthly_9',
                                "id"	=> 'own_location_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('touch_panel_at_own_location_quarterly.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('touch_panel_at_own_location_quarterly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['touch_panel_at_own_location_quarterly']->type,
                                "name" => 'touch_panel_at_own_location_quarterly_4',
                                "id"	=> 'touch_panel_at_own_location_quarterly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="touch_panel_at_own_location_quarterly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['touch_panel_at_own_location_quarterly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['touch_panel_at_own_location_quarterly']->show_customer_flag,
                                "name" => 'touch_panel_at_own_location_quarterly_7',
                                "id"	=> 'touch_panel_at_own_location_quarterly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['touch_panel_at_own_location_quarterly']->contract_terms,
                                "name" => 'touch_panel_at_own_location_quarterly_8',
                                "id"	=> 'touch_panel_at_own_location_quarterly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['touch_panel_at_own_location_quarterly']->billing_period,
                                "name" => 'touch_panel_at_own_location_quarterly_9',
                                "id"	=> 'touch_panel_at_own_location_quarterly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('own_mobile_app_monthly.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_mobile_app_monthly_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_mobile_app_monthly']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_mobile_app_monthly_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_mobile_app_monthly']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_mobile_app_monthly_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_mobile_app_monthly']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_mobile_app_monthly_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_mobile_app_monthly']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('own_mobile_app_monthly.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => $pricing_map[1]['own_mobile_app_monthly']->type,
                                "name" => 'own_mobile_app_monthly_4',
                                "id"	=> 'own_mobile_app_monthly_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="own_mobile_app_monthly_6"
                        value="<?php echo APUtils::number_format($pricing_map[1]['own_mobile_app_monthly']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => $pricing_map[1]['own_mobile_app_monthly']->show_customer_flag,
                                "name" => 'own_mobile_app_monthly_7',
                                "id"	=> 'own_mobile_app_monthly_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => $pricing_map[1]['own_mobile_app_monthly']->contract_terms,
                                "name" => 'own_mobile_app_monthly_8',
                                "id"	=> 'own_mobile_app_monthly_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => $pricing_map[1]['own_mobile_app_monthly']->billing_period,
                                "name" => 'own_mobile_app_monthly_9',
                                "id"	=> 'own_mobile_app_monthly_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th><?php echo lang('api_access.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="api_access_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['api_access']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="api_access_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['api_access']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="api_access_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['api_access']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="api_access_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['api_access']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('api_access.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => @$pricing_map[1]['api_access']->type,
                                "name" => 'api_access_4',
                                "id"	=> 'api_access_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="api_access_6"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['api_access']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => @$pricing_map[1]['api_access']->show_customer_flag,
                                "name" => 'api_access_7',
                                "id"	=> 'api_access_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => @$pricing_map[1]['api_access']->contract_terms,
                                "name" => 'api_access_8',
                                "id"	=> 'api_access_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => @$pricing_map[1]['api_access']->billing_period,
                                "name" => 'api_access_9',
                                "id"	=> 'api_access_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo lang('clevver_subdomain.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="clevver_subdomain_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['clevver_subdomain']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="clevver_subdomain_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['clevver_subdomain']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="clevver_subdomain_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['clevver_subdomain']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="clevver_subdomain_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['clevver_subdomain']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('clevver_subdomain.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => @$pricing_map[1]['clevver_subdomain']->type,
                                "name" => 'clevver_subdomain_4',
                                "id"	=> 'clevver_subdomain_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="clevver_subdomain_6"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['clevver_subdomain']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => @$pricing_map[1]['clevver_subdomain']->show_customer_flag,
                                "name" => 'clevver_subdomain_7',
                                "id"	=> 'clevver_subdomain_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => @$pricing_map[1]['clevver_subdomain']->contract_terms,
                                "name" => 'clevver_subdomain_8',
                                "id"	=> 'clevver_subdomain_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => @$pricing_map[1]['clevver_subdomain']->billing_period,
                                "name" => 'clevver_subdomain_9',
                                "id"	=> 'clevver_subdomain_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo lang('own_domain.label'); ?></th>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_domain_5"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_domain']->item_value); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_domain_10"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_domain']->item_value_owner); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_domain_11"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_domain']->item_value_special); ?>" /></td>
                    <td><input class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly?> <?php echo $readonly;?> name="own_domain_12"
                        value="<?php echo APUtils::number_format(@$pricing_map[5]['own_domain']->item_value_owner_special); ?>" /></td>
                    <td><?php echo lang('own_domain.dimension'); ?></td>
                    <td>
                        <?php 
                        echo my_form_dropdown(array(
                                "data" => $type_data,
                                "value_key" => 'id',
                                "label_key" => 'label',
                                "value" => @$pricing_map[1]['own_domain']->type,
                                "name" => 'own_domain_4',
                                "id"	=> 'own_domain_4',
                                "clazz" => 'input-width',
                                "style" => 'width:150px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td><input  class="input-txt-none <?php echo $readonlyClass?>" type="text" <?php echo $readonly;?> name="own_domain_6"
                        value="<?php echo APUtils::number_format(@$pricing_map[1]['own_domain']->rev_share_in_percent); ?>" /></td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::DROPDOWN_ACTIVE_CODE,
                                "value" => @$pricing_map[1]['own_domain']->show_customer_flag,
                                "name" => 'own_domain_7',
                                "id"	=> 'own_domain_7',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_CONTRACT_TERM_CODE,
                                "value" => @$pricing_map[1]['own_domain']->contract_terms,
                                "name" => 'own_domain_8',
                                "id"	=> 'own_domain_8',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                    <td>
                        <?php 
                        echo code_master_form_dropdown(array(
                                "code" => APConstants::PRICING_BILLING_PERIOD_CODE,
                                "value" => @$pricing_map[1]['own_domain']->billing_period,
                                "name" => 'own_domain_9',
                                "id"	=> 'own_domain_9',
                                "clazz" => 'input-width',
                                "style" => 'width:100px',
                                "has_empty" => true
                        ));
                        ?>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <th>&nbsp;</th>
                    <td colspan="10"><button id="submitButton">Submit</button>
                        <button id="cancelButton" type="button">Cancel</button></td>
                </tr>
            </table>
        </div>
        <input type="hidden" name="pricing_template_id" value="<?php echo $pricing_template->id;?>" />
        <input type="hidden" name="id" value="<?php echo $pricing_template->id;?>" />
    </form>
</div>
<div class="clear-height"></div>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/price/js/PricingTemplateForm.js"></script>
<script>
    jQuery(document).ready(function() {
        var baseUrl = '<?php echo base_url(); ?>';

        PricingTemplateForm.init(baseUrl);
    });
</script>