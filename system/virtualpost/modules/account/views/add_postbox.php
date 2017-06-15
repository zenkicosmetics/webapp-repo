<?php
    $submit_url = base_url().'account/add_postbox';
    $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
    $list_enterprise_account = array();
    $enterprise_account = new stdClass();
    $enterprise_account->id = APConstants::ENTERPRISE_CUSTOMER;
    $enterprise_account->name = lang('account_type_'.APConstants::ENTERPRISE_CUSTOMER);
    $list_enterprise_account[] = $enterprise_account;
    
    $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
?>
<form id="addPostboxForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th style="width: 150px;">Type </th>
            <td>
                <?php 
                    if ($isEnterpriseCustomer) {
                        echo my_form_dropdown(array(
                            "data" => $list_enterprise_account,
                            "value_key" => 'id',
                            "label_key" => 'name',
                            "value" => APConstants::ENTERPRISE_CUSTOMER,
                            "name" => 'account_type',
                            "id"    => 'addPostboxForm_account_type',
                            "clazz" => 'input-width',
                            "style" => 'width: 130px',
                            "has_empty" => false
                        ));
                    } else {
                        echo code_master_form_dropdown(array(
                            "code" => APConstants::ACCOUNT_TYPE,
                            "value" => $type,
                            "name" => 'account_type',
                            "id"    => 'addPostboxForm_account_type',
                            "clazz" => 'input-width',
                            "style" => 'width: 130px',
                            "has_empty" => false
                        ));
                    }
                ?>
            </td>
        </tr>
        <?php if($isPrimaryCustomer){ ?>
        <tr>
            <th style="width: 150px;">User </th>
            <td>
                <?php 
                    echo my_form_dropdown(array(
                        "data" => $list_users,
                        "value_key" => 'customer_id',
                        "label_key" => 'user_name',
                        "value" => $customer_id,
                        "name" => 'list_user_code',
                        "id"    => 'addPostboxForm_list_user_code',
                        "clazz" => 'input-width',
                        "style" => 'width: 130px',
                        "has_empty" => false
                    ));
                ?>
            </td>
        </tr>
        <?php }?>
        <tr>
            <th>Location</th>
            <td>
            	<?php 
            	    if (empty($location_id)) {
            	        $location_id = '';
            	    }
            	?>
               
                <?php echo my_form_dropdown(array(
                 "data" => $locate,
                 "value_key" => 'id',
                 "label_key" => 'location_name',
                 "value" => $location_id,
                 "name" => 'location',
                 "id"    => 'cust_location',
                 "clazz" => 'input-width',
                 "style" => 'width: 130px',
                 'show_only_express_shipping' => "1",   
                 "has_empty" => false
                 ));?>
                 <?php if($advanced): ?>
                 <span style="position: absolute;margin-top: 14px;"><?php echo ($location_id) ? (APUtils::number_format($pricing_map[3]['postbox_fee']->item_value)) : 0; ?> EUR/month <a id="location_price" style="text-decoration: underline;"> (full price list…)</a></span>
                <?php endif; ?>
               
                <div id="error_message" class="required" style="color: red;display: none;"><?php echo lang("only_add_first_location_for_free_and_private_postbox");?></div>
                
            </td>
        </tr>
        <tr>
            <th class="tipsy_tooltip" title="Please enter here the name, that you want to be found under at your new address. Please check correct spelling.">Name</th>
            <td id="name_postbox">
                 <input type="text" id="addPostboxForm_name" name="custname"  class="input-width custom_autocomplete" maxlength=255 />
                 <?php if($advanced){ ?>
                 <span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="Please enter here the persons name under which you want to receive mail. This name later has to be verified by two Ids"></span>
                 <?php } ?>
            </td>
        </tr>
        <tr>
            <th  class="tipsy_tooltip" title="Please enter here the company name, that you want to be found under at your new address. Please check correct spelling.">Company Name</th>
            <td>
                <input type="text" id="addPostboxForm_company" name="company" class="input-width custom_autocomplete" maxlength=255 />
                <?php if($advanced){ ?>
                 <span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="Please enter here the company name under which you want to receive mail. You have to verify the company name later with a valid document. Leave this field empty, if you are not a company"></span>
                 <?php } ?>
            </td>
        </tr>
        <tr>
            <th>Postbox ID <span class="required">*</span></th>
            <td>
                 <input type="text" id="addPostboxForm_postbox_name" name="postname" class="input-width custom_autocomplete" maxlength="35" />
                 <?php if($advanced){ ?>
                 <span style="vertical-align: unset;" class="managetables-icon icon_help tipsy_tooltip" original-title="This ID is only for your internal use. It is not required in the mail address. Chose your own ID if you like"></span>
                 <?php } ?>
            </td>
        </tr>
    </table>
    <input type="hidden" name="primary_location" id="primary_location" value="<?php echo $primary_location?>" />
    
    <!-- this param for user enterprise -->
    <input type="hidden" name="product_type" id="product_type" value="<?php echo $product_type?>" />
    <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id?>" />
