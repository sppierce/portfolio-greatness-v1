var resizeTimer;
var droppicsSriptImageloadedLoaded;
jQuery(document).ready(function($) {
    (droppicsColorboxInit = function(){    
        droppicsSriptImageloadedLoaded=setInterval(function(){
            if(typeof(window.droppicsHeadLoaded)==='undefined' || 
                (typeof(window.droppicsHeadLoaded)==='boolean' && window.droppicsHeadLoaded===true)){
                    $('.droppicslightboxsingle').colorbox({title:function(){
                                return $(this).data('title');
                        },maxWidth:'90%',maxHeight:'90%',className: 'droppics',iframe:true,width:'100%',height:'100%'});
                    $('.droppicslightbox').each(function(){
                        $(this).find('.wimg.droppicslightbox a').colorbox({
                            title:function(){
                                return $(this).data('title');
                            },
                            maxWidth:'90%',maxHeight:'90%',className: 'droppics',
                            onComplete:function(){
                                $('#cboxLoadedContent .cboxPhoto').attr('title',$(this).find('img').attr('title'));
                                $('#cboxLoadedContent .cboxPhoto').attr('alt',$(this).find('img').attr('alt'));
                            }
                        });
                        
                    });
                    $('.droppicsgallery').each(function(){   
                 
                        $(this).find('.wimg.droppicsvideo a').colorbox({  
                           title:function(){                               
                                return $(this).data('title');
                           },
                           iframe:true, innerWidth: 640, innerHeight: 390,
                            maxWidth:'90%',maxHeight:'90%',className: 'droppics',
                            onComplete:function(){
                               
                               $(this).colorbox.resize({innerWidth:$(this).data('vwidth'), innerHeight: $(this).data('vheight')} ); 
                               //fix over the full-screen button
                               $("#cboxNext, #cboxPrevious").css("height","85%")                                
                            }
                        });
                    });
                    
                    $('.droppicssinglevideo').each(function(){   
                          $(this).find('a').colorbox({  
                           title:function(){                               
                                return $(this).data('title');
                           },
                           iframe:true, innerWidth: 640, innerHeight: 390,
                            maxWidth:'90%',maxHeight:'90%',className: 'droppics',
                            onComplete:function(){
                               
                               $(this).colorbox.resize({innerWidth:$(this).data('vwidth'), innerHeight: $(this).data('vheight')} ); 
                               //fix over the full-screen button
                               $("#cboxNext, #cboxPrevious").css("height","85%")                                
                            }
                        });
                    })
                    
                    $('.droppicssingleimage').colorbox({title:function(){
                                return $(this).data('title');
                        },maxWidth:'90%',maxHeight:'90%',className: 'droppics'});

                    clearInterval(droppicsSriptImageloadedLoaded);
                }
            },100);    
    })();
});