<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('price_view_admin_pricesphone_PriceForCustomerPhoneNumbers'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="searchPhoneNumberForm" action="<?php echo base_url()?>admin/price/phones" method="post">
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
                        "style" => 'width: 250px',
                        "has_empty" => true
                    ));
                ?>
                <button id="searchPhoneNumberButton" class="admin-button"><?php admin_language_e('price_view_admin_pricesphone_SearchBtn'); ?></button>
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

<div class="clear-height"></div>
<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('price_view_admin_pricesphone_PricesForForwarding'); ?></h2>
</div>
<!--Outbound Calls-->
<div class="ym-grid mailbox">
    <form id="searchOutboundCallsForm" action="<?php echo base_url()?>admin/price/phones" method="post">
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
                        "style" => 'width: 250px',
                        "has_empty" => true
                    ));
                ?>
                <button id="searchOutboundCallsButton" class="admin-button">Search</button>
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

<div class="hide">
    <div id="divEditPhoneNumberPrice" title="<?php admin_language_e('price_view_admin_pricesphone_EditPhoneNumberPrice'); ?>" class="input-form dialog-form"></div>
    <div id="divEditPhoneNumberPriceRemark" title="<?php admin_language_e('price_view_admin_pricesphone_Remarks'); ?>" class="input-form dialog-form"></div>
    <div id="divEditOutboundCallsPrice" title="<?php admin_language_e('price_view_admin_pricesphone_EditOutboundCallsPrice'); ?>" class="input-form dialog-form"></div>
    <div id="divEditOutboundCallsPriceRemark" title="<?php admin_language_e('price_view_admin_pricesphone_Remarks'); ?>" class="input-form dialog-form"></div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	
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
        var url = '<?php echo base_url() ?>admin/price/phones';
        var tableH = $.getTableHeight() - 30; //#1297 check all tables in the system to minimize wasted space 
        $("#dataGridPhoneNumberPriceResult").jqGrid({
            url: url,
            postData: $('#searchPhoneNumberForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space 
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPhoneNumberPricePager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames:[
                '<?php admin_language_e('price_view_admin_pricesphone_ID'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_Country'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_NumberType'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_SetupFee'); ?>', 
                '<?php admin_language_e('price_view_admin_pricesphone_Upcharge1'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_MonthlyFee'); ?>', 
                '<?php admin_language_e('price_view_admin_pricesphone_Upcharge2'); ?>', 
                '<?php admin_language_e('price_view_admin_pricesphone_Minute'); ?>', 
                '<?php admin_language_e('price_view_admin_pricesphone_Upcharge3'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_Charging'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country',index:'country', width: 100 },
               {name:'type',index:'type', width: 100},
               {name:'one_time_fee',index:'one_time_fee', width: 100},
               {name:'one_time_fee_upcharge',index:'one_time_fee_upcharge', width: 100},
               {name:'recurring_fee',index:'recurring_fee', width: 100},
               {name:'recurring_fee_upcharge',index:'recurring_fee_upcharge', width: 100},
               {name:'per_min_fee',index:'per_min_fee', width: 100},
               {name:'per_min_fee_upcharge',index:'per_min_fee_upcharge', width: 100},
               {name:'recurrence_interval',index:'recurrence_interval', width: 100},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreenById('dataGridPhoneNumberPriceResult', $( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }
	
    function actionFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-phone-number-price" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('price_view_admin_pricesphone_Edit'); ?>"></span></span>'
               + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-comment-add managetables-icon-comment-add-phone-number" data-id="' + cellvalue 
               + '" title="<?php admin_language_e('price_view_admin_pricesphone_Edit'); ?>"></span></span>';
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
                var loadUrl = '<?php echo base_url()?>admin/price/edit_price_phone_number' + '?id=' + id;
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
                var loadUrl = '<?php echo base_url()?>admin/price/edit_price_phone_number_remark' + '?id=' + id;
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
        var url = '<?php echo base_url() ?>admin/price/phones';
        var tableH = $.getTableHeight() - 30; //#1297 check all tables in the system to minimize wasted space 
        $("#dataGridOutboundCallsPriceResult").jqGrid({
            url: url,
            postData: $('#searchOutboundCallsForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space 
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridOutboundCallsPricePager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames:[
                '<?php admin_language_e('price_view_admin_pricesphone_ID'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_Country'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_PricePerMinute'); ?>',
                '<?php admin_language_e('price_view_admin_pricesphone_Upcharge'); ?>', 
                '<?php admin_language_e('price_view_admin_pricesphone_Action'); ?>'
            ],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'country',index:'country', width: 300 },
               {name:'usage_fee',index:'usage_fee', width: 200},
               {name:'usage_fee_upcharge',index:'usage_fee_upcharge', width: 200},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionOutboundCallsFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
                $.autoFitScreenById('dataGridOutboundCallsPriceResult', $( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }
	
    function actionOutboundCallsFormater(cellvalue, options, rowObject) {
        return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit managetables-outbound-calls-price" data-id="' + cellvalue 
                + '" title="<?php admin_language_e('price_view_admin_pricesphone_Edit'); ?>"></span></span>'
                + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-comment-add managetables-icon-comment-add-outbound-call" data-id="' 
                + cellvalue + '" title="<?php admin_language_e('price_view_admin_pricesphone_Edit'); ?>"></span></span>';
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
                var loadUrl = '<?php echo base_url()?>admin/price/edit_price_outbound_call' + '?id=' + id;
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
                var loadUrl = '<?php echo base_url()?>admin/price/edit_price_outbound_call_remark' + '?id=' + id;
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
