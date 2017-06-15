<style>
.xx .input-btn {
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
    width: 300px;
    line-height: 1.4;
    margin-right: 4px;
}

.backBtn {
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

.xx .input-btn span {
    text-decoration: underline;
}

.xx .ym-grid {
    margin-bottom: 12px !important;
    margin-top: 0px !important;
}

.xx a:HOVER {
    text-decoration: none;
}

.xx .bd {
    border: 1px solid #a5a5a5;
    padding: 20px !important;
    /*     max-height: 460px; */
    /*     height: 460px; */
}

.xx .bd-header {
    border-bottom: 1px solid #a5a5a5;
    padding-bottom: 12px !important;
    font-size: 1.2em !important;
    font-weight: bold;
}

.xx .bd-header a {
    font-size: 14px !important;
}

.xx .bd-content {
    /*     max-height: 420px; */
    /*     height: 420px; */
    /*     overflow-y: auto; */

}

.xx .description strong {
    margin-right: 10px;
}

.ym-g30 {
    width: 30%;
}

.ym-g70 {
    width: 70%;
}

input.input-txt {
    margin-left: 0px !important;
}

textarea.input-txt {
    background: #fff;
    border: 1px solid #DADADA;
    border-radius: 3px 3px 3px 3px;
    font-size: 13px;
    height: 120px;
    margin-left: 0px;
    text-indent: 5px;
    width: 98%;
    line-height: 25px;
    padding: 10px;
}

#fancybox-outer, #fancybox-content {
    border-radius: 5px !important;
}

.input-error {
    border: 1px #800 solid !important;
    color: #800;
}

