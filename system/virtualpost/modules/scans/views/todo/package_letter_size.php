<table id="packageLetterSizeDataGridResult"></table>
<div id="packageLetterSizeDataGridPager"></div>
<input type="hidden" id="select_package_letter_size_id" value="" />
<script type="text/javascript">
    searchPackageLetterSize();
    
    /**
     * Close scan window and load image
     */
    function searchPackageLetterSize() {
    	// $("#packageLetterSizeDataGridResult").jqGrid('GridUnload');
    	var url = '<?php echo base_url() ?>scans/todo/search_package_letter_size';
    	$("#packageLetterSizeDataGridResult").jqGrid({
        	url: url, 
        	postData: {
            },
        	datatype: "json",
        	height: '100%',
            width: 550,
            rowNum: '<?php echo APContext::getAdminPagingSetting()?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            sortname: 'id',
            shrinkToFit:false,
            altRows:true,
            altclass:'jq-background',
            captions: '',
            colNames:['ID','Name','Up to weight', 'Up to size', 'Price'],
            colModel:[
               {name:'id',index:'id', hidden: true},
               {name:'name',index:'name', width:150, align: 'left'},
               {name:'weight',index:'weight', width:100, align: 'left'},
               {name:'size',index:'size', width:175, align: 'left'},
               {name:'price',index:'price', width:75, align: 'left'}
            ],
            // When double click to row
            onSelectRow: function(row_id) {
                $('#select_package_letter_size_id').val(row_id);
            },
            loadComplete: function() {
            }
        });
    }
</script>