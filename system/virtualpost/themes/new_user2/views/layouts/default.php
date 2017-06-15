<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php 
    $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
    $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
    $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
    $customer = APContext::getCustomerLoggedIn();
    // TODO: always get customer from db.
    //$customer = APContext::getCustomerByID($customer->customer_id);
    $title = APContext::getTitleSite($template);
?>
<title><?php echo $title;?></title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">

<base href="<?php echo base_url(); ?>" />

<!-- Mobile viewport optimized -->
<meta name="viewport" content="width=960, initial-scale=1, maximum-scale=1"/>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- metadata needs to load before some stuff -->
<?php file_partial('metadata'); ?>

<!-- inline css for design scheme -->
<?php include 'system/virtualpost/themes/assets/css/change_style_enterprise.php';?>


<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
var CONTEXT_PATH = '<?php echo base_url()?>';
var RIGHT_PANEL_WIDTH = 182;
var LEFT_PANEL_WIDTH = 200;
</script>
</head>
<body id="user-page" style="visibility:hidden;min-width: 800px;">
    <?php if($isEnterpriseCustomer && !$isPrimaryCustomer){ ?>
    <div id="freshwidget-button-custom" data-html2canvas-ignore="true" style="display: block; left: 300px;" class="freshwidget-button fd-btn-top">
        <a href="#" class="freshwidget-theme" id="freshwidgetCustomButton" style="color: white; background-color: rgb(254, 204, 52); border-color: white;">Support &amp; Feedback</a>
    </div>
    <div style="display: none" class="hide">
        <div id="freshwidgetCustomWindow" title="Support & feedback" class="input-form dialog-form"> </div>
    </div>
    <?php }?>
    
    <!-- manually attach allowOverflow method to pane -->
    <div class="ui-layout-north">
        <?php 
            $postbox_id = isset($current_postbox) ? $current_postbox : 0;
            $flag = 0;
            if(!empty($customer_users)){
                $customer_user = $customer_users[0];
            }
            
        ?>
        <?php file_partial('header'); ?>
            <div class="container" id="wrapSlideMessage">
                <div id="slides"> 
            <?php if (!empty($customer) && ($customer->activated_flag != '1'))  { $flag++;
                        //&& empty($customer_user->deactivated_type)
                        ?>
                <div class="jbar jbar-top notification_bar" style="cursor: pointer; display: block; width: 470px; height:70px;margin-bot">
                    <span class="jbar-content" style="color: #F91D00;font-weight: bold;">
                    Your account is not activated yet.
                    <br />
                    Please complete the <a id="setup_process"  class="main_link_color" style="font-size: 14px; font-weight: bold;">setup process</a>
                    </span>
                </div>    
            <?php } ?>

            <?php if (!empty($list_pending_envelope_customs)) { $flag++;?>
                <div class="jbar jbar-top notification_bar" style="cursor: pointer; display: block; width: 470px; height:70px;">
                    <span class="jbar-content" style="color: #F91D00;font-weight: bold;font-size: 12px;">
                    You have an item marked for shipping that needs customs declaration.<br/>
                    Please complete the <a id="declare_customs_process"  class="main_link_color"  style="font-size: 12px; font-weight: bold;">customs declaration process</a>
                    </span>
                </div>    
            <?php } ?> 
            
            <?php if (isset($customer) && !CaseUtils::isVerifiedAddress($customer->customer_id)) { $flag++;?>
                <div class="jbar jbar-top notification_bar" style="cursor: pointer; display: block; width: 470px; height:70px;padding-top: 14px;">
                    <span class="jbar-content" style="color: #F91D00;font-weight: bold;font-size: 12px;">
                    One of your addresses needs verification. Please click <a  class="main_link_color" href="<?php echo base_url()?>cases/verification">here</a> to verify online.
                    </span>
                </div>    
            <?php } ?> 
            
            <?php 
            if ( isset($customer) && !CaseUtils::isVerifiedPostboxAddress($postbox_id, $customer->customer_id)) { $flag++;$postbox = $this->postbox_m->getPostboxAndLocation($postbox_id);?>
                <div class="jbar jbar-top notification_bar" style="cursor: pointer; display: block; width: 470px; height:70px;padding-top: 0px;">
                    <span class="jbar-content" style="color: #F91D00;font-weight: bold;font-size: 12px;">
                     Your postbox <?php echo isset($postbox) && (is_object($postbox)) ? $postbox->location_name." - ".$postbox->postbox_name:"addresses";  ?>  needs verification. <br/> Please click <a href="<?php echo base_url()?>cases/verification">here</a> to verify online.
                    </span>
                </div>    
            <?php } ?>

         </div>
    </div>
                
    </div>
    
    <!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
    <div class="ui-layout-west">
        <div id="content-wrapper-west">
            <div id="content-left-wrapper-west">
                <?php if ($isPrimaryCustomer) { 
                    file_partial('left_panel_enterprise_mailbox');
                } else {
                    file_partial('left_panel_mailbox');
                }?>
            </div>
        </div>
            <?php 
            // clevvermail postbox is default product.
            $selection_clevver_product = isset($customer_product_setting['SELECTION_CLEVVER_PRODUCT'])? $customer_product_setting['SELECTION_CLEVVER_PRODUCT']: APConstants::CLEVVERMAIL_PRODUCT;
            $setup_process_status = ($customer->accept_terms_condition_flag !== '1') || (isset($customer) && $customer->activated_flag != '1');
            if($isPrimaryCustomer){
                $selection_clevver_product = 'enterprise';
                // check trong truong hop activated customers
                if(!$setup_process_status){
                    if (isset($customer_product_setting['activate_10_postbox_enterprise_customer']) && $customer_product_setting['activate_10_postbox_enterprise_customer'] == '0'){
                        $setup_process_status = true;
                    }
                }
            }

            if ($setup_process_status) {
            ?>
            <div id="left_notification_bar" class="left_jbar jbar-top" style="cursor: pointer; height: 286px; display: block; bottom: 46px;">
                <span class="jbar-content" style="color: #F91D00;font-weight: bold;position: relative; top: 10px;">
                Setup Status
                </span>
                <ul style="text-align: left;">
                    <?php if (isset($customer_product_setting['shipping_address_completed']) && $customer_product_setting['shipping_address_completed'] == 1) {?>
                    <li class="completed"><a>Shipping Address</a></li>
                    <?php } else {?>
                    <li class="not_completed"><a id="shipping_not_completed" class="main_link_color">Shipping Address</a></li>
                    <?php } ?>
                    
                    <?php if (isset($customer_product_setting['invoicing_address_completed']) && $customer_product_setting['invoicing_address_completed'] == 1) {?>
                    <li class="completed"><a >Invoicing Address</a></li>
                    <?php } else {?>
                    <li class="not_completed"><a id="invoicing_not_completed"  class="main_link_color">Invoicing Address</a></li>
                    <?php }?>
                    
                    <?php if($selection_clevver_product == APConstants::CLEVVERMAIL_PRODUCT || $isEnterpriseCustomer){ ?>
                        <?php if (isset($customer_product_setting['postbox_name_flag']) && $customer_product_setting['postbox_name_flag'] == 1) {?>
                        <li class="completed"><a>Postbox Name</a></li>
                        <?php } else {?>
                        <li class="not_completed"><a id="postnoxname_not_completed"  class="main_link_color">Postbox Name</a></li>
                        <?php }?>

                        <?php if (isset($customer_product_setting['name_comp_address_flag']) && $customer_product_setting['name_comp_address_flag'] == 1) {?>
                        <li class="completed"><a >Name/company in Address</a></li>
                        <?php } else {?>
                        <li class="not_completed"><a id="name_comp_address_not_completed"  class="main_link_color">Name/company in Address</a></li>
                        <?php }?>
                    
                        <?php if (isset($customer_product_setting['city_address_flag']) && $customer_product_setting['city_address_flag'] == 1) {?>
                        <li class="completed"><a >City for Address</a></li>
                        <?php } else {?>
                        <li class="not_completed"><a id="city_address_not_completed"  class="main_link_color">City for Address</a></li>
                        <?php } ?>
                        <?php if ($isPrimaryCustomer){?>
                            <?php if (isset($customer_product_setting['activate_10_postbox_enterprise_customer']) && $customer_product_setting['activate_10_postbox_enterprise_customer'] == '1') {?>
                            <li class="completed"><a >Complete 10 postboxes</a></li>
                            <?php } else {?>
                            <li class="not_completed"><a id="complete_ten_postboxes_enterprise"  class="main_link_color">Complete postboxes</a></li>
                            <?php }?>
                        <?php }?>
                    <?php } else if ($selection_clevver_product == APConstants::CLEVVERPHONE_PRODUCT){?>
                        <?php if (isset($customer_product_setting['activate_add_phone_number']) && $customer_product_setting['activate_add_phone_number'] == 1) {?>
                        <li class="completed"><a >Register phone number</a></li>
                        <?php } else {?>
                        <li class="not_completed"><a id="register_phone_number_product"  class="main_link_color">Register phone number</a></li>
                        <?php }?>
                    <?php }?>
                    
                    <?php if(APContext::isNormalCustomerUser() || APContext::isStandardCustomer()){ ?>
                    <?php if (isset($customer_product_setting['payment_detail_flag']) && $customer_product_setting['payment_detail_flag'] == 1) {?>
                    <li class="completed"><a >Payment details</a></li>
                    <?php } else {?>
                    <li class="not_completed"><a id="payment_detail_not_completed"  class="main_link_color">Payment details</a></li>
                    <?php }?>
                    <?php }?>
                    
                    <?php if (isset($customer_product_setting['email_confirm_flag']) && $customer_product_setting['email_confirm_flag'] == 1) {?>
                    <li class="completed"><a >E-Mail confirmation</a></li>
                    <?php } else {?>
                    <li class="not_completed"><a id="email_confirmation_not_completed"  class="main_link_color">E-Mail confirmation</a></li>
                    <?php }?>
                    <?php if(!$isEnterpriseCustomer 
                            || ($isPrimaryCustomer 
                                    && isset($customer_product_setting['activate_10_postbox_enterprise_customer']) 
                                    && $customer_product_setting['activate_10_postbox_enterprise_customer'] == '1' ) ){ 
                        $is_show_link_openbalance = false;
                        $is_completed_openbalance = true;
                        if($open_balance >= 0.01){
                            $is_show_link_openbalance = true;
                        }
                        if($open_balance > 0.01 || ($total_open_balance > 0.01 && empty($customer->deactivated_type) && $customer->activated_flag != '1' ) ){
                            $is_completed_openbalance = false;
                        }
                    ?>
                        <li class="<?php echo ($is_completed_openbalance)? "completed" :"not_completed"; ?>"><a  class="main_link_color" <?php echo ($is_show_link_openbalance) ? 'id="open_balance_link"' : ""; ?> >Open Balance: 
                        <?php echo $currency->currency_sign.' '.APUtils::convert_currency($total_open_balance, $currency->currency_rate); ?>
                        </a></li>
                    <?php }?>
                    <?php if (isset($customer) && ($customer->accept_terms_condition_flag == '0') ) {?>
                    <li class="<?php echo ($customer->accept_terms_condition_flag == '0') ? "not_completed": "completed"; ?>">
                        <a class="accept_terms_condition main_link_color"> Terms &AMP; Conditions</a>
                    </li>
                    <?php }?>
                </ul>
            </div>
        <?php }?>
    </div>
    
    <div class="ui-layout-south">
        <?php file_partial('footer'); ?>
        <?php if ( ($customer->activated_flag == '1' && CaseUtils::isVerifiedAddress($customer->customer_id)))  {?>
                <div id="collect_shipping_container">
                    <input type="button" id="customerCollectShippingButton" class="input-btn c btn-yellow tipsy_tooltip" value="<?php language_e('them_user_view_layo_defa_CollForw') ?>" 
                        style="left: 12px;bottom: 30px;position: absolute ;z-index: 100;width: 193px;" title="<?php language_e('them_user_view_layo_defa_HereYouCanOrder') ?>">
                </div>
        <?php }?>
    </div>
    
    <div class="ui-layout-east">
        <?php echo file_partial('right_panel'); ?>
    </div>
    
    <div class="ui-layout-center">
            <div id="content-center-wrapper">
                <div id="content-body-wrapper" >
                    <?php echo $template['body']; ?>
                </div>
                <div class="ym-clearfix"></div>    
            </div>
    </div>
