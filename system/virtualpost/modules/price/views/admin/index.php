<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('price_view_admin_index_PricingTemplateManagement'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 10px;">
    <button id="addPartnerButton" class="admin-button"><?php admin_language_e('price_view_admin_index_AddBtn'); ?></button>
    <select class="input-width" id="ddlPricingType">
        <option value=""><?php admin_language_e('price_view_admin_index_All'); ?></option>
        <option value="Clevver"><?php admin_language_e('price_view_admin_index_Clevver'); ?></option>
        <option value="Enterprise"><?php admin_language_e('price_view_admin_index_Enterprise'); ?></option>
    </select>
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide">
    <div id="divAddPartner" title="<?php admin_language_e('price_view_admin_index_AddPricingTemplate'); ?>" class="input-form dialog-form"></div>
    <div id="divEditPartner" title="<?php admin_language_e('price_view_admin_index_EditPricingTemplate'); ?>" class="input-form dialog-form"></div>
</div>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/price/js/PricingTemplateList.js"></script>
<script type="text/javascript">
	$(document).ready( function() {
		var baseUrl = '<?php echo base_url(); ?>',
			rowNum = '<?php echo APContext::getAdminPagingSetting(); ?>',
			rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

		var DEFAULT_PRICING_MODEL_INVOICE = '<?php echo APConstants::DEfAULT_PRICING_MODEL_INVOICE; ?>';

		PricingTemplateList.init(baseUrl, rowNum, rowList, DEFAULT_PRICING_MODEL_INVOICE);
	});
</script>