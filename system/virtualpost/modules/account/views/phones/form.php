<?php
    $submit_url = base_url() . 'account/phones/add';
?>
<form id="addEditPhoneForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>Target name <span class="required">*</span></th>
            <td><input class="input-width" type="text" name="phone_name" id="phone_name" value="" maxlength="250" /></td>
        </tr>
        <tr>
            <th>Target Type <span class="required">*</span></th>
            <td>
                <select id="phone_type" name="phone_type" class="input-width" style="width: 262px;">
                    <option value="regular">regular</option>
                    <option value="IP">IP</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th>Phone number <span class="required">*</span></th>
            <td>
                
                <?php 
                    /**
                    echo my_form_dropdown(array(
                            "data" => $list_numbers,
                            "value_key"=> 'phone_number',
                            "label_key"=> 'phone_number',
                            "value"=>"",
                            "name" => 'phone_number',
                            "id"    => 'phone_number',
                            "clazz" => 'input-width',
                            "style" => 'width: 262px',
                            "has_empty" => false
                    ));
                    */
                ?>
                <input type="text" id="phone_number" name="phone_number" value="" class="input-width" />
                <input type="text" id="phone_number2" name="phone_number2" value="" class="input-width hide" />
                <br/>
                <div style="margin-left: 10px;">(Example format: +14243332434)</div>
            </td>
        </tr>
    </table>
    <div id="gridwraper_phonelist">
        <div id="searchTableResult_phonelist">
            <table id="dataGridResult_phonelist"></table>
            <div id="dataGridPager_phonelist"></div>
        </div>
    </div>
    <input type="hidden" id="h_action_type" name="h_action_type" value="" />
    <input type="hidden" id="id" name="id" value="" />
</form>

<script>
$(document).ready(function () {
    changePhoneType();
    
    $("#phone_type").change(function(){
        changePhoneType();
    });
    
    /**
     * change phone type
     * @returns {undefined}
     */
    function changePhoneType(){
        var current_type = $('#phone_type').val();
        
        if(current_type == 'IP'){
            $("#phone_number2").show();
            $("#phone_number").hide();
        }else{
            $("#phone_number").show();
            $("#phone_number2").hide();
        }
    }
});
</script>
