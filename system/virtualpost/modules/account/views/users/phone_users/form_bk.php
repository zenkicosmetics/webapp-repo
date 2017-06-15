<style>
    .user_setting_table tr td {
        line-height: 0.5em;
    }
    .user_setting_inner_table tr td {
        line-height: 0.2em;
    }   
</style>
<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'account/users/add';
} else {
    $submit_url = base_url() . 'account/users/edit/'.$user->user_id;
}
?>
<form id="addEditUserForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table class="user_setting_table">
    	<tr>
            <th>Name <span class="required">*</span></th>
            <td><input type="text" id="addEditUserForm_name" name="name" value="<?php echo $user->name ?>" class="input-width" maxlength=50 /></td>
            <th>E-mail <span class="required">*</span></th>
            <td><input type="text" id="addEditUserForm_email" name="email" value="<?php echo $user->email ?>" class="input-width" maxlength=50 /></td>
        </tr>
        <?php if($action_type == 'add'): ?>
        <tr>
            <th>Password <span class="required">*</span></th>
            <td><input type="password" id="addEditUserForm_password" name="password" value="" class="input-width custom_autocomplete" maxlength=50 /></td>
            <th>Retype Password <span class="required">*</span></th>
            <td><input type="password" id="addEditUserForm_repeat_password" name="repeat_password" value=""	class="input-width custom_autocomplete" maxlength=50 /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th>Status</th>
            <td>
                <select class="input-width" id="status_flag" name="status_flag" style = "width: 262px;">
                    <option value="0" <?php echo $user->activated_flag == 0 ? 'selected="selected"' : ''; ?>><?php echo lang('users.status.not_activated') ?></option>
                    <option value="1" <?php echo $user->activated_flag == 1 ? 'selected="selected"' : ''; ?>><?php echo lang('users.status.activated') ?></option>
                </select>
            </td>
            <th></th>
            <td style="text-align: left">
                
            </td>
        </tr>
        <?php if($action_type != 'add'): ?>
        <tr>
            <th>Country</th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=> $sonetel_user->location->country,
                            "name" => 'country_code',
                            "id"    => 'country_code_3',
                            "clazz" => 'input-width',
                            "style" => 'width: 262px',
                            "has_empty" => false
                    ));
                ?>
                
            </td>
            <th>Area</th>
            <td>
                <?php 
                // #472: added
                echo my_form_dropdown(array(
                        "data" => $list_area,
                        "value_key"=> 'area_code',
                        "label_key"=> 'area_name',
                        "value"=> $sonetel_user->location->area_code,
                        "name" => 'area_code',
                        "id"    => 'area_code',
                        "clazz" => 'input-width',
                        "style" => 'width: 262px',
                        "has_empty" => false
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Call Thru</th>
            <td>PIN: <?php $pin_property_name = 'callthru-pin'; echo $sonetel_user->call->outgoing->$pin_property_name; ?></td>
            <th>Call To</th>
            <td>
                <table class="user_setting_inner_table">
                    <tr>
                        <td>First do this</td>
                        <td>
                            <?php 
                            $text = '';
                            $action = $sonetel_user->call->incoming->first_action;
                            if ($action->action == 'ring') {
                                $text = 'Ring to my '.$action->to;
                                if (isset($action->id) && $action->id != '-NA-') {
                                    $text = $text.' ('.$action->id.')';
                                }
                            } else if ($action->action == 'forward') {
                                $text = 'Forward calls to my '.$action->to;
                                if (isset($action->id) && $action->id != '-NA-') {
                                    $text = $text.' ('.$action->id.')';
                                }
                            } else {
                                $text = 'Disconnect the call';
                            }
                            echo $text;
                            ?>
                        </td>
                        <td><a href="#" id="first_action_link" style="text-decoration: underline">Change</a></td>
                    </tr>
                    <tr>
                        <td>If no answer</td>
                        <td>
                            <?php 
                            $text = '';
                            $action = $sonetel_user->call->incoming->second_action;
                            if ($action->action == 'ring') {
                                $text = 'Ring to my '.$action->to;
                                if (isset($action->id) && $action->id != '-NA-') {
                                    $text = $text.' ('.$action->id.')';
                                }
                            } else if ($action->action == 'forward') {
                                $text = 'Forward calls to my '.$action->to;
                                if (isset($action->id) && $action->id != '-NA-') {
                                    $text = $text.' ('.$action->id.')';
                                }
                            } else {
                                $text = 'Disconnect the call';
                            }
                            echo $text;
                            ?>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>My phone numbers</th>
            <td>
                <?php $numbers = $sonetel_user->numbers; ?>
                <table class="user_setting_inner_table">
                    <?php foreach ($numbers as $number) { ?>
                    <tr>
                        <td><?php echo $number->number.' ('.lang('phone_number_type_'.$number->number_type).')'; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
            <th>My Phones</th>
            <td>
                <?php $phones = $sonetel_user->phones; ?>
                <?php if ($phones != 'no entries') { ?>
                <?php $phones_list = $phones->list; ?>
                <table class="user_setting_inner_table">
                    <?php foreach ($phones_list as $phone) { ?>
                    <tr>
                        <td><?php echo lang('phone_type_'.$phone->phone_type);?></td>
                        <td><?php if ($phone->phone_type == 'sip') { $phone->sip_details; } else { echo $phone->phnum;}?></td>
                    </tr>
                    <?php } ?>
                </table>
                <?php } else { ?>
                <table class="user_setting_inner_table">
                    <tr>
                        <td>no entries<td>
                        <td><td>
                    </tr>
                </table>
                <?php } ?>
            </td>
        </tr>
        <?php endif; ?>
    </table> 

    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" />
    <input type="hidden" id="addEditUserForm_id" name="id" value="<?php echo $user->user_id; ?>" />
</form>
<div class="hide">
    <div id="callToSettingWindow" title="Setting Call" class="input-form dialog-form"></div>
</div>
<script>
jQuery(document).ready(function () {
    // User click to call to setting
    $('#first_action_link').live('click', function(){
        changeCallToSetting();
    });
    
    // User change the country code
    $('#country_code_3').live('change', function() {
        var url = '<?php echo base_url() . "account/users/load_area_code_target"?>';
        var country_code = $('#country_code_3').val();
        $.bindSelect(url, 'country_code=' + country_code, 'area_code', '', '', function() {
        });
    });
    
    // Change CallToSetting
    function changeCallToSetting() {
        // Clear control of all dialog form
        $('#callToSettingWindow').html('');
        var userId = $('#addEditUserForm_id').val();
        var changeCallToSettingUrl = '<?php echo base_url()?>account/users/change_call_setting_phone_users?user_id='+userId;
        // Open new dialog
        $('#callToSettingWindow').openDialog({
            autoOpen: false,
            height: 420,
            width: 800,
            modal: true,
            open: function () {
                $(this).load(changeCallToSettingUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    saveChangeCallToSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#callToSettingWindow').dialog('option', 'position', 'center');
        $('#callToSettingWindow').dialog('open');
    }
    // Save change call to setting
    function saveChangeCallToSetting() {
        var submitUrl = $('#changeCallToSettingForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'changeCallToSettingForm',
            success: function (data) {
                if (data.status) {
                    $('#callToSettingWindow').dialog('close');
                    $.displayInfor(data.message, null, function () {
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
});
</script>
