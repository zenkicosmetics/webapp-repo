<div id="searchTableResult" style="margin-top: 10px;">
    <table id="dataGridStatus"></table>
    <div id="dataGridStatusPager"></div>
</div>

<script>
$(document).ready(function() {

    loadStatusGrid(); // Call search method
    
    function loadStatusGrid (){
        $("#dataGridStatus").jqGrid('GridUnload');
        
        var url = '<?php echo base_url() ?>' + 'admin/settings/loadLanguageStatus';
        var lastSel;
        
        $("#dataGridStatus").jqGrid({
            url: url,
            postData: $('#languageSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height:200, 
            width: 490, 
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridStatusPager",
            sortname: 'country_code',
            viewrecords: true,
            shrinkToFit: true,
            captions: '',
            colNames: [
                '<?php admin_language_e('settings_view_admin_languagestatus_ID'); ?>', 
                '<?php admin_language_e('settings_view_admin_languagestatus_Language'); ?>',
                '<?php admin_language_e('settings_view_admin_languagestatus_Status'); ?>'
            ],
            colModel: [
                {name:'id',index:'id', hidden: true},
                {name:'code',index:'code', width:300, sortable:false},
                {name:'status',index:'status', width:100, sortable:false, align:"center", editable: true, edittype: "select", formatter: 'select', editoptions: { value: {0: 'Inactive', 1: 'Activate'} } },
            ],
            loadComplete: function() {
                $("#dataGridStatus").jqGrid('setGridWidth', 490, true);
                //Enable edit mode for all row
                var $this = $(this), ids = $this.jqGrid('getDataIDs'), i, l = ids.length;
                for (i = 0; i < l; i++) {
                    $this.jqGrid('editRow', ids[i], true);
                }
            }
        });
    };

});   

</script>

