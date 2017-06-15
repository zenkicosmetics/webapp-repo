<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('cases_view_admin_index_CasesSystem'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerSearchForm"
        action="<?php echo base_url()?>cases/admin/index" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 150px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('cases_view_admin_index_SearchText'); ?>:</label>
                </div>
                <div class="ym-g80 ym-gl">
                    <input type="text" id="searchCustomerForm_enquiry" name="enquiry" style="width: 248px" value="" class="input-txt" maxlength=255/>
                </div>
            </div>
        </div>
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl"
                    style="width: 150px; text-align: left;">
                    <label style="text-align: left;"><?php admin_language_e('cases_view_admin_index_FilterByStatus'); ?>:</label>
                </div>
                <div class="ym-g80 ym-gl">
                    <select id="status" name="status"
                        class="input-width">
                        <option value=""></option>
                        <option value="1"><?php admin_language_e('cases_view_admin_index_Processing'); ?></option>
                        <option value="2"><?php admin_language_e('cases_view_admin_index_Completed'); ?></option>
                    </select>
                     <button id="searchCaseButton" type="button"  class="admin-button"><?php admin_language_e('cases_view_admin_index_Search'); ?></button>
                     <button id="createVerificationReportButton" type="button"  class="admin-button"><?php admin_language_e('cases_view_admin_index_CreateVerificationReport'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="ym-clearfix"></div>
<div id="searchTableResult">
    <div class="clear-height"></div>
    <table id="dataGridResult"></table>
    <div id="dataGridPager"></div>
</div>
<div class="clear-height"></div>

<!-- Content for dialog -->
<div class="hide">
    <div id="createReport" title="<?php admin_language_e('cases_view_admin_index_Completed'); ?>Create verification report" class="input-form dialog-form">
    </div>
</div>
<div class="hide" style="display: none;">
    <a id="view_report_file" class="iframe"> <?php admin_language_e('cases_view_admin_index_PreviewFile'); ?></a>
</div>

<div id="caseItemCheckList" style="display: none;">
    <h3 style="font-size: 15px; font-weight: bold;"><?php admin_language_e('cases_view_admin_index_FullFillmentProcess'); ?></h3>
    <br />
    <div id="ChecklistTableResult">
        <div class="clear-height"></div>
        <table id="dataGridResult2"></table>
        <div id="dataGridPager2"></div>
    </div>
    <div class="clear-height"></div>
    <div style="display: none;">
        <form id="xx" method="post"></form>
    </div>

    <form id="hiddenAccessCustomerSiteForm" target="blank" action="<?php echo base_url()?>admin/customers/view_site" method="post">
        <input type="hidden" id="hiddenAccessCustomerSiteForm_customer_id" name="customer_id" value="" />
    </form>

    <div id="addCustomer" title="<?php admin_language_e('cases_view_admin_index_Completed'); ?>Add Customer" class="input-form dialog-form"></div>
    <div id="viewDetailCustomer" title="<?php admin_language_e('cases_view_admin_index_Completed'); ?>View Customer Details" class="input-form dialog-form">
    </div>
</div>
<style>
<!--
.state-complete {
    color: #BEBEBE;
}

.state-todo {
    font-weight: bold !important;
}
-->
</style>
<script type="text/javascript">
$(document).ready( function() {

    //#1297 check all tables in the system to minimize wasted space
    var tableH = $.getTableHeight() + 10;

    // Call search method
    searchCases();

    /**
     * #1054 verification reporting
    * Process when user click to add group button
    */
    $('button').button();
    $('#createVerificationReportButton').click(function() {

        $('.dialog-form').html('');
            $('#createReport').openDialog({
                autoOpen: false,
                height: 299,
                width: 491,
                modal: true,
                open: function() {
                    $(this).load("<?php echo base_url() ?>cases/admin/create_verification_report", function() {

                    });
                },
                buttons: {
                    'Create': function() {
                        var location  = $("#location_id").val();
                        var location_name  = $("#location_id option:selected").text();
                        var startDate = $("#createReportForm_startDate").val();
                        var endDate   = $("#createReportForm_endDate").val();
                        var url  = "<?php echo APContext::getFullBasePath(); ?>cases/todo/verification_report?location="+location+"&location_name="+location_name+"&startDate="+startDate+"&endDate="+endDate;
                        window.open(url);
                        return;
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#createReport').dialog('option', 'position', 'center');
            $('#createReport').dialog('open');

    });

    $('#searchCaseButton').button().live('click', function() {
        $('#caseItemCheckList').hide();
        searchCases();
    });

    /**
     * Seacrch data case
     * function searchCases()
     */
    function searchCases() {
        $("#dataGridResult").jqGrid('GridUnload');

        // Defined url
        var url = '<?php echo base_url() ?>cases/admin/index';

        // Search
        $("#dataGridResult").jqGrid({
            url: url,
            postData: $('#customerSearchForm').serializeObject(),
            mtype: 'POST',
            datatype: "json",
            height:tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager",
            viewrecords: true,
            shrinkToFit:false,
            sortname: 'modified_date',
            sortorder: 'desc',
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_admin_index_CustomerId'); ?>',
                '<?php admin_language_e('cases_view_admin_index_CustomerCode'); ?>',
                '<?php admin_language_e('cases_view_admin_index_OpeningDate'); ?>',
                '<?php admin_language_e('cases_view_admin_index_CaseName'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Email'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Description'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Product'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Country'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Status'); ?>',
                '<?php admin_language_e('cases_view_admin_index_LastActivity'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Action'); ?>',
                ''
            ],
            colModel:[
                {name:'id', index:'id', hidden: true},
                {name:'customer_id',index:'customer_id', hidden: true},
                {name:'customer_code', index:'customer_code', width: 150, formatter: toCustomerFormater02},
                {name:'opening date', index:'created_date', width: 120, align:"center"},
                {name:'case identifier', index:'case_identifier', width: 250},
                {name:'email', index:'email', width: 300, formatter: toCustomerFormater},
                {name:'description', index:'description', width: 335},
                {name:'Partner', index:'partner', width: 130, align:"center", sortable: false},
                {name:'country name', index:'country_name', width: 110, align:"center", sortable: false},
                {name:'status', index:'status', width: 120, align:"center", sortable: false},
                {name:'modified_date', index:'modified_date', width: 200, align:"center"},
                {name:'action',index:'action', width:100, sortable: false, align:"center", formatter: actionFormater},
                {name:'has_to_do', index:'has_to_do', hidden: true}
            ],
            afterInsertRow: function(id, data)
            {
                var trElement = $("#"+ id, $('#dataGridResult'));
                if(data.action == '2') {
                    trElement.addClass('state-complete');
                }
                if(data.has_to_do == '1') {
                    trElement.find("td").each(function() {
                        $(this).addClass('state-todo');
                    });
                }
            },
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
            },
            onSelectRow: function(row_id) {
                var data_row = $('#dataGridResult').jqGrid("getRowData",row_id);
                searchChecklist(data_row.id);
                $('#caseItemCheckList').show();
            },
            loadComplete: function() {
                $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
            }
        });
    }

    function actionFormater(cellvalue, options, rowObject) {
        if (cellvalue == '2') {
            return '<?php admin_language_e('cases_view_admin_index_Completed'); ?>';
        }  else if(rowObject[11] == '1'){
            // show process now
            if(rowObject[12] == '1'){
                return '<a  style="text-decoration:underline; color: #336699" href="<?php echo base_url(); ?>cases/admin/verification?case_id='+rowObject[0] 
                        +'"><?php admin_language_e('cases_view_admin_index_Completed'); ?>Process now</a>';
            }
            return '';
        } else {
            return '';
        }
    }

    function toCustomerFormater(cellvalue, options, rowObject) {
        if(rowObject[9] == '<?php admin_language_e('cases_view_admin_index_Completed'); ?>'){

            return '<a class="view_customer_detail" data-id="' + rowObject[1] + '" style="text-decoration: underline;color: #bebebe;"  >' + rowObject[5] + '</a>';
        }
            return '<a class="view_customer_detail" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[5] + '</a>';
    }

    function toCustomerFormater02(cellvalue, options, rowObject) {
            if(rowObject[9] == '<?php admin_language_e('cases_view_admin_index_Completed'); ?>'){
                return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;color: #bebebe;"  >' + rowObject[2] + '</a>';
            }

            return '<a class="access_customer_site" data-id="' + rowObject[1] + '" style="text-decoration: underline;"  >' + rowObject[2] + '</a>';
    }

    /** START SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */
    <?php include 'system/virtualpost/modules/customers/js/js_customer_info.php'; ?>
    /** END SOURCE TO VIEW CUSTOMER DETAIL AND DIRECT CHARGE */

    /**
     * Access the customer site
     */
    $('.access_customer_site').live('click', function() {
        var customer_id = $(this).attr('data-id');
        $('#hiddenAccessCustomerSiteForm_customer_id').val(customer_id);
        $('#hiddenAccessCustomerSiteForm').submit();
    });


    function searchChecklist(case_id){
        $("#dataGridResult2").jqGrid('GridUnload');
        var url = '<?php echo base_url() ?>cases/admin/show_checklist';

        $("#dataGridResult2").jqGrid({
            url: url,
            postData: {case_id: case_id},
            mtype: 'POST',
            datatype: "json",
            height:tableH, //#1297 check all tables in the system to minimize wasted space,
            width: ($( window ).width()- 40), //#1297 check all tables in the system to minimize wasted space
            rowNum: '<?php echo APContext::getAdminPagingSetting();?>',
            rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
            pager: "#dataGridPager2",
            viewrecords: true,
            shrinkToFit:false,
            captions: '',
            colNames:[
                'id',
                '<?php admin_language_e('cases_view_admin_index_Caseid'); ?>',
                '',
                '<?php admin_language_e('cases_view_admin_index_Basetaskname'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Milestone'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Created'); ?>',
                '<?php admin_language_e('cases_view_admin_index_LastUpdated'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Status'); ?>',
                '<?php admin_language_e('cases_view_admin_index_Responsible'); ?>',
                '<?php admin_language_e('cases_view_admin_index_LastConfirmedBy'); ?>',
                '<?php admin_language_e('cases_view_admin_index_YourTask'); ?>'],
            colModel:[
                {name:'id', index:'id', hidden: true},
                {name:'case_id', index:'case_id', hidden: true},
                {name:'status', index:'status', hidden: true},
                {name:'base_task_name', index:'base_task_name', hidden: true},
                {name:'milestone', index:'milestone', width: 390},
                {name:'created_date', index:'created_date', width: 200, align:"center"},
                {name:'updated_date', index:'updated_date', width: 200, align:"center"},
                {name:'status_name', index:'status_name', width: 200, align:"center"},
                {name:'responsible', index:'responsible', width: 350,align:"center"},
                {name:'last_confirmed_by', index:'last_confirmed_by', width: 300,align:"center"},
                {name:'action',index:'action', width:200, sortable: false, align:"center", formatter: actionFormater2}
            ],
            // When double click to row
            ondblClickRow: function(row_id,iRow,iCol,e) {
                var data_row = $('#dataGridResult2').jqGrid("getRowData",row_id);
            },
            loadComplete: function() {
                //$.autoFitScreen(1100);
                 $.autoFitScreen($( window ).width()- 40); //#1297 check all tables in the system to minimize wasted space
                $("a.xx").click(function(){
                    var _this=$(this);
                    $("form#xx").attr('action', _this.data("action"));
                    $('<input>').attr({
                        type: 'hidden',
                        value: _this.data("caseId"),
                        name: 'case_id'
                    }).appendTo('form#xx');
                    $('<input>').attr({
                        type: 'hidden',
                        value: _this.data("op"),
                        name: 'op'
                    }).appendTo('form#xx');
                    $("form#xx").submit();
                });
            }
        });
    }

    function actionFormater2(cellvalue, options, rowObject) {
        var product_id = rowObject[0];
        var case_id = rowObject[1];
        var status = rowObject[2];
        var action_name = rowObject[3];

        if ((product_id == '1')||(product_id == '5')){
            action = '<?php echo base_url() ?>cases/todo/review_' + action_name ;
        }

        return '<a class="xx" data-action="' + action + '" data-case-id="'+ case_id +'" data-op="'+
            cellvalue +'" style="text-decoration:underline; color: #336699">' + cellvalue + '</a>';
    }

    $('#view_report_file').fancybox({
        width: 1000,
        height: 800
    });

});
</script>