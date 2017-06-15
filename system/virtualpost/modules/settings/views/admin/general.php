<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('settings_view_admin_general_BreadScrum'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"
	action="<?php echo base_url()?>admin/settings/index">
	<div class="input-form">
		<table class="settings">
			<tr>
				<th class="input-width-200"><?php admin_language_e('settings_view_admin_general_SiteName'); ?></th>
				<td>
					<input type="text" id="SITE_NAME_CODE" name="SITE_NAME_CODE" value="<?php echo Settings::get(APConstants::SITE_NAME_CODE)?>" class="input-width input-width-400" /><br />
					<small><?php admin_language_e('settings_view_admin_general_SiteNameDescription'); ?></small>
				</td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('settings_view_admin_general_LogoOnMainColor'); ?></th>
				<td>
    				<?php $site_logo_image_path = Settings::get(APConstants::SITE_LOGO_CODE);?>
    				<img id="SITE_LOGO_CODE_IMG" alt="" src="<?php if (!empty($site_logo_image_path)) {echo APContext::getAssetPath().$site_logo_image_path;}?>" /><br />
					<input type="text" id="SITE_LOGO_CODE" name="SITE_LOGO_CODE" value="<?php echo Settings::get(APConstants::SITE_LOGO_CODE)?>" class="input-width input-width-400 readonly" readonly="readonly" />
					<button type="button" class="tooltip admin-button" id="selectMainLogoButton"><?php admin_language_e('settings_view_admin_general_ChangeBtn'); ?></button>
					<input type="file" id="imagepath03" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
					<small><?php admin_language_e('settings_view_admin_general_LogoOnMainColorDescription'); ?></small>
				</td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('settings_view_admin_general_LogoOnWhite'); ?></th>
				<td>
					<?php $site_logo_white_image_path = Settings::get(APConstants::SITE_LOGO_WHITE_CODE);?>
					<img id="SITE_LOGO_WHITE_CODE_IMG" alt="" src="<?php if (!empty($site_logo_white_image_path)) { echo APContext::getAssetPath().$site_logo_white_image_path; } ?>" /><br />
					<input type="text" id="SITE_LOGO_WHITE_CODE" name="SITE_LOGO_WHITE_CODE" value="<?php echo Settings::get(APConstants::SITE_LOGO_WHITE_CODE)?>" class="input-width input-width-400 readonly" readonly="readonly" />
					<button type="button" class="tooltip admin-button" id="selectWhiteLogoButton"><?php admin_language_e('settings_view_admin_general_ChangeBtn'); ?></button>
					<input type="file" id="imagepath04" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
					<small><?php admin_language_e('settings_view_admin_general_LogoOnWhiteDescription'); ?></small>
				</td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('settings_view_admin_general_FirstLetterImage'); ?></th>
				<td>
					<input type="text" id="FIRST_ENVELOPE_KEY"	name="FIRST_ENVELOPE_KEY" value="<?php echo Settings::get(APConstants::FIRST_ENVELOPE_KEY)?>" class="input-width input-width-400 readonly" readonly="readonly" />
					<button type="button" class="tooltip admin-button" id="selectEnvelopeFileButton"><?php admin_language_e('settings_view_admin_general_BrowserBtn'); ?></button>
					<button type="button" class="tooltip admin-button" id="previewEnvelopeFileButton"><?php admin_language_e('settings_view_admin_general_PreviewBtn'); ?></button>
					<input type="file" id="imagepath02" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
					<small><?php admin_language_e('settings_view_admin_general_FirstLetterImageDescription'); ?></small>
				</td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('settings_view_admin_general_FirstLetterItem'); ?></th>
				<td>
					<input type="text" id="FIRST_LETTER_KEY" name="FIRST_LETTER_KEY" value="<?php echo Settings::get(APConstants::FIRST_LETTER_KEY)?>" class="input-width input-width-400 readonly" readonly="readonly" />
					<button type="button" class="tooltip admin-button" id="selectFileButton"><?php admin_language_e('settings_view_admin_general_BrowserBtn'); ?></button>
					<button type="button" class="tooltip admin-button" id="previewFileButton"><?php admin_language_e('settings_view_admin_general_PreviewBtn'); ?></button>
					<input type="file" id="imagepath" name="imagepath" class=""	style="visibility: hidden; display: none;" /> <br />
					<small><?php admin_language_e('settings_view_admin_general_FirstLetterItemDescription'); ?></small>
				</td>
			</tr>
			<tr>
				<th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
				<td>
					<button id="saveButton" class="admin-button"><?php admin_language_e('settings_view_admin_general_SaveBtn'); ?></button>
				</td>
			</tr>
		</table>
	</div>
</form>
<div class="clear-height"></div>
<div class="hide" style="display: none;">
    <a id="preview_first_letter" class="iframe" href="<?php echo APContext::getAssetPath().Settings::get(APConstants::FIRST_LETTER_KEY)?>"><?php admin_language_e('settings_view_admin_general_PreviewFirstLetter'); ?></a>
    <a id="preview_first_letter_envelope" class="iframe" href="<?php echo APContext::getAssetPath().Settings::get(APConstants::FIRST_ENVELOPE_KEY)?>"><?php admin_language_e('settings_view_admin_general_PreviewFirstLetterEnvelope'); ?></a>
</div>
<script type="text/javascript">
$(document).ready( function() {
	var assetPath = '<?php echo APContext::getAssetPath()?>';
	$('.admin-button').button();
	$("#selectFileButton").button().click(function() {
		$('#imagepath').val('');
        $('#imagepath').click();
        return false;
    });
	$("#selectEnvelopeFileButton").button().click(function() {
		$('#imagepath02').val('');
        $('#imagepath02').click();
        return false;
    });

	$("#selectMainLogoButton").button().click(function() {
		$('#imagepath03').val('');
        $('#imagepath03').click();
        return false;
    });

	$("#selectWhiteLogoButton").button().click(function() {
		$('#imagepath04').val('');
        $('#imagepath04').click();
        return false;
    });

	$('#preview_first_letter').fancybox({
		width: 1000,
		height: 800
	});

	$('#preview_first_letter_envelope').fancybox({
		width: 1000,
		height: 800
	});

	$('#displayTermsServiceLink').fancybox({
		width: 1000,
		height: 800
	});

	$('#displayPrivacyLink').fancybox({
		width: 1000,
		height: 800
	});

	$("#imagepath").change(function (){
		// Upload data here
		$.ajaxFileUpload({
			id: 'imagepath',
			data: {},
			url: '<?php echo base_url()?>admin/settings/upload_first_letter',
			success: function(data) {
			    $('#FIRST_LETTER_KEY').val(data.message);
			}
		});
    });

	$("#imagepath02").change(function (){
		// Upload data here
		$.ajaxFileUpload({
			id: 'imagepath02',
			data: {},
			url: '<?php echo base_url()?>admin/settings/upload_first_letter',
			success: function(data) {
			    $('#FIRST_ENVELOPE_KEY').val(data.message);
			}
		});
    });

	$("#imagepath03").change(function (){
		// Upload data here
		$.ajaxFileUpload({
			id: 'imagepath03',
			data: {},
			url: '<?php echo base_url()?>admin/settings/upload_main_logo',
			success: function(data) {
				$('#SITE_LOGO_CODE_IMG').attr('src', assetPath + data.message);
			    $('#SITE_LOGO_CODE').val(data.message);
			}
		});
    });

	$("#imagepath04").change(function (){
		// Upload data here
		$.ajaxFileUpload({
			id: 'imagepath04',
			data: {},
			url: '<?php echo base_url()?>admin/settings/upload_white_logo',
			success: function(data) {
				$('#SITE_LOGO_WHITE_CODE_IMG').attr('src', assetPath + data.message);
			    $('#SITE_LOGO_WHITE_CODE').val(data.message);
			}
		});
    });

	$("#previewFileButton").button().click(function() {
		var first_letter_url = $('#FIRST_LETTER_KEY').val();
		if (first_letter_url != '') {
			$('#preview_first_letter').click();
		}
        return false;
    });

	$("#previewEnvelopeFileButton").button().click(function() {
		var first_letter_url = $('#FIRST_ENVELOPE_KEY').val();
		if (first_letter_url != '') {
			$('#preview_first_letter_envelope').click();
		}
        return false;
    });
});
</script>