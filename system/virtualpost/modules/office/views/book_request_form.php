<?php
$submit_url = base_url() . 'office/book_request_form';
?>
<form id="addBookRequestForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>Location <span class="required">*</span></th>
            <td>
                <input type="text" id="addBookRequestForm_location_name" name="location_name"  class="input-width" readonly="readonly"
                       value="<?php echo $location->location_name ?>" />
            </td>
        </tr>
        <tr>
            <th>Your Name <span class="required">*</span></th>
            <td>
                <input type="text" id="addBookRequestForm_your_name" name="your_name"  class="input-width" maxlength=100 
                       value="<?php if (!empty($customer_address)) { echo $customer_address->invoicing_address_name;} ?>" />
            </td>
        </tr>
        <tr>
            <th>Your email <span class="required">*</span></th>
            <td>
                <input type="text" id="addBookRequestForm_your_email" name="your_email" class="input-width" maxlength=100
                       value="<?php echo $customer->email ?>" />
            </td>
        </tr>
        <tr>
            <th>Callback phone number <span class="required">*</span></th>
            <td>
                <input type="text" id="addBookRequestForm_your_phone" name="your_phone"  class="input-width" maxlength=30 
                       value="<?php if (!empty($customer_address)) { echo $customer_address->shipment_phone_number;} ?>" />
            </td>
        </tr>
        <tr>
            <th colspan="2">Your booking request: <span class="required">*</span></th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea cols="60" rows="7" class="input-width" style="width: 95%;" name="booking_request"></textarea>
            </td>
        </tr>
    </table>
    <input type="hidden" name="location_id" id="addBookRequestForm_location_id" value="<?php echo $location->id; ?>" />
</form>

