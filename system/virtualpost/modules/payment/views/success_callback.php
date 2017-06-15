<p>Your credit card has been added successfully.</p>
<p>Please close this popup to continue your registration process.</p>
<script>
setTimeout(function(){ 
	parent.$.fancybox.close();
	parent.window.location = '<?php echo APContext::getFullBasePath()?>mailbox?first_regist=1';
},1000);
</script>