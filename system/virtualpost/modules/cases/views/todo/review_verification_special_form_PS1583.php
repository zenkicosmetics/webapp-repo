<style>
.VR04 #submitButton, #reopenButton {
    padding: .4em 1em;
    color: #fff;
    background: #336699;
    border: 1px solid #569bdb;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    font-style: normal;
    font-size: 14px;
    line-height: 1.4;
}

#backBtn {
    padding: .4em 1em;
    color: #6c6c6c;
    background: #fff;
    border: 1px solid #569bdb;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    font-style: normal;
    font-size: 14px;
    line-height: 1.4;
    margin-left: 4px;
    font-weight: bold;
}

.VR04 .input-btn span {
    text-decoration: underline;
}

.VR04 .ym-grid {
    margin-bottom: 12px !important;
    margin-top: 0px !important;
}

.VR04 a:HOVER {
    text-decoration: none;
}

.VR04 .bd {
    border: 1px solid #a5a5a5;
    padding: 20px !important;
/*    max-height: 460px;
    height: 460px;*/
}

.VR04 .bd-header {
    border-bottom: 1px solid #a5a5a5;
    padding-bottom: 12px !important;
    font-size: 1.2em;
}

.VR04 .description strong {
    margin-right: 10px;
}

input.input-txt {
    margin-left: 0px !important;
}

.VR04 .description {
    margin: 10px auto auto 20px;
}

.VR04 textarea {
    width: 100% !important;
    /*     padding-left: 10px; */
    font-size: 13px !important;
}

.VR04 .input-width {
    width: 100% !important;
}

.ui-tabs, .ui-tabs-panel {
    padding: 0px !important;
}

#mailbox {
    width: 100% !important;
}

.ui-layout-center {
    overflow: visible;
}

.ui-tabs .ui-tabs-nav li {
    width:32%;
    text-align: center;
}
.ui-tabs .ui-tabs-nav li a {
    display: inline-block;
    float: none;
    padding: 5px;
    text-decoration: none;
    width: 100%;
}

.ui-tabs { overflow: hidden; position: relative; padding: .2em; zoom: 1; }

.verification-history .ym-grid {
    margin: 0 !important;
    padding: 2px 0 2px 0;
}

.verification-history .ym-gbox {
    padding: 0;
    padding-left: 5px;
}

</style>
<?php $case_id = $special->case_id; ?>
<div class="header">
    <h2
        style="font-size: 1.3em; font-weight: bold; margin-bottom: 10px;"><?php printf('%1$s > %2$s >Review %3$s', $case_name, $this->router->fetch_class(), $milestone_name) ?></h2>