.upload-success {
    color: #fff;
    background: #336699;
}
.gray-btn{
    background: #d3d3d3 none repeat scroll 0 0 !important;
    padding: .4em 1em;
    color: #6c6c6c;
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
.gray-btn:hover{
    background: #d3d3d3 none repeat scroll 0 0 !important;
}
input.input-txt{
    background: #fff;
}
select.input-txt{
    background: #fff;
}
</style>

<div class="ym-grid content" id="case-body-wrapper">
    <div class="cloud-body-wrapper xx">
        <div class="ym-grid">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_special_VerificationsRequired'); ?>:</h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80 bd">
                <div class="ym-grid">
                    <div class="bd-header">
                        <?php echo ($type == 'general') ? '':$milestone_name;?> <br />
                        <span><?php language_e('cases_view_verification_special_ApplicationForDeliveryOfMailTh'); ?></span>
                            <!--- See Privacy Act Statement<span >
                            id="below">below</span><a href="#hereModal"
                            id="here">here</a-->
                    </div>
                </div>
                <form id="special_verification_form" method="post">
                    <div class="ym-grid">
                        <div class="ym-gl ym-g100 bd-content">
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_NameInWhichApplicantsMailWillB'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <!-- style="background:#eee;" disabled="disabled" -->
                                    <input style="background:#eee;" type="text" class="input-txt" readonly
                                           value="<?php echo $verify_postbox->name . ($verify_postbox->name && $verify_postbox->company ? ', ' : "") . $verify_postbox->company; ?>"
                                           name="name_to_delivery" class="tipsy_tooltip"
                                           original-title="<?php language_e('cases_view_verification_special_ChangeNamePostboxTooltip'); ?>" />
                                </div>
                                <label style="margin-left: 0px; position: relative;"><?php language_e('cases_view_verification_special_TheIDsRequiredBelowMustMatchTh'); ?></label>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_NameOfApplicantBox6'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <?php
                                    $name_of_appicant = "";
                                    if(empty($customer_addresses)){
                                        $customer_addresses = new stdClass();
                                        $customer_addresses->invoicing_company = "";
                                        $customer_addresses->invoicing_street = "";
                                        $customer_addresses->invoicing_city = "";
                                        $customer_addresses->invoicing_region = "";
                                        $customer_addresses->invoicing_postcode = "";
                                    }

                                    if ($cases_verification) {
                                        $name_of_appicant = $cases_verification->name_of_applicant;
                                    } else if (!empty($customer_addresses->invoicing_address_name)) {
                                        $name_of_appicant = $customer_addresses->invoicing_address_name;
                                    } else {
                                        $name_of_appicant = $customer_addresses->invoicing_company;
                                    }
                                    ?>
                                    <input type="text" class="input-txt" value="<?php echo $name_of_appicant; ?>" name="name_of_applicant" />
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_AddressOfApplicantBoxes7a7b7c7'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px"><?php language_e('cases_view_verification_special_NoStreetAptsteNo'); ?></label></div>
                                        <div class="ym-gl ym-g70">
                                            <input type="text" class="input-txt" name="street_of_applicant"
                                                   value="<?php echo $cases_verification ? $cases_verification->street_of_applicant : $customer_addresses->invoicing_street; ?>" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px">City</label></div>
                                        <div class="ym-gl ym-g70">
                                            <input type="text" name="city_of_applicant" class="input-txt"
                                                   value="<?php echo $cases_verification ? $cases_verification->city_of_applicant : $customer_addresses->invoicing_city; ?>" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px">State/Region</label></div>
                                        <div class="ym-gl ym-g70">
                                            <input type="text" class="input-txt" name="region_of_applicant"
                                                   value="<?php echo $cases_verification ? $cases_verification->region_of_applicant : $customer_addresses->invoicing_region; ?>" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px">ZIP/Post Code</label></div>
                                        <div class="ym-gl ym-g70">
                                            <input type="text" class="input-txt" name="postcode_of_applicant"
                                                   value="<?php echo $cases_verification ? $cases_verification->postcode_of_applicant : $customer_addresses->invoicing_postcode; ?>" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30">Country</div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            echo my_form_dropdown(array(
                                                "data" => $countries,
                                                "value_key" => 'id',
                                                "label_key" => 'country_name',
                                                "value" => $cases_verification ? $cases_verification->country_of_applicant : $customer_addresses->invoicing_country,
                                                "name" => 'country_of_applicant',
                                                "id" => 'country_of_applicant',
                                                "clazz" => 'input-txt',
                                                "style" => 'margin-left:0',
                                                "has_empty" => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_ApplicantTelephoneNumberinclud'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt" name="phone_of_applicant"
                                           value="<?php echo $cases_verification ? $cases_verification->phone_of_applicant : $customer_addresses->invoicing_phone_number; ?>" />
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40"><?php language_e('cases_view_verification_special_TwoTypesOfIdentificationAreReq'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g70">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="text" class="input-txt" placeholder="<?php language_e('cases_view_verification_special_LicenseApplicantPlaceHolder'); ?>" name="id_of_applicant"
                                                   value="<?php echo $cases_verification ? $cases_verification->id_of_applicant : ""; ?>"/>
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <div class="description" style="margin-top: 0px; margin-left: 15px;">
                                                <i><?php language_e('cases_view_verification_special_WriteDownTheTypeOfFirstIdentif'); ?></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="file" id="id_of_applicant_verification_file" name="id_of_applicant_verification" style="display: none" />
                                            <input type="text"  class="input-txt" id="id_of_applicant_verification_txt" name="id_of_applicant_verification_txt" readonly
                                                   value="<?php echo empty($cases_verification) ? "" : basename($cases_verification->id_of_applicant_local_file_path) ?>" />
                                            <input type="hidden" value="0" class="input-txt" id="id_of_applicant_verification_change" name="id_of_applicant_verification_change" />
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <button id="id_of_applicant_verification_btn">Upload</button>
                                            <button id="id_of_applicant_view_btn" class="<?php echo empty($cases_verification) || empty($cases_verification->id_of_applicant_local_file_path) ? "" : "upload-success" ?>">View</button>
                                        </div>
                                    </div>
                                    <div class="ym-grid" style="height: 20px;"></div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="text" class="input-txt" placeholder="<?php language_e('cases_view_verification_special_LicenseApplicantPlaceHolder'); ?>" name="license_of_applicant" 
                                                   value="<?php echo $cases_verification ? $cases_verification->license_of_applicant : ""; ?>"/>
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <div class="description" style="margin-top: 0px; margin-left: 15px;">
                                                <i><?php language_e('cases_view_verification_special_WriteDownTheTypeOfSecondIdenti'); ?></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="file" id="license_of_applicant_verification_file" name="license_of_applicant_verification" style="display: none" />
                                            <input type="text" class="input-txt" id="license_of_applicant_verification_txt" name="license_of_applicant_verification_txt" readonly
                                                   value="<?php echo empty($cases_verification) ? "" : basename($cases_verification->license_of_applicant_local_file_path) ?>" />
                                            <input type="hidden" value="0" class="input-txt"
                                                   id="license_of_applicant_verification_change" name="license_of_applicant_verification_change" />
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <button id="license_of_applicant_verification_btn">Upload</button>
                                            <button id="license_of_applicant_view_btn" class="<?php echo empty($cases_verification) || empty($cases_verification->license_of_applicant_local_file_path) ? "" : "upload-success" ?>">View</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="ym-gl ym-g30">
                                    <div style="margin-left: 10px;margin-top: 40px">
                                        <i><?php language_e('cases_view_verification_special_AcceptableIdentificationInclud'); ?></i>
                                    </div>
                                </div>
                            </div>

                            <div class="ym-grid">
                                <div class="ym-gl ym-g80">
                                    <?php language_e('cases_view_verification_special_IHerebyGiveAuthorizationToRece'); ?><select
                                        name="xx" class="input-text"
                                        style="width: 55px"><option
                                            value="NO"
                                            selected="selected">NO</option>
                                        <option value="YES">YES</option></select>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80 bd-header"><?php language_e('cases_view_verification_special_FillOutIfApplicantIsACompany'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_NameOfFirmOrCorporation'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input style="background:#eee;" type="text" class="input-txt" readonly name="name_of_corporation" class="tipsy_tooltip"
                                           original-title="<?php language_e('cases_view_verification_special_ChangeNamePostboxTooltip'); ?>"
                                           value="<?php echo (!empty($verify_postbox->company)) ? $verify_postbox->company : ''; ?>" />
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_AddressOfCompanyBoxes10a10b10c'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px"><?php language_e('cases_view_verification_special_NoStreetAptsteNo'); ?></label></div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            $street_of_corporation = "";
                                            if ($cases_verification) {
                                                $street_of_corporation = $cases_verification->street_of_corporation;
                                            } elseif (!empty($postbox->company)) {
                                                $street_of_corporation = $customer_addresses->invoicing_street;
                                            }
                                            ?>
                                            <input type="text" class="input-txt" value="<?php echo $street_of_corporation; ?>" name="street_of_corporation" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px"><?php language_e('cases_view_verification_special_City'); ?></label></div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            $city_of_corporation = "";
                                            if ($cases_verification) {
                                                $city_of_corporation = $cases_verification->city_of_corporation;
                                            } elseif (!empty($postbox->company)) {
                                                $city_of_corporation = $customer_addresses->invoicing_city;
                                            }
                                            ?>
                                            <input type="text" class="input-txt" value="<?php echo $city_of_corporation; ?>" name="city_of_corporation" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px"><?php language_e('cases_view_verification_special_StateRegion'); ?></label></div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            $region_of_corporation = "";
                                            if ($cases_verification) {
                                                $region_of_corporation = $cases_verification->region_of_corporation;
                                            } else if (!empty($postbox->company)) {
                                                $region_of_corporation = $customer_addresses->invoicing_region;
                                            }
                                            ?>
                                            <input type="text" class="input-txt" value="<?php echo $region_of_corporation; ?>" name="region_of_corporation" />
                                        </div>
                                    </div>
                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><label style="float:right;padding-right:10px"><?php language_e('cases_view_verification_special_ZipPostCode'); ?></label></div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            $postcode_of_corporation = "";
                                            if ($cases_verification) {
                                                $postcode_of_corporation = $cases_verification->postcode_of_corporation;
                                            } elseif (!empty($postbox->company)) {
                                                $postcode_of_corporation = $customer_addresses->invoicing_postcode;
                                            }
                                            ?>
                                            <input type="text" class="input-txt" value="<?php echo $postcode_of_corporation; ?>" name="postcode_of_corporation" />
                                        </div>
                                    </div>

                                    <div class="ym-grid">
                                        <div class="ym-gl ym-g30"><?php language_e('cases_view_verification_special_Country'); ?></div>
                                        <div class="ym-gl ym-g70">
                                            <?php
                                            echo my_form_dropdown(array(
                                                "data" => $countries,
                                                "value_key" => 'id',
                                                "label_key" => 'country_name',
                                                "value" => $cases_verification ? $cases_verification->country_of_corporation : $customer_addresses->invoicing_country,
                                                "name" => 'country_of_corporation',
                                                "id" => 'country_of_corporation',
                                                "clazz" => 'input-txt',
                                                "style" => 'margin-left:0',
                                                "has_empty" => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="ym-gl ym-g40">
                                    <div class="description" style="margin-top: 0px;">
                                    <?php
                                        $type_name = $type == 'general' ? "General CMRA" : "USPS";
                                    ?>
                                        <i><?php language_e('cases_view_verification_special_AddYourBusinessContactInformat'); ?> </i>
                                    </div>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_BusinessTelephoneNumberincludi'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <?php
                                    $invoicing_phone_number = "";
                                    if ($cases_verification) {
                                        $invoicing_phone_number = $cases_verification->phone_of_corporation;
                                    } else if (!empty($postbox->company)) {
                                        $invoicing_phone_number = $customer_addresses->invoicing_phone_number;
                                    }
                                    ?>
                                    <input type="text" class="input-txt" name="phone_of_corporation" value="<?php echo $invoicing_phone_number ?>" />
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"><?php language_e('cases_view_verification_special_TypeOfBusinessBox11'); ?></div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="text" class="input-txt" name="business_type_of_corporation" id="business_type_of_corporation" 
                                           value="<?php echo $cases_verification ? $cases_verification->business_type_of_corporation : ""; ?>" />
                                </div>
                                <div class="ym-gl ym-g60">
                                    <div class="description" style="margin-top: 0px;">
                                        <i><?php language_e('cases_view_verification_special_IndicateWhatProductsOrServices'); ?></i>
                                    </div>
                                </div>
                            </div>

                             <!-- box 12-->
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_special_IfApplicantIsAFirmNameEachMemb'); ?></div>
                            </div>
<!--                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">-->
                                    <?php
//                                    $note1 = "";
//                                    if ($cases_verification) {
//                                        $note1 = $cases_verification->note1;
//                                    } elseif (!empty($postbox->name)) {
//                                        $note1 = $postbox->name;
//                                    }
                                    ?>
<!--                                    <textarea rows="5" cols="5" class="input-txt" name="note1"><?php //echo $note1; ?></textarea>
                                </div>
                            </div>-->
                            <!--<div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="file" id="additional_verification_file" name="additional_verification" style="display: none">
                                    <input type="text" id="additional_verification_txt" class="input-txt" readonly>
                                    <input type="hidden" id="additional_verification_change" name="additional_verification_change" value="0" class="input-txt">
                                </div>
                                <div class="ym-gl ym-g60">
                                    <button id="additional_verification_btn">Upload</button>
                                    <button id="additional_delete_btn" style="display: none;">Delete</button>
                                    <button id="additional_view_btn" class="<?php //echo empty($cases_verification) || empty($cases_verification->additional_local_file_path) ? "" : "upload-success" ?>">View</button>
                                </div>
                            </div>--->

                            <div id="divMailReceiverContainer">
                                <?php
                                $total_count = 4;
                                if(count($mailReceivers) >= $total_count){
                                    $total_count = count($mailReceivers);
                                    $mail_receivers = $mailReceivers;
                                }else{
                                    $mail_receivers = array();
                                    foreach ($mailReceivers as $obj){
                                        $mail_receivers[] = $obj;
                                    }
                                    for($i = count($mailReceivers); $i<$total_count; $i++){
                                        $mail_receivers[] = "";
                                    }
                                }
//                                $index = 0;
                                ?>
                                <?php
                                foreach($mail_receivers as $mr){ // $index ++;
                                ?>
                                <div class="ym-grid">
                                        <div class="ym-gl ym-g60">
                                            Name: <input type="text" name="mail_receiver_name[]" class="input-txt mail_receiver_name" style="width:310px"
                                                         value="<?php echo isset($mr->name) ? $mr->name : ""; ?>" maxlength="100" />
                                            <input type="text" class="input-txt input-file-name"  name="input_file_name[]" readonly style="width:120px;border-color:#a8a8a8;"
                                                   value="<?php echo $mr ? basename($mr->local_file_path) : ""; ?>" maxlength="100" />
                                            <input type="hidden" name="mail_receiver_id[]" value="<?php echo isset($mr->id) ? $mr->id : ""; ?>"
                                                   class="input-txt input-file-id" style="width:50px" />
                                        </div>
                                        <div class="ym-gl ym-g40">
                                            <button class="upload-button" data-op="mail_receiver" data-old-data="<?php echo $mr ? "1" : ""; ?>" data-id="<?php echo $mr ? $mr->id : "" ?>"
                                                    type="button">Upload</button>
                                            <button type="button" data-op="mail_receiver" data-id="<?php echo $mr ? $mr->id : "" ?>"
                                                    class="<?php echo $mr ? "upload-success" : "" ?> view-pdf">View</button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $mr ? $mr->id : "" ?>" data-op="mail_receiver">Delete</button>
                                            <input type="hidden" name="mail_receiver_ids[]" value="<?php echo $mr ? $mr->id : "" ?>" class="input-txt input-file-id mail-receiver-file-id" style="width:50px" />
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g75"><a class="ym-gr" href="#"  class="main_link_color" id="addMailReceiver"><?php language_e('cases_view_verification_special_AddMailReceiver'); ?></a></div>
                            </div>
                            <br />

                            <!-- box 13-->
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_special_IfACORPORATIONGiveNamesAndAddr'); ?></div>
                            </div>
<!--                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="5" cols="5" class="input-txt" name="note2"><?php //echo $cases_verification ?$cases_verification->note2 :""; ?></textarea>
                                </div>
                            </div>-->
<!--                            <div class="ym-grid">
                                <div class="ym-gl ym-g40">
                                    <input type="file" id="additional_company_verification_file" name="additional_company_verification" style="display: none">
                                    <input type="text" id="additional_company_verification_txt" class="input-txt" readonly>
                                    <input type="hidden" id="additional_company_verification_change" class="input-txt" name="additional_company_verification_change" value="0">
                                </div>
                                <div class="ym-gl ym-g60">
                                    <button id="additional_company_verification_btn">Upload</button>
                                    <button id="additional_company_delete_btn" style="display: none;">Delete</button>
                                    <button id="additional_company_view_btn" class="<?php //echo empty($cases_verification) || empty($cases_verification->additional_company_local_file_path) ? "" : "upload-success" ?>">View</button>
                                </div>
                            </div>-->

                            <!-- officer or owner -->
                            <div class="ym-grid">
                                <div class="ym-gl ym-g100"><?php language_e('cases_view_verification_special_IfACORPORATIONGiveNamesAndAddresses'); ?></div>
                            </div>
                            <div id="divOfficerContainer">
                                <?php
                                $total_count = 4;
                                if(count($officers) >= $total_count){
                                    $total_count = count($officers);
                                    $officer_owners = $officers;
                                }else{
                                    $officer_owners = array();
                                    foreach ($officers as $obj){
                                        $officer_owners[] = $obj;
                                    }
                                    for($i = count($officers); $i<$total_count; $i++){
                                        $officer_owners[] = "";
                                    }
                                }
                                ?>
                                <?php foreach($officer_owners as $office){ ?>
                                <div class="ym-grid">
                                        <div class="ym-gl ym-g62">
                                            Name: <input type="text" name="officer_name[]" class="input-txt office-name" style="width:200px"
                                                         value="<?php echo isset($office->name) ? $office->name : ""; ?>" maxlength="100" />
                                                         <?php
                                                         echo code_master_form_dropdown(array(
                                                             "code" => APConstants::CASE_VERIFICATION_USPS_OFFICER_OWNER,
                                                             "value" => isset($office->type) ? $office->type : "",
                                                             "name" => 'officer_type[]',
                                                             "id" => '',
                                                             "clazz" => 'input-width office-type',
                                                             "style" => 'width: 50px',
                                                             "has_empty" => true
                                                         ));
                                                         ?>
                                            <input type="text" name="officer_rate[]" value="<?php echo isset($office->rate) ? $office->rate : ""; ?>"
                                                   class="input-txt office-rate" style="width:50px" /> %
                                            <input type="hidden" name="officer_file_id[]" value="<?php echo isset($office->id) ? $office->id : ""; ?>"
                                                   class="input-txt input-file-id" />
                                            <input type="text"  class="input-txt input-file-name" style="width:120px;border-color:#a8a8a8" readonly
                                                   value="<?php echo $office ? basename($office->officer_local_path) : ""; ?>" />
                                        </div>
                                        <div class="ym-gl ym-g33">
                                            <button class="upload-button"  data-id="<?php echo $office ? $office->id : "" ?>"
                                                    data-op="officer_onwer" data-old-data="<?php echo $mr ? "1" : ""; ?>">Upload</button>
                                            <button class="<?php echo $office ? "upload-success" : "" ?> view-pdf"
                                                    data-id="<?php echo $office ? $office->id : "" ?>" data-op="officer_onwer">View</button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $office ? $office->id : "" ?>" data-op="officer_onwer" style="width:65px">Delete</button>
                                            <input type="hidden" name="officer_file_ids[]" value="<?php echo $office ? $office->id : "" ?>" class="input-txt input-file-id officer-onwer-file-id"/>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60" style="width:90%"><a class="ym-gr" href="#"  class="main_link_color" id="addOfficerOrOwner">Add officer or owner</a></div>
                            </div>

                            <!-- business company-->
                            <div class="ym-grid" id="mock">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_special_IfBusinessNamecorporationOrTra'); ?></div>
                            </div>
<!--                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <textarea rows="5" cols="5" class="input-txt" name="note3"><?php //echo $cases_verification ?$cases_verification->note3 :"";?></textarea>
                                </div>
                            </div>-->

                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><?php language_e('cases_view_verification_special_PleaseUploadYourBusinessLicens'); ?>:</div>
                            </div>
                            <div id="divBusinesssLicenseContainer">
                                <?php
                                if(empty($business_licenses)){
                                    $business_licenses = array("");
                                }
                                ?>
                                <?php foreach($business_licenses as $b){?>
                                <div class="ym-grid">
                                        <div class="ym-gl ym-g40">
                                            <input type="text" name="business_license_name[]" class="input-txt input-file-name business_license_name" style="border-color:#a8a8a8"
                                                   readonly value="<?php echo $b ? basename($b->local_file_path) : ""; ?>" maxlength="100" />
                                        </div>
                                        <div class="ym-gl ym-g60">
                                            <button class="upload-button business-license-name" data-id="<?php echo $b ? $b->id : ""; ?>"
                                                    data-op="business_license" data-old-data="<?php echo $b ? "1" : ""; ?>">Upload</button>
                                            <button class="<?php echo $b ? "upload-success" : "" ?> view-pdf" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="business_license">View</button>
                                            <!-- #1189 New Function: need to add a "remove" icon -->
                                            <button type="button"  class="delete-button" data-id="<?php echo $b ? $b->id : ""; ?>" data-op="business_license" >Delete</button>
                                            <input type="hidden" name="business_license_ids[]" value="<?php echo $b ? $b->id : ""; ?>" class="input-txt input-file-id business-license-file-id"  />
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60"><a class="ym-gr" href="#"  class="main_link_color" id="addBusinessLicenseDocument">Add document</a></div>
                            </div>

                            <div class="ym-grid" style="margin-top: 90px !important">
                                <div class="ym-gl ym-g60">
                                    <strong>Next Steps: </strong>
                                </div>
                            </div>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g60">
                                    <strong>
                                        <ol>
                                            <li><?php language_e('cases_view_verification_special_ClickBelowToSaveAndCreatePDF'); ?></li>
                                            <li><?php language_e('cases_view_verification_special_AddYourSignatureInBox5IfYouWan'); ?></li>
                                            <li><?php language_e('cases_view_verification_special_PrintPDFAndHaveItNotarizedInBo'); ?></li>
                                            <li><?php language_e('cases_view_verification_special_ScanAndUploadSignedDocumentPDF'); ?></li>
                                        </ol>
                                    </strong>
                                </div>
                            </div>
                            <?php if ($status_verification_special && $status_verification_special->status == 3) { ?>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g60">Comment for registration</div>
                                </div>
                                <div class="ym-grid">
                                    <div class="ym-gl ym-g60">
                                        <?php
                                            $comment_for_registration_content = "";
                                            if($cases_verification && $cases_verification->comment_for_registration_content != '0'){
                                                $comment_for_registration_content = $cases_verification->comment_for_registration_content;
                                            }
                                        ?>
                                        <textarea class="input-txt" name="comment_for_registration_content"
                                            rows="5" cols="5"><?php echo $comment_for_registration_content;?></textarea>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="ym-grid">
                                <div class="ym-gl ym-g80"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="case_id" name="case_id" value="<?php echo $case_id; ?>" />
                    <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />
                    <input type="hidden" id="special_verification_file_change" name="special_verification_file_change" value="0" />
                    <input type="hidden" id="check_click_save_btn" name="check_click_save_btn" value="0" />

                    <!-- hiden field for check resubmit-->
                    <input type="hidden" id="change_usps_mail_receiver_flag" value="" name="change_usps_mail_receiver_flag" />
                    <input type="hidden" id="change_usps_officer_flag" value="" name="change_usps_officer_flag" />
                    <input type="hidden" id="change_usps_business_license_flag" value="" name="change_usps_business_license_flag" />
                </form>
            </div>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g100">
                <a class="input-btn" id="save_btn" href="#">Save and
                    create PDF...</a><a class="input-btn"
                    id="upload_btn" href="#"><?php language_e('cases_view_verification_special_UploadSignedScan'); ?></a><a
                    class="backBtn gray-btn" id="submit_btn" href="#">Submit</a><a
                    href="#" class="backBtn" id="backBtn">Back</a>
            </div>
        </div>
    </div>
</div>
<div style="display: none" class="hide">
    <a id="view_verification_file" class="iframe">Preview file</a>

    <input type="file" id="special_verification_file" name="special_verification_file" style="display: none">
    <input type="file" id="id_of_applicant_verification_file" name="id_of_applicant_verification" style="display: none">
    <input type="file" id="license_of_applicant_verification_file" name="license_of_applicant_verification" style="display:none">

    <form method="post">
        <input name="upload_file_input" id="upload_file_input" value="" type="file" />
    </form>

    <div id="reSubmitWindow" title="Confirm Submit Verification" class="input-form dialog-form"></div>
    <div id="hereModal" style="width: 600px; padding: 15px;"><?php language_e('cases_view_verification_special_StrongPrivacyActStatementstron'); ?>
    </div>

    <!-- mock of officer -->
    <div class="officer_mock">
        <div class="ym-grid">
            <div class="ym-gl ym-g62">
                Name: <input type="text" name="officer_name[]" class="input-txt office-name" style="width:200px"
                             value="" maxlength="100" />
                             <?php
                             echo code_master_form_dropdown(array(
                                 "code" => APConstants::CASE_VERIFICATION_USPS_OFFICER_OWNER,
                                 "value" => "",
                                 "name" => 'officer_type[]',
                                 "id" => '',
                                 "clazz" => 'input-width office-type',
                                 "style" => 'width: 50px;',
                                 "has_empty" => true
                             ));
                             ?>
                <input type="text" name="officer_rate[]" value="" class="input-txt office-rate" style="width:50px" /> %
                <input type="hidden" name="officer_file_id[]" value="" class="input-txt input-file-id" />
                <input type="text" style="width:120px;border-color:#a8a8a8" class="input-txt input-file-name"  readonly value="" />
            </div>
            <div class="ym-gl ym-g33">
                <button type="button" class="upload-button" data-id="" data-op="officer_onwer">Upload</button>
                <button type="button" class="view-pdf" data-id="" data-op="officer_onwer">View</button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="delete-button" data-id="" data-op="officer_onwer" style="width:65px">Delete</button>
                <input type="hidden" name="officer_file_ids[]" value="" class="input-txt input-file-id officer-onwer-file-id" />
            </div>
        </div>
    </div>

    <!-- mail receiver mock -->
    <div class="mail_receiver_mock">
        <div class="ym-grid">
            <div class="ym-gl ym-g60">
                Name: <input type="text" name="mail_receiver_name[]" class="input-txt mail_receiver_name" style="width:310px"
                             value="" maxlength="100" />
                <input type="text" style="width:120px;border-color:#a8a8a8" class="input-txt input-file-name" name="input_file_name[]" readonly value="" maxlength="100" />
                <input type="hidden" name="mail_receiver_id[]" value="" class="input-txt input-file-id" />
            </div>
            <div class="ym-gl ym-g40">
                <button type="button" class="upload-button" data-id="" data-op="mail_receiver">Upload</button>
                <button type="button" class="view-pdf" data-op="mail_receiver" data-id="">View</button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="delete-button" data-id="" data-op="mail_receiver">Delete</button>
                <input type="hidden" name="mail_receiver_ids[]" value="" class="input-txt input-file-id mail-receiver-file-id" />
            </div>
        </div>
    </div>

    <!-- business company mock -->
    <div class="business_license_mock">
        <div class="ym-grid">
            <div class="ym-gl ym-g40">
                <input type="text" style="border-color:#a8a8a8" name="business_license_name[]" class="input-txt input-file-name business_license_name"  readonly value="" maxlength="100" />
            </div>
            <div class="ym-gl ym-g60">
                <button class="upload-button business-license-name" data-id="" data-op="business_license">Upload</button>
                <button class="view-pdf" data-id="" data-op="business_license">View</button>
                <!-- #1189 New Function: need to add a "remove" icon -->
                <button type="button"  class="button-subtract delete-button" data-id="" data-op="business_license">Delete</button>
                <input type="hidden" name="business_license_ids[]" value="" class="input-txt input-file-id business-license-file-id" />
            </div>
        </div>
    </div>

    <input type="hidden" name="postbox_company" id="postbox_company" value="<?php echo $verify_postbox->company;?>" />
</div>

<script type="text/javascript">
$(document).ready(function(){

    var submit_button_flag = '0';

    $('input[name="name_to_delivery"]').tipsy({
        trigger: 'click',
        gravity: 'n'
    });

    $('input[name="name_of_corporation"]').tipsy({
        trigger: 'click',
        gravity: 'n'
    });

    $("#below").show();
    $("#here").hide();

    $(".bd-content").scroll(function () {
        var topOfMockDiv = $("#mock").offset().top;
        //$(this).scrollTop()
        if ($(this).height() > topOfMockDiv) {
            $("#below").hide();
            $("#here").show();
        } else {
            $("#below").show();
            $("#here").hide();
        }
    });

    $("#here").fancybox({
        maxWidth    : 800,
        maxHeight    : 600,
        fitToView    : false,
        width        : '70%',
        height        : '70%',
        autoSize    : false,
        closeClick    : false,
        openEffect    : 'none',
        closeEffect    : 'none'
    });

    $("#backBtn").click(function(){
        history.back(-1);
        return  false;
    });

    $(".bd-content").slimScroll({height:($(window).height() - 353)+'px'});

    $("#save_btn").click(function(e){
        e.preventDefault();

        $("#check_click_save_btn").val(1);
        var submitUrl = '<?php echo base_url()?>cases/verification/verification_special_form_PS1583';
        $("#special_verification_form").find(".input-error").tipsy('disable').removeClass("input-error");

        $.ajaxSubmit({
            url: submitUrl,
            formId: 'special_verification_form',
            success: function(obj) {
                if (obj.status) {
                    $.infor({
                        message:"Save and create file PDF sucessfull.<br/>Please click button OK to download file PDF!",
                        ok:function(){
                            document.location.href = '<?php echo base_url()?>cases/verification/special_file_export';
                        }
                    });

                    $("#save_btn").addClass('gray-btn');
                    submit_button_flag = "1";
                } else {
                    $.each( obj.data.message, function( key, value ){
                        $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                    });
                    // validate officer owner
                    validate_officer_owner();

                    // validate mail receiver
                    validate_mail_receiver();

                     // validate business company
                     validate_business_company();

                    $("#special_verification_form").find(".input-error").tipsy({gravity: 'sw'});

                    if(obj.message.code == "1"){
                        $.displayError(obj.message.message);
                    }
                    else {
                        $.displayError(obj.message);
                    }
                }
            }
        });

        return false;
    });

    $("#submit_btn").click(function(e){

        e.preventDefault();
        
        if($(this).hasClass('backBtn')){
            var first_submit = "<?php echo $first_submit ?>";
            if(first_submit != '1'){
                $.displayError("<?php language_e('cases_view_verification_special_UploadSignedScanMess'); ?>");
                return;
            }
        }

        $("#check_click_save_btn").val(0);

        var submitUrlData = '<?php echo base_url()?>cases/verification/verification_special_form_PS1583';

        $("#special_verification_form").find(".input-error").tipsy('disable').removeClass("input-error");
        
        //Submit data 1st time
        $.ajaxSubmit({
            url: submitUrlData,
            formId: 'special_verification_form',
            success: function(obj) {
                
                if (obj.status) {

                    var submitUrl = '<?php echo base_url()?>cases/verification/verification_special_form_PS1583_submit';
                    $("#special_verification_form").find(".input-error").tipsy('disable').removeClass("input-error");
                    
                    //Submit data 2nd times
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'special_verification_form',
                        success: function(obj) {
                            //console.log("H0:"+JSON.stringify(obj));
                            if (obj.status) {
                                $.infor({
                                    message: obj.message,
                                    ok:function(){
                                        // de hien thi case duoc chon trong man hinh your case.
                                        document.location.href = '<?php echo base_url()?>cases?product_id=5&case_id=<?php echo $case_id;?>';
                                    }
                                });
                            } else {
                                $.each( obj.data, function( key, value ){
                                    $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                                });

                                // validate officer owner
                                validate_officer_owner();

                                 // validate mail receiver
                                 validate_mail_receiver();

                                 // validate business company
                                 validate_business_company();

                                $("#special_verification_form").find(".input-error").tipsy({gravity: 'sw'});

                                if(obj.message.code == "1"){
                                    $.displayError(obj.message.message);
                                }
                                else {
                                    $.displayError(obj.message);
                                }

                            }
                        }
                    });

                    return false;
                } else {
                    var first_submit = "<?php echo $first_submit ?>";
                    if(first_submit == '1'){

                        $.each( obj.data.message, function( key, value ){
                            $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                    }


                    if(obj.message.code == "1"){
                        $.displayError(obj.message.message);
                    }
                    else if(obj.message.code == "0") {
                        $("#reSubmitWindow").html("<p style='color: #d14b4b;font-weight: bold; margin-top: 16px;'>"+obj.message.message+"</p>");
                        $('#reSubmitWindow').openDialog({
                            autoOpen: false,
                            height: 200,
                            width: 500,
                            modal: false,
                            open: function () {},
                            buttons: {
                                'Cancel and correct data': function () {
                                    $(this).dialog('close');
                                },
                                'Re-submit with same data': function () {
                                    $(this).dialog('close');
                                    resubmitVerification();
                                }
                            }
                        });
                        $('#reSubmitWindow').dialog('option', 'position', 'center');
                        $('#reSubmitWindow').dialog('open');
                    }
                    else {
                        $.each( obj.message, function( key, value ){
                            $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                        $.displayError(obj.message);
                    }
                    return;
                }
            }

        });

    });

    $("#upload_btn").click(function(){
        $("#special_verification_file").click();
        return false;
    });

    $("#id_of_applicant_verification_btn").click(function(event){
        $("#id_of_applicant_verification_file").click();
        return false;
    });

    $("#license_of_applicant_verification_btn").click(function(event){
        $("#license_of_applicant_verification_file").click();
        return false;
    });

    $("#additional_verification_btn").click(function(event){
        $("#additional_verification_file").click();
        return false;
    });

    $("#additional_company_verification_btn").click(function(event){
        $("#additional_company_verification_file").click();
        return false;
    });

    $('#special_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError('Please select pdf file to upload.');
            return;
        }
       $("#special_verification_file_change").val(1);
       // Upload data here
       $.ajaxFileUpload({
           id: 'special_verification_file',
           data: {
               case_id: '<?php echo $case_id; ?>',
               type: '<?php echo $type; ?>',
               input_file_client_name: 'special_verification_file'
           },
           url: '<?php echo base_url()?>cases/verification/special_upload_file',
           resetFileValue:true,
           success: function(obj) {
               //document.location.href = '<?php echo base_url()?>cases/verification';
               $('#submit_btn').removeClass('backBtn');
               $('#upload_btn').addClass('gray-btn');
               if(submit_button_flag == "1"){
                    $('#submit_btn').removeClass('gray-btn');
                    $('#submit_btn').addClass('input-btn');
                }

               $.infor({message:"Upload file successful."});
           }
       });
    });

    $('#id_of_applicant_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_special_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }

        $("#id_of_applicant_verification_change").val(1);

        // Upload data here
        $.ajaxFileUpload({
            id: 'id_of_applicant_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                type: '<?php echo $type; ?>',
                input_file_client_name: 'id_of_applicant_verification'
            },
            url: '<?php echo base_url()?>cases/verification/special_upload_file',
            resetFileValue:true,
            success: function(obj) {
                $('#id_of_applicant_verification_txt').val($("#id_of_applicant_verification_file").val().split('\\').pop());
                $('#id_of_applicant_view_btn').addClass('upload-success');
            }
        });
      });

    $('#license_of_applicant_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_special_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }
        $("#license_of_applicant_verification_change").val(1);
        // Upload data here
        $.ajaxFileUpload({
            id: 'license_of_applicant_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                type: '<?php echo $type; ?>',
                input_file_client_name: 'license_of_applicant_verification'
            },
            url: '<?php echo base_url()?>cases/verification/special_upload_file',
            resetFileValue:true,
            success: function(obj) {
                $('#license_of_applicant_verification_txt').val($("#license_of_applicant_verification_file").val().split('\\').pop());
                $('#license_of_applicant_view_btn').addClass('upload-success');
            }
        });
      });

    $('#additional_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_special_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }

        $("#additional_verification_change").val(1);
        // Upload data here
        $.ajaxFileUpload({
            id: 'additional_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                type: '<?php echo $type; ?>',
                input_file_client_name: 'additional_verification'
            },
            url: '<?php echo base_url()?>cases/verification/special_upload_file',
            resetFileValue:true,
            success: function(obj) {
                $('#additional_verification_txt').val($("#additional_verification_file").val().split('\\').pop());
                //$('#additional_verification_btn').hide();
                //$('#additional_delete_btn').show();
                $('#additional_view_btn').addClass('upload-success');
            }
        });
      });

    $('#additional_company_verification_file').change(function(click) {
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
           $.displayError("<?php language_e('cases_view_verification_special_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }

        $("#additional_company_verification_change").val(1);

        // Upload data here
        $.ajaxFileUpload({
            id: 'additional_company_verification_file',
            data: {
                case_id: '<?php echo $case_id; ?>',
                type: '<?php echo $type; ?>',
                input_file_client_name: 'additional_company_verification'
            },
            url: '<?php echo base_url()?>cases/verification/special_upload_file',
            resetFileValue:true,
            success: function(obj) {
                $('#additional_company_verification_txt').val($("#additional_company_verification_file").val().split('\\').pop());
                //$('#additional_company_verification_btn').hide();
                //$('#additional_company_delete_btn').show();
                $('#additional_company_view_btn').addClass('upload-success');
            }
        });
      });

    // Click on the "Delete" button to delete the uploaded additional file
    if ($('#additional_verification_txt').val() != '') {
        //$('#additional_verification_btn').hide();
        //$('#additional_delete_btn').show();
    }

    $("#additional_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url()?>cases/verification/delete_additional_verification_file',
            data: { case_id: '<?php echo $case_id; ?>', file_type: 'additional_verification' },
            success: function(data) {
                if (data.status) {
                    $('#additional_verification_btn').show();
                    $('#additional_delete_btn').hide();
                    $('#additional_view_btn').removeClass('upload-success');
                    $('#additional_verification_file').val('');
                    $('#additional_verification_txt').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

    $("#additional_company_delete_btn").click(function() {
        $.ajaxExec({
            url: '<?php echo base_url()?>cases/verification/delete_additional_verification_file',
            data: { case_id: '<?php echo $case_id; ?>' , file_type: 'additional_company_verification' },
            success: function(data) {
                if (data.status) {
                    $('#additional_company_verification_btn').show();
                    $('#additional_company_delete_btn').hide();
                    $('#additional_company_view_btn').removeClass('upload-success');
                    $('#additional_company_verification_file').val('');
                    $('#additional_company_verification_txt').val('');
                } else {
                    console.log('Failed to delete additional_verification_file');
                }
            }
        });
        return false;
    });

    $("#id_of_applicant_view_btn").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/special_view_file?case_id=<?php echo $case_id?>&op=id");
        $('#view_verification_file').click();
        return false;
    });

    $("#license_of_applicant_view_btn").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/special_view_file?case_id=<?php echo $case_id?>&op=license");
        $('#view_verification_file').click();
        return false;
    });

    $("#additional_view_btn").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/special_view_file?case_id=<?php echo $case_id?>&op=additional");
        $('#view_verification_file').click();
        return false;
    });

    $("#additional_company_view_btn").click(function(){
        $('#view_verification_file').attr('href',"<?php echo base_url()?>cases/verification/special_view_file?case_id=<?php echo $case_id?>&op=company");
        $('#view_verification_file').click();
        return false;
    });

    $('#view_verification_file').fancybox({
        width: 1000,
        height: 800
    });

    $("#addOfficerOrOwner").click(function(e){
        e.preventDefault();

        var content = $(".officer_mock").html();
        $("#divOfficerContainer").append(content);
        return false;
    });

    $("#addMailReceiver").click(function(e){
        e.preventDefault();

        var content = $(".mail_receiver_mock").html();
        $("#divMailReceiverContainer").append(content);
        return false;
    });

    $("#addBusinessLicenseDocument").click(function(e){
        e.preventDefault();

        var content = $(".business_license_mock").html();
        $("#divBusinesssLicenseContainer").append(content);
        return false;
    });

    var item_click;
    $(".upload-button").live('click',function(e){
        e.preventDefault;

        item_click = $(this);
        // do upload function
        $("#upload_file_input").click();

        return false;
    });

    $('#upload_file_input').change(function(e) {
        e.preventDefault();
        myfile= $( this ).val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "PDF")
                && (ext.toUpperCase() != "JPG")
                && (ext.toUpperCase() != "TIF")
                && (ext.toUpperCase() != "BMP")
                && (ext.toUpperCase() != "PNG")){
            $.displayError("<?php language_e('cases_view_verification_special_PleaseSelectPDFJPGTIFBMPPNGFil'); ?>");
            return;
        }
        var op = $(item_click).data('op');
        var time = $.now();

        var submitUrl = '<?php echo base_url()?>cases/verification/';
        var seq_number = "";
        if(op == "mail_receiver"){
            seq_number = "01";
            submitUrl = submitUrl += "upload_resource?t="+time;
            $("#change_usps_mail_receiver_flag").val("1");
        }else if(op == "officer_onwer"){
            submitUrl = submitUrl += "upload_special_document?t="+time;
            $("#change_usps_officer_flag").val("1");
        } if(op == "business_license"){
            submitUrl = submitUrl += "upload_resource?t="+time;
            seq_number = "02";
            $("#change_usps_business_license_flag").val("1");
        }

        // Upload data here
        $.ajaxFileUpload({
            id: 'upload_file_input',
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : $(item_click).data("id"),
                op: op,
                seq_number: seq_number,
                type: '<?php echo $type; ?>',
                input_file_client_name: "upload_file_input",
                base_taskname: '<?php echo $base_taskname ?>'
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
                $(item_click).parent().find('.view-pdf').addClass('upload-success');
                $(item_click).parent().parent().find(".input-file-name").val(myfile.split('\\').pop());
                if(op == "officer_onwer"){
                    $(item_click).parent().find('.view-pdf').data('id', response.data.response_id);
                    $(item_click).data("id", response.data.response_id);
                    $(item_click).parent().parent().find(".input-file-id").val(response.data.response_id);
                }else{
                    $(item_click).parent().find('.view-pdf').data('id', response.data.file_id);
                    $(item_click).data("id", response.data.response_id);
                    $(item_click).parent().parent().find(".input-file-id").val(response.data.file_id);
                }
            }
        });

        return false;
    });

    /**
     * view all pdf file.
     */
    $(".view-pdf").live('click',function(e){
        e.preventDefault();
        var url = "<?php echo base_url()?>cases/verification/";
        var op = $(this).data('op');
        if(op == "mail_receiver" || op == 'business_license'){
            url += "view_resource?case_id=<?php echo $case_id?>";
        }else{
            url += "special_view_file?case_id=<?php echo $case_id?>";
        }
        url += "&id=" + $(this).data('id');
        url += "&op=" + op;
        url += "&file_id=" + $(this).data('id');

        $('#view_verification_file').attr('href',url);
        $('#view_verification_file').click();
        return false;
    });

    /**
     * resubmit case.
     * @returns {Boolean}
     */
    function resubmitVerification(){
        $("#check_click_save_btn").val(1);
        var submitUrlData = '<?php echo base_url()?>cases/verification/verification_special_form_PS1583';
        $("#special_verification_form").find(".input-error").tipsy('disable').removeClass("input-error");
        $.ajaxSubmit({
            url: submitUrlData,
            formId: 'special_verification_form',
            success: function(obj1) {
                if (obj1.status) {
                    var submitUrl = '<?php echo base_url()?>cases/verification/verification_special_form_PS1583_submit';
                    $("#special_verification_form").find(".input-error").tipsy('disable').removeClass("input-error");
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'special_verification_form',
                        success: function(obj) {
                            if (obj.status) {
                                $.infor({
                                    message: obj.message,
                                    ok:function(){
                                        document.location.href = '<?php echo base_url()?>cases?product_id=5&case_id=<?php echo $case_id;?>';
                                    }
                                });
                            } else {
                                $.each( obj.data, function( key, value ){
                                    $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                                });

                                $("#special_verification_form").find(".input-error").tipsy({gravity: 'sw'});
                                if(obj.message.code == "1"){
                                    $.displayError(obj.message.message);
                                } else {
                                    $.displayError(obj.message);
                                }
                            }
                        }
                    });
                } else {
                    $.each( obj1.data, function( key, value ){
                        $("#special_verification_form").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                    });
                    $("#special_verification_form").find(".input-error").tipsy({gravity: 'sw'});
                    $.displayError(obj1.message);
                    return false;
                }
            }
        });

        return false;
    }

    function validate_officer_owner(){
        var flag_check = true;

        $('.input-file-id').each(function(index){
            var parent = $(this).parent().parent();

            var file_id = $.trim($(this).val());
            var name = $.trim(parent.find(".office-name").val());
            var type = $.trim(parent.find(".office-type").val());
            var rate = $.trim(parent.find(".office-rate").val());
            if(file_id != "" || name != "" || type != "" || rate != ""){
                flag_check = false;
                if(name == ""){
                    parent.find(".office-name").addClass("input-error").attr("title", "Please input the name.");
                }
                if(type == ""){
                    parent.find(".office-type").addClass("input-error").attr("title", "Please input the type");
                }
                if(rate == "" ){
                    parent.find(".office-rate").addClass("input-error").attr("title", "Please input the rate and rate > 25%.");
                }

                if(file_id == ""){
                    parent.find(".office-name").addClass("input-error").attr("title", "Please upload the file.");
                }
                parent.find(".office-name").focus();
            }
        });

        return flag_check;
    }

    function validate_mail_receiver(){
        var flag_check = true;

        $('.mail_receiver_name').each(function(index){
            var parent = $(this).parent().parent();

            var name = $.trim(parent.find(".mail_receiver_name").val());
            var file_name = $.trim(parent.find(".input-file-name").val());

            if(name != "" ){
                if(file_name == ""){
                    flag_check = false;
                    parent.find(".upload-button").addClass("input-error").attr("title", "Please upload document here.");
                }

                parent.find(".upload-button").focus();
            }
        });

        return flag_check;
    }

    function validate_business_company(){
        var flag_check = true;
        if($("#postbox_company").val() != ''){
            $('.input-file-id').each(function(index){
                var parent = $(this).parent().parent();
                var name = $.trim(parent.find(".business_license_name").val());

                if(name == "" ){
                    flag_check = false;
                    parent.find(".business-license-name").addClass("input-error").attr("title", "Please upload the file.");
                }else{
                     parent.find(".business-license-name").removeClass("input-error").attr("title", "");
                }

                 parent.find(".business_license_name").focus();
            });

            if($("#business_type_of_corporation").val() == ""){
                flag_check = false;
                $("#business_type_of_corporation").addClass("input-error").attr("title", "The type of business is required.");
                $("#business_type_of_corporation").focus();
            }
        }

        return flag_check;
    }

    // Button delete (#1189 New Function: need to add a "remove" icon )
    var item_delete_click;
    $(".delete-button").live('click',function(e){
        e.preventDefault;

        item_delete_click = $(this);

        // do delete function
        var op = $(item_delete_click).data('op');
        var time = $.now();
        var submitUrl = "<?php echo base_url()?>cases/verification/";
        var id;

        if(op == "officer_onwer"){
            //$("#change_usps_mail_receiver_flag").val("1");
            submitUrl += "delete_specical_resource?t="+time;
            op = "officer_onwer";
            id = $(item_delete_click).parent().find(".officer-onwer-file-id").val();
        }else if(op == "mail_receiver"){
            //$("#change_usps_officer_flag").val("1");
            submitUrl += "delete_specical_resource?t="+time;
            op = "mail_receiver";
            id = $(item_delete_click).parent().find(".mail-receiver-file-id").val();
        }else if(op == "business_license"){
            submitUrl += "delete_specical_resource?t="+time;
            op = "business_license";
            id = $(item_delete_click).parent().find(".business-license-file-id").val();
        }

        // Delete data here
        $.ajaxExec({
            data: {
                case_id: '<?php echo $case_id; ?>',
                id : id,
                op: op
            },
            url: submitUrl,
            resetFileValue:true,
            success: function(response) {
               $(item_delete_click).parent().parent().remove();
            }
        });
        return false;
    });

});
</script>