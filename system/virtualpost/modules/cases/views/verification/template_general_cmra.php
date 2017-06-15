<br />
<table cellpadding="3">
    <colgroup>
        <col width="25%" />
        <col width="25%" />
        <col width="25%" />
        <col width="25%" />
    </colgroup>
    <tr>
        <td colspan="3" style="line-height: 0.86em;"><?php language_e('cases_view_verification_template_general_cmra_H2ApplicationForDeliveryOfMail'); ?></td>
        <td
            style="border-left: 1px solid black; border-top: 1px solid black;">1.
            Date <br /><?php echo date(APConstants::DATEFORMAT_OUTPUT_PDF, $updated_date)?></td>
    </tr>
    <tr>
        <td colspan="4"
            style="border-top: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_InConsiderationOfDeliveryOfMyO'); ?></td>
    </tr>
</table>
<table cellpadding="3">
    <colgroup>
        <col width="50%" />
        <col width="50%" />
    </colgroup>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_2NameInWhichApplicantsMailWill'); ?>
            <br /> <strong style="font-size: 1.1em"><?php echo $name_to_delivery?><?php if (!empty($verify_postbox) && !empty($verify_postbox->company)) { echo ', '.$verify_postbox->company;}?></strong>
        </td>
        <td style="border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_3AddressToBeUsedForDeliveryInc'); ?><br /> <strong
            style="font-size: 1.1em">
            <br /><?php echo $p_location_street?>
            <br /><?php echo $p_location_postcode?>
            <br /><?php echo $p_location_city?>
            <br /><?php echo $p_location_country?>
            </strong>
        </td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_4ApplicantAuthorizesDeliveryTo'); ?> <strong style="font-size: 1.1em">
                <br /><?php echo $company_name?>
                <br /><?php echo $p_location_street?>
                <br /><?php echo $p_location_postcode?>
                <br /><?php echo $p_location_city?>
                <br /><?php echo $p_location_country?>
            </strong>
        </td>
        <td style="border-bottom: 1px solid black">5.This Authorization Is Extended to Include Restricted Delivery Mail for the
            Undersigned(s)<br />
        <strong style="font-size: 1.1em"><?php echo $xx?></strong>
        </td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black">6.
            Name of Applicant<br /> <strong style="font-size: 1.1em"><?php echo $name_of_applicant?></strong>
        </td>
        <td rowspan="2" style="border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_7ApplicantHomeAddressNumberStr'); ?> <br /> <strong style="font-size: 1.1em"><?php echo $street_of_applicant?>
             <br /><?php echo $city_of_applicant?>, <?php echo $postcode_of_applicant?>
             <br /><?php echo $region_of_applicant?>
             <br /><?php echo $country_applicant_name?></strong>
             <br />Telephone
            Number (<strong style="font-size: 1.1em"><?php echo $phone_of_applicant?></strong>
            )
        </td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_8TwoTypesOfIdentificationAreRe'); ?></td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black; height: 30px;">a.
            <strong style="font-size: 1.1em"><?php echo $id_of_applicant?></strong>
        </td>
        <td style="border-bottom: 1px solid black;">9. Name of Firm or
            Corporation<br /> <strong style="font-size: 1.1em"><?php echo $name_of_corporation?></strong>
        </td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black; height: 30px;">b.
            <strong style="font-size: 1.1em"><?php echo $license_of_applicant?></strong>
        </td>
        <td rowspan="2" style="border-bottom: 1px solid black">10.
            Business Address (Number, street, city, state and ZIP Code)
            <br /> <strong style="font-size: 1.1em"><?php echo $street_of_corporation?>
             <br /><?php echo $city_of_corporation?>, <?php echo $postcode_of_corporation?>
             <br /><?php echo $region_of_corporation?>
             <br /><?php echo $country_corporation_name?></strong>
             <br />Telephone
            Number (<strong style="font-size: 1.1em"><?php echo $phone_of_corporation?></strong>
            )
        </td>
    </tr>
    <tr>
        <td style="border-right: 1px solid black;"><?php language_e('cases_view_verification_template_general_cmra_AcceptableIdentificationInclud'); ?></td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_RegistrationCardOrCertificateO'); ?></td>
        <td style="border-bottom: 1px solid black">11. Kind of Business<br />
            <strong style="font-size: 1.1em"><?php echo $business_type_of_corporation?></strong></td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_12IfApplicantIsAFirmNameEachMe'); ?><br /> <strong
            style="font-size: 1.1em"><?php //echo $note1?></strong>
        </td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_13IfACORPORATIONGiveNamesAndAd'); ?><br />
            <strong style="font-size: 1.1em"><?php //echo $note2?></strong>
            <br/><br/>
        </td>
        <td style="border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_14IfBusinessNameOfTheAddressCo'); ?><br /> <strong style="font-size: 1.1em"><?php //echo $note3?></strong>
            <br/><br/>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_WarningTheFurnishingOfFalseOrM'); ?></td>
    </tr>
    <tr>
        <td
            style="border-right: 1px solid black; border-bottom: 1px solid black"><?php language_e('cases_view_verification_template_general_cmra_15SignatureOfAgentNotaryPublic'); ?><br /> <br /> <br />
        </td>
        <td
            style="border-bottom: 1px solid black; background-color: #fff294"><?php language_e('cases_view_verification_template_general_cmra_16SignatureOfApplicantIfFirmOr'); ?><br /> <br /> <br />
        </td>
    </tr>
</table>
<!-- <tcpdf method="AddPage" /> -->
<div style="width: 100%; text-align: center;">
    <h2>Privacy Act Statement</h2>
</div>
<table cellpadding="5">
    <colgroup>
        <col width="20%" />
        <col width="20%" />
        <col width="20%" />
        <col width="20%" />
        <col width="20%" />
    </colgroup>
    <tr>
        <td></td>
        <td colspan="3"
            style="border: 2px solid black; font-size: 1.2em;">"<?php language_e('cases_view_verification_template_general_cmra_ThisInformationWillBeUsedToAut'); ?>"</td>
        <td></td>
    </tr>
</table>