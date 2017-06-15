<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('scan_views_todo_index_TodoList'); ?></h2>
</div>
<div class="ym-grid mailbox" style="width: 1200px;">
	<form id="locationForm" action="<?php echo base_url()?>scans/todo" method="post">
		<div class="ym-g33 ym-gl">
			<div class="ym-grid input-item">
				<div class="ym-g20 ym-gl"><label><?php admin_language_e('scan_views_todo_index_Location'); ?></label></div>
				<div class="ym-g40 ym-gl">
					<?php
					// check access for supper admin and instance admin.
					if(APContext::isAdminParner() || APContext::isAdminUser()){
						echo my_form_dropdown(array(
							"data" => $list_access_location,
							"value_key" => 'id',
							"label_key" => 'location_name',
							"value" => $location_id,
							"name" => 'location_id',
							"id"	=> 'location_id',
							"clazz" => 'input-width',
							"style" => 'width:220px',
							"has_empty" => true
						));
					}else{
						echo my_form_dropdown(array(
							"data" => $list_access_location,
							"value_key" => 'id',
							"label_key" => 'location_name',
							"value" => $location_id,
							"name" => 'location_id',
							"id"	=> 'location_id',
							"clazz" => 'input-width readonly',
							"style" => 'width:220px;',
							"has_empty" => false,
							"html_option" => '',
						));
					}
					?>
				</div>
			</div>
		</div>
	</form>
	<div class="ym-clearfix"></div>

	<form id="addIncommingEnvelopeForm" action="<?php echo base_url()?>scans/todo" method="post">
		<div class="ym-g33 ym-gl">
			<div class="ym-grid input-item">
				<div class="ym-g20 ym-gl">
					<label><?php admin_language_e('scan_views_todo_index_From'); ?></label>
				</div>
				<div class="ym-g80 ym-gl">
					<input type="text" id="from_ID" name="from_customer_name"
						   value="<?php echo $envelope->from?>" class="input-txt readonly" maxlength=255 readonly="readonly" disabled="disabled"/>
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-g20 ym-gl">
					<label><?php admin_language_e('scan_views_todo_index_To'); ?></label>
				</div>
				<div class="ym-g80 ym-gl">
					<input type="text" id="to_name_ID" name="to_customer_name"
						   value="<?php echo $envelope->to_customer_name?>" class="input-txt readonly" maxlength=255 readonly="readonly" disabled="disabled"/>
					<input type="hidden" id="to_ID" name="to_customer_id"
						   value="<?php echo $envelope->to_customer_id?>" class="readonly" maxlength=255 readonly="readonly" />
					<input type="hidden" id="envelope_ID" name="envelope"
						   value="" class="readonly" maxlength=255 readonly="readonly" />

				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-g20 ym-gl">
					<label><?php admin_language_e('scan_views_todo_index_Type'); ?></label>
				</div>
				<div class="ym-g80 ym-gl">
					<input type="text" id="type_ID" name="type"
						   value="<?php echo $envelope->type?>" class="input-txt readonly" maxlength=255 readonly="readonly" disabled="disabled"/>
					<input type="hidden" id="type_id_ID" name="type_id"
						   value="<?php echo $envelope->type_id?>" class="readonly" maxlength=255 readonly="readonly" />
				</div>
			</div>
			<div class="ym-clearfix"></div>
			<div class="ym-grid input-item">
				<div class="ym-g20 ym-gl">
					<label><?php admin_language_e('scan_views_todo_index_Weight'); ?></label>
				</div>
				<div class="ym-g40 ym-gl">
					<input type="text" id="Weight_ID" name="weight" value="<?php echo $envelope->weight?>"
						   class="input-txt readonly" maxlength=50  disabled="disabled"/>
				</div>
				<?php if (APContext::isAdminUser()) {?>
<!--					<div class="ym-gl only_for_scan_item" style="width: 125px;">
						<label>Invoice</label>
					</div>
					<div class="ym-g10 ym-gl only_for_scan_item" style="width: 20px;">
						<input type="checkbox" id="invoice_flag_ID" name="invoice_flag" value="1"
							   class="input-txt customCheckbox" style="width: 20px;" />
					</div>-->
				<?php } ?>
			</div>
			<div class="ym-clearfix"></div>
			<?php if (APContext::isAdminUser()) {?>
				<div class="ym-grid input-item only_for_scan_item">
					<div class="ym-g20 ym-gl">
						<label><?php admin_language_e('scan_views_todo_index_Category'); ?></label>
					</div>
					<div class="ym-g80 ym-gl">
						<?php echo code_master_form_dropdown(array(
							"code" => APConstants::CATEGORY_TYPE_CODE,
							"value" => $envelope->type,
							"name" => 'category_type',
							"id"	=> 'category_type',
							"clazz" => 'input-text',
							"style" => '',
							"has_empty" => true
						));?>
					</div>
				</div>
			<?php }?>
			<div class="ym-clearfix"></div>
		</div>
		<div id="previewEnvelopeScanContainer" class="ym-g33 ym-gl hide" style="width: 420px">
			<div id="previewEnvelopeScan" style="margin-left: 20px; margin-top: -35px">

			</div>
		</div>
		
		<div id="previewShippingItemContainer" class="ym-g33 ym-gl hide" style="width: 500px; margin-left: 30px; margin-top: -35px">
			<table id="shippingItemDataGridResult"></table>
			<div id="shippingItemDataGridPager"></div>
		</div>

		<div class="ym-g20 ym-gl hide" id="scanButtonContainer" style="margin-top: -35px;margin-left: 10px;">
			
			<!-- --------------------------------------------- -->
			<div class="wrap_tracking_number" style="margin-bottom: 5px;margin-top: 0px; display: none;">
				<div style="width: 360px;height: 108px;">
					<table class="tbl_tracking_number" style="margin-top: 0px;" cellpadding="0" cellspacing="0" width="100%">
						<tr class="item_was_forward">
							<th style="padding-left: 0px;" colspan="2">
								<h2 id="detail_item_was_forward" style="font-weight: bold; font-size: 14px;"></h2>
							</th>
						</tr>
						<tr>
							<td style="width: 115px;"><span class="tracking"><?php admin_language_e('scan_views_todo_index_TrackingNumber'); ?><span class="required">*</span> </span></td>
							<td class="todo_tracking_number">
                                <input disabled="disabled" type="text" class="input-txt tracking_disable" name="tracking_number" id="tracking_number" class="tracking_number" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td><span class="tracking"><?php admin_language_e('scan_views_todo_index_ShippingService'); ?><span class="required">*</span> </span></td>
							<td class="todo_list_shipping_service_available" id="list_shipping_service_available">
								<select style="width:240px;" class="input-width tracking_disable" id="shipping_services" name="shipping_services">
								<option value="0"><?php admin_language_e('scan_views_todo_index_SelectShippingService'); ?></option>
								</select>
							</td>
						</tr>
						<tr id="tr_no_tracking_number">
							<td><span class=""><?php admin_language_e('scan_views_todo_index_NoTrackingNumber'); ?></span></td>
							<td><input disabled="disabled" type="checkbox" value="0" name="no_tracking_number" id="no_tracking_number" class="no_tracking_number tracking_disable" /></td>
						</tr>

						<tr class="no_save_tracking">
							<td></td>
							<td><input type="button" value="<?php admin_language_e('scan_views_todo_index_SaveTrackingNumber'); ?>" name="btn_save_tracking_number" 
                                       id="btn_save_tracking_number" class="tracking_disable" /></td>
						</tr>

						<tr class="no_save_tracking">
							<td></td>
							<td><input type="button" value="<?php admin_language_e('scan_views_todo_index_NoTrackingNumberAvailable'); ?>" 
                                       name="btn_no_save_tracking_number" id="btn_no_save_tracking_number" class="tracking_disable" /></td>
						</tr>

					</table>

				</div>

				
			</div>
			<!-- --------------------------------------------- -->

			<div class="ym-g100 ym-gl" id="scanButtonContainerSub" >
				<input type="hidden" id="shipping_services_hidden" name="shipping_services_hidden" value="" />
                <input type="button" id="scanEnvelopeButton" class="input-btn c" value="<?php admin_language_e('scan_views_todo_index_ScanEnvelope'); ?>" style="margin-left: 20px;"/>
				<input type="button" id="scanItemButton" class="input-btn c" value="<?php admin_language_e('scan_views_todo_index_ScanItem'); ?>" style="margin-left: 20px;"/>
				<input type="button" id="shippingEnvelopeButton" class="input-btn c" value="<?php admin_language_e('scan_views_todo_index_PrepareShipping'); ?>" style="margin-left: 0px;"/>
				<input type="button" id="markCompletedButton" disabled="disabled" class="input-btn-disable c" value="<?php admin_language_e('scan_views_todo_index_MarkCompleted'); ?>" style="margin-left: 20px;" />
			</div>
		</div> <!-- end div#scanButtonContainer -->
		
	</form>
	<a id="dynaScanLink" href="<?php echo base_url()?>scans/todo/scan" title="<?php admin_language_e('scan_views_todo_index_ScanEnvelope'); ?>" style="display: none">
        <?php admin_language_e('scan_views_todo_index_ScanLink'); ?></a>
