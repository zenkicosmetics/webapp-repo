<style>
.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>
<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('scan_view_completed_shipmentlist_ListOfShipment'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="locationReportingSearchForm"
          action="<?php echo base_url() ?>completed/shipment_list"
          method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('scan_view_completed_shipmentlist_Location'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $locations,
                        "value_key" => 'id',
                        "label_key" => 'location_name',
                        "value" => $location_id,
                        "name" => 'location_available_id',
                        "id" => 'location_available_id',
                        "clazz" => 'input-txt',
                        "style" => 'width: 150px',
                        "has_empty" => false
                    ));
                    ?>
                </div>        
                <div class="ym-g30 ym-gl" style="width: 100px">
                    <label style="text-align: left;margin-left: 20px;"><?php admin_language_e('scan_view_completed_shipmentlist_Month'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_year,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_year,
                        "name" => 'year',
                        "id" => 'year',
                        "clazz" => 'input-txt',
                        "style" => 'width: 70px',
                        "has_empty" => false
                    ));
                    ?>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_month,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_month,
                        "name" => 'month',
                        "id" => 'month',
                        "clazz" => 'input-txt',
                        "style" => 'width: 50px',
                        "has_empty" => false
                    ));
                    ?>
                </div>
                <div class="ym-g30 ym-gl">
                    <label style="text-align: left;margin-left: 30px;"><?php admin_language_e('scan_view_completed_shipmentlist_TotalPostalCharge'); ?> 
                        <span id="total_postal_charge">0,00</span> EUR</label>
                </div>
            </div>
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('scan_view_completed_shipmentlist_SearchText'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <input type="text" id="invoiceReportingSearchForm_keyword" name="enquiry" placeholder="<?php admin_language_e('scan_view_completed_shipmentlist_SearchPlaceHolderText'); ?>" 
                           style="width: 148px" value="" class="input-txt" />
                </div>
                <div class="ym-g20 ym-gl" style="width: 100px">
                    <label style="text-align: left;margin-left: 20px;"><?php admin_language_e('scan_view_completed_shipmentlist_Account'); ?></label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $shipping_api,
                        "value_key" => 'account_no',
                        "label_key" => 'account_no',
                        "value" => $account_no,
                        "name" => 'account_no',
                        "id" => 'account_no',
                        "clazz" => 'input-txt',
                        "style" => 'width: 123px',
                        "has_empty" => true
                    ));
                    ?>
                </div>
                <div class="ym-g30 ym-gl">
                    <label style="text-align: left;margin-left: 30px;"><?php admin_language_e('scan_view_completed_shipmentlist_IncludedUpcharge'); ?><span id="total_upcharge">0,00</span> EUR</label>
                </div>
                <button style="margin-left: 30px" id="locationReportingButton" class="admin-button"><?php admin_language_e('scan_view_completed_shipmentlist_Search'); ?></button>
            </div>
        </div>
    </form>
</div>
<div id="gridwraper" style="margin: 0px;">
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>

