<div id="displayAllCollectItemsContainer" style="margin:10px; max-width: 855px">
    <div id="displayAllCollectItems_searchTableResult" style="margin: 10px;">
    	<table id="displayAllCollectItems_dataGridResult"></table>
    	<div id="displayAllCollectItems_dataGridPager"></div>
    </div>
</div>
<script type="text/javascript">
$(document).ready( function() {
    var package_id = '<?php echo $package_id; ?>';
    // Search envelope customs
    searchEnvelopeCustomsInPackage(package_id);
    
    /**
     * Search data
     * 
     * package_id: The package identify of this package
     */
    function searchEnvelopeCustomsInPackage(package_id) {
        $("#displayAllCollectItems_dataGridResult").jqGrid('GridUnload');
        var envelope_id = $("#declare_customs_envelope_id").val();
        var url = '<?php echo base_url() ?>mailbox/search_all_collect_item?envelope_id=' +envelope_id;
        $("#displayAllCollectItems_dataGridResult").jqGrid({
            url: url,
            postData: { package_id: package_id},
            mtype: 'POST',
            datatype: "json",
            width: '580',
            height: '250',
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#displayAllCollectItems_dataGridPager",
            sortname: '',
            sortorder: '',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['From', 'Type', 'Weight', 'Date'],
            colModel: [
                {name: 'from_customer_name', index: 'from_customer_name', sortable: false},
                {name: 'type', index: 'type', width: 120, sortable: false},
                {name: 'weight', index: 'weight', width: 100, sortable: false},
                {name: 'incomming_date', index: 'incomming_date', width: 100, sortable: false}
            ],
            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
            },
            loadComplete: function () {

            }
        });
    }
});
</script>