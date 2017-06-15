<div class="ym-grid">
    <div id="cloud-body-wrapper" style="margin-left: 20px;">
        <h2 style="border: 0px;">Your Location</h2>
        <span>
            Use your own location for your users
        </span>
    </div>
</div>

<div id="gridwraper" style="margin: 20px 0px 0px 20px;">
    <form id="searchUserForm" action="#" method="post">
        <div class="ym-grid">
            <button type="button" id="btnAddNewLocation" class="input-btn btn-yellow" style="margin-left: 5px">Add new location</button>
        </div>
    </form>
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>
<div style="margin: 20px 20px ">
        <p>
            Use the admin panel to manage your locations:
        </p>
        <p>
            URL: <a class="main_link_color" href="<?php echo base_url();?>admin/login"><?php echo APContext::getFullBasePath().'admin/login'; ?></a>
        </p>
        <p>
            Your user name: <?php $customer = APContext::getCustomerByID(APContext::getParentCustomerCodeLoggedIn()); echo $customer->email; ?>
        </p>
        <p>
            Your password: same as your user password
        </p>
        <div class="ym-grid">
            <button type="button" id="btnAccessAdminPanelSite" class="input-btn btn-yellow" style="margin-left: 5px">Go to the admin panel now</button>
        </div>
</div>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide">
    <div id="addLocationWindow" title="Add Location" class="input-form dialog-form">
    </div>
    <div id="editLocationWindow" title="Edit Location" class="input-form dialog-form">
    </div>
</div>
<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/LocationCustomer.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});
        
        LocationCustomer.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>', 'all');
        
        $('#btnAccessAdminPanelSite').click(function() {
            var url = '<?php echo base_url();?>admin/login';
            window.open(url, '_blank');
        });
    });
</script>