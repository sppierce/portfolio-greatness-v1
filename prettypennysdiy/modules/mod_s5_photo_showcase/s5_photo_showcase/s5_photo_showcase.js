if(! Number.prototype.times) Number.prototype.times=function (b,c){for(var a=0;a<this;a++){b.call(c,a,this)}};
Array.prototype.from=function (item){
	if (item == null) return [];
	return (typeof(item) == 'array') ? item :   [item];
};
$A=function (i){return Array.from(i).slice();};
$clear=function (i){clearTimeout(i);clearInterval(i);return null;};
if(Function.prototype.passx==undefined){
	Function.prototype.passx=function (args, bind){
		var self = this;
		if (args != null) args = Array.prototype.from(args);
		return function(){
			return self.apply(bind, args || arguments);
		};
	};
}
if(undefined == Function.prototype.bind){
	Function.prototype.bind=function(that){
		var self = this,
			args = arguments.length > 1 ? Array.slice(arguments, 1) : null,
			F = function(){};

		var bound = function(){
			var context = that, length = arguments.length;
			if (this instanceof bound){
				F.prototype = self.prototype;
				context = new F;
			}
			var result = (!args && !length)
				? self.call(context)
				: self.apply(context, args && length ? args.concat(Array.slice(arguments)) : args || arguments);
			return context == that ? result : context;
		};
		return bound;
	}
};
Function.prototype.periodical=function (c,b,a){return setInterval(this.passx((a==null?[]:a),b),c);};
$defined=function (i){return(i!=null);};
Function.prototype.chain=function(f){f();return window.chainx;}
window.chainx=function(){};
(function($){
window.s5_photoshowcase_iCarousel = function(_1, _2){
    this.options= {
        animation: {
            type: "fadeNscroll",
            direction: "left",
            amount: 1,
            transition: 'easeOutCirc',
            duration: 500,
            rotate: {
                type: "manual",
                interval: 5000,
                onMouseOver: "stop"
            }
        },
        item: {
            klass: "item",
            size: document.getElementById("s5_photo_showcase_height_width").offsetWidth
        },
        idPrevious: "previous",
        idNext: "next",
        idToggle: "toggle",
        onClickPrevious: function(){},
       onClickNext:function(){},
       onPrevious: function(){},
        onNext: function(){},
        onGoTo: function(){},
        display_time:5
    };
    this.initialize(_1, _2);
    
}
    s5_photoshowcase_iCarousel.prototype.initialize= function (_1, _2) {
		var oldanim=this.options.animation;
		var olditem=this.options.item;
		$.extend(oldanim,_2.animation);
		$.extend(olditem,_2.item);
		$(this.options).extend(_2);
		this.options.animation=oldanim;
		this.options.item=olditem;
		//console.log(this.options);
        this.container = $(_1);
        this.aItems = $("." + this.options.item.klass);
        this.isMouseOver = false;
        if (this.options.idPrevious != "undefined" && $(this.options.idPrevious)) {
            $(this.options.idPrevious).bind("click", (function (_3) {
                _3.preventDefault();
                this._previous();
                $(this).trigger("onClickPrevious", this, 20)
            }).bind(this))
        }
        if (this.options.idNext != "undefined" && $(this.options.idNext)) {
            $(this.options.idNext).bind("click", (function (_4) {
                _4.preventDefault();
                this._next();
                $(this).trigger("onClickNext", this, 20)
            }).bind(this))
        }
        if (this.options.idToggle != "undefined" && $(this.options.idToggle)) {
            $(this.options.idToggle).bind("click", (function (_5) {
                _5.preventDefault();
                this._toggle()
            }).bind(this))
        }
        var _6 = this.options.animation;
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            $(this.aItems).each((function (i,_7) {
                this.aItems[i].fx = function(obj){$(_7).animate(obj, {
                    'duration': _6.duration,
                   'transition': _6.transition,
                    'queue':false
                });return window.chainx;};
                $(_7).css("opacity", 0);
                $(_7).bind(
                    "mouseenter",(function () {
                        this.isMouseOver = true;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = $clear(this.timer)
                        }
                    }).bind(this));
                    $(_7).bind( "mouseleave",(function () {
                        this.isMouseOver = false;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this)
                        }
                    }).bind(this));
            }).bind(this));
            this.height = parseInt(this.container.height());
             (2).times((function () {
                $(this.aItems).each((function (i,_8) {
                    $(_8).clone().removeAttr('id').appendTo($(this.container))
                }).bind(this))
            }).bind(this));
             this.aItems = $("." + this.options.item.klass);
            this.atScreen = 0;
            this._animate(this.atScreen);
            break;
        default: 
            (2).times((function () {
                $(this.aItems).each((function (i,_8) {
                    $(_8).clone().removeAttr('id').appendTo($(this.container))
                }).bind(this))
            }).bind(this));
            this.aItems = $("." + this.options.item.klass);
            //console.log(this.aItems = $("." + this.options.item.klass));
          
            $(this.aItems).each((function (i,_9) {
                $(_9).bind(
                    "mouseenter", (function () {
                        this.isMouseOver = true;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = $clear(this.timer)
                        }
                    }).bind(this));
                  $(_9).bind(  "mouseleave",(function () {
                        this.isMouseOver = false;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this)
                        }
                    }).bind(this));
            }).bind(this));
            this.fx = function(obj){this.container.animate(obj,{'duration': _6.duration,'easing':_6.transition,'queue':false,'complete':function(){}});return window.chainx;};
            this.atScreen = this.aItems.length / 3;
            break
        }
        if (this.options.animation.rotate.type == "auto") {
            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this);
            this.currentActiveIdx  = 1;
            $('#s5_photo_showcase_button_frame > div').each(function(i,d){
					$(d).mouseenter(function(e){
						this.currentActiveIdx = i + 1;
						clearInterval(this.timer);
				}.bind(this)).mouseleave(function(e){
					this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this);
				}.bind(this));
			}.bind(this));
            
        }

        /*
         * Add active/inactive classes */
         var tmp = 0, j = 0, that=this;
         $('.s5_photo_showcase_item', this.container).each(function(i,d){
			tmp +=	d.offsetWidth;
			if(tmp > Math.abs(that.container[0].offsetLeft)) {j=i;return false;}
		});

		$('.s5_photo_showcase_item, .s5_photo_showcase_active', this.container).removeClass('s5_photo_showcase_active').addClass('s5_photo_showcase_inactive');
		$('.s5_photo_showcase_item', this.container).eq(j + 1).removeClass('s5_photo_showcase_inactive').addClass('s5_photo_showcase_active');
	}; 
    
     s5_photoshowcase_iCarousel.prototype.goTo= function (n) {
		 
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _b = this.atScreen;
            this.atScreen = Math.abs(n % (this.aItems.length / 3));
            //this.atScreen += this.aItems.length / 3;
            this._animate(this.atScreen, _b);
            break;
        default:
            this.atScreen = Math.abs(n % (this.aItems.length / 3));
            this.atScreen += this.aItems.length / 3;
            this._animate(this.atScreen);
            break
        }
	this.timer = $clear(this.timer);
	this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this) ;

        $(this).trigger("onGoTo", this, 20)
    },
    s5_photoshowcase_iCarousel.prototype._previous= function () {
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _c = this.atScreen;
            this.atScreen -= this.options.animation.amount;
            if (this.atScreen < 0) {
                this.atScreen = (this.aItems.length - 1)
            }
            this._animate(this.atScreen, 'prev');
            break;
        default:
            this.atScreen -= this.options.animation.amount;
            if (this.atScreen < this.aItems.length / 3) {
                this.container.css(this.options.animation.direction, -this.options.item.size * this.aItems.length * 2 / 3);
                this.atScreen = this.aItems.length * 2 / 3 - this.options.animation.amount
            }
            this._animate(this.atScreen,'prev');
            break
        }
	//this.timer = $clear(this.timer);
	//this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this) ;
        $(this).trigger("onPrevious", this, 20)
    },
    s5_photoshowcase_iCarousel.prototype._next=function () {
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _d = this.atScreen;
            this.atScreen += this.options.animation.amount;
            if (this.atScreen >= this.aItems.length) {
                this.atScreen = 0
            }
            this._animate(this.atScreen, _d);
            break;
        default:
            this.atScreen += this.options.animation.amount;
            if (this.atScreen > this.aItems.length * 2 / 3) {
                this.container.css(this.options.animation.direction, -this.options.item.size * this.aItems.length / 3);
                this.atScreen = this.aItems.length / 3 + this.options.animation.amount
            }
            this._animate(this.atScreen);
            break
        }
	//this.timer = $clear(this.timer);
	//this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this) ;
        $(this).trigger("onNext", this, 20)
    },
   s5_photoshowcase_iCarousel.prototype. _toggle= function () {
        (parseInt(this.container.height()) == 0) ? this.container.animate({'height':this.height}, {duration: 1000, easing: 'easeOutCirc'})
	: this.container.animate({'height':0},{duration: 1000,easing: 'easeOutCirc'});
    },
    s5_photoshowcase_iCarousel.prototype._autoRotate=function () {
        if (this.options.animation.rotate.onMouseOver == "stop" && !this.isMouseOver) {
			// make current menu item active
			this.currentActiveIdx ++;
			this.currentActiveIdx = this.currentActiveIdx >  $('#s5_photo_showcase_button_frame > div').size()  ?  1 : this.currentActiveIdx;
			// end 
            this._next()
        }
    },
    s5_photoshowcase_iCarousel.prototype._animate= function (a, b) {
		if(console) console.log(a,b);
            var _10 = this;
                _10.fx({
                    "left": -a * document.getElementById("s5_photo_showcase_height_width").offsetWidth + (document.getElementById("s5_photo_showcase_height_width").offsetLeft) - document.getElementById("s5_photo_showcase_outer").offsetLeft
                });
	$('.s5_photo_showcase_item', this.container).removeClass('s5_photo_showcase_active').addClass('s5_photo_showcase_inactive');
	$('.s5_photo_showcase_item', this.container).eq(a ).removeClass('s5_photo_showcase_inactive').addClass('s5_photo_showcase_active');
    }

})(jQuery);

