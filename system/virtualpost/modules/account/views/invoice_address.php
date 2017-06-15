<form id="updateInvoiceAddressWindow_saveAddressForm" action="<?php echo base_url() . 'account/invoice_address'; ?>" method="post">
    <div class="ym-grid" >
        <table style="border: none" border="0" class="invoice_address_table">
            <tr>
                <td width="100px"><label>Name:</label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_address_name" 
                           id="invoicing_address_name" value="<?php echo !empty($address)? $address->invoicing_address_name : "";?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Company:</label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_company" 
                           id="invoicing_company" value="<?php echo !empty($address)? $address->invoicing_company : "";?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Street: <span class="required">*</span></label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_street" id="invoicing_street" value="<?php
                    if ($address) {
                        echo $address->invoicing_street;
                    }
                    ?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Post Code: <span class="required">*</span></label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_postcode" id="invoicing_postcode" 
                           value="<?php echo !empty($address)? $address->invoicing_postcode : ""; ?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>City: <span class="required">*</span></label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_city" id="invoicing_city" 
                           value="<?php echo !empty($address)? $address->invoicing_city : ""; ?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Region: <span class="required">*</span></label></td>
                <td>
                    <input class="input-txt" type="text" name="invoicing_region" id="invoicing_region"
                           value="<?php echo !empty($address)? $address->invoicing_region : ""; ?>" />
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Country: <span class="required">*</span></label></td>
                <td>
                    <select id="invoicing_country" name="invoicing_country" class="input-width" style="width: 99%;margin-left: 0px;">
                        <?php foreach ($countries as $country) { ?>
                            <option value="<?php echo $country->id ?>" <?php if (!empty($address) && $address->invoicing_country == $country->id) { ?> selected="selected" <?php } ?>><?php echo $country->country_name ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td width="100px"><label>Phone Number: </label></td>
                <td>
                    <input class="input-txt" name="invoicing_phone_number" id="invoicing_phone_number" type="text" 
                           value="<?php echo !empty($address)? $address->invoicing_phone_number : ""; ?>" />
                </td>
            </tr>
        </table>
    </div>
    
    <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
</form>

<script type="text/javascript">
    $(document).ready(function ($) {
        /**
         * submit change/add invoice address
         */
        $("#updateInvoiceAddressWindow_saveBtn").die("click");
        $("#updateInvoiceAddressWindow_saveBtn").live('click', function () {
            $.ajaxSubmit({
                url: "<?php echo base_url() ?>account/save_invoice_address",
                formId: 'updateInvoiceAddressWindow_saveAddressForm',
                success: function (data) {
                    if (data.status) {
                        $.displayInfor(data.message, null, function () {
                            $('#updateInvoiceAddressWindow').dialog('close');
                            $("#updateInvoiceAddressWindow").parent().find('.ui-dialog-buttonpane button:contains("Cancel")').click();
                            $("#updateInvoiceAddressWindow").parent().find('.ui-dialog-titlebar-close').click();
                        });
                    }else{
                        $.displayError(data.message);
                    }
                }
            });

            return false;
        });
    });

</script>