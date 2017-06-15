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
    <b>You request an international shipping for an item. For this you need to declare to us the specific content of your package, so that we can prepare a correct customs declaration. <br/> 
    Please provide 1. description and material 2. quantity and 3. customs value in EUR for every position in you package.*<br/>
    </b>
    <div id="envelopeCustomsDetail" style="margin:10px; max-width: 855px">
        <table class="displayInfo" style="width: 100%">
            <?php if($shipping_type == 1){?>
            <tr>
                <th style="width: 30%;text-align: left;">From: <?php echo $envelope->from_customer_name;?></th>
                <th style="width: 20%;text-align: left;">Type: <?php echo Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);?></th>
                <th style="width: 20%;text-align: left;">Weight: <?php echo number_format($envelope->weight / 1000, 2) . 'kg'?> </th>
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
                <th style="width: 30%;text-align: left;">From: <a id="declare_customs_multiple_link" data-package_id="<?php echo $package_id; ?>" href="#" style="text-decoration: underline">multiple</a></th>
                <th style="width: 20%;text-align: left;">Type: multiple</th>
                <th style="width: 20%;text-align: left;">Weight: <?php echo number_format($weight / 1000, 2) . 'kg'?></th>
                <th style="width: 30%;text-align: left;">
                    <div style="float: left;">
                        Date: multiple
                    </div>
                </th>
            </tr>
            <?php }?>
        </table>
    </div>
   
    <div id="searchTableResult" style="margin: 10px;">
    	<table id="dataGridResult"></table>
    	<div id="prowed1"></div>
    </div>
    <div style="margin:10px; max-width: 855px">
        <table border="0" style="width: 100%">
            <tr>
                <td><button id="addNewRecord" style="margin-left: 10px">Add New</button><button id="calButton" style="margin-left: 10px">Calculate</button></td>
                <td width="100px" style="text-align: right"><label style="text-align: right">Sum:</label></td>
                <td width="100px"><span id="total_quantity">0</span></td>
                <td width="200px"><span id="total_cost">0</span></td>
            </tr>
        </table>
    </div>
    <div class="clear-height"></div>
    <input type="hidden" id="declare_customs_envelope_id" name="envelope_id" value="<?php echo $envelope_id;?>" />
	<b>*Please note, that with confirming this customs declaration you take full responsibility for the accuracy and completeness of your declaration. <br/>
	You confirm to assume any liability resulting from wrongful declaration or custom problems due to illegal or inappropriate items or their declaration by the laws of the recipients country. <br/>
	The recipient can be charged with customs duties, cost of handling and VAT in regard of the regulations in the recipients country.<br/>
    </b>
</div>
<br />
<div style="text-align: right">
    <label><b>Please enter phone number of recipient</b></label> <input type="text" value="<?php if (!empty($phone_number)) {echo $phone_number;}?>" style="height: 24px;" name="phone_number" class="input-text" id="phone_number" maxlength="20" />
</div>
<br />
<div style="text-align: right">
    <label><b>Cost of this customs declaration: <span id="cost_of_custom_declaration_estimate"></span> EUR</b></label>
</div>

<div class="hide" style="display: none;">
    <div id="displayDeclareCustomsMultiple" title="Item Included In This Collect forwarding" class="input-form dialog-form">
    </div>
    
    <input type="hidden" name="declareCustomForm_custom_outgoing_01" id="declareCustomForm_custom_outgoing_01" value="<?php echo $pricing_map['custom_declaration_outgoing_01'] ?>" />
    <input type="hidden" name="declareCustomForm_custom_outgoing_02" id="declareCustomForm_custom_outgoing_02" value="<?php echo $pricing_map['custom_declaration_outgoing_02'] ?>" />
</div>

