/**
* Open address screen.
*/
function openManageAddressWindow(callback, customer_id) {
    if(customer_id == undefined || customer_id == "undefined"){
        customer_id = "";
    }
    // Open new dialog
    $('#forward_address').openDialog({
        autoOpen: false,
        height: 500,
        width: 950,
        modal: true,
        open: function (event, ui) {
            $(this).load("<?php echo base_url() ?>customers/forwardAddress?customer_id="+customer_id, function () {
                //$('#shipment_address_name_id').focus();
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
                $("#current_order_forward_address").val(parseInt($("#current_order_forward_address").val()) + 1);
                $("#clone div.input-cols").clone().appendTo("#wrap_forward_add").animate({
                    scrollTop: 0},
                        1400,
                        "easeOutQuint"
                        );
            },
            'Save': function () {
                var submitUrl = $('#saveForward_AddressForm').attr('rel');
                var data_submit = $('#saveForward_AddressForm').serializeObject();
                data_submit = JSON.stringify(data_submit);
                $.ajaxExec({
                    type: "POST",							
                    url: submitUrl,
                    data: {"data_submit":data_submit},
                    success: function (data) {
                        if (data.status) {
                            $('#forward_address').dialog('close');
                            $.infor({
                                message: data.message,
                                ok: function () {
                                 if(typeof(callback) == 'function'){
                                     callback();
                                 }else{
                                     location.reload();
                                  }
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
} // end function openManageAddressWindow

