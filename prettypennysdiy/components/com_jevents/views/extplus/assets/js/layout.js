jevjq(document).ready( function(){
	jevjq(".jev_dayoutofmonth").each(
		function(idx, el) {
			if (jevjq(el).parent().hasClass("slots1")) {
				jevjq(el).css('min-height', "81px");
			}
			else {
				var psizey = jevjq(el).parent().height();
				jevjq(el).css('min-height', psizey + "px");
			}
		});

});