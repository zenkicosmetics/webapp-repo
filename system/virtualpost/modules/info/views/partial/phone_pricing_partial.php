<style type="text/css">
    .ui-slider .ui-slider-handle {
        width: 1.125rem;
        height: 1.125rem;
        border-radius: 0.5625rem;
        background-color: rgb(33,66,99);
    }
    
    <?php  if($is_dialog == '1'){?>
    .input-btn {
        display: inline-block;
        position: relative;
        padding: 0;
        margin-right: .1em;
        text-decoration: none !important;
        cursor: pointer;
        text-align: center;
        zoom: 1;
        overflow: visible;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        padding: 6px;
        border-radius: 3px;
    }
    .btn-yellow {
        background: rgb(255, 204, 0) !important;
        color: #000;
        font-weight: normal;
        border: solid 1px #bfbfbf;
        box-shadow: none !important;
    }
    <?php }?>
</style>
<table style="width: 100%; border: none" border="0">
    <tr>
        <td style="width:50%;border: none; vertical-align: top">
            <div style="border: #d3d3d3 solid 1px; padding: 10px; border-radius: 5px; width: 300px;">
                Where would you like to have a phone number?
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=>"USA",
                            "name" => 'country_code',
                            "id"    => 'country_code_3',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; margin-left: 0px',
                            "has_empty" => false
                    ));
                ?><br /><br />
                <?php 
                echo my_form_dropdown(array(
                        "data" => $list_area,
                        "value_key"=> 'area_code',
                        "label_key"=> 'area_name',
                        "value"=>"",
                        "name" => 'area_code',
                        "id"    => 'area_code',
                        "clazz" => 'input-width',
                        "style" => 'width: 250px; margin-left: 0px',
                        "has_empty" => false
                ));
                ?><br /><br />
                <select id="phone_number_list" name="phone_number_list" class="input-width" style="width: 250px; margin-left: 0px;"></select>
                <br />
                <br />
                <div class="hide" style="display:none" id="showNumberInfoDivContainer">
                </div>
                <div><span><a href="#" id="showNumberInfo" class="main_link_color" style="text-decoration: underline;">Show number info</a></span><!--<span style="margin-left: 80px;">View full list</span>--></div>
            </div>
            <div style="position: relative; top: 10px;border: #d3d3d3 solid 1px; padding: 10px; border-radius: 5px; width: 300px;">
                Where should incoming calls be forwarded to?
                <?php 
                    echo my_form_dropdown(array(
                            "data" => $list_forwarding,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'pricing_name',
                            "value"=>"USA",
                            "name" => 'country_code_forwarding',
                            "id"    => 'country_code_forwarding',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; margin-left: 0px',
                            "has_empty" => false
                    ));
                ?>
            </div>
        </td>
        <td style="width:50%;border: none; vertical-align: top">
            <div style="border: #d3d3d3 solid 1px; padding: 10px; border-radius: 5px;">
                <div id="slider" class="slider"></div>
                <br /><br />
                <input type="hidden" name="selected_minute" id="selected_minute" value="0" />
                <div style="text-align: right; font-weight: bold"><span class="slider-value" >0</span> minutes</div>
                <div style="text-align: right;">Incoming calls per month</div>
            </div>
            
            <div class="menu_title_background" style="position: relative; top: 10px;border: #d3d3d3 solid 1px; padding: 10px; border-radius: 5px;">
                <div class="ym-grid">
                    <div class="ym-gl"><h3 style="font-size: 18px">Price:</h3></div>
                    <div class="ym-gr">
                        <?php
                        echo my_form_dropdown(array(
                            "data" => $currencies,
                            "value_key" => 'currency_short',
                            "label_key" => 'currency_short',
                            "value" => '',
                            "name" => 'currency',
                            "id" => 'currency',
                            "clazz" => 'input-width',
                            "style" => 'width: 100px;',
                            "has_empty" => false
                        ));
                        ?>
                    </div>
                </div>
                <br /><br />
                <div class="ym-grid">
                    <div class="ym-gl">Setup fee, one time</div>
                    <div class="ym-gr"><span id="setup_fee"></span></div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl">Monthly fee</div>
                    <div class="ym-gr"><span id="monthly_fee"></span></div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl tipsy_tooltip" title="Price for incoming calls">Call forwarding(<span class="slider-value" >0</span> minutes)</div>
                    <div class="ym-gr"><span id="call_forwarding_fee"></span></div>
                </div>
                <div class="ym-grid">
                    <div class="ym-gl" id="selected_country_id">- selected country</div>
                    <div class="ym-gr"></div>
                </div>
            </div>
            
            <div class="ym-clearfix"></div>
            <div style="position: relative; top: 20px;border: #d3d3d3 solid 1px; padding: 10px; border-radius: 5px;">
                <div class="ym-grid">
                    <div class="ym-gl"><h3 id="estimated_cost" style="font-size: 18px"></h3><small>Estimated monthly cost</small></div>
                    <div class="ym-gr"><button id="addPhoneNumberButton" type="button" class="input-btn btn-yellow">Add a Phone Number NOW!</button></div>
                </div>
            </div>
            <div class="ym-clearfix"></div>
            <br />
            <div style="text-align: center; margin-top: 5px;">All contracts are yearly contracts with automatic renewal</div>
        </td>
    </tr>
