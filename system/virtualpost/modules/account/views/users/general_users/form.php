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
    $submit_url = base_url() . 'account/users/add_general_users';
} else {
    $submit_url = base_url() . 'account/users/edit_general_users?customer_id='.$user->customer_id;
}
?>

<input type="hidden" id="product_type" name="product_type" value="<?php echo $product_type?>" />

<!-- Include -->
<?php include ("system/virtualpost/modules/account/views/users/general_users/form_partial.php"); ?>

<div id="userPostboxSettingContainer" style="display: none">
    <?php if ($product_type == 'all' && $action_type == 'edit') {?>
        <span style="margin-left: 10px; font-weight: bold;">Products</span>
    <?php } else if ($action_type == 'add') {?>
       <span style="margin-left: 10px; font-weight: bold;">
           <button type="button" id="btnAddNewPostboxToUser" class="input-btn btn-yellow">Add Postbox</button>
       </span>
    <?php } ?>
    <div id="gridwraper" style="margin-left: 10px; margin-top: 5px;">
        <div id="searchTableProductResult">
            <table id="dataGridProductResult"></table>
            <div id="dataGridProductPager"></div>
        </div>
    </div>
</div>
<?php if ($action_type == 'edit') {?>
<div style="margin-top: 5px;">
    <span style="margin-left: 10px; font-weight: bold;">
        <button type="button" id="btnAddNewPostboxToUser" class="input-btn btn-yellow">Add Postbox</button>
    </span>
    <span style="margin-left: 10px; font-weight: bold;">
        <button type="button" id="addNewPhoneNumberLink" class="input-btn btn-yellow">Assign Phone Number</button>
    </span>
</div>
<?php } ?>
<?php if ($action_type == 'add') {?>
<div id="userPhoneSettingContainer" style="display: none; margin-top: 5px;">
    
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
    $('#addNewPhoneNumberLink, #btnAddNewPostboxToUser, #addNewPhonesLink').button();
    var action = $('#h_action_type').val();
    changeProductType();
    
    // Change product type
    function changeProductType() {
        // Add new
        if(action == 'add'){
            var product_type = $('#addEditUserForm_product_type').val();
            if (product_type == 'all') {
                $('#userPostboxSettingContainer').hide();
                $('#userPhoneSettingContainer').hide();
                GeneralUsers.searchProducts();
            } else if (product_type == 'postbox') {
                $('#userPostboxSettingContainer').show();
                $('#userPhoneSettingContainer').hide();
                PostboxUsers.searchPostboxProducts();
            } else if (product_type == 'phone') {
                $('#userPostboxSettingContainer').hide();
                $('#userPhoneSettingContainer').show();
                PhoneUsers.searchPhoneNumberProducts();
                PhoneUsers.searchPhonesProducts();
                PhoneUsers.searchHandlingRules();
            }
        }else{
            var product_type = $('#product_type').val();
            if (product_type == 'all') {
                $('#userPostboxSettingContainer').show();
                $('#userPhoneSettingContainer').hide();
                GeneralUsers.searchProducts();
            } else if (product_type == 'postbox') {
                $('#userPostboxSettingContainer').show();
                $('#userPhoneSettingContainer').hide();
                PostboxUsers.searchPostboxProducts();
            } else if (product_type == 'phone') {
                $('#userPostboxSettingContainer').hide();
                $('#userPhoneSettingContainer').show();
                PhoneUsers.searchPhoneNumberProducts();
                PhoneUsers.searchPhonesProducts();
                PhoneUsers.searchHandlingRules();
            }
        }
        
    }
    
    // Change event
    $('#addEditUserForm_product_type').live('change', function() {
        changeProductType();
    });
});
</script>
