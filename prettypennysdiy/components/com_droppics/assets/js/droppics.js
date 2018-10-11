/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barr?re (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
var editor;
if(typeof(Droppics)==='undefined'){
    var Droppics={};
    Droppics.can = {};
    Droppics.can.create=false;
    Droppics.can.edit=false;
    Droppics.can.delete=false;
    Droppics.baseurl='';    
    Droppics.selection = {};
    Droppics.custom = false;
    Droppics.editionMode = false;
}
var ck_id_gallery_last_view;

jQuery(document).ready(function($) {
        if(Droppics.can.edit){
            $('#preview').sortable({ 
                placeholder: 'highlight',
                revert: 300,
                distance: 15,
                tolerance: "pointer",
                items: ".wimg",
                helper : 'clone',
                appendTo: "body",
                cursorAt: {top:0, left: 0 },
                // add helper drop file
                helper : function(e,item){
                    filename = $(item).find('.img').data('file');
                    linkimg = $(item).find('.img').attr('src');
                    count = $('#preview').find('.selimg.selected').length;
                    countselect = 0;
                    $('#preview').find('.selimg.selected').each(function(){
                        filenameslect = $(this).find('.img').data('file');
                        if(filenameslect === filename){
                            countselect++;
                        }
                    });
                    if(countselect) {
                        $('#preview').find('.selimg.selected').each(function(){
                            $(this).parent().css("display", "none");
                        });
                    }
                    if(count>1 && countselect >0) {
                        return $( "<div id='file-handle' class='ui-widget-header' '><div style='display: none;'>"+ filename + "</div><span" + " class='fCount'>"+count+"</span><img src="+linkimg+"></div>" );
                    }else {
                        return $( "<div id='file-handle' class='ui-widget-header' ><div style='display: none;'>"+ filename +"</div><img src="+linkimg+"></div>" );
                    }
                },
                update : function(){
                    var json='';
                    $.each($('#preview .img'),function(i,val){
                        if(json!==''){
                            json+=',';
                        }
                        json+='"'+i+'":'+$(val).data('id-picture');
                    });
                    json = '{'+json+'}';
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=files.reorder",
			data    : {order: json},
                        type    :   "POST"
                    });
                },
                /** Prevent firefox bug positionnement **/
                start: function (event, ui) {
                    var userAgent = navigator.userAgent.toLowerCase();
                    if( ui.helper !== undefined && userAgent.match(/firefox/) ){
                        ui.helper.css('position','absolute').css('margin-top', $(window).scrollTop() );
                    }
                },
                beforeStop: function (event, ui) {
                    $('#preview').find('.selimg').each(function(){
                        $(this).parent().css("display","block");
                    });
                    var userAgent = navigator.userAgent.toLowerCase();
                    if( ui.offset !== undefined && userAgent.match(/firefox/) ){
                        ui.helper.css('margin-top', 0);
                    }
                }
            });
            $('#preview').disableSelection();
        }
        
       /* Init File import */
        if(Droppics.can.create){
            $('#jao').jaofiletree({ 
                script  : 'index.php?option=com_droppics&task=connector.listdir&tmpl=component',
                usecheckboxes : 'files',
                showroot : 'Images/'
            });
        }
        $('#importFilesBtn').click(function(){
            id_gallery = $('#gallerieslist li.active').data('id-gallery');
            //id_category = $('input[name=id_category]').val();
            var files= '';
            $($('#jao').jaofiletree('getchecked')).each(function(){files+='&files[]='+this.file;});
            if(files===''){
                return;
            }
            $.ajax({
                url     :   "index.php?option=com_droppics&task=files.import&"+$('#categoryToken').attr('name') + "=1&id_gallery="+id_gallery,
                type    : 'GET',
                data    :   files
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
                    bootbox.alert(result.datas.nb+Joomla.JText._('COM_DROPPICS_JS_X_FILES_IMPORTED', ' files imported'));
                    checkAndUpdatePreview(id_gallery);
                }else{
                    if(typeof(result.datas)!=='undefined' && result.datas=='noerror'){

                    }else{
                        bootbox.alert(result.response);
                    }
                }
            });
            return false;
        });
        $('#selectAllImportFiles').click(function(){
            $('#filesimport input[type="checkbox"]').attr('checked', true);
        });
        $('#unselectAllImportFiles').click(function(){
            $('#filesimport input[type="checkbox"]').attr('checked', false);
        });

       Droppics.checkAndUpdatePreview = checkAndUpdatePreview = function(id_gallery)
        {
            $.ajax({
                url: "index.php?option=com_droppics&task=gallery.ajaxCheckExitGallery&id_gallery=" + id_gallery ,
                type: "POST",
                dataType: "json",
            }).done(function (result) {
                if(result.datas.exist==false) {
                    updatepreview();
                }else {
                    updatepreview(id_gallery);
                }
            })
        }

    
