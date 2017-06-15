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

<div style="margin-left: 10px; margin-top: 5px; font-weight: bold;">Handling Rules</div>
<div id="gridwraperHandlingRule" style="margin-left: 10px; margin-top: 5px;">
    <div id="searchTableHandlingRuleResult">
        <table id="dataGridHandlingRuleResult"></table>
        <div id="dataGridHandlingRulePager"></div>
    </div>
</div>
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
        PhoneUsers.searchHandlingRules();
    }
});
</script>
