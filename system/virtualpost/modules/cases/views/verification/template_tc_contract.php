<table border="0" style="border:none; font-size: 35px;">
    <tr>
        <td align="left"><strong><?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?><br />
            <?php echo Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE)?> <br />
            <?php echo Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE).' '.Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE);?><br />
            <?php echo Settings::get(APConstants::INSTANCE_OWNER_COUNTRY_CODE)?></strong>
        </td>
    </tr>
</table>

<div style="font-size:28px"><?php echo $term_and_condition;?></div>

<br />
<table border="0" style="border:none">
    <tr>
        <td width="66%"><strong style="font-size:25px">Date: <?php echo APUtils::convert_timestamp_to_date(time()); ?></strong></td>
        <td width="33%">
            <hr />
            <br/><strong style="font-size:25px" ><?php language_e('cases_view_verification_template_tc_contract_Name'); ?> <?php echo $name;?><br/>
                <?php language_e('cases_view_verification_template_tc_contract_CompanyName'); ?> <?php echo $company;?></strong>
        </td>
    </tr>
</table>