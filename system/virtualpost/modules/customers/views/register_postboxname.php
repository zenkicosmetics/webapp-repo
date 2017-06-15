<style type="text/css">
    tbody th{
        border: none;
    }
</style>
<form id="saveRegisterPostboxNameForm" action="<?php echo base_url().'customers/save_postboxname';?>" method="post">
    <table border="0px" style="border: none; width: 100%">
        <tr>
            <th>Select a city for your postbox <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $locate,
                    "value_key" => 'id',
                    "label_key" => 'location_name',
                    "value" => $postbox->location_available_id,
                    "name" => 'location_available_id',
                    "id"    => 'saveRegisterPostboxNameForm_location_available_id',
                    "clazz" => 'input-txt-none',
                    'show_only_express_shipping' => "1",
                    "style" => '',
                    "has_empty" => false
                 ));?>
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="location_available_id_tipsy_tooltip" 
                  title="Please select the city in which you want to open your new postbox"></span>
            </td>
        </tr>
        
        <tr>
            <th>Select your postbox type <span class="required">*</span></th>
            <td>
                <?php 
                    if(!APContext::isEnterpriseCustomer()){
                        echo code_master_form_dropdown(array(
                            "code" => APConstants::ACCOUNT_TYPE,
                            "value" => '1',
                            "name" => 'account_type',
                            "id"    => 'saveRegisterPostboxNameForm_account_type',
                            "clazz" => 'input-txt-none',
                            "style" => '',
                            "has_empty" => false
                        ));
                    }else{
                        echo '<select id="saveRegisterPostboxNameForm_account_type" name="account_type" class="input-txt-none"><option value="5">Enterprise</option></select>';
                    }
                ?>
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="location_available_id_tipsy_tooltip" 
                    title="The “As You Go” account is free for the first 6 months. The “Business Account” has a base price that includes more activities, and the price per any additional activity is lower."></span>
                <span id="saveRegisterPostboxNameForm_as_you_go_hightlight">0 EUR postbox fee for 6 months <br/>
                    <a href="#" id="saveRegisterPostboxNameForm_pricing_button" style="float: right; margin-right: 70px;" class="main_link_color">See detailed pricing here...</a></span>
            </td>
        </tr>
        
        <tr>
            <th colspan="2">Enter the name of the first* mail recipient:</th>
        </tr>
        
        <tr>
            <th>Name: </th>
            <td>
                <input class="input-txt-none" name="address_name" type="text"
                            value="<?php if (!empty($postbox->name)) {  echo $postbox->name;} else { if (!empty($address->invoicing_address_name)) { echo $address->invoicing_address_name; }}?>"  />
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="address_name_tipsy_tooltip" 
                      title="A full name (first and surname) and/or company name of the mail recipient is needed. Filling one of the two fields is required. 
                      The exact name is important to be able to assig the incoming postal items to your postbox."></span>
            </td>
        </tr>
        
        <tr>
            <th>Company: </th>
            <td>
                <input class="input-txt-none"  name="address_company_name" type="text"
                    value="<?php if (!empty($postbox->company)) { echo $postbox->company;} else { if (!empty($address->invoicing_company)) { echo $address->invoicing_company;}}?>"  />
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="address_company_name_tipsy_tooltip" 
                      title="A full name (first and surname) and/or company name of the mail recipient is needed. Filling one of the two fields is required.
                      The exact name is important to be able to assig the incoming postal items to your postbox."></span>
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
                <br/> <br/><br/>
                *You can add additional postboxes in your account after the setup process.
            </td>
        </tr>
    </table>
    <input id="saveRegisterPostboxNameForm_postbox_name_id" class="input-txt-none" name="postbox_name" maxlength="35" 
           type="hidden" value="<?php echo $postbox->postbox_name?>" style="width: 280px" />
    <input type="hidden" value="1" name="first_location_flag" />
</form>
<div class="hide" style="display: none">
    <div id="saveRegisterPostboxNameFormPriceList" title="Price list" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){

	$('.tipsy_tooltip').tipsy({gravity: 'sw'});
    
	//$(".tipsy_tooltip" ).each(function( index ) {
	//	$(this).tipsy("show");
	//});
	//setTimeout(function() {
	//	$(".tipsy_tooltip" ).each(function( index ) {
	//		$(this).tipsy("hide");
	//	});
	//},2000);

	/**
	 * When change location avaiable id
	 */
	$('#saveRegisterPostboxNameForm_location_available_id').live('change', function() {
		load_location_info();
	});
	load_location_info();
    
    $("#saveRegisterPostboxNameForm_account_type").live('change', function(){
        if($(this).val() == "1"){
            $("#saveRegisterPostboxNameForm_as_you_go_hightlight").show();
        }else{
            $("#saveRegisterPostboxNameForm_as_you_go_hightlight").hide();
        }
    });
    
    $("#saveRegisterPostboxNameForm_pricing_button").click(function(){
        $("#saveRegisterPostboxNameFormPriceList").html("");
        
        // Open new dialog
        $('#saveRegisterPostboxNameFormPriceList').openDialog({
            autoOpen: false,
            height: 550,
            width: 1100,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>customers/load_price_list", function() {
                    //$('#addEditLocationForm_LocationName').focus();
                });
            }
        });

        $('#saveRegisterPostboxNameFormPriceList').dialog('option', 'position', 'center');
        $('#saveRegisterPostboxNameFormPriceList').dialog('open');
    });
    
    /**
     * Load location information
     */
    function load_location_info() {
        var location_available_id = $('#saveRegisterPostboxNameForm_location_available_id').val();
        if (location_available_id == '') {
            return;
        }
        var submitUrl = '<?php echo base_url()?>customers/get_auto_postbox_name?location_available_id=' + location_available_id;
        $.ajaxExec({
             url: submitUrl,
             success: function(data) {
                 if (data.status) {
                     $('#saveRegisterPostboxNameForm_postbox_name_id').val(data.data.postbox_name);
                     //$('#postboxname_street').html(data.data.location.street);
                     //$('#postboxname_postcode').html(data.data.location.postcode);
                     //$('#postboxname_city').html(data.data.location.city);
                     //$('#postboxname_country').html(data.data.location.country);
                     //$('#postboxname_phone').html(data.data.location.phone_number);
                     //$('#postboxname_email').html(data.data.location.email);
                 } else {
                        $.displayError(data.message);
                 }
             }
         });
    }
});
</script>

