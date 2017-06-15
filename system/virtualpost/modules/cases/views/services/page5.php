<div class="ym-grid content services" id="case-body-wrapper">
    <div id="go-back" style="width: 200px"><span><a id="backButton" href="#" ><?php language_e('cases_view_services_page5_CheckYourCase'); ?></a></span></div>
	<div class="ym-clearfix"></div>

	<div class="header center">
		<h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_services_page5_ThankYouForYourOrder'); ?>…</h2>
		<h4><?php language_e('cases_view_services_page5_ANewCaseWasOpened'); ?></h4>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-clearfix"></div>
	<div class="ym-gl">
		<h3><?php language_e('cases_view_services_page5_NextStepsForOpeningTheClevver'); ?>:</h3>
		<br />
		<div class="description">
			<table>
				<tr>
					<td>&nbsp;</td>
					<td><?php language_e('cases_view_services_page5_Order'); ?></td>
					<td><?php language_e('cases_view_services_page5_Completed'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php language_e('cases_view_services_page5_Payment'); ?></td>
					<td><?php language_e('cases_view_services_page5_Completed'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php language_e('cases_view_services_page5_Questions'); ?></td>
					<td><?php language_e('cases_view_services_page5_InTheNextStepYouWillBeAskedAll'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php language_e('cases_view_services_page5_Documents'); ?></td>
					<td><?php language_e('cases_view_services_page5_AllNecessaryDocumentsWillBeGen'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php language_e('cases_view_services_page5_Registration'); ?></td>
					<td><?php language_e('cases_view_services_page5_AfterTheMoneyArrivedInTheAccou'); ?></td>
				</tr>
			</table>

		</div>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid">
		<div class="ym-gl ym-g70">
			<!-- <div class="ym-gr" style="padding-right: 20px"><a href="<?php echo base_url()?>cases">Check Your Case status </a></div> -->
		</div>
		<div class="ym-gr ym-g30">
			<a class="input-btn" href="<?php echo base_url()?>cases"><?php language_e('cases_view_services_page5_ProcessToNextStep'); ?>...</a>
		</div>
	</div>
</div>

<script type="text/javascript">
$("#backButton").click(function(){
	history.back(-1);
	return  false;
});
</script>