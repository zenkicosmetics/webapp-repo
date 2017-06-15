jQuery(document).ready(function($){
    $("#control_arrow").bind("click",function(){
		if( $("#content-right-wrapper").css("display") == 'block'){
			$("#content-right-wrapper").hide();
			$("#content-left-wrapper").width(996);
		}else{
			$("#content-right-wrapper").show();
			$("#content-left-wrapper").width(763);
		}
	}); 
});


$(function() {
	$( "#calendar-box" ).datepicker();
});