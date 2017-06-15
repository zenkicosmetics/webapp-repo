<!-- --------------------------------Start Change forwarding address -------------------------------->
<div class="popup_container">
    <div class="scan_popup_change_fw_address scan_popup_change_fw_address_<?php echo $envelope->id; ?>" style="display: <?php echo empty($envelope->id) ? 'none' : 'block' ?>;">
        <h2 style="margin-top: -8px;"><?php language_e('customer_view_direct_change_forward_address_ConfirmDirectForwarding') ?></h2>
        <div class="ym-clearfix"></div>
        <ul style="margin-top: 8px;margin-bottom: 8px;text-align: center;">
            <li>
                <a class="yes yes_sendmail btn-yellow" id="yes_sendmail_<?php echo $envelope->id; ?>" data-id="<?php echo $envelope->id ?>" 
                   data-packagetype="<?php echo Settings::get_alias01(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id) ?>" 
                   data-postbox_id="<?php echo $envelope->postbox_id ?>"><?php language_e('customer_view_direct_change_forward_address_Yes') ?></a>
            </li>
        </ul>

        <div class="" style="margin-top: 3px; margin-bottom: 4px;">
            <a rel="<?php echo $envelope->id; ?>" href="#" style="color: #336699;font-size: 12px;text-decoration: underline;" class="new_forward_address" 
               data-option="direct"><?php language_e('customer_view_direct_change_forward_address_AddNewAddress') ?>
            </a>
        </div>
        
        <!-- Start new layout -->
        <div style='border: 1px solid #d3d3d3; margin-top: 15px;height: 200px;overflow-y: scroll;' class='calculateShipping'>
            <table style="width: 100%">  
                <thead>
                    <tr> 
                        <th><?php language_e('customer_view_direct_change_forward_address_ShippingTo') ?></th> 
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>          
                <tbody> 
                <!-- Display the standard forwarding address -->
                <?php if (!empty($standardFWAddress)): ?>
                <tr>
                    <td style="<?php if ($envelope->shipping_address_id == '0') { ?> font-weight: bold <?php } ?>">
                        <?php echo $standardFWAddress; ?>
                    </td>
                    <td>
                        <input type="button" value="<?php language_e('customer_view_direct_change_forward_address_Confirm') ?>" class="forward_address_radio" 
                        title="<?php language_e('customer_view_direct_change_forward_address_ConfirmButtonTooltip') ?>"
                        data-envelope_id="<?php echo $envelope->id; ?>"
                        data-shipping_address_id="0" />

                    </td>
                </tr>
                <?php endif; ?>
                
                <!-- Display other option -->
                <?php
                if (count($customer_address)):
                    foreach ($customer_address as $row):
                        $alternativeAddress = "";
                        if (!empty($row->shipment_address_name)) {
                            $alternativeAddress .= ucwords(strtolower($row->shipment_address_name)) . ", ";
                        }
                        if (!empty($row->shipment_company)) {
                            $alternativeAddress .= ucwords(strtolower($row->shipment_company)) . ", ";
                        }
                        if (!empty($row->shipment_street)) {
                            $alternativeAddress .= ucwords(strtolower($row->shipment_street)) . ", ";
                        }
                        if (!empty($row->shipment_city)) {
                            $alternativeAddress .= ucwords(strtolower($row->shipment_city)) . ", ";
                        }
                        if (!empty($row->country_name)) {
                            $alternativeAddress .= ucwords(strtolower($row->country_name)) . ", ";
                        }
                        if (!empty($row->shipment_phone_number)) {
                            $alternativeAddress .= ucwords(strtolower($row->shipment_phone_number)) . ", ";
                        }

                        $alternativeAddress = APUtils::autoHidenTextUTF8($alternativeAddress, $startPosition = 0, $encoding = 'UTF-8', $numberLastCharacter = 2, $strCompare = ", ");
                        if ($row->is_primary_address != 1) { ?>
                <tr>
                    <td style="<?php if ($envelope->shipping_address_id == $row->shipping_address_id) { ?> font-weight: bold <?php } ?>">
                        <?php echo $alternativeAddress; ?>
                    </td>
                    <td>
                        <input type="button" value="<?php language_e('customer_view_direct_change_forward_address_Confirm') ?>" class="forward_address_radio" 
                        title="<?php language_e('customer_view_direct_change_forward_address_ConfirmButtonTooltip') ?>"
                        data-envelope_id="<?php echo $envelope->id; ?>"
                        data-shipping_address_id="<?php echo $row->shipping_address_id; ?>" />
                    </td>
                </tr>
                <?php }
                    endforeach;
                endif;
                ?>
                </tbody>
            </table>
        </div>
        <!-- End new layout -->
        
        <div class="" style="margin: 10px auto 0;">
            <a style="color: #336699;" href="#" class="manage_forward_address_direct"><?php language_e('customer_view_direct_change_forward_address_ManageAddress') ?></a>
        </div>
    </div> 

</div><!-- end div.popup_container-->

<input type="hidden" id="direct_shipping_selected_shipping_address_id" value="<?php echo $envelope->shipping_address_id?>" />

