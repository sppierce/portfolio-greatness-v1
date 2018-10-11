var visible = false;
var timer = null;

function Timer(callback, delay) {
	var timerId, start, remaining = delay;

	this.pause = function() {
		window.clearTimeout(timerId);
		remaining -= new Date() - start;
	};

	this.start = this.resume = function() {
		start = new Date();
		timerId = window.setTimeout(callback, remaining);
	};

	this.cancel = function() {
		window.clearTimeout(timerId);
	};

	this.start();
}

(function($){
	$( document ).ready(function() {
		el = $('#cmessages-container');
	
		el.addClass(cmessages_position);
	
		//fix z-index for nice shadow
		elMsgs = $('div#cmessages-container div.message');
		if(elMsgs.length > 1){
			elMsgs.each(function(i){
				$(this).css('z-index', elMsgs.length+100 - i);
			});
		}
		
		if(cmessages_dock){
			el.addClass('dock');
		} 
	
		el.mouseenter(function(e){
			if(cmessages_autohide){
				timer.pause();
			}
		});
		
		el.mouseleave(function(e){
			if(cmessages_autohide){
				timer.resume();
			}
		});

		el.find('.close a').click(function(e){
			e.preventDefault();
			toggleMessage();
			if(cmessages_autohide){
				timer.cancel();
			}
		});
		
		
		if(cmessages_position == 'center' || cmessages_position == 'specify'){
			el.css('position', 'absolute');
			el.css('z-index', 1000);
			el.addClass('popup');
		
			if(typeof cmessages_width != 'undefined') el.css('width', cmessages_width + (cmessages_width.indexOf('%') == -1?'px':''));
			if(typeof cmessages_height != 'undefined') el.css('height', cmessages_height + (cmessages_height.indexOf('%') == -1?'px':''));
		
			if(cmessages_position == 'specify'){
				if(typeof cmessages_top != 'undefined') el.css('top', cmessages_top + (cmessages_top.indexOf('%') == -1?'px':''));
				if(typeof cmessages_right != 'undefined') el.css('right', cmessages_right + (cmessages_right.indexOf('%') == -1?'px':''));
				if(typeof cmessages_bottom != 'undefined') el.css('bottom', cmessages_bottom + (cmessages_bottom.indexOf('%') == -1?'px':''));
				if(typeof cmessages_left != 'undefined') el.css('left', cmessages_left + (cmessages_left.indexOf('%') == -1?'px':''));
			}else{
				el.css('top', '50%');
				el.css('left', '50%');
			
				lMar = el.outerWidth() / 2;
				lPos = el.offset().left;
				if((lPos - lMar) < 0) lMar = 0;
				el.css('margin-left', '-'+lMar+'px');
			
				tMar = el.outerHeight() / 2;
				tPos = el.offset().top;

				if((tPos - tMar) < 0) tMar = 0;
				el.css('margin-top', '-'+tMar+'px');
			}
		}
	
		if(cmessages_autohide){
			timer = new Timer('toggleMessage()', cmessages_autohide * 1000);
		}
		
		el.toggle();
		toggleMessage();
	});

}(jQuery));

function toggleMessage(){
	el = jQuery('#cmessages-container');
	if(cmessages_fade){
		if(el.hasClass('popup')){
			el.fadeToggle();
		}else{
			el.slideToggle();
		}
	}else{
		el.toggle();
	}
	

}
