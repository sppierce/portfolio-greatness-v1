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

window.addEvent('domready', function(){
	el = $('cmessages-container');
	
	el.addClass(cmessages_position);
	
	//fix z-index for nice shadow
	if($$('div#cmessages-container div.message').length > 1){
		$$('div#cmessages-container div.message').each(function(e, i, a){
			e.style.zIndex = a.length+100 - i;
		});
	}
	
	if(cmessages_dock){
		//if(cmessages_position == 'top' || cmessages_position == 'bottom'){
			el.addClass('dock');
		//}
	}
	
	el.addEvent('mouseenter', function(e){
		if(cmessages_autohide){
			timer.pause();
		}
	});
	
	el.addEvent('mouseleave', function(e){
		if(cmessages_autohide){
			timer.resume();
		}
	});

	el.getChildren('.close a')[0].addEvent('click', function(e){
		e.preventDefault();
		toggleMessage();
		if(cmessages_autohide){
			timer.cancel();
		}
	});
			
	if(cmessages_position == 'center' || cmessages_position == 'specify'){
		el.style.position = 'absolute';
		el.style.zIndex = 1000;
		el.addClass('popup');
		
		if(typeof cmessages_width != 'undefined') el.style.width = cmessages_width + (cmessages_width.indexOf('%') == -1?'px':'');
		if(typeof cmessages_height != 'undefined') el.style.height = cmessages_height + (cmessages_height.indexOf('%') == -1?'px':'');
		
		if(cmessages_position == 'specify'){
			if(typeof cmessages_top != 'undefined') el.style.top = cmessages_top + (cmessages_top.indexOf('%') == -1?'px':'');
			if(typeof cmessages_right != 'undefined') el.style.right = cmessages_right + (cmessages_right.indexOf('%') == -1?'px':'');
			if(typeof cmessages_bottom != 'undefined') el.style.bottom = cmessages_bottom + (cmessages_bottom.indexOf('%') == -1?'px':'');
			if(typeof cmessages_left != 'undefined') el.style.left = cmessages_left + (cmessages_left.indexOf('%') == -1?'px':'');
		}else{
			el.style.top = '50%';
			el.style.left = '50%';
			
			lMar = el.getSize().x/2;
			lPos = el.getCoordinates().left;
			if((lPos - lMar) < 0) lMar = 0;
			el.style.marginLeft = '-'+lMar+'px';
			
			tMar = el.getSize().y/2;
			tPos = el.getCoordinates().top;
			if((tPos - tMar) < 0) tMar = 0;
			el.style.marginTop = '-'+tMar+'px';
		}
	}else{
		
	}
	
	if(cmessages_autohide){
		timer = new Timer('toggleMessage()', cmessages_autohide * 1000);
	}
	
	toggleMessage();
});

function toggleMessage(){
	el = $('cmessages-container');
	if(visible){
		if(cmessages_fade){
			if(el.hasClass('popup')){
				el.fade('out').get('tween').chain(function(){el.style.display='none'});
			}else{
				el.slide('out').get('slide').chain(function(){el.style.display='none'});
			}
		}else{
			el.style.display = 'none';
		}
		visible = false;
	}else{
		if(cmessages_fade){
			if(el.hasClass('popup')){
				el.fade('hide').fade('in');
			}else{
				el.slide('hide');
				el.getParent('div').style.zIndex = '100';
				el.slide('in');
			}
		}else{
			el.style.display = '';
		}
		visible = true;
	}
}
