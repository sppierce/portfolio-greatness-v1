jevjq(document).ready( function(){
	// adjust for the border width
	var adjust = 1;
	if (jevjq("jevents_body").hasClass("jeventsdark")){
//		adjust = 2;
	}

	jevjq(".jev_dayoutofmonth").each(
	function(idx, el){
		el.style.width=(Math.max(0, parseInt(el.offsetWidth) - adjust)) +"px";
		if (jevjq(el).parent().hasClass("slots1")) {
			jevjq(el).css('min-height', "81px");
		}
		else {
			var psizey = jevjq(el).parent().height();
			jevjq(el).css('min-height', psizey + "px");
		}

		if (jevjq(el).hasClass("jevblocks1")){
			el.style.borderRightWidth="1px";
		}
		else {
			el.style.borderRightWidth="0px";
		}
	});

});