<div id="main_loader"></div>
<input type="hidden" value="<?php echo  isset($hide_panes) ? $hide_panes : "";?>" id="hidePanesLaylout"  />
<input type="hidden" value="0" id="openScanOrEnvelopeFlag"  />
<script type="text/javascript">

$(function() {
  $("#wrapSlideMessage").show();    
  $('#slides').slidesjs({
    width: 460,
    height: 64,
    navigation: false
  });
});
<?php if($flag > 1) { ?>
$(function() {
    <?php //if($flag > 2) { ?>
    //$("#wrapSlideMessage").css({"margin-top":"-82px"});
    <?php //} ?>
    $("ul.slidesjs-pagination a").show();
});
<?php } else if($flag == 1) { ?>
$(function() {
    $("ul.slidesjs-pagination a").hide();
    $(".notification_bar").css({"left":"0px"});
});
<?php } else if($flag == 0) { ?>
$(function() {
    $("#wrapSlideMessage").hide();
});
<?php } ?>


$(window).load(function() {
    $("#main_loader").hide();
    $("#user-page").css('visibility', '');
});
</script>
<script type="text/javascript">
var myLayout;
var ctrlDown = false;
var shiftDown = false;
var myLayout;
$(document).ready( function() {
    var option = {
        defaults:{
        }
        , north: {
            resizable: false
            , closable: false
            ,size:91
            , spacing_open:0
         }
         , south: {
             resizable: false
             , closable: false
             ,spacing_open:0
             , size:30
         }
         , west: {
             size:220
            , closable:false
            , resizable: false
            , spacing_open:    0
         }
         , east: {
            size:274
           , resizable:false
           //, fxName:"none"
         }
         , center:{
            minSize:980,
            // enable showOverflow on west-pane so popups will overlap north pane
            center__showOverflowOnHover: true,
            spacing_open:0
            , onresize_end:function (){
                var width = $('.ui-layout-center ').width();
                $('#paginationContainer').css('margin-left', -3);
                if (width > 1140){
                    $('.items').css('width', width-10);
                    $('#paginationContainer').css('width', width-31);
                }else{
                     //$('.items').css('min-width', 900);
                     //$('#paginationContainer').css('min-width', 900);
                     // fixbug: #266
                    if(myLayout.state.east.isClosed){
                        $("#paginationContainer").css('min-width', $(window).width() - 230);
                          $("#paginationContainer").css('width', $(window).width() - 230);
                          
                        $('.items').css('min-width', $(window).width()-230);
                          $('.items').css('width', ($(window).width() -230));
                    }else{
                        $("#paginationContainer").css('min-width', $(window).width() - 510);
                          $("#paginationContainer").css('width', $(window).width() - 510);
                          
                        $('.items').css('min-width', $(window).width() - 510);
                          $('.items').css('width', ($(window).width() - 510));
                    }
                }
            }
         }
    };
    myLayout = $('#user-page').layout(option);
    
    // Apply common control by jQuery UI
    $.initPage();

    $(document).bind('contextmenu', function(e) {
        return false;
    });

    var timeout = <?php echo APUtils::getConfigTimeout()?>;
    // Ajax timeout
    $(document).idleTimeout({ 
        inactivity: timeout * 1000, 
        redirect_url: '<?php echo base_url()?>customer/logout', 
        logout_url: '<?php echo base_url()?>customer/logout' 
    });
    
    // Rezise event
    $('.ui-layout-center').resize(function() {
        changeSize();
    });

    var ctrlKey = 17, shifKey = 16, cKey = 67;
    $(document).keydown(function(e) {
        if (e.keyCode == ctrlKey) {
            ctrlDown = true;
        } else if (e.keyCode == shifKey) {
            shiftDown = true;
        }
    }).keyup(function(e) {
        if (e.keyCode == ctrlKey) {
            ctrlDown = false;
        } else if (e.keyCode == shifKey) {
            shiftDown = false;
        }
    });
    
    /**
    * Change window size
    */
    function changeSize() {
        $('#content-center-wrapper').css('width', $('.ui-layout-center').width());
        $('#content-body-wrapper').css('width', $('.ui-layout-center').width());
        if ($('#content-center-wrapper').hasVerticalScrollbar()) {
          var new_width = $('#mainMailboxTable').width() - 5;
          $('#mainMailboxTable').css('width', new_width);
          console.log('Change width of table mainMailboxTable: ' + new_width);
        }

        $('#paginationContainer').css('width', $('#mainMailboxTable').width()- 21);
        $('#paginationContainer').css('border-top', 0);
        $('#paginationContainer').css('margin-left', -2);

        // set height of PDF/scan
        var windowHeight = $(window).height();

        if(windowHeight > 650){
            $('#mailbox_document_image').css('height', windowHeight - 430);
        }
        if(windowHeight > $('#mainMailboxTable').height()-200){
            //$('#paginationContainer').css('margin-top', windowHeight -$('#mainMailboxTable').height()-210);
        }
        // console.log($('#mainMailboxTable').height());
        if($('#mainMailboxTable').height() == 44){
            $('#paginationContainer').css('display', "none");
        }
    }
    changeSize();

    $( window ).resize(function() {
        changeSize();
    });

    // Custom ajax request to check session timeout
    $(document).ajaxComplete(function( event,request, settings ) {
        if (request.responseText == '{"status":false,"message":"session time out","data":{"code":"999"}}') {
            document.location = '<?php echo base_url()?>customers/logout';
        }
    });

    // Send ajax request
    $.ajaxExec({
        url: '<?php echo base_url()?>customers/check_session',
        success: function(data) {
            if (!data.session_exist) {
                document.location = '<?php echo base_url()?>customers/logout';
            }
        }
    });

    // 20151114 DuNT Start added:  fixbug #673
    // toggle east panes :the first login
    if($("#hide_panes").val() == ""){
        if($(window).width() > 1600){
              // open east pane.
              myLayout.open("east");
        }else{
              // close east pane.
          //No animation when start. fix bug 673
          option.east.fxName = "none";    
          myLayout = $('#user-page').layout(option);
            myLayout.close("east");
        }
    }else{
        if($("#hide_panes").val() == '0'){
            // close east pane.
            option.east.fxName = "none";    
            myLayout = $('#user-page').layout(option);
              myLayout.close("east");
        }else{
            // Open east pane.
              myLayout.open("east");
        }
    }
    option.east.fxName = "slide";

    // handle event for east layout click open/close.
    $(".ui-layout-toggler").bind("click", function(){
        if(myLayout.state.east.isClosed){
            $("#hidePanesLaylout").val("0");
            $("#openScanOrEnvelopeFlag").val("1");

            // Send ajax request
            $.ajaxExec({
                url: '<?php echo base_url()?>mailbox/update_hide_panes',
                data: {hide: "0"},
                success: function(data) {
                      // do nothing
                }
            });
        }else{
            $("#hidePanesLaylout").val("1");
            $("#openScanOrEnvelopeFlag").val("1");

            // Send ajax request
            $.ajaxExec({
                url: '<?php echo base_url()?>mailbox/update_hide_panes',
                data: {hide: "1"},
                success: function(data) {
                      // do nothing
                }
            });
        }
    });
    // 20151114 DuNT End added:  fixbug #673
    
    // support and feedback custom
    $("#freshwidget-button-custom").hide();
    <?php 
    if($isEnterpriseCustomer && !$isPrimaryCustomer 
            && (!empty(AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY)) 
                    || !empty(AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY)) )){ ?>
    $("#freshwidget-button").hide();
    $("#freshwidget-button-custom").show();
    $("#freshwidgetCustomButton").live('click',function(e){
        e.preventDefault();
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#freshwidgetCustomWindow').openDialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            open: function () {
                $(this).load("<?php echo base_url() ?>account/setting/get_support_feedback", function () {
                });
            },
        });
        $('#freshwidgetCustomWindow').dialog('option', 'position', 'center');
        $('#freshwidgetCustomWindow').dialog('open');
        return false;
    });
    $("#freshWidgetTechniqueSupportUserButton").live('click', function(){
        $('#freshwidgetCustomWindow').dialog("destroy");
        $("#FreshWidget").show();
        $("#FreshWidget").css('display', 'block');
        return false;
    });
    <?php }else{?>
    $("#freshwidget-button-custom").remove();
    <?php }?>
        
    var termAndConditionContent = $(".accept_terms_condition").html();
    if(termAndConditionContent == null || termAndConditionContent == undefined){
        $("#left_notification_bar").css('height', '255px');
    }
});
</script>
<script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
    FreshWidget.init("", {"queryString": "&helpdesk_ticket[requester]=<?php echo isset($customer_user) ? $customer_user->email : ""; ?>&widgetType=popup&formTitle=Support+%26+Feedback&submitThanks=Thank+you+very+much.+We+will+process+your+request+as+quickly+as+possible.", "widgetType": "popup", "buttonType": "text", "buttonText": '<?php language_e('them_user_view_layo_defa_SuppAndFeed')?>', "buttonColor": "white", "buttonBg": "#FECC34", "alignment": "1", "offset": "300px", "submitThanks": "Thank you very much. We will process your request as quickly as possible.", "formHeight": "500px", "url": "https://clevvermail.freshdesk.com"} );
</script>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PZDH47"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PZDH47');</script>
<!-- End Google Tag Manager -->
</body>
</html>