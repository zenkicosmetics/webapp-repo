<br />

<table border="0" style="border:none" width="100%">
    <tr>
        <?php $logo_url = Settings::get(APConstants::SITE_LOGO_CODE);
            if (empty($logo_url)) {
                $logo_url = APContext::getImagePath().'/logo_white_beta.png';
            }else {
                $logo_url = APContext::getAssetPath().$logo_url;
            }
        ?>
        <td width="100px"><img width="100px" src="<?php echo $logo_url?>" /></td>
        <td width="85%"><h1><?php language_e('cases_view_verification_template_california_mailbox_AcknowledgementForCaliforniaMa'); ?> </h1></td>
    </tr>
</table>
<br />
<p><?php language_e('cases_view_verification_template_california_mailbox_ThisAcknowledgementIsRequiredB'); ?></p>
<p><?php language_e('cases_view_verification_template_california_mailbox_ByObtainingUseOfAPrivateMailbo'); ?>:</p>
<table border="0" style="border:none" width="100%">
    <tr>
        <td>1. &nbsp;&nbsp;&nbsp; <?php language_e('cases_view_verification_template_california_mailbox_IAmObligatedToDiscloseMyActual'); ?></td>
    </tr>
    <tr>
        <td>2. &nbsp;&nbsp;&nbsp; <?php language_e('cases_view_verification_template_california_mailbox_BySigningBelowIIrrevocablyAuth'); ?></td>
    </tr>
    <tr>
        <td>3. &nbsp;&nbsp;&nbsp; <?php language_e('cases_view_verification_template_california_mailbox_IFurtherAcknowledgeThatIUnders'); ?>
</td>
    </tr>
</table>
<p><?php language_e('cases_view_verification_template_california_mailbox_IHerebyAgreeToAcceptAndAbideBy'); ?></p>
<br />
<table border="0" style="border:none" width="100%">
    <tr>
        <td width="15%"><?php echo APUtils::convert_timestamp_to_date(time());?><hr />DATE</td>
        <td width="85%">&nbsp;<hr />SIGNATURE</td>
    </tr>
    <tr>
        <td width="99%">&nbsp;<br /><?php echo $name_of_applicant;?><hr />NAME (Printed)</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td width="99%">&nbsp;<br /><?php echo $p_location_street?> <hr />Street address</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td width="99%">&nbsp;<br /><?php echo $p_location_city?>, <?php echo $p_location_postcode?> <hr /></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="left" width="50%"><span style="float:left;text-align: left">CITY/STATE/ZIP</span></td>
        <td align="right" width="50%"><span style="float:right;text-align: right;">POP/ACKCA/1402</span></td>
    </tr>
</table>