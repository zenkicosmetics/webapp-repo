<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 99%;margin-left: 20px;">
        <h2 style="border: 0px;">Users  Management</h2>
        <span>
            You can add user to your account with own login but no access to invoicing and payment information 
        </span>
    </div>
</div>


<div id="gridwraper" style="margin: 20px 0px 0px 20px;">
    <form id="searchUserForm" action="#" method="post">
        <div class="ym-grid">
            <label style="text-align: left;">Search Text:</label>
            <input id="searchUserForm_enquiry" name="enquiry" style="width: 248px" value="" class="input-txt" maxlength="255" type="text">
            
            <button type="button" id="btnSearchUser" class="input-btn btn-yellow" style="margin-left: 5px">Search</button>
            <button type="button" id="btnAddNewUser" class="input-btn btn-yellow" style="margin-left: 5px">Add new user</button>
            <input id="hideDeletedUser" name="hideDeletedUser" value="1" checked="checked" type="checkbox"  style="margin-left: 15px" />
            <span style="font-size: 15px; margin-left: 3px;">Hide Deleted User</span>
        
            <input type="hidden" id="searchUserForm_current_product_type" value="all" />
        </div>
    </form>
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide" style="display: none">
    <div id="addUser" title="Add User" class="input-form dialog-form"> </div>
    <div id="editUser" title="Edit User" class="input-form dialog-form"> </div>
    <div id="changePasswordUser" title="Change Password" class="input-form dialog-form"></div>
    <div id="confirmDeleteUserWindow" title="Confirm delete user" class="input-form dialog-form"> </div>
</div>
<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/GeneralUsers.js?t=<?php echo time() ?>"></script>
<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/PhoneUsers.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/PostboxUsers.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});
        
        GeneralUsers.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>', 'all');
        PhoneUsers.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>');
        PostboxUsers.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>');
    });

</script>