/**
         * Reload a gallery preview
         * @param id_gallery
         */
         Droppics.updatepreview = updatepreview = function(id_gallery){
            if(typeof(id_gallery)==="undefined" || id_gallery===null){
                $('#gallerieslist li:first').addClass('active');
                id_gallery = $('#gallerieslist li.active').data('id-gallery');
                $('input[name=id_gallery]').val(id_gallery);
            }else{
                $('input[name=id_gallery]').val(id_gallery);
            }
            loading('#wpreview');
            $.ajax({
                url     :   "index.php?option=com_droppics&view=gallery&format=raw&id_gallery="+id_gallery,
                type    :   "POST"
            }).done(function(data){
                $('#preview').html($(data));
                if(Droppics.can.edit){
                    var remote_video = (Droppics.addRemoteVideo == 1) ? '<a href="javascript:void(0)" id="add_remote_video" class="btn btn-large btn-primary">'+Joomla.JText._('COM_DROPPICS_JS_ADD_REMOTE_VIDEO', 'Add remote video')+'</a> ' : '';
                    $('<div id="dropbox"><span class="message">'+Joomla.JText._('COM_DROPPICS_JS_DROP_FILES_HERE', 'Drop images here to upload')+'.<i> '+Joomla.JText._('COM_DROPPICS_JS_USE_UPLOAD_BUTTON', 'Or use the button below')+'</i></span><input class="hide" type="file" id="upload_input" multiple="">'+ remote_video+ '<a href="" id="upload_button" class="btn btn-large btn-primary">'+Joomla.JText._('COM_DROPPICS_JS_SELECT_FILES', 'Select files')+'</a></div><div class="clr"></div>').appendTo('#preview');
                    
                    $('#add_remote_video').unbind('click').click(function(e){ 
                        e.preventDefault();
                        //var allowed =  ["youtube", "vimeo","dailymotion"]; 
                        bootbox.dialog($("#droppics-remote-form").html(),
                            [{
                                "label" : "Save",
                                "class" : "btn-primary",
                                "callback": function() {
                                  
                                    var id_gallery = $('input[name=id_gallery]').val();
                                    var remote_url = encodeURIComponent($('.remote-dialog #droppics-remote-url').val());                                   
                                    var ajax_url = "index.php?option=com_droppics&task=files.addRemoteUrl&id_gallery=" + id_gallery ;
                                   
                                    $.ajax({
                                        url: ajax_url,
                                        type: "POST",
                                        data: {remote_url: remote_url}
                                    }).done(function (data) {

                                        result = $.parseJSON(data);
                                        if (result.response === true) {
                                            updatepreview();
                                        } else {
                                            bootbox.alert(result.response);
                                        }
                                        $('.remote-dialog').remove();

                                    });
                                }
                            }, {
                                "label" : "Cancel",
                                "class" : "s",
                                "callback": function() {
                                    $('.remote-dialog').remove();
                                }
                            }],
                            {
                                classes : 'remote-dialog'
                            }

                        );
                   
                        return false;
                    });
                    
                    
                    $('#preview').sortable('refresh');            
                }

                initUploadBtn();
                initThemeBtn();
                initImages();
                initInsertBtns();

                showThemes();
                loadParams();
                $('#wpreview').unbind();
                initDropbox($('#wpreview'));
                theme = $('input[name=theme]').val();
                $('.themesblock .selected').removeClass('selected');
                $('.themesblock a[data-theme='+theme+']').addClass('selected');
                rloading('#wpreview');
                
                if(typeof(Droppics.selection.selection)!=='undefined' && Droppics.selection.selection!=='' && typeof(Droppics.selection.picture)!=='undefined' && Droppics.selection.picture!==null){
                   
                    $('#preview img[data-id-picture='+Droppics.selection.picture+']').trigger('click');
                   
                }
                if(typeof(gcaninsert)!=='undefined' && gcaninsert !== true){
                    if(ck_id_gallery_last_view) {
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+id_gallery+'"]').addClass('active');
                    }
                }

            });
        };        
                
        /* init menu actions */
        initMenu();

        /* Load nestable */
        $('.nested').nestable().on('change', function(event, e){
            pk = $(e).data('id-gallery');
            if($(e).prev('li').length===0){
                position = 'first-child';
                if($(e).parents('li').length===0){
                    //root
                    ref = 0;
                }else{
                    ref = $(e).parents('li').data('id-gallery');
                }
            }else{
                position = 'after';
                ref = $(e).prev('li').data('id-gallery');
            }
            $.ajax({
                url     :   "index.php?option=com_droppics&task=categories.order&pk="+pk+"&position="+position+"&ref="+ref,
                type    :   "POST"
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){

                }else{
                    bootbox.alert(result.response);
                }
            });
        });
        if(Droppics.collapse===true){
            $('.nested').nestable('collapseAll');
        }
        
        //Check what is loaded via editor
        if(typeof(gcaninsert)!=='undefined' && gcaninsert===true){
            //check id_gallery add after in category
            var ck_id_gallery = getCookie('ck_id_gallery');
            if(typeof(window.parent.tinyMCE)!=='undefined'){
                content = window.parent.tinyMCE.get(e_name).selection.getContent();
                imgparent = window.parent.tinyMCE.get(e_name).selection.getNode().parentNode;
                exp = '<img.*data\-droppicspicture="([0-9]+)".*?>';
                picture = content.match(exp);
                exp = '<img.*data\-droppicscategory="([0-9]+)".*?>';
                category = content.match(exp);
                exp = '<img.*data\-droppicsgallery="([0-9]+)".*?>';
                gallery = content.match(exp);
                Droppics.selection = new Array();
                Droppics.selection.content = content;
                Droppics.selection.imgparent = imgparent;
                if(picture!==null && category!=null){
                    if(picture!==null){
                        elem = $(content).filter('img[data-droppicspicture='+picture[1]+']');
                        Droppics.selection.selection = elem;
                        Droppics.selection.selection.$ = Droppics.selection.selection[0];
                        delete Droppics.selection.selection[0];
                        Droppics.selection.picture = picture[1];
                    }
                    if(category!=null){
                        Droppics.selection.gallery = category[1];
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+category[1]+'"]').addClass('active');
                        checkAndUpdatePreview(category[1]);
                    }
                }else if(gallery!==null){
                    Droppics.selection.gallery = gallery[1];
                    $('#gallerieslist li').removeClass('active');
                    $('#gallerieslist li[data-id-gallery="'+gallery[1]+'"]').addClass('active');
                    checkAndUpdatePreview(gallery[1]);
                }else{
                    if(ck_id_gallery){
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+ck_id_gallery+'"]').addClass('active');
                        checkAndUpdatePreview(ck_id_gallery);
                    }else{
                        updatepreview();
                    }
                }
            }else if(typeof window.parent.CKEDITOR != 'undefined') {
                var ckEditor = window.parent.CKEDITOR.instances[e_name];  
                imgElement = ckEditor.getSelection().getSelectedElement();
                if(typeof imgElement != "undefined" && imgElement != null ) {
                    picture = imgElement.getAttribute("data-droppicspicture"); 
                    category = imgElement.getAttribute("data-droppicscategory"); 
                    gallery = imgElement.getAttribute("data-droppicsgallery"); 
                    if(picture!==null && category!=null){
                        if(picture!==null){
                            Droppics.selection.selection = imgElement;
                            Droppics.selection.picture = picture;
                        }
                        if(category!=null){
                            Droppics.selection.gallery = category;
                            $('#gallerieslist li').removeClass('active');
                            $('#gallerieslist li[data-id-gallery="'+category+'"]').addClass('active');
                            checkAndUpdatePreview(category);
                        }
                    }else if(gallery!==null){
                        Droppics.selection.gallery = gallery;
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+gallery+'"]').addClass('active');
                        checkAndUpdatePreview(gallery);
                    }else{
                        updatepreview();
                    }
                }else{
                    if(ck_id_gallery){
                        $('#gallerieslist li').removeClass('active');
                        $('#gallerieslist li[data-id-gallery="'+ck_id_gallery+'"]').addClass('active');
                        checkAndUpdatePreview(ck_id_gallery);
                    }else{
                        updatepreview();
                    }
                }
            }else{
                updatepreview();
            }
        }else{
            /* Load gallery */
            ck_id_gallery_last_view = getCookie('ck_id_gallery_last_view');
            checkAndUpdatePreview(ck_id_gallery_last_view);
        }
        //initDeleteBtn();

        function initImages(){
            $(document).unbind('click.window').bind('click.window',function(e){
                if( $(e.target).is('#rightcol') || 
                    $(e.target).parents('#rightcol').length>0 || 
                    $(e.target).is('.modal-backdrop') || 
                    $(e.target).is('.cke_dialog_background_cover') || 
                    $(e.target).parents('.bootbox.modal').length>0 ||
                    $(e.target).parents('.cke_inner').length>0  ||
                    $(e.target).parents('.cke_dialog').length>0 ||
                    $(e.target).is('.selimg .img') || 
                    Droppics.editionMode ===true ||
                    $(e.target).parents('#toolbar-copy').length> 0 ||
                    $(e.target).parents('#toolbar-scissors').length> 0
                    ){
                    return;
                }                
                $('#preview .selimg.selected').removeClass('selected');
                showThemes();
            });        

            $('#preview .img').unbind('click').click(function(e){                                             
                
                clickTarget = e.target;                               
                clickTimer = setTimeout(function() {                        
                    clearTimeout(clickTimer);   
                                                                        
                        //Allow multiselect
                        if($(clickTarget).parents('.wimg').find('.selimg').hasClass('selected')){
                           
                            if (!(e.ctrlKey || e.metaKey)){
                                $('#preview .selimg.selected').removeClass('selected');
                            }
                            $(clickTarget).parents('.wimg').find('.selimg').removeClass('selected');
                        }else{
                           
                            if (!(e.ctrlKey || e.metaKey)){
                                $('#preview .selimg.selected').removeClass('selected');
                            }
                            
                            $(clickTarget).parents('.wimg').find('.selimg').addClass('selected');
                        }
                       
                       if($('#preview .selimg.selected').length==1){
                           $('#preview').addClass('somethingselected');
                           showImage(clickTarget);
                       }else if ($('#preview .selimg.selected').length>1){
                           showImages();
                       }else{
                           showThemes();
                       }
                     
                       e.stopPropagation();
                },400);               
                
            });
            $('#preview .img .wbtn').remove();     
            
            $("#wpreview").imagesLoaded( function(){
                $('#wpreview img').each(function(index,value) {
                        var aw = jQuery(value).width();
                        var pw = jQuery(value).parents('.selimg').width();                     
                        var mw = Math.ceil((pw-aw) / 2);                       
                        $(value).next('i.video').css('right', mw);
                });
            })
            
        }

        function showThemes(){
            $('#rightcol').animate({width : '210px'});
            $('#pwrapper').animate({'margin-right':'220px'});
            $('.imageblock').fadeOut(function(){$('.themesblock').fadeIn();});
            $('#insertimage').fadeOut(function(){$('#insertgallery').fadeIn();});
            $('#preview').removeClass('somethingselected');
        }

        function showImage(e){
            //delete ckeditor if exists
            if(typeof(editor)!=='undefined' && editor!==null){
                editor.destroy();
                editor = null;
            }
            
            $('#pwrapper').animate({'margin-right':'520px'});
            $('#imageparameters').fadeIn();
            $('#imageedit').fadeIn();
            $('#imageblock').fadeIn();
            $('.themesblock').fadeOut(function(){$('.imageblock').fadeIn();});
            $('#insertgallery').fadeOut(function(){
                $('#insertimage').fadeIn();
                $('#editImage').fadeIn();
            });

            $('#rightcol').animate({width : '510px'},{complete:function(){
                $('#singleimage').attr('src',$(e).attr('src'));
                loadImageParams();
            }});
        }

        function loadImageParams(){
            id_picture = $('.selimg.selected img').data('id-picture');
            id_gallery = $('input[name=id_gallery]').val();
            imgp_replete_action =  $('#imgp_replete_action').val();
            imgp_replete_id_file =  $('#imgp_replete_id_file').val();
            d = new Date();
            var checkRepleteImg = false;
            if(imgp_replete_action == '1' && imgp_replete_id_file==id_picture){
                checkRepleteImg = true;
            }
            exttime =  checkRepleteImg == true ? '?'+d.getTime() : '';
            $.ajax({
                url     :   "index.php?option=com_droppics&view=picture&format=raw&id="+id_picture+'&id_gallery='+id_gallery,
                type    :   "POST"
            }).done(function(data){
                $('#imageparameters').html(data);
                SqueezeBox.initialize({});
                SqueezeBox.assign($('a.modal').get(), {
                        parse: 'rel'
                });
                $('#imageparameters form').unbind().on('submit',function(e){
                    //for bxslider only
                    if($("#jform_params_bxslider_image_html").length){
                        $("#jform_params_bxslider_image_html").val($("#bxsliderimagehtml").html());
                    }
                    id_picture = $('.selimg.selected img').data('id-picture');
                    id_gallery = $('input[name=id_gallery]').val();

                    //fix value for checkbox
                    $(this).find('input:checkbox').each(function(){
                         if( !$(this).is(':checked') ){
                             $(this).val("");
                        }
                    })                  
                    
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=picture.save&id="+id_picture+'&id_gallery='+id_gallery,
                        type    :   "POST",
                        data    :   $(this).parent().find('[name*="jform"], input')
                    }).done(function(data){
                        result = jQuery.parseJSON(data);
                        if(result.response===true){
                            loadImageParams();

                            $('.selimg.selected img').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+result.datas.id_gallery+'/thumbnails/'+result.datas.file+exttime);
                            $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                        }else{
                            bootbox.alert(result.response);
                        }
                    });                
                    return false;
                });
                
                if(typeof(Droppics.selection.selection)!=='undefined' && Droppics.selection.selection!=='' && typeof(Droppics.selection.picture)!=='undefined' && Droppics.selection.picture!==null){            
                    
                    try {
                        $('input[name="jform[params][imgp_radius]"]').val(parseInt($(Droppics.selection.selection.$).css('border-radius')||0));                       
                        $('input[name="jform[params][imgp_border]"]').val(parseInt($(Droppics.selection.selection.$).css('border-width')||0));
                        $('input[name="jform[params][imgp_border_color]"]').val(rgb2hex($(Droppics.selection.selection.$).css('border-color')));
                        $('input[name="jform[params][imgp_shadow]"]').val( ($(Droppics.selection.selection.$).css('box-shadow')||'').replace(/^.*(rgba?\([^)]+\)) ([0-9]+)px.*$/,'$2')||'0').trigger('change');
                        $('input[name="jform[params][imgp_shadow_color]"]').val(rgb2hex( ($(Droppics.selection.selection.$).css('box-shadow')||'').replace(/^.*(rgba?\([^)]+\)).*$/,'$1')||'#CCCCCC')).trigger('change');
                        //check css image not center
                        if($(Droppics.selection.selection.$).css('float') !=='none' ){
                            $('input[name="jform[params][imgp_margin_left]"]').val(parseInt($(Droppics.selection.selection.$).css('margin-left')||4));
                            $('input[name="jform[params][imgp_margin_right]"]').val(parseInt($(Droppics.selection.selection.$).css('margin-right')||4));
                            $('input[name="jform[params][imgp_margin_top]"]').val(parseInt($(Droppics.selection.selection.$).css('margin-top')||4));
                            $('input[name="jform[params][imgp_margin_bottom]"]').val(parseInt($(Droppics.selection.selection.$).css('margin-bottom')||4));
                        }
                        $('#imagealign button').removeClass('active');
                        switch ($(Droppics.selection.selection.$).css('float')){
                            case 'left':
                                $('#imagealign button[data-align="left"]').addClass('active');
                                break;  
                            case 'right':
                                $('#imagealign button[data-align="right"]').addClass('active');
                                break;
                            default:
                                $('#imagealign button[data-align="none"]').addClass('active');
                                break;
                        }
                       
                        //onclick
                        if($(Droppics.selection.selection.$).data('droppicslightbox')=='lightbox'){ 
                            $('#imgp_click').val('lightbox');
                        }else if($(Droppics.selection.imgparent).is('a')){
                                $('#imgp_click').val('custom').trigger('change');
                                $('#click_content_custom_id').val($(Droppics.selection.imgparent).first('a').attr('href'));
                        }
                      
                        if($(Droppics.selection.imgparent).first('a').attr('target')==='_blank'){
                            $('#imgp_click_target').val('_blank');
                        }else if($(Droppics.selection.imgparent).first('a').hasClass('droppicslightboxsingle')){ 
                            $('#imgp_click_target').val('lightbox');
                        }else{
                            $('#imgp_click_target').val('current');
                        }

                        if($(Droppics.selection.selection.$).data('droppicssource')==='original'){ 
                            $('#imgp_source input[value="original"]').attr('checked','checked');
                            $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/'+$('.selimg.selected' + ' img').data('file'));
                        }else if($(Droppics.selection.selection.$).data('droppicssource')==='thumbnail'){ 
                            $('#imgp_source input[value="thumbnail"]').attr('checked','checked');                           
                            $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/thumbnails/'+$('.selimg.selected img').data('file'));
                        }else{
                            Droppics.custom = $(Droppics.selection.selection.$).data('droppicssource');
                        }
                        Droppics.selection = {};   
                    }catch(err) {
                        console.log(err);
                    }
                    
                }
                //Remove old customs
                //$('#imgp_source input[value^=custom_]').parent().remove();
                //Load customs images
                customs = $('.selimg.selected img').data('customs').each(function(el){
                    cloned = $('#imgp_source .template').first().clone(true).insertBefore('#imgp_source .template').removeClass('template').show();
                    if(typeof el.title != "undefined") {
                        cloned.find('span').html(el.title+' ('+el.width+'x'+el.height+')');
                    }else {
                        cloned.find('span').html(el.file.split('.')[0]+' ('+el.width+'x'+el.height+')');
                    }
                    cloned.find('input').attr('value', 'custom_'+el.id);
                    if(Droppics.custom==='custom_'+el.id){
                        cloned.find('input').attr('checked','checked');
                        $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/custom/'+el.file+'?');
                    }
                });

                if(Droppics.custom!==false){
                    Droppics.custom=false;
                }else{
                    var  imgp_source_array = ["original","thumbnail","custom"];
                    var chk_customs = $('input[name="jform[params][imgp_source]"]').val();
                    if (imgp_source_array.indexOf(chk_customs) == -1) {
                        $('.radio input[value="' + chk_customs + '"]').attr('checked', 'checked');
                    } else {
                        if ($('#imgp_source input:checked').length === 0 || chk_customs == "custom") {
                            $('#imgp_source label:first input').attr('checked', 'checked').trigger('change');
                            $('#singleimage').attr('src', Droppics.baseurl + '/'+drp_root_folder+'/' + id_gallery + '/thumbnails/' + $('.selimg.selected img').data('file'));
                        }
                    }
                }


                rloading('#rightcol');
                initDeleteBtn();
                imgp_bind_event();
                $('.droppics-chosen-select').chosen({"disable_search_threshold":5,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Type or select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
                var remote_video = '';
                $('<div id="dropbox"><span class="message">'+Joomla.JText._('COM_DROPPICS_JS_DROP_FILES_HERE_REPLACE', 'Drop images here to replace')
                    +'.<i> '+Joomla.JText._('COM_DROPPICS_JS_USE_REPLACE_BUTTON', 'Or use the button below')+'</i></span><input class="hide" type="file" id="replace_upload_input" multiple="">'
                    + remote_video+ '<a href="" id="replace_upload_button" class="btn btn-large btn-primary">'+Joomla.JText._('COM_DROPPICS_JS_SELECT_FILES_REPLACE', 'Select and Replace')+'</a></div><div class="clr"></div>').appendTo('#replete_wrap');
                initReplaceUploadBtn();
                initDropboxReplace($('#replete_wrap'));
                if(checkRepleteImg){
                    srcold =  $('#imageblock #singleimage').attr("src");
                    $('#imageblock #singleimage').attr("src",srcold+exttime);
                }
            });
        }

        function showImages(){
            $('#imageparameters').fadeOut();
            $('#imageedit').fadeOut();
            $('#imageblock').fadeOut();
            $('#insertimage').fadeOut();
            $('#editImage').fadeOut();
        }

        function initDeleteBtn(){
            $('.deleteImage').unbind('click').click(function(e){
                    e.preventDefault();
                    if($(e.target).is('#deleteImage')){
                        message = 'COM_DROPPICS_JS_ARE_YOU_SURE_ALL';
                    }else{
                        message = 'COM_DROPPICS_JS_ARE_YOU_SURE';
                    }
                    that = $(this);
                    bootbox.confirm(Joomla.JText._(message, 'Are you sure')+'?',function(result){
                        if(result===true){
                            //Delete picture
                            if($(e.target).is('#deleteImage')){
                                var pictures = [];
                                $('#preview .selimg.selected img.img').each(function(index){
                                    pictures[index] = $(this).data('id-picture');
                                });
                                $.ajax({
                                    url     :   "index.php?option=com_droppics&task=files.delete",
                                    type    :   "POST",
                                    data    :   {pictures : pictures}
                                }).done(function(data){
                                    result = jQuery.parseJSON(data);
                                    $.each(result,function(index,value){
                                        $('#preview .selimg.selected img.img[data-id-picture="'+value+'"]').parents('.wimg').fadeOut(500, function() {$(this).remove();});
                                    });
                                });
                                showThemes();
                            }else{
                                //delete custom picture                               
                                custom = $(that).siblings('input').val(); 
                                custom = custom.replace('custom_','');
                                $.ajax({
                                    url     :   "index.php?option=com_droppics&task=files.deleteCustom",
                                    type    :   "POST",
                                    data    :   {id : custom}
                                }).done(function(data){
                                    result = jQuery.parseJSON(data);
                                    if(result.response===true){
                                        infos = $('.selimg.selected img').data('customs');
                                        newinfos = [];
                                        $(infos).each(function(i,v){
                                           if(v.id!==custom){
                                               newinfos.push(v); 
                                           } 
                                        });
                                        $('.selimg.selected img').data('customs',newinfos)                                        
                                        $(that).parent().siblings(':first').find('input').attr('checked','checked').trigger('change');
                                        $(that).parent().remove();                                        
                                    }else{
                                        bootbox.alert(result.response);
                                    }
                                });
                            }
                        }
                    }); 
                    return false;
                });
        }

        function initInsertBtns(){
            $('#insertgallery').unbind('click').click(function(){
               window.parent.jInsertEditorText(insertGallery(),e_name);
               window.parent.SqueezeBox.close();
            });
            $('#insertimage').unbind('click').click(function(e){
                e.preventDefault();
                if($(this).hasClass('disabled')){
                    return;
                }
                datas = '';
                style = getStyle(false);
                id_gallery = $('input[name=id_gallery]').val();
                src= Droppics.relativeUrl+'/'+drp_root_folder+'/'+id_gallery+'/';
                switch ($('#imgp_source input:checked').val()){
                    case 'thumbnail':
                        src += 'thumbnails/';
                        datas += 'data-droppicssource="thumbnail"';
                        src += $('.selimg.selected img').data('file');
                        break;
                    case 'original':
                        datas += 'data-droppicssource="original"';
                        src += $('.selimg.selected img').data('file');
                        break;
                    default:
                        custom = $('#imgp_source input:checked').val();
                        custom = custom.replace('custom_','');
                        infos = $('.selimg.selected img').data('customs');
                        result = $.grep(infos, function(e){ 
                            return e.id == custom; 
                        });
                        src += 'custom/'+result[0].file;
                        datas += 'data-droppicssource="custom_'+result[0].id+'"';
                        break;
                }
                
                nclick = $('#imgp_click').val();
                datas+=' data-click="'+nclick+'"';
                if(nclick==='lightbox'){
                    datas+=' data-droppicslightbox="lightbox"';
                }
                
                if($('#show_caption').is(':checked')) {
                     datas += ' data-show-caption="1" ';
                }
               
                id_gallery = jQuery('input[name=id_gallery]').val();

                title = $('#click_content_custom_title').val().replace('"','&quot;');
                src = src + '?' + Date.now();
                image = '<img src="'+src+'" title="'+title+'" data-title="'+title+'" data-droppicspicture="'+$('.selimg.selected img').data('id-picture')+'" data-droppicscategory="'+id_gallery+'" style="'+style+'" '+datas+' />';

                vtarget='';
                target = $('#imgp_click_target').val();
                if(target=='_blank'){
                    vtarget='target="_blank"';
                }else if(target=='lightbox'){
                    vtarget='class="droppicslightboxsingle"';
                }

                if(nclick==='article'){
                    article = $('#click_content_article_link').val();
                    if(article!==''){
                        image = '<a href="'+article+'" '+vtarget+' >'+image+'</a>';
                    }
                }else if(nclick==='menuitem'){
                    menuitem = $('#click_content_menuitem_id').val();
                    image = '<a href="'+menuitem+'" '+vtarget+' >'+image+'</a>';
                }else if(nclick==='custom'){
                    custom = $('#click_content_custom_id').val();
                    if(custom.substring(0,4)==='www.'){
                        custom = 'http://'+custom;
                    }
                    image = '<a href="'+custom+'" '+vtarget+' >'+image+'</a>';
                }
                setCookie('ck_id_gallery',id_gallery,30);
                if(window.parent){
                    window.parent.jInsertEditorText(image,e_name);
                    window.parent.SqueezeBox.close();
                }
            });

            //init unique image
            $('#singleimage').attr('style',getStyle(true));
            $('#imageblock input').on('change',function(){
                $('#singleimage').attr('style',getStyle(true));
            });


            function getStyle(isimage){
                style="";
                radius = $('input[name="jform[params][imgp_radius]"]').val();
                if(radius>0){
                    style+='border-radius: '+radius+'px;';
                    style+='-webkit-border-radius: '+radius+'px;';
                    style+='-moz-border-radius: '+radius+'px;';
                }
                border = $('input[name="jform[params][imgp_border]"]').val();
                if(border>0){
                    style+='border-width: '+border+'px; border-style:solid;';
                    bordercolor = $('input[name="jform[params][imgp_border_color]"]').val();
    //                if(bordercolor!=''){
                        style+='border-color: '+bordercolor+';';
    //                }
                }
                shadow = $('input[name="jform[params][imgp_shadow]"]').val();
                shadowcolor = $('input[name="jform[params][imgp_shadow_color]"]').val();
                if(shadowcolor!='' && shadow>0){
                    style += 'shadow-color: '+shadowcolor+';';
                    style += 'box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                    style += '-moz-box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                    style += '-webkit-box-shadow: '+shadow+'px '+shadow+'px '+shadow+'px 1px '+shadowcolor+';';
                }
                if(typeof(isimage)===undefined || isimage===false ){
                    // check image position center
                    jpimagealign = $('#imagealign button.active').data('align');
                    if(jpimagealign == 'none'){
                        style+='margin: auto ; display: block; ';
                    }else {
                        margin_left = $('input[name="jform[params][imgp_margin_left]"]').val();
                        if (margin_left > 0) {
                            style += 'margin-left: ' + margin_left + 'px;';
                        }
                        margin_top = $('input[name="jform[params][imgp_margin_top]"]').val();
                        if (margin_top > 0) {
                            style += 'margin-top: ' + margin_top + 'px;';
                        }
                        margin_right = $('input[name="jform[params][imgp_margin_right]"]').val();
                        if (margin_right > 0) {
                            style += 'margin-right: ' + margin_right + 'px;';
                        }
                        margin_bottom = $('input[name="jform[params][imgp_margin_bottom]"]').val();
                        if (margin_bottom > 0) {
                            style += 'margin-bottom: ' + margin_bottom + 'px;';
                        }
                    }
                    switch ($('#imagealign button.active').data('align')){
                        case 'none':
                            break;
                        case 'right':
                            style += 'float: right;';
                            break;
                        default:
                            style += 'float: left;';
                            break;
                    }
                }                
                return style;
            }
        }

        function initThemeBtn(){
            $('.themesblock a.themebtn').unbind('click').click(function(e){
                theme = $(this).data('theme');
                id_gallery = $('input[name=id_gallery]').val();
                $.ajax({
                    url     :   'index.php?option=com_droppics&task=gallery.setTheme&id_gallery='+id_gallery+'&theme='+theme,
                    type    :   'POST'
                }).done(function(data){
                    result = jQuery.parseJSON(data);
                    if(result.response===true){
                        updatepreview(id_gallery);        
                    }else{
                        bootbox.alert(result.response);
                    }

                });
                return false;
            });
        }

        function loadParams(){
            id_gallery = $('input[name=id_gallery]').val();
            loading('#rightcol');
            $.ajax({
                url     :   "index.php?option=com_droppics&task=gallery.edit&layout=form&id="+id_gallery,
                type    :   'POST'
            }).done(function(data){
                $('#galeryparams').html(data);
                $('#droppicsparams').on('submit',function(){
                    id_gallery = $('input[name=id_gallery]').val();
                    $.ajax({
                        url     :   "index.php?option=com_droppics&task=gallery.save&id="+id_gallery,
                        type    :   "POST",
                        data    :   $('#droppicsparams [name*="jform"], #droppicsparams input')
                    }).done(function(data){
                        result = jQuery.parseJSON(data);
                        if(result.response===true){
                            checkAndUpdatePreview(id_gallery);
                            loadParams();
                            $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                        }else{
                            bootbox.alert(result.response);
                        }
                        loadParams();
                    });                
                    return false;
                });
                rloading('#rightcol');
               

            });
        }
