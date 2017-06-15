<?php
    $submit_url = base_url().'account/delete_postbox';
?>
<form id="delPostboxForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th>Postbox Name <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                                     "data" => $data,
                                     "value_key" => 'postbox_id',
                                     "label_key" => 'label',
                                     "value" => '',
                                     "name" => 'postbox_name',
                                     "id"    => 'delPostboxForm_sltPostbox',
                                     "clazz" => 'input-width',
                                     "style" => 'width: 150px',
                                     "has_empty" => true,
                                     "option_default" => '---Select Postbox---'
                                 ));?>
            </td>
        </tr>        
    </table>
    <input type="hidden" name="direct_delete" id="direct_delete" value="0" /> 
</form>