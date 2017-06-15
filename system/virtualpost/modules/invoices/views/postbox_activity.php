<div id="searchTableResult" style="margin: 10px;">
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        load_activity();
        
        function load_activity(){
            $("#dataGridResult").jqGrid('GridUnload');
            $("#dataGridResult").jqGrid({
                url: "<?php echo base_url()?>invoices/get_postbox_activity",
                mtype: 'POST',
                datatype: "json",
                width: 560,
                height: 300,
                rowNum: '<?php echo APContext::getPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#dataGridPager",
                sortname: 'postbox_id',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: ['', 'Customer', 'Postbox name', 'Type', 'Postbox fee'],
                colModel: [
                    {name: 'postbox_id', index: 'postbox_id', hidden: true},
                    {name: 'customer_id', index: 'customer_id', width: 120},
                    {name: 'postbox_name', index: 'postbox_name', width: 200},
                    {name: 'type', index: 'type', width: 100, align: "center"},
                    {name: 'postbox_fee', index: 'postbox_fee', width: 100, align: "center", sortable: false}
                ],
                loadComplete: function () {
                    //$.autoFitScreen(DATAGRID_WIDTH);
                }
            });
        }
    });
</script>