/* right tab with cookie */
        droppic_tabs = jQuery.parseJSON ( getCookie("droppic_tabs") ); 
        if(droppic_tabs ==  null) { droppic_tabs = {} ; }
                
        if( $("#rightcol .themesblock .well h4").find(".ui-accordion-header-icon").length == 0) {
            $("#rightcol .themesblock .well h4").prepend('<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>');
        }
                
        $("#rightcol .themesblock .well h4").each(function(){
            content_id = $(this).next().attr('id');
            if(typeof droppic_tabs[content_id] != "undefined" && !droppic_tabs[content_id] ) {
                    $(this).next().hide();
            } else {
                    $(this).next().show();
                    $( this ).addClass( "ui-state-active" );
            }
        }) ;
                
        $("#rightcol .themesblock .well h4").click(function() {                        
            $( this ).toggleClass( "ui-state-active" );
            $(this).next().toggle();
            droppic_tabs[$(this).next().attr('id')] = $(this).hasClass('ui-state-active');        
            document.cookie="droppic_tabs="+ JSON.stringify(droppic_tabs);
            return false;
        });
                
        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i=0; i<ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
            }
            return "";
        }
    
        function initUploadBtn(){
            if(Droppics.can.edit){
                $('#upload_button').on('click',function(){
                    $('#upload_input').trigger('click');
                    return false;
                });
            }
        }
    function initReplaceUploadBtn(){
        if(Droppics.can.edit){
            $('#replace_upload_button').on('click',function(){
                $('#replace_upload_input').trigger('click');
                return false;
            });
        }
    }

        /**
         * Click on new gallery btn
         */
        $('#newgallery').on('click',function(){
            $.ajax({
                url     :   "index.php?option=com_droppics&task=category.addCategory",
                type    : 'POST',
                data    :   $('#galleryToken').attr('name') + '=1'
            }).done(function(data){
                result = jQuery.parseJSON(data);
                if(result.response===true){
                   link = ''+
                         '<li class="dd-item dd3-item" data-id-gallery="'+result.datas.id_category+'">'+
                                '<div class="dd-handle dd3-handle"></div>'+
                                '<div class="dd-content dd3-content">';
                        if(Droppics.can.edit){
                                    link += '<a class="edit"><i class="icon-edit"></i></a>';
                        }
                        if(Droppics.can.delete){
                                    link += '<a class="trash"><i class="icon-trash"></i></a>';
                        }
                                    link += '<a href="" class="t">'+
                                        '<span class="title">'+result.datas.name + '</span>' +
                                    '</a>'+
                                '</div>';
                    $(link).prependTo('#gallerieslist');
                    initMenu();
                    catDroppable();
                    $('#insertgallery').show();
                    $('#gallerieslist li[data-id-gallery='+result.datas.id_category+']').find('.dd-content').click();
                }else{
                    bootbox.alert(result.response);
                }
            });
            return false;
        });


        /**
         * Init the dropbox 
         **/
        function initDropbox(dropbox){
            if(!Droppics.can.edit){
                return;
            }
            // for theme bxslider
            dropbox.filedrop({
                    paramname:'pic',
                    fallback_id:'upload_input',
                    maxfiles: 30,
                    maxfilesize: 10,
                    queuefiles: 2,
                    data: {
                        id_gallery : function(){
                            return $('input[name=id_gallery]').val(); 
                        },
                        // check theme bxslider add default option image discription
                        check_theme_bxslider: function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') { return 1; } else{ return 0; }
                        },
                        bxs_images_bgcolor : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_images_bgcolor').val();
                            }
                        },
                        bxs_images_transparency : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_images_transparency').val();
                            }
                        },
                        bxs_desc_top_position : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_desc_top_position').val();
                            }
                        },
                        bxs_desc_left_position : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_desc_left_position').val();
                            }
                        },
                        bxs_desc_width_position : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_desc_width_position').val();
                            }
                        },
                        bxs_desc_height_position : function() {
                            if ($('#themeSelection .selected').data('theme') === 'bxslider') {
                                return $('.bxs_desc_height_position').val();
                            }
                        }
                    },
                    url: 'index.php?option=com_droppics&task=files.upload',

                    uploadFinished:function(i,file,response){
                        if(response.response===true){
                            $.data(file).addClass('done');
                            $.data(file).find('img').attr('src', response.datas.thumbnail);
                            $.data(file).find('img').attr('data-id-picture', response.datas.id_picture);
                            $.data(file).find('img').attr('data-file', response.datas.name);
                            if(typeof response.datas.customs != 'undefined') {
                                $.data(file).find('img').attr('data-customs', response.datas.customs);
                            }else {
                                $.data(file).find('img').attr('data-customs', '[]');
                            }

                        }else{
                            bootbox.alert(response.response);
                            $.data(file).remove();
                        }
                    },

                    error: function(err, file) {
                            switch(err) {
                                    case 'BrowserNotSupported':
                                            bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_BROWSER_NOT_SUPPORT_HTML5', 'Your browser does not support HTML5 file uploads!'));
                                            break;
                                    case 'TooManyFiles':
                                            bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_TOO_ANY_FILES','Too many files')+'!');
                                            break;
                                    case 'FileTooLarge':
                                            bootbox.alert(file.name+' '+Joomla.JText._('COM_DROPPICS_JS_FILE_TOO_LARGE', 'is too large')+'!');
                                            break;
                                    default:
                                            break;
                            }
                    },

                    // Called before each upload is started
                    beforeEach: function(file){
                            if(!file.type.match(/^image\//)){
                                    bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_ONLY_IMAGE_ALLOWED','Only images are allowed')+'!');
                                    return false;
                            }
                    },

                    uploadStarted:function(i, file, len){

                            var preview = $('<div class="wimg uploadplaceholder">'+
                                                '<div class="selimg">'+
                                                    '<img class="img" />'+
                                                    '<span class="uploaded"></span>'+
                                                    '<div class="progress progress-striped active">'+
                                                        '<div class="bar"></div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>');
                            var image = $('img', preview);

                            var reader = new FileReader();

                            reader.onload = function(e){

                                    // e.target.result holds the DataURL which
                                    // can be used as a source of the image:

                                    image.attr('src',e.target.result);
                            };

                            // Reading the file as a DataURL. When finished,
                            // this will trigger the onload function above:
                            reader.readAsDataURL(file);
                            //edit prepend view
                            var list = document.getElementById('preview').getElementsByClassName("droppicsgallery")[0];
                            var idImageOnTop = $('#idImageOnTop').val();
                            if(idImageOnTop == 1){
                                $(list).prepend(preview);
                            }else{
                                preview.appendTo('#preview .droppicsgallery');
                            }
                            // Associating a preview container
                            // with the file, using jQuery's $.data():

                            $.data(file,preview);
                    },

                    progressUpdated: function(i, file, progress) {
                            $.data(file).find('.progress .bar').width(progress+'%');
                    },

                    afterAll: function(){
                        $('#preview .progress').delay(300).fadeIn(300).hide(300, function(){
                          $(this).remove();
                        });
                        $('#preview .uploaded').delay(300).fadeIn(300).hide(300, function(){
                          $(this).remove();
                        });
                        $('#preview .wimg').delay(1200).show(1200,function(){
                            $(this).removeClass('done placeholder');
                        });
                        initInsertBtns();
                        initImages();
                    },
                    rename : function(name){
                        ext = name.substr(name.lastIndexOf('.'),name.lenght);
                        name = name.substr(0, name.lastIndexOf('.'));
                        var pattern_accent = new Array("é", "è", "ê", "ë", "ç", "à", "â", "ä", "î", "ï", "ù", "ô", "ó", "ö"); 
                        var pattern_replace_accent = new Array("e", "e", "e", "e", "c", "a", "a", "a", "i", "i", "u", "o", "o", "o");
                        name = preg_replace (pattern_accent, pattern_replace_accent,name);

                        name = name.replace(/\s+/gi, '-');
                        name = name.replace(/[^a-zA-Z0-9\-]/gi, '');                    
                        return name+ext;
                    }
            });
        }

        //replace image dropics
    function initDropboxReplace(dropbox){
        if(!Droppics.can.edit){
            return;
        }
        var data = new FormData();
        $('#preview .selimg.selected img.img').each(function(index){
            data.append('id_picture_old', $(this).data('id-picture'));
            data.append('name_file_old', $(this).data('file'));
        });
        data.append('id_gallery',$('input[name=id_gallery]').val());

        dropbox.filedrop({
            paramname:'pic',
            fallback_id:'replace_upload_input',
            maxfiles: 30,
            maxfilesize: 10,
            queuefiles: 2,
            data: {
                id_gallery : function(){return $('input[name=id_gallery]').val();},
                id_picture_old : function(){return $('#preview .selimg.selected img.img').data('id-picture');},
                name_file_old : function(){return $('#preview .selimg.selected img.img').data('file');},
                cus_id_checked : function(){
                    custom = $('#imgp_source input:checked').val();
                    custom = custom.replace('custom_','');
                    return custom;
                }
            },
            url: 'index.php?option=com_droppics&task=files.uploadReplaceFile',

            uploadFinished:function(i,file,response){
                if(response.response===true){
                    $.data(file).addClass('done');
                    $.data(file).find('img').attr('src', response.datas.thumbnail);
                    $.data(file).find('img').attr('data-id-picture', response.datas.id_picture);
                    $.data(file).find('img').attr('data-file', response.datas.name);
                    if(typeof response.datas.customs != 'undefined') {
                        $.data(file).find('img').attr('data-customs', response.datas.customs);
                    }else {
                        $.data(file).find('img').attr('data-customs', '[]');
                    }
                    $('#imgp_replete_action').val(1);
                    $('#imgp_replete_id_file').val(response.datas.id_picture);

                    id_gallery = $('input[name=id_gallery]').val();
                    tst = new Date().getTime();
                    switch ($('#imgp_source input:checked').val()){
                        case 'thumbnail':
                            $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/thumbnails/'+response.datas.name+'?ver='+tst);
                            if($('#bxsliderimage')[0]) {
                                $('#bxsliderimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/thumbnails/'+response.datas.name+'?ver='+tst);
                            }
                            break;
                        case 'original':
                            $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/'+response.datas.name+'?ver='+tst);
                            if($('#bxsliderimage')[0]) {
                                $('#bxsliderimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/'+response.datas.name+'?ver='+tst);
                            }
                            break;
                        default:
                            result = response.datas.customschecked;
                            $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/custom/'+result.file+'?ver='+tst);
                            $('#newCustomFilename').val( result.file.substr(0,result.file.lastIndexOf('.') ) );
                            $('#changeCustomFile').show();
                            if($('#bxsliderimage')[0]) {
                                $('#bxsliderimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/custom/'+result.file+'?ver='+tst);
                            }
                            break;
                    }
                    $.data(file).find('.selimg').attr("class","selimg selected");
                }else{
                    bootbox.alert(response.response);
                    $.data(file).remove();
                }
            },

            error: function(err, file) {
                switch(err) {
                    case 'BrowserNotSupported':
                        bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_BROWSER_NOT_SUPPORT_HTML5', 'Your browser does not support HTML5 file uploads!'));
                        break;
                    case 'TooManyFiles':
                        bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_TOO_ANY_FILES','Too many files')+'!');
                        break;
                    case 'FileTooLarge':
                        bootbox.alert(file.name+' '+Joomla.JText._('COM_DROPPICS_JS_FILE_TOO_LARGE', 'is too large')+'!');
                        break;
                    default:
                        break;
                }
            },

            // Called before each upload is started
            beforeEach: function(file){
                if(!file.type.match(/^image\//)){
                    bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_ONLY_IMAGE_ALLOWED','Only images are allowed')+'!');
                    return false;
                }
            },

            uploadStarted:function(i, file, len){

                var preview = $('<div class="wimg uploadplaceholder">'+
                    '<div class="selimg">'+
                    '<img class="img" />'+
                    '<span class="uploaded"></span>'+
                    '<div class="progress progress-striped active">'+
                    '<div class="bar"></div>'+
                    '</div>'+
                    '</div>'+
                    '</div>');
                var image = $('img', preview);

                var reader = new FileReader();

                reader.onload = function(e){

                    // e.target.result holds the DataURL which
                    // can be used as a source of the image:

                    image.attr('src',e.target.result);
                };

                // Reading the file as a DataURL. When finished,
                // this will trigger the onload function above:
                reader.readAsDataURL(file);
                $('#preview .selimg.selected').each(function(index){
                    $(this).empty();
                    $(this).replaceWith(preview);
                });
                $.data(file,preview);
            },

            progressUpdated: function(i, file, progress) {
                $.data(file).find('.progress .bar').width(progress+'%');
            },

            afterAll: function(){
                $('#preview .progress').delay(300).fadeIn(300).hide(300, function(){
                    $(this).remove();
                });
                $('#preview .uploaded').delay(300).fadeIn(300).hide(300, function(){
                    $(this).remove();
                });
                $('#preview .wimg').delay(1200).show(1200,function(){
                    $(this).removeClass('done placeholder');
                });
                initInsertBtns();
                initImages();
            },
            rename : function(name){
                ext = name.substr(name.lastIndexOf('.'),name.lenght);
                name = name.substr(0, name.lastIndexOf('.'));
                var pattern_accent = new Array("é", "è", "ê", "ë", "ç", "à", "â", "ä", "î", "ï", "ù", "ô", "ó", "ö");
                var pattern_replace_accent = new Array("e", "e", "e", "e", "c", "a", "a", "a", "i", "i", "u", "o", "o", "o");
                name = preg_replace (pattern_accent, pattern_replace_accent,name);

                name = name.replace(/\s+/gi, '-');
                name = name.replace(/[^a-zA-Z0-9\-]/gi, '');
                return name+ext;
            }
        });
    }

        /* Title edition */
        function initMenu(){
            /**
            * Click on delete gallery btn
            */
           $('#gallerieslist .dd-content .trash').unbind('click').on('click',function(){
               id_gallery = $(this).parents('li').data('id-gallery');
               bootbox.confirm(Joomla.JText._('COM_DROPPICS_JS_WANT_DELETE_GALLERY','Do you really want to delete "')+$(this).parent().find('.title').text()+'"?', function(result) {
                   if(result===true){
                       $.ajax({
                           url     :   "index.php?option=com_droppics&task=categories.delete&id_gallery="+id_gallery,
                           type    :   'POST',
                           data    :   $('#galleryToken').attr('name') + '=1'
                       }).done(function(data){
                           result = jQuery.parseJSON(data);
                           if(result.response===true){
                               $('#mygalleries #gallerieslist li[data-id-gallery='+id_gallery+']').remove();
                               $('#preview').contents().remove();
                               first = $('#mygalleries #gallerieslist li').first();
                               if(first.length>0){
                                   first.click();
                               }else{
                                   $('#insertgallery').hide();
                               }
                           }else{
                               bootbox.alert(result.response);
                           }
                           updatepreview();
                       });
                   }
               });
               return false;
           });

            /* Set the active gallery on menu click */
            $('#gallerieslist .dd-content').unbind('click').click(function(e){
                id_gallery = $(this).parent().data('id-gallery');
                $('input[name=id_gallery]').val(id_gallery);
                updatepreview(id_gallery);
                // set cookie in com_droppics last view
                setCookie('ck_id_gallery_last_view',id_gallery,1);
                $('#gallerieslist li').removeClass('active');
                $(this).parent().addClass('active');
                return false;
            });

            $('#gallerieslist .dd-content a.edit').unbind().click(function(e){
                e.stopPropagation();
                $this = this;
                link = $(this).parent().find('a span.title');
                oldTitle = link.text();
                $(link).attr('contentEditable',true);
                $(link).addClass('editable');
                $(link).selectText();

                $('#gallerieslist a span.editable').bind('click.mm',hstop);  //let's click on the editable object
                $(link).bind('keypress.mm',hpress); //let's press enter to validate new title'
                $('*').not($(link)).bind('click.mm',houtside);

                function unbindall(){
                    $('#gallerieslist a span').unbind('click.mm',hstop);  //let's click on the editable object
                    $(link).unbind('keypress.mm',hpress); //let's press enter to validate new title'
                    $('*').not($(link)).unbind('click.mm',houtside);
                }

                //Validation       
                function hstop(event){
                    event.stopPropagation();
                    return false;
                }

                //Press enter
                function hpress(e){
                    if ( e.which == 13 ) {
                        e.preventDefault();
                        unbindall();
                        updateTitle($(link).text());
                        $(link).removeAttr('contentEditable');
                        $(link).removeClass('editable');
                    }
                }

                //click outside
                function houtside(e){
                    unbindall();
                    updateTitle($(link).text());
                    $(link).removeAttr('contentEditable');
                    $(link).removeClass('editable');
                }


                function updateTitle(title){
                    id_gallery = $(link).parents('li').data('id-gallery');
                    if(title!==''){
                        $.ajax({
                            url     :   "index.php?option=com_droppics&task=category.setTitle&id_gallery="+id_gallery+'&title='+title,
                            type    :   "POST"
                        }).done(function(data){
                            result = jQuery.parseJSON(data);
                            if(result===true){
                                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                                return true;
                            }
                            $(link).text(oldTitle);
                            return false;
                        });
                    }else{
                        $(link).text(oldTitle);
                        return false;
                    }

                }
            });
        }

        function loading(e){
            $(e).addClass('dploadingcontainer');
            $(e).append('<div class="dploading"></div>');
        }
        function rloading(e){
            $(e).removeClass('dploadingcontainer');
            $(e).find('div.dploading').remove();
        }

         /** Initialise simple image edition **/
        var c = null, filters = {}, rendering = false, crop_api = null, crop = {}, editedImage = {};       

        $('#backtogallery').click(function(e){
            e.preventDefault();
            $('#picture_wrapper').fadeOut(function(){
                $('#main_wrapper').fadeIn(function(){Droppics.editionMode = false;});
                $('#camanimg').replaceWith('<img id="camanimg" />');
                resetFilters();
            }); 
        });

        $('#picture_wrapper #picture_params .filter input').on('change',
            $.throttle(300,function(){
                if(rendering === true){
                    return;
                }
                var functionValue, functionName;
                rendering = true;
                functionName = $(this).data('filter');
                functionValue = $(this).val();
                filters[functionName] = functionValue;

                $(this).siblings('.filterValue').html(functionValue);

                
                c = Caman('#camanimg', function () {
                    $('#picture_wrapper #picture_params #presetFilters option:first').attr('selected','selected');
                    this.revert(false);
                    for(key in filters){
                        this[key](parseFloat(filters[key]));
                    }
                    this.render();
                });

            })
        );
        
        $('#picture_wrapper #picture_params #presetFilters').change(function(e){
            e.preventDefault();
            resetFilters();
            that = $(this);
            Caman('#camanimg', function () {
                this.revert(false);
                    this[that.val()]();
                    this.render();
            });
        });
        
        
        function resetFilters(){
            $('#picture_wrapper #picture_params .filter input').val(0);
            $('#picture_wrapper #picture_params .filter .filterValue').html('0');
            filters={};
        }

        Caman.Event.listen(c,'renderFinished',function(){
            if(crop_api===null){
                initCrop();
            }
            rendering = false;
        });

        initCropFn = function(){
            $('#camanimg').Jcrop({
                onSelect : function(c){ crop = c; },
                onChange : function(c){$('#currentSelection').html(c.w+' x '+c.h);}
            },function(){
                crop_api = this;
            });
        };

        initCrop = function(){
                if(crop_api!==null){
                    $('#camanimg').attr('style', '');
                    if($('#camanimg').parent().is('.jcrop-holder')){
                        $('#camanimg').unwrap();
                        $('#picture_edit *:not(#camanimg)').remove();
                        crop_api.release();
                        crop_api.destroy();
                    }
                }
                
                $('#btnCrop').unbind('click').click(function(){
                    Caman('#camanimg', function () {
                        if(crop.h>0 && crop.w>0){
                            this.crop(crop.w,crop.h,crop.x,crop.y);
                            crop_api.release();
                            crop_api.disable();
                            crop = {};
                            $('#camanimg').css({'width':'','height':''});
                            this.render();
                            $('#camanimg').attr('style', '');
                            $('#camanimg').unwrap();
                            $('#picture_edit *:not(#camanimg)').remove();
                            crop_api.destroy();
                            crop_api = null;
                        }
                    });
                });
                
                initCropFn();
        };
        
        
        $('#btnMirror').click(function(){
            Caman('#camanimg', function () {
                this.mirror();
                this.render();
            });
        });
        
        $('#btnVMirror').click(function(){
            Caman('#camanimg', function () {
                this.mirror(true);
                this.render();
            });
        });
        
        $('#btnRotate').click(function(){
            Caman('#camanimg', function () {
                $('#picture_wrapper #picture_params .filter input').trigger('change');
                ownCropDestroy();
                this.revert(false);
                this.rotate(90);
                this.render();
            });
        });
        
        $('#imageLoad').click(function(e){
            e.preventDefault();
            ownCropDestroy();
            resetFilters();            
            tst = new Date().getTime();
            $('#camanimg').replaceWith('<img id="camanimg" src="'+editedImage.picture+'?'+tst+'" />');
            loadDims();
            Caman('#camanimg', function () {
                this.render();
            });
        });
                
        $('#btnsave').click(function(){
            Caman("#camanimg", function () {
                this.render(function () {
                    if(editedImage.ext.toLowerCase()==='jpg' || editedImage.ext.toLowerCase()==='jpeg'){
                        var image = this.toBase64('jpeg');
                    }else if(editedImage.ext.toLowerCase()==='png'){
                        var image = this.toBase64('png');
                    }else if (editedImage.ext.toLowerCase()==='gif'){
                        var image = this.toBase64('gif');
                    }else{
                        return false;
                    }

                    datas = {
                        image : image, 
                        ext : editedImage.ext,
                        id_picture : editedImage.id_picture,
                        type : editedImage.type                        
                    };
                    
                    if(editedImage.type==='custom'){
                        datas.id_custom = editedImage.id_custom;
                    }
                    
                    $.ajax({
                      url     :   'index.php?option=com_droppics&task=files.replace',
                      type    :   'POST' ,
                      data    :   datas,
                      cache: false
                     }).done(function(data){
                          result = jQuery.parseJSON(data);
                          if(result.response===true){
                              //refresh the thumbnail
                              if(editedImage.type==='thumbnail'){
                                  $.ajax({
                                      url   :   editedImage.picture,
                                      cache :   false
                                  }).done(function(){
                                        src = $('.selimg.selected img').attr('src');
                                        $('.selimg.selected img').removeAttr('src').attr('src',src).trigger('change');
                                  });
                              }
                              $('#imgp_source input[checked="checked"]').trigger('change');

                              $('#picture_wrapper').fadeOut(function(){
                                $('#main_wrapper').fadeIn();
                                $('#camanimg').replaceWith('<img id="camanimg" />');
                                resetFilters();
                                $.gritter.add({text:result.datas.message});
                                Droppics.editionMode = false;
                            });
                          }else{
                              bootbox.alert(result.response);
                          }
                      });
                    });
              });
        });
        
        $('#saveType').change(function(){
           if($(this).val()==='custom'){
               $('#saveFilename').show();
           }else{
               $('#saveFilename').hide();
           }
        });

    /************** JCrop extend **************/
    ownCropDestroy = function(){
        
            if(crop_api!==null){
                crop_api.release();
                crop_api.disable();
            }
            crop = {};
            $('#camanimg').css({'width':'','height':''});
            $('#camanimg').attr('style', '');
            if($('#camanimg').parent().is('.jcrop-holder')){
                $('#camanimg').unwrap();
            }
            $('#picture_edit').children(':not(#camanimg)').remove();
            if(crop_api!==null){
                crop_api.destroy();
            }
            crop_api = null;
    };

    /*********** Caman js plugins *************/
    copyAttributes = function(from, to, opts) {
        var attr, _i, _len, _ref, _ref1, _results;
        if (opts == null) {
            opts = {};
        }
        _ref = from.attributes;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            attr = _ref[_i];
            if ((opts.except != null) && (_ref1 = attr.nodeName, __indexOf.call(opts.except, _ref1) >= 0)) {
                continue;
            }
            _results.push(to.setAttribute(attr.nodeName, attr.nodeValue));
        }
        return _results;
    };
    Caman.Plugin.register("mirror", function(vertical) {
        var canvas, ctx;
        // Support NodeJS by checking for exports object
        if (typeof exports !== "undefined" && exports !== null) {
            canvas = new Canvas(this.dimensions.width, this.dimensions.height);
        } else {
            canvas = document.createElement('canvas');
            copyAttributes(this.canvas, canvas);
            canvas.width = this.dimensions.width;
            canvas.height = this.dimensions.height;
        }
        ctx = canvas.getContext('2d');

        if (vertical == true) {
            ctx.translate(0, canvas.height);
            ctx.scale(1, -1)
        } else {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1)
        }

        ctx.drawImage(this.canvas, 0, 0);
        return this.replaceCanvas(canvas);
    });

    Caman.Filter.register("mirror", function() {
        this.processPlugin("mirror", arguments);
    });


    Caman.Plugin.register("rotate", function(degrees) {
        var angle, canvas, ctx, height, to_radians, width, x, y;
        angle = degrees % 360;
        if (angle === 0) {
            return this.dimensions = {
                width: this.canvas.width,
                height: this.canvas.height
            };
        }
        to_radians = Math.PI / 180;
        if (typeof exports !== "undefined" && exports !== null) {
            canvas = new Canvas();
        } else {
            canvas = document.createElement('canvas');
            copyAttributes(this.canvas, canvas);
        }
        if (angle === 90 || angle === -270 || angle === 270 || angle === -90) {
            width = this.canvas.height;
            height = this.canvas.width;
            x = width / 2;
            y = height / 2;
        } else if (angle === 180) {
            width = this.canvas.width;
            height = this.canvas.height;
            x = width / 2;
            y = height / 2;
        } else {
            width = Math.sqrt(Math.pow(this.originalWidth, 2) + Math.pow(this.originalHeight, 2));
            height = width;
            x = this.canvas.height / 2;
            y = this.canvas.width / 2;
        }
        canvas.width = width;
        canvas.height = height;
        ctx = canvas.getContext('2d');
        ctx.save();
        ctx.translate(x, y);
        ctx.rotate(angle * to_radians);
        ctx.drawImage(this.canvas, -this.canvas.width / 2, -this.canvas.height / 2, this.canvas.width, this.canvas.height);
        pixelData = ctx.getImageData(0, 0, this.width, this.height).data;
        ctx.restore();

        c = this.replaceCanvas(canvas);

        this.imageData.width = this.originalWidth = this.preScaledWidth = this.width;
        this.imageData.height = this.originalHeight = this.preScaledHeight = this.height;

        jQuery.extend(this.initializedPixelData, this.pixelData);
        jQuery.extend(this.originalPixelData, this.pixelData);

        this.imageData.data = this.PixelData = pixelData;

        return c;
    });

    Caman.Filter.register("rotate", function() {
        return this.processPlugin("rotate", Array.prototype.slice.call(arguments, 0));
    });
    
        /** Initialise single image insertion **/
        function imgp_bind_event(){
            $('#imagealign button').on('click',function(){
                $('#imagealign button').removeClass('active');
                $(this).addClass('active');
                        
                var value = $(this).data('align');
                $('input[name="jform[params][imagealign]"]').val(value);
            });
            
            $('#imgp_click').on('change',function(){
                $('.click_content_block').hide();
                $('#imgp_click_target_wrap').hide();
                switch($(this).val()){
                    case 'article':
                        $('#click_content_article').show();
                        $('#imgp_click_target_wrap').show();
                        break;
                    case 'menuitem':
                        $('#click_content_menuitem').show();
                        $('#imgp_click_target_wrap').show();
                        break;
                    case 'custom':
                        $('#click_content_custom').show();
                        $('#imgp_click_target_wrap').show();
                        break;
                }
            });
            
            $('.imgp_source').on('click',function(){
                var value = $(this).val();
                $('input[name="jform[params][imgp_source]"]').val(value);                
            });
            
            $('#imgp_source input[value="custom"]').on('change',function(){
                origName = $("#jform_file").val(); 
                lastCustomR = $('#imgp_source input[value^="custom_"]').last();
                if(lastCustomR.length>0) {
                    custom_id =  parseInt( $(lastCustomR).attr("value").replace("custom_","") ) + 1 ;              
                }else {
                    custom_id = 1;
                }               
                $("#customFilename").val(origName+"-"+ custom_id );
                
                $('#newCustomSize').show();
                $('#changeCustomFile').hide();
                $('#insertimage').addClass('disabled');
            });

            $('#imgp_source input:not([value="custom"])').on('change',function(){
                $('#newCustomSize').hide();
                $('#insertimage').removeClass('disabled');
                id_gallery = $('input[name=id_gallery]').val();
                tst = new Date().getTime();
                switch ($('#imgp_source input:checked').val()){
                    case 'thumbnail':                                        
                        $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/thumbnails/'+$('.selimg.selected img').data('file')+'?'+tst);
                        break;
                    case 'original':
                        $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/'+$('.selimg.selected img').data('file')+'?'+tst);
                        break;
                    default:
                        custom = $('#imgp_source input:checked').val();
                        custom = custom.replace('custom_','');
                        infos = $('.selimg.selected img').data('customs');
                        result = $.grep(infos, function(e){ 
                            return e.id == custom; 
                        });
                      
                        $('#singleimage').attr('src',Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/custom/'+result[0].file+'?'+tst);
                        
                        $('#newCustomFilename').val( result[0].file.substr(0,result[0].file.lastIndexOf('.') ) );
                        $('#changeCustomFile').show();
                        break;
                }
                               
            });
                       
            /* Create custom size button */
            $('#applyCustomFile').click(function(e){
                e.preventDefault();
                currentPicture = $('#preview .selimg.selected img');
                custom = $('#imgp_source input:checked').val();
                custom = custom.replace('custom_','');
                $.ajax({
                    url     :   "index.php?option=com_droppics&task=files.renameCustomFile",
                    type    :   "POST",
                    data    :   {
                        id_gallery  :    $('input[name=id_gallery]').val(),
                        id_custom       :  custom,                         
                        filename    :   $('#newCustomFilename').val()
                    }
                }).done(function(data){
                    result = jQuery.parseJSON(data);                
                    if(result.response===true){
                        customs = [];
                        infos = $(currentPicture).data('customs');
                        infos.each(function(el){
                            if( el.id == custom) {
                                el.file = result.datas.file;
                            }
                            customs.push(el); 
                        });    
                       
                        $(currentPicture).data('customs',customs);
                        $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                    }else{
                        bootbox.alert(result.response);
                    }
                });
            });
            
            /* Create custom size button */
            $('#applyNewCustomSize').click(function(){
                currentPicture = $('#preview .selimg.selected img');
                $.ajax({
                    url     :   "index.php?option=com_droppics&task=files.customResize",
                    type    :   "POST",
                    data    :   {
                        id_picture  :   currentPicture.data('id-picture'),
                        width       :   $('#customWidth').val(),  
                        height      :   $('#customHeight').val(),
                        filename    :   $('#customFilename').val()
                    }
                }).done(function(data){
                    result = jQuery.parseJSON(data);                
                    if(result.response===true){
                        cloned = $('#imgp_source .template').clone(true).insertBefore('#imgp_source .template').removeClass('template').show();
                        cloned.find('span').html(result.datas.file+' ('+result.datas.width+'x'+result.datas.height+')');                    
                        customs = $(currentPicture).data('customs');
                        customs.push(result.datas); 
                        $(currentPicture).data('customs',customs);
                        cloned.find('input').attr('checked','checked').val('custom_'+result.datas.id).trigger('change');
                    }else{
                        bootbox.alert(result.response);
                    }
                });
            });
             
            $('.editImage').click(function(e){  
                e.preventDefault();
                Droppics.editionMode = true;
                $(this).siblings('input').attr('checked','checked').trigger('change');
               
                $('#main_wrapper').fadeOut(function(){
                    $('#picture_wrapper').fadeIn(function(){
                        id_gallery = $('input[name=id_gallery]').val();
                        src= Droppics.baseurl+'/'+drp_root_folder+'/'+id_gallery+'/';
                        Droppics.selection.picture = $('#preview .selimg.selected img').data('id-picture');
                        Droppics.selection.gallery = id_gallery = $('input[name=id_gallery]').val();
                        editedImage.ext = $('#preview .selimg.selected img').data('file').split('.').pop();
                        editedImage.id_picture = Droppics.selection.picture;            
                        switch ($('#imgp_source input:checked').val()){
                            case 'thumbnail':                                        
                                src += 'thumbnails/';
                                src += $('.selimg.selected img').data('file');
                                editedImage.type = 'thumbnail';
                                break;
                            case 'original':
                                src += $('.selimg.selected img').data('file');
                                editedImage.type = 'original';
                                break;
                            default:
                                custom = $('#imgp_source input:checked').val();
                                custom = custom.replace('custom_','');
                                infos = $('.selimg.selected img').data('customs');
                                result = $.grep(infos, function(e){ 
                                    return e.id == custom; 
                                });
                                src += 'custom/'+result[0].file;
                                editedImage.type = 'custom';
                                editedImage.id_custom = result[0].id;
                                break;
                        }
                        editedImage.picture = src;
                        $('#camanimg').attr('src',editedImage.picture);
                       
                        ownCropDestroy();
                        c = Caman('#camanimg', function () {
                            this.render();
                        });
                    });
                });
               
                //load dims
                (loadDims = function(){
                    var newImg = new Image();
                    newImg.src = editedImage.picture;
                    newImg.onload = function() {
                        $('#resizeWidth').val(newImg.width);
                        $('#resizeHeight').val(newImg.height);
                        $('#originalSize').html(newImg.width+' x '+newImg.height);
                        $('#resizeWidth,#resizeHeight').bind('input',function(){
                            if(this.id==='resizeWidth'){
                                $('#resizeHeight').val(parseInt(parseInt(newImg.height,10)*parseInt($('#resizeWidth').val(),10)/parseInt(newImg.width,10),10));
                            }else{
                                $('#resizeWidth').val(parseInt(parseInt(newImg.width,10)*parseInt($('#resizeHeight').val(),10)/parseInt(newImg.height,10),10));
                            }
                        });
                    };
                })();

                $('#applyresize').click(function(){
                    Caman('#camanimg', function () {
                        ownCropDestroy();
                        this.resize({
                            width: $('#resizeWidth').val(),
                            height: $('#resizeHeight').val()
                        });
                        this.render();
                    });
                });

                initCrop();
            });
        }
       
        //imgp_bind_event();
    //override Joomla.submitbutton
    if(typeof Joomla !="undefined" ) {
        var oldJoomlaSubmition = Joomla.submitbutton;
        var selectedFiles = [];
        var lastAction = '';
        var sourceCat = 0;
        Joomla.submitbutton = function ($task) {
            if ($task == 'files.copyfile' || $task == 'files.movefile') {

                if ($('#preview .selimg.selected').length == 0) {
                    bootbox.alert(Joomla.JText._('COM_DROPPICS_JS_NO_FILES_IMAGE_SELETED', 'Please select file(s)'));
                    return;
                }
                lastAction = $task;
                sourceCat = $('#gallerieslist li.active').data('id-gallery');
                selectedFiles = [];
                $('#preview .selimg.selected .img').each(function (index) {
                    selectedFiles.push($(this).data('id-picture'));
                })
                if (lastAction == 'files.copyfile') {
                    //do nothing
                } else {
                    $('#preview .selimg.selected').css('opacity', '0.7');
                }

            }
            else if ($task == 'files.paste') {
                if (selectedFiles.length == 0) {
                    bootbox.alert(Joomla.JText._('COM_DROPFILES_JS_NO_FILES_COPIED_CUT', 'There is no copied/cut files yet'));
                }
                cat_gallery = $('#gallerieslist li.active').data('id-gallery');
                if (cat_gallery != sourceCat) {
                    countFiles = selectedFiles.length;
                    iFile = 0;
                    while (selectedFiles.length > 0) {
                        id_file = selectedFiles.pop();
                        $.ajax({
                            url: "index.php?option=com_droppics&task=" + lastAction + "&id_gallery=" + cat_gallery + '&active_gallery=' + sourceCat + '&id_file=' + id_file,
                            type: "POST"
                        }).done(function (data) {
                            iFile++;
                            if (iFile == countFiles) {
                                if (lastAction == 'files.copyfile') {
                                    $.gritter.add({text: Joomla.JText._('COM_DROPFILES_JS_FILES_COPIED', 'File(s) copied with success!')});
                                } else {
                                    $.gritter.add({text: Joomla.JText._('COM_DROPFILES_JS_FILES_MOVED', 'File(s) moved with success!')});
                                }

                                updatepreview(cat_gallery);
                            }
                        });
                    }
                }
            } else if($task =='imagesinfo'){
                window.location.href = 'index.php?option=com_droppics&view=imagesinfo';
            } else if($task =='com.droppics'){
                window.location.href = 'index.php?option=com_droppics';
            }
            else {
                oldJoomlaSubmition($task);
            }

        }
    }
    // drop file image
    var ctrlDown = false;
    $(window).on("keydown", function(event) {
        if (event.which === 17) {
            ctrlDown = true;
        }
    }).on("keyup", function(event) {
        ctrlDown = false;
    });

    catDroppable = function() {
        $("#gallerieslist li.dd-item > .dd-content").droppable({
            accept: '.wimg',
            hoverClass: "dd-content-hover",
            tolerance: "pointer",
            drop: function( event, ui ) {
                $(this).addClass( "ui-state-highlight" );
                gall_target = $(event.target).parent().data("id-gallery");
                current_gall = $("#gallerieslist .dd-item.active").data('id-gallery');
                if (current_gall != gall_target) {
                    count = $('#preview').find('.selimg.selected').length;
                    countselect = 0;
                    $('#preview').find('.selimg.selected').each(function(){
                        filenameslect = $(this).find('.img').data('file');
                        if(filenameslect === filename){
                            countselect++;
                        }
                    });
                    var id_file = [];
                    if(count>0 && countselect > 0) { //multiple file
                        iFile = 0;
                        $('#preview').find('.selimg.selected').each(function(){
                            iFile++;
                            id_file.push($(this).find('.img').data("id-picture"));
                        })
                            if (ctrlDown) { //copy file
                                $.ajax({
                                    url: "index.php?option=com_droppics&task=files.copyfile&id_gallery=" + gall_target + '&active_gallery='+current_gall+'&id_file=' + id_file,
                                    type: "POST"
                                }).done(function (data) {
                                    if(iFile== count) {
                                        $.gritter.add({text:Joomla.JText._('COM_DROPFILES_JS_FILES_COPIED', 'Files copied with success!')});
                                    }
                                });
                            }else {
                                $.ajax({
                                    url: "index.php?option=com_droppics&task=files.movefile&id_gallery=" + gall_target + '&active_gallery='+current_gall+'&id_file=' + id_file,
                                    type: "POST",
                                    dataType: "json",
                                }).done(function (result) {
                                    if(typeof result.datas.id_file  != "undefined") {
                                        $('img[data-id-picture="'+ result.datas.id_file +'"]').remove();
                                    }
                                    if(iFile== count) {
                                        $.gritter.add({text:Joomla.JText._('COM_DROPFILES_JS_FILES_MOVED', 'Files moved with success!')});
                                    }
                                    updatepreview(current_gall);
                                });
                            }
                    }
                    else {  //single file
                        id_file = $(ui.draggable).find('.img').data("id-picture");
                        if (ctrlDown) { //copy file
                            $.ajax({
                                url: "index.php?option=com_droppics&task=files.copyfile&id_gallery=" + gall_target + '&active_gallery='+current_gall+'&id_file=' + id_file,
                                type: "POST"
                            }).done(function (data) {
                                $.gritter.add({text:Joomla.JText._('COM_DROPFILES_JS_FILE_COPIED', 'File copied with success!')});
                            });
                        }else {
                            $.ajax({
                                url: "index.php?option=com_droppics&task=files.movefile&id_gallery=" + gall_target + '&active_gallery='+current_gall+'&id_file=' + id_file,
                                type: "POST"
                            }).done(function (data) {
                                $('img[data-id-picture="'+id_file+'"]').remove();
                                updatepreview(current_gall);
                                $.gritter.add({text:Joomla.JText._('COM_DROPFILES_JS_FILE_MOVED', 'File moved with success!')});
                            });
                        }
                    }
                }
                updatepreview(current_gall);
                $(this).removeClass( "ui-state-highlight" );
            }
        });
    }
    catDroppable();
});

