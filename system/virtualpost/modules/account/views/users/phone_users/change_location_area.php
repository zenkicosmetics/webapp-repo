<?php
    $submit_url = base_url().'account/users/change_location_area?customer_id='.$customer_id;
?>
<form id="changeLocationAreaUserForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>        
        <tr>
            <th>Country</th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=> !empty($sonetel_user) ? $sonetel_user->location->country : '',
                            "name" => 'country_code',
                            "id"    => 'country_code_3',
                            "clazz" => 'input-width',
                            "style" => 'width: 262px',
                            "has_empty" => false
                    ));
                ?>
            </td>
        </tr>
        <tr>
            <th>Area</th>
            <td>
                <?php 
                // #472: added
                echo my_form_dropdown(array(
                        "data" => $list_area,
                        "value_key"=> 'area_code',
                        "label_key"=> 'area_name',
                        "value"=> !empty($sonetel_user) ? $sonetel_user->location->area_code : '',
                        "name" => 'area_code',
                        "id"    => 'area_code',
                        "clazz" => 'input-width',
                        "style" => 'width: 262px',
                        "has_empty" => false
                ));
                ?>
            </td>
        </tr>
    </table>
</form>
<script>
jQuery(document).ready(function () {
    // User change the country code
    $('#country_code_3').live('change', function() {
        var url = '<?php echo base_url() . "account/users/load_area_code_target"?>';
        var country_code = $('#country_code_3').val();
        $.bindSelect(url, 'country_code=' + country_code, 'area_code', '', '', function() {
        });
    });
});
</script>