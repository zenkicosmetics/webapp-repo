<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px">General Settings</h2>
</div>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Instance</a></li>
        <li><a href="#tabs-2">Instance Owner</a></li>
        <li><a href="#tabs-3">Design</a></li>
        <li><a href="#tabs-4">Terms of Service</a></li>
        <li><a href="#tabs-5">E-Filiale</a></li>
        <li><a href="#tabs-6">Payone Account</a></li>
    </ul>
    <div id="tabs-1" class="button_container">
    	<form id="usesrSearchForm" method="post"
    		action="<?php echo base_url()?>admin/settings/index">
    		<div class="input-form">
    			<table class="settings">
    				<tr>
    					<th class="input-width-200">Site Name</th>
    					<td><input type="text" id="SITE_NAME_CODE" name="SITE_NAME_CODE"
    						value="<?php echo Settings::get(APConstants::SITE_NAME_CODE)?>"
    						class="input-width input-width-400" /><br /> <small>The name of
    							the website for page titles and for use around the site.</small></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Logo on main color</th>
    					<td>
    					    <?php $site_logo_image_path = Settings::get(APConstants::SITE_LOGO_CODE);?>
    					    <img id="SITE_LOGO_CODE_IMG" alt="" src="<?php if (!empty($site_logo_image_path)) {echo APContext::getAssetPath().$site_logo_image_path;}?>" />
    					    <br/>
    					    <input type="text" id="SITE_LOGO_CODE"
    						name="SITE_LOGO_CODE"
    						value="<?php echo Settings::get(APConstants::SITE_LOGO_CODE)?>"
    						class="input-width input-width-400 readonly" readonly="readonly" />
    						<button type="button" class="tooltip admin-button" id="selectMainLogoButton">Change</button>
                    	    <input type="file" id="imagepath03"
                    				name="imagepath" class=""
                    				style="visibility: hidden; display: none;" />
    						<br /> <small>This image will display on the left corner of website</small></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Logo on white</th>
    					<td>
    					    <?php $site_logo_white_image_path = Settings::get(APConstants::SITE_LOGO_WHITE_CODE);?>
    					    <img id="SITE_LOGO_WHITE_CODE_IMG" alt="" src="<?php if (!empty($site_logo_white_image_path)) {echo APContext::getAssetPath().$site_logo_white_image_path;}?>" />
    					    <br/>
    					    <input type="text" id="SITE_LOGO_WHITE_CODE"
    						name="SITE_LOGO_WHITE_CODE"
    						value="<?php echo Settings::get(APConstants::SITE_LOGO_WHITE_CODE)?>"
    						class="input-width input-width-400 readonly" readonly="readonly" />
    						<button type="button" class="tooltip admin-button" id="selectWhiteLogoButton">Change</button>
                    	    <input type="file" id="imagepath04"
                    				name="imagepath" class=""
                    				style="visibility: hidden; display: none;" />
    						<br /> <small>This image will display on the left corner of website</small></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">First letter image</th>
    					<td><input type="text" id="FIRST_ENVELOPE_KEY"
    						name="FIRST_ENVELOPE_KEY"
    						value="<?php echo Settings::get(APConstants::FIRST_ENVELOPE_KEY)?>"
    						class="input-width input-width-400 readonly" readonly="readonly" />
    						<button type="button" class="tooltip admin-button" id="selectEnvelopeFileButton">Browser</button>
    						<button type="button" class="tooltip admin-button" id="previewEnvelopeFileButton">Preview</button>
                    	    <input type="file" id="imagepath02"
                    				name="imagepath" class=""
                    				style="visibility: hidden; display: none;" />
    						<br /> <small>This envelope should appear in the new postbox exactly 24 hours after the creation of the account</small></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">First letter item</th>
    					<td><input type="text" id="FIRST_LETTER_KEY"
    						name="FIRST_LETTER_KEY"
    						value="<?php echo Settings::get(APConstants::FIRST_LETTER_KEY)?>"
    						class="input-width input-width-400 readonly" readonly="readonly" />
    						<button type="button" class="tooltip admin-button" id="selectFileButton">Browser</button>
    						<button type="button" class="tooltip admin-button" id="previewFileButton">Preview</button>
                    	    <input type="file" id="imagepath"
                    				name="imagepath" class=""
                    				style="visibility: hidden; display: none;" />
    						<br /> <small>This letter should appear in the new postbox exactly 24 hours after the creation of the account</small></td>
    				</tr>
    				<tr>
    				    <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
    				    <td>
    				        <button id="saveButton" class="admin-button">Save</button>
    				    </td>
    				</tr>
    			</table>
    		</div>
    	</form>
    </div>
    
    <!-- Instance Owner -->
    <div id="tabs-2" class="button_container">
    	<form id="usesrSearchForm" method="post"
    		action="<?php echo base_url()?>admin/settings/instance_owner">
    		<div class="input-form">
    			<table class="settings">
    			    <tr>
    					<th class="input-width-200">Company</th>
    					<td><input type="text" id="INSTANCE_OWNER_COMPANY_CODE" name="INSTANCE_OWNER_COMPANY_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Street</th>
    					<td><input type="text" id="INSTANCE_OWNER_STREET_CODE" name="INSTANCE_OWNER_STREET_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">PLZ</th>
    					<td><input type="text" id="INSTANCE_OWNER_PLZ_CODE" name="INSTANCE_OWNER_PLZ_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">City</th>
    					<td><input type="text" id="INSTANCE_OWNER_CITY_CODE" name="INSTANCE_OWNER_CITY_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Region</th>
    					<td><input type="text" id="INSTANCE_OWNER_REGION_CODE" name="INSTANCE_OWNER_REGION_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_REGION_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Country</th>
    					<td><input type="text" id="INSTANCE_OWNER_COUNTRY_CODE" name="INSTANCE_OWNER_COUNTRY_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">VAT Number</th>
    					<td><input type="text" id="INSTANCE_OWNER_VAT_NUM_CODE" name="INSTANCE_OWNER_VAT_NUM_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Tax Number</th>
    					<td><input type="text" id="INSTANCE_OWNER_TAX_NUMBER_CODE" name="INSTANCE_OWNER_TAX_NUMBER_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TAX_NUMBER_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Director</th>
    					<td><input type="text" id="INSTANCE_OWNER_DIRECTOR_CODE" name="INSTANCE_OWNER_DIRECTOR_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_DIRECTOR_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">IBAN</th>
    					<td><input type="text" id="INSTANCE_OWNER_IBAN_CODE" name="INSTANCE_OWNER_IBAN_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">SWIFT/BIC</th>
    					<td><input type="text" id="INSTANCE_OWNER_SWIFT_CODE" name="INSTANCE_OWNER_SWIFT_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Bank Name</th>
    					<td><input type="text" id="INSTANCE_OWNER_BANK_NAME_CODE" name="INSTANCE_OWNER_BANK_NAME_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Telefon Invoice</th>
    					<td><input type="text" id="INSTANCE_OWNER_TEL_INVOICE_CODE" name="INSTANCE_OWNER_TEL_INVOICE_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">FAX</th>
    					<td><input type="text" id="INSTANCE_OWNER_FAX_CODE" name="INSTANCE_OWNER_FAX_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_FAX_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Telefon Sales</th>
    					<td><input type="text" id="INSTANCE_OWNER_TEL_SALES_CODE" name="INSTANCE_OWNER_TEL_SALES_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_SALES_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Telefon Support</th>
    					<td><input type="text" id="INSTANCE_OWNER_TEL_SUPPORT_CODE" name="INSTANCE_OWNER_TEL_SUPPORT_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_SUPPORT_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Mail Invoice</th>
    					<td><input type="text" id="INSTANCE_OWNER_MAIL_INVOICE_CODE" name="INSTANCE_OWNER_MAIL_INVOICE_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Mail Sales</th>
    					<td><input type="text" id="INSTANCE_OWNER_MAIL_SALES_CODE" name="INSTANCE_OWNER_MAIL_SALES_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_SALES_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Mail Support</th>
    					<td><input type="text" id="INSTANCE_OWNER_MAIL_SUPPORT_CODE" name="INSTANCE_OWNER_MAIL_SUPPORT_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_SUPPORT_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Website</th>
    					<td><input type="text" id="INSTANCE_OWNER_WEBSITE_CODE" name="INSTANCE_OWNER_WEBSITE_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_WEBSITE_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Registered number</th>
    					<td><input type="text" id="INSTANCE_OWNER_REGISTERED_NUM_CODE" name="INSTANCE_OWNER_REGISTERED_NUM_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Place of registration</th>
    					<td><input type="text" id="INSTANCE_OWNER_PLACE_REGISTRATION_CODE" name="INSTANCE_OWNER_PLACE_REGISTRATION_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_PLACE_REGISTRATION_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Account number</th>
    					<td><input type="text" id="INSTANCE_OWNER_ACCOUNTNUMBER_CODE" name="INSTANCE_OWNER_ACCOUNTNUMBER_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_ACCOUNTNUMBER_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Bank code</th>
    					<td><input type="text" id="INSTANCE_OWNER_BANKCODE_CODE" name="INSTANCE_OWNER_BANKCODE_CODE"
    						value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_BANKCODE_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    				    <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
    				    <td>
    				        <button id="saveInstanceOwnerButton" class="admin-button">Save</button>
    				    </td>
    				</tr>
    		    </table>
    		</div>
    	</form>
    </div>
    
    <div id="tabs-3" class="button_container">
    	<form id="usesrSearchForm" method="post"
    		action="<?php echo base_url()?>admin/settings/design">
    		<div class="input-form">
    			<table class="settings">
    			    <tr>
    					<th class="input-width-200">Main Color</th>
    					<td><input type="text" id="MAIN_COLOR_CODE" name="MAIN_COLOR_CODE"
    						value="<?php echo Settings::get(APConstants::MAIN_COLOR_CODE)?>"
    						class="input-width color_code" style="width: 50px; background: #<?php echo Settings::get(APConstants::MAIN_COLOR_CODE)?>" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Secondary Color</th>
    					<td><input type="text" id="SECOND_COLOR_CODE" name="SECOND_COLOR_CODE"
    						value="<?php echo Settings::get(APConstants::SECOND_COLOR_CODE)?>"
    						class="input-width color_code" style="width: 50px; background: #<?php echo Settings::get(APConstants::SECOND_COLOR_CODE)?>" /></td>
    				</tr>
    				<tr>
    				    <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
    				    <td>
    				        <button id="saveDesignButton" class="admin-button">Save</button>
    				    </td>
    				</tr>
    		    </table>
    		</div>
    	</form>
    </div>
    <!-- Terms of service -->
    <div id="tabs-4" class="button_container">
        <button id="addTermsServiceButton">Add Terms Of Service</button>
        <br />
        <!-- Term of service-->
        <div id="searchTableTermsServiceResult" style="margin-top: 10px;">
        	<table id="dataGridTermsServiceResult"></table>
        	<div id="dataGridTermsServicePager"></div>
        </div>
        
        <!-- Privacy statement-->
        <br />
        <button id="addPrivacyStatementButton">Add Privacy Statement</button>
        <br />
        <div id="searchTablePrivacyResult" style="margin-top: 10px;">
        	<table id="dataGridPrivacyResult"></table>
        	<div id="dataGridPrivacyPager"></div>
        </div>
        
    </div>
    <!-- Estamp -->
    <div id="tabs-5" class="button_container">
    	<form id="usesrSearchForm" method="post"
    		action="<?php echo base_url()?>admin/settings/estamp">
    		<div class="input-form">
    			<table class="settings">
    			    <tr>
    					<th class="input-width-200">E-Stamp username</th>
    					<td><input type="text" id="ESTAMP_USER" name="ESTAMP_USER"
    						value="<?php echo Settings::get(APConstants::ESTAMP_USER)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp Password</th>
    					<td><input type="password" id="ESTAMP_PASSWORD" name="ESTAMP_PASSWORD"
    						value="<?php echo Settings::get(APConstants::ESTAMP_PASSWORD)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp Link</th>
    					<td><input type="text" id="ESTAMP_LINK" name="ESTAMP_LINK"
    						value="<?php echo Settings::get(APConstants::ESTAMP_LINK)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp PARTNER ID</th>
    					<td><input type="text" id="ESTAMP_PARTNER_ID" name="ESTAMP_PARTNER_ID"
    						value="<?php echo Settings::get(APConstants::ESTAMP_PARTNER_ID)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp KEY PHASE</th>
    					<td><input type="text" id="ESTAMP_KEY_PHASE" name="ESTAMP_KEY_PHASE"
    						value="<?php echo Settings::get(APConstants::ESTAMP_KEY_PHASE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp PARTNER SIGNATURE</th>
    					<td><input type="text" id="ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ" name="ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ"
    						value="<?php echo Settings::get(APConstants::ESTAMP_SCHLUESSEL_DPWN_MARKTPLATZ)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">E-Stamp Namespace</th>
    					<td><input type="text" id="ESTAMP_NAMESPACE" name="ESTAMP_NAMESPACE"
    						value="<?php echo Settings::get(APConstants::ESTAMP_NAMESPACE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    				    <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
    				    <td>
    				        <button id="savePayoneButton" class="admin-button">Save</button>
    				    </td>
    				</tr>
    		    </table>
    		</div>
    	</form>
    </div>
    <!-- Payone -->
    <div id="tabs-6" class="button_container">
    	<form id="usesrSearchForm" method="post"
    		action="<?php echo base_url()?>admin/settings/payone">
    		<div class="input-form">
    		    <h2>Payone setting of production system (mode LIVE)</h2>
    			<table class="settings">
    			    <tr>
    					<th class="input-width-200">Merchant-id</th>
    					<td><input type="text" id="MERCHANT_ID_CODE" name="MERCHANT_ID_CODE"
    						value="<?php echo Settings::get(APConstants::MERCHANT_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Sub Account-ID</th>
    					<td><input type="text" id="SUB_ACCOUNT_ID_CODE" name="SUB_ACCOUNT_ID_CODE"
    						value="<?php echo Settings::get(APConstants::SUB_ACCOUNT_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Portal-ID</th>
    					<td><input type="text" id="PORTAL_ID_CODE" name="PORTAL_ID_CODE"
    						value="<?php echo Settings::get(APConstants::PORTAL_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Portal-KEY</th>
    					<td><input type="text" id="PORTAL_KEY_CODE" name="PORTAL_KEY_CODE"
    						value="<?php echo Settings::get(APConstants::PORTAL_KEY_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    			</table>
    			
    			<h2>Payone setting of TEST/DEV system (mode TEST)</h2>
    			<table class="settings">
    				<!-- Using for test -->
    				<tr>
    					<th class="input-width-200">Test Merchant-id</th>
    					<td><input type="text" id="TEST_MERCHANT_ID_CODE" name="TEST_MERCHANT_ID_CODE"
    						value="<?php echo Settings::get(APConstants::TEST_MERCHANT_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Test Sub Account-ID</th>
    					<td><input type="text" id="TEST_SUB_ACCOUNT_ID_CODE" name="TEST_SUB_ACCOUNT_ID_CODE"
    						value="<?php echo Settings::get(APConstants::TEST_SUB_ACCOUNT_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Test Portal-ID</th>
    					<td><input type="text" id="TEST_PORTAL_ID_CODE" name="TEST_PORTAL_ID_CODE"
    						value="<?php echo Settings::get(APConstants::TEST_PORTAL_ID_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    					<th class="input-width-200">Test Portal-KEY</th>
    					<td><input type="text" id="TEST_PORTAL_KEY_CODE" name="TEST_PORTAL_KEY_CODE"
    						value="<?php echo Settings::get(APConstants::TEST_PORTAL_KEY_CODE)?>"
    						class="input-width" /></td>
    				</tr>
    				<tr>
    				    <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
    				    <td>
    				        <button id="savePayoneButton" class="admin-button">Save</button>
    				    </td>
    				</tr>
    		    </table>
    		</div>
    	</form>
    </div>
