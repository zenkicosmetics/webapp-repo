<style>
.shipping_table td, .shipping_table th {
    padding:1px;
}
.mailbox .input-txt-shipping{
    height: 20px;
}

#shippingEnvelopeFormContainer a:focus {
    background-color: transparent;
}

</style>
    
<div id="shippingEnvelopeFormContainer" class="ym-grid mailbox" style="min-width: 900px">
    <form id="shippingEnvelopeForm" action="<?php echo base_url()?>scans/todo/shipping" method="post">
        <div class="ym-g40 ym-gl" style="width: 525px">
        	<table id="popupShippingItemDataGridResult"></table>
	        <div id="popupShippingItemDataGridPager"></div>
	        <div class="ym-clearfix"></div>
	        <div id="shippingLabelPreview" style="width: 500px; height: 280px;border: 1px solid #DADADA;border-radius: 3px; margin-top: 5px">
	            <iframe id="shippingLabelPreviewIframe" style="width: 100%; height: 100%; border: none" src=""></iframe>
	        </div>
	        
        </div>
        <div class="ym-g40 ym-gl" style="width: 470px">
            <div class="ym-gl input-cols" style="width: 470px">
                <div class="ym-grid input-item">
                    <div class="ym-gl" style="width: 470px; margin-top: -10px;">
                        <h2 style="font-size: 16px; font-weight: bold; float: left">Shipping Address</h2>
                        <span style="float:right; margin-top: 10px"><a style="text-decoration: underline; color: #0000FF" href="#" id="shippingEnvelopeForm_editCopyButton">Save</a></span>
                    </div>
                </div>
                <div class="ym-clearfix"></div>
                <div class="ym-grid input-item">
                    <table class="shipping_table" id="shippingEnvelopeForm_copyDiv" style="width: 100%; margin-top: 0.4em">
                        <tr>
                            <td style="width: 100px;">
                                <div>Name </div>
                                <div>Company </div>
                                <div>Street </div>
                                <div>Post Code - City </div>
                                <div>Region </div>
                                <div>Country </div>
                            </td>
                            <td>
                                <textarea id="shippingEnvelopeForm_copyContentDiv" readonly="readonly"
                                          style="height: 114px;width: 354px;resize: none;padding: 5px; border: 1px solid #dadada;line-height: 18px;"></textarea>
                            </td>
                        </tr>
                    </table>
                    <table class="shipping_table hide"  id="shippingEnvelopeForm_editDiv" style="width: 100%; margin-top: 0.4em">
                        <tr>
                            <td style="width: 100px;">Name</td>
                            <td>
                                <input class="input-txt input-txt-shipping" name="shipment_address_name" id="shipment_address_name" type="text"
                                    value="<?php echo !empty($customer_address) ? $customer_address->shipment_address_name : "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Company</td>
                            <td>
                                <input class="input-txt input-txt-shipping" name="shipment_company" id="shipment_company" type="text"
                                    value="<?php echo !empty($customer_address)? $customer_address->shipment_company : "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Street</td>
                            <td>
                                <input class="input-txt input-txt-shipping" name="shipment_street" id="shipment_street" type="text"
                                        value="<?php echo !empty($customer_address) ? $customer_address->shipment_street: "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Post Code - City</td>
                            <td>
                                <input class="input-txt input-txt-shipping" name="shipment_postcode" id="shipment_postcode" type="text" style="width: 146px"
                                        value="<?php echo !empty($customer_address) ? $customer_address->shipment_postcode: "";?>" />
                                - <input class="input-txt input-txt-shipping" name="shipment_city" id="shipment_city" type="text" style="width: 204px"
                                        value="<?php echo !empty($customer_address) ? $customer_address->shipment_city: "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Region</td>
                            <td>
                                <input class="input-txt input-txt-shipping" name="shipment_region" id="shipment_region" type="text" 
                                        value="<?php echo !empty($customer_address) ? $customer_address->shipment_region : "";?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td>
                                <?php echo my_form_dropdown(array(
                                    "data" => $countries,
                                    "value_key" => 'id',
                                    "label_key" => 'country_name',
                                    "value" => ( !empty($customer_address) ? $customer_address->shipment_country : 0),
                                    "name" => 'shipment_country',
                                    "id" => 'shipment_country',
                                    "clazz" => 'input-width',
                                    "style" => 'width: 100%; padding: 3px;',
                                    "has_empty" => false,
                                    "option_default" => ''
                                )); ?>
                            </td>
                        </tr>
                        
                         
                    </table>
                </div>
                <div class="ym-clearfix"></div>
                <div class="ym-grid input-item">
                    <div class="ym-gl" style="width: 470px; margin-left: 4px">
                        Phone Number: &nbsp;<input class="input-txt" style="height: 25px; width: 200px;" name="shipment_phone_number" id="shipment_phone_number" type="text"
                            value="<?php echo !empty($customer_address) ? $customer_address->shipment_phone_number : ""; ?>" />
                        <button id="customer_info" style="width: 120px;margin-left: 42px;">Customer Info</button>
                        <a href="javascrip:void()" id="set_pre_payment" style="text-decoration: underline; color: #0000FF;margin-left: 0px;margin-top: 12px;float: left;margin-bottom: 4px;">»Require pre-payment for this shipment»</a>
                    </div>
                </div>
            </div>
            <div class="ym-grid input-item">
                <table class="shipping_table" style="width: 100%; margin-top: 0.4em">
                    <tr>
                        <td>
                            Selected shipping service: 
                            <span><?php echo my_form_dropdown(array(
                                "data" => $servicesAvailbale,
                                "value_key" => 'id',
                                "label_key" => 'name',
                                "value" => $selected_shipping_service_id,
                                "name" => 'shipping_service_id',
                                "id"    => 'shippingEnvelopeForm_shipping_service_id',
                                "clazz" => 'input-width',
                                "style" => 'width: 200px',
                                "has_empty" => false
                            ));?></span>
                            <span id="shipping_service_no_tracking" style="color: red; display: none; margin-left: 10px;"
                                  title="The customer has explicitly confirmed to send this item without tracking information. Please use this shipping service without tracking information." class="tipsy_tooltip" >No tracking!</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ym-grid input-item">
                <!-- Load by ajax script -->
                <span id="loading-icon" class="icon-process" style="display: block; margin: 50px auto"></span>
                <div id="shippingEnvelopeForm_shippingServiceForm" style="display: none;"></div>
            </div>
    		
    	</div>                                                                            
        
        <input name="location_id" id="location_id" type="hidden"  value="<?php echo $location_id?>" />        
    	<input type="hidden" id="shippingEnvelopeForm_customer_id" name="customer_id" value="<?php echo $customer_id?>" />
    	<input type="hidden" id="shippingEnvelopeForm_envelope_id" name="envelope_id" value="<?php echo $envelope_id?>" />
    	<input type="hidden" id="shippingEnvelopeForm_postbox_id" name="postbox_id" value="<?php echo $postbox_id?>" />
    	<input type="hidden" id="shippingEnvelopeForm_package_id" name="package_id" value="<?php echo $package_id?>" />
    	<input type="hidden" id="shippingEnvelopeForm_package_size" name="package_size" value="<?php echo $ppl[0][2]?>" />
    	<input type="hidden" id="shippingEnvelopeForm_package_price_id" name="package_price" value="<?php echo $ppl[0][3]?>" />
    	<input type="hidden" id="shippingEnvelopeForm_shipping_type" name="shipping_type_id" value="<?php echo $shipping_type?>" />
    	<input type="hidden" id="shippingEnvelopeForm_shipment_type" name="shipment_type_id" value="<?php echo $shipping_type?>" />
    	<input type="hidden" id="shippingEnvelopeForm_estamp_url" name="estamp_url" value="" />
    	<!-- <input type="hidden" id="shippingEnvelopeForm_envelope_ids" name="envelope_ids" value="" />  -->
    	<input type="hidden" id="shippingEnvelopeForm_current_scan_type" name="current_scan_type" value="" />
    	<input type="hidden" id="shippingEnvelopeForm_current_view_type" name="current_view_type" value="" />
        <input type="hidden" id="multiple_quantity" name="multiple_quantity" class="input-width" value=""/>
        <input type="hidden" id="multiple_length" name="multiple_length" class="input-width" value=""/>
        <input type="hidden" id="multiple_width" name="multiple_width" class="input-width" value=""/>
        <input type="hidden" id="multiple_height" name="multiple_height" class="input-width" value="" />
        <input type="hidden" id="multiple_weight" name="multiple_weight" class="input-width" value=""/>
        <input type="hidden" id="shippingEnvelopeForm_tracking_number" name="shippingEnvelopeForm_tracking_number" class="input-width" value=""/>
    
	</form>
