<?php 
    $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
?>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 99%;margin-left: 20px;">
        <h2 style="border: 0px;">Prices for customer phone numbers</h2>
    </div>
</div>
<div style="margin:10px 0 0 20px">
    <div class="ym-grid mailbox">
        <form id="searchPhoneNumberForm" action="<?php echo base_url()?>account/phones_price_setting/index" method="post">
            <div class="ym-g70 ym-gl">
                <div class="ym-grid input-item">
                    <?php 
                        // #472: added
                        echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=> "",
                            "name" => 'country_code',
                            "id"    => 'country_code_31',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; margin-left:0px;',
                            "has_empty" => true
                        ));
                    ?>
                    <button id="searchPhoneNumberButton" class="input-btn btn-yellow" style="margin-left: 10px">Search</button>
                </div>
            </div>
            <input type="hidden" value="phone_number" name="price_type" />
         </form>
    </div>
    <div class="clear-height"></div>
    <div class="button_container">
            <div id="searchTablePhoneNumberPriceResult">
            <table id="dataGridPhoneNumberPriceResult"></table>
            <div id="dataGridPhoneNumberPricePager"></div>
        </div>
        <div class="clear-height"></div>
    </div>
</div>
<div class="clear-height"></div>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 99%;margin-left: 20px;">
        <h2 style="border: 0px;">Prices for forwarding (Call Rates per Minute)</h2>
    </div>
</div>
<div style="margin:10px 0 0 20px">
    <!--Outbound Calls-->
    <div class="ym-grid mailbox">
        <form id="searchOutboundCallsForm" action="<?php echo base_url()?>account/phones_price_setting/index" method="post">
            <div class="ym-g70 ym-gl">
                <div class="ym-grid input-item">
                    <?php 
                        // #472: added
                        echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key"=> 'country_code_3',
                            "label_key"=> 'country_name',
                            "value"=> "",
                            "name" => 'country_code',
                            "id"    => 'country_code_32',
                            "clazz" => 'input-width',
                            "style" => 'width: 250px; margin-left:0px;',
                            "has_empty" => true
                        ));
                    ?>
                    <button id="searchOutboundCallsButton" class="input-btn btn-yellow" style="margin-left: 10px">Search</button>
                </div>
            </div>
            <input type="hidden" value="outbound_call" name="price_type" />
         </form>
    </div>
    <div class="clear-height"></div>
    <div class="button_container">
        <div id="searchTableOutboundCallsPriceResult">
            <table id="dataGridOutboundCallsPriceResult"></table>
            <div id="dataGridOutboundCallsPricePager"></div>
        </div>
        <div class="clear-height"></div>
    </div>
