<div class="ym-grid content services" id="case-body-wrapper">
    <div class="ym-grid">
        <div class="header">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_todo_review_personal_identity_PersonalIdentityInformation'); ?></h2>
        </div>
        <form id="personal_identify_form" action="<?php echo base_url()?>cases/todo/review_personal_identify" method="post">
        <input type="hidden" id="personal_identify_form_case_id" name="case_id" value="<?php echo $case_id;?>" />
        <table>
                <tr>
                    <td>
                        <div id="tabs">
                            <ul>
                                <li><a href="#tabs-1"><?php admin_language_e('cases_view_todo_review_personal_identity_IdentityInformationOfDirector'); ?></a></li>
                                <?php if (!empty($personal_identity_02)) {?>
                                <li><a href="#tabs-2"><?php admin_language_e('cases_view_todo_review_personal_identity_SecondDirector'); ?></a></li>
                                <?php }?>
                            </ul>
                            <div id="tabs-1">
                                <table style="width: 450px;">
                                    <tr>
                                        <th width="100px;"><?php admin_language_e('cases_view_todo_review_personal_identity_FirstName'); ?> <span class="required">*</span></th>
                                        <td><input type="text" name="n1_first_name" value="<?php echo $personal_identity_01->first_name?>" class="input-txt" required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_MiddleName'); ?> <span class="required">*</span></th>
                                        <td><input type="text" name="n1_middle_name" value="<?php echo $personal_identity_01->middle_name?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_LastName'); ?> <span class="required">*</span></th>
                                        <td><input type="text" name="n1_last_name" value="<?php echo $personal_identity_01->last_name?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_StreetAddress'); ?> <span class="required">*</span></th>
                                        <td><input type="text" name="n1_street_address" value="<?php echo $personal_identity_01->street_address?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PostCode'); ?> <span class="required">*</span></th>
                                        <td><input type="text" name="n1_post_code" value="<?php echo $personal_identity_01->post_code?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_City'); ?></th>
                                        <td><input type="text" name="n1_city" value="<?php echo $personal_identity_01->city?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_Region'); ?></th>
                                        <td><input type="text" name="n1_region" value="<?php echo $personal_identity_01->region?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_Country'); ?> <span class="required">*</span></th>
                                        <td><select name="n1_country" class="input-width">
                                            <?php foreach ( $countries as $country ) :?>
                                            <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                            <?php endforeach;?>
                                         </select></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_DateOfBirth'); ?></th>
                                        <td><input type="text" name="n1_date_of_birth" value="<?php echo $personal_identity_01->date_of_birth?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PlaceOfBirth'); ?></th>
                                        <td><input type="text" name="n1_place_of_birth" value="<?php echo $personal_identity_01->place_of_birth?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_CountryOfBirth'); ?></th>
                                        <td><select class="input-width" name="n1_country_of_birth">
                                                <?php foreach ( $countries as $country ) :?>
                                                <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country_of_birth == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                                <?php endforeach;?>
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PhoneNumber'); ?></th>
                                        <td><input type="text" name="n1_phone_number" value="<?php echo $personal_identity_01->phone_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_MobileNumber'); ?></th>
                                        <td><input type="text" name="n1_mobile_number" value="<?php echo $personal_identity_01->mobile_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_EmailAddress'); ?></th>
                                        <td><input type="text" name="n1_email_address" value="<?php echo $personal_identity_01->email_address?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PassportNumber'); ?></th>
                                        <td><input type="text" name="n1_passport_number" value="<?php echo $personal_identity_01->passport_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><?php admin_language_e('cases_view_todo_review_personal_identity_NotarizedIdentificationDocumen'); ?></th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="text" id="n1_input_passport_certificate" value="<?php echo $personal_identity_01->passport_local_file_path?>" class="input-txt" style="width: 340px; margin-left: 0px;">
                                            <button id="n1_button_preview_passport_certificate"><?php admin_language_e('cases_view_todo_review_personal_identity_Preview'); ?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><?php admin_language_e('cases_view_todo_review_personal_identity_NotarizedBirthCertificate'); ?></th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="text" id="n1_input_birth_certificate" value="<?php echo $personal_identity_01->birth_certificate_local_file_path?>" class="input-txt" style="width: 340px; margin-left: 0px;">
                                            <button id="n1_button_preview_birth_certificate"><?php admin_language_e('cases_view_todo_review_personal_identity_Preview'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php if (!empty($personal_identity_02)) {?>
                            <div id="tabs-2">
                                <table style="width: 450px;">
                                    <tr>
                                        <th width="100px;"><?php admin_language_e('cases_view_todo_review_personal_identity_FirstName'); ?> </th>
                                        <td><input type="text" name="n2_first_name" value="<?php echo $personal_identity_02->first_name?>" class="input-txt" required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_MiddleName'); ?> </th>
                                        <td><input type="text" name="n2_middle_name" value="<?php echo $personal_identity_02->middle_name?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_LastName'); ?> </th>
                                        <td><input type="text" name="n2_last_name" value="<?php echo $personal_identity_02->last_name?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_StreetAddress'); ?> </th>
                                        <td><input type="text" name="n2_street_address" value="<?php echo $personal_identity_02->street_address?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PostCode'); ?> </th>
                                        <td><input type="text" name="n2_post_code" value="<?php echo $personal_identity_02->post_code?>" class="input-txt"  required="required" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_City'); ?></th>
                                        <td><input type="text" name="n2_city" value="<?php echo $personal_identity_02->city?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_Region'); ?></th>
                                        <td><input type="text" name="n2_region" value="<?php echo $personal_identity_02->region?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_Country'); ?> </th>
                                        <td><select name="n2_country" class="input-width">
                                            <?php foreach ( $countries as $country ) :?>
                                            <option value="<?php echo $country->id?>" <?php if ($personal_identity_02->country == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                            <?php endforeach;?>
                                         </select></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_DateOfBirth'); ?></th>
                                        <td><input type="text" name="n2_date_of_birth" value="<?php echo $personal_identity_02->date_of_birth?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PlaceOfBirth'); ?></th>
                                        <td><input type="text" name="n2_place_of_birth" value="<?php echo $personal_identity_02->place_of_birth?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_CountryOfBirth'); ?></th>
                                        <td><select class="input-width" name="n2_country_of_birth">
                                            <?php foreach ( $countries as $country ) :?>
                                            <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country_of_birth == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                            <?php endforeach;?>
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PhoneNumber'); ?></th>
                                        <td><input type="text" name="n2_phone_number" value="<?php echo $personal_identity_02->phone_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_MobileNumber'); ?></th>
                                        <td><input type="text" name="n2_mobile_number" value="<?php echo $personal_identity_02->mobile_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_EmailAddress'); ?></th>
                                        <td><input type="text" name="n2_email_address" value="<?php echo $personal_identity_02->email_address?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th ><?php admin_language_e('cases_view_todo_review_personal_identity_PassportNumber'); ?></th>
                                        <td><input type="text" name="n2_passport_number" value="<?php echo $personal_identity_02->passport_number?>" class="input-txt" /></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><?php admin_language_e('cases_view_todo_review_personal_identity_NotarizedIdentificationDocumen'); ?></th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="text" id="n2_input_passport_certificate" value="<?php echo $personal_identity_02->passport_local_file_path?>" class="input-txt" style="width: 340px; margin-left: 0px;">
                                            <button id="n2_button_preview_passport_certificate"><?php admin_language_e('cases_view_todo_review_personal_identity_Preview'); ?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><?php admin_language_e('cases_view_todo_review_personal_identity_NotarizedBirthCertificate'); ?></th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="text" id="n2_input_birth_certificate" value="<?php echo $personal_identity_02->birth_certificate_local_file_path?>" class="input-txt" style="width: 340px; margin-left: 0px;">
                                            <button id="n2_button_preview_birth_certificate"><?php admin_language_e('cases_view_todo_review_personal_identity_Preview'); ?></button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php } ?>
                         </div>
                    </td>
                    <td>
                        <table style="width: 50%;">
                            <tr>
                                <th width="100px;">Status <span class="required">*</span></th>
                                <td><select class="input-width" name="status">
                                        <option value="2"><?php admin_language_e('cases_view_todo_review_personal_identity_Completed'); ?></option>
                                        <option value="3"><?php admin_language_e('cases_view_todo_review_personal_identity_Incomplete'); ?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <th><?php admin_language_e('cases_view_todo_review_personal_identity_Comment'); ?> <span class="required">*</span></th>

                                <td><textarea rows="3" cols="65" name="comment_content" class="input-txt" style="height: 100%; background: #FFF;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button id="submitButton"><?php admin_language_e('cases_view_todo_review_personal_identity_Submit'); ?></button>
                            </tr>
                        </table>
                    </td>
                 </tr>
          </table>
        </form>
    </div>
    <div class="ym-clearfix"></div>
</div>
<div class="hide" style="display: none;">
    <a id="n1_link_preview_passport_certificate" class="iframe" href="<?php echo base_url()?>cases/todo/view_personal_identity_document?doc_type=1&director_number=1&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_personal_identity_PreviewPasswordCertificate'); ?></a>
    <a id="n1_link_preview_birth_certificate" class="iframe" href="<?php echo base_url()?>cases/todo/view_personal_identity_document?doc_type=1&director_number=1&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_personal_identity_PreviewBirthDateCertificate'); ?></a>
    <a id="n2_link_preview_passport_certificate" class="iframe" href="<?php echo base_url()?>cases/todo/view_personal_identity_document?doc_type=1&director_number=2&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_personal_identity_PreviewPasswordCertificate'); ?></a>
    <a id="n2_link_preview_birth_certificate" class="iframe" href="<?php echo base_url()?>cases/todo/view_personal_identity_document?doc_type=1&director_number=2&case_id=<?php echo $case_id?>"><?php admin_language_e('cases_view_todo_review_personal_identity_PreviewBirthDateCertificate'); ?></a>
</div>
<script type="text/javascript">
$(document).ready( function() {
    $( "#tabs" ).tabs();
    $('#submitButton').button();
    $('#n1_link_preview_passport_certificate, #n2_link_preview_passport_certificate, #n1_link_preview_birth_certificate, #n2_link_preview_birth_certificate').fancybox({
        width: 1000,
        height: 800
    });

    <?php if (!empty($personal_identity_01->passport_local_file_path)){?>
    $("#n1_button_preview_passport_certificate").click(function(){
        $('#n1_link_preview_passport_certificate').click();
        return false;
    });
    $("#n1_button_preview_birth_certificate").click(function(){
        $('#n1_link_preview_birth_certificate').click();
        return false;
    });
    <?php } ?>

    <?php if (!empty($personal_identity_02->passport_local_file_path)){?>
    $("#n2_button_preview_passport_certificate").click(function(){
        $('#n2_link_preview_passport_certificate').click();
        return false;
    });
    $("#n2_button_preview_birth_certificate").click(function(){
        $('#n2_link_preview_birth_certificate').click();
        return false;
    });
    <?php }?>

    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var submitUrl = $('#personal_identify_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'personal_identify_form',
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