</div>
<div class="hide">
    <div id="packageLetterSizeWindow" title="Select Package Letter Size" class="input-form dialog-form">
	</div>
	<div id="viewCustomsDetail" title="View Customs Detail" class="input-form dialog-form">
	</div>
    <div id="windowCustomerInfo" title="Customer Infomation" class="input-form dialog-form">
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="display_pdf_invoice" class="iframe" href="#">Display Proforma Invoice</a>
</div>
<!-- Content for dialog -->
<div style="display:none">
    <a id="view_custom_file" class="iframe" href="">Preview file</a>
</div>

<?php Asset::js('html2canvas.js'); ?>
<?php Asset::js('jquery.plugin.html2canvas.js'); ?>
<?php Asset::js('jquery.printelement.min.js'); ?>
<?php echo Asset::render(); ?>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/scans/js/PrepareShipping.js"></script>
<script type="text/javascript">
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    $('button').button();
    var mappingShippingServiceToTemplate = {};
    var mappingShippingServiceToNoTracking = {};
    <?php foreach ($servicesAvailbale as $shipping_service) {?>
        mappingShippingServiceToTemplate['<?php echo $shipping_service->id?>'] = '<?php echo $shipping_service->shipping_service_template?>';
        mappingShippingServiceToNoTracking['<?php echo $shipping_service->id?>'] = '<?php echo $shipping_service->tracking_information_flag?>';
    <?php }?>
    
    var printLabelEnable = false;
    var weight_unit = "<?php echo $weight_unit?>";
    var baseUrl = '<?php echo base_url(); ?>',
            rowNum = '<?php echo APContext::getAdminPagingSetting();?>',
            rowList = [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>];
    PrepareShipping.init(baseUrl, rowNum, rowList);
    
    checkEditCopyLink($("#shippingEnvelopeForm_editCopyButton"));
    $("#shippingEnvelopeForm_editCopyButton").click(function(e){
        e.preventDefault();
        checkEditCopyLink(this);
        return false;
    });
    
    $("#shippingEnvelopeForm_copyContentDiv").focus(function(){
        $("#shippingEnvelopeForm_copyContentDiv").select();
    });
    
    function checkEditCopyLink(obj){
        var text = $(obj).text();
        if(text == 'Edit'){
            $(obj).text("Save");
            $("#shippingEnvelopeForm_editDiv").show();
            $("#shippingEnvelopeForm_copyDiv").hide();
        }else{
            $(obj).text("Edit");
            var text = "";
            text += $("#shipment_address_name").val() + "\n";
            text += $("#shipment_company").val() + "\n";
            text += $("#shipment_street").val() + "\n";
            text += $("#shipment_postcode").val() + " - "+ $("#shipment_city").val() + "\n";
            text += $("#shipment_region").val() + "\n";
            text += $("#shipment_country option:selected").text();
            
            $("#shippingEnvelopeForm_copyContentDiv").text(text);
            $("#shippingEnvelopeForm_copyDiv").show();
            $("#shippingEnvelopeForm_editDiv").hide();
        }
    }
</script>