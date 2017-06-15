<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php 
    $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
    $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
    
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

<!-- common css --->
<link rel="stylesheet" href="<?php echo APContext::getAssetPath() ?>/system/virtualpost/themes/assets/css/Aristo.css" />

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
    <div class="ui-layout-north" id="ui-layout-north-panel">
    	<?php file_partial('header'); ?>
        
        <div class="container" id="wrapSlideMessage">
            <div id="slides"> 
                <?php if ( isset($customer_user) && !CaseUtils::isVerifiedPostboxAddress($postbox_id, $customer_user->customer_id)) { $flag++;$postbox = $this->postbox_m->getPostboxAndLocation($postbox_id);?>
                    <div id="notification_bar" class="jbar jbar-top" style="cursor: pointer; display: block; width: 470px; height:70px;padding-top: 0px;">
                        <span class="jbar-content" style="color: #F91D00;font-weight: bold;font-size: 12px;">
                         Your postbox <?php echo isset($postbox) && (is_object($postbox)) ? $postbox->location_name." - ".$postbox->postbox_name:"addresses";  ?>  needs verification. <br/> Please click <a href="<?php echo base_url()?>cases/verification">here</a> to verify online.
                        </span>
                    </div>    
                <?php } ?>

             </div>
        </div>

        <div id="phoneFragmentContaincer" style="background-image: url('system/virtualpost/themes/phone/images/background_logo_3000.png')">
            <span id='phoneFrameCloseIcon' style="cursor: pointer">x</span>
            <img src ="system/virtualpost/themes/phone/images/phone_icon.png" alt="phone " style="float:left; height: 90%; margin-left:15px; margin-top: 14px;"/>
            <h2 style="font-size: 22px; font-weight: bold; left: 50px;position: relative;">Get a new international phone number</h2>
            <ul>
                <li>International forwarding</li>
                <li>Unlimited voicemails</li>
                <li>Competitive rates<span><button id="addPhoneNumberLinkButton" type="button" class="input-btn btn-yellow">Add phone number now</button></span></li>
            </ul>
        </div>
    </div>
    
    <div class="ui-layout-west">

        <div id="content-wrapper">
            <div id="content-left-wrapper">
    	        <?php file_partial('left_panel');?>
            </div>
        </div>
    </div>
    
    <div class="ui-layout-south">
    	<?php file_partial('footer'); ?>
    	<?php if (isset($customer) && $customer->activated_flag == '1' && CaseUtils::isVerifiedAddress($customer->customer_id)) {?>
            <div id="collect_shipping_container">
                    <input type="button" id="customerCollectShippingButton" class="input-btn c tipsy_tooltip" value="Collect Forwarding" 
                            style="left: 12px;bottom: 30px;position: absolute ;z-index: 100;width: 193px;" 
                            title="here you can order the immediate shipment of all items that are marked for collect forwarding">
            </div>
        <?php }?>
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
<input type="hidden" value="0" id="openScanOrEnvelopeFlag"  />
<?php $flag = 1;?>
<script type="text/javascript">
$(function() {
  $("#wrapSlideMessage").show();
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
    $("#notification_bar").css({"left":"0px"});
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
//var myLayout;

$(document).ready( function() {
    var length_north = 280;
    if(sessionStorage.HidePhoneFrame == '1'){
        $('#phoneFragmentContaincer').hide();
        length_north = 89;
    }
    
    var option = {
            defaults:{
        }
        , north: {
            resizable: false
            , closable: false
            ,size:length_north
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
            , spacing_open:	0
         }
         , east: {
            size:274
           , resizable:false
               //, fxName:"none"
         }
         , center:{
            minSize:980,
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
                    } else {
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
    
    // Remove banner top
    $("#phoneFrameCloseIcon").live('click', function(){
        $('#phoneFragmentContaincer').hide();

        // Change the size of north panel
        $('#ui-layout-north-panel').css('height', 89);
        // Call ajax request to update
        sessionStorage.HidePhoneFrame = '1';
        $(window).resize();
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
        //console.log($('.ui-layout-center').width());
        $('#content-center-wrapper').css('width', $('.ui-layout-center').width() - 30);
        $('#content-body-wrapper').css('width', $('.ui-layout-center').width() - 30);
        if ($('#content-center-wrapper').hasVerticalScrollbar()) {
            $new_width = $('#mainMailboxTable').width() - 5;
            $('#mainMailboxTable').css('width', new_width);
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
    
    // support and feedback custom
    $("#freshwidget-button-custom").hide();
    <?php 
    if($isEnterpriseCustomer && !$isPrimaryCustomer 
            && (!empty(AccountSetting::get($isPrimaryCustomer, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY)) || !empty(AccountSetting::get($isPrimaryCustomer, APConstants::CUSTOMER_SUPPORT_PHONE_KEY)) )){ ?>
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
        
    $("#addPhoneNumberLinkButton").click(function(){
        location.href = "<?php echo base_url()?>account/number";
    });
 
});
</script>
<script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
    FreshWidget.init("", {"queryString": "&helpdesk_ticket[requester]=<?php echo isset($customer) ? $customer->email : ""; ?>&widgetType=popup&formTitle=Support+%26+Feedback&submitThanks=Thank+you+very+much.+We+will+process+your+request+as+quickly+as+possible.", "widgetType": "popup", "buttonType": "text", "buttonText": '<?php language_e('them_phon_view_layo_defa_SuppAndFeed')?>', "buttonColor": "white", "buttonBg": "#FECC34", "alignment": "1", "offset": "300px", "submitThanks": "Thank you very much. We will process your request as quickly as possible.", "formHeight": "500px", "url": "https://clevvermail.freshdesk.com"} );
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