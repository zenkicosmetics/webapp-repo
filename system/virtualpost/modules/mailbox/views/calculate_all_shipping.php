<?php if(!empty($allServicesRates)){ ?>
<table style="width: 100%; margin: 0px 0px 10px 0px;"> 
    <tbody>
        <tr> 
            <td style="font-weight: bold" width='80px'>Shipping To:</td> 
            <td>
                <?php
                    $shippingAddress = "";
                    if(!empty($customers_address->shipment_address_name)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_address_name)).", ";
                    } 
                    if(!empty($customers_address->shipment_company)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_company)).", ";
                    } 
                    if(!empty($customers_address->shipment_street)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_street)).", ";
                    } 
                    if(!empty($customers_address->shipment_postcode)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_postcode)).", ";
                    } 
                    if(!empty($customers_address->shipment_city)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_city)).", ";
                    } 
                    if(!empty($customers_address->shipment_region)){
                        $shippingAddress .= ucwords(strtolower($customers_address->shipment_region)).", ";
                    } 
                    if(!empty($customers_address->country_name)){
                        $shippingAddress .= ucwords(strtolower($customers_address->country_name));
                    } 
                    echo $shippingAddress;
                ?>
            </td>
            <td width='100px'><a style="color:blue" data-envelope-id="<?php echo $target_envelope_id; ?>" href="#" id="changeAddressLink">Change address</a></td>
        </tr>
    </tbody> 
</table>
<?php
    $exist_netcharge = false;
    foreach($listShippingServices as $listShippingService) {
        if (!empty($allServicesRates[$listShippingService->id]['total_charge'])) {
            $exist_netcharge = true;
        }
    }
?>
<div style='border: 1px solid #d3d3d3' class='calculateShipping'>
<table style="width: 100%">         
    <thead>
        <tr> 
            <th>Carrier</th> 
            <th style="text-align:center;">Service<br><span style="font-size: 10px;">Click the name below for more information</span></th> 
            <th style="text-align:center; <?php if (!$exist_netcharge) { echo 'display: none;'; }?>">
                Net Charge
                <span class="managetables-icon icon_help tipsy_tooltip" data-tooltip="location_available_id_tipsy_tooltip" title="Please note, that currently the system can only calculate the charges automatically for selected services in a few countries. We are working to include more services and countries. The calculated charge includes the postal charge as well as our handling fee and the cost of customs process and handling if applicable."></span>
            </th> 
            <th></th>
        </tr>
    </thead>          
    <tbody> 
    <?php
    foreach($listShippingServices as $listShippingService) {
        if(isset($allServicesRates[$listShippingService->id])){
            $logo_url = $allServicesRates[$listShippingService->id]['logo_url'];
            $logo_img = '';
            if (empty($logo_url)) {
                $logo_url = APConstants::FEDEX_DEFAULT_LOGO_PATH;
                $logo_img = 'display: none';
            }
            $logo_url = base_url().$logo_url;
        ?>      
        <tr>
            <td>
                <img style='float:left; <?php echo $logo_img ?>' alt="<?php echo $listShippingService->carrier_name; ?>" title="<?php echo $listShippingService->carrier_name; ?>" src="<?php echo $logo_url; ?>" width="50" />
            <span style="float:left;margin-left: 7px;"><?php echo $listShippingService->carrier_name;?></span></td>
            <td><strong><a href="#" class="show_service_info"
                           data-service-id="<?php echo $listShippingService->id?>"
                           data-carrier ="<?php echo $listShippingService->carrier_name ?>"
                           data-name="<?php echo $listShippingService->name?>"
                           data-service="<?php echo $listShippingService->short_desc?>"
                           data-description="<?php echo $listShippingService->long_desc?>"
                           data-tracking_information_flag="<?php echo $listShippingService->tracking_information_flag?>"
                           data-service_available_flag="<?php echo $allServicesRates[$listShippingService->id]['service_available_flag'] ?>"
                           ><?php echo $listShippingService->name; ?>
                    </a></strong>&nbsp;&nbsp;<span class="glyphicon glyphicon-info-sign" aria-hidden="true"
                                                                                            data-toggle="tooltip" data-placement="top" title="Estimated Delivery: <?php //echo $rate->estimatedDelivery; ?>"></span>
                
            </td>
            <td style="text-align:center; <?php if (!$exist_netcharge) { echo 'display: none;'; }?>">
                <?php 
                    $title = "";
                    if (empty($allServicesRates[$listShippingService->id]['total_charge'])) {
                        $title = "this service can not be calculated automatically.";
                    }
                ?>
                <span class="price" title="<?php echo $title;?>">
                    <?php
                    if (!empty($allServicesRates[$listShippingService->id]['total_charge'])) {
                        echo $allServicesRates[$listShippingService->id]['total_charge'] . ' ' . $allServicesRates[$listShippingService->id]['currency_short'];
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
            </td>
            <td>
                <?php
                if (!empty($allServicesRates[$listShippingService->id]['total_charge'])
                    || $allServicesRates[$listShippingService->id]['service_available_flag'] == APConstants::ON_FLAG) {
                ?>    
                    <input type="button" id="confirmCalculateShipping-<?php echo $listShippingService->id ?>" value="Confirm" class="confirmCalculateShipping " 
                        title="click this button to confirm this option."
                        data-shipping_type="<?php echo $shipping_type; ?>" 
                        data-shipping_rate='<?php echo $allServicesRates[$listShippingService->id]['raw_total_charge']; ?>' 
                        data-number_parcel='<?php echo $allServicesRates[$listShippingService->id]['number_parcel']; ?>' 
                        data-shipping_rate_id="<?php echo $listShippingService->id; ?>" 
                        data-id="<?php echo implode(",", $envelope_id); ?>"
                        data-raw_postal_charge="<?php echo $allServicesRates[$listShippingService->id]['raw_postal_charge']; ?>"
                        data-raw_customs_handling="<?php echo $allServicesRates[$listShippingService->id]['raw_customs_handling']; ?>"
                        data-raw_handling_charges="<?php echo $allServicesRates[$listShippingService->id]['raw_handling_charges']; ?>"
                        data-tracking_information_flag="<?php echo $listShippingService->tracking_information_flag?>" />
                
                <?php } else { ?>   
                    <img style='float:left' title="this service can not be selected for this shipment." 
                            src="<?php echo base_url(); ?>system/virtualpost/themes/new_user2/images/disable.png" width="30" />
                <?php } ?>
                
            </td>
        </tr>
        <?php
        }
        } ?>
    </tbody> 
</table>
</div>
<div style="display: none">
    <div id="divShippingServiceInfo"  title="Shipping service information">
        <table>
            <tr>
                <td><strong>Carrier</strong></td>
                <td id="tbl_carrier_name"></td>
            </tr>
            <tr>
                <td><strong>Product name</strong></td>
                <td id="tbl_name"></td>
            </tr>
            <tr>
                <td><strong>Service</strong></td>
                <td id="tbl_service"></td>
            </tr>
            <tr>
                <td><strong>Description</strong></td>
                <td id="tbl_long_desc"></td>
            </tr>
            <tr id="tr_tracking">
                <td><strong>Tracking</strong></td>
                <td id="tbl_tracking" style="color: red">
                    WARNING: this service does not provide you with tracking information. If you chose this service, all risks of this item being lost or damaged in the shipping process are on customer side.
                </td>
            </tr>
        </table>
    </div>
</div>
 <?php }else{echo 'For this shipment the price can not be calculated automatically';} ?>


