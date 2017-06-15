<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 99%;margin-left: 20px;">
        <h2 style="border: 0px;">Email Template Management</h2>
    </div>
</div>

<div id="gridwraper" style="margin: 20px 0px 0px 20px;">
    <form id="customerEmailForm" action="<?php echo base_url()?>account/email_template/index" method="post">
        <div class="ym-grid input-item">
            <input type="text" id="customerEmailForm_enquiry" name="enquiry" style="width: 250px; margin-left: 0px;"
                        value="" class="input-width" maxlength=255 />
            <?php echo my_form_dropdown(array(
                "data" => $languages,
                "value_key" => 'language',
                "label_key" => 'language',
                "value" => '',
                "name" => 'language',
                "id" => 'language',
                "clazz" => 'input-width',
                "style" => 'width: 150px;',
                "has_empty" => false
             )); ?>
            <button id="searchEmailButton" class="input-btn btn-yellow" style="margin-left: 10px">Search</button>
        </div>
     </form>
    <div class="clear-height"></div>
    <div id="searchTableResult" style="margin-top: 10px;">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
    <div class="clear-height"></div>
</div>

<script type="text/javascript">
$(document).ready( function() {
	
    // Call search method
   searchemails();

	// Search email button
    $('#searchEmailButton').button().live('click', function() {
    	searchemails();
    	return false;
    });
	
    /**
     * Search data
     */
    function searchemails() {
        $("#dataGridResult").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>account/email_template/index';
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#customerEmailForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height:  ($(window).height()- 330),
            width: 1100,
            rowNum: '100',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            sortname: 'id',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames:['id','Code','Slug','Subject','Description', 'Action'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'code',index:'code', width: 150, sortable: false },
               {name:'slug',index:'slug', width: 250 },
               {name:'subject',index:'subject', width:325},
               {name:'Description',index:'Description', width:400},
               {name:'id',index:'id', width:75, sortable: false, align:"center", formatter: actionFormater}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                console.log(data_row);
            },
            loadComplete: function() {
                $.autoFitScreen(1100);
            }
        });
    }

    function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="fa fa-pencil-square-o managetables-icon-edit-email" data-id="' + cellvalue + '" title="Edit"></span></span>';
    }

    /**
     * Process when user click to edit icon.
     */
    $('.managetables-icon-edit-email').live('click', function() {
        var id = $(this).data('id');
        window.location = "<?php echo base_url() ?>account/email_template/edit?id="+id;
        return false;
    });
});
</script>
