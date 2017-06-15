<?php
    $mode = 'readonly';
?>
<div class="ym-grid content services" id="case-body-wrapper">
    <div class="ym-grid">
        <div class="header">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_todo_review_company_info_CompanyInformation'); ?></h2>
        </div>
        <form id="company_information_form"
            action="<?php echo base_url()?>cases/todo/review_company_information"
            method="post">
            <input type="hidden" id="company_information_form_case_id"
                name="case_id" value="<?php echo $case_id;?>" />
             <table>
                <tr>
                    <td>
                        <table style="width: 50%;">
                            <tr>
                                <th width="100px;"><?php admin_language_e('cases_view_todo_review_company_info_CompanyLegalStatus'); ?>'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="company_legal" value="<?php echo $company_information->company_legal?>" <?php echo $mode;?>
                                    class="input-txt" required="required" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_CompanyName'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="company_name" value="<?php echo $company_information->company_name?>" <?php echo $mode;?>
                                    class="input-txt" required="required" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_StreetAddress'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="street_address" value="<?php echo $company_information->street_address?>" <?php echo $mode;?>
                                    class="input-txt" required="required" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_PostCode'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="post_code" value="<?php echo $company_information->post_code?>" class="input-txt" <?php echo $mode;?>
                                    required="required" /></td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td><input type="text" name="city" value="<?php echo $company_information->city?>" class="input-txt" <?php echo $mode;?>/></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_Region'); ?></th>
                                <td><input type="text" name="region" value="<?php echo $company_information->region?>" class="input-txt" <?php echo $mode;?>/></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_Country'); ?> <span class="required">*</span></th>
                                <td><select name="country" class="input-width">
                                                <?php foreach ( $countries as $country ) :?>
                                                <option
                                            value="<?php echo $country->id?>" <?php if ($company_information->country == $country->id){?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                                <?php endforeach;?>
                                             </select></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_Website'); ?></th>
                                <td><input type="text" name="website" value="<?php echo $company_information->website?>" class="input-txt" <?php echo $mode;?>/></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_PurposeOfCompany'); ?></th>
                                <td><textarea rows="3" cols="65" name="purpose_of_company" class="input-txt" style="height: 100%; background: #FFF;" <?php echo $mode;?>><?php echo $company_information->purpose_of_company?></textarea>
                                </td>
                            </tr>

                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_RegisteredCapital'); ?></th>
                                <td><input type="text" name="registered_capital" value="<?php echo $company_information->registered_capital?>" <?php echo $mode;?>
                                    class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_CapitalPaidIn'); ?></th>
                                <td><input type="text" name="capital_paid" value="<?php echo $company_information->capital_paid?>" <?php echo $mode;?>
                                    class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_PhoneNumber'); ?></th>
                                <td><input type="text" name="phone_number" value="<?php echo $company_information->phone_number?>" <?php echo $mode;?>
                                    class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_EmailAddress'); ?></th>
                                <td><input type="text" name="email_address" value="<?php echo $company_information->email_address?>" <?php echo $mode;?>
                                    class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_RegistrationNumber'); ?></th>
                                <td><input type="text" name="registration_number" value="<?php echo $company_information->registration_number?>" <?php echo $mode;?>
                                    class="input-txt" /></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table style="width: 50%;">
                            <tr>
                                <th width="100px;"><?php admin_language_e('cases_view_todo_review_company_info_Status'); ?> <span class="required">*</span></th>
                                <td><select class="input-width" name="status">
                                        <option value="2"><?php admin_language_e('cases_view_todo_review_company_info_Completed'); ?></option>
                                        <option value="3"><?php admin_language_e('cases_view_todo_review_company_info_Incomplete'); ?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_company_info_Comment'); ?> <span class="required">*</span></th>

                                <td><textarea rows="3" cols="65" name="comment_content" class="input-txt" style="height: 100%; background: #FFF;"><?php echo $company_information->comment_content?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button id="submitButton"><?php admin_language_e('cases_view_todo_review_company_info_Submit'); ?></button>
                            </tr>
                        </table>
                    </td>
            </tr>
            </table>
        </form>
    </div>
    <div class="ym-clearfix"></div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    $('#submitButton').button();
    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var submitUrl = $('#company_information_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'company_information_form',
            success: function(data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>