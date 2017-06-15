<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('product_view_admin_shippingcarrier_ShippingCarriers'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="shippingCarrierSearchForm" action="<?php echo base_url()?>admin/products/shipping_carriers" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <button id="btnAddShippingCarrier" class="admin-button"><?php admin_language_e('product_view_admin_shippingcarrier_AddBtn'); ?></button>
            </div>
        </div>
    </form>
</div>
<div class="button_container">
    <div class="button-func"></div>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
    	<table id="dataGridResult"></table>
    	<div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="addShippingCarrier" title="<?php admin_language_e('product_view_admin_shippingcarrier_AddShippingCarrier'); ?>" class="input-form dialog-form"></div>
    <div id="editShippingCarrier" title="<?php admin_language_e('product_view_admin_shippingcarrier_EditShippingCarrier'); ?>" class="input-form dialog-form"></div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/products/js/ShippingCarriers.js"></script>
<script>
jQuery(document).ready(function() {
    var baseUrl = '<?php echo base_url() ?>',
            rowNum = '<?php echo APContext::getAdminPagingSetting();?>',
            rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

    ShippingCarriers.init(baseUrl, rowNum, rowList);
});
</script>