function s5_photo_showcase_item_sizing() {
var s5_photo_showcase_resize_items = document.getElementById("s5_photo_showcase_outer").getElementsByTagName("LI");
	for (var s5_photo_showcase_resize_items_y=0; s5_photo_showcase_resize_items_y<s5_photo_showcase_resize_items.length; s5_photo_showcase_resize_items_y++) {
	var s5_photo_showcase_resize_items_classname = s5_photo_showcase_resize_items[s5_photo_showcase_resize_items_y].className;
	if (s5_photo_showcase_resize_items_classname.indexOf("s5_photo_showcase_item") >= 0) {
		s5_photo_showcase_resize_items[s5_photo_showcase_resize_items_y].style.width = document.getElementById("s5_photo_showcase_height_width_img").offsetWidth + "px";
		s5_photo_showcase_resize_items[s5_photo_showcase_resize_items_y].style.height = document.getElementById("s5_photo_showcase_height_width_img").offsetHeight + "px";
	}
	document.getElementById("s5_photo_showcase_inner").style.width = (s5_photo_showcase_resize_items_y + 1) * document.getElementById("s5_photo_showcase_height_width_img").offsetWidth + "px";
}
}

var s5_photo_showcase_initial_load = "yes";
function s5_photo_showcase_left_positioning() {
var s5_photo_showcase_count_items = document.getElementById("s5_photo_showcase_outer").getElementsByTagName("LI");
	if (s5_photo_showcase_initial_load == "yes") {
		document.getElementById("s5_photo_showcase_button_content").style.left = ((document.getElementById("s5_photo_showcase_height_width_img").offsetWidth * s5_photo_showcase_count_items.length) * -1) + (document.getElementById("s5_photo_showcase_height_width").offsetLeft - document.getElementById("s5_photo_showcase_outer").offsetLeft) + "px";
		s5_photo_showcase_initial_load = "no";
	} else {
		document.getElementById("s5_photo_showcase_button_content").style.left = ((document.getElementById("s5_photo_showcase_height_width_img").offsetWidth * (s5_photo_showcase_count_items.length/3)) * -1) + (document.getElementById("s5_photo_showcase_height_width").offsetLeft - document.getElementById("s5_photo_showcase_outer").offsetLeft) + "px";
	}
}