<script type="text/javascript">
$(document).ready( function() {
    var data = [];
    var lastSel = 0;
    var temp_data_row = {
             id: '',
             material_name: '',
             hs_code: '',
             country: "",
             quantity: '', 
             cost: ''
    };

    // Search envelope customs
    searchEnvelopeCustoms();

    $('#addNewRecord').button({
        icons: {
            primary: "ui-icon-plus"
        }
    }).click(function(){
        // Get total record
    	var total_record = $("#dataGridResult").getGridParam("reccount");
    	var datarow = temp_data_row;
    	datarow.id = total_record + 1;
    	data.push(datarow);
    	$("#dataGridResult").jqGrid('addRowData',total_record + 1, datarow);
    });

    $('#calButton').button({}).click(function(){
        // Get total record
    	var gridData = $("#dataGridResult").jqGrid('getGridParam','data');
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

        $("#total_cost").html(total_cost);
        $("#total_quantity").html(total_quantity);
        
        // show estimate cost of custom.
        var estimate_cost_custom = 0;
        if(total_cost > 1000){
            estimate_cost_custom = $("#declareCustomForm_custom_outgoing_01").val();
        }else if(total_cost > 0 && total_cost <= 1000){
            estimate_cost_custom = $("#declareCustomForm_custom_outgoing_02").val();
        }
        $("#cost_of_custom_declaration_estimate").html(estimate_cost_custom);
    });
	
    /**
     * Search data
     */
    function searchEnvelopeCustoms() {
        // var url = '<?php echo base_url()?>mailbox/load_declare_customs';
        for (var i = 0; i < 1; i++) {
            data.push({id: i+1, material_name: '', quantity: '', cost: ''});
        }
        $("#dataGridResult").jqGrid('GridUnload');
        $("#dataGridResult").jqGrid({
            data: data,
            datatype: "local",
            height: 120,
            width: 850,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            sortname: 'material_name',
            rownumbers: true,
            rownumWidth: 30,
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: true,
            altRows:true,
            multiselect: false,
            multiselectWidth: 40,
            altclass:'jq-background',
            captions: '',
            editable: true,
            cellEdit: true,
            cellsubmit: 'clientArray',
            colNames:['ID','Description and Material', 'H.S.Code', 'Country of origin','Quantity','Customs value in EUR'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'material_name', editable: true, index:'material_name', sortable:false, width: 370},
               {name:'hs_code',index:'hs_code', editable: true , sortable:false, width:100},
               {name:'country', index:"country", sortable:false ,editable: true, edittype:'custom', width: 150, editoptions:{custom_element:myElement, custom_value:myValue}},
               {name:'quantity',index:'quantity', editable: true , sortable:false, width:100},
               {name:'cost',index:'cost', editable: true , sortable:false, width: 200}
            ],
            onSelectRow: function(id){
                if(id && id!==lastSel){ 
                   $('#dataGridResult').restoreRow(lastSel); 
                   lastSel=id; 
                }
                $('#dataGridResult').editRow(id, true); 
           }
        });
        $("#dataGridResult").jqGrid('navGrid',"#prowed1",{edit:true,add:true,del:true});
    }
    
    function myElement(value, options){
        <?php 
            $html = '<select role="select" style="width:98%">';
            $html .= '<option value="">&nbsp;</option>';
            foreach($countries as $c){
                $html .= '<option value="'.$c->country_name.'">'.$c->country_name.'</option>';
            }
            $html .= "</select>";
        ?>
        return '<?php echo $html; ?>';
    }

    function myValue(elem, operation, value){
        if(operation === 'get') {
            return $(elem).val();
        } else if(operation === 'set') {
            $('select',elem).val(value);
        }
    }
    
    /**
     * When user click to multiple link the system will open all envelopes item of this package 
     */
    $('#declare_customs_multiple_link').live('click', function() {
        var package_id = $(this).data('package_id');
        // Open new dialog
	$('#displayDeclareCustomsMultiple').openDialog({
		autoOpen: false,
		height: 420,
		width: 650,
		modal: true,
		open: function() {
            $(this).load("<?php echo base_url() ?>mailbox/display_all_collect_item?package_id=" + package_id, function() {
            });
		},
		buttons: {
		}
	});
	
	$('#displayDeclareCustomsMultiple').dialog('option', 'position', 'center');
	$('#displayDeclareCustomsMultiple').dialog('open');
        return false;
    });
});
</script>