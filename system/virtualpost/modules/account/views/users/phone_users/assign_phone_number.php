<?php
    $submit_url = base_url().'account/users/assign_phone_number';
?>
<form id="assginPhoneNumberToUserForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th>Phone Number <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $list_customer_avail_phonenumber,
                    "value_key" => 'phone_number',
                    "label_key" => 'phone_number_label',
                    "value" => '',
                    "name" => 'phone_number',
                    "id"    => 'assginPhoneNumberToUserForm_phone_number',
                    "clazz" => 'input-width',
                    "style" => 'width: 150px',
                    "has_empty" => true,
                    "option_default" => '---Select Phone Number---'
                ));?>
            </td>
        </tr>        
    </table>
    <input type="hidden" name="customer_id" id="assginPhoneNumberToUserForm_customer_id" value="<?php echo $customer_id;?>" /> 
</form>