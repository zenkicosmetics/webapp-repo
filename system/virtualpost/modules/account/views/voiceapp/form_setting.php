<?php
    $submit_url = base_url() . 'account/voiceapp/edit_setting';
?>
<form id="addEditVoiceAppForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>Name <span class="required">*</span></th>
            <td><input type="text" id="addEditVoiceAppForm_name" name="name" value="<?php echo $voiceapp->name ?>" class="input-width" maxlength=250 /></td>
            <th>Short code <span class="required">*</span></th>
            <td><input type="text" disabled="disabled" id="addEditVoiceAppForm_shortcode" name="shortcode" value="<?php echo $voiceapp_detail->shortcode ?>" class="input-width" maxlength=250 /></td>
        </tr>
    	<tr>
            <th>Language <span class="required">*</span></th>
            <td>
                <select name="voice" id="voice" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="en" <?php if ($voiceapp_detail->voice == 'en') {?> selected="selected" <?php } ?>>English</option>
                </select>
            </td>
            <th>Sip address <span class="required">*</span></th>
            <td><input type="text" disabled="disabled" id="addEditVoiceAppForm_sip_address" name="sip_address" value="<?php echo $voiceapp_detail->sip_address ?>" class="input-width" maxlength=250 /></td>
        </tr>
        <?php if ($voiceapp->app_type == 'ivr') { ?>
        <tr>
            <th>Action</th>
            <td>
                <input type="checkbox" name="action_play_welcome" value="1" <?php if ($voiceapp_detail->play_welcome == 'yes') {?> checked="checked" <?php } ?> />Play the welcome message. <br/>
                <input type="checkbox" name="action_get_extension" value="1" <?php if ($voiceapp_detail->get_extension == 'yes') {?> checked="checked" <?php } ?> />Offer callers to enter extension (Auto attendant).<br/>
                <input type="checkbox" name="action_play_menu" value="1" <?php if ($voiceapp_detail->play_menu == 'yes') {?> checked="checked" <?php } ?> />Play the main menu. <br/>
            </td>
            <th></th>
            <td></td>
        </tr>
        <?php } ?>
        <?php if ($voiceapp->app_type == 'mailbox') { ?>
            <th>Deliver to <span class="required">*</span></th>
            <td>
                <select name="deliver_to" id="deliver_to" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="user" <?php if ($voiceapp_detail->deliver_to == 'user') {?> selected="selected" <?php } ?>>user</option>
                    <option value="email" <?php if ($voiceapp_detail->deliver_to == 'email') {?> selected="selected" <?php } ?>>email</option>
                </select>
            </td>
            <th>Deliver to details  <span class="required">*</span></th>
            <td>
                <?php 
                    $enable_list_user = false;
                    $user_list_style = 'display: none;';
                    if ($voiceapp_detail->deliver_to == 'user') {
                        $enable_list_user = true;
                        $user_list_style = '';
                    }
                ?>
                <input type="text" id="addEditVoiceAppForm_deliver_to_details_01" name="deliver_to_details_01" 
                       value="<?php echo $voiceapp_detail->deliver_to_details ?>" class="input-width" maxlength=250
                       style="<?php if ($enable_list_user) {?> display: none; <?php }?> "/>
                
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_user_phone,
                            "value_key"=> 'phone_user_id',
                            "label_key"=> 'user_name',
                            "value" => $voiceapp_detail->deliver_to_details,
                            "name" => 'deliver_to_details_02',
                            "id"    => 'addEditVoiceAppForm_deliver_to_details_02',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; '.$user_list_style,
                            "has_empty" => false
                    ));
                ?>
            </td>
        <?php } ?>
    </table>
    
    <!-- Display for IVR type -->
    <?php if ($voiceapp->app_type == 'ivr') { ?>
    <table>
        <tr>
            <th>Main Menu</th>
            <td></td>
            <td style="text-align: right">Show more</td>
        </tr>
        <tr>
            <th>Press menu 1</th>
            <td>
                <select name="menu_digit_0_action" id="menu_digit_0_action" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="call_user" <?php if ($voiceapp_detail->menu->digit_0->action.'_'.$voiceapp_detail->menu->digit_0->to == 'call_user') {?> selected="selected" <?php } ?> >Connect to user</option>
                    <option value="call_other" <?php if ($voiceapp_detail->menu->digit_0->action.'_'.$voiceapp_detail->menu->digit_0->to == 'call_other') {?> selected="selected" <?php } ?>>Connect to other</option>
                    <option value="play_prompt" <?php if ($voiceapp_detail->menu->digit_0->action.'_'.$voiceapp_detail->menu->digit_0->to == 'play_prompt') {?> selected="selected" <?php } ?>>Play message</option>
                    <option value="connect_app" <?php if ($voiceapp_detail->menu->digit_0->action.'_'.$voiceapp_detail->menu->digit_0->to == 'connect_app') {?> selected="selected" <?php } ?>>Connect to voice app</option>
                    <option value="disconnect" <?php if ($voiceapp_detail->menu->digit_0->action.'_'.$voiceapp_detail->menu->digit_0->to == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                </select>
            </td>
            <td style="text-align: right">
                <?php 
                    $user_list_style = 'display: none;';
                    if ($voiceapp_detail->menu->digit_0->action != 'call_other') {
                        $user_list_style = '';
                    }
                ?>
                <input type="text" id="menu_digit_0_to_other" name="menu_digit_0_to_other" 
                       value="<?php echo $voiceapp_detail->menu->digit_0->id; ?>" class="input-width" maxlength=250
                       style="width: 240px; <?php if (empty($user_list_style)) { echo 'display: none;'; }?> "/>
                
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_user_phone,
                            "value_key"=> 'phone_user_id',
                            "label_key"=> 'user_name',
                            "value" => $voiceapp_detail->menu->digit_0->id,
                            "name" => 'menu_digit_0_to',
                            "id"    => 'menu_digit_0_to',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; '.$user_list_style,
                            "has_empty" => true
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Press menu 2</th>
            <td>
                <select name="menu_digit_1_action" id="menu_digit_1_action" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="call_user" <?php if ($voiceapp_detail->menu->digit_1->action.'_'.$voiceapp_detail->menu->digit_1->to == 'call_user') {?> selected="selected" <?php } ?> >Connect to user</option>
                    <option value="call_other" <?php if ($voiceapp_detail->menu->digit_1->action.'_'.$voiceapp_detail->menu->digit_1->to == 'call_other') {?> selected="selected" <?php } ?>>Connect to other</option>
                    <option value="play_prompt" <?php if ($voiceapp_detail->menu->digit_1->action.'_'.$voiceapp_detail->menu->digit_1->to == 'play_prompt') {?> selected="selected" <?php } ?>>Play message</option>
                    <option value="connect_app" <?php if ($voiceapp_detail->menu->digit_1->action.'_'.$voiceapp_detail->menu->digit_1->to == 'connect_app') {?> selected="selected" <?php } ?>>Connect to voice app</option>
                    <option value="disconnect" <?php if ($voiceapp_detail->menu->digit_1->action.'_'.$voiceapp_detail->menu->digit_1->to == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                </select>
            </td>
            <td style="text-align: right">
                <?php 
                    $user_list_style = 'display: none;';
                    if ($voiceapp_detail->menu->digit_1->action != 'call_other') {
                        $user_list_style = '';
                    }
                ?>
                <input type="text" id="menu_digit_1_to_other" name="menu_digit_1_to_other" 
                       value="<?php echo $voiceapp_detail->menu->digit_1->id; ?>" class="input-width" maxlength=250
                       style="width: 240px; <?php if (empty($user_list_style)) { echo 'display: none;'; }?> "/>
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_user_phone,
                            "value_key"=> 'phone_user_id',
                            "label_key"=> 'user_name',
                            "value" => $voiceapp_detail->menu->digit_1->id,
                            "name" => 'menu_digit_1_to',
                            "id"    => 'menu_digit_1_to',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; '.$user_list_style,
                            "has_empty" => true
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Press menu 3</th>
            <td>
                <select name="menu_digit_2_action" id="menu_digit_2_action" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="call_user" <?php if ($voiceapp_detail->menu->digit_2->action.'_'.$voiceapp_detail->menu->digit_2->to == 'call_user') {?> selected="selected" <?php } ?> >Connect to user</option>
                    <option value="call_other" <?php if ($voiceapp_detail->menu->digit_2->action.'_'.$voiceapp_detail->menu->digit_2->to == 'call_other') {?> selected="selected" <?php } ?>>Connect to other</option>
                    <option value="play_prompt" <?php if ($voiceapp_detail->menu->digit_2->action.'_'.$voiceapp_detail->menu->digit_2->to == 'play_prompt') {?> selected="selected" <?php } ?>>Play message</option>
                    <option value="connect_app" <?php if ($voiceapp_detail->menu->digit_2->action.'_'.$voiceapp_detail->menu->digit_2->to == 'connect_app') {?> selected="selected" <?php } ?>>Connect to voice app</option>
                    <option value="disconnect" <?php if ($voiceapp_detail->menu->digit_2->action.'_'.$voiceapp_detail->menu->digit_2->to == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                </select>
            </td>
            <td style="text-align: right">
                <?php 
                    $user_list_style = 'display: none;';
                    if ($voiceapp_detail->menu->digit_2->action != 'call_other') {
                        $user_list_style = '';
                    }
                ?>
                <input type="text" id="menu_digit_2_to_other" name="menu_digit_2_to_other" 
                       value="<?php echo $voiceapp_detail->menu->digit_2->id; ?>" class="input-width" maxlength=250
                       style="width: 240px; <?php if (empty($user_list_style)) { echo 'display: none;'; }?> "/>
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_user_phone,
                            "value_key"=> 'phone_user_id',
                            "label_key"=> 'user_name',
                            "value" => $voiceapp_detail->menu->digit_2->id,
                            "name" => 'menu_digit_2_to',
                            "id"    => 'menu_digit_2_to',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; '.$user_list_style,
                            "has_empty" => true
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Press menu 4</th>
            <td>
                <select name="menu_digit_3_action" id="menu_digit_3_action" class="input-width" style="width: 260px;">
                    <option value=""></option>
                    <option value="call_user" <?php if ($voiceapp_detail->menu->digit_3->action.'_'.$voiceapp_detail->menu->digit_3->to == 'call_user') {?> selected="selected" <?php } ?> >Connect to user</option>
                    <option value="call_other" <?php if ($voiceapp_detail->menu->digit_3->action.'_'.$voiceapp_detail->menu->digit_3->to == 'call_other') {?> selected="selected" <?php } ?>>Connect to other</option>
                    <option value="play_prompt" <?php if ($voiceapp_detail->menu->digit_3->action.'_'.$voiceapp_detail->menu->digit_3->to == 'play_prompt') {?> selected="selected" <?php } ?>>Play message</option>
                    <option value="connect_app" <?php if ($voiceapp_detail->menu->digit_3->action.'_'.$voiceapp_detail->menu->digit_3->to == 'connect_app') {?> selected="selected" <?php } ?>>Connect to voice app</option>
                    <option value="disconnect" <?php if ($voiceapp_detail->menu->digit_3->action.'_'.$voiceapp_detail->menu->digit_3->to == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                </select>
            </td>
            <td style="text-align: right">
                <?php 
                    $user_list_style = 'display: none;';
                    if ($voiceapp_detail->menu->digit_3->action != 'call_other') {
                        $user_list_style = '';
                    }
                ?>
                <input type="text" id="menu_digit_3_to_other" name="menu_digit_3_to_other" 
                       value="<?php echo $voiceapp_detail->menu->digit_3->id; ?>" class="input-width" maxlength=250
                       style="width: 240px; <?php if (empty($user_list_style)) { echo 'display: none;'; }?> "/>
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_user_phone,
                            "value_key"=> 'phone_user_id',
                            "label_key"=> 'user_name',
                            "value" => $voiceapp_detail->menu->digit_3->id,
                            "name" => 'menu_digit_3_to',
                            "id"    => 'menu_digit_3_to',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; '.$user_list_style,
                            "has_empty" => true
                    ));
                ?>
            </td>
        </tr>
    </table>
    <?php } ?>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" />
    <input type="hidden" id="addEditVoiceAppForm_id" name="id" value="<?php echo $voiceapp->id?>" />
</form>

<script>
jQuery(document).ready(function () {
    var app_type = '<?php echo $voiceapp->app_type ?>';
    // When user change the delivery
    $('#deliver_to').live('change', function() {
        var deliver_to = $('#deliver_to').val();
        if (deliver_to == 'user') {
            $('#addEditVoiceAppForm_deliver_to_details_01').hide();
            $('#addEditVoiceAppForm_deliver_to_details_02').show();
        } else if (deliver_to == 'email') {
            $('#addEditVoiceAppForm_deliver_to_details_01').show();
            $('#addEditVoiceAppForm_deliver_to_details_02').hide();
        }
    });
        
    $('#menu_digit_0_action, #menu_digit_1_action, #menu_digit_2_action, #menu_digit_3_action').change(function(){
        var action = $(this).val();
        var index = $(this).attr('id').substring(11, 12);
        var url = '<?php echo base_url() . "account/users/load_voiceapp_target"?>';
        if (action !== '') {
            $.bindSelect(url, 'menu_digit_action=' + action, 'menu_digit_' + index + '_to', '', '', function() {});
            if (action == 'call_other') {
                $('#menu_digit_' + index + '_to_other').show();
                $('#menu_digit_' + index + '_to').hide();
            } else {
                $('#menu_digit_' + index + '_to_other').hide();
                $('#menu_digit_' + index + '_to').show();
            }
        }
    });
    if (app_type == 'ivr') {
        $('#changeVoiceAppResponseWindow').dialog('option', 'height', '570');
    } else if (app_type == 'mailbox') {
        $('#changeVoiceAppResponseWindow').dialog('option', 'height', '370');
    }
});
</script>
