<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('setting_view_location_index_SettingsLocations'); ?></h2>
</div>
<div id="searchTableResult" style="margin: 0px;">
    <button id="addLocationButton" class="admin-button">Add</button>
    <div class="clear-height"></div>
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="addLocation" title="<?php admin_language_e('setting_view_location_index_AddLocationAddress'); ?>" class="input-form dialog-form">
	</div>
	<div id="editLocation" title="<?php admin_language_e('setting_view_location_index_EditLocationAddress'); ?>" class="input-form dialog-form">
	</div>
	<!--<div id="window_location_office" title="Feature Office" class="input-form dialog-form">
	</div>-->
</div>

<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/settings/js/LocationList.js"></script>
<script type="text/javascript">
	$(document).ready( function() {
		var baseUrl = '<?php echo base_url(); ?>',
			rowNum = '<?php echo APContext::getAdminPagingSetting(); ?>',
			rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

		LocationList.init(baseUrl, rowNum, rowList);
	});
</script>