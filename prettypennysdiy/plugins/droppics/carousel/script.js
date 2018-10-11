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

var droppicsSriptCarouselLoaded;
var pathname = window.location.pathname;

(function($){

    if(typeof(carousel_cat_id_click)==='undefined') {
        carousel_cat_id_click = "";
    }
    $(document).ready(function(){
        if(typeof(initGallery)==='undefined') {
            initGallery = false;
        }
        if(!initGallery && hash_category_id != carousel_cat_id_click) {
            $('.droppicsgallery').each(function (index) {
                listchid = $(this).data('listchid').split(",");
                if ($.inArray(hash_category_id.toString(), listchid) > -1) {
                    $(this).empty();
                    that_carousel = this;
                    initGallery = true;
                    $.ajax({
                        type: "GET",
                        dataType: "html",
                        url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + hash_category_id
                    }).done(function (data) {
                        unloadStyleId('droppicsgalleryStyle' + $(that_carousel).data('id'));
                        unloadHeadFiles();
                        $(window).scrollTop($(that_carousel).offset().top);
                        $(that_carousel).replaceWith(data);
                    });
                    return;
                }
            })
        }

        droppicsSriptCarouselLoaded = setInterval(function () {
            if (typeof(window.droppicsHeadLoaded) === 'undefined' ||
                (typeof(window.droppicsHeadLoaded) === 'boolean' && window.droppicsHeadLoaded === true)) {
                $('.droppicsgallerycarousel').each(function () {
                    options = new Array();
                    that = $(this);
                    if (parseInt(that.data('shownav')) !== 1) {
                        options['directionNav'] = false;
                    } else {
                        options['directionNav'] = true;
                    }
                    if (parseInt(that.data('controlnav')) !== 1) {
                        options['controlNav'] = false;
                    } else {
                        options['controlNav'] = true;
                    }
                    options['afterChange'] = function () {
                        index = $('.droppicsgallerycarousel .nivoSlider').data('nivo:vars').currentSlide;
                        $('.droppicsgallerycarousel .nivoSlider').find(".wimg a").css("visibility", 'hidden');
                        if ($('.droppicsgallerycarousel .nivoSlider').find(".wimg:eq(" + index + ")").hasClass("isVideo")) {
                            $(".droppicsgallerycarousel i.video").css('opacity', '1').css('z-index', '9999');
                            $('.droppicsgallerycarousel .nivoSlider .wimg a.playBtn').css("visibility", 'visible');
                        } else {
                            $(".droppicsgallerycarousel i.video").css('opacity', '0').css('z-index', '-1');
                            $('.droppicsgallerycarousel .nivoSlider .wimg a.playBtn').css("visibility", "hidden");

                            $('.droppicsgallerycarousel .nivoSlider').find(".wimg:eq(" + index + ")").find('a').css("visibility", 'visible');
                        }
                    };

                    options['afterLoad'] = function () {
                        $('.droppicsgallerycarousel .nivoSlider').find(".wimg a").css("visibility", 'hidden');
                        if ($('.droppicsgallerycarousel .nivoSlider').find('.playBtn').length > 0) {
                            $('.droppicsgallerycarousel .nivoSlider').append('<i class="video"></i>');
                            $('i.video').click(function (e) {
                                e.preventDefault();
                                $(this).parents(".nivoSlider").find('.playBtn').last().click();
                            });
                            if ($('.droppicsgallerycarousel .nivoSlider').find(".wimg:eq(" + 0 + ")").hasClass("isVideo")) {
                                $(".droppicsgallerycarousel i.video").css('opacity', '1').css('z-index', '9999');
                                $('.droppicsgallerycarousel .nivoSlider .wimg a.playBtn').css("visibility", 'visible');
                            } else {
                                $(".droppicsgallerycarousel i.video").css('opacity', '0').css('z-index', '-1');
                                $('.droppicsgallerycarousel .nivoSlider .wimg a.playBtn').css("visibility", "hidden");
                                $('.droppicsgallerycarousel .nivoSlider').find(".wimg:eq(" + 0 + ")").find('a').css("visibility", 'visible');
                            }
                        }
                    };

                    that.find('.nivoSlider').nivoSlider(options);
                });
                clearInterval(droppicsSriptCarouselLoaded);
            }
        }, 100);

        if(carousel_cat_id_click){
            prependTo_gallery = '#droppicsgallery'+carousel_cat_id_click ;
        }else {
            prependTo_gallery = '.droppicsgallerycarousel';
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

        $(".droppicsgallerycarousel .droppicscatslink").unbind('click').click(function (e) {
            e.preventDefault();
            that = this;
            categorytitle = $(that).data('categorytitle').toString().replace(/ /g, '-');
            var  urlnewparam = addParameter(categorytitle,$(that).data('id'));
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "index.php?option=com_droppics&view=frontgallery&&id_gallery=" + $(that).data('id'),
            }).done(function (data) {
                window.history.pushState('', document.title, urlnewparam);
                unloadStyleId('droppicsgalleryStyle' + $(that).closest('.droppicsgallerycarousel').data('id'));
                unloadHeadFiles();
                $(window).scrollTop($(that).closest('.droppicsgallery').offset().top);
                $(that).closest('.droppicsgallery').replaceWith(data);
            });
        });

        $('.playBtn').click(function (e) {
            e.preventDefault();
            $('.droppicsgallerycarousel .nivoSlider').data('nivoslider').stop();
            var index = $(".droppicsgallerycarousel .nivo-controlNav .active").attr('rel');

            $(this).find('img').hide();
            var autoPlayLink = $("#vframeContainer").find(".vframe:eq(" + index + ")").html();
            var videoFrame = '<iframe src="' + autoPlayLink + '" allowfullscreen="" frameborder="0" height="100%" width="100%"></iframe>';
            $(this).append(videoFrame);
            $(this).css('opacity', '1');
            $(".droppicsgallerycarousel i.video").css('opacity', '0').css('z-index', '-1');
        });

        $('i.video').click(function (e) {
            e.preventDefault();
            $(this).parents(".nivoSlider").find('.playBtn').last().click();
        });

        $('.droppicsgallerycarousel .nivo-controlNav a, .nivo-directionNav a').live('click', function () {

            $('.droppicsgallerycarousel .nivoSlider .wimg iframe').remove();
            $('.droppicsgallerycarousel .nivoSlider .wimg a.playBtn').css("opacity", 0);
            $('.droppicsgallerycarousel .nivoSlider .wimg img').show();
            $('.droppicsgallerycarousel .nivoSlider').data('nivoslider').start();
            $(".droppicsgallerycarousel i.video").css('opacity', '1').css('z-index', '9999');
        });
    });
})(jQuery);
