<div class="ym-grid content services" id="case-body-wrapper">
    <div id="go-back"><span><a id="backButton" href="#" ><?php language_e('cases_view_bankaccount_personal_identity_form_Back'); ?></a></span></div>
    <div class="ym-clearfix"></div>
    <div class="header">
        <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_bankaccount_personal_identity_form_PleaseFillOutAllRequiredInform'); ?>
        </h2>
    </div>
    <div class="ym-clearfix"></div>
    <div class="ym-grid">
        <form id="personal_identify_form" action="<?php echo base_url()?>cases/bankaccount/personal_identify" method="post">
            <input type="hidden" id="personal_identify_form_case_id" name="case_id" value="<?php echo $case_id;?>" />
            <table>
                <tr>
                    <th style="font-size: 15px"><?php language_e('cases_view_bankaccount_personal_identity_form_IdentityInformationOfDirector'); ?>
                    </th>
                    <th style="font-size: 15px"><?php language_e('cases_view_bankaccount_personal_identity_form_OptionalSecondDirector'); ?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <table style="width: 450px;">
                            <tr>
                                <th width="100px;"><?php language_e('cases_view_bankaccount_personal_identity_form_FirstName'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="n1_first_name" value="<?php echo $personal_identity_01->first_name?>" class="input-txt" required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_MiddleName'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="n1_middle_name" value="<?php echo $personal_identity_01->middle_name?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_LastName'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="n1_last_name" value="<?php echo $personal_identity_01->last_name?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_StreetAddress'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="n1_street_address" value="<?php echo $personal_identity_01->street_address?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PostCode'); ?> <span class="required">*</span></th>
                                <td><input type="text" name="n1_post_code" value="<?php echo $personal_identity_01->post_code?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_City'); ?></th>
                                <td><input type="text" name="n1_city" value="<?php echo $personal_identity_01->city?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_Region'); ?></th>
                                <td><input type="text" name="n1_region" value="<?php echo $personal_identity_01->region?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_Country'); ?> <span class="required">*</span></th>
                                <td><select name="n1_country" class="input-txt">
                                    <?php foreach ( $countries as $country ) :?>
                                    <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                    <?php endforeach;?>
                                 </select></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_DateOfBirth'); ?></th>
                                <td><input type="text" name="n1_date_of_birth" value="<?php echo $personal_identity_01->date_of_birth?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PlaceOfBirth'); ?></th>
                                <td><input type="text" name="n1_place_of_birth" value="<?php echo $personal_identity_01->place_of_birth?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_CountryOfBirth'); ?></th>
                                <td><select class="input-txt" name="n1_country_of_birth">
                                        <?php foreach ( $countries as $country ) :?>
                                        <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country_of_birth == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                        <?php endforeach;?>
                                </select></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PhoneNumber'); ?></th>
                                <td><input type="text" name="n1_phone_number" value="<?php echo $personal_identity_01->phone_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_MobileNumber'); ?></th>
                                <td><input type="text" name="n1_mobile_number" value="<?php echo $personal_identity_01->mobile_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_EmailAddress'); ?></th>
                                <td><input type="text" name="n1_email_address" value="<?php echo $personal_identity_01->email_address?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PassportNumber'); ?></th>
                                <td><input type="text" name="n1_passport_number" value="<?php echo $personal_identity_01->passport_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th colspan="2"><?php language_e('cases_view_bankaccount_personal_identity_form_NotarizedIdentificationDocumen'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="file" id="n1_passport_certificate" name="n1_passport_certificate" style="display: none;">
                                    <input type="text" id="n1_input_passport_certificate" class="input-txt" style="width: 340px; margin-left: 0px;"> <button id="n1_button_passport_certificate"><?php language_e('cases_view_bankaccount_personal_identity_form_Upload'); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2"><?php language_e('cases_view_bankaccount_personal_identity_form_NotarizedBirthCertificate'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="file" id="n1_birth_certificate" name="n1_birth_certificate" style="display: none;">
                                    <input type="text" id="n1_input_birth_certificate" class="input-txt" style="width: 340px; margin-left: 0px;"> <button id="n1_button_birth_certificate"><?php language_e('cases_view_bankaccount_personal_identity_form_Upload'); ?></button>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Second director -->
                    <td>
                        <table style="width: 450px;">
                            <tr>
                                <th width="100px;"><?php language_e('cases_view_bankaccount_personal_identity_form_FirstName'); ?> </th>
                                <td><input type="text" name="n2_first_name" value="<?php echo $personal_identity_02->first_name?>" class="input-txt" required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_MiddleName'); ?> </th>
                                <td><input type="text" name="n2_middle_name" value="<?php echo $personal_identity_02->middle_name?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_LastName'); ?> </th>
                                <td><input type="text" name="n2_last_name" value="<?php echo $personal_identity_02->last_name?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_StreetAddress'); ?> </th>
                                <td><input type="text" name="n2_street_address" value="<?php echo $personal_identity_02->street_address?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PostCode'); ?> </th>
                                <td><input type="text" name="n2_post_code" value="<?php echo $personal_identity_02->post_code?>" class="input-txt"  required="required" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_City'); ?></th>
                                <td><input type="text" name="n2_city" value="<?php echo $personal_identity_02->city?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_Region'); ?></th>
                                <td><input type="text" name="n2_region" value="<?php echo $personal_identity_02->region?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_Country'); ?> </th>
                                <td><select name="n2_country" class="input-txt">
                                    <?php foreach ( $countries as $country ) :?>
                                    <option value="<?php echo $country->id?>" <?php if ($personal_identity_02->country == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                    <?php endforeach;?>
                                 </select></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_DateOfBirth'); ?></th>
                                <td><input type="text" name="n2_date_of_birth" value="<?php echo $personal_identity_02->date_of_birth?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PlaceOfBirth'); ?></th>
                                <td><input type="text" name="n2_place_of_birth" value="<?php echo $personal_identity_02->place_of_birth?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_CountryOfBirth'); ?></th>
                                <td><select class="input-txt" name="n2_country_of_birth">
                                    <?php foreach ( $countries as $country ) :?>
                                    <option value="<?php echo $country->id?>" <?php if ($personal_identity_01->country_of_birth == $country->id) { ?> selected="selected" <?php }?>> <?php echo $country->country_name?></option>
                                    <?php endforeach;?>
                                </select></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PhoneNumber'); ?></th>
                                <td><input type="text" name="n2_phone_number" value="<?php echo $personal_identity_02->phone_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_MobileNumber'); ?></th>
                                <td><input type="text" name="n2_mobile_number" value="<?php echo $personal_identity_02->mobile_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_EmailAddress'); ?></th>
                                <td><input type="text" name="n2_email_address" value="<?php echo $personal_identity_02->email_address?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th ><?php language_e('cases_view_bankaccount_personal_identity_form_PassportNumber'); ?></th>
                                <td><input type="text" name="n2_passport_number" value="<?php echo $personal_identity_02->passport_number?>" class="input-txt" /></td>
                            </tr>
                            <tr>
                                <th colspan="2"><?php language_e('cases_view_bankaccount_personal_identity_form_NotarizedIdentificationDocumen'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="file" id="n2_passport_certificate" name="n2_passport_certificate" style="display: none;">
                                    <input type="text" id="n2_input_passport_certificate" class="input-txt" style="width: 340px; margin-left: 0px;"> <button id="n2_button_passport_certificate"><?php language_e('cases_view_bankaccount_personal_identity_form_Upload'); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2"><?php language_e('cases_view_bankaccount_personal_identity_form_NotarizedBirthCertificate'); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="file" id="n2_birth_certificate" name="n2_birth_certificate" style="display: none;">
                                    <input type="text" id="n2_input_birth_certificate" class="input-txt" style="width: 340px; margin-left: 0px;"> <button id="n2_button_birth_certificate"><?php language_e('cases_view_bankaccount_personal_identity_form_Upload'); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;">
                                    <button id="submitButton" class="input-btn"><?php language_e('cases_view_bankaccount_personal_identity_form_NextStep'); ?></button>
                                </td>
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
    var assetPath = '<?php echo APContext::getAssetPath()?>';
    $("#n1_button_passport_certificate").button().click(function() {
        $('#n1_passport_certificate').val('');
        $('#n1_passport_certificate').click();
        return false;
    });
    $("#n1_passport_certificate").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }
        var case_id = $('#personal_identify_form_case_id').val();
        // Upload data here
        $.ajaxFileUpload({
            id: 'n1_passport_certificate',
            data: {
                doc_type: '1',
                case_id: $('#personal_identify_form_case_id').val(),
                director_number: '1'
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_personal_identity_document?doc_type=1&director_number=1&case_id=' + case_id,
            success: function(data) {
                $('#n1_input_passport_certificate').val($("#n1_passport_certificate").val());
            }
        });
    });

    $("#n1_button_birth_certificate").button().click(function() {
        $('#n1_birth_certificate').val('');
        $('#n1_birth_certificate').click();
        return false;
    });
    $("#n1_birth_certificate").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }
        var case_id = $('#personal_identify_form_case_id').val();
        // Upload data here
        $.ajaxFileUpload({
            id: 'n1_birth_certificate',
            data: {
                doc_type: '2',
                case_id: $('#personal_identify_form_case_id').val(),
                director_number: '1'
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_personal_identity_document?doc_type=2&director_number=1&case_id=' + case_id,
            success: function(data) {
                $('#n1_input_birth_certificate').val($("#n1_birth_certificate").val());
            }
        });
    });

    $("#n2_button_passport_certificate").button().click(function() {
        $('#n2_passport_certificate').val('');
        $('#n2_passport_certificate').click();
        return false;
    });
    $("#n2_passport_certificate").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }
        var case_id = $('#personal_identify_form_case_id').val();
        // Upload data here
        $.ajaxFileUpload({
            id: 'n2_passport_certificate',
            data: {
                doc_type: '1',
                case_id: $('#personal_identify_form_case_id').val(),
                director_number: '2'
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_personal_identity_document?doc_type=1&director_number=2&case_id=' + case_id,
            success: function(data) {
                $('#n2_input_passport_certificate').val($("#n2_passport_certificate").val());
            }
        });
    });

    $("#n2_button_birth_certificate").button().click(function() {
        $('#n2_birth_certificate').val('');
        $('#n2_birth_certificate').click();
        return false;
    });
    $("#n2_birth_certificate").change(function (){
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if(ext.toUpperCase() != "PDF"){
           $('#container').css('visibility', 'hidden');
           $.displayError('Please select pdf file to upload.', null, function() {
              $('#container').css('visibility', '');
               });
            return;
        }
        var case_id = $('#personal_identify_form_case_id').val();
        // Upload data here
        $.ajaxFileUpload({
            id: 'n2_birth_certificate',
            data: {
                doc_type: '2',
                case_id: $('#personal_identify_form_case_id').val(),
                director_number: '2'
            },
            url: '<?php echo base_url()?>cases/bankaccount/upload_personal_identity_document?doc_type=2&director_number=2&case_id=' + case_id,
            success: function(data) {
                $('#n2_input_birth_certificate').val($("#n2_birth_certificate").val());
            }
        });
    });

    /**
     * When user click submit button
     */
    $('#submitButton').click(function(){
        var case_id = $('#personal_identify_form_case_id').val();
        var submitUrl = $('#personal_identify_form').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'personal_identify_form',
            success: function(data) {
                if (data.status) {
                    document.location.href = '<?php echo base_url()?>cases/bankaccount/company_information?case_id=' + case_id;
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });
});
</script>