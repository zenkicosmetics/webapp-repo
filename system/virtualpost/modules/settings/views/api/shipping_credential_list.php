<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_shippingcredentiallist_ShippingCredentials'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="shippingCredentialSearchForm" action="#" method="post">
        <div class="ym-gl">
            <div class="ym-g30 ym-gl">
				<input type="text" id="shippingCredentialSearchForm_search_text" name="search_text" style="width: 250px" value="" class="input-txt" />
             </div>
            <button style="margin-left: 20px" id="searchShippingButton" class="admin-button"><?php admin_language_e('setting_view_api_shippingcredentiallist_SearchBtn'); ?></button>
            <button style="margin-left: 20px" id="addShippingButton" class="admin-button"><?php admin_language_e('setting_view_api_shippingcredentiallist_AddBtn'); ?></button>
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
	<div id="addShippingCredential" title="<?php admin_language_e('setting_view_api_shippingcredentiallist_AddShippingCredential'); ?>" class="input-form dialog-form"></div>
	<div id="editShippingCredential" title="<?php admin_language_e('setting_view_api_shippingcredentiallist_EditShippingCredential'); ?>" class="input-form dialog-form"></div>
</div>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/settings/js/ShippingCredentialList.js"></script>
<script type="text/javascript">
	$(document).ready( function() {
		var baseUrl = '<?php echo base_url(); ?>',
			rowNum = '<?php echo APContext::getAdminPagingSetting(); ?>',
            rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

		ShippingCredentialList.init(baseUrl, rowNum, rowList);
	});
</script>