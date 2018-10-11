/**
 * Created by Administrator on 2/23/2017.
 */

jQuery(document).ready(function($) {
    var pic_Data  = {
        mbulk_copy: '',
        gallery_id: '',
        pics: []
    };

    var orderby = $('#filter_order_by').val().split(".");
    $('a.js-stools-column-order').each( function (e) {
        spans = $(this).find("span");
        if (orderby[0] != $(this).data('name')) {
            spans.removeClass("icon-arrow-down-3");
            spans.removeClass("icon-arrow-up-3");
        }else{
            if(orderby[1]=='ASC'){
                spans.addClass("icon-arrow-up-3").removeClass("icon-arrow-down-3");
            }else{
                spans.addClass("icon-arrow-down-3").removeClass("icon-arrow-up-3");
            }
        }
    });

    $('textarea.metaseo-img-name').on('change',function () {
        pic_Data.pics = [];
        pic_Data.gallery_id = $(this).data('id-gallery');
        pic_Data.pics.push({'pic_id':$(this).data('post-id'),'pic_name': this.value+$(this).data('extension') });
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.renamePicture',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
            }else{
                bootbox.alert(data.response);
            }
        });
    });

    $('textarea.drp-img-alt').on('change',function () {
        pic_Data.pics = [];
        pic_Data.gallery_id = $(this).data('id-gallery');
        pic_Data.pics.push({'pic_id':$(this).data('post-id'),'pic_name': this.value});
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.upAltPictures',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
            }else{
                bootbox.alert(data.response);
            }
        });
    });

    $('textarea.drp-img-title').on('change',function () {
        pic_Data.pics = [];
        pic_Data.gallery_id = $(this).data('id-gallery');
        pic_Data.pics.push({'pic_id':$(this).data('post-id'),'pic_name': this.value});
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.upTitlePictures',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
            }else{
                bootbox.alert(data.response);
            }
        });
    });

    $('textarea.drp-img-caption').on('change',function () {
        pic_Data.pics = [];
        pic_Data.gallery_id = $(this).data('id-gallery');
        pic_Data.pics.push({'pic_id':$(this).data('post-id'),'pic_caption': this.value});
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.upCaptionPictures',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
            }else{
                bootbox.alert(data.response);
            }
        });
    });




    $('.image_do_copy_alt').unbind('click').click(function(e) {
        pic_Data.mbulk_copy = $('#filter_image_mbulk_copy').val();
        pic_Data.gallery_id = $('#filter_gallery_id').val();
        pic_Data.pics = [];
        if($('#filter_image_mbulk_copy').val() == 'all'){

        }else {
            check_selected = 0;
            $('input:checkbox[name=cb-selected]:checked').each(function()
            {
                pic_id = $(this).val();
                pic_name = $('#img-name-'+pic_id).val().trim();
                pic_Data.pics.push({'pic_id':pic_id,'pic_name': pic_name });
                check_selected = check_selected + 1;
            });
            if(!check_selected){
                return;
            }
        }
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.upAltPictures',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                setTimeout(function(){ window.location.reload() },1500);
            }else{
                bootbox.alert(data.response);
            }
        });
    });

    $('.image_do_copy_title').unbind('click').click(function(e) {
        pic_Data.mbulk_copy = $('#filter_image_mbulk_copy').val();
        pic_Data.gallery_id = $('#filter_gallery_id').val();
        pic_Data.pics = [];
        if($('#filter_image_mbulk_copy').val() == 'all'){
            //todo
        }else {
            check_selected = 0;
            $('input:checkbox[name=cb-selected]:checked').each(function()
            {
                pic_id = $(this).val();
                pic_name = $('#img-name-'+pic_id).val().trim();
                pic_Data.pics.push({'pic_id':pic_id,'pic_name': pic_name });
                check_selected = check_selected + 1;
            });
            if(!check_selected){
                return;
            }
        }
        $.ajax({
            url     :   'index.php?option=com_droppics&task=files.upTitlePictures',
            type    :   'POST' ,
            data: { pic_Data: pic_Data },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            if(data.response){
                $.gritter.add({text:Joomla.JText._('COM_DROPPICS_JS_SAVED', 'Saved')});
                setTimeout(function(){ window.location.reload() },1500);
            }else{
                bootbox.alert(data.response);
            }
        });
    });



    $("#cb-select-all-1").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
    $("#cb-select-all-2").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    $('select#filter_gallery_id').on('change', function() {
        val_selected = this.value;
        $('select#filter_gallery_id').each(function() {
            $(this).val(val_selected);
        });
    });

    $('select#filter_image_mbulk_copy').on('change', function() {
        val_selected = this.value;
        $('select#filter_image_mbulk_copy').each(function() {
            $(this).val(val_selected);
        });
    });


    $('a.js-stools-column-order').click(function() {
        data_name_slect = $(this).data('name');
        $('a.js-stools-column-order').each( function (e) {
            spans = $(this).find("span");
            if (data_name_slect != $(this).data('name')) {
                spans.removeClass("icon-arrow-down-3");
                spans.removeClass("icon-arrow-up-3");
            }else{
                if(spans.hasClass("icon-arrow-down-3")){
                    spans.addClass("icon-arrow-up-3").removeClass("icon-arrow-down-3");
                    $('#filter_order_by').val(data_name_slect+'.ASC');
                }else{
                    spans.addClass("icon-arrow-down-3").removeClass("icon-arrow-up-3");
                    $('#filter_order_by').val(data_name_slect+'.DESC')
                }
            }
        });
        Joomla.submitform();
    });

});