</div>

<div id="searchTableResult" style="margin: 10px;">
	<table id="dataGridResult"></table>
	<div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
	<div id="scanEnvelopeWindow" title="<?php admin_language_e('scan_views_todo_index_ScanEnvelope'); ?>" class="input-form dialog-form">
	</div>

	<div id="shippingEnvelopeWindow" title="<?php admin_language_e('scan_views_todo_index_AddressLabelPrintInterface'); ?>" class="input-form dialog-form">
	</div>
	<input type="hidden" id="scan_type_id" value="1" />
	<input type="hidden" id="current_scan_type" value="" />
	<input type="hidden" id="has_scan_image_id" value="" />
	<input type="hidden" id="nextToDoForm_current_row_id" value="" />
	<input type="hidden" id="nextToDoForm_postbox_id" value="" />
	<input type="hidden" id="nextToDoForm_package_id" value="" />
	<input type="hidden" id="scanItemTemporaryFlag_id" value="" />
	<input type="hidden" id="documentType" value="" /><!--1: upload file; 2: scan file -->

	<a id="display_document_full" class="iframe" href="#"><?php admin_language_e('scan_views_todo_index_GotoDocumentView'); ?></a>
	<a id="display_envelope_full" class="iframe" href="#"><?php admin_language_e('scan_views_todo_index_GotoEnvelopeView'); ?></a>

	<form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url()?>admin/customers/view_site" method="post">
		<input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
	</form>
