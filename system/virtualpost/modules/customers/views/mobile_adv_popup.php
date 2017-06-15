<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Mobile App</title>
        <link rel="stylesheet" href="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/css/jquery-ui-1.8.20.custom.css" />
        <link rel="stylesheet" href="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/css/Aristo.css" />

	<script src="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/js/jquery-1.7.2.min.js"></script>
        <script src="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/js/jquery.blockUI.js"></script>
        <script src="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/js/jquery-ui-1.8.20.custom.min.js"></script>
        <script src="<?php echo APContext::getFullBasePath(); ?>system/virtualpost/themes/new_user2/js/jquery.common.js"></script>
	<style>
		.close_button {
		    background: #eebe01 none repeat scroll 0 0;
		    border: 1px solid #eebe01;
		    color: #fff;
		    cursor: pointer;
		    font-size: 17px;
		    padding: 5px 15px;
		}
		.slider img {
		  width: 100%;
		  display: none;
		}
		img.showImage {
		  display: inline-block;
		}
		 
		img.hideImage {
		  display: none;
		}
	</style>
</head>

<body style="font-family: arial,helvetica,sans-serif;">

<div style="margin:auto; width:479px;text-align:center" class="slider">
	<a href="https://itunes.apple.com/de/app/clevvermail/id1096570674?mt=8" target="_blank">
		<img id="image0" src="<?php echo APContext::getAssetPath() ?>images/iOS_app.png">
	</a>
	<a href="https://play.google.com/store/apps/details?id=com.clevvermail.mobile" target="_blank">
		<img id="image1" src="<?php echo APContext::getAssetPath() ?>images/Android_app.png">
	</a>
	<br/><br/>
	<input type="checkbox" id="show_ios_popup" /> Do not show this message again
	<br/><br/>
	<button class="close_button" id="closeButton">Close</button>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
var slider = document.getElementsByClassName('slider')[0];
var images = slider.getElementsByTagName('img');
var counter = 1;

function showImage (index) {
  // Set classname on the image-elements (hide them)
  for (var i = 0; i < images.length; i += 1) {
	  $("#image" + i).hide();  
  }
  
  // Add the showImage classname to the img-element you want
  $("#image" + index).show();
}

showImage(counter);
setInterval(function(){ 
	counter++; 
	if (counter > 1) { 
		counter = 0; 
	} 
	showImage(counter);
}, 5000);
	
$('#closeButton').live('click', function() {
    var result = $('#show_ios_popup').is(':checked');
    if (result) {
        // Call ajax request to update
        // localStorage.NotShowIOSApp = '1';
        var submitUrl = '<?php echo APContext::getFullBasePath(); ?>customers/update_mobile_adv_popup';
        $.ajaxExec({
                url: submitUrl,
                data: {'flag' : '1'},
                success: function(data) {
                    parent.$.fancybox.close();
                }
        });
    }else{
        parent.$.fancybox.close();
    }
});
});
</script>
</body>
</html>