</div>
<div class="clear-height"></div>
<div class="hide" style="display: none;">
    <a id="preview_first_letter" class="iframe" href="<?php echo APContext::getAssetPath().Settings::get(APConstants::FIRST_LETTER_KEY)?>">Preview first letter</a>
    <a id="preview_first_letter_envelope" class="iframe" href="<?php echo APContext::getAssetPath().Settings::get(APConstants::FIRST_ENVELOPE_KEY)?>">Preview first letter envelope</a>
    
    <div id="addTermsServiceWindow" title="Add Terms Of Service"></div>
    <div id="addPrivacyStatementWindow" title="Add Privacy Statement"></div>
    
    <a href="" id="displayTermsServiceLink" class="iframe"></a>
    <a href="" id="displayPrivacyLink" class="iframe"></a>
</div>

<script type="text/javascript">
$(document).ready( function() {
	$( "#tabs" ).tabs();
	$( "#tabs" ).tabs({ selected: <?php echo $atab?> });
	$('.admin-button').button();
	$('.color_code').colorpicker({
		ok: function(event, color) {
			$(this).css( "background-color", '#' + color.formatted );
		}
	});
	var assetPath = '<?php echo APContext::getAssetPath();?>';
	
	searchPrivacyStatement();
	
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


	/**
	 * Search data for terms and service
	 */
	function searchTermsService() {
		$("#dataGridTermsServiceResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>settings/terms?type=1';
		
        $("#dataGridTermsServiceResult").jqGrid({
        	url: url,
        	// postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: 200,
            width: 1000,
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridTermsServicePager",
            sortname: 'created_date',
            sortorder: "desc",
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','URL', 'Creted Date', 'Current','Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'file_name',index:'file_name', width:550},
               {name:'created_date',index:'created_date', width:125, align:"center"},
               {name:'use_flag',index:'use_flag', width:75, sortable: false, align:"center", formatter: activeFormater},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		// console.log(data_row);
            },
            loadComplete: function() {
                // $.autoFitScreen(1240);
            }
        });
	}

	function activeFormater(cellvalue, options, rowObject) {
		if (cellvalue == '1') {
			return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-tick">Check</span></span>';
		} else {
			return '';
		}
	}
	
	function actionFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit view_terms" title="View Terms Of Service" data-id="' + cellvalue + '"></span></span>';
	}


	/**
	 * Search data for terms and service
	 */
	function searchPrivacyStatement() {
		$("#dataGridPrivacyResult").jqGrid('GridUnload');
		var url = '<?php echo base_url() ?>settings/terms?type=2';
		
        $("#dataGridPrivacyResult").jqGrid({
        	url: url,
        	// postData: $('#usesrSearchForm').serializeObject(),
            mtype: 'POST',
        	datatype: "json",
            height: 200,
            width: 1000,
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPrivacyPager",
            sortname: 'created_date',
            sortorder: "desc",
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['ID','URL', 'Creted Date', 'Current','Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'file_name',index:'file_name', width:550},
               {name:'created_date',index:'created_date', width:125, align:"center"},
               {name:'use_flag',index:'use_flag', sortable: false, align:"center", width:75, formatter: activeFormater},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionPrivacyFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
        		// var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
        		// console.log(data_row);
            },
            loadComplete: function() {
                // $.autoFitScreen(1240);
            }
        });
	}

	function actionPrivacyFormater(cellvalue, options, rowObject) {
		return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit view_privacy" title="View Privacy" data-id="' + cellvalue + '"></span></span>';
	}

	/**
	 * Add terms of service
	 */
	$('#addTermsServiceButton').button().click(function(){
	    window.location = "<?php echo base_url() ?>settings/terms/add_terms";
	});
	
	/**
	 * Process when user click to view icon.
	 */
	$('.view_terms').live('click', function() {
		var id = $(this).data('id');
		window.location = "<?php echo base_url() ?>settings/terms/edit_terms?id=" + id;
	});

	/**
	 * Add terms of service
	 */
	$('#addPrivacyStatementButton').button().click(function(){
		window.location = "<?php echo base_url() ?>settings/terms/add_privacy";
	});

	/**
	 * Process when user click to view icon.
	 */
	$('.view_privacy').live('click', function() {
		var id = $(this).data('id');
		window.location = "<?php echo base_url() ?>settings/terms/edit_privacy?id=" + id;
	});

	
});
</script>