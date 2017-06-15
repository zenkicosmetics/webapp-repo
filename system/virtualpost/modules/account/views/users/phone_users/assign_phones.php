<?php
    $submit_url = base_url().'account/users/assign_phones';
?>
<form id="assginPhonesToUserForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th>Phones <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $list_customer_avail_phones,
                    "value_key" => 'id',
                    "label_key" => 'phone_name',
                    "value" => '',
                    "name" => 'phone_id',
                    "id"    => 'assginPhonesToUserForm_phones',
                    "clazz" => 'input-width',
                    "style" => 'width: 150px',
                    "has_empty" => true,
                    "option_default" => '---Select Phones---'
                ));?>
            </td>
        </tr>        
    </table>
    <input type="hidden" name="customer_id" id="assginPhonesToUserForm_customer_id" value="<?php echo $customer_id;?>" /> 
</form>