<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_overview_Header'); ?></h2>
</div>
<form action="<?php echo base_url()?>admin/report/overview" method="post">
<div class="ym-grid mailbox">
	<div class="ym-g80 ym-gl">
		<div class="ym-grid input-item">
			<div class="ym-g50 ym-gl" style="width: 250px">
				<select id="ddlReportType" class="input-text" name="location_id">
					<option value=""><?php admin_language_e('report_views_admin_overview_OptValue'); ?></option>
                    <?php foreach ($locations as $location) {?>
                    <option value="<?php echo $location->id?>" <?php if ($location_id == $location->id) {?> selected="selected" <?php }?>><?php echo $location->location_name?></option>
                    <?php }?>
                </select>
			</div>
			<button><?php admin_language_e('report_views_admin_overview_BtnSearch'); ?></button>
		</div>
	</div>
</div>
</form>
<div class="ym-clearfix"></div>
<div style="padding: 0px; margin-top: 10px;"><?php admin_language_e('report_views_admin_overview_LblMonth'); ?><?php echo APUtils::getCurrentMonthInvoice();?>/<?php echo APUtils::getCurrentYear();?></div>

<div style="padding: 0px; margin-top: 10px;"><?php admin_language_e('report_views_admin_overview_LblStatusMonthEnd'); ?></div>
<hr style="width: 100%; padding: 0px; margin-top: 10px;" />
<div class="ym-clearfix"></div>

<!-- Detail report -->

<div style="border-radius: 10px; padding: 0px;">
	<table class="report">
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdTotalAcc'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->total_account?>" ></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgOpenTime'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_activity_open_time?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgRevPostboxAsyougo'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_revenue_postbox_free?>"></td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdInactivate'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->total_inactive_account?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgRevAcc'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_revenue_account?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgRevPostboxPrivate'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_revenue_postbox_private?>"></td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdPostbox'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->total_postboxes?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgLocationAcc'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_location_account?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdAvgRevPostboxBuss'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_revenue_postbox_business?>"></td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdPercentBussPostbox'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->percent_business_postboxes?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdTicketsCreated'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->tickets_created?>"></td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdTicketsCreated'); ?></td>
			<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $overview->avg_revenue_postbox?>"></td>
		</tr>
	</table>
</div>
<div class="ym-clearfix"></div>
<hr style="width: 100%; padding: 0px; margin-top: 10px;" />
<div style="border-radius: 10px; padding: 0px;">
	<table class="report">
		<tr>
            <?php foreach ($list_monthly_report as $monthly_report) {?>        
            <td>
				<table>
					<tr>
						<td colspan="2" style="font-weight: bold;"><?php echo $monthly_report->month;?></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdTotalInv'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->total_invoices?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdTotalCreditNote'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->total_credit_notes?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdTotalRev'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->total_revenue?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdNewAcc'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->total_new_accounts?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdDeletedAcc'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->total_deleted_accounts?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdChurn'); ?>:</td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->percent_account_churn?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdNewNetAdds'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->new_account_net_adds?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdNewPostbox'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->new_postboxed?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdDeletedPostbox'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->deleted_postboxes?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdChurn'); ?>:</td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->percent_postbox_churn?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdNewNetAdds'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->new_postbox_net_adds?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdItemsReceived'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->num_of_items_received?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdEnvScan'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->num_of_envelope_scans?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdItemScan'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->num_of_item_scans?>"></td>
					</tr>
					<tr>
						<td><?php admin_language_e('report_views_admin_overview_TblHdItemShippment'); ?></td>
						<td><input type="text" class="input-txt" style="width: 75px;" value="<?php echo $monthly_report->num_of_items_shippments?>"></td>
					</tr>
				</table>
			</td>
            <?php }?>
        </tr>
	</table>
</div>
<hr style="width: 100%; padding: 0px; margin-top: 10px;" />
<div style="border-radius: 10px; padding: 0px;">
	<table style="width: 1000px" class="report">
	    <tr>
			<td style="width: 350px">&nbsp;</td>
			<td style="width: 75px; text-align: center;">
			    #
			</td>
			<td style="width: 75px; text-align: center;">
			    %
			</td>
			<td style="width: 350px">&nbsp;</td>
			<td style="width: 75px; text-align: center;">
			    #
			</td>
			<td style="width: 75px; text-align: center;">
			    %
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdCurrentBussAcc'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdUpgradesToPrivate'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdNumPrivateAcc'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdUpgradesToPrivate'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdNumAsyougoAcc'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdUpgradesToBuss'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdNumBussPostbox'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdDowngradesToPrivate'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdNumPrivatePostbox'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdDowngradesToAsyougo'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
		<tr>
			<td><?php admin_language_e('report_views_admin_overview_TblHdNumAsyougoPostbox'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td><?php admin_language_e('report_views_admin_overview_TblHdDowngradesPrivateAssyougo'); ?></td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
			<td>
			    <input type="text" class="input-txt" style="width: 75px;">
			</td>
		</tr>
	</table>
</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
});
</script>