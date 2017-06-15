<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'account/number/add';
} else {
    $submit_url = base_url() . 'account/number/edit';
}
?>
<style type="text/css">
    #addEditNumberFormTable tbody th{
        border: none;
    }
    #area_code_chosen {
        margin-left: 10px;
    }
</style>
<form id="addEditNumberForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table id="addEditNumberFormTable">
    	<tr>
            <th style="width: 100px;text-align: right">Country <span class="required">*</span></th>
            <td>
                <?php 
                    // #472: added
                    echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=>"USA",
                            "name" => 'country_code',
                            "id"    => 'country_code_3',
                            "clazz" => 'input-width input-txt-none',
                            "style" => 'width: 250px',
                            "has_empty" => false
                    ));
                ?>
                
            </td>
            <th style="width: 100px; text-align: right">Our Location</th>
            <td>
                <button id="getPostboxLocationButton">Get a number at your postbox location</button>
                <input type="hidden" id="addEditNumberForm_location_id" name="location_id" value="" />
            </td>
        </tr>
        <tr>
            <th style="text-align: right">Area <span class="required">*</span></th>
            <td>
                <?php 
                // #472: added
                echo my_form_dropdown(array(
                        "data" => $list_area,
                        "value_key"=> 'area_code',
                        "label_key"=> 'area_name',
                        "value"=>"",
                        "name" => 'area_code',
                        "id"    => 'area_code',
                        "clazz" => 'input-width input-txt-none',
                        "style" => 'width: 250px; margin-left:10px;',
                        "has_empty" => false
                ));
                ?>
            </td>
            <th></th>
            <td>
                (The advantage of phone numbers at our postbox location is, that you don't have to verify the address for it separately )
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <a href="#" id="confirmLimitationLink" style="text-decoration: underline; margin-left: 0px;" class="main_link_color">Limitations and verification requirements for this location</a>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div id="searchTableResult_phonelist">
                    <table id="dataGridResult_phonelist"></table>
                    <div id="dataGridPager_phonelist"></div>
                </div>
            </td>
        </tr>
        <tr>
            <th>Auto Renewal</th>
            <td>
                <input type="checkbox" id="addEditNumberForm_auto_renewal" name="auto_renewal" value="1" <?php if($number->auto_renewal == '1') { ?>checked="checked"<?php } ?> />
            </td>
            <th></th>
            <td style="text-align: right">
                <div style="margin-right: 30px">
                    <input type="checkbox" id="addEditNumberForm_confirm_terms_condition" /> <a id="linkTermsAndCondition" class="main_link_color" href="<?php echo base_url()?>info/view_term_inline" target="_bank" style="text-decoration: underline">I confirm the terms and condition</a>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                <button id="bookSelectedNumberButton" class="input-btn btn-yellow" style="margin-right: 30px;">Book Selected Number to Account</button>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" />
    <input type="hidden" id="addEditNumberForm_phone_number" name="phone_number" value="" />
    <input type="hidden" id="addEditNumberForm_range" name="range" value="" />
    <input type="hidden" id="addEditNumberForm_initial_amount" name="initial_amount" value="" />
    <input type="hidden" id="id" name="id" value="<?php echo $number->id?>" />
</form>
<div class="hide">
    <div id="selectYourPostboxLocationWindow" title="Select Your Postbox Location" class="input-form dialog-form"></div>
    <div id="limitationVerificationLocationWindow" title="Limitations and verification requirements" class="input-form dialog-form"></div>
</div>
<link rel="stylesheet" href="<?php echo APContext::getAssetPath() ?>system/virtualpost/themes/account_setting2/css/chosen.css" />
<script src="<?php echo APContext::getAssetPath() ?>/system/virtualpost/themes/account_setting2/js/chosen.jquery.min.js"></script>
<script>
jQuery(document).ready(function () {
    $('#btnSearchPhoneList, #getPostboxLocationButton, #bookSelectedNumberButton').button();
        
    // Search
    searchAvailablePhone();
    
    // User change the country code
    $('#country_code_3').live('change', function() {
        var url = '<?php echo base_url() . "account/users/load_area_code_target"?>';
        var country_code = $('#country_code_3').val();
        $.bindSelect(url, 'country_code=' + country_code, 'area_code', '', '', function() {
            $('#area_code').trigger('chosen:updated');
            
            searchAvailablePhone();
        });
        if (selected_countr_code_3 != country_code) {
            $('#location_id').val('');
        }
    });
    
    $('#area_code').chosen();
    $('#area_code').live('change', function() {
        searchAvailablePhone();
    });
    
    $('#getPostboxLocationButton').live('click', function(){
       selectYourPostboxLocation();
       return false;
    });
    
    $('#confirmLimitationLink').live('click', function(){
       openLimitationVerificationWindow();
       return false;
    });
    
    function searchAvailablePhone() {
        $("#dataGridResult_phonelist").jqGrid('GridUnload');
        $("#dataGridResult_phonelist").jqGrid({
            url: '<?php echo base_url()?>account/number/list_avail_phone',
            postData: $('#addEditNumberForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            width: 780,
            height: '150px',
            rowNum: 10,
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager_phonelist",
            sortname: 'phone_number',
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: false,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Phone Number', 'Type', 'City', 'Setup', 'Monthly', 'Per Minute', 'Contract Terms', 'Range'],
            colModel: [
                {name: 'id', index: 'id', formatter: function radio(cellValue, option, rowObject) {
                        console.log(rowObject);
                        return '<input type="radio" name="radio_' + option.gid + '" value = "' + cellValue +'" data-range = "' + rowObject[5] +'" data-initial_amount = "' + rowObject[3] +'" />';
                    }, width: 50, sortable: false, align: "center"
                },
                {name: 'phone_number', index: 'phone_number', width: 120, align: "left"},
                {name: 'number_type', index: 'number_type', width: 100, align: "left"},
                {name: 'city', index: 'city',  width: 130, sortable: false, align: "left"},
                {name: 'initial_amount', index: 'initial_amount', width: 80, sortable: false, align: "left"},
                {name: 'monthly_amount', index: 'monthly_amount', width: 80, sortable: false, align: "left"},
                {name: 'per_min_fee', index: 'per_min_fee', width: 80, sortable: false, align: "left"},
                {name: 'recurrence_interval ', index: 'recurrence_interval ', width: 80, sortable: false, align: "left"},
                {name: 'range', index: 'range', hidden: true, sortable: false, align: "left"}
            ],
            loadComplete: function () {
                
            }
        });
        $("#dataGridResult_phonelist").jqGrid('setLabel','phone_number','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','city','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','number_type','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','initial_amount','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','monthly_amount','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','recurrence_interval','',{'text-align':'left'});
        $("#dataGridResult_phonelist").jqGrid('setLabel','range','',{'text-align':'left'});
    }
    
    // Select your postbox location
    function selectYourPostboxLocation() {
        // Clear control of all dialog form
        $('#selectYourPostboxLocationWindow').html('');
        var currentPostboxLocation = $('#addEditNumberForm_location_id').val();
        // Open new dialog
        $('#selectYourPostboxLocationWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 450,
            modal: true,
            open: function () {
                $(this).load('<?php echo base_url()?>account/number/select_your_postbox_location?selected_location_id='+currentPostboxLocation, function () {
                    
                });
            },
            buttons: {
                'Select': function () {
                    $('#addEditNumberForm_location_id').val($('#addEditNumberSelectLocationForm_location_id').val());
                    $(this).dialog('close');
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#selectYourPostboxLocationWindow').dialog('option', 'position', 'center');
        $('#selectYourPostboxLocationWindow').dialog('open');
        return false;
    }
    
    // Select your postbox location
    function openLimitationVerificationWindow() {
        // Clear control of all dialog form
        $('#limitationVerificationLocationWindow').html('');
        var countryCode = $('#country_code_3').val();
        
        // Open new dialog
        $('#limitationVerificationLocationWindow').openDialog({
            autoOpen: false,
            height: 420,
            width: 650,
            modal: true,
            open: function () {
                $(this).load('<?php echo base_url()?>account/number/limication_verification?country_code='+countryCode, function () {
                    
                });
            }
        });
        $('#limitationVerificationLocationWindow').dialog('option', 'position', 'center');
        $('#limitationVerificationLocationWindow').dialog('open');
        return false;
    }
});
</script>