/**
* Insert the current gallery into a content editor
*/
function insertGallery(){
    id_gallery = jQuery('input[name=id_gallery]').val();
    //set cookie value id_gallery then insert
    setCookie('ck_id_gallery',id_gallery,30);
    dir = decodeURIComponent(getUrlVar('path'));
    code = '<img src="'+dir+'/components/com_droppics/assets/images/t.gif"'+
                'data-droppicsgallery="'+id_gallery+'"'+
                'data-droppicsversion="'+Droppics.version+'"'+
                'style="background: url('+dir+'/components/com_droppics/assets/images/gallery.png) no-repeat scroll center center #D6D6D6;'+
                'border: 2px dashed #888888;'+
                'height: 200px;'+
                'border-radius: 10px;'+
                'width: 99%;" data-gallery="'+id_gallery+'" />';
    return code;
}

//From http://jquery-howto.blogspot.fr/2009/09/get-url-parameters-values-with-jquery.html
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function getUrlVar(v){
    if(typeof(getUrlVars()[v])!==undefined){
        return getUrlVars()[v];
    }
    return null;
}

function preg_replace (array_pattern, array_pattern_replace, my_string) {var new_string = String (my_string);for (i=0; i<array_pattern.length; i++) {var reg_exp= RegExp(array_pattern[i], "gi");var val_to_replace = array_pattern_replace[i];new_string = new_string.replace (reg_exp, val_to_replace);}return new_string;}

