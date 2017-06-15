<?php
    $submit_url = base_url().'account/change_my_account_type';
?>
<form id="changeMyAccountTypeForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th>Postbox</th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $postboxes,
                            "value_key"=> 'postbox_id',
                            "label_key"=> 'label',
                            "value"=>"",
                            "name" => 'postbox_id',
                            "id"    => 'postbox_id',
                            "clazz" => 'input-width',
                            "style" => 'width: 300px',
                            "has_empty" => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Select new postbox type <span class="required">*</span></th>
            <td>
                <?php echo code_master_form_dropdown(array(
                                     "code" => APConstants::ACCOUNT_TYPE,
                                     // "value" => $customer->account_type,
                                     "value"=> APConstants::BUSINESS_TYPE, // #472: default is business type.
                                     "name" => 'account_type',
                                     "id"    => 'changeMyAccountTypeForm_account_type',
                                     "clazz" => 'input-width',
                                     "style" => 'width: 300px',
                                     "has_empty" => false
                                 ));?>
            </td>
        </tr>
    </table>
</form>