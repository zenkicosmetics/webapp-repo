<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('product_view_admin_shippingservice_ShippingServices'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="shippingServiceSearchForm" action="<?php echo base_url()?>admin/products/list_shipping_services" method="post">
        <div class="ym-g70 ym-gl">
        	<div class="ym-grid input-item">
				<input type="text" id="searchCustomerForm_enquiry" name="enquiry" style="width: 250px" value="" class="input-txt" maxlength=255 />
				<button id="searchCustomerBlackListButton" class="admin-button"><?php admin_language_e('product_view_admin_shippingservice_SearchBtn'); ?></button>
				<button id="btnAddShippingService" class="admin-button"><?php admin_language_e('product_view_admin_shippingservice_AddBtn'); ?></button>
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
	<div id="addShippingService" title="<?php admin_language_e('product_view_admin_shippingservice_AddShippingService'); ?>" class="input-form dialog-form"></div>
	<div id="editShippingService" title="<?php admin_language_e('product_view_admin_shippingservice_EditShippingService'); ?>" class="input-form dialog-form"></div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/products/js/ShippingServices.js"></script>
<script>
	jQuery(document).ready(function() {
		var baseUrl = '<?php echo base_url() ?>',
			rowNum = '<?php echo APContext::getAdminPagingSetting();?>',
			rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

		ShippingServices.init(baseUrl, rowNum, rowList);
	});
</script>