<style>
    .input-width{
        margin: 0px;
    }
</style>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2>Invoice Setup</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
    </div>
</div>
<div class="clearfix"></div>
<div id="account-body-wrapper" style="margin:20px 0 0 40px">
    <div class="ym-grid">
        <form id="usesrSearchForm" method="post" action="<?php echo base_url() ?>account/setting/invoice_setup">
            <div class="input-form">
                <table class="settings" style="width: 50%;float: left;">
                    <tr>
                        <th class="input-width-200">Company <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_COMPANY_CODE"
                                   name="INSTANCE_OWNER_COMPANY_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_COMPANY_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Street <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_STREET_CODE"
                                   name="INSTANCE_OWNER_STREET_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_STREET_CODE) ?>"
                                   class="input-width"  maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">PLZ <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_PLZ_CODE"
                                   name="INSTANCE_OWNER_PLZ_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_PLZ_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">City <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_CITY_CODE"
                                   name="INSTANCE_OWNER_CITY_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_CITY_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Region</th>
                        <td><input type="text" id="INSTANCE_OWNER_REGION_CODE"
                                   name="INSTANCE_OWNER_REGION_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_REGION_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Country <span class="required">*</span></th>
                        <td>
                        <?php echo my_form_dropdown(array(
                            "data" => $countries,
                            "value_key" => 'id',
                            "label_key" => 'country_name',
                            "value" => AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_COUNTRY_CODE),
                            "name" => 'INSTANCE_OWNER_COUNTRY_CODE',
                            "id" => 'INSTANCE_OWNER_COUNTRY_CODE',
                            "clazz" => 'input-width',
                            "style" => 'width: 260px;',
                            "has_empty" => true,
                            "option_default" => 'no country'
                        )); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="input-width-200">VAT Number <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_VAT_NUM_CODE"
                                   name="INSTANCE_OWNER_VAT_NUM_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_VAT_NUM_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Tax Number</th>
                        <td><input type="text" id="INSTANCE_OWNER_TAX_NUMBER_CODE"
                                   name="INSTANCE_OWNER_TAX_NUMBER_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_TAX_NUMBER_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Director <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_DIRECTOR_CODE"
                                   name="INSTANCE_OWNER_DIRECTOR_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_DIRECTOR_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">IBAN</th>
                        <td><input type="text" id="INSTANCE_OWNER_IBAN_CODE"
                                   name="INSTANCE_OWNER_IBAN_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_IBAN_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">SWIFT/BIC</th>
                        <td><input type="text" id="INSTANCE_OWNER_SWIFT_CODE"
                                   name="INSTANCE_OWNER_SWIFT_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_SWIFT_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Bank Name</th>
                        <td><input type="text" id="INSTANCE_OWNER_BANK_NAME_CODE"
                                   name="INSTANCE_OWNER_BANK_NAME_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_BANK_NAME_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Customs Number</th>
                        <td><input type="text" id="INSTANCE_OWNER_CUSTOMS_NUMBER"
                                   name="INSTANCE_OWNER_CUSTOMS_NUMBER"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_CUSTOMS_NUMBER) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
                        <td>
                            <button id="saveInstanceOwnerButton" class="input-btn btn-yellow" type="button">Save</button>
                        </td>
                    </tr>
                </table>

                <table class="settings" style="width: 50%;float:right">
                    <tr>
                        <th class="input-width-200">Telephone Invoice</th>
                        <td><input type="text" id="INSTANCE_OWNER_TEL_INVOICE_CODE"
                                   name="INSTANCE_OWNER_TEL_INVOICE_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">FAX</th>
                        <td><input type="text" id="INSTANCE_OWNER_FAX_CODE"
                                   name="INSTANCE_OWNER_FAX_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_FAX_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Telephone Sales</th>
                        <td><input type="text" id="INSTANCE_OWNER_TEL_SALES_CODE"
                                   name="INSTANCE_OWNER_TEL_SALES_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_TEL_SALES_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Telephone Support</th>
                        <td><input type="text" id="INSTANCE_OWNER_TEL_SUPPORT_CODE"
                                   name="INSTANCE_OWNER_TEL_SUPPORT_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_TEL_SUPPORT_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Mail Invoice <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_MAIL_INVOICE_CODE"
                                   name="INSTANCE_OWNER_MAIL_INVOICE_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Mail Sales</th>
                        <td><input type="text" id="INSTANCE_OWNER_MAIL_SALES_CODE"
                                   name="INSTANCE_OWNER_MAIL_SALES_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_MAIL_SALES_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Mail Support</th>
                        <td><input type="text" id="INSTANCE_OWNER_MAIL_SUPPORT_CODE"
                                   name="INSTANCE_OWNER_MAIL_SUPPORT_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_MAIL_SUPPORT_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Website</th>
                        <td><input type="text" id="INSTANCE_OWNER_WEBSITE_CODE"
                                   name="INSTANCE_OWNER_WEBSITE_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_WEBSITE_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Registered number <span class="required">*</span></th>
                        <td><input type="text" id="INSTANCE_OWNER_REGISTERED_NUM_CODE"
                                   name="INSTANCE_OWNER_REGISTERED_NUM_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Place of registration</th>
                        <td><input type="text" id="INSTANCE_OWNER_PLACE_REGISTRATION_CODE"
                                   name="INSTANCE_OWNER_PLACE_REGISTRATION_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_PLACE_REGISTRATION_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Account number</th>
                        <td><input type="text" id="INSTANCE_OWNER_ACCOUNTNUMBER_CODE"
                                   name="INSTANCE_OWNER_ACCOUNTNUMBER_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_ACCOUNTNUMBER_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    <tr>
                        <th class="input-width-200">Bank code</th>
                        <td><input type="text" id="INSTANCE_OWNER_BANKCODE_CODE"
                                   name="INSTANCE_OWNER_BANKCODE_CODE"
                                   value="<?php echo AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_BANKCODE_CODE) ?>"
                                   class="input-width" maxlength="250" /></td>
                    </tr>
                    
                    
                </table>
                
            </div>
        </form>

    </div>
</div>
<div class="clearfix"></div>
<br />
<br />
<br />
<script type="text/javascript">
    $(document).ready(function () {
        $('.input-btn').button();
        
        $("#saveInstanceOwnerButton").click(function(e){
            e.preventDefault();
            
            $.ajaxSubmit({
                url: '<?php echo base_url() ?>account/setting/invoice_setup',
                formId: 'usesrSearchForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null,  function() { });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            
            return false;
        });
    });
</script>