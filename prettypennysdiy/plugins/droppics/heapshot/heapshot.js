/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

(function( $ ) {
  
    var options =  {
        'indexStart'        : 100,
        'rotation'          : 180,
        'easing'            : function(x, t, b, c, d){return c*t/d + b;},
        'overflowparents'   : true
    };

    var _animate = false;

    var $heapshot;

    var methods = {
        init : function( o ) { 
            $heapshot = $(this);
            
            $heapshot.imagesLoaded(function(){
                $.extend(options,o);

                if(options.overflowparents===true){
                    $heapshot.parents().css('overflow','visible');
                }

                var width=0, height=0, top=0, left=0;
                var offset = $heapshot.offset();
                $heapshot.find('li').each(function(index,elem){
                    $(elem).css('z-index',options.indexStart+$heapshot.find('li').length-index);
                    t = Math.floor((Math.random()*10));
                    $(elem).css('top',t*5);
                    a = Math.floor((Math.random()*15)+3);
                    s=Math.floor((Math.random()*2));
                    if(s==0){
                        a=-a;
                    }
                    $(elem).data('rotation',a);
                    $(elem).rotate({
                        angle : a
                    });
                    el = $(elem).get(0);
                    bound = el.getBoundingClientRect();

//                    w = Math.max($(elem).height()*Math.sin(a*(Math.PI / 180)),$(elem).width()*Math.cos(a*(Math.PI / 180)));
//                    h = Math.max($(elem).height()*Math.cos(a*(Math.PI / 180)),$(elem).width()*Math.sin(a*(Math.PI / 180)));
                    if(bound.width > width){
                        width = bound.width;
                    }
                    if(bound.height > height){
                        height = bound.height;
                    }
                    if(bound.top < offset.top){
                        top = bound.top - offset.top;
                    }
                    if(bound.left < offset.left){
                        left = bound.left - offset.left;
                    }
                 });
                 $heapshot.css('width',width);
                 $heapshot.css('height',height);
//                 $heapshot.find('li').each(function(index,elem){
//                     $(elem).css('top',-top);
//                     $(elem).css('left',-left);
//                 });
                 bindFirst();
            });
        },
        next : function(){
            next();
        },
        previous : function(){
            previous();
        }
    };

    next = function(){
        
    };

    previous = function(){
        
    };

    bindFirst = function(){
        if(_animate === true ){
            return;
        }
        bindto = null;
        nbli = $heapshot.find('li').length;
        $heapshot.find('li').each(function(index,elem){
            if(parseInt($(elem).css('z-index')) === options.indexStart+nbli){
                bindto = elem;
            }
        });
        $(bindto).find('img').click(function(event){            
            if(_animate === true ){
                event.stopPropagation();
                return;
            }
            _animate = true;
            $e = $(this).parent();
            from = parseInt($e.getRotateAngle());
            to = parseInt(from + options.rotation);
            $e.animate({
                left : $(this).position().left+$(this).width()+20
            }, 1500, function() {
                //animation finished
                $e.css('z-index',options.indexStart);
                $heapshot.find('li').each(function(index,elem){
                    $(elem).css('z-index',parseInt($(elem).css('z-index'))+1);
                });
                rfrom = parseInt($(this).getRotateAngle());
                rto = parseInt(rfrom - options.rotation);
                $e.animate({
                    left : 0,
                }, 1500, function(){
                    $heapshot.find('li img').unbind();
                    _animate = false;
                    bindFirst();
                });
                $e.rotate({
                    angle: rfrom, 
                    animateTo : rto,
                    easing: options.easing,
                    duration:1500
                });
            });
            $e.rotate({
                angle: from, 
                animateTo : to,
                easing: options.easing,
                duration:1500
            });

        });  
    };

    $.fn.heapshot = function( method ) {
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            //error
        }    
  };
})( jQuery );