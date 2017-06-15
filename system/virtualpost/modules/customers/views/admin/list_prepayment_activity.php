<div class="button_container">
    <h1 style="font-size: 18px; font-weight: bold;">List pending activity:</h1><br/>
    <span>Customer: <?php echo $customer->email;?></span><br/>
    <span>Open Balance Due: <?php echo $currency_short.' '.APUtils::convert_currency($open_balance_due, $currency_rate, 2, $decimal_separator);?></span><br/>
    <span>Open Balance This Month: <?php echo $currency_short.' '.APUtils::convert_currency($open_balance_this_month, $currency_rate, 2, $decimal_separator);?></span><br/>
    <div class="clear"></div>
    <form action="#" id="listPendingPrepaymentActivityForm" class="dialog-form">
        <input type="hidden" id="listPendingPrepaymentActivityForm_customer_id" name="id" value="<?php echo $customer_id;?>" />
        <input type="hidden" id="listPendingPrepaymentActivityForm_list_pending_activity" name="list_pending_activity" value="" />
    </form>
    <div id="listPendingPrepaymentActivityDetailResult" style="margin-top: 10px;margin-left: 0px;">
    	<table id="dataGridlistPendingPrepaymentActivityDetailResult"></table>
    	<div id="dateGridPaginglistPendingPrepaymentActivityDetailResult"></div>
    </div>
    
    <input type="hidden" id="listPendingPrepaymentActivityForm_total_avail_cost" name="" value="<?php echo $total_avail_cost ?>" />
</div>
<div class="clear-height"></div>
<div class="hide">
    
</div>
<script type="text/javascript">
$(document).ready( function() {
    // Call function load all pending prepayment activity
    loadListPendingPrePaymentActivity();
    
    // Load list pending prepayment
    function loadListPendingPrePaymentActivity() {
        var customer_id = $('#listPendingPrepaymentActivityForm_customer_id').val();
        $("#dataGridlistPendingPrepaymentActivityDetailResult").jqGrid('GridUnload');
	var url = '<?php echo base_url() ?>admin/customers/get_list_prepayment_activity?id=' + customer_id;
        $("#dataGridlistPendingPrepaymentActivityDetailResult").jqGrid({
            url: url,
            postData: {},
            mtype: 'POST',
            datatype: "json",
            width: 640,
            height:150,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dateGridPaginglistPendingPrepaymentActivityDetailResult",
            sortname: '',
            sortorder: '',
            viewrecords: true,
            shrinkToFit:false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames:['', 'From Name','Activity ID', 'Activty Name', 'EUR Net Amount', 'Net Amount', ''],
            colModel:[
               {name:'activity_id',index:'activity_id', hidden: true},
               {name:'from_name',index:'from_name', width: 200, sortable: false},
               {name:'activity_id',index:'activity_id', hidden: true},
               {name:'activity_name',index:'activity_name', width: 250, sortable: false},
               {name:'eur_net_amount',index:'eur_net_amount', hidden: true},
               {name:'net_amount',index:'net_amount', width:100, sortable: false, align:"center"},
               {name:'id',index:'id', hidden: true}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            loadComplete: function() {
            }
        });
    }
    
});
</script>