</table>
<div class="ym-clearfix"></div>
<br />
<br />
<hr style="border-color: #d3d3d3; ; width: 100%" />
<div id="callRateDivContainer">
    <!-- <h3>Country of your target phone number</h3>-->
    <div>
        <input type="text" class="input-width" value="" name="enquiry" id="enquiry" placeholder="Search for country"
               style="margin-left:0px;"/>
        <button type="button" id="searchButton" class="input-btn btn-yellow">Search</button>
    </div>
    <br/>
    <div class="clearfix"></div>
    <div class="ym-grid">
        <div class="ym-gl">
            <div id="gridwraper">
                <div id="searchTableResult">
                    <table id="searchTableResult_dataGridResult"></table>
                    <div id="searchTableResult_dataGridPager"></div>
                </div>
            </div>
            <div class="clear-height"></div>
        </div>
    </div>
</div>
<div class="ym-clearfix"></div>
<br />
<br />
<link rel="stylesheet" href="<?php echo APContext::getAssetPath() ?>system/virtualpost/themes/account_setting2/css/chosen.css" />
<script src="<?php echo APContext::getAssetPath() ?>/system/virtualpost/themes/account_setting2/js/chosen.jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    // define new variable.
    var PRICE_PLAN_NORMAL = <?php echo json_encode($price_plan) ?>;
    var FORWARDING_PRICE = <?php echo json_encode($list_forwarding) ?>;
    var CURRENCIES = <?php echo json_encode($currencies) ?>;
    
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    
    // init phone number.
    bindingPhoneNumberList();
    
    $('#country_code_3').live('change', function() {
        var url = '<?php echo base_url() . "account/users/load_area_code_target"?>';
        var country_code = $('#country_code_3').val();
        $.bindSelect(url, 'country_code=' + country_code, 'area_code', '', '', function() {
            // apply chosen plugin.
            $('#area_code').trigger('chosen:updated');
            //$("#area_code").val($("#area_code option:first").val());
            
            bindingPhoneNumberList();
        });
        
        resetPricePlan();
    });
    
    $('#area_code').live('change', function() {
        bindingPhoneNumberList();
        
        resetPricePlan();
    });
    
    $("#phone_number_list").live ('change', function(){
        resetPricePlan();
    });
    
    $( "#slider" ).slider({
        min: 0,
        max: 1000,
        step: 1,
        slide: function(event, ui){
            var selected_val = ui.value;
        
            // set value here
            $("#selected_minute").val(selected_val);
            $(".slider-value").html(selected_val);

            // calculate price plan of number
            calPricePlan();
        }
    });
    $( "#slider" ).on('slidestop', function(event, ui){
        var selected_val = ui.value;
        
        // set value here
        $("#selected_minute").val(selected_val);
        $(".slider-value").html(selected_val);
        
        // calculate price plan of number
        calPricePlan();
    });
    
    $("#currency, #country_code_forwarding").live('change', function(){
        calPricePlan();
    });
    
    var showNumberInfo = true;
    $("#showNumberInfo").html("Hide number info");
    $("#showNumberInfoDivContainer").show();
    $("#showNumberInfo").click(function(e){
        e.preventDefault();
        
        if(showNumberInfo == false){
            $(this).html("Hide number info");
            $("#showNumberInfoDivContainer").show();
            showNumberInfo = true;
        }else{
            $(this).html("Show number info");
            $("#showNumberInfoDivContainer").hide();
            showNumberInfo = false;
        }
        
        return false;
    });
    
    $("#addPhoneNumberButton").click(function(e){
        location.href= "<?php echo base_url() ?>account/number";
        return false;
    });
    
    callRateByCountry();
    $("#searchButton").live('click', function(){
        callRateByCountry();
    });
    
    $("#area_code").chosen();
    
    $("#phone_number_list").live('change', function(){
        // calculate price plan of number
        calPricePlan();
    });
    
    /**
     * Gets list phone number.
     * @returns {undefined}
     */
    function bindingPhoneNumberList(){
        var country_code = $('#country_code_3').val();
        var area_code = $('#area_code').val();
        var url = '<?php echo base_url() . "info/load_phone_number_list"?>';
        $.bindSelect(url, 'country_code=' + country_code+'&area_code='+area_code, 'phone_number_list', null, '', function() {
            // do nothing
            calPricePlan();
        });
    }
    
    function calPricePlan(){
        var url = '<?php echo base_url() ?>info/cal_phone_number_pricing_plan';
        // get params
        var minutes = $("#selected_minute").val();
        var country_code = $('#country_code_3').val();
        var area_code = $("#area_code").val();
        var country_code_forwarding = $('#country_code_forwarding').val();
        var currency = $("#currency").val();
        
        // result
        var setup_fee = 0;
        var monthly_fee = 0;
        var call_forwarding_fee = 0;
        
        // calculate setup fee and monthly fee
        for(i=0; i< PRICE_PLAN_NORMAL.length; i ++){
            if(PRICE_PLAN_NORMAL[i].country_code_3 == country_code){
                setup_fee = parseFloat(PRICE_PLAN_NORMAL[i].one_time_fee) + parseFloat(PRICE_PLAN_NORMAL[i].one_time_fee_upcharge);
                var monthly_upcharge = 1 + parseFloat(PRICE_PLAN_NORMAL[i].recurring_fee_upcharge/100);
                monthly_fee = parseFloat(PRICE_PLAN_NORMAL[i].recurring_fee) * monthly_upcharge;
                break;
            }
        }
        
        // calculate call forwarding fee.
        for(i=0; i< FORWARDING_PRICE.length; i ++){
            if(FORWARDING_PRICE[i].country_code_3 == country_code_forwarding){
                var upcharge = 1 + parseFloat(FORWARDING_PRICE[i].usage_fee_upcharge/100);
                call_forwarding_fee = parseFloat(minutes * FORWARDING_PRICE[i].usage_fee * upcharge);
                break;
            }
        }
        
        // total cost.
        var estimated_cost = setup_fee + monthly_fee + call_forwarding_fee;
        
        // convert curreny
        var currency_sign = '';
        for(i=0; i< CURRENCIES.length; i++){
            if(currency == CURRENCIES[i].currency_short){
                var rate = parseFloat(CURRENCIES[i].currency_rate);
                setup_fee = setup_fee * rate;
                monthly_fee = monthly_fee * rate;
                call_forwarding_fee = call_forwarding_fee * rate;
                estimated_cost = estimated_cost * rate;
                currency_sign = CURRENCIES[i].currency_sign;
                break;
            }
        }
        
        $("#setup_fee").html(setup_fee.toFixed(2).replace('.', ',') + ' ' + currency);
        $("#monthly_fee").html(monthly_fee.toFixed(2).replace('.', ',') + ' ' + currency);
        $("#call_forwarding_fee").html(call_forwarding_fee.toFixed(2).replace('.', ',') + ' ' + currency);
        $("#estimated_cost").html(currency_sign + ' ' + estimated_cost.toFixed(2).replace('.', ','));
        
        // set selected country
        $("#selected_country_id").html("- " + $('#country_code_3').find(":selected").text());
        
        // get phone number description.
        $.ajaxExec({
            url: "<?php echo base_url() ?>info/get_phone_limitation",
            data:{country_code: country_code, phone_number: $("#phone_number_list").val()},
            success: function (data) {
                if (data.status) {
                    $("#showNumberInfoDivContainer").html(data.data);
                }
            }
        });
        
        return false;
    }
    
    function resetPricePlan(){
        $("#setup_fee").html("");
        $("#monthly_fee").html("");
        $("#call_forwarding_fee").html("");
        $("#estimated_cost").html("");
        $("#selected_minute").val(0);
        $(".slider-value").html(0);
        $("#slider").slider("value", 0);
    }
    
    function callRateByCountry(){
        $("#searchTableResult_dataGridResult").jqGrid('GridUnload');
        $("#searchTableResult_dataGridResult").jqGrid({
            url: '<?php echo base_url() ?>info/load_outboundcall_list',
            mtype: 'POST',
            datatype: "json",
            postData: {enquiry: $("#enquiry").val()},
            width: 950,
            height: 300,
            rowNum: 10,
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#searchTableResult_dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            // multiselect: true,
            // multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Country of your target phone number', 'Call Rate (EUR)'],
            colModel: [
                {name: 'id', index: 'id', hidden: true},
                {name: 'country', index: 'phone_number', width: 820},
                {name: 'rate', index: 'country', width: 100, sortable: false}
            ],
            loadComplete: function () {
                //$.autoFitScreen(DATAGRID_WIDTH);
            }
        });
    }
});
</script>