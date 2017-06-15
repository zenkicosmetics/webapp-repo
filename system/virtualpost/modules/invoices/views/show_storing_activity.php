<div id="showStoringActvityLink_gridwraper" style="margin: 0px;">
    <div id="showStoringActvityLink_searchTableResult" style="margin-top: 10px;">
        <table id="showStoringActvityLink_dataGridResult"></table>
        <div id="showStoringActvityLink_dataGridPager"></div>
    </div>
</div>
<div class="clear-height"></div>
<script type="text/javascript">
    $(document).ready(function(){
        load_activity();
        
        function load_activity(){
            $("#showStoringActvityLink_dataGridResult").jqGrid('GridUnload');
            $("#showStoringActvityLink_dataGridResult").jqGrid({
                url: "<?php echo base_url()?>invoices/get_storing_activity",
                mtype: 'POST',
                datatype: "json",
                width: 900,
                height: 250,
                rowNum: '<?php echo APContext::getPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#showStoringActvityLink_dataGridPager",
                sortname: 'id',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: ['From', 'Envelope', "Customer", 'Envelope type','Incomming date', 'Sent out on', 'Trash on', 'Previous total', 'Current month', 'Price', 'Amount'],
                colModel: [
                    {name: 'from_customer_name', index: 'from_customer_name', width: 200},
                    {name: 'envelope_id', index: 'envelope_id', width: 200},
                    {name: 'customer_id', index: 'customer_id', width: 120},
                    {name: 'type', index: 'type', width: 145, sortable: false},
                    {name: 'incomming_date', index: 'incomming_date', width: 100, sortable: false},
                    {name: 'sent_out', index: 'sent_out', width: 100, sortable: false},
                    {name: 'trash', index: 'trash', width: 100, sortable: false},
                    {name: 'total1', index: 'total1', width: 100, sortable: false},
                    {name: 'total2', index: 'total2', width: 100, sortable: false},
                    {name: 'price', index: 'price', width: 100, sortable: false},
                    {name: 'amount', index: 'amount', width: 100, sortable: false}
                ],
                loadComplete: function () {
                    //$.autoFitScreen(DATAGRID_WIDTH);
                }
            });
        }
    });
</script>