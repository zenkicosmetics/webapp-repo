<div class="header">
    <h2 style="font-size:  20px; margin-bottom: 10px"><?php admin_language_e('email_view_admin_email_index_EmailTemplateManagement'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="customerEmailForm" action="<?php echo base_url() ?>admin/email" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <input type="text" id="customerEmailForm_enquiry" name="enquiry" style="width: 250px"
                       value="" class="input-txt" maxlength=255 />
                <select name="relevant_enterprise_account" id="relevant_enterprise_account" class="input-width" style="width: 180px; margin-left: 5px;">
                    <option value="0"><?php admin_language_e('email_view_admin_email_index_ClevverMail'); ?></option>
                    <option value="1"><?php admin_language_e('email_view_admin_email_index_EnterpriseCustomers'); ?></option>
                </select>
                <?php
                echo my_form_dropdown(array(
                    "data" => $languages,
                    "value_key" => 'language',
                    "label_key" => 'language',
                    "value" => '',
                    "name" => 'language',
                    "id" => 'language',
                    "clazz" => 'input-width',
                    "style" => 'width: 150px; margin-left: 5px;',
                    "has_empty" => false
                ));
                ?>
                <button id="searchEmailButton" class="admin-button" style=" margin-left: 5px;"><?php admin_language_e('email_view_admin_email_index_SearchBtn'); ?></button>
                <button type="button" id="addButton" class="admin-button" style=" margin-left: 5px;"><?php admin_language_e('email_view_admin_email_index_AddTranslationBtn'); ?></button>
            </div>
        </div>
    </form>
</div>

<div class="clear-height"></div>
<div class="button_container">
    <div id="searchTableResult">
        <table id="dataGridResult"></table>
        <div id="dataGridPager"></div>
    </div>
    <div class="clear-height"></div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        // Call search method
        searchemails();

        // Search email button
        $('#searchEmailButton').button().live('click', function () {
            searchemails();
            return false;
        });

        /**
         * Search data
         */
        function searchemails() {
            $("#dataGridResult").jqGrid('GridUnload');
            var url = '<?php echo base_url() ?>admin/email';
            var tableH = $.getTableHeight() + 13;
            $("#dataGridResult").jqGrid({
                url: url,
                postData: $('#customerEmailForm').serializeObject(),
                mtype: 'POST',
                datatype: "json",
                height: tableH,
                width: ($(window).width() - 40),
                rowNum: '100',
                rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE); ?>],
                pager: "#dataGridPager",
                sortname: 'id',
                viewrecords: true,
                shrinkToFit: true,
                captions: '',
                colNames: [
                    '<?php admin_language_e('email_view_admin_email_index_ID'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_CustomerID'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_Code'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_AvailableForEnterprise'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_Slug'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_UsedBy'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_Subject'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_Description'); ?>', 
                    '<?php admin_language_e('email_view_admin_email_index_Action'); ?>'
                ],
                colModel: [
                    {name: 'id', index: 'id', hidden: true},
                    {name: 'customer_id', index: 'customer_id', hidden: true},
                    {name: 'code', index: 'code', width: 150, sortable: false},
                    {name: 'relevant_enterprise_account', index: 'relevant_enterprise_account', width: 120, align: "center"},
                    {name: 'slug', index: 'slug', width: 250},
                    {name: 'used_by', index: 'used_by', width: 100, sortable: false},
                    {name: 'subject', index: 'subject', width: 325},
                    {name: 'Description', index: 'Description', width: 320},
                    {name: 'id', index: 'id', width: 75, sortable: false, align: "center", formatter: actionFormater}
                ],
                // When double click to row
                ondblClickRow: function (row_id, iRow, iCol, e) {
                    var data_row = $('#dataGridResult').jqGrid("getRowData", row_id);
                    console.log(data_row);
                },
                loadComplete: function () {
                    $.autoFitScreen(($(window).width() - 40));
                }
            });
        }

        function actionFormater(cellvalue, options, rowObject) {
            return '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-edit" data-customer_id="'+rowObject[1]+'" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('email_view_admin_email_index_Edit'); ?>"></span></span>'
                    + '<span style="display:inline-block;"><span class="managetables-icon managetables-icon-delete" data-customer_id="'+rowObject[1]+'" data-id="' + cellvalue 
                    + '" title="<?php admin_language_e('email_view_admin_email_index_Delete'); ?>"></span></span>';
        }


        /**
         * Process when user click to add button.
         */
        $('#addButton').button().live('click', function () {
            var url = "<?php echo base_url() ?>admin/email/add?language=" + $('#language').val();
            url += '&relevant_enterprise_account=' + $('#relevant_enterprise_account').val();
            window.location = url;
            return false;
        });

        /**
         * Process when user click to edit icon.
         */
        $('.managetables-icon-edit').live('click', function () {
            var id = $(this).data('id');
            var customer_id = $(this).data('customer_id');
            window.location = "<?php echo base_url() ?>admin/email/edit?id=" + id + "&customer_id=" + customer_id;
            return false;
        });

        /**
         * Process when user click to delete icon.
         */
        $('.managetables-icon-delete').live('click', function () {
            var id = $(this).data('id');
            var customer_id = $(this).data('customer_id');

            // Show confirm dialog
            $.confirm({
                message: '<?php admin_language_e('email_view_admin_email_index_ConfirmDeleteMessage'); ?>',
                yes: function () {
                    var submitUrl = '<?php echo base_url() ?>admin/email/delete/' + id+ "?customer_id=" + customer_id;
                    $.ajaxExec({
                        url: submitUrl,
                        success: function (data) {
                            if (data.status) {
                                // Reload data grid
                                searchemails();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
