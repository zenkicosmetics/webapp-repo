<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/report/add_partner_receipt';
} else {
    $submit_url = base_url() . 'admin/report/edit_partner_receipt';
}
?>
<form id="addEditPartnerReceiptForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TblHdPartner'); ?><span class="required">*</span></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_partners,
                    "value_key" => 'partner_id',
                    "label_key" => 'partner_name',
                    "value" => $receipt->partner_id,
                    "name" => 'partner_id',
                    "id" => 'partner_id',
                    "clazz" => 'input-txt',
                    "style" => 'width: 260px',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TbtHdLocation'); ?><span class="required">*</span></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $list_locations,
                    "value_key" => 'id',
                    "label_key" => 'location_name',
                    "value" => $receipt->location_id,
                    "name" => 'location_id',
                    "id" => 'location_id',
                    "clazz" => 'input-txt',
                    "style" => 'width: 260px',
                    "has_empty" => true
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TbtHdDateRecp'); ?><span class="required">*</span></th>
            <td><input type="text" id="addEditPartnerReceiptForm_date_of_receipt" name="date_of_receipt"
                       value="<?php echo $receipt->date_of_receipt ?>"
                       class="input-width input_date" maxlength=20 style="width: 100px" /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TbtHdNetAmount'); ?><span class="required">*</span></th>
            <td><input type="text" id="addEditPartnerReceiptForm_net_amount" name="net_amount"
                       value="<?php echo $receipt->net_amount ?>"
                       class="input-width custom_autocomplete" maxlength=20 /></td>
        </tr>
        <tr>
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TbtHdDesc'); ?><span class="required">*</span></th>
            <td><textarea id="addEditPartnerReceiptForm_description" name="description" cols ="3" rows="5"
                       class="input-width custom_autocomplete" maxlength=500 placeholder='input comments'><?php echo $receipt->description ?></textarea></td>
        </tr>
        <tr>
            <!--#1296 add receipt scan/upload to receipts--> 
            <th><?php admin_language_e('report_views_admin_add_partner_receipt_TbtHdRecp'); ?><span class="required">*</span> <br> <span style="color:gray">(jpg, png, bmp, tif, pdf)</span> </th>
            <td>
                <!--local_file_path-->
                <input type="hidden" name="local_file_path" value="<?php echo isset($receipt) ? $receipt->local_file_path : ""; ?>" 
                       class="input-txt input-file-id  local_file_path"  />
                <input type="text" name="partner_file_id" class="input-txt input-file-name" style="width:262px;border-color:#a8a8a8" readonly
                       value="<?php echo isset($receipt) ? basename($receipt->local_file_path) : ''; ?>" />
                <!--upload button-->
                <button class="upload-button"  data-id="<?php echo isset($receipt) ? $receipt->id : '' ?>"
                        data-op="partner_receipt" data-old-data="<?php echo isset($receipt) ? '1' : ''; ?>">Upload</button>              
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type"
           value="<?php echo $action_type ?>" /> <input type="hidden" id="id"
           name="id" value="<?php echo $receipt->id ?>" />
</form>
<!-- display none --> 
<!--#1296 add receipt scan/upload to receipts--> 
<div style="display: none">    
    <form method="post">
        <input name="upload_file_input" id="upload_file_input" value="" type="file" />
    </form>
</div>
<!-- end display none --> 

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var currentDate = '<?php echo date('d.m.Y'); ?>';
        $('.input_date').datepicker();
        $('.input_date').datepicker("option", "dateFormat", "dd.mm.yy");
        $('.input_date').val(currentDate);


        // When change partner information
        $('#partner_id').live('change', function () {
            // Get value 
            var v_partner_id = $(this).val();
            // Url
            var url = '<?php echo base_url() ?>admin/partner/get_list_location_bypartner?partner_id=' + v_partner_id;
            
            if($('#location_id').val().length == 0 ){
                $.ajaxExec({
                    url: url,
                    success: function (locations) {
                        $('#location_id').bindData(locations, 'id', 'location_name', '');
                    }
                });
             }
        });

        // When change location information
          $('#location_id').live('change', function () {
            // Get value  
            var v_location_id = $(this).val();
            // Url
            var url = '<?php echo base_url() ?>admin/partner/get_partner_bylocation?location_id=' + v_location_id;

            if($('#partner_id').val().length == 0 ){
                $.ajaxExec({
                    url: url,
                    success: function (partner) {
                        $('#partner_id').bindData(partner, 'partner_id', 'partner_name', '');
                    }
                });
            }
          });
       
   
        //#1296 add receipt scan/upload to receipts 
        $('button').button();

        // When click upload button
        var item_click;
        $('.upload-button').live('click', function (e) {
            /*
             *  Method stops the default action of an element from happening, example:
             *  Prevent a submit button from submitting a form
             *  Prevent a link from following the URL
             */
            e.preventDefault();

            // Defined item_click is upload-button's class
            item_click = $(this);

            // Action click 
            $('#upload_file_input').click();

            return false;
        });

        //Action click upload file
        $('#upload_file_input').change(function (e) {
            /*
             *  Method stops the default action of an element from happening, example:
             *  Prevent a submit button from submitting a form
             *  Prevent a link from following the URL
             */
            e.preventDefault();

            // Defined variable
            myfile = $(this).val();
            var ext = myfile.split('.').pop();
            var v_op = $(item_click).data('op');
            var v_time = $.now();
            var submitUrl = "<?php echo base_url() ?>admin/report/";

            // Check extension of file
            if ((ext.toUpperCase() != "PDF")
                    && (ext.toUpperCase() != "JPG")
                    && (ext.toUpperCase() != "TIF")
                    && (ext.toUpperCase() != "BMP")
                    && (ext.toUpperCase() != "PNG")) {
                $.displayError('Please select PDF, JPG, TIF, BMP, PNG file to upload.');
                return;
            }

            // Check url's partner receipt
            if (v_op == "partner_receipt") {
                submitUrl += "upload_file?t=" + v_time;
            }

            // Upload file ajax
            $.ajaxFileUpload({
                id: 'upload_file_input',
                data: {
                    type: v_op,
                    input_file_client_name: 'upload_file_input'
                },
                url: submitUrl,
                resetFileValue: true,
                success: function (response) {
                    // response value: local_file_path, input-file-name
                    $(item_click).parent().find(".local_file_path").val(response.data.local_file_path);
                    $(item_click).parent().find(".input-file-name").val(myfile.split('\\').pop());
                }
            });

            // Return
            return false;
        });

    });
</script>
