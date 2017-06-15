jQuery(function($) {
	// Check all checkboxes in container table or grid
	$(".check-all").live('click', function () {
		var check_all		= $(this),
			all_checkbox	= $(this).is('.grid-check-all')
				? $(this).parents(".list-items").find(".grid input[type='checkbox']")
				: $(this).parents("table").find("tbody input[type='checkbox']");

		all_checkbox.each(function () {
			if (check_all.is(":checked") && ! $(this).is(':checked'))
			{
				$(this).click();
			}
			else if ( ! check_all.is(":checked") && $(this).is(':checked'))
			{
				$(this).click();
			}
		});

		// Check all?
		$(".table_action_buttons .btn").prop('disabled', false);
	});
	
	// Add the close link to all alert boxes
	$('.alert').livequery(function(){
		$(this).prepend('<a href="#" class="close">x</a>');
	});

	// Close the notifications when the close link is clicked
	$('a.close').live('click', function(e){
		e.preventDefault();
		$(this).fadeTo(200, 0); // This is a hack so that the close link fades out in IE
		$(this).parent().fadeTo(200, 0);
		$(this).parent().slideUp(400, function(){
			$(window).trigger('notification-closed');
			$(this).remove();
		});
	});
	
	// Fade in the notifications
	$('.alert').livequery(function(){
		$(this).fadeIn('slow', function(){
			$(window).trigger('notification-complete');
		});
	});
	
	// fix footer to bottom
	function fixHeight(){
		var left_panel_height = $('div.body-content-wrap aside').height();
		var footer_height = $('div.body-content-wrap div#body-c footer').height();
		var content_height = $('div.body-content-wrap div#body-c div.body-lc').height();
		var max = (left_panel_height > content_height + footer_height)?left_panel_height:content_height + footer_height;
	    if(left_panel_height < max){
	    	 // $('div.body-content-wrap aside').css('min-height', max);
	    }
	    if((content_height + footer_height) < max){
	    	$('div.body-content-wrap div#body-c div.body-lc').css('min-height', max);
	    }
    }
	
});