</div>
<div class="input-form dialog-form VR04">
    <div class="ym-grid">
        <!-- Company Identification -->
        <div class="ym-gl ym-g40">
            <div class="ym-grid">
                <div class="ym-gl ym-g90 bd">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-header"><strong><?php echo $milestone_name;?> <br />
                            <span><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_ApplicationForDeliveryOfMail'); ?></span></strong>
                        </div>
                    </div>
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_NameInWhichApplicantsMailWill'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        value="<?php echo $special->name_to_delivery?>"
                                        disabled />
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_NameOfApplicantBox6'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        value="<?php echo $special->name_of_applicant;?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_AddressOfApplicantBoxes', ['box_name' => '(Boxes 7a, 7b, 7c, 7d)']); ?></div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_NoStreetAptsteNo'); ?></label>
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->street_of_applicant;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">City</div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->city_of_applicant;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_StateRegion'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->region_of_applicant;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_ZIPPostCode'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->postcode_of_applicant;?>"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Country'); ?></label></div>
                                        <div class="ym-gl ym-g60">
                                            <?php
                                                echo my_form_dropdown(array(
                                                    "data" => $countries,
                                                    "value_key" => 'id',
                                                    "label_key" => 'country_name',
                                                    "value" => $special->country_of_applicant,
                                                    "name" => 'country_of_applicant',
                                                    "id" => 'country_of_applicant',
                                                    "clazz" => 'input-text',
                                                    "style" => '',
                                                    "has_empty" => true,
                                                    'html_option'=> 'disabled="disabled"'
                                                ));
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_ApplicantTelephoneNumberinclud'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        value="<?php echo $special->phone_of_applicant;?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_TwoTypesOfIdentificationAreRequired'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        disabled
                                        value="<?php echo $special->id_of_applicant?>">
                                </div>
                                <div class="ym-gl ym-g60">
                                    <div class="description"
                                        style="margin-top: 0px;">
                                        <i><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_WriteDownTheTwoIDsThatYouWill'); ?></i>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile_05" href="#"><strong><?php echo basename($special->id_of_applicant_local_file_path)?></strong>
                                    </a>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        disabled
                                        value="<?php echo $special->license_of_applicant?>">
                                </div>
                                <div class="ym-gl ym-g60">
                                    <div class="description"
                                        style="margin-top: 0px;">
                                        <i><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_AcceptableIdentificationIncludes'); ?></i>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile_06" href="#"><strong><?php echo basename($special->license_of_applicant_local_file_path)?></strong>
                                    </a>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100 bd-header"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_FillOutIfApplicantIsACompany'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_NameOfFirmOrCorporation'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        value="<?php echo $special->name_of_corporation;?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_AddressOfApplicantBoxes', ['box_name' => '(Boxes 10a, 10b, 10c, 10d)']); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_NoStreetAptsteNo'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->street_of_corporation;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_City'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->city_of_corporation;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_StateRegion'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->region_of_corporation;?>"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_ZIPPostCode'); ?></div>
                                        <div class="ym-gl ym-g60">
                                            <input type="text"
                                                class="input-txt"
                                                value="<?php echo $special->postcode_of_corporation;?>"
                                                disabled>
                                        </div>
                                    </div>

                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Country'); ?></label></div>
                                        <div class="ym-gl ym-g60">
                                            <?php
                                                echo my_form_dropdown(array(
                                                        "data" => $countries,
                                                        "value_key" => 'id',
                                                        "label_key" => 'country_name',
                                                        "value" => $special->country_of_corporation,
                                                        "name" => 'country_of_corporation',
                                                        "id" => 'country_of_corporation',
                                                        "clazz" => 'input-text',
                                                        "style" => '',
                                                        "has_empty" => true,
                                                        'html_option'=> 'disabled="disabled"'
                                                ));
                                            ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="ym-gl ym-g40">
                                    <div class="description"
                                        style="margin-top: 0px;">
                                        <i><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_AddYourBusinessContact'); ?> </i>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">Business
                                    Telephone Number (including area
                                    code) (Box 10e)</div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        value="<?php echo $special->phone_of_corporation;?>"
                                        disabled>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100">Type of
                                    Business (Box 11)</div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt"
                                        disabled
                                        value="<?php echo $special->business_type_of_corporation?>">
                                </div>
                                <div class="ym-gl ym-g60">
                                    <div class="description"
                                        style="margin-top: 0px;">
                                        <i><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_IndicateWhatProductsOrServices'); ?>
                                        </i>
                                    </div>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_IfApplicantIsAFirmNameEachMember'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="5" cols="5"
                                        class="input-txt" disabled><?php echo $special->note1?></textarea>

                                </div>
                            </div>
                            <?php if($mailReceivers){?>
                            <div class="ym-grid">
                                <div  class="ym-gl ym-g100">
                                    <table border="0">
                                         <colgroup>
                                            <col width="50%">
                                            <col width="50%">
                                        </colgroup>
                                        <tr>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Name'); ?></th>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_FileName'); ?></th>
                                        </tr>

                                        <?php foreach($mailReceivers as $receiver){?>
                                            <tr>
                                                <th><?php echo $receiver->name ?></th>
                                                <th><a class="view-file" data-id="<?php echo $receiver->id ?>" data-type="mail_receiver" data-case-id ="<?php echo $case_id?>"
                                                       data-op="10" href="#"><strong><?php echo basename($receiver->local_file_path)?></th>
                                            </tr>
                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                             <?php }?>

                            <!--<div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile_07" href="#"><strong><?php // echo basename($special->additional_local_file_path)?></strong>
                                    </a>
                                </div>
                            </div>-->
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_IfACORPORATIONGiveNamesAndAddr'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="5" cols="5"
                                        class="input-txt" disabled><?php echo $special->note2?></textarea>

                                </div>
                            </div>
                             <?php if($officers){?>
                            <div class="ym-grid">
                                <div  class="ym-gl ym-g100">
                                    <table border="0">
                                         <colgroup>
                                            <col width="50%">
                                            <col>
                                            <col>
                                            <col >
                                        </colgroup>
                                        <tr>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Name'); ?></th>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Type'); ?></th>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Rate'); ?>(%)</th>
                                            <th><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_File'); ?></th>
                                        </tr>

                                        <?php foreach($officers as $officer){?>
                                            <tr>
                                                <td><?php echo $officer->name ?></td>
                                                <td><?php echo $officer->type == "1" ? "Owner" : "Officer"; ?></td>
                                                <td><?php echo $officer->rate ?></td>
                                                <td><a class="view-file" data-id="<?php echo $officer->id ? $officer->id: "" ?>" data-type="officer_onwer" data-case-id ="<?php echo $case_id?>"
                                                       data-op="11" href="#" ><strong><?php echo basename($officer->officer_local_path) ?></strong></a></td>
                                            </tr>
                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                             <?php }?>

                            <!---<div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile_09" href="#"><strong><?php // echo basename($special->additional_company_local_file_path)?></strong>
                                    </a>
                                </div>
                            </div>--->

                            <div class="ym-grid" id="mock">
                                <div class="ym-gl ym-g60">If business
                                    name (corporation or trade name) has
                                    been registered, give name of county
                                    and state, and date of registration</div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="5" cols="5"
                                        class="input-txt" disabled><?php echo $special->note3?></textarea>
                                </div>
                            </div>
                            <?php foreach($business_licenses as $b){?>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g80">
                                        <a class="view-file" data-id="<?php echo $b->id ?>" data-type="business_license" data-case-id ="<?php echo $case_id?>"
                                           data-op="12" href="#"><strong><?php echo basename($b->local_file_path)?></strong>
                                        </a>
                                    </div>
                                </div>
                            <?php }?>
                            <!--<div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <a id="viewFile" href="#"><strong><?php // echo basename($special->verification_local_file_path)?></strong>
                                    </a>
                                </div>
                            </div>-->
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- View file -->
        <div class="ym-gl ym-g30" style="width: 35%; margin: 0 10px">
            <div class="ym-grid">
                <div id="tabs" style="height: 100%">
                    <ul class="tab-menu">
                        <li><a href="#verification-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Verification'); ?></span></a></li>
                        <?php if (! empty ( $special->id_of_applicant_local_file_path )) { ?>
                        <li><a href="#id_of_applicant-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_IDOfApplicant'); ?></span></a></li>
                        <?php } if (! empty ( $special->license_of_applicant_local_file_path )) { ?>
                        <li><a href="#license_of_applicant-file-tab"><span><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_LicenseOfApplicant'); ?></span></a></li>
                        <?php }?>
                         <?php if (! empty ( $special->additional_local_file_path )) { ?>
                        <li><a href="#additional-file-tab"><span>Additional</span></a></li>
                        <?php }?>
                         <?php if (! empty ( $special->additional_company_local_file_path )) { ?>
                        <li><a href="#additional-company-file-tab"><span>Additional Company</span></a></li>
                        <?php }?>

                        <?php foreach($mailReceivers as $mailReceiver) { ?>
                          <li><a href="#verification-file-mail-<?php echo $mailReceiver->id?>"><span><?php echo "Verification" .  substr(basename($mailReceiver->local_file_path), -11, -4) ?></span></a></li>
                        <?php } ?>

                        <?php foreach($officers as $officer) { ?>
                           <li><a href="#verification-file-officer-<?php echo $officer->id?>"><span><?php echo "Verification" .  substr(basename($officer->officer_local_path),  -11, -4) ?></span></a></li>
                        <?php } ?>

                         <?php foreach($business_licenses as $b){?>
                            <li><a href="#verification-file-business-licenses-<?php echo $b->id?>"><span><?php echo "Verification" .  substr(basename($b->local_file_path),  -11, -4) ?></span></a></li>
                         <?php } ?>
                    </ul>

                    <iframe id="verification-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url()?>cases/todo/view?type=VR04&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
                    </iframe>

                    <?php if (! empty ( $special->id_of_applicant_local_file_path )) { ?>

                    <iframe id="id_of_applicant-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url()?>cases/todo/view?type=VR04&op=05&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
                    </iframe>

                    <?php } if (! empty ( $special->license_of_applicant_local_file_path )) { ?>

                    <iframe id="license_of_applicant-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url()?>cases/todo/view?type=VR04&op=06&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
                    </iframe>

                    <?php }?>
                    <?php if (! empty ( $special->additional_local_file_path )) { ?>

                    <iframe id="additional-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url()?>cases/todo/view?type=VR04&op=07&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
                    </iframe>

                    <?php }?>

                    <?php if (! empty ( $special->additional_company_local_file_path )) { ?>

                    <iframe id="additional-company-file-tab"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url()?>cases/todo/view?type=VR04&op=09&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
                    </iframe>

                    <?php }?>

                     <?php foreach($mailReceivers as $mailReceiver) { ?>

                    <iframe id="verification-file-mail-<?php echo $mailReceiver->id?>"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $mailReceiver->id?>">
                    </iframe>

                     <?php } ?>

                    <?php foreach($officers as $officer) { ?>

                    <iframe id="verification-file-officer-<?php echo $officer->id?>"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $officer->id?>&type=officer">
                    </iframe>

                     <?php } ?>

                    <?php foreach($business_licenses as $b){?>

                    <iframe id="verification-file-business-licenses-<?php echo $b->id?>"
                        style="width: 100%; position: absolute; height: 100%; border: none"
                        src="<?php echo base_url() ?>cases/todo/view_resource?file_id=<?php echo $b->id?>">
                    </iframe>

                     <?php } ?>
                </div>
            </div>
        </div>
        <!-- Admin verifying -->
        <div class="ym-gl ym-g20" style="margin: 0 10px;">
            <form id="personalVerificationForm" action="#" method="post">
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Status'); ?> <span class="required">*</span>
                    </div>

                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <select class="input-width" name="status"
                            <?php echo $view?'disabled':'';?>>
                            <option
                                <?php echo $special->status == "2"? "selected" : "";?>
                                value="2"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Completed'); ?></option>
                            <option
                                <?php echo $special->status == "3"? "selected" : "";?>
                                value="3"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Incomplete'); ?></option>
                        </select>
                    </div>
                </div>
                <?php if ($special->comment_for_registration_content != '0' ){?>
                 <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65" disabled="disabled"
                                  name="comment_for_registration_content" class="input-txt"
                                  ><?php echo $special->comment_for_registration_content; ?></textarea>
                    </div>
                </div>
                <?php }?>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Comment'); ?> <span class="required">*</span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_LastConfirmed'); ?></label> <span><?php echo APUtils::convert_timestamp_to_date($special->comment_date);?></span>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <textarea rows="3" cols="65"
                            <?php echo $view?'disabled':'';?>
                            name="comment_content" class="input-txt"><?php echo $special->comment_content?></textarea>
                    </div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <input type="button" class="input-btn"
                            style="<?php echo $view?'display:none':'';?>"
                            id="submitButton" value="Submit"><input type="button" class="input-btn"
                            style="<?php echo $view?'':'display:none';?>"
                            id="reopenButton" value="ReOpen"><input
                            type="button" id="backBtn" value="Back">
                    </div>
                </div>
                <input type="hidden" id="case_id" name="case_id"
                    value="<?php echo $special->case_id;?>" />
                <input type="hidden" id="type" name="type"
                    value="<?php echo $type;?>" />
            </form>

            <!-- Verification history -->
            <?php include ("system/virtualpost/modules/cases/views/todo/verification_history.php"); ?>
            <!-- End Verification history -->

            <div class="info-content">
                <!-- invoice address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_InvoicingAddress'); ?></h3>
                    </div>
                </div>
                <div class="ym-grid">
                    <table border="0" style="border: 0px; margin:0px">
                        <tr>
                            <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Name'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_address_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Company'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_company : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Street'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_street : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PostCode'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_postcode : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_City'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_city : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Region'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_region : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Country'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_country_name : ""; ?></td>
                        </tr>
                        <tr>
                            <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PhoneNo'); ?> </label></th>
                            <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_phone_number : ""; ?></td>
                        </tr>

                    </table>
                </div>

                <!-- postbox address -->
                <div class="ym-grid">
                    <div class="ym-gl ym-g100">
                        <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Postbox'); ?></h3>
                    </div>
                </div>
                <?php foreach ($postboxes as $select_postbox): ?>
                    <div class="ym-grid">
                        <table border="0" style="border: 0px; margin:0px">
                            <tr>
                                <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PostboxID'); ?></label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_code; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Type'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->postbox_type; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Name'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->name; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Company'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->company; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_Location'); ?> </label></th>
                                <td style="padding: 0px"><?php echo $select_postbox->location_name; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <div class="hide" style="display: none;">
        <a id="view_verification_file" class="iframe"
            href="<?php echo base_url()?>cases/todo/view?type=VR04&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
            <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PreviewFile'); ?></a> <a id="view_verification_file_05"
            class="iframe"
            href="<?php echo base_url()?>cases/todo/view?type=VR04&op=05&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
            <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PreviewFile'); ?></a> <a id="view_verification_file_06"
            class="iframe"
            href="<?php echo base_url()?>cases/todo/view?type=VR04&op=06&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
            <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PreviewFile'); ?></a> <a id="view_verification_file_07"
            class="iframe"
            href="<?php echo base_url()?>cases/todo/view?type=VR04&op=07&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
            <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PreviewFile'); ?></a>

            <a id="view_verification_file_09"
            class="iframe"
            href="<?php echo base_url()?>cases/todo/view?type=VR04&op=09&case_id=<?php echo $special->case_id?>&t=<?php echo time()?>">
            <?php admin_language_e('cases_view_todo_review_verification_special_form_PS1583_PreviewFile'); ?></a>

            <a href="#" class="iframe" id="view_verification_file_10"></a>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $(".bd-content").slimScroll({height:($(window).height() - 330)+'px'});
    $(".info-content").slimScroll({height:($(window).height() - 480)+'px'});
    $( "#tabs" ).attr("style",'height:'+($(window).height() - $( "#tabs" ).offset().top - 70)+'px');
    $( "#tabs" ).tabs();

    $("#submitButton").click(function(){
        var submitUrl = '<?php echo base_url()?>cases/todo/review_verification_special_form_PS1583';
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'personalVerificationForm',
            success: function(data) {
                if (data.status) {
                    $.infor({message:"Submit data successfull.",ok:function(){
                        document.location.href = '<?php echo base_url()?>cases/todo';
                    }});
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return ;
    });

    $('a[id^="view_verification_file"]').fancybox({
        width: 1000,
        height: 800
    });

    $("#backBtn").click(function(){
        window.history.back();
    });

    $("#viewFile").click(function(){
        $('#view_verification_file').click();
        return false;
    });
    $("#viewFile_05").click(function(){
        $('#view_verification_file_05').click();
        return false;
    });
    $("#viewFile_06").click(function(){
        $('#view_verification_file_06').click();
        return false;
    });
    $("#viewFile_07").click(function(){
        $('#view_verification_file_07').click();
        return false;
    });

    $("#viewFile_09").click(function(){
        $('#view_verification_file_09').click();
        return false;
    });

    $(".view-file").live("click", function(e){
        e.preventDefault();

        var id = $(this).data("id");
        var type = $(this).data("type");
        var case_id = $(this).data("case-id");
        var time = $.now();
        var op = $(this).data("op");
        var url = "<?php echo base_url()?>cases/todo/";
        if(type == "mail_receiver" || type == 'business_license'){
            url += "view_resource?file_id="+id+"&type="+type+"&case_id="+case_id+"&t="+time+"&op="+op;
        }else{
            url += "view?id="+id+"&type="+type+"&case_id="+case_id+"&t="+time+"&op="+op;
        }
        $("#view_verification_file_10").attr("href", url);
        $("#view_verification_file_10").click();

        return false;
    });


    $("#reopenButton").click(function(){
        $("textarea[name='comment_content']").removeAttr("disabled");
        $("select[name='status']").removeAttr("disabled");
        $("#submitButton").attr("style","display:show");
        $(this).attr("style","display:none");
   });
});
</script>