<script type="text/javascript">
$(document).ready(function(){
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
        
    $(".show_service_info").click(function(){
       
        var parent = $(this).parent().parent();
        var service_id = $(this).data('service-id');
        var dialog= $('#divShippingServiceInfo');
        var carrier_name = $(this).data('carrier');
        var name = $(this).data('name');
        var service = $(this).data('service');
        var desc = $(this).data('description');
        var tracking_information_flag = $(this).data('tracking_information_flag');
        var service_available_flag = $(this).data('service_available_flag');
        var buttons = [];
        
        if (service_available_flag) {
            
            buttons = [
                {
                    text: "Cancel",
                    click: function(){
                        $(dialog).dialog('close');
                    }
                },
                {
                    text: "Confirm",
                    style: "background: #ffca00; color: #fff",
                    click: function(){
                        $('#confirmCalculateShipping-'+service_id).click();
                    }
                }
            ];
        
        } else {
            buttons = [
                {
                    text: "Cancel",
                    click: function(){
                        $(dialog).dialog('close');
                    }
                }
            ];
        }
        
        
        // Open new dialog
        $(dialog).openDialog({
            autoOpen: false,
            height: 300,
            width: 500,
            modal: true,
            closeOnEscape: false,
            open: function(){
                $("#tbl_carrier_name").html(carrier_name);
                $("#tbl_name").html(name);
                $("#tbl_service").html(service);
                $("#tbl_long_desc").html(desc);
                if (tracking_information_flag == '0') {
                    $('#tr_tracking').show();
                } else {
                    $('#tr_tracking').hide();
                }
            },
            buttons: buttons
         });
        $(dialog).dialog('option', 'position', 'center');
        $(dialog).dialog('open');
        return false;
    });
    
    $("#changeAddressLink").click(function (e) {
        e.preventDefault();
        $('#changeForwardAddressWindow').html('');
        
        var envelope_id = $(this).data('envelope-id');
        $('#changeForwardAddressWindow').openDialog({
            autoOpen: false,
            height: 380,
            width: 630,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                <?php if($shipping_type == "1"){// direct shipping ?>
                        $(this).load("<?php echo base_url() ?>customers/direct_change_forward_address?hide_flag=1&reload_rate_flag=1&envelope_id="+envelope_id, function() {});
                <?php }else{// collect shipping?>
                    $(this).load("<?php echo base_url() ?>customers/collect_change_forward_address?hide_flag=1&reload_rate_flag=1&envelope_id="+envelope_id, function() {});
                <?php }?>
            }
        });	
        $('#changeForwardAddressWindow').dialog('option', 'position', 'center');
        $('#changeForwardAddressWindow').dialog('open');
        
        return false;
    });
});
</script>