<div class="info-content">
    <!-- invoice address -->
    <div class="ym-grid">
        <div class="ym-gl ym-g100">
            <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_customer_address_InvoicingAddress'); ?></h3>
        </div>
    </div>
    <div class="ym-grid">
        <table border="0" style="border: 0px; margin:0px">
            <tr>
                <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_customer_address_Name'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_address_name : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Company'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_company : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Street'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_street : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_PostCode'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_postcode : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_City'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_city : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Region'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_region : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Country'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_country_name : ""; ?></td>
            </tr>
            <tr>
                <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_PhoneNo'); ?></label></th>
                <td style="padding: 0px"><?php echo $customer_addresses ? $customer_addresses->invoicing_phone_number : ""; ?></td>
            </tr>

        </table>
    </div>

    <!-- postbox address -->
    <div class="ym-grid">
        <div class="ym-gl ym-g100">
            <h3 style="font-size: 16px"><?php admin_language_e('cases_view_todo_customer_address_Postbox'); ?></h3>
        </div>
    </div>
    <?php foreach ($postboxes as $select_postbox): ?>
        <div class="ym-grid">
            <table border="0" style="border: 0px; margin:0px">
                <tr>
                    <th style="padding: 0px; width: 100px;"><label><?php admin_language_e('cases_view_todo_customer_address_PostboxID'); ?></label></th>
                    <td style="padding: 0px"><?php echo $select_postbox->postbox_code; ?></td>
                </tr>
                <tr>
                    <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Type'); ?></label></th>
                    <td style="padding: 0px"><?php echo $select_postbox->postbox_type; ?></td>
                </tr>
                <tr>
                    <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Name'); ?></label></th>
                    <td style="padding: 0px"><?php echo $select_postbox->name; ?></td>
                </tr>
                <tr>
                    <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Company'); ?></label></th>
                    <td style="padding: 0px"><?php echo $select_postbox->company; ?></td>
                </tr>
                <tr>
                    <th style="padding: 0px"><label><?php admin_language_e('cases_view_todo_customer_address_Location'); ?></label></th>
                    <td style="padding: 0px"><?php echo $select_postbox->location_name; ?></td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>
</div><!-- end of info -->