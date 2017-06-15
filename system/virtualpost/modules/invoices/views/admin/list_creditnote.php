<div class="ym-grid">
    <div id="gridwraper" style="margin: 0px;">
        <div id="searchTableResult" style="margin-top: 10px;">
            <table id="dataGridResult"></table>
            <div id="dataGridPager"></div>
        </div>
    </div>
    <div class="clear-height"></div>
</div>
<script>
    $(document).ready(function () {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>admin/invoices/list_creditnote_by_location?location_id=<?php echo $location_id ?>&ym=<?php echo $yearmonth; ?>';
        $("#dataGridResult").jqGrid({
            url: url,
            mtype: 'POST',
            datatype: "json",
            width: 900,
            height: 350,
            rowNum: '<?php echo APContext::getAdminPagingSetting(); ?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
            pager: "#dataGridPager",
            sortname: 'created_date',
            sortorder: 'desc',
            viewrecords: true,
            shrinkToFit: false,
            multiselect: true,
            multiselectWidth: 40,
            captions: '',
            colNames: ['', 'Customer ID', 'Name', 'Email', 'Status', 'Credit note ID', 'Date', 'Amount', 'PDF'],
            colModel: [
                {name: 'customer_id', index: 'customer_id', hidden: true},
                {name: 'customer_code', index: 'customer_code', width: 90},
                {name: 'user_name', index: 'user_name', width: 130, sortable: false},
                {name: 'email', index: 'email', width: 130},
                {name: 'status', index: 'status', width: 80, sortable: false},
                {name: 'invoice_code', index: 'invoice_code', width: 100, sortable: false},
                {name: 'invoice_month', index: 'invoice_month', width: 100, sortable: false},
                {name: 'total_invoice', index: 'total_invoice', width: 100, sortable: false},
                {name: 'invoice_code', ndex: 'invoice_code', width: 80, sortable: false, align: "center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function (row_id, iRow, iCol, e) {
            },
            loadComplete: function () {
            }
        });

        function actionFormater(cellvalue, options, rowObject) {
            url = encodeURIComponent('<?php echo APContext::getFullBasePath()?>admin/report/export_credit_by_location/'+cellvalue+'?location_id=<?php echo $location_id ?>&type=credit&tmp=1&customer_id='+ rowObject[0]);
            
            return '<a class="pdf" target="_blank" href="<?php echo base_url()?>admin/report/view_pdf_invoice?url='+url+'" id="'+cellvalue+'" data-customer-id="' + rowObject[0] + '">&nbsp;</a>';
        }
        
        
    });
</script>