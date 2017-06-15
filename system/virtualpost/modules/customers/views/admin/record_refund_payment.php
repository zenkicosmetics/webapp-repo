<div class="button_container mailbox">
    <table style="border: 0px solid #dadada;margin: 5px 10px;width: 750px">
        <tr>
            <td style="width: 100px;">Customer</td>
            <td><?php echo $customer->email;?></td>
            <td style="width: 100px;">Rev Share(%)</td>
            <td>
                <input type="text" value="50" id="createDirectChargeDetailResult_RevShare" class="input-width" style="width: 100px" />
            </td>
        </tr>
        <tr>
            <td style="width: 100px;">Invoice Date test</td>
            <td style="width: 100px;"><input type="text" value="<?php echo date($date_format )?>" id="createDirectChargeDetailResult_InvoiceDate" class="input-width input_date" style="width: 100px" /></td>
            <td style="width: 100px;">Location</td>
            <td>
                <?php
                    echo my_form_dropdown(array (
                            "data" => $list_locations,
                            "value_key" => 'id',
                            "label_key" => 'location_name',
                            "value" => APUtils::getPrimaryLocationBy($customer_id),
                            "name" => 'location_id',
                            "id" => 'createDirectChargeDetailResult_location_id',
                            "clazz" => 'input-txt',
                            "style" => 'width: 200px',
                            "has_empty" => false 
                    ));
                    ?>
            </td>
        </tr>
        
    </table>
    <div id="createDirectChargeDetailResult" style="margin: 10px;">
    	<table id="dataGridDirectChargeDetailResult"></table>
    	<div id="prowed1"></div>
    </div>
    <button id="addNewRecord" style="margin-left: 10px">Add New</button>
    <button id="calculateData" style="margin-left: 5px">Calculate</button>
    <div class="clear"></div>
    <table style="border: 0px solid #dadada;margin: 5px 10px;width: 750px">
        <tr>
            <td style="width: 80%; border-top: 1px solid #dadada;">Sum</td>
            <td id="createDirectChargeDetailResult_Sum" style="width: 20%; border-top: 1px solid #dadada;text-align: right;"></td>
        </tr>
        <tr>
            <td style="width: 80%">VAT</td>
            <td id="createDirectChargeDetailResult_VAT" style="width: 20%; text-align: right;"></td>
        </tr>
        <tr>
            <td style="width: 80%;border-top: 1px solid #dadada;">Total</td>
            <td id="createDirectChargeDetailResult_Total"  style="width: 20%; border-top: 1px solid #dadada; text-align: right;"></td>
        </tr>
    </table>
<?php 
    // Gets digital good and shipping vat rate.
    $vat  = APUtils::getVatRateOfCustomer($customer_id);
    $digital_good_vat = APUtils::getVatRateOfDigitalGoodBy($customer_id);
    $shipping_vat = APUtils::getVatRateOfShippingByCustomer($customer_id);
?>
</div>
<div class="clear-height"></div>
<div class="hide">
    <input type="hidden" id="createDirectCharge_customer_id" value="<?php echo $customer_id;?>" />
    <input type="hidden" id="createDirectCharge_vat" value="<?php echo $vat->rate;?>" />
    <input type="hidden" id="vat_local_service_id" value="<?php echo $vat->rate;?>" />
    <input type="hidden" id="vat_digital_good_id" value="<?php echo $digital_good_vat->rate;?>" />
    <input type="hidden" id="vat_shipping_id" value="<?php echo $shipping_vat->rate;?>" />
</div>
<script type="text/javascript">
$(document).ready( function() {
	var date_format = "<?php echo APConstants::DATEFORMAT_05;//($date_format == APConstants::DATEFORMAT_DEFAULT)?  APConstants::DATEFORMAT_04 : APConstants::DATEFORMAT_05 ?>";
	$(".datepicker").datepicker();
    $(".datepicker").datepicker("option", "dateFormat", date_format);

    var currentDate = '<?php echo date(APConstants::DATEFORMAT_DEFAULT)?>';
	var data = [];
	var temp_data_row = {
	         id: '',
	         description: '',
	         quantity: '', 
	         net_price: '',
	         vat_case: ''
	};
	var lastSel = 0;
	$('.input_date').datepicker();
	$('.input_date').datepicker( "option", "dateFormat", date_format);
	$('.input_date').val(currentDate);
    $('#calculateData').button();
	$('#addNewRecord').button({
    	icons: {
            primary: "ui-icon-plus"
        }
    }).click(function(){
        // Get total record
    	var total_record = $("#dataGridDirectChargeDetailResult").getGridParam("reccount");
    	var datarow = temp_data_row;
    	datarow.id = total_record + 1;
    	data.push(datarow);
    	$("#dataGridDirectChargeDetailResult").jqGrid('addRowData',total_record + 1, datarow);
    });
	
	// Search envelope customs
	createDirectCharge();
	
	/**
	 * Search data
	 */
	function createDirectCharge() {
		for (var i = 0; i < 1; i++) {
		    data.push({id: i+1, description: '', quantity: '', net_price: '', vat_case: ''});
		}
		$("#dataGridDirectChargeDetailResult").jqGrid('GridUnload');
        $("#dataGridDirectChargeDetailResult").jqGrid({
            data: data,
        	datatype: "local",
        	height: 150,
            width: 750,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            sortname: 'id',
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
            colNames:['ID','Description','Quantity','Net Price', 'VAT Case'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'description', editable: true, index:'description', sortable:false, width: 270},
               {name:'quantity',index:'quantity', editable: true , sortable:false, width:100},
               {name:'net_price',index:'net_price', editable: true , sortable:false, width: 100},
               {name:'vat_case', index:"vat_case", sortable:false ,editable: true, edittype:'custom', width: 200, editoptions:{custom_element:myElement, custom_value:myValue}}
            ],
            onSelectRow: function(id){
                if(id && id!==lastSel){ 
                   $('#dataGridDirectChargeDetailResult').restoreRow(lastSel); 
                   lastSel=id; 
                }
                $('#dataGridDirectChargeDetailResult').editRow(id, true); 
           }
        });

        function myElement(value, options){
            <?php 
                $html = '<select role="select" style="width:98%">';
                $html .= '<option value=" ">&nbsp;</option>';
                foreach($vat_cases as $case){
                    $html .= '<option value="'.$case->label.'">'.$case->label.'</option>';
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
        
        $("#createDirectChargeDetailResult_location_id").change(function(){
            var location_id = $(this).val();
            getRevShareOfLocation(location_id)
        });

        getRevShareOfLocation($("#createDirectChargeDetailResult_location_id").val());
        function getRevShareOfLocation(location_id) {
            $.ajaxExec({
                url: "<?php echo base_url() ?>admin/customers/get_rev_location/"+ location_id,
                success: function (data) {
                    if (data.status) {
                        $("#createDirectChargeDetailResult_RevShare").val(data.data.rev_share);
                    } else {
                        $("#createDirectChargeDetailResult_RevShare").val("50");
                    }
                }
            });
        }

        
        $("#dataGridDirectChargeDetailResult").jqGrid('navGrid',"#prowed1",{edit:true,add:true,del:true});
	}
});
</script>
