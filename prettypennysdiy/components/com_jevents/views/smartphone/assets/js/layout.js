jQuery(document).ready(function(){
	jQuery(".jev_dayoutofmonth").each(
	function(idx, el){
		if (jQuery(el).parent().hasClass("slots1")){
			el.style.height = "81px";
		}
		else {
			var psize = jQuery(el).parent().height();
			el.style.height=psize+"px";
		}
	});

});