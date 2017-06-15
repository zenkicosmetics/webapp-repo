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
    
    $title = APContext::getTitleSite($template);
?>
<title><?php echo $title;?></title>
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">

<base href="<?php echo base_url(); ?>" />

<!-- Mobile viewport optimized -->
<meta name="viewport" content="width=device-width,user-scalable=no">

<!-- metadata needs to load before some stuff -->
	<?php file_partial('metadata'); ?>

<!-- inline css for design scheme -->
<?php include 'system/virtualpost/themes/assets/css/change_style_enterprise.php';?>

</head>
<body id="user-page">
    <!-- support & feedback -->
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
    	<?php file_partial('header'); ?>
    
    </div>
    
    <?php if ($module != 'office' && $module !== 'banking') {?>
    <!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
    <div class="ui-layout-west">
        <div class="ym-wrapper">
        <div id="content-wrapper">
            <div id="content-left-wrapper">
    	        <?php file_partial('left_panel');?>
            </div>
        </div>
        </div>
    </div>
    <?php } ?>
    <div class="ui-layout-south">
    	<?php file_partial('footer'); ?>
    </div>
    
    
    <div class="ui-layout-center">
        	<div id="content-center-wrapper" class="gb2">
        		    <?php echo $template['body']; ?>
        	</div>
    </div>

<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
var myLayout;
var DATAGRID_WIDTH = 1030;
$(document).ready( function() {
    // Apply common control by jQuery UI
    $.initPage();

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
    		     size:200
    	    	, closable:false
    	    	, resizable: false
    	    	, spacing_open:	0
    	     }
    	     , east: {
    	     }
    	     , center:{
    	    	minSize:980, 
    	    	spacing_open:0
    	     }
    	};
    	myLayout = $('body').layout(option);

    var timeout = <?php echo APUtils::getConfigTimeout()?>;
    // Ajax timeout
    $(document).idleTimeout({ 
        inactivity: timeout * 1000, 
        redirect_url: '<?php echo base_url()?>customer/logout', 
        logout_url: '<?php echo base_url()?>customer/logout' 
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
            && (!empty(AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY)) || !empty(AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY)) )){ ?>
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
        
    // scroll left menu
    $("#content-left-wrapper").slimScroll({
        height:($(window).height() - 125)+'px',
        railVisible: true,
        alwaysVisible: true,
        color: "#<?php echo $list_color['COLOR_010']; ?>"
    });
});
</script>

<script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
    FreshWidget.init("", {"queryString": "&helpdesk_ticket[requester]=<?php echo APContext::getCustomerLoggedIn()->email; ?>&widgetType=popup&formTitle=Support+%26+Feedback&submitThanks=Thank+you+very+much.+We+will+process+your+request+as+quickly+as+possible.", "widgetType": "popup", "buttonType": "text", "buttonText": '<?php language_e('them_acco_view_layo_defa_SuppAndFeed')?>', "buttonColor": "white", "buttonBg": "#FECC34", "alignment": "1", "offset": "300px", "submitThanks": "Thank you very much. We will process your request as quickly as possible.", "formHeight": "500px", "url": "https://clevvermail.freshdesk.com"} );
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