<div class="categories-box">
	<div id="tree" style="height: 600px;"></div>
</div>
<script type="text/javascript">
$(document).ready( function() {
	var url_init = '<?php echo base_url() ?>cloud/dropbox_folder_tree';
	
	$("#tree").dynatree({
		autoFocus: false,
  	     initAjax: {
  	         url: url_init,
    	     data: {
             },
			 complete : function(data){
				 if(data.responseText === 'null'){
					 window.location = 'mailbox/request_dropbox';
				 }
			 }
  	     },
       	 onActivate: function(node) {
        	selected_node = node;
         },
         onLazyRead: function(node){
             node.appendAjax({
                url: url_init,
                data: {
                    "key": node.data.key
                }
             });
         },
	});
});
</script>