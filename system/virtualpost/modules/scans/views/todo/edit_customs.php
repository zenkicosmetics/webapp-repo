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
    <div id="declare_customs_form_envelopeCustomsDetail" style="margin:10px; max-width: 855px">
        <table class="displayInfo" style="width: 100%">
            <?php if($shipping_type == 1){?>
            <tr>
                <th style="width: 35%;text-align: left;">From: <?php echo $envelope->from_customer_name;?></th>
                <th style="width: 20%;text-align: left;">Type: <?php echo Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);?></th>
                <th style="width: 15%;text-align: left;">Weight: <?php echo number_format($envelope->weight / 1000, 2) . 'kg'?> </th>
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
            <?php }else{?>
            <tr>
                <th style="width: 35%;text-align: left;">From: multiple</th>
                <th style="width: 20%;text-align: left;">Type: multiple</th>
                <th style="width: 15%;text-align: left;">Weight: <?php echo number_format($weight / 1000, 2) . 'kg'?></th>
                <th style="width: 30%;text-align: left;">
                    <div style="float: left;">
                        Date: multiple
                    </div>
                </th>
            </tr>
            <?php }?>
        </table>
    </div>
   
    <div id="declare_customs_form_searchTableResultEnvelopeCustoms" style="margin: 10px;">
    	<table id="declare_customs_form_dataGridResultEnvelopeCustoms"></table>
        <div id="prowed1"></div>
    </div>
    
    <div style="max-width: 848px">
        <table border="0" style="width: 100%">
            <tr>
                <td>
                    <button id="declare_customs_form_addNewRecord">Add New</button>
                    <button id="declare_customs_form_calButton" style="margin-left: 10px">Calculate</button>
                </td>
                <td width="100px" style="text-align: right"><label style="text-align: right">Sum:</label></td>
                <td width="100px"><span id="declare_customs_form_total_quantity">0</span></td>
                <td width="200px"><span id="declare_customs_form_total_cost">0</span></td>
            </tr>
        </table>
    </div>
    <input type="hidden" id="declare_customs_form_envelope_id" name="envelope_id" value="<?php echo $envelope_id;?>" />
</div>
<div class="clear-height"></div>

<input type="hidden" name="declareCustomForm_custom_outgoing_01" id="declareCustomForm_custom_outgoing_01" value="<?php echo $pricing_map['custom_declaration_outgoing_01'] ?>" />
<input type="hidden" name="declareCustomForm_custom_outgoing_02" id="declareCustomForm_custom_outgoing_02" value="<?php echo $pricing_map['custom_declaration_outgoing_02'] ?>" />
    
