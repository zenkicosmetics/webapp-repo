<div id="showActvityLink_gridwraper<?php echo $type ?>" style="margin: 0px;">
    <div id="showActvityLink_searchTableResult<?php echo $type ?>" style="margin-top: 10px;">
        <table id="showActvityLink_dataGridResult<?php echo $type ?>"></table>
        <div id="showActvityLink_dataGridPager<?php echo $type ?>"></div>
    </div>
</div>
<div class="clear-height"></div>
<script type="text/javascript">
    $(document).ready(function(){
        load_activity();
        
        function load_activity(){
            $("#showActvityLink_dataGridResult<?php echo $type ?>").jqGrid('GridUnload');
            $("#showActvityLink_dataGridResult<?php echo $type ?>").jqGrid({
                url: "<?php echo base_url()?>invoices/get_detail_activity?type=<?php echo $type ?>",
                mtype: 'POST',
                datatype: "json",
                width: 900,
                height: 250,
                rowNum: '<?php echo APContext::getPagingSetting();?>',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
                pager: "#showActvityLink_dataGridPager<?php echo $type ?>",
                sortname: 'activity_type',
                sortorder: 'desc',
                viewrecords: true,
                shrinkToFit: false,
                multiselect: true,
                multiselectWidth: 40,
                captions: '',
                colNames: ['From','Envelope', "Customer", 'Location', 'Activity','Price'],
                colModel: [
                    {name: 'from_customer_name', index: 'from_customer_name', width: 200},
                    {name: 'envelope_id', index: 'envelope_id', width: 200},
                    {name: 'customer_id', index: 'customer_id', width: 120},
                    {name: 'location_id', index: 'location_id', width: 80},
                    {name: 'activity', index: 'activity', width: 130, sortable: false},
                    {name: 'fee', index: 'fee', width: 100, align: "center", sortable: false}
                ],
                loadComplete: function () {
                    //$.autoFitScreen(DATAGRID_WIDTH);
                }
            });
        }
    });
</script>