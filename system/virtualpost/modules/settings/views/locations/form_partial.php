<style>
    .input-custom { 
        width: 267px !important; 
    }
    #addEditLocationForm hr{
       padding: 0px;
       margin: 0px;
       background: blue;
       border-color: blue;
    }
    
    /*.invisible {display: none;}*/
</style>
<?php
if (empty($location_admin_page)){
    $location_admin_page = false;
}
?>
<form id="addEditLocationForm" method="post" action="<?php echo $submit_url ?>" enctype="multipart/form-data" autocomplete="on">
    <table>
        <tr>
            <td style="width: 50%">
                <table>
                    <!-- first group --->
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_LocationName'); ?><span class="required">*</span></th>
                        <td><input type="text" id="addEditLocationForm_LocationName" name="location_name" 
                                   value="<?php echo $location->location_name; ?>" class="input-width custom_autocomplete input-custom" maxlength="60"/></td>
                    </tr>
                    
                    <?php if(!empty($location_admin_page) && $action_type != 'add') {?>
                    <tr>
                        <th>Location Type</th>
                        <td>
                            <select class="input-txt-none" name="location_type" style="width: 80px">
                                <option value="0">Clevver location</option>
                                <option value="1">Enterprise Open</option>
                                <option value="2">Enterprise Closed</option>
                            </select>
                            <span>Enterprise account</span>
                            <input type="text" name="enterprise_account" style="width: 67px"  readonly="readonly"
                                   value="<?php echo !empty($location_customer) ? $location_customer->customer_code : ""; ?>" class="input-width readonly" />
                        </td>
                    </tr>
                    <?php }else if(!empty($location_admin_page)){?>
                    <tr>
                        <th colspan="2" style="height: 27px; line-height: 27px;">&nbsp;<br/></th>
                    </tr>
                    <?php }?>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    
                    <!-- second group--->
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_Street'); ?><span class="required">*</span></th>
                        <td><input type="text" id="route" name="street" value="<?php echo $location->street ?>" class="input-width custom_autocomplete input-custom" maxlength=255 /></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_Postcode'); ?><span class="required">*</span></th>
                        <td><input type="text" id="postal_code" name="postcode" value="<?php echo $location->postcode ?>" class="input-width custom_autocomplete input-custom" maxlength=10/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_City'); ?><span class="required">*</span></th>
                        <td><input type="text" id="locality" name="city" value="<?php echo $location->city ?>" class="input-width custom_autocomplete input-custom" maxlength=255/></td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_Region'); ?><span class="required">*</span></th>
                        <td><input type="text" id="administrative_area_level_1" name="region" value="<?php echo $location->region ?>" class="input-width custom_autocomplete input-custom" maxlength=255/></td>
                    </tr>
                    <tr>
                        <th style="width: 140px;"><?php admin_language_e('setting_view_location_form_Country'); ?><span class="required">*</span></th>
                        <td>
                            <?php echo my_form_dropdown(array(
                                "data" => $countries,
                                "value_key" => 'id',
                                "label_key" => 'country_name',
                                "value" => $location->country_id,
                                "name" => 'country_id',
                                "id" => 'country_id',
                                "clazz" => 'input-width input-txt-none',
                                "style" => 'width: 278px;',
                                "has_empty" => true,
                                "option_default" => 'no country'
                            )); ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Phone number<span class="required">*</span></th>
                        <td><input type="text" id="location_phone_number" name="location_phone_number" value="<?php echo $location->location_phone_number ?>"
                                   class="input-width  input-custom" maxlength="30" /></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    
                    <?php if(!empty($location_admin_page)) {?>
                    <!-- third group--->
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_PartnerName'); ?></th>
                        <td>
                            <?php echo my_form_dropdown(array(
                                "data" => $list_partner,
                                "value_key" => 'partner_id',
                                "label_key" => 'partner_name',
                                "value" => $location->partner_id,
                                "name" => 'partner_id',
                                "id" => 'partner_id',
                                "clazz" => 'input-txt-none',
                                "style" => 'width: 278px;',
                                "has_empty" => true,
                                "option_default" => 'no partner'
                            )); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_RevShare'); ?></th>
                        <td><input type="text" id="rev_share" name="rev_share" value="<?php echo $location->rev_share ?>" class="input-width input-custom" maxlength=10 /></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    <?php }?>
                    <!-- forth group-->
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_Image'); ?></th>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'imagepath',
                                'id' => 'imagepath',
                                'value' => $location->image_path
                            );
                            echo form_upload($data);
                            ?>
                            <br/><?php admin_language_e('setting_view_location_form_SelectFileToUpload'); ?>
                            <?php if(!empty($location->image_path)){ ?>
                            <br/>
                            <img src="<?php echo APContext::getAssetPath().$location->image_path ?>" style="height: 50px" />
                            <?php }?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_OfficialEmailForPostReceiving'); ?><span class="required">*</span></th>
                        <td> <input type="text" value="<?php echo empty($location->email) ? '': $location->email; ?>" 
                                    id="email" name="email" class="input-width" maxlength="255">
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_DisplayPhoneNumber'); ?></th>
                        <td> 
                            <input type="checkbox" id="phone_number_flag" name="phone_number_flag" value="1" <?php if (isset($location->phone_number_flag) && $location->phone_number_flag === '1') { ?> checked="checked" <?php } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_OfficialPhoneNumberForPostReceiving'); ?><span class="required">*</span></th>
                        <td> <input type="text" value="<?php echo empty($location->phone_number) ? '': $location->phone_number; ?>" 
                                    id="phone_number" name="phone_number" class="input-width" maxlength="30">
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_BusinessPostboxText'); ?></th>
                        <td> <input type="text" value="<?php echo empty($location->business_postbox_text) ? '': $location->business_postbox_text; ?>" 
                                    id="business_postbox_text" name="business_postbox_text" class="input-width" maxlength="64">
                        </td>
                    </tr>
                    <?php if($location_admin_page || (!$location_admin_page && $action_type != 'add')){ ?>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_DigitalDevices'); ?></th>
                        <td>
                            <?php 
                            if($location_admin_page){
                                $width = "278px";
                            }else{
                                $width = "130px";
                            }
                            if($location_admin_page){
                                echo my_form_dropdown(array(
                                    "data" => $digital_devices,
                                    "value_key" => 'id',
                                    "label_key" => 'panel_code',
                                    "value" => $location->device_id,
                                    "name" => 'device_id',
                                    "id" => 'device_id',
                                    "clazz" => 'input-width input-txt-none ',
                                    "style" => 'width: '.$width,
                                    "has_empty" => true
                                )); 
                            }else {  ?>
                                <input type="text" value="<?php echo empty($location->device_id) ? '': $location->device_id; ?>" 
                                       id="device_id" name="device_id" readonly="readonly" class="input-width readonly" style="width: 100px;" />
                                <a href="#" id="addDigitalPanelLink">Add a digital panel...</a>
                                <span class="managetables-icon icon_help tipsy_tooltip" original-title="You can add a digital touch panel, that shows your up to date postboxes at your location."></span>
                            <?php }?>
                        </td>
                    </tr>
                    <?php }?>
                    <tr>
                        <th>
                            <?php admin_language_e('setting_view_location_form_DailyReminder'); ?>
                        </th>
                        <td valign="middle" style="vertical-align: middle">
                           <input type="checkbox" id="sent_daily_reminder_flag" name="sent_daily_reminder_flag" value="1" 
                               <?php if ($location->sent_daily_reminder_flag === '1') { ?> checked="checked" <?php } ?> />
                        </td>
                    </tr>
                    <!-- fifth group-->
                    <?php if(!empty($location_admin_page) || ( empty($location_admin_page) && $action_type != 'add')) {?>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    <tr>
                        <th width="52%"><?php admin_language_e('setting_view_location_form_OfficeSpaceAvailable'); ?></th>

                        <td> <input type="checkbox" id="office_space_active_flag" name="office_space_active_flag" value="1"
                                <?php if (isset($location->office_space_active_flag) && $location->office_space_active_flag == '1') { ?>checked="checked" <?php } ?>   /> 
                        </td>
                    </tr>

                    <tr>
                        <th ><?php admin_language_e('setting_view_location_form_LocationOffersSharedOfficeSpace'); ?></th>

                        <td> <input type="checkbox" id="shared_office_space_flag" name="shared_office_space_flag" value="1"
                                <?php if ($location->shared_office_space_flag == '1') { ?>checked="checked" <?php } ?>   /> 
                        </td>
                    </tr>
                    <tr class="invisible">
                        <th><?php admin_language_e('setting_view_location_form_ImageForSharedOffice'); ?></th>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'shared_office_image_path',
                                'id' => 'shared_office_image_path',
                                'value' => $location->shared_office_image_path
                            );
                            echo form_upload($data);
                            ?>
                        </td>
                    </tr>
                    <tr class="invisible">
                        <th><?php admin_language_e('setting_view_location_form_SelectFeaturesOfSharedOffice'); ?></th>
                        <td> <input type="button" value="Open" class="input-btn" id="location_office">
                        </td>
                    </tr>
                    <tr class="invisible">
                        <th><?php admin_language_e('setting_view_location_form_EmailForBookingRequests'); ?></th>
                        <td> <input type="text" value="<?php echo empty($location->booking_email_address) ? '': $location->booking_email_address; ?>" 
                                    id="booking_email_address" name="booking_email_address" class="input-width">
                        </td>
                    </tr>
                    <?php }?>
                </table>
            </td>
            
            <td style="width: 50%">
                <table>
                    <!-- first group --->
                    <?php if(!empty($location_admin_page)) {?>
                    <tr>
                        <th style="width: 160px;">
                            <?php admin_language_e('setting_view_location_form_AvailableForPublic'); ?>
                        </th>
                        <td valign="middle" style="vertical-align: middle">
                           <input type="checkbox" id="public_flag" style="height: 20px; line-height: 20px;" name="public_flag" 
                                  value="1" <?php if ($location->public_flag === '1') { ?> checked="checked" <?php } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_OpenForExternal'); ?></th>
                        <td><input type="checkbox" id="share_external_flag"   style="height: 20px; line-height: 20px;" name="share_external_flag" 
                                   value="1" <?php if ($location->share_external_flag == '1') { ?> checked="checked" <?php } ?> /></td>
                    </tr>
                    <?php } else{?>
                    <tr>
                        <th colspan="2" style="height: 27px; line-height: 27px;">&nbsp;<br/></th>
                    </tr>
                    <?php }?>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    
                    <!-- second group --->
                    <?php if(!empty($location_admin_page)) {?>
                    <tr>
                        <th><?php admin_language_e('setting_view_location_form_ShippingFactorFL'); ?><span class="required">*</span></th>
                        <td><input type="text" id="shipping_factor_fl" name="shipping_factor_fl" value="<?php echo $location->shipping_factor_fl; ?>" class="input-width custom_autocomplete input-custom" maxlength=10/></td>
                    </tr>
                    <?php }?>
                    <tr>
                        <th>
                            <?php admin_language_e('setting_view_location_form_OnlyExpressShipping'); ?>
                        </th>
                        <td valign="middle" style="vertical-align: middle">
                           <input type="checkbox" id="only_express_shipping_flag" name="only_express_shipping_flag" value="1" <?php if ($location->only_express_shipping_flag === '1') { ?> checked="checked" <?php } ?> />
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><?php admin_language_e('setting_view_location_form_AvailableShippingServices'); ?> 
                            <span><input id="addEditLocationForm_searchAvailableShippingServices" value="" placeholder="search"
                                         style="width: 200px; float: right" type="text" class="input-width"/></span></th>
                    </tr>
                    
                    <tr>
                        <td colspan="2" style="padding: 0 !important;">
                            <table style="width:400px;">
                                <tr>
                                    <td>
                                        <select multiple id="all_shipping_services" class="input-txt-none" style="width: 200px; height:135px">
                                            <?php foreach($shipping_services as $shipping_service): ?>
                                            <option value="<?php echo $shipping_service->id; ?>" title="<?php echo $shipping_service->name; ?>"><?php echo $shipping_service->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td valign="middle" style="vertical-align: middle">
                                        <button type="button" id="btnAddShippingService"> &gt;&gt;</button>
                                        <br/>
                                        <button type="button" id="btnRemoveShippingService"> &lt;&lt;</button>
                                    </td>
                                    <td>
                                        <select multiple name="available_shipping_services[]" id="available_shipping_services" class="input-txt-none" style="width: 201px; height:135px">
                                            <?php foreach($location_shipping_services as $shipping_service): ?>
                                                <option selected="selected" value="<?php echo $shipping_service->id; ?>" title="<?php echo $shipping_service->name; ?>"><?php echo $shipping_service->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php if ($action_type != 'add') {?>
                    <tr>
                        <th>Standard national letter shipping</th>
                        <td>
                            <select id="primary_letter_shipping" name="primary_letter_shipping" class="input-width" style="width:278px">

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Standard international letter shipping</th>
                        <td>
                            <select id="primary_international_letter_shipping" name="primary_international_letter_shipping" class="input-width" style="width:278px">

                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Standard national parcel service</th>
                        <td>
                            <select id="standard_national_parcel_service" name="standard_national_parcel_service" class="input-width" style="width:278px">

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Standard international parcel service</th>
                        <td>
                            <select id="standard_international_parcel_service" name="standard_international_parcel_service" class="input-width" style="width:278px">

                            </select>
                        </td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    
                    <!-- third group --->
                    <tr>
                        <th colspan="2">
                            <?php admin_language_e('setting_view_location_form_TypeAvailable'); ?>
                            <span><input id="addEditLocationForm_searchListTypeAvailable" value="" placeholder="search"
                                         style="width: 200px; float: right" type="text" class="input-width"/></span>
                         </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 0 !important;">
                            <table style="width:400px;margin-top: -12px;">
                                <tr>
                                    <td>
                                        <select multiple id="list_type" class="input-txt-none" style="width: 200px; height:100px">
                                            <?php foreach($listType as $type): ?>
                                                <option value="<?php echo $type->ActualValue; ?>"><?php echo $type->LabelValue; ?></option>
                                            <?php endforeach; ?>
                                        </select> 
                                    </td>
                                    <td valign="middle" style="vertical-align: middle">
                                        <button type="button" id="btnAddType"> &gt;&gt;</button>
                                        <br/>
                                        <button type="button" id="btnRemoveType"> &lt;&lt;</button>
                                    </td>
                                    <td>
                                        <select multiple name="list_type_available[]" id="list_type_available" class="input-txt-none" style="width: 201px; height:100px">
                                            <?php if(count($list_type_available)){ foreach($list_type_available as $type_available): ?>
                                                <option value="<?php echo $type_available->ActualValue; ?>"><?php echo $type_available->LabelValue; ?></option>
                                            <?php endforeach; } ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php if(!empty($location_admin_page) ) {?>
                    <!-- forth group-->
                     <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    <tr>
                        <th colspan="2"><?php admin_language_e('setting_view_location_form_PricingTemplate'); ?><span class="required">*</span>
                            <span><input id="addEditLocationForm_searchPricingTemplate" value="" placeholder="search"
                                         style="width: 200px; float: right" type="text" class="input-width"/></span>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:0 !important;">
                            <table style="width:400px;">
                                <tr>
                                    <td>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $pricing_templates,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => isset($location->pricing_template_id) ? $location->pricing_template_id : '',
                                            "name" => 'pricing_template_id1',
                                            "id" => 'pricing_template_id1',
                                            "clazz" => 'input-width input-txt-none',
                                            "style" => 'width: 200px; height:135px',
                                            "has_empty" => false,
                                            "html_option" => 'multiple = "true"'
                                        )); ?>
                                    </td>
                                    <td valign="middle" style="vertical-align: middle">
                                        <button type="button" id="addButton"> &gt;&gt;</button>
                                        <br/>
                                        <button type="button" id="removeButton"> &lt;&lt;</button>
                                    </td>
                                    <td>
                                        <?php echo my_form_dropdown(array(
                                            "data" => $pricing_templates_list,
                                            "value_key" => 'id',
                                            "label_key" => 'name',
                                            "value" => isset($location->pricing_template_id) ? $location->pricing_template_id : '',
                                            "name" => 'pricing_template_id[]',
                                            "id" => 'pricing_template_id',
                                            "clazz" => 'input-width input-txt-none',
                                            "style" => 'width: 201px; height:135px',
                                            "has_empty" => false,
                                            "html_option" => 'multiple = "true" required="required"'
                                        )); ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php }?>
                    
                </table>
            </td>
        </tr>
    </table>
    
    
    <input type="hidden" id="business_concierge_flag" name="business_concierge_flag" value="<?php echo isset($location_office) && is_object($location_office)? $location_office->business_concierge_flag:0;?>" />
    <input type="hidden" id="video_conference_flag" name="video_conference_flag" value="<?php echo isset($location_office) && is_object($location_office) ? $location_office->video_conference_flag:0;?>" />
    <input type="hidden" id="meeting_rooms_flag" name="meeting_rooms_flag" value="<?php echo isset($location_office) && is_object($location_office)? $location_office->meeting_rooms_flag:0;?>" />
    
    <input type="hidden" id="office_feature_1" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=1) && is_object($list_location_office_feature[0])? $list_location_office_feature[0]->feature_name:'';?>" />
    <input type="hidden" id="office_feature_2" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=2) && is_object($list_location_office_feature[1])? $list_location_office_feature[1]->feature_name:'';?>" />
    <input type="hidden" id="office_feature_3" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=3) && is_object($list_location_office_feature[2])? $list_location_office_feature[2]->feature_name:'';?>" />
    <input type="hidden" id="office_feature_4" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=4) && is_object($list_location_office_feature[3])? $list_location_office_feature[3]->feature_name:'';?>" />
    <input type="hidden" id="office_feature_5" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=5) && is_object($list_location_office_feature[4])? $list_location_office_feature[4]->feature_name:'';?>" />
    <input type="hidden" id="office_feature_6" name="feature[]" value="<?php echo isset($list_location_office_feature) && (count($list_location_office_feature)>=6) && is_object($list_location_office_feature[5])? $list_location_office_feature[5]->feature_name:'';?>" />
    
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type; ?>"/>
    <input type="hidden" id="h_location_id" name="id" value="<?php echo $location->id; ?>"/>
    <input type="hidden" id="imagepath_id" name="imagepath_filename" value=""/>
    <input type="hidden" id="shared_office_image_path_id" name="imagepath_shared_office" value=""/>

    <input type="hidden" id="h_primary_letter_shipping" name="h_primary_letter_shipping" value="<?php echo $location->primary_letter_shipping ?>"/>
    <input type="hidden" id="h_primary_international_letter_shipping" name="h_primary_international_letter_shipping" value="<?php echo $location->primary_international_letter_shipping ?>"/>
    <input type="hidden" id="h_standard_national_parcel_service" name="h_standard_national_parcel_service" value="<?php echo $location->standard_national_parcel_service ?>"/>
    <input type="hidden" id="h_standard_international_parcel_service" name="h_standard_international_parcel_service" value="<?php echo $location->standard_international_parcel_service ?>"/>
</form>

<div class="hide">
    <div id="window_location_office" title="Location Office Feature" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#addEditLocationForm_searchAvailableShippingServices").keyup(function(){
        var input = $(this).val().toUpperCase();
        $("#all_shipping_services > option").each(function() {
            if($(this).html().toUpperCase().indexOf(input) == -1){
                $(this).hide(); 
            }else{
                $(this).show();
            }
        });
    });
    
    $("#addEditLocationForm_searchListTypeAvailable").keyup(function(){
        var input = $(this).val().toUpperCase();
        $("#list_type > option").each(function() {
            if($(this).html().toUpperCase().indexOf(input) == -1){
                $(this).hide(); 
            }else{
                $(this).show();
            }
        });
    });
    
    $("#addEditLocationForm_searchPricingTemplate").keyup(function(){
        var input = $(this).val().toUpperCase();
        $("#pricing_template_id1 > option").each(function() {
            if($(this).html().toUpperCase().indexOf(input) == -1){
                $(this).hide(); 
            }else{
                $(this).show();
            }
        });
    });
});
</script>