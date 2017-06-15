<?php
    $submit_url = base_url().'account/users/assign_postbox';
?>
<form id="assginPostboxToUserForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th>Postbox Name <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                    "data" => $list_postbox,
                    "value_key" => 'postbox_id',
                    "label_key" => 'label',
                    "value" => '',
                    "name" => 'postbox_id',
                    "id"    => 'assginPostboxToUserForm_postbox_id',
                    "clazz" => 'input-width',
                    "style" => 'width: 150px',
                    "has_empty" => true,
                    "option_default" => '---Select Postbox---'
                ));?>
            </td>
        </tr>        
    </table>
    <input type="hidden" name="customer_id" id="assginPostboxToUserForm_customer_id" value="<?php echo $customer_id;?>" /> 
</form>