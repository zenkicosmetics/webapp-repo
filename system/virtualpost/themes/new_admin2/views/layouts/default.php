<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title><?php echo Settings::get(APConstants::SITE_NAME_CODE).' - '.$template['title'];?></title>

<base href="<?php echo base_url(); ?>" />
<link type="image/png" href="<?php echo APContext::getAssetPath()?>images/favicon2.png" rel="icon">

<!-- Mobile viewport optimized -->
<meta name="viewport" content="width=device-width,user-scalable=no">

<!-- metadata needs to load before some stuff -->
	<?php file_partial('metadata'); ?>
</head>

<body id="user-page" style="" class="body <?php echo ci()->router->fetch_class(),' ',ci()->router->fetch_method();?>">

<?php 
    $list_color = APContext::getListColors(true);
?>
<style type="text/css">
    #headerAdminBannerID {
        background-color: #<?php echo $list_color['COLOR_001'] ?>;
    }
    #navAdminSiteId{
        background-color: #<?php echo $list_color['COLOR_002'] ?>;
    }
    #navAdminSiteId a{
        color: #<?php echo $list_color['COLOR_005'] ?>;
    }
</style>
<!-- manually attach allowOverflow method to pane -->
<div class="ui-layout-north">
    <header id="headerAdminBannerID">
    	<?php echo file_partial('header'); ?>
    	<?php echo file_partial('navigation'); ?>
    </header>
</div>

<!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
<div class="ui-layout-center">
    	<div id="content-wrapper" class="bg2">
			<div id="mailbox">
    		    <?php echo $template['body']; ?>
    		</div>
    		<div class="ym-clearfix"></div>	
    	</div>
</div>
<div class="ym-clearfix"></div>	
<div class="ui-layout-south">
	<?php echo file_partial('footer'); ?>
</div>

<script type="text/javascript">
var IMAGE_PATH = '<?php echo APContext::getImagePath()?>';
var DATAGRID_WIDTH = 1030;
var CONTEXT_PATH = '<?php echo APContext::getFullBasePath()?>';
var myLayout;
$(document).ready( function() {
	// Apply common control by jQuery UI
    $.initPage();

    var option = {
    		defaults:{
    	    }
    	    , north: {
    	        resizable: false
    	        , closable: false
    	        , spacing_open:0
    	        , zIndex: 	50
    	     }
    	     , south: {
    	    	 resizable: false
    		     , closable: false
    		     ,spacing_open:0
    		     , size:30
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
    		    		$('.items').css('width', 1130);
    			    	$('#paginationContainer').css('width', 1110);
    		    	}
    	    	}
    	     }
    	};
    	myLayout = $('body').layout(option);
    	myLayout.allowOverflow('north');

    var timeout = <?php echo APUtils::getConfigTimeout()?>;
    // Ajax timeout
    $(document).idleTimeout({ 
        inactivity: timeout * 1000, 
        redirect_url: '<?php echo base_url()?>admin/logout', 
        logout_url: '<?php echo base_url()?>admin/logout' 
    });

    function autoFitScreen(width){
    	// Gets screen width
        var screen_width = $(window).width() - 60;
        if (screen_width > width) {
        	$("#dataGridResult").jqGrid('setGridWidth', screen_width);
        } else {
        	$("#dataGridResult").jqGrid('setGridWidth', width);
        }
    }
    // Custom ajax request to check session timeout
    $(document).ajaxComplete(function( event,request, settings ) {
        if (request.responseText == '{"status":false,"message":"session time out","data":{"code":"999"}}') {
        	document.location = '<?php echo base_url()?>admin/logout';
        }
  	});
});
</script>
<style type="text/css">
    <?php $bg_color = Settings::get(APConstants::SECOND_COLOR_CODE);?>
    a:hover, a:focus {
    	/*background-color: #<?php echo $bg_color?>;*/
    	color:#333333;
    	text-decoration:none;
    }
    ul.menusm li ul li a {
        background: #<?php echo $bg_color?>;
    }
    nav ul li a{
    	padding:13px 30px;
    	background:#<?php echo $bg_color?>;
    	font-size:15px;
    	color:#333333;
    }
    nav ul li a:hover, nav ul li a.act{
    	background: none repeat-y scroll 0 0 #eebe01;
    	text-decoration:none;
    	color:#333333;
    }
    ul.menusm li ul li a:hover, ul.menusm li ul li a.act{
    	background: none repeat-y scroll 0 0 #eebe01;
    	text-decoration:none;
    	color:#333333;
    }
    
</style>
</body>
</html>