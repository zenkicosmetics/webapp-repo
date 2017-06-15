<?php
    $submit_url = base_url().'account/number/change_phone_number_setting?phone_number='.$phonenumber->phnum;
?>
<form id="changePhoneNumberConnectionSettingForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th>Phone Number</th>
            <td><div style="margin-left: 10px"><?php echo $phonenumber->phnum; ?></div></td>
        </tr>
        <tr>
            <th>Target Type</th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $list_connected_to_type,
                            "value_key"=> 'key',
                            "label_key"=> 'label',
                            "value"=> $phonenumber->connect_to_type,
                            "name" => 'connect_to_type',
                            "id"    => 'connect_to_type',
                            "clazz" => 'input-width',
                            "style" => 'width: 262px',
                            "has_empty" => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Target To</th>
            <td>
                <?php 
                // #472: added
                echo my_form_dropdown(array(
                        "data" => $list_connected_to,
                        "value_key"=> 'key',
                        "label_key"=> 'label',
                        "value"=> $phonenumber->connect_to,
                        "name" => 'connect_to',
                        "id"    => 'connect_to',
                        "clazz" => 'input-width',
                        "style" => 'width: 262px',
                        "has_empty" => false
                ));
                ?>
                <div id="linkAddNewTargetContainer" style="display: none;margin-left:10px">
                    Can not find the target. Click <a href="<?php echo base_url() . 'account/target'?>" target="_blank" style="text-decoration: underline">here</a> to add new target
                </div>
            </td>
        </tr>
    </table>
    <input type="hidden" value="<?php echo $phonenumber->phnum;?>" id="changePhoneNumberConnectionSettingForm_phone_number" />
</form>
<script>
jQuery(document).ready(function () {
    loadConnectToList();
    $('#connect_to_type').live('change', function() {
       loadConnectToList();
    });
    
    function loadConnectToList() {
        var url = '<?php echo base_url() . "account/number/load_phonenumber_setting_to_target"?>';
        var connect_type_to = $('#connect_to_type').val();
        $.bindSelect(url, 'connect_type_to=' + connect_type_to, 'connect_to', '', '', function() {
            // Set default value
            var optionCount = $("#connect_to option[value!='']").length;
            if (optionCount == 0) {
                $('#linkAddNewTargetContainer').show();
            } else {
                $('#linkAddNewTargetContainer').hide();
            }
        });
    }
});
</script>