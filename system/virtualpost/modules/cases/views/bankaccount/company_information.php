<div class="ym-grid content services" id="case-body-wrapper">
	<div id="go-back">
		<span><a id="backButton" href="<?php echo base_url()?>cases/bankaccount/personal_identify?case_id=<?php echo $case_id?>"><?php language_e('cases_view_admin_bankaccount_company_infor_Back'); ?></a></span>
	</div>
	<div class="ym-clearfix"></div>
	<div class="header">
		<h2 style="font-size: 20px; margin-bottom: 10px">Please fill out all
			required information</h2>
	</div>
	<div class="ym-clearfix"></div>
	<div class="ym-grid">
	    <h2><?php language_e('cases_view_admin_bankaccount_company_infor_CompanyInformation'); ?>:</h2>
		<form id="company_information_form"
			action="<?php echo base_url()?>cases/bankaccount/company_information"
			method="post">
			<input type="hidden" id="company_information_form_case_id"
				name="case_id" value="<?php echo $case_id;?>" />

			<table style="width: 100%;">
				<tr>
					<th width="100px;"><?php language_e('cases_view_admin_bankaccount_company_infor_CompanyLegalStatus'); ?> <span class="required">*</span></th>
					<td><input type="text" name="company_legal" value="<?php echo $company_information->company_legal?>"
						class="input-txt" required="required" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_CompanyName'); ?> <span class="required">*</span></th>
					<td><input type="text" name="company_name" value="<?php echo $company_information->company_name?>"
						class="input-txt" required="required" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_StreetAddress'); ?> <span class="required">*</span></th>
					<td><input type="text" name="street_address" value="<?php echo $company_information->street_address?>"
						class="input-txt" required="required" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_PostCode'); ?> <span class="required">*</span></th>
					<td><input type="text" name="post_code" value="<?php echo $company_information->post_code?>" class="input-txt"
						required="required" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_City'); ?></th>
					<td><input type="text" name="city" value="<?php echo $company_information->city?>" class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_Region'); ?></th>
					<td><input type="text" name="region" value="<?php echo $company_information->region?>" class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_Country'); ?> <span class="required">*</span></th>
					<td><select name="country" class="input-txt">
            		                <?php foreach ( $countries as $country ) :?>
                                    <option
								value="<?php echo $country->id?>" <?php if ($company_information->country == $country->id){?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                    <?php endforeach;?>
                                 </select></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_Website'); ?></th>
					<td><input type="text" name="website" value="<?php echo $company_information->website?>" class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_PurposeOfCompany'); ?></th>
					<td><textarea rows="3" cols="65" name="purpose_of_company" class="input-txt" style="height: 100%; background: #FFF;"><?php echo $company_information->purpose_of_company?></textarea>
					</td>
				</tr>

				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_RegisteredCapital'); ?></th>
					<td><input type="text" name="registered_capital" value="<?php echo $company_information->registered_capital?>"
						class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_CapitalPaidIn'); ?></th>
					<td><input type="text" name="capital_paid" value="<?php echo $company_information->capital_paid?>"
						class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_PhoneNumber'); ?></th>
					<td><input type="text" name="phone_number" value="<?php echo $company_information->phone_number?>"
						class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_EmailAddress'); ?></th>
					<td><input type="text" name="email_address" value="<?php echo $company_information->email_address?>"
						class="input-txt" /></td>
				</tr>
				<tr>
					<th><?php language_e('cases_view_admin_bankaccount_company_infor_RegistrationNumber'); ?></th>
					<td><input type="text" name="registration_number" value="<?php echo $company_information->registration_number?>"
						class="input-txt" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<button id="submitButton" class="input-btn">Next Step</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="ym-clearfix"></div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    /**
     * When user click submit button
     */
	$('#submitButton').click(function(){
		var case_id = $('#company_information_form_case_id').val();

        var submitUrl = $('#company_information_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'company_information_form',
            success: function(data) {
                if (data.status) {
                	document.location.href = '<?php echo base_url()?>cases/bankaccount/company_registration_document?case_id=' + case_id;
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>