function s5_photo_showcase_arrow_positioning() {
	document.getElementById("s5_photo_showcase_prev_line").style.left = ((document.getElementById("s5_photo_showcase_outer").offsetWidth - document.getElementById("s5_photo_showcase_height_width_img").offsetWidth) / 2) + "px";
	document.getElementById("s5_photo_showcase_prev").style.left = ((document.getElementById("s5_photo_showcase_outer").offsetWidth - document.getElementById("s5_photo_showcase_height_width_img").offsetWidth) / 2) + document.getElementById("s5_photo_showcase_prev_line").offsetWidth - 1 + "px";
	document.getElementById("s5_photo_showcase_next_line").style.left = ((document.getElementById("s5_photo_showcase_outer").offsetWidth - document.getElementById("s5_photo_showcase_height_width_img").offsetWidth) / 2) + document.getElementById("s5_photo_showcase_height_width_img").offsetWidth + "px";
	document.getElementById("s5_photo_showcase_next").style.left = ((document.getElementById("s5_photo_showcase_outer").offsetWidth - document.getElementById("s5_photo_showcase_height_width_img").offsetWidth) / 2) + document.getElementById("s5_photo_showcase_height_width_img").offsetWidth - document.getElementById("s5_photo_showcase_next").offsetWidth + 1 + "px";
	
	document.getElementById("s5_photo_showcase_prev_line").style.height = document.getElementById("s5_photo_showcase_height_width_img").offsetHeight + "px";
	document.getElementById("s5_photo_showcase_prev").style.height = document.getElementById("s5_photo_showcase_height_width_img").offsetHeight + "px";
	document.getElementById("s5_photo_showcase_next_line").style.height = document.getElementById("s5_photo_showcase_height_width_img").offsetHeight + "px";
	document.getElementById("s5_photo_showcase_next").style.height = document.getElementById("s5_photo_showcase_height_width_img").offsetHeight + "px";
}

