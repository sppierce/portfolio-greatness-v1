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

var droppicsSriptHeapshotLoaded;
var pathname = window.location.pathname;
(function($){
    $(document).ready(function(){
        if(typeof(heapshot_cat_id_click)==='undefined') {
            heapshot_cat_id_click = "";
        }
        if(typeof(initGallery)==='undefined') {
            initGallery = false;
        }
        if(!initGallery && hash_category_id != heapshot_cat_id_click) {
            $('.droppicsgallery').each(function (index) {
                listchid = $(this).data('listchid').split(",");
                if ($.inArray(hash_category_id.toString(), listchid) > -1) {
                    $(this).empty();
                    that_heapshot = this;
                    initGallery = true;
                    $.ajax({
                        type: "GET",
                        dataType: "html",
                        url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + hash_category_id
                    }).done(function (data) {
                        unloadStyleId('droppicsgalleryStyle' + $(that_heapshot).data('id'));
                        unloadHeadFiles();
                        $(window).scrollTop($(that_heapshot).offset().top);
                        $(that_heapshot).replaceWith(data);
                    });
                    return;
                }
            })
        }

        droppicsSriptHeapshotLoaded=setInterval(function(){
        if((typeof(window.droppicsHeadLoaded)==='undefined' && typeof($.imageLoaded())!=='undefined') || 
                (typeof(window.droppicsHeadLoaded)==='boolean' && window.droppicsHeadLoaded===true)){
            $('.droppicsgalleryheapshot').each(function(){
                options = new Array();
                that = $(this);
                if(that.find('ul').data('overflowparents')==='1'){
                    options['overflowparents'] = true;
                }else{
                    options['overflowparents'] = false;
                }
                options['rotation'] = 80;
                that.find('ul').heapshot(options);
            });
            clearInterval(droppicsSriptHeapshotLoaded);
            }
        },100);

        if(heapshot_cat_id_click){
            prependTo_gallery = '#droppicsgallery'+heapshot_cat_id_click ;
        }else {
            prependTo_gallery = '.droppicsgalleryheapshot';
        }
        $(''+prependTo_gallery+' .droppicscats .wcat').each(function () {
            if(typeof($(this).find('.droppicscatslink').data('catimage')) !== 'undefined' && $(this).find('.droppicscatslink').data('catimage')== '1' ){
                return;
            }
                $(this).find('.droppicscatslink').css('margin-left', ($(this).width() - ($(this).find('.droppicscatslink img').width() + parseInt($(this).find('.droppicscatslink img').css('margin-left')) + parseInt($(this).find('.droppicscatslink img').css('margin-right')))) / 2);
                elem = $(this).find('.droppicscatslink').clone();
                ;
                elem.find('span').remove();
                elem.css({
                    'position': 'absolute',
                    'top': $(this).find('.droppicscatslink').position().top,
                    'left': $(this).find('.droppicscatslink').position().left
                });
                rot = Math.floor((Math.random() * 10) + 1);
                elem.css({
                    '-webkit-transform': 'rotate(' + rot + 'deg)',
                    '-moz-transform': 'rotate(' + rot + 'deg)',
                    '-ms-transform': 'rotate(' + rot + 'deg)',
                    'transform': 'rotate(' + rot + 'deg)'
                })
                    .prependTo($(this));
                rot = Math.floor((Math.random() * 10) + 1);
                elem.clone().css({
                    '-webkit-transform': 'rotate(-' + rot + 'deg)',
                    '-moz-transform': 'rotate(-' + rot + 'deg)',
                    '-ms-transform': 'rotate(-' + rot + 'deg)',
                    'transform': 'rotate(-' + rot + 'deg)'
                })
                    .prependTo($(this));
            });

        $(".droppicsgalleryheapshot .droppicscatslink").unbind('click').click(function(e) {
            e.preventDefault();
            that = this;
            categorytitle = $(that).data('categorytitle').toString().replace(/ /g, '-');
            var  urlnewparam = addParameter(categorytitle,$(that).data('id'));
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + $(that).data('id'),
            }).done(function (data) {
                unloadStyleId('droppicsgalleryStyle' + $(that).closest('.droppicsgalleryheapshot').data('id'));
                unloadHeadFiles();
                $(window).scrollTop($(that).closest('.droppicsgallery').offset().top);
                $(that).closest('.droppicsgallery').replaceWith(data);
                window.history.pushState('', document.title,urlnewparam);
            });
        });

    });
})(jQuery);
