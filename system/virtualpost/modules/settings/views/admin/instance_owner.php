<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px">Settings > Instances >
		Instance Owner</h2>
</div>
<form id="usesrSearchForm" method="post"
	action="<?php echo base_url()?>admin/settings/instance_owner">
	<div class="input-form">
            <table class="settings" style="width: 50%;float: left;">
                    <tr>
                            <th class="input-width-200">Company</th>
                            <td><input type="text" id="INSTANCE_OWNER_COMPANY_CODE"
                                    name="INSTANCE_OWNER_COMPANY_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Street</th>
                            <td><input type="text" id="INSTANCE_OWNER_STREET_CODE"
                                    name="INSTANCE_OWNER_STREET_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">PLZ</th>
                            <td><input type="text" id="INSTANCE_OWNER_PLZ_CODE"
                                    name="INSTANCE_OWNER_PLZ_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">City</th>
                            <td><input type="text" id="INSTANCE_OWNER_CITY_CODE"
                                    name="INSTANCE_OWNER_CITY_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Region</th>
                            <td><input type="text" id="INSTANCE_OWNER_REGION_CODE"
                                    name="INSTANCE_OWNER_REGION_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_REGION_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Country</th>
                            <td><input type="text" id="INSTANCE_OWNER_COUNTRY_CODE"
                                    name="INSTANCE_OWNER_COUNTRY_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">VAT Number</th>
                            <td><input type="text" id="INSTANCE_OWNER_VAT_NUM_CODE"
                                    name="INSTANCE_OWNER_VAT_NUM_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Tax Number</th>
                            <td><input type="text" id="INSTANCE_OWNER_TAX_NUMBER_CODE"
                                    name="INSTANCE_OWNER_TAX_NUMBER_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TAX_NUMBER_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Director</th>
                            <td><input type="text" id="INSTANCE_OWNER_DIRECTOR_CODE"
                                    name="INSTANCE_OWNER_DIRECTOR_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_DIRECTOR_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">IBAN</th>
                            <td><input type="text" id="INSTANCE_OWNER_IBAN_CODE"
                                    name="INSTANCE_OWNER_IBAN_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">SWIFT/BIC</th>
                            <td><input type="text" id="INSTANCE_OWNER_SWIFT_CODE"
                                    name="INSTANCE_OWNER_SWIFT_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Bank Name</th>
                            <td><input type="text" id="INSTANCE_OWNER_BANK_NAME_CODE"
                                    name="INSTANCE_OWNER_BANK_NAME_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Telefon Invoice</th>
                            <td><input type="text" id="INSTANCE_OWNER_TEL_INVOICE_CODE"
                                    name="INSTANCE_OWNER_TEL_INVOICE_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">FAX</th>
                            <td><input type="text" id="INSTANCE_OWNER_FAX_CODE"
                                    name="INSTANCE_OWNER_FAX_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_FAX_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Telefon Sales</th>
                            <td><input type="text" id="INSTANCE_OWNER_TEL_SALES_CODE"
                                    name="INSTANCE_OWNER_TEL_SALES_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_SALES_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Telefon Support</th>
                            <td><input type="text" id="INSTANCE_OWNER_TEL_SUPPORT_CODE"
                                    name="INSTANCE_OWNER_TEL_SUPPORT_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_TEL_SUPPORT_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Mail Invoice</th>
                            <td><input type="text" id="INSTANCE_OWNER_MAIL_INVOICE_CODE"
                                    name="INSTANCE_OWNER_MAIL_INVOICE_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Mail Sales</th>
                            <td><input type="text" id="INSTANCE_OWNER_MAIL_SALES_CODE"
                                    name="INSTANCE_OWNER_MAIL_SALES_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_SALES_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Mail Support</th>
                            <td><input type="text" id="INSTANCE_OWNER_MAIL_SUPPORT_CODE"
                                    name="INSTANCE_OWNER_MAIL_SUPPORT_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_MAIL_SUPPORT_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Website</th>
                            <td><input type="text" id="INSTANCE_OWNER_WEBSITE_CODE"
                                    name="INSTANCE_OWNER_WEBSITE_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_WEBSITE_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Registered number</th>
                            <td><input type="text" id="INSTANCE_OWNER_REGISTERED_NUM_CODE"
                                    name="INSTANCE_OWNER_REGISTERED_NUM_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Place of registration</th>
                            <td><input type="text" id="INSTANCE_OWNER_PLACE_REGISTRATION_CODE"
                                    name="INSTANCE_OWNER_PLACE_REGISTRATION_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_PLACE_REGISTRATION_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Account number</th>
                            <td><input type="text" id="INSTANCE_OWNER_ACCOUNTNUMBER_CODE"
                                    name="INSTANCE_OWNER_ACCOUNTNUMBER_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_ACCOUNTNUMBER_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Bank code</th>
                            <td><input type="text" id="INSTANCE_OWNER_BANKCODE_CODE"
                                    name="INSTANCE_OWNER_BANKCODE_CODE"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_BANKCODE_CODE)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200">Customs Number</th>
                            <td><input type="text" id="INSTANCE_OWNER_CUSTOMS_NUMBER"
                                    name="INSTANCE_OWNER_CUSTOMS_NUMBER"
                                    value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_CUSTOMS_NUMBER)?>"
                                    class="input-width" /></td>
                    </tr>
                    <tr>
                            <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
                            <td>
                                    <button id="saveInstanceOwnerButton" class="admin-button">Save</button>
                            </td>
                    </tr>
            </table>
                
            <table class="settings" style="width: 50%;float:right">
                <tr>
                    <td colspan="2"><h2 style="font-size: 16px;">Accounting</h2></td>
                </tr>
                
                <tr>
                    <td>Gegenkonto :</td>
                    <td><input type="text" id="GEGENKONTO_NUMBER"
                                    name="GEGENKONTO_NUMBER"
                                    value="<?php echo Settings::get(APConstants::GEGENKONTO_NUMBER)?>"
                                    class="input-width" /></td>   
                </tr>
                
                <tr>
                    <td>Only third country taxable :</td>
                    <td><input type="text" id="THIRST_COUNTRY_TAXABLE"
                                    name="THIRST_COUNTRY_TAXABLE"
                                    value="<?php echo Settings::get(APConstants::THIRST_COUNTRY_TAXABLE)?>"
                                    class="input-width" /></td>   
                </tr>
                
                <tr>
                    <td>Only EU country taxable :</td>
                    <td><input type="text" id="EU_COUNTRY_TAXABLE"
                                    name="EU_COUNTRY_TAXABLE"
                                    value="<?php echo Settings::get(APConstants::EU_COUNTRY_TAXABLE)?>"
                                    class="input-width" /></td>   
                </tr>
                
                <tr>
                    <td>Inland taxable revenue :</td>
                    <td><input type="text" id="INLAND_TAXABLE_REVENUE"
                                    name="INLAND_TAXABLE_REVENUE"
                                    value="<?php echo Settings::get(APConstants::INLAND_TAXABLE_REVENUE)?>"
                                    class="input-width" /></td>   
                </tr>
            </table>
	</div>
</form>
<script type="text/javascript">
$(document).ready( function() {
	$('.admin-button').button();
});
</script>