<script type="text/javascript">
$(document).ready( function() {
    
    // country
    var countries = '<?php echo json_encode($countries);?>';

    $('button').button();
    
    var data = [];
    var lastSel = 0;
    var temp_data_row = {
        id: '',
        material_name: '',
        quantity: '', 
        cost: ''
	};
    
    init_declare_custom_data();
    function init_declare_custom_data(){
        var envelope_id = $('#declare_customs_form_envelope_id').val();
        var url = '<?php echo base_url()?>scans/todo/load_declare_customs';
        $.ajaxExec({
            url: url,
            data: {
              envelope_id: envelope_id
            },
            success: function (response) {
                console.log("response", response);
                if(response.total > 0){
                    for(i=0; i< response.rows.length; i++){
                        var item = {
                            id: i+1,
                            material_name: response.rows[i].cell[1],
                            hs_code: response.rows[i].cell[2],
                            country: response.rows[i].cell[3],
                            quantity: response.rows[i].cell[4], 
                            cost: response.rows[i].cell[5]
                        }
                        data.push(item);
                    }
                }
                
                // Search envelope customs
                searchEnvelopeCustomsForm();
            }
        });
    }
	/**
	 * Search data
	 */
	function searchEnvelopeCustomsForm() {
		var envelope_id = $('#declare_customs_form_envelope_id').val();
		var url = '<?php echo base_url()?>scans/todo/load_declare_customs?envelope_id=' + envelope_id;
		$("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('GridUnload');
        $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid({
            //url: url,
        	//datatype: "json",
            datatype: "local",
            data: data,
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
            editable: true,
            cellEdit: true,
            loadonce: true,
            cellsubmit: 'clientArray',
            multiselectWidth: 40,
            altclass:'jq-background',
            captions: '',
            colNames:['ID','Description and Material', 'H.S.Code', 'Country of origin','Quantity','Customs value in EUR'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'material_name', index:'material_name',editable: true, sortable:false, width: 300},
               {name:'hs_code',index:'hs_code', editable: true , sortable:false, width:100},
               {name:'country', index:"country", sortable:false ,editable: true, edittype:'custom', width: 150, editoptions:{custom_element:myElement, custom_value:myValue}},
               {name:'quantity',index:'quantity', editable: true , sortable:false, width:100},
               {name:'cost',index:'cost',sortable:false,editable: true, width: 140}
            ],
            loadComplete: function() {
                var sum_cost = 0;
                for(i=0; i< data.length; i++){
                    sum_cost += parseFloat(data[i].cost * data[i].quantity);
                }
                
                $("#declare_customs_form_total_cost").html(sum_cost);
                $("#declare_customs_form_total_quantity").html(data.length);

            	$("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('footerData','set',{quantity:'Total Cost:',cost: sum_cost});
            },
            onSelectRow: function(id){
                if(id && id!==lastSel){ 
                   $('#declare_customs_form_dataGridResultEnvelopeCustoms').restoreRow(lastSel); 
                   lastSel=id; 
                }
                
                $('#declare_customs_form_dataGridResultEnvelopeCustoms').editRow(id, true); 
           }
        });
        
        $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('navGrid',"#prowed1",{edit:true,add:true,del:true});
	}
    
    $('#declare_customs_form_addNewRecord').button({
    	icons: {
            primary: "ui-icon-plus"
        }
    }).click(function(){
        // Get total record
    	var total_record = $("#declare_customs_form_dataGridResultEnvelopeCustoms").getGridParam("reccount");
    	var datarow = temp_data_row;
    	datarow.id = total_record + 1;
    	data.push(datarow);
    	$("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('addRowData',total_record + 1, datarow);
        
    });

	$('#declare_customs_form_calButton').button({
    }).click(function(){
        // Get total record
    	var gridData = $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('getGridParam','data');
        var total_quantity = 0;
        var total_cost = 0;

        var lengthData = gridData.length;
        for (var i=0; i < lengthData; i++) {
            var data_row = gridData[i];
            if (data_row.material_name != '') {
                total_cost += parseFloat(data_row.cost) * parseInt(data_row.quantity);
                total_quantity += parseInt(data_row.quantity);
            }
        }
        
        $("#declare_customs_form_dataGridResultEnvelopeCustoms").jqGrid('footerData','set',{quantity:'Total Cost:',cost: total_cost});
        $("#declare_customs_form_total_cost").html(total_cost);
        $("#declare_customs_form_total_quantity").html(total_quantity);
    });
    
    function myElement(value, options){
        var list_countries = JSON.parse(countries);
        var html = '<select role="select" style="width:98%">';
        html += '<option value="">&nbsp;</option>';
        for(i=0; i< list_countries.length; i++){
            if(list_countries[i].id == value){
                html += '<option selected="selected" value="' + list_countries[i].country_name + '">' + list_countries[i].country_name + '</option>';
            }else{
                html += '<option value="' + list_countries[i].country_name + '">' + list_countries[i].country_name + '</option>';
            }
        }
        html += "</select>";
        return html;
    }

    function myValue(elem, operation, value){
        if(operation === 'get') {
            return $(elem).val();
         } else if(operation === 'set') {
            $('select',elem).val(value);
         }
    }
});
</script>