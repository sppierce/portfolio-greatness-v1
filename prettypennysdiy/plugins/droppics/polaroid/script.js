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

var droppicsSriptPolaroidLoaded;
var spinner;
var pathname = window.location.pathname;

var spinnerOpts = {
  lines: 11,
  length: 4,
  width: 3, 
  radius: 7,
  corners: 0.4, 
  rotate: 0, 
  direction: 1, 
  color: '#000',
  speed: 1, 
  trail: 30, 
  shadow: false,
  hwaccel: false,
  className: 'droppicsspinner',
  zIndex: 2e9, 
  top: 'auto',
  left: '50%'
};
if(typeof(droppicsAutobrowse)==='undefined'){
    var droppicsAutobrowse = [];
}

(function($){
    $(document).ready(function(){
        if(typeof(polaroid_cat_id_click)==='undefined') {
            polaroid_cat_id_click = "";
        }
        if(typeof(initGallery)==='undefined') {
            initGallery = false;
        }
            if(!initGallery && hash_category_id != polaroid_cat_id_click) {
                $('.droppicsgallery').each(function (index) {
                    listchid = $(this).data('listchid').split(",");
                    if ($.inArray(hash_category_id.toString(), listchid) > -1) {
                        $(this).empty();
                        that_polaroid = this;
                        initGallery = true;
                        $.ajax({
                            type: "GET",
                            dataType: "html",
                            url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + hash_category_id
                        }).done(function (data) {
                            $.colorbox.remove();
                            unloadStyleId('droppicsgalleryStyle' + $(that_polaroid).data('id'));
                            unloadHeadFiles();
                            $(window).scrollTop($(that_polaroid).offset().top);
                            $(that_polaroid).replaceWith(data);
                        });
                        return;
                    }
                })
            }

        if(polaroid_cat_id_click){
            prependTo_gallery = '#droppicsgallery'+polaroid_cat_id_click ;
        }else {
            prependTo_gallery = '.droppicsgallerypolaroid';
        }

        $(''+prependTo_gallery+' .droppicscats .wcat').each(function () {
                if(typeof($(this).find('.droppicscatslink').data('catimage')) !== 'undefined' && $(this).find('.droppicscatslink').data('catimage')== '1' ){
                    return;
                }
                rot = 0;
                elem = $(this).find('.droppicscatslink').clone();
                elem.find('span').remove();
                elem.css({
                    'position': 'absolute',
                    'top': $(this).find('.droppicscatslink').position().top,
                    'left': $(this).find('.droppicscatslink').position().left
                });
                rot = Math.floor((Math.random() * 20) + 1);
                elem.css({
                    '-webkit-transform': 'rotate(' + rot + 'deg)',
                    '-moz-transform': 'rotate(' + rot + 'deg)',
                    '-ms-transform': 'rotate(' + rot + 'deg)',
                    'transform': 'rotate(' + rot + 'deg)'
                })
                    .prependTo($(this));
                rot = Math.floor((Math.random() * 20) + 1);
                elem.clone().css({
                    '-webkit-transform': 'rotate(-' + rot + 'deg)',
                    '-moz-transform': 'rotate(-' + rot + 'deg)',
                    '-ms-transform': 'rotate(-' + rot + 'deg)',
                    'transform': 'rotate(-' + rot + 'deg)'
                })
                    .prependTo($(this));
                rot = 0;
                $(this).find('.droppicscatslink:last').css(
                    {
                        '-webkit-transform': 'rotate(' + rot + 'deg)',
                        '-moz-transform': 'rotate(' + rot + 'deg)',
                        '-ms-transform': 'rotate(' + rot + 'deg)',
                        'transform': 'rotate(' + rot + 'deg)'
                    })
            });

        $(".droppicsgallerypolaroid .droppicscatslink").unbind('click').click(function(e) {
            e.preventDefault();
            that = this;
            categorytitle = $(that).data('categorytitle').toString().replace(/ /g, '-');
            var  urlnewparam = addParameter(categorytitle,$(that).data('id'));
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + $(that).data('id'),
            }).done(function (data) {
                $.colorbox.remove();
                unloadStyleId('droppicsgalleryStyle' + $(that).closest('.droppicsgallerypolaroid').data('id'));
                unloadHeadFiles();
                $(document).scrollTop($(that).closest('.droppicsgallery').offset().top);
                $(that).closest('.droppicsgallery').replaceWith(data);
                window.history.pushState('', document.title,urlnewparam);
            });
        });
        
        droppicsScriptPolaroidLoaded=setInterval(function(){
        if((typeof(window.droppicsHeadLoaded)==='undefined' && typeof($.imageLoaded())!=='undefined') || 
                (typeof(window.droppicsHeadLoaded)==='boolean' && window.droppicsHeadLoaded===true)){
            
                
                $(".droppicsgallerypolaroid").each(function(){
                    var id = $(this).data('id');
                    var that = this;
                    var current = 0;
                    if($(that).data('useinfinite') && typeof(droppicsAutobrowse)!=='undefined' && typeof(droppicsAutobrowse[id])!=='undefined'){
                           var number = $(this).data('infiniteajax');
                           var offset = $(this).data('infinitefirst');
                            $(that).find('.droppicspictures').autobrowse({
                                 url: false,
                                 template: function (response)
                                 {
                                     var markup='';
                                     for (var i=0; i<number && i+current<droppicsAutobrowse[id].length ; i++)
                                     {
                                         markup += droppicsAutobrowse[id][i+current];
                                     };
                                     current += number;
                                     $(that).find(".droppicspictures").find("div.clr").remove();
                                     return markup+'<div class="clr"></div>';
                                 },
                                 itemsReturned: function (response) {
                                     if(current>=droppicsAutobrowse[id].length){
                                         return 0;
                                     }
                                     return number;
                                 },
                                 offset: offset,
                                 loader: {
                                     append : function(){
                                             div = document.createElement('div');
                                             div.className = "droppicsspinnerwrapper";
                                             spinner = new OwnSpinner(spinnerOpts).spin(div);
                                             $(that).find('.droppicspictures').after(div);
                                             return '';
                                         },
                                         remove : function(){
                                             $(that).imagesLoaded(function(){
                                                 spinner.stop();
                                                 $(that).find('.droppicsspinnerwrapper').remove();
                                                 rotateImages();
                                                 droppicsColorboxInit();                                
                                             });
                                         }
                                     }
                                 }
                             );
                        }
                    (rotateImages= function() {
                        $('.droppicsgallerypolaroid.droppicsrotate .wimg.droppicsrotate').each(function(i,e){
                            a = Math.floor((Math.random()*15)+3);
                            s=Math.floor((Math.random()*2));
                            if(s==0){
                                a=-a;
                            }
                            $(e).removeClass('droppicsrotate');
                            $(e).data('rotation',a);
                            $(e).rotate({
                            angle : a,
                            bind: { 
                                        mouseover : function() { 
                                            $(e).rotate({animateTo:0})
                                        },
                                        mouseout : function() { 
                                            $(e).rotate({animateTo:$(e).data('rotation')})
                                        }
                                    } 
                            });        
                        });
                    })();
                });
                clearInterval(droppicsScriptPolaroidLoaded);
            }
        },100);

    });

})(jQuery);
