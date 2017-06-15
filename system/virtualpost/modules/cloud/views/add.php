<?php
    $submit_url = base_url().'cloud/add';
?>

<form id="addCloudServiceForm" method="post"
    action="<?php echo $submit_url?>">
    <h2 style="font-weight: bold;"><?php language_e('cloud_view_add_ClickTheNextButtonToEstablishA'); ?></h2>
    <table>
        <tr>
            <th><?php echo lang("cloud.type")?> <span class="required">*</span></th>
            <td>
                <?php echo my_form_dropdown(array(
                                    "data" => $list_cloud_service,
                                    "value_key" => 'cloud_id',
                                    "label_key" => 'cloud_name',
                                    "value" => '',
                                    "name" => 'cloud_id',
                                    "id"    => 'addCloudServiceForm_cloud_id',
                                    "clazz" => 'input-width',
                                    "style" => '',
                                    "has_empty" => false
                 ));?>
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
});
</script>