<script type="text/javascript">
$(document).ready(function(){
    $('.tipsy_tooltip').tipsy({gravity: 'se'});
    
    $(".forward_address_radio").click(function () {
        var current_shipping = $("#direct_shipping_selected_shipping_address_id").val();
        var envelope_id = $(this).attr('data-envelope_id');
        var shipping_address_id = $(this).attr('data-shipping_address_id');

        if(current_shipping == shipping_address_id){
            return ;
        }
        
        $("#direct_shipping_selected_shipping_address_id").val(shipping_address_id);
        $.ajaxExec({
            url: "<?php echo base_url() ?>mailbox/save_shipping_address?shipping_address_id=" + shipping_address_id + '&envelope_id=' + envelope_id,
            success: function (data) {
                if (!data) {
                    $.displayError("System error occurs. Please contact System Administrator.");
                    return;
                }
                
                $("#changeForwardAddressWindow").dialog("destroy");
                $('#calculateShippingRateWindow').dialog("destroy");
                
                // show the shipping rate dialog.
                var shipping_type = '1'; //direct
                var includedAllFlag = 0;
                openCalculateShippingRate(shipping_type, envelope_id, includedAllFlag);
            }
        });
    });
    
    /**
    * When user click link Manage forwarding addresses
    */
   $("a.manage_forward_address_direct").live('click', function () {
       $('.scan-popup').hide();
       // Open new dialog
       $('#forward_address').html('');
       $('#forward_address').openDialog({
           autoOpen: false,
           height: 500,
           width: 950,
           modal: true,
           open: function (event, ui) {
               $(this).load("<?php echo base_url() ?>customers/forwardAddress?envelope_id=<?php echo $envelope->id; ?>", function () {
               });
           },
           buttons: {
               'Add alternative address': function () {
                   if ($("#wrap_forward_add #shipment_street").val() == '') {
                       $("#wrap_forward_add #shipment_street").addClass('error');
                       return;
                   }

                   if ($("#wrap_forward_add #shipment_postcode").val() == '') {
                       $("#wrap_forward_add #shipment_postcode").addClass('error');
                       return;
                   }
                   if ($("#wrap_forward_add #shipment_city").val() == '') {
                       $("#wrap_forward_add #shipment_city").addClass('error');
                       return;
                   }

                   var current_forward_address = $("#current_order_forward_address").val();

                   if ($("#wrap_forward_add .shipment_street_alt_" + (current_forward_address - 1)).val() == '') {
                       $("#wrap_forward_add .shipment_street_alt_" + (current_forward_address - 1)).addClass('error');
                       return;
                   }

                   if ($("#wrap_forward_add .shipment_postcode_alt_" + (current_forward_address - 1)).val() == '') {
                       $("#wrap_forward_add .shipment_postcode_alt_" + (current_forward_address - 1)).addClass('error');
                       return;
                   }

                   if ($("#wrap_forward_add .shipment_city_alt_" + (current_forward_address - 1)).val() == '') {
                       $("#wrap_forward_add .shipment_city_alt_" + (current_forward_address - 1)).addClass('error');
                       return;
                   }

                   $("#clone div.input-cols .shipment_street_alt").attr('rel', current_forward_address).removeClass('shipment_street_alt_' + (current_forward_address - 1)).addClass("shipment_street_alt_" + current_forward_address);
                   $("#clone div.input-cols .shipment_postcode_alt").attr('rel', current_forward_address).removeClass('shipment_postcode_alt_' + (current_forward_address - 1)).addClass("shipment_postcode_alt_" + current_forward_address);
                   $("#clone div.input-cols .shipment_city_alt").attr('rel', current_forward_address).removeClass('shipment_city_alt_' + (current_forward_address - 1)).addClass("shipment_city_alt_" + current_forward_address);


                   $("#clone div.input-cols h2").html("Alternative " + current_forward_address + "<div onClick='return deleteAddressClick();' rel='0' class='deleteAddress' id='deleteAddress'>&nbsp;</div>");
                   $("#current_order_forward_address").val(parseInt(current_forward_address) + 1);

                   $("#clone div.input-cols").clone().appendTo("#wrap_forward_add").animate({scrollTop: 0},1400,"easeOutQuint");
               },
               'Save': function () {
                   var submitUrl = $('#saveForward_AddressForm').attr('rel');
                   var data_submit = $('#saveForward_AddressForm').serializeObject();
                   data_submit = JSON.stringify(data_submit);
                   $.ajaxExec({
                       type: "POST",
                       url: submitUrl,
                       data: {"data_submit": data_submit},
                       success: function (data) {
                           if (data.status) {
                               $('#forward_address').dialog('close');
                               $.infor({
                                   message: data.message,
                                   ok: function () {
                                        var envelope_id = data.data.envelope_id;
                                        
                                        if (!envelope_id) {
                                            $.displayInfor("There is no item selected to direct shipment.");
                                            return false;
                                        }
                                        
                                        $('#changeForwardAddressWindow').html('');
                                        $('#changeForwardAddressWindow').load("<?php echo APContext::getFullBasePath(); ?>customers/direct_change_forward_address?envelope_id=" + envelope_id , function () {});
                                   }
                               });

                           } else {
                               //$('#forward_address').dialog('close');
                               $.displayError(data.message);
                           }
                       }
                   });
               }

           }
       });

       $('#forward_address').dialog('option', 'position', 'center');
       $('#forward_address').dialog('open');

       return false;
   });
});

</script>