<div style="display:none;">
    <div id="viewCustomsDetail" title="<?php admin_language_e('scan_view_completed_shipmentlist_ViewCustomsDetail'); ?>" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    //#1297 check all tables in the system to minimize wasted space 
    var tableH = $.getTableHeight() + 12; 
    
    $('button').button();
    
    searchShipment();
    
    $("#dataGridResult").jqGrid({
        mtype: 'POST',
        datatype: '{"page":"1","total":0,"records":0}',
        height: tableH, //#1297 check all tables in the system to minimize wasted space
        width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
        colNames:[
            '', 
            '', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_Date'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_CustomerID'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_EnvelopeCode'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_Email'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_Carrier'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_ShippingService'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_TrackingNumber'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_Type'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_Weight'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_ProformaInv'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_Account'); ?>', 
            '<?php admin_language_e('scan_view_completed_shipmentlist_PostalCharge'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_Upcharge'); ?>',
            '<?php admin_language_e('scan_view_completed_shipmentlist_CompletedBy'); ?>'
        ],
        colModel:[
            {name:'id',index:'id', hidden: true},
            {name:'envelope_id',index:'envelope_id', hidden: true},
            {name:'shipping_date',index:'shipping_date', width: 50},
            {name:'customer_code',index:'customer_code', width: 50},
            {name:'envelope_code',index:'envelope_code', width: 50},
            {name:'email',index:'email', width: 50},
            {name:'carrier_id',index:'carrier_id', width: 50},
            {name:'shipping_service_id',index:'shipping_service_id', width: 50, sortable: false},
            {name:'tracking_number',index:'tracking_number', width: 50},
            {name:'type',index:'type', width: 50, sortable: false},
            {name:'weight',index:'weight', width: 50, sortable: false},
            {name:'customs_id',index:'customs_id', width: 50, sortable: false},
            {name:'api_account_no',index:'api_account_no', width: 50},
            {name:'postal_charge',index:'postal_charge', width: 75, sortable: false},
            {name:'upcharge',index:'upcharge', width: 75, sortable: false},
            {name:'completed_by',index:'completed_by', width: 100, sortable: false}
        ]
    });
    
    /*
     *  Search shipment 
     *  function searchShipment()
     */
    function searchShipment() {
        
        $("#dataGridResult").jqGrid('GridUnload');
        
        // Url defined 
        var url = '<?php echo base_url() ?>scans/completed/search_shipping_list';
        
        // JqGrid table
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#locationReportingSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height: tableH, //#1297 check all tables in the system to minimize wasted space
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
            pager: "#dataGridPager",
            sortname: 'customer_shipping_report.shipping_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:[
                '',
                '', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Date'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_CustomerID'); ?>',
                '<?php admin_language_e('scan_view_completed_shipmentlist_EnvelopeCode'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Email'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Carrier'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_ShippingService'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_TrackingNumber'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Type'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Weight'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_ProformaInv'); ?>',
                '<?php admin_language_e('scan_view_completed_shipmentlist_Account'); ?>',
                '<?php admin_language_e('scan_view_completed_shipmentlist_PostalCharge'); ?>', 
                '<?php admin_language_e('scan_view_completed_shipmentlist_Upcharge'); ?>',
                '<?php admin_language_e('scan_view_completed_shipmentlist_CompletedBy'); ?>'
            ],
            colModel:[
                {name:'id',index:'id', hidden: true},
                {name:'envelope_id',index:'envelope_id', hidden: true},
                {name:'shipping_date',index:'shipping_date', width: 100, align: "center"},
                {name:'customer_code',index:'customer_code', width: 150, sortable: false},
                {name:'envelope_code',index:'envelope_code', width: 200},
                {name:'email',index:'email', width: 200, sortable: false},
                {name:'carrier_id',index:'carrier_id', width: 150, sortable: false},
                {name:'shipping_service_id',index:'shipping_service_id', width: 200, sortable: false},
                {name:'tracking_number',index:'tracking_number', width: 150},
                {name:'type',index:'type', width: 80, align: "center", sortable: false},
                {name:'weight',index:'weight', width: 80, sortable: false, align: 'right'},
                {name:'customs_id',index:'customs_id', width: 80, sortable: false,formatter: actionFormater},
                {name:'api_account_no',index:'api_account_no', width: 80},
                {name:'postal_charge',index:'postal_charge', width: 80, sortable: false, align: 'right'},
                {name:'upcharge',index:'upcharge', width: 80, sortable: false, align: 'right'},
                {name:'completed_by',index:'completed_by', width: 135, sortable: false}
            ],
            loadComplete: function () {
                $.autoFitScreen(($( window ).width()- 40));
            }
        });
        
        // get total charge
        $.ajaxExec({
            url: '<?php echo base_url() ?>scans/completed/get_total_charge',
            data: $('#locationReportingSearchForm').serializeObject(),
            success: function(data) {
                $("#total_postal_charge").html(data.data.postal_charge);
                $("#total_upcharge").html(data.data.upcharge);
            }
        });
    }
    
    function actionFormater(cellvalue, options, rowObject) {
        if(cellvalue == '' || cellvalue == 0 || cellvalue == null){
            return "";
        }
        return '<span style="display:inline-block;"><a data-envelope_id="'+rowObject[1]+'" data-id="' + cellvalue 
                + '" class="view_proforma" title="view" style="color:blue;text-decoration: underline"><?php admin_language_e('scan_view_completed_shipmentlist_View'); ?></a></span>';
    }

    $("#locationReportingButton").click(function(e){
        e.preventDefault();
        searchShipment();
        return false;
    });
    
    $(".view_proforma").live('click',function(){
        var envelope_id = $(this).data("envelope_id");
        
        $('#viewCustomsDetail').html("");
        // Open popup allow customer declare customs information
        var submitUrl =  '<?php echo base_url() ?>scans/todo/view_customs?' + 'envelope_id=' + envelope_id + '&t='+Date.now();
        $('#viewCustomsDetail').openDialog({
            autoOpen: false,
            height: 490,
            width: 900,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
                $(this).load(submitUrl, function () {
                });
            },
            buttons: {
                'Close': function () {
                    $(this).dialog('destroy');
                }
            }
        });

        $('#viewCustomsDetail').dialog('option', 'position', 'center');
        $('#viewCustomsDetail').dialog('open');
        return false;
    });
});
</script>