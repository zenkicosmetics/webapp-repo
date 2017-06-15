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
                        <th>Partner name <span class="required">*</span></th>
                        <td><input type="text" id="partner_name" name="partner_name" value="<?php echo $partner->partner_name?>"
                            class="input-txt" maxlength="50" /></td>
                    </tr>
                    <!-- 
                    <tr>
                        <th>Location Street <span class="required">*</span></th>
                        <td><input type="text" id="location_street" name="location_street" value="<?php echo $partner->location_street?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <th>Location Zipcode <span class="required">*</span></th>
                        <td><input type="text" id="location_zipcode" name="location_zipcode" value="<?php echo $partner->location_zipcode?>"
                            class="input-txt" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <th>Location City <span class="required">*</span></th>
                        <td><input type="text" id="location_city" name="location_city" value="<?php echo $partner->location_city?>"
                            class="input-txt" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <th>Location Region <span class="required">*</span></th>
                        <td><input type="text" id="location_region" name="location_region" value="<?php echo $partner->location_region?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                     
                    <tr>
                        <th>Country <span class="required">*</span></th>
                        <td><select id="location_country" name="location_country" class="input-text">
                            <?php foreach ($countries as $country):?>
                                    <option value="<?php echo $country->id?>" <?php if ($partner->location_country == $country->id):?> selected="selected" <?php endif;?>>
                                        <?php echo $country->country_name?>
                                    </option>
                             <?php endforeach;?>
                            </select></td>
                    </tr>
                    -->
                    <tr>
                        <th>Company name <span class="required">*</span></th>
                        <td><input type="text" id="company_name" name="company_name" value="<?php echo $partner->company_name?>"
                            class="input-txt" maxlength="50" /></td>
                    </tr>
                    <tr>
                        <th>Invoicing street <span class="required">*</span></th>
                        <td><input type="text" id="invoicing_street" name="invoicing_street" value="<?php echo $partner->invoicing_street?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <th>Prepay charge <span class="required">*</span></th>
                        <td><input type="text" id="threhold_for_direct_prepay_charge" name="threhold_for_direct_prepay_charge"
                            value="<?php echo $partner->threhold_for_direct_prepay_charge?>" class="input-txt" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <th>Partner type <span class="required">*</span></th>
                        <td>
                        <?php echo form_dropdown('partner_type', array("0" => 'Location partner', "1" => 'Marketing partner', '3'=> 'Service partner'), $partner->partner_type ,'Class="input-text" id="partner_type"');?></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != '1') {echo "hide";}?>">
                    	<th>Customer discount (%) </th>
                    	<td><input type="text" id="customer_discount" name="customer_discount" value="<?php echo $partner->customer_discount?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != '1') {echo "hide";}?>"">
                    	<th>Partner domain </th>
                    	<td><input type="text" id="partner_domain" name="partner_domain" value="<?php echo $partner->partner_domain?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    
                    <tr>
                        <th>Invoicing zipcode <span class="required">*</span></th>
                        <td><input type="text" id="invoicing_zipcode" name="invoicing_zipcode" value="<?php echo $partner->invoicing_zipcode?>"
                            class="input-txt" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <th>Invoicing city <span class="required">*</span></th>
                        <td><input type="text" id="invoicing_city" name="invoicing_city" value="<?php echo $partner->invoicing_city?>"
                            class="input-txt" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <th>Invoicing region <span class="required">*</span></th>
                        <td><input type="text" id="invoicing_region" name="invoicing_region" value="<?php echo $partner->invoicing_region?>"
                            class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <th>Country <span class="required">*</span></th>
                        <td><select id="invoicing_country" name="invoicing_country" class="input-text">
                            <?php foreach ($countries as $country):?>
                                    <option value="<?php echo $country->id?>" <?php if ($partner->invoicing_country == $country->id):?> selected="selected" <?php endif;?>>
                                        <?php echo $country->country_name?>
                                    </option>
                             <?php endforeach;?>
                            </select></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != '1') {echo "hide";}?>"">
                    	<th>Rev-share Ad</th>
                    	<td><input type="text" id="rev_share_in_percent" name="rev_share_in_percent" value="<?php echo $partner->rev_share_in_percent?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    <tr class="marketing-partner <?php if($partner->partner_type != '1') {echo "hide";}?>"">
                    	<th>Duration rev-share</th>
                    	<td><input type="text" id="duration_rev_share" name="duration_rev_share" value="<?php echo $partner->duration_rev_share?>" class="input-txt" maxlength="255" /></td>
                    </tr>
                    
                    <!-- 
                    <tr>
                        <th>Price Model <span class="required">*</span></th>
                        <td>
                            <?php
                                echo my_form_dropdown ( array (
                                        "data" => $price_model,
                                        "value_key" => 'id',
                                        "label_key" => 'name',
                                        "value" => $partner->price_model,
                                        "name" => 'price_model',
                                        "id" => 'price_model',
                                        "clazz" => 'input-text',
                                        "style" => '',
                                        "has_empty" => false 
                                ) );
                            ?>
                        </td>
                    </tr>
                     -->
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" /> <input type="hidden" id="h_partner_id"
        name="partner_id" value="<?php echo $partner->partner_id?>" /> <input type="hidden" id="h_partner_code" name="partner_code"
        value="<?php echo $partner->partner_code?>" />
</form>
<script type="text/javascript">

$(document).ready( function() {

    $("#partner_type").change(function(){
        if($("#partner_type").val() == "1"){
            $('.marketing-partner').removeClass('hide');
        }else{
        	$('.marketing-partner').addClass('hide');
        }
    });
});
</script>