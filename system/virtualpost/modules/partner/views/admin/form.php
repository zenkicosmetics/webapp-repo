<?php
if ($action_type == 'add') {
    $submit_url = base_url () . 'partner/admin/add';
} else {
    $submit_url = base_url () . 'partner/admin/edit';
}
?>
<form id="addEditPartnerForm" method="post" action="<?php echo $submit_url?>" autocomplete="on">
    <table>
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_PartnerName'); ?><span class="required">*</span></th>
                        <td><input type="text" id="partner_name" name="partner_name" value="<?php echo $partner->partner_name?>"
                            class="input-txt" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_CompanyName'); ?><span class="required">*</span></th>
                        <td><input type="text" id="company_name" name="company_name" value="<?php echo $partner->company_name?>"
                            class="input-txt" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_InvoicingStreet '); ?><span class="required">*</span></th>
                        <td><input type="text" id="invoicing_street" name="invoicing_street" value="<?php echo $partner->invoicing_street?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_PartnerType '); ?><span class="required">*</span></th>
                        <td>
                        <?php echo form_dropdown('partner_type', 
                                array(  "0" => admin_language('partner_view_admin_form_LocationPartner'), 
                                        "1" => admin_language('partner_view_admin_form_MarketingPartner'), 
                                        '2'=> admin_language('partner_view_admin_form_ServicePartner')), 
                                $partner->partner_type ,'Class="input-text" id="partner_type"');?></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_CustomerDiscount'); ?></th>
                    	<td><input type="text" id="customer_discount" name="customer_discount" value="<?php echo $partner->customer_discount?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_DurationCustomerDiscount'); ?></th>
                    	<td><input type="text" id="duration_customer_discount" name="duration_customer_discount" value="<?php echo $partner->duration_customer_discount?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_RevShare'); ?></th>
                    	<td><input type="text" id="rev_share_ad" name="rev_share_ad" value="<?php echo $partner->rev_share_ad?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_DurationRevShare'); ?></th>
                    	<td><input type="text" id="duration_rev_share" name="duration_rev_share" value="<?php echo $partner->duration_rev_share?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <tr class="service-partner <?php if($partner->partner_type != APConstants::PARTNER_SERVICE_TYPE && $partner->partner_type != APConstants::PARTNER_LOCATION_TYPE) {echo "hide";}?>"">
                    	<th><?php admin_language_e('partner_view_admin_form_ContactPerson'); ?><span class="required">*</span></th>
                    	<td><input type="text" id="main_contact_point" name="main_contact_point" value="<?php echo $partner->main_contact_point?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="service-partner <?php if($partner->partner_type != APConstants::PARTNER_SERVICE_TYPE && $partner->partner_type != APConstants::PARTNER_LOCATION_TYPE) {echo "hide";}?>"">
                    	<th><?php admin_language_e('partner_view_admin_form_Email'); ?><span class="required">*</span></th>
                    	<td><input type="text" id="email" name="email" value="<?php echo $partner->email?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_FreeBusinessPostboxBonusMonth'); ?></th>
                    	<td><input type="text" id="bonus_month" name="bonus_month" value="<?php echo $partner->bonus_month?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <?php if($action_type == 'edit'):?>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_PartnerCode'); ?></th>
                        <td><input type="text" id="partner_code" name="partner_code" value="<?php echo $partner->partner_code?>" readonly="readonly"
                            class="input-txt readonly" maxlength="50" /></td>
                    </tr>
                    <?php endif;?>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_InvoicingZipcode'); ?><span class="required">*</span></th>
                        <td><input type="text" id="invoicing_zipcode" name="invoicing_zipcode" value="<?php echo $partner->invoicing_zipcode?>"
                            class="input-txt" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_InvoicingCity'); ?><span class="required">*</span></th>
                        <td><input type="text" id="invoicing_city" name="invoicing_city" value="<?php echo $partner->invoicing_city?>"
                            class="input-txt" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_InvoicingRegion'); ?><span class="required">*</span></th>
                        <td><input type="text" id="invoicing_region" name="invoicing_region" value="<?php echo $partner->invoicing_region?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('partner_view_admin_form_Country'); ?><span class="required">*</span></th>
                        <td><select id="invoicing_country" name="invoicing_country" class="input-text">
                            <?php foreach ($countries as $country):?>
                                    <option value="<?php echo $country->id?>" <?php if ($partner->invoicing_country == $country->id):?> selected="selected" <?php endif;?>>
                                        <?php echo $country->country_name?>
                                    </option>
                             <?php endforeach;?>
                            </select></td>
                    </tr>
                    
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_PartnerDomain'); ?></th>
                    	<td><input type="text" id="partner_domain" name="partner_domain" value="<?php echo $partner->partner_domain?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    
                    <tr class="service-partner <?php if($partner->partner_type != APConstants::PARTNER_SERVICE_TYPE && $partner->partner_type != APConstants::PARTNER_LOCATION_TYPE ) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_PhoneNumber'); ?><span class="required">*</span></th>
                    	<td><input type="text" id="phone" name="phone" value="<?php echo $partner->phone?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_ForRegistration'); ?><?php echo APConstants::MONEY_UNIT;?></th>
                    	<td><input type="text" id="registration" name="registration" value="<?php echo $partner->registration?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_ForActivation'); ?><?php echo APConstants::MONEY_UNIT;?></th>
                    	<td><input type="text" id="activation" name="activation" value="<?php echo $partner->activation?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <tr class="marketing-partner <?php if($partner->partner_type != APConstants::PARTNER_MARKETING_TYPE) {echo "hide";}?>">
                    	<th><?php admin_language_e('partner_view_admin_form_BonusLocation'); ?></th>
                    	<td>
                            <?php 
                                echo my_form_dropdown(array(
									"data" => $locations,
									"value_key" => 'id',
									"label_key" => 'location_name',
									"value" => $partner->bonus_location,
									"name" => 'bonus_location',
									"id"    => 'bonus_location',
									"clazz" => 'input-text',
									"style" => '',
									"has_empty" => true
								));
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php if($action_type == 'edit'){?>
        <tr>
            <td colspan="2" style="text-align: center; color:red"><?php admin_language_e('partner_view_admin_form_ChangeDataNotification'); ?></td>
        </tr>
        <?php }?>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" /> <input type="hidden" id="h_partner_id"
        name="id" value="<?php echo $partner->partner_id?>" /> <input type="hidden" id="h_partner_code" name="partner_code"
        value="<?php echo $partner->partner_code?>" />
</form>
<script type="text/javascript">

$(document).ready( function() {

	change();
	
    $("#partner_type").change(function(){
        change();
    });

    function change(){
    	if($("#partner_type").val() == "1" ){
            $('.marketing-partner').removeClass('hide');
            $('.service-partner').addClass('hide');
        }else if ($("#partner_type").val() == "2" || $("#partner_type").val() == "0") {
        	$('.marketing-partner').addClass('hide');
        	$('.service-partner').removeClass('hide');
        } else {
        	$('.marketing-partner').addClass('hide');
        	$('.service-partner').addClass('hide');
        }
    }
});
</script>