//https://gist.github.com/ncr/399624
jQuery.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
  return this.each(function(){
    var clicks = 0, self = this;
    jQuery(this).click(function(event){
      clicks++;
      if (clicks == 1) {
        setTimeout(function(){
          if(clicks == 1) {
            single_click_callback.call(self, event);
          } else {
            double_click_callback.call(self, event);
          }
          clicks = 0;
        }, timeout || 300);
      }
    });
  });
}

//From http://stackoverflow.com/questions/1740700/how-to-get-hex-color-value-rather-than-rgb-value
function rgb2hex(rgb) {    
    if(typeof(rgb)==='undefined' ||  rgb==='none' || rgb===null || rgb==='' || rgb.substring(0,1)==='#'){
        return '#CCCCCC';
    }    
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);    
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

//From http://stackoverflow.com/questions/19663427/jquery-set-css-background-opacity
function hex2rgba(hex, opacity){
    //extract the two hexadecimal digits for each color
    var patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
    var matches = patt.exec(hex);

    //convert them to decimal
    var r = parseInt(matches[1], 16);
    var g = parseInt(matches[2], 16);
    var b = parseInt(matches[3], 16);

    //create rgba string
    var rgba = "rgba(" + r + "," + g + "," + b + "," + opacity + ")";

    //return rgba colour
    return rgba;
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}