</div>
<div class="hide">
    <div id="divEditPhoneNumberPrice" title="Edit Phone Number Price" class="input-form dialog-form"></div>
    <div id="divEditPhoneNumberPriceRemark" title="Remarks" class="input-form dialog-form"></div>
    <div id="divEditOutboundCallsPrice" title="Edit Outbound Calls Price" class="input-form dialog-form"></div>
    <div id="divEditOutboundCallsPriceRemark" title="Remarks" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$(document).ready( function() {
    var isEnterpriseCustomer = <?php if ($isEnterpriseCustomer) { echo 'true';} else {echo 'false';}?>;
    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    
    // Call search method
    searchPhoneNumberPrice();
    searchOutboundCallsPrice();

    // Search email button
    $('#searchPhoneNumberButton').button().live('click', function() {
    	searchPhoneNumberPrice();
    	return false;
    });
    $('#country_code_31').live('change', function(){
        searchPhoneNumberPrice();
    });
    
    // Search email button
    $('#searchOutboundCallsButton').button().live('click', function() {
    	searchOutboundCallsPrice();
    	return false;
    });
    $('#country_code_32').live('change', function(){
        searchOutboundCallsPrice();
    });
	
    /**
     * Search data
     */
    function searchPhoneNumberPrice() {
        $("#dataGridPhoneNumberPriceResult").jqGrid('GridUnload');
        var upcharge1 = 'Upcharge 1(abs) <span class="managetables-icon icon_help tipsy_tooltip" title="You can set this upcharge (absolute value) to add your charges to the Setup Fee"></span>';
        var upcharge2 = 'Upcharge 2(%) <span class="managetables-icon icon_help tipsy_tooltip" title="You can set this upcharge (percentage value) to add your charges to the Monthly Fee"></span>';
        var upcharge3 = 'Upcharge 3(abs) <span class="managetables-icon icon_help tipsy_tooltip" title="You can set this upcharge (absolute value) to add your charges to the charges for incoming calls per minute"></span>';

        var hiddenUpcharge1 = !isEnterpriseCustomer;
        var hiddenUpcharge2 = !isEnterpriseCustomer;
        var hiddenUpcharge3 = !isEnterpriseCustomer;
        var url = '<?php echo base_url() ?>account/phones_price_setting/index';
        $("#dataGridPhoneNumberPriceResult").jqGrid({
            url: url,
            postData: $('#searchPhoneNumberForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 200,
            width: 1100,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPhoneNumberPricePager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames:['id','Country','Number Type','Setup Fee', upcharge1, 'Monthly Fee', upcharge2, 'Minute (excl. fwd)', upcharge3, 'Charging', 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country_code_3',index:'country_code_3', width: 100, sortable: false },
               {name:'type',index:'type', width: 100, sortable: false},
               {name:'one_time_fee',index:'one_time_fee', width: 100, sortable: false},
               {name:'one_time_fee_upcharge',index:'one_time_fee_upcharge', width: 130, hidden: hiddenUpcharge1, sortable: false},
               {name:'recurring_fee',index:'recurring_fee', width: 100, sortable: false},
               {name:'recurring_fee_upcharge',index:'recurring_fee_upcharge', width: 130, hidden: hiddenUpcharge2, sortable: false},
               {name:'per_min_fee',index:'per_min_fee', width: 100, sortable: false},
               {name:'per_min_fee_upcharge',index:'per_min_fee_upcharge', width: 130, hidden: hiddenUpcharge3, sortable: false},
               {name:'recurrence_interval',index:'recurrence_interval', width: 80, sortable: false},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionFormater, hidden: !isEnterpriseCustomer}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $('.tipsy_tooltip').tipsy({gravity: 'sw'});
                // $.autoFitScreenById('dataGridPhoneNumberPriceResult', 1100);
            }
        });
    }
	
    function actionFormater(cellvalue, options, rowObject) {
        if (isEnterpriseCustomer) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-phone-number-price" data-id="' + cellvalue + '" title="Edit"></span></span>';
        } else {
            return '';
        }
    }
    
    // Process when user click on edit icon.
    $('.managetables-phone-number-price').live('click', function () {
        editPhoneNumberPrice(this);
    });
    
    $('.managetables-icon-comment-add-phone-number').live('click', function () {
        editPhoneNumberPriceRemark(this);
    });
    
    // Edit phone number price
    function editPhoneNumberPrice(elem) {
        var id = $(elem).data('id');
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divEditPhoneNumberPrice').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            open: function () {
                var loadUrl = '<?php echo base_url()?>account/phones_price_setting/edit_price_phone_number' + '?id=' + id;
                $(this).load(loadUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    savePhoneNumberPrice();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditPhoneNumberPrice').dialog('option', 'position', 'center');
        $('#divEditPhoneNumberPrice').dialog('open');
    }
    // Save Phone number price
    function savePhoneNumberPrice() {
        var submitUrl = $('#addEditPricePhoneNumberForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPricePhoneNumberForm",
            success: function (data) {
                if (data.status) {
                    $('#divEditPhoneNumberPrice').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        searchPhoneNumberPrice();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
    
    // Edit phone number price
    function editPhoneNumberPriceRemark(elem) {
        var id = $(elem).data('id');
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divEditPhoneNumberPriceRemark').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            open: function () {
                var loadUrl = '<?php echo base_url()?>account/phones_price_setting/edit_price_phone_number_remark' + '?id=' + id;
                $(this).load(loadUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    savePhoneNumberPriceRemark();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditPhoneNumberPriceRemark').dialog('option', 'position', 'center');
        $('#divEditPhoneNumberPriceRemark').dialog('open');
    }
    // Save Phone number price
    function savePhoneNumberPriceRemark() {
        var submitUrl = $('#addEditPricePhoneNumberRemarkForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPricePhoneNumberRemarkForm",
            success: function (data) {
                if (data.status) {
                    $('#divEditPhoneNumberPriceRemark').dialog('close');
                    $.displayInfor(data.message, null, function () {
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
    
    /**
     * Search data
     */
    function searchOutboundCallsPrice() {
        $("#dataGridOutboundCallsPriceResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>account/phones_price_setting/index';
        var upcharge4 = 'Upcharge 4(%) <span class="managetables-icon icon_help tipsy_tooltip" title="You can set this upcharge (percentage value) to add your charges to the charges for forwarded calls per minute"></span>';
        var hiddenUpcharge4 = !isEnterpriseCustomer;
        $("#dataGridOutboundCallsPriceResult").jqGrid({
            url: url,
            postData: $('#searchOutboundCallsForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: 200,
            width: 650,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridOutboundCallsPricePager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames:['id','Country','Price per Minute', upcharge4, 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country',index:'country', width: 250 , sortable: false},
               {name:'usage_fee',index:'usage_fee', width: 150, sortable: false},
               {name:'usage_fee_upcharge',index:'usage_fee_upcharge', width: 150, hidden: hiddenUpcharge4, sortable: false},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionOutboundCallsFormater, hidden: !isEnterpriseCustomer}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $('.tipsy_tooltip').tipsy({gravity: 'sw'});
                // $.autoFitScreenById('dataGridOutboundCallsPriceResult', 1100);
            }
        });
    }
	
    function actionOutboundCallsFormater(cellvalue, options, rowObject) {
        if (isEnterpriseCustomer) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-outbound-calls-price" data-id="' + cellvalue + '" title="Edit"></span></span>';
        } else {
            return '';
        }
    }

    // Process when user click on edit icon.
    $('.managetables-outbound-calls-price').live('click', function () {
        editOutboundCallPrice(this);
    });
    // Process when user click on edit icon.
    $('.managetables-icon-comment-add-outbound-call').live('click', function () {
        editOutboundCallPriceRemark(this);
    });
    
    
    // Edit phone number price
    function editOutboundCallPrice(elem) {
        var id = $(elem).data('id');
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divEditOutboundCallsPrice').openDialog({
            autoOpen: false,
            height: 200,
            width: 450,
            modal: true,
            open: function () {
                var loadUrl = '<?php echo base_url()?>account/phones_price_setting/edit_price_outbound_call' + '?id=' + id;
                $(this).load(loadUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    saveOutboundCallPrice();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditOutboundCallsPrice').dialog('option', 'position', 'center');
        $('#divEditOutboundCallsPrice').dialog('open');
    }
    // Save Phone number price
    function saveOutboundCallPrice() {
        var submitUrl = $('#addEditPriceOutboundCallForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPriceOutboundCallForm",
            success: function (data) {
                if (data.status) {
                    $('#divEditOutboundCallsPrice').dialog('close');
                    $.displayInfor(data.message, null, function () {
                        searchOutboundCallsPrice();
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
    
    
    // Edit phone number price
    function editOutboundCallPriceRemark(elem) {
        var id = $(elem).data('id');
        // Clear control of all dialog form
        $('.dialog-form').html('');
        // Open new dialog
        $('#divEditOutboundCallsPriceRemark').openDialog({
            autoOpen: false,
            height: 300,
            width: 450,
            modal: true,
            open: function () {
                var loadUrl = '<?php echo base_url()?>account/phones_price_setting/edit_price_outbound_call_remark' + '?id=' + id;
                $(this).load(loadUrl, function () {
                });
            },
            buttons: {
                'Save': function () {
                    saveOutboundCallPriceRemark();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#divEditOutboundCallsPriceRemark').dialog('option', 'position', 'center');
        $('#divEditOutboundCallsPriceRemark').dialog('open');
    }
    // Save Phone number price
    function saveOutboundCallPriceRemark() {
        var submitUrl = $('#addEditPriceOutboundCallRemarkForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: "addEditPriceOutboundCallRemarkForm",
            success: function (data) {
                if (data.status) {
                    $('#divEditOutboundCallsPriceRemark').dialog('close');
                    $.displayInfor(data.message, null, function () {
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
});
</script>
