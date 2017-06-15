<div class="ym-grid content services"  id="case-body-wrapper">
	<div id="go-back"><span><a id="backButton" href="#" ><?php language_e('cases_view_services_page3_Back'); ?></a></span></div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="header">
		<h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_services_page3_DeutscheBankAGClevverBusinessA'); ?></h2>
	</div>
	<div class="ym-clearfix"></div>
	<div class="ym-grid">
		<div class="ym-gl ym-g80">
			<div class="description"><?php language_e('cases_view_services_page3_ProcessAndAdditionalOptions'); ?></div>
		</div>
	</div>
	<div class="ym-clearfix"></div>

	<br />
	<div class="ym-grid">
		<div class="ym-gl ym-g80">&nbsp;</div>
		<div class="ym-gr ym-g20">
			<a class="input-btn"
				href="<?php echo base_url()?>cases/services/start?p=4"><?php language_e('cases_view_services_page3_Next'); ?></a>
		</div>
	</div>
</div>

<script type="text/javascript">
$("#backButton").click(function(){
	history.back(-1);
	return  false;
});
</script>