</form>
<div class="hide">
    <div id="priceInfoWindow" title="Price Information" class="input-form dialog-form">
    </div>
    <div id="message_warning_location" style="display: none;">
        <div>
        <div align="left">Warning: Please note that the current address of the Tel Aviv location will be changed on 1st September 2017 to the following address:</div>
        <table border="0" width="100%" style="margin-top:-5px;">
            <tr>
                <td width="20%">&nbsp;</td>
                <td>
                    WE Tower – 9th floor <br />
                    150-152 Menachem Begin Road <br />
                    Tel Aviv 6492106 <br />
                    Israel 
                </td>
            </tr>
        </table>
        <div align="left">The current address will then no longer be valid. If you add a postbox now for the current address, 
            the postbox will automatically switch to the new address at that date. Mail arriving at the old address 
            will be accepted after the switch for another 6 months</div>
        </div>
    </div>
</div>
<?php //Asset::css('jquery.selectBoxIt.css'); ?>
<?php //Asset::js('jquery.selectBoxIt.min.js'); ?>
<?php //echo Asset::render(); ?>

<style>
/* 
 .selectboxit-container .selectboxit-options {
    width: 232px !important;
    height: 200px;
  }
  #cust_locationSelectBoxItText {max-width: 600px;}

@media screen and (-webkit-min-device-pixel-ratio:0) {
    #cust_locationSelectBoxItText {max-width: 600px; width: 250px !important;}
    
    .selectboxit-container .selectboxit-options {
    min-width: 250px !important;
    height: 200px;
  }
 
}
*/
span.managetables-icon {
    width: 16px;
    height: 16px;
    display: inline-block;
    text-indent: -99999px;
    cursor: pointer;
    vertical-align: middle;
}

