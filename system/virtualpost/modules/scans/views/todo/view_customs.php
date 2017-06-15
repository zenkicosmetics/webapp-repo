<?php 
$envelope_class = 'envelop';
$item_class = 'scan_email';
// Setting class for envelope icon
if ($envelope->envelope_scan_flag == null) {
	$envelope_class = 'envelop';
} else if ($envelope->envelope_scan_flag == '0') {
	$envelope_class = 'envelop-yellow';
} else if ($envelope->envelope_scan_flag == '1') {
	$envelope_class = 'envelop-blue';
}

// Setting class for item scan
if ($envelope->item_scan_flag == null) {
	$item_class = 'scan_email';
} else if ($envelope->item_scan_flag == '0') {
	$item_class = 'scan_email-yellow';
} else if ($envelope->item_scan_flag == '1') {
	$item_class = 'scan_email-blue';
}
?>
<div class="button_container">
    <b style="font-weight: bold;color: red;margin-left: 10px;">
    Attention: for this shipment a customs declaration /proforma invoice has to be prepared
    </b>
    <div id="envelopeCustomsDetail" style="margin:10px; max-width: 855px">
        <table class="displayInfo" style="width: 100%">
            <tr>
                <th style="width: 35%;text-align: left;">From: <?php echo $envelope->from_customer_name;?></th>
                <th style="width: 20%;text-align: left;">Type: <?php echo Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);?></th>
                <th style="width: 15%;text-align: left;">Weight: <?php echo number_format($envelope->weight, 0) . $envelope->weight_unit?> </th>
                <th style="width: 30%;text-align: left;">
                    <div style="float: left;">
                        Date: <?php echo APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y')?>
                    </div>
                    <div style="float: left;">
                        <div id="envelope_class" class="ym-gl  wrap <?php echo $envelope_class?>">&nbsp;</div>
                    	<div id="scan_class" style="margin-top: -3px;" class="ym-gl  wrap <?php echo $item_class?>">&nbsp;</div>
                	</div>
                </th>
            </tr>
        </table>
    </div>
   
    <div id="searchTableResultEnvelopeCustoms" style="margin: 10px;">
    	<table id="dataGridResultEnvelopeCustoms"></table>
    </div>
    <input type="hidden" id="declare_customs_envelope_id" name="envelope_id" value="<?php echo $envelope_id;?>" />
</div>
<div class="clear-height"></div>

<script type="text/javascript">
$(document).ready( function() {    
	// Search envelope customs
	searchEnvelopeCustoms();
	
	/**
	 * Search data
	 */
	function searchEnvelopeCustoms() {
		var envelope_id = $('#declare_customs_envelope_id').val();
		var url = '<?php echo base_url()?>scans/todo/load_declare_customs?envelope_id=' + envelope_id;
		$("#dataGridResultEnvelopeCustoms").jqGrid('GridUnload');
        $("#dataGridResultEnvelopeCustoms").jqGrid({
            url: url,
        	datatype: "json",
        	height: 200,
            width: 850,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            sortname: 'material_name',
            rownumbers: true,
            footerrow:true,
            userDataOnFooter:true,
            rownumWidth: 30,
            sortorder: 'asc',
            viewrecords: true,
            shrinkToFit: true,
            altRows:true,
            multiselect: false,
            multiselectWidth: 40,
            altclass:'jq-background',
            captions: '',
            colNames:['ID','Description and Material','HS Code','Country','Quantity','Customs value in EUR'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'material_name', index:'material_name', sortable:false, width: 200},
               {name:'hs_code',index:'hs_code', sortable:false, width:100},
               {name:'country',index:'country', sortable:false, width:100},
               {name:'quantity',index:'quantity', sortable:false, width:100},
               {name:'cost',index:'cost',sortable:false, width: 140}
            ],
            loadComplete: function(data) {
                var rows = data.rows;
                var sum_cost = 0;
                for(i=0; i< rows.length; i ++){
                    sum_cost += rows[i].cell[2] * rows[i].cell[3];
                }

            	// $('#shippingEnvelopeForm_package_size').val(sum_weight);
            	$("#dataGridResultEnvelopeCustoms").jqGrid('footerData','set',{quantity:'Total Cost:',cost: sum_cost});

                // Set all envelopes id
            	var envelope_ids = $("#popupShippingItemDataGridResult").jqGrid('getDataIDs');
                $('#dataGridResultEnvelopeCustoms').val(envelope_ids);
            }
        });
	}
});
</script>