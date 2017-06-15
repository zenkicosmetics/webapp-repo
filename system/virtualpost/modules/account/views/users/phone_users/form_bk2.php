<style>
    .user_setting_table tr td, .user_setting_table tr th {
        padding: 0.2em 0.5em;
    }
    .user_setting_inner_table tr td {
        line-height: 0.2em;
    }
    .user_setting_table_02 tr td, .user_setting_table_02 tr th {
        padding: 0px;
    }
</style>
<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'account/users/add_phone_users';
} else {
    $submit_url = base_url() . 'account/users/edit_phone_users?customer_id='.$user->customer_id;
}
?>

<!-- Include -->
<?php include ("system/virtualpost/modules/account/views/users/general_users/form_partial.php"); ?>

<?php if($action_type == 'edit') { ?>

<table class="user_setting_table_02" style="margin-top: 10px;">
    <tr>
        <td style="width: 50%">
            <span style="margin-left: 10px; font-weight: bold;">
                Phone Numbers:
                <button type="button" id="addNewPhoneNumberLink" class="input-btn btn-yellow">Add</button>
            </span>
            <div id="gridwraperPhoneNumber" style="margin-left: 10px; margin-top: 5px;">
                <div id="searchTablePhoneNumberResult">
                    <table id="dataGridPhoneNumberResult"></table>
                    <div id="dataGridPhoneNumberPager"></div>
                </div>
            </div>
        </td>
        <td style="width: 50%">
            <span style="margin-left: 10px; font-weight: bold;">
                Targets:
                <button type="button" id="addNewPhonesLink" class="input-btn btn-yellow">Add</button>
            </span>
            <div id="gridwraperPhones" style="margin-left: 10px; margin-top: 5px;">
                <div id="searchTablePhonesResult">
                    <table id="dataGridPhonesResult"></table>
                    <div id="dataGridPhonesPager"></div>
                </div>
            </div>
        </td>
    </tr>
</table>

<div style="margin-left: 10px; margin-top: 5px; font-weight: bold;">Phone Number Setting</div>
<table class="user_setting_table" style="margin-top: 10px;">
    <tr>
        <th>Location:</th>
        <td>
            <span id="changeUserLocationText">
            <?php if (!empty($country)) { echo $country->country_name. ' '.$country->phone_country_code; } else { echo 'N/A';}?>
            </span>
        </td>
        <td><a id="changeUserLocationLink" href="#" style="text-decoration: underline">Change</a></td>
        <th>Calls to this User:</th>
        <td>
            <?php 
                $text = '';
                if (!empty($sonetel_user)) {
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
                }
                echo $text;
            ?>
        </td>
        <td><a id="changeUserFirstActionLink" href="#" style="text-decoration: underline">Change</a></td>
    </tr>
    <tr>
        <th>Area:</th>
        <td>
            <span id="changeUserAreaText">
            <?php if (!empty($select_area)) {echo $select_area->area_name;} else { echo 'N/A';}?>
            </span>
        </td>
        <td><a id="changeUserAreaLink" href="#" style="text-decoration: underline">Change</a></td>
        <th>If no answer:</th>
        <td>
            <?php 
                $text = '';
                if (!empty($sonetel_user)) {
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
                }
                echo $text;
            ?>
        </td>
        <td><a id="changeUserSecondActionLink" href="#" style="text-decoration: underline">Change</a></td>
    </tr>
    <tr>
        <th>Call thru:</th>
        <td>PIN: <?php $pin_property_name = 'callthru-pin'; if (!empty($sonetel_user)) { echo $sonetel_user->call->outgoing->$pin_property_name; } else { echo 'N/A';} ?></td>
        <td><a id="changeUserCallThruActionLink" href="#" style="text-decoration: underline">Change</a></td>
        <th>When I call:</th>
        <?php
            $show = '';
            if (!empty($sonetel_user)) {
                $show = $sonetel_user->call->outgoing->show;
            }
        ?>
        <td>Show my number(CLI) = 
        <?php if ($show == 'auto') {
            echo 'Automatic';
        } else if ($show == 'none') {
            echo 'None';
        } else if ($show == 'inum') {
            echo 'iNUM';
        } else {
            echo $show;
        }?></td>
        <td><a id="changeUserShowMyNumberLink" href="#" style="text-decoration: underline">Change</a></td>
    </tr>
</table>
<?php } ?>
<div class="hide">
    <div id="changeLocationAreaWindow" title="Change User Location" class="input-form dialog-form"></div>
    <div id="callToSettingWindow" title="Change Call Setting" class="input-form dialog-form"></div>
    <div id="callOutGoingWindow" title="Change Call Outgoing" class="input-form dialog-form"></div>
    <div id="assignPhoneNumberWindow" title="Add Phone Number" class="input-form dialog-form"></div>
    <div id="assignPhonesWindow" title="Add Phones" class="input-form dialog-form"></div>
</div>
<script>
jQuery(document).ready(function () {
    $('#btnAddPostboxToUser, #addNewPhonesLink, #addNewPhoneNumberLink').button();
    var action = $('#h_action_type').val();
    console.log(action);
    if (action === 'edit') {
        PhoneUsers.searchPhoneNumberProducts();
        PhoneUsers.searchPhonesProducts();
    }
});
</script>