.icon_help {
    background-image: url("<?php echo base_url('./system/virtualpost/themes/new_user2/images/1399041633_help-browser.png')?>");
}
.btn-disable
{
    cursor: not-allowed;
    pointer-events: none;

    /*Button disabled - CSS color class*/
    color: #c0c0c0;
    background-color: #ddd;

}
</style>
<script type="text/javascript">   
$(document).ready(function($){
    
    // hot feature
    check_tel_aviv_location('<?php echo $location_id;?>');
    
    $("#location_price").attr('rel',$("#cust_location").val());
    $('#location_price').live('click',function(){
        $('#priceInfoWindow').html('');
        $('#priceInfoWindow').openDialog({
            autoOpen: false,
            height: 640,
            width: 1150,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                var location_id = $('#location_price').attr('rel');
                $(this).load("<?php echo base_url() ?>customers/view_pricing?location_id="+location_id, function() {
                });
            },
            buttons: {
                'Close': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#priceInfoWindow').dialog('option', 'position', 'center');
        $('#priceInfoWindow').dialog('open');
    });

    $('.tipsy_tooltip').tipsy({gravity: 'sw'});
    
    $("#cust_location").live('change', function(){
        check_error_message();
        autofillPostboxId();
        
        return false;
    });
    autofillPostboxId();
    function autofillPostboxId(){
        
        var tmp = $('#cust_location').find(":selected").text();
        var location_id = $('#cust_location').find(":selected").val();
        //var tmp = $.trim($('#cust_locationSelectBoxItText').text());
        //var location_id = $('#cust_location').val();
        
        if($.trim(tmp) != ''){
            tmp = tmp.substring(0, 3).toUpperCase();
            //$('#addPostboxForm_postbox_name').val(tmp);
        	$.ajaxExec({
    			url: "<?php echo base_url() ?>account/get_max_postbox_code",
    			data:{location_id: location_id},
    			success: function(res) {
    				if (res.status) {
    					$('#addPostboxForm_postbox_name').val(tmp + res.data.code);
    				}
    			}
    		});
        }
    }

    check_error_message();

    $("#addPostboxForm_account_type").live('change', function(){
    	check_error_message();
    });

    function check_error_message(){
        var account_type = $("#addPostboxForm_account_type").val();
    	if( ( $("#primary_location").val() == $('#cust_location').val() || account_type == '<?php echo APConstants::BUSINESS_TYPE?>' )
    	    	||  account_type == '<?php echo APConstants::ENTERPRISE_CUSTOMER?>'){
            $('#error_message').hide();
        }else{
        	$('#error_message').show();
        }
    }
    
    /**
     * #1113 check for identical Name in postbox name field 
	 * Check name and display popup-warning message for sugggestion a unique name and then fill into name field when customer have inputted name in name field 
	 */
    $("#addPostboxForm_name").die("change");
	$('#addPostboxForm_name').live('change', function() {
        var baseUrl =  '<?php echo base_url(); ?>'; 
        var v_location = $('#cust_location').val();
        var v_custname = $('#addPostboxForm_name').val();
       
        // check name postbox name is not empty
        if(v_custname.length != 0){
            
            $.ajaxExec({
                url: baseUrl + 'account/check_suggestion_name_of_postbox',
                data: {location: v_location,custname:v_custname },
                success: function (data) {
                    if (data.status) {
                        // disable button submit
                        $("button:first").prop("disabled", true).find("span").addClass("btn-disable");
                        // Show confirm dialog
                        if(data.data.value != ''){
                            $.confirm({
                                message: data.data.message,
                                yes: function() {
                                    $('#addPostboxForm_name').val(data.data.value);
                                    $('#name_postbox').append( "<strong id='tick'>&#10003;</strong>" );
                                    $("button:first").prop("disabled", false).find("span").removeClass("btn-disable");
                                }
                            });
                        }else{
                            //$.displayInfor(data.message);
                            $('#name_postbox').append( "<strong id='tick'>&#10003;</strong>" );
                            $("button:first").prop("disabled", false).find("span").removeClass("btn-disable");
                        }
                    } else {
                        $('#name_postbox').append( "<strong id='tick'>&#10003;</strong>" );
                        $.displayError(data.message);
                    }
                }
            }); 
        }
        
        $("button:first").prop("disabled", false).find("span").removeClass("btn-disable");
        $('strong').remove( "#tick" );
        
       return false;
	});
    
    //Warning: Please note that the current address of the Tel Aviv location will be changed on 1st September 2017 to the following address
    function check_tel_aviv_location(location_id){
        if(location_id == 38){
            $.infor({message: $("#message_warning_location").html(), title: "Warning"});
        }
    }
    
    /**
     * submit add postbox.
     */
    $("#addPostboxWindow_saveBtn").die("click");
    $("#addPostboxWindow_saveBtn").live('click', function(){
        var new_postbox_type = $('#addPostboxForm_account_type').val();
        
        // update selected user.
        <?php if($isPrimaryCustomer){?>
            $("#customer_id").val($("#addPostboxForm_list_user_code").val());
        <?php }?>
        
        if (new_postbox_type == '3') {
            addedPostboxSubmit();
        } else {
            $.ajaxExec({
                url: '<?php echo base_url() ?>account/check_current_balance',
                data: {add: "add"},
                success: function (data) {
                    if (data.status === false) {
                        // show confirmation popup
                        openPaymentBox('add');
                    } else {
                        addedPostboxSubmit();
                    }
                }
            });
        }
        
        return false;
    });
    
    /**
     * open payment box. 
     * @param {type} method
     * @returns {undefined}     
     */
    function openPaymentBox (method) {
        var loadUrl = '<?php echo base_url(); ?>account/payment_box?method=' + method;
        $.openDialog('#openPaymentBoxWindow', {
            height: 220,
            width: 600,
            openUrl: loadUrl,
            title: "Confirmation",
            closeButtonLabel: "Close"
        });
        return;
    }
    
    /**
     * submit add postbox action.
     * @returns {undefined}
     */
    function addedPostboxSubmit(){
        // directly payment and added postbox.
        var submitUrl = $('#addPostboxForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        
        $(".ui-dialog-buttonpane button:contains('Submit')").attr('disabled', true);
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addPostboxForm',
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        $('#addPostboxWindow').dialog('close');
                        $("#addPostboxWindow").parent().find('.ui-dialog-buttonpane button:contains("Cancel")').click();
                        $("#addPostboxWindow").parent().find('.ui-dialog-titlebar-close').click();
                    });
                } else {
                    // #1012 Pre-payment process
                    if (data.prepayment === true) {
                        var new_postbox_type = $('#addPostboxForm_account_type').val();
                        var location_id = $('#cust_location').val();
                        openEstimateCostDialog('add_more_postbox', location_id, '', new_postbox_type);
                    } else {
                    	$.displayError(data.message);
                    }
                    $(".ui-dialog-buttonpane button:contains('Submit')").attr('disabled', false);
                }
            }
        });
    }
    
    /**
     * open estimate cost for prepayment dialog.
     * @param {type} type
     * @param {type} location_id
     * @param {type} postbox_id
     * @param {type} postbox_type
     * @returns {undefined}
     */
    function openEstimateCostDialog(type, location_id, postbox_id, postbox_type){
        
        var url = '<?php echo base_url() ?>customers/estimate_fee_pre_payment';
        url += "?type=" + type;
        url += "&postbox_type=" + postbox_type;
        url += "&location_id=" + location_id;
        url += "&postbox_id=" + postbox_id;
        
        $.openDialog('#make_prepayment_dialog', {
            height: 475,
            width: 660,
            openUrl: url,
            title: "Confirmation",
            closeButtonLabel: "Close"
        });
    }
    
});

</script>