function s5_photo_showcase_check_active () {
var s5_photo_showcase_check_active_find_id = "no";
var s5_photo_showcase_check_active = document.getElementById("s5_photo_showcase_outer").getElementsByTagName("LI");
	for (var s5_photo_showcase_check_active_y=0; s5_photo_showcase_check_active_y<s5_photo_showcase_check_active.length; s5_photo_showcase_check_active_y++) {
	var s5_photo_showcase_check_active_classname = s5_photo_showcase_check_active[s5_photo_showcase_check_active_y].className;
	if (s5_photo_showcase_check_active_classname.indexOf("s5_photo_showcase_item") >= 0) {
		if (s5_photo_showcase_check_active_find_id == "no") {
			if (s5_photo_showcase_check_active[s5_photo_showcase_check_active_y].id) { } else {
				s5_photo_showcase_check_active[s5_photo_showcase_check_active_y].className = "s5_photo_showcase_item s5_photo_showcase_active";
				s5_photo_showcase_check_active_find_id = "yes";
			}
		}
	}
}
}

function s5_photo_showcase_resize_calls() {
s5_photo_showcase_item_sizing();
s5_photo_showcase_left_positioning();
s5_photo_showcase_arrow_positioning();
}

function s5_photo_showcase_loaded() {
s5_photo_showcase_left_positioning();
s5_photo_showcase_arrow_positioning();
s5_photo_showcase_item_sizing();
document.getElementById("s5_photo_showcase_outer").className = "s5_photo_showcase_outer_loaded";
}

jQuery(document).ready( function() {
s5_photo_showcase_item_sizing();
s5_photo_showcase_left_positioning();
s5_photo_showcase_arrow_positioning();
window.setTimeout(s5_photo_showcase_check_active,200);
window.setTimeout(s5_photo_showcase_loaded,500);
document.getElementById("s5_photo_showcase_outer").style.visibility = "visible";
});

jQuery(window).resize(s5_photo_showcase_resize_calls);
