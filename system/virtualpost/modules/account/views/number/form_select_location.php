<?php

?>
<style type="text/css">
    #addEditNumberSelectLocationFormTable tbody th{
        border: none;
    }
</style>
<form id="addEditNumberSelectLocationForm" method="post" class="dialog-form" action="#">
    <table id="addEditNumberSelectLocationFormTable">
    	<tr>
            <th style="width: 100px;">Location</th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $locations,
                            "value_key"=> 'id',
                            "label_key"=> 'location_name',
                            "value" => $selected_location_id,
                            "name" => 'addEditNumberSelectLocationForm_location_id',
                            "id"    => 'addEditNumberSelectLocationForm_location_id',
                            "clazz" => 'input-width input-txt-none',
                            "style" => 'width: 250px',
                            "has_empty" => true
                    ));
                    ?>
            </td>
        </tr>
        
    </table>
    
    <br /><br />
    <?php if($postbox_count == 0){ ?>
    <a style="color: #0e76bc; text-decoration: underline" href="<?php echo  base_url() ?>account/postbox_setting">Add postbox...</a>
    <?php }?>
</form>

<script>
jQuery(document).ready(function () {
    
});
</script>