</div>
<!-- Content for dialog -->
<div class="hide">
	<div id="viewDetailCustomer" class="input-form dialog-form"></div>
	<div id="createDirectCharge" class="input-form dialog-form"></div>
	<div id="recordExternalPayment" class="input-form dialog-form"></div>
	<div id="recordRefundPayment" class="input-form dialog-form"></div>
	<div id="createDirectChargeWithoutInvoice" class="input-form dialog-form"></div>
	<div id="createDirectInvoice" class="input-form dialog-form"></div>
	<div id="envelopeCommentWindow" title="<?php admin_language_e('scan_views_todo_index_Comment'); ?>" class="input-form dialog-form"></div>
	<div id="preShippingWindow" title="<?php admin_language_e('scan_views_todo_index_ConfirmShipping'); ?>" class="input-form dialog-form"></div>
</div>
<div class="hide" id="printerLabelArea"></div>

<div class="hide" style="display: none;">
    <div id="dialogShippingProcess" title="<?php admin_language_e('scan_views_todo_index_Shipping'); ?>">
        <div id="progressbarLabel" class="progress-label"><?php admin_language_e('scan_views_todo_index_CurrentProgress0'); ?></div>
        <div id="progressbarShippingProcess"></div>
    </div>
</div>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/scans/js/TodoList.js"></script>
<script>
	$(document).ready( function() {
		var baseUrl = '<?php echo base_url(); ?>',
			rowNum = '<?php echo APContext::getAdminPagingSetting();?>',
			rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];

		/** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
		<?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
		/** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */

		TodoList.init(baseUrl, rowNum, rowList);
	});
</script>