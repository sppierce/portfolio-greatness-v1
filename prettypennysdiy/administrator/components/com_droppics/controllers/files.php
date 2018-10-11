<?php
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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class DroppicsControllerFiles extends JControllerForm
{
    protected $allowed_ext = array('jpg','jpeg','png','gif');

    public function import(){
        $user = JFactory::getUser();
        if(!$user->authorise('core.admin')){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_PERMISSION'));
        }
        $id_gallery = JFactory::getApplication()->input->getInt('id_gallery', 0);
        if($id_gallery<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_CATEGORY'));
        }
        $this->canEdit($id_gallery);

        $params = JComponentHelper::getParams('com_droppics');

        //todo: créer un répertoire spécial pour les categories
        $picture_dir = droppicsBase::getPicturePath($id_gallery);
        if(!file_exists($picture_dir)){
            JFolder::create($picture_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($picture_dir.'index.html', $data);
        }
        $full_dir = droppicsBase::getFullPicturePath($id_gallery);
        if(!file_exists($full_dir)){
            JFolder::create($full_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($full_dir.'index.html', $data);
        }
        $thumbnail_dir = droppicsBase::getThumbnailPath($id_gallery);
        if(!file_exists($thumbnail_dir)){
            JFolder::create($thumbnail_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($thumbnail_dir.'index.html', $data);
        }

        $model = $this->getModel();
        $files = JFactory::getApplication()->input->get('files',null,'array');
        if(!empty($files)) {
            $count = 0;
            foreach ($files as $file) {
               $fname = basename($file);
               $newname = droppicsBase::makeSafeFilename( $fname);
               $file = JPATH_ROOT.DIRECTORY_SEPARATOR. 'images'. DIRECTORY_SEPARATOR.$file;
               //$pic = $_FILES['pic'];
                if(!in_array(strtolower(JFile::getExt($file)),$this->allowed_ext)){
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
                }

                if(file_exists($full_dir.$newname)){
                    $newname = uniqid().'.'.strtolower(JFile::getExt( $file));
                }

                 if(!JFile::copy($file, $full_dir.$newname)){
                        $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_MOVE_FILE')  );
                 }

                $imageInfo = getimagesize($full_dir.$newname);
                if($imageInfo===false){
                    JFile::delete($full_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'));
                }
                try{
                    $this->generatePicture($newname, $imageInfo,$id_gallery);
                }catch (Exception $e){
                    JFile::delete($full_dir.$newname);
                    JFile::delete($thumbnail_dir.$newname);
                    JFile::delete($picture_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'),$e);
                }

                //Insert new image into databse                
                $id_picture = $model->addPicture($newname,$id_gallery,JFile::stripExt($fname));
                if(!$id_picture){
                    JFile::delete($picture_dir.$newname);
                    JFile::delete($full_dir.$newname);
                    JFile::delete($thumbnail_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
                }
                $this->generateCustomSize($id_picture);
               // $this->exit_status(true,array('id_picture'=>$id_picture,'name'=>$newname,'picture'=>droppicsBase::getPictureURL($id_gallery).$newname,'thumbnail'=>droppicsBase::getPictureURL($id_gallery).'thumbnails/'.$newname));

                $count++;
            }
            $this->exit_status(true,array('nb'=>$count));
        }
        $this->exit_status(JText::_('Error while importing')); //todo : translate
    }

    public function upload(){
        $id_gallery = JFactory::getApplication()->input->getInt('id_gallery', 0);
        // check theme bxslider add default option image discription
        $check_theme_bxslider = JFactory::getApplication()->input->getInt('check_theme_bxslider', 0);
        $params = JComponentHelper::getParams('com_droppics');
        $params_theme_bxslider = array('');
        if ($check_theme_bxslider) {
            $bxslider_image_bgcolor = JFactory::getApplication()->input->getString('bxs_images_bgcolor', '#2e2e2e');
            $bxslider_image_transparency = JFactory::getApplication()->input->getString('bxs_images_transparency', '90');
            $bxslider_image_top = JFactory::getApplication()->input->getString('bxs_desc_top_position', '80');
            $bxslider_image_left = JFactory::getApplication()->input->getString('bxs_desc_left_position', '0');
            $bxslider_image_width = JFactory::getApplication()->input->getString('bxs_desc_width_position', '100');
            $bxslider_image_height = JFactory::getApplication()->input->getString('bxs_desc_height_position', '20');
            if ($params->get('loadthemecategory', 1) == 0) {
                $bxslider_image_bgcolor = $params->get('bxslider_images_bgcolor', '#2e2e2e');
                $bxslider_image_transparency = $params->get('bxslider_images_transparency', '90');
                $bxslider_image_top = $params->get('bxslider_desc_top_position', '80');
                $bxslider_image_left = $params->get('bxslider_desc_left_position', '0');
                $bxslider_image_width = $params->get('bxslider_desc_width_position', '100');
                $bxslider_image_height = $params->get('bxslider_desc_height_position', '20');
            }
            $params_theme_bxslider = array('bxslider_image_bgcolor' => $bxslider_image_bgcolor, 'bxslider_image_transparency' => $bxslider_image_transparency,
                'bxslider_image_top' => $bxslider_image_top, 'bxslider_image_left' => $bxslider_image_left, 'bxslider_image_width' => $bxslider_image_width,
                'bxslider_image_height' => $bxslider_image_height
            );
        }
        if($id_gallery<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_GALLERY'));
        }

        $this->canEdit($id_gallery);

        //todo: créer un répertoire spécial pour les galleries
        $picture_dir = droppicsBase::getPicturePath($id_gallery);
        if(!file_exists($picture_dir)){
            JFolder::create($picture_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($picture_dir.'index.html', $data);
        }
        $full_dir = droppicsBase::getFullPicturePath($id_gallery);
        if(!file_exists($full_dir)){
            JFolder::create($full_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($full_dir.'index.html', $data);

        }
        $thumbnail_dir = droppicsBase::getThumbnailPath($id_gallery);
        if(!file_exists($thumbnail_dir)){
            JFolder::create($thumbnail_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($thumbnail_dir.'index.html', $data);
        }

        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_HTTP_RESPONSE'));
        }

        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){

                $pic = $_FILES['pic'];
                if(!in_array(strtolower(JFile::getExt($pic['name'])),$this->allowed_ext)){
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
                }

                $newname = droppicsBase::makeSafeFilename($pic['name']);
                if(file_exists($full_dir.$newname)){
                    $newname = uniqid().'.'.strtolower(JFile::getExt($pic['name']));
                }

                if(!JFile::upload($pic['tmp_name'], $full_dir.$newname)){
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_MOVE_FILE'));
                }
                $imageInfo = getimagesize($full_dir.$newname);
                if($imageInfo===false){
                    JFile::delete($picture_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'));
                }
                try{
                    $this->generatePicture($newname, $imageInfo,$id_gallery);
                }catch (Exception $e){
                    JFile::delete($full_dir.$newname);
                    JFile::delete($thumbnail_dir.$newname);
                    JFile::delete($picture_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'),$e);
                }

                //Insert new image into databse
                $model = $this->getModel();
                if ($params->get('new_image_on_top', 1)==1){
                    $id_picture = $model->addPictureCheckParam($newname,$id_gallery,JFile::stripExt($pic['name']),$params_theme_bxslider);
                }else  {
                    $id_picture = $model->addPicture($newname,$id_gallery,JFile::stripExt($pic['name']),$params_theme_bxslider);
                }
                if(!$id_picture){
                    JFile::delete($picture_dir.$newname);
                    JFile::delete($full_dir.$newname);
                    JFile::delete($thumbnail_dir.$newname);
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
                }
                $customs = $this->generateCustomSize($id_picture);

                $this->exit_status(true,array('id_picture'=>$id_picture,'name'=>$newname,'picture'=>droppicsBase::getPictureURL($id_gallery).$newname,'thumbnail'=>droppicsBase::getPictureURL($id_gallery).'thumbnails/'.$newname,'customs'=> json_encode($customs) ));
        }
        $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_ERROR_UPLOAD'));
    }

    public function addRemoteUrl() {

        $input = JFactory::getApplication()->input;
        $id_gallery = $input->getInt('id_gallery');
        if($id_gallery<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_GALLERY'));
        }
        $this->canEdit($id_gallery);

        //todo: créer un répertoire spécial pour les galleries
        $picture_dir = droppicsBase::getPicturePath($id_gallery);
        if(!file_exists($picture_dir)){
            JFolder::create($picture_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($picture_dir.'index.html', $data);
        }
        $full_dir = droppicsBase::getFullPicturePath($id_gallery);
        if(!file_exists($full_dir)){
            JFolder::create($full_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($full_dir.'index.html', $data);

        }
        $thumbnail_dir = droppicsBase::getThumbnailPath($id_gallery);
        if(!file_exists($thumbnail_dir)){
            JFolder::create($thumbnail_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($thumbnail_dir.'index.html', $data);
        }

        $remote_title = $input->getString('remote_title');
        $remote_url = trim(rawurldecode($input->getString('remote_url')) );

        if ($remote_url == '') {
            $this->exit_status(JText::_('Enter url'));
        }
        else {
            if (!preg_match("(http://|https://)", $remote_url)) {
                $this->exit_status(JText::_($remote_url . " is not a valid URL"));
            }
        }

        $video_type = $this->getVideoType($remote_url);
        if(!in_array($video_type, array('youtube','vimeo','dailymotion') ) ) {
             $this->exit_status(JText::_($remote_url . " is not a valid video URL"));
        }
        $vInfo = $this->getVideoInfo($remote_url,$video_type);
        $newname = $this->downloadVideoThumbnail($vInfo,$full_dir);
        if ($remote_title == '') {
            if(isset($vInfo['title']) && $vInfo['title']) {
                $remote_title = $vInfo['title'] ;
            }
           // $this->exit_status(JText::_('Enter title'));
        }

        $imageInfo = getimagesize($full_dir . $newname);
        if ($imageInfo === false) {
            JFile::delete($picture_dir . $newname);
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'));
        }
        try {
            $this->generatePicture($newname, $imageInfo, $id_gallery);
        } catch (Exception $e) {
            JFile::delete($full_dir . $newname);
            JFile::delete($thumbnail_dir . $newname);
            JFile::delete($picture_dir . $newname);
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'), $e);
        }

        //Insert new image into databse
        $model = $this->getModel();
        $params = array('is_video'=> 1,'vid'=> $vInfo['vid'],'directLink'=> $vInfo['directLink'],'ratio'=> $vInfo['ratio'],
                    'video_url'=>$remote_url,'content_custom_title' => $remote_title ,'video_type' => $video_type) ;
        $id_picture = $model->addVideo($newname, $params, $id_gallery, $remote_title);
        if (!$id_picture) {
            JFile::delete($picture_dir . $newname);
            JFile::delete($full_dir . $newname);
            JFile::delete($thumbnail_dir . $newname);
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
        }
        $this->exit_status(true, array('id_picture' => $id_picture, 'name' => $newname, 'picture' => droppicsBase::getPictureURL($id_gallery) . $newname, 'thumbnail' => droppicsBase::getPictureURL($id_gallery) . 'thumbnails/' . $newname));
    }

    function getVideoType($url) {
        $url = parse_url($url);
        $vType = '';

        if (strcasecmp($url['host'], 'youtu.be') === 0)
        {
           $vType = 'youtube' ;
        }elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
        {
             $vType = 'youtube' ;
        }elseif (strcasecmp($url['host'], 'vimeo.com') === 0 || strcasecmp($url['host'], 'player.vimeo.com') === 0)
        {
             $vType = 'vimeo' ;
        }elseif (strcasecmp($url['host'], 'www.dailymotion.com') === 0)
        {
             $vType = 'dailymotion' ;
        }else {
            //do nothing
        }

        return $vType;
    }

    function getVideoInfo($url, $type) {
        $vInfo = array();
        switch ($type) {
            case 'youtube':
                $vid = $this->parse_youtube_url($url);
                if(empty($vid)) {
                     $this->exit_status(JText::_($url . " is not a valid Youtube URL"));
                }
                $vInfo['type'] = 'youtube';
                $vInfo['vid'] = $vid;
                $vInfo['directLink'] = "https://www.youtube.com/embed/".$vid."?rel=0&wmode=transparent";
                $vInfo['thumbnail'] = 'https://img.youtube.com/vi/' . $vid . '/0.jpg';

                //get title
                $url = "https://www.youtube.com/watch?v=".$vid;
                $html = file_get_contents($url);
                $doc = new DOMDocument();
                $doc->strictErrorChecking = false;
                $doc->recover=true;
                $doc->preserveWhiteSpace = FALSE;
                @$doc->loadHTML("<html><body>".$html."</body></html>");

                $title_div = $doc->getElementById('eow-title');
                $vInfo['title'] = trim($title_div->nodeValue);
                $v_width = 0 ;$v_height =0;
                foreach($doc->getElementsByTagName('meta') as $meta) {
                    if($meta->getAttribute('property') == 'og:video:width') {
                        //Assign the value from content attribute to $meta_og_image
                        $v_width = $meta->getAttribute('content');
                    }
                    if($meta->getAttribute('property') == 'og:video:height') {
                        //Assign the value from content attribute to $meta_og_image
                        $v_height = $meta->getAttribute('content');
                    }
                    if($v_width > 0 & $v_height >0) { break ;}
                }
                if($v_width > 0 & $v_height >0) {
                     $vInfo['ratio'] = $v_width / $v_height;
                }else {
                    $vInfo['ratio'] = 16 /9 ;
                }
                break;
            case 'vimeo':
                $vid = $this->parse_vimeo_url($url);
                if(empty($vid)) {
                     $this->exit_status(JText::_($url . " is not a valid Vimeo URL"));
                }
                $vInfo['type'] = 'vimeo';
                $vInfo['vid'] = $vid;
                $vInfo['directLink']  = "https://player.vimeo.com/video/". $vid;
                $hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/$vid.php"));
                $vInfo['thumbnail'] = $hash[0]['thumbnail_large'];
                $vInfo['title'] = trim($hash[0]['title']);
                if($hash[0]['height']) {
                    $vInfo['ratio'] = (int)$hash[0]['width'] / (int)$hash[0]['height'];
                }else {
                    $vInfo['ratio'] = 16 /9 ;
                }

                break;
            case 'dailymotion' :
                $vid = $this->getDailyMotionId($url);
                if(empty($vid)) {
                     $this->exit_status(JText::_($url . " is not a valid Dailymotion URL"));
                }
                $vInfo['type'] = 'dailymotion';
                $vInfo['vid'] = $vid;
                $vInfo['directLink']  = "//www.dailymotion.com/embed/video/".$vid ;
                $vObj =  json_decode(file_get_contents('https://api.dailymotion.com/video/'.$vid.'?fields=thumbnail_large_url,title,aspect_ratio') );
                $vInfo['thumbnail'] = $vObj->thumbnail_large_url;
                $vInfo['ratio'] = $vObj->aspect_ratio;
                $vInfo['title'] =   trim($vObj->title);
                break;
            default:
                break;
        }
        return $vInfo;
    }

    function downloadVideoThumbnail($vInfo, $full_dir) {
        $newname = '';
        $vid = $vInfo['vid'];
        $newname = $vid. '.jpg';
        if(file_exists($full_dir.$newname)){
            $newname = $vid . "_". uniqid().'.jpg';
        }

        if(!copy($vInfo['thumbnail'], $full_dir . $newname) ) {
            # open file to write
            $fp = fopen ($full_dir . $newname, 'w+');
            # start curl
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $vInfo['thumbnail'] );
            # set return transfer to false
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            # increase timeout to download big file
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
            # write data to local file
            curl_setopt( $ch, CURLOPT_FILE, $fp );
            # execute curl
            curl_exec( $ch );
            # close curl
            curl_close( $ch );
            # close local file
            fclose( $fp );

        }

        return $newname;
    }

    //http://stackoverflow.com/questions/12387389/how-to-parse-dailymotion-video-url-in-javascript/12387913
    function getDailyMotionId($url)
    {
        $url = str_replace('/embed','', $url); //remove 'embed'
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $url, $m)) {
            if (isset($m[6])) {
                return $m[6];
            }
            if (isset($m[4])) {
                return $m[4];
            }
            return $m[2];
        }
        return false;
    }

    //http://stackoverflow.com/a/29860052
    function parse_vimeo_url($url) {
        $vid = false;
        if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $url, $matches)) {
           $vid =  $matches[5];
        }

	return $vid;
    }

    //http://stackoverflow.com/questions/6556559/youtube-api-extract-video-id/6556662
    function parse_youtube_url($url) {
        $video_id = false;
        $url = parse_url($url);
        if (strcasecmp($url['host'], 'youtu.be') === 0)
        {
            #### (dontcare)://youtu.be/<video id>
            $video_id = substr($url['path'], 1);
        }
        elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
        {
            if (isset($url['query']))
            {
                parse_str($url['query'], $url['query']);
                if (isset($url['query']['v']))
                {
                    #### (dontcare)://www.youtube.com/(dontcare)?v=<video id>
                    $video_id = $url['query']['v'];
                }
            }
            if ($video_id == false)
            {
                $url['path'] = explode('/', substr($url['path'], 1));
                if (in_array($url['path'][0], array('e', 'embed', 'v')))
                {
                    #### (dontcare)://www.youtube.com/(whitelist)/<video id>
                    $video_id = $url['path'][1];
                }
            }
        }
        return $video_id;

    }

    public function replace(){
        $id_picture = JFactory::getApplication()->input->getInt('id_picture', 0);
        $itype = JFactory::getApplication()->input->getString('type', 'picture');
        if($id_picture<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
        }

        $model = $this->getModel();
        $picture = $model->getPicture($id_picture);

        $this->canEdit($picture->id_gallery);

        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_HTTP_RESPONSE'));
        }

        list($type, $imgstr) = explode(';', JFactory::getApplication()->input->get('image',null,'raw'));
        list(, $type)        = explode(':', $type);
        list(, $imgstr)      = explode(',', $imgstr);
        $content = base64_decode($imgstr);

        $thumbnail_dir = droppicsBase::getThumbnailPath($picture->id_gallery);
        $picture_dir = droppicsBase::getPicturePath($picture->id_gallery);
        $custom_dir = droppicsBase::getCustomPath($picture->id_gallery);

        $wimg = WideImage::loadFromString($content);
        $imgString = $wimg->asString($type);
        if($itype=='thumbnail'){
            if(JFile::write($thumbnail_dir.$picture->file, $imgString)){
                $this->exit_status(true,array('message'=>JText::_('COM_DROPPICS_CTRL_FILES_SAVED')));
            }
        }elseif($itype=='original'){
            if(JFile::write($picture_dir.$picture->file, $imgString)){
                $this->exit_status(true,array('message'=>JText::_('COM_DROPPICS_CTRL_FILES_SAVED')));
            }
        }else{
            $id_custom = JFactory::getApplication()->input->getInt('id_custom', 0);
            if($id_custom<=0){
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
            }
            $custom = $model->getCustomPicture($id_custom);

            $ext = strtolower(JFile::getExt($custom->file));
            if(!in_array($ext, $this->allowed_ext)){
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
            }

            if(JFile::write($custom_dir.$custom->file, $imgString)){
                $this->exit_status(true,array('message'=>JText::_('COM_DROPPICS_CTRL_FILES_SAVED')));
            }
        }

        $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_ERROR_UPLOAD'));
    }


     /**
    * Resize name custom file
     */
    public function renameCustomFile(){
        $id_gallery = JFactory::getApplication()->input->getInt('id_gallery', 0);
        $id_custom = JFactory::getApplication()->input->getInt('id_custom', 0);
        $filename= JFactory::getApplication()->input->getString('filename', '');

        if($id_gallery<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
        }
        $model = $this->getModel();
        $custom_picture = $model->getCustomPicture($id_custom);
        if(!$custom_picture){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
        }

        $fullImageExt = strtolower(JFile::getExt($custom_picture->file));
        $safeFilename = droppicsBase::makeSafeFilename($filename.'.'.$fullImageExt);
        $customImagePath = droppicsBase::getCustomPath($id_gallery);
        if( @rename($customImagePath.$custom_picture->file, $customImagePath.$safeFilename ) ) {
            $result = $model->changeCustomPicture($id_custom, $safeFilename);
            if(!$result){
                @rename($customImagePath.$safeFilename , $customImagePath.$custom_picture->file);
            }
        }else {
            $result = false;
        }

        if(!$result){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
        }
        $this->exit_status(true,array('id_custom'=>$id_custom,'file'=>  ($safeFilename)) );

    }

    /**
    * Resize and create a custom size
     */
    public function customResize(){
        $id_picture = JFactory::getApplication()->input->getInt('id_picture', 0);
        if($id_picture<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
        }

        $model = $this->getModel();
        $picture = $model->getPicture($id_picture);

        $filename = JFactory::getApplication()->input->getString('filename', '');
        if($filename===''){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILENAME'));
        }

        $fullImagePath = droppicsBase::getFullPicturePath($picture->id_gallery);
        $fullImage = $fullImagePath.$picture->file;
        $fullImageExt = strtolower(JFile::getExt($picture->file));

        $customImagePath = droppicsBase::getCustomPath($picture->id_gallery);

        if(!in_array($fullImageExt, $this->allowed_ext)){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
        }
        $safeFilename = droppicsBase::makeSafeFilename($filename.'.'.$fullImageExt);
        if(file_exists($customImagePath.$safeFilename)){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_EXISTS'));
        }

        $fullImageSize = getimagesize($fullImage);


        $this->canEdit($picture->id_gallery);

        $height = JFactory::getApplication()->input->getInt('height', 0);
        $width = JFactory::getApplication()->input->getInt('width', 0);
        if(($height === 0 && $width === 0) || $width > $fullImageSize[0] || $height > $fullImageSize[1]){
            $this->exit_status(JText::sprintf('COM_DROPPICS_CTRL_FILES_WRONG_SIZE',$fullImageSize[0],$fullImageSize[1]));
        }
        if($height===0){
            $height = $fullImageSize[1];
        }
        if($width===0){
            $width = $fullImageSize[0];
        }

        try{
            $image = WideImage::load($fullImage)->resize($width, $height,@$fit,'down');
            if($fullImageSize['mime']=='image/jpeg'){
                $imgString = $image->asString('jpg',droppicsBase::getParam('jpg_quality',80));
                JFile::write($customImagePath.$safeFilename, $imgString);
            }elseif($fullImageSize['mime']=='image/png'){
                $imgString = $image->asString('png',droppicsBase::getParam('png_quality',9));
                JFile::write($customImagePath.$safeFilename, $imgString);
            }elseif($fullImageSize['mime']=='image/gif'){
                $imgString = $image->asString('gif');
                JFile::write($customImagePath.$safeFilename, $imgString);
            }else{
                throw new Exception('Wrong image type');
            }
        }catch (Exception $e){
            $this->exit_status('error while writing image');
        }
        $finalInfo = getimagesize($customImagePath.$safeFilename);

        $id = $model->addCustomPicture($safeFilename,$filename,$picture->id,$finalInfo[0],$finalInfo[1]);
        if(!$id){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
        }
        $this->exit_status(true,array('id'=>$id,'id_picture'=>$id_picture,'file'=>  ($safeFilename),'width'=>$finalInfo[0],'height'=>$finalInfo[1]));
    }

    public function generateCustomSize($id_picture) {
        $results = array();
        $predefinedsizes = droppicsBase::getParam('predefinedsizes','');
        if(!empty($predefinedsizes)) {
            $model = $this->getModel();
            $picture = $model->getPicture($id_picture);
            $id_gallery = $picture->id_gallery;
            $fullImagePath = droppicsBase::getFullPicturePath($id_gallery);
            $fullImage = $fullImagePath.$picture->file;
            $fullImageExt = strtolower(JFile::getExt($picture->file));
            $fullImageSize = getimagesize($fullImage);
            $this->canEdit($id_gallery);

            $customImagePath = droppicsBase::getCustomPath($id_gallery);
            $cropping = array(1=>array('left','top'),2=>array('center','top'),3=>array('right','top'),4=>array('left','middle'),5=>array('center','middle'),6=>array('right','middle'),7=>array('bottom','top'),8=>array('bottom','middle'),9=>array('bottom','right'));
            $doCrop = array_key_exists(droppicsBase::getParam('cropping',0),$cropping);
            $fit = $doCrop?'outside':'inside';

            $temps = explode(";",$predefinedsizes);
            foreach ($temps as $temp) {
                $temp = trim(str_replace('px', '', $temp));
                list($width,$height)= explode('x', $temp);
                $custom_title= "";
                if(strpos($width, "-")!== false) {
                    $custom_title = substr($width,0, strrpos($width, "-") );
                    $width = substr($width,strrpos($width, "-")+1 );
                }
                $width= $width*1; $height= $height*1;
                if(($height === 0 && $width === 0) || $width > $fullImageSize[0] || $height > $fullImageSize[1]){
                    $this->exit_status(JText::sprintf('COM_DROPPICS_CTRL_FILES_WRONG_SIZE',$fullImageSize[0],$fullImageSize[1]));
                }

                if($width===0){
                    $width = $fullImageSize[0];
                }
                 if($height===0){
                    $height = $fullImageSize[1]/$fullImageSize[0] * $width;
                }

                $filename = basename($picture->file,".".$fullImageExt).'-'.$width.'x'.$height;
                if($custom_title=="") {
                    $custom_title  = $filename;
                }

                $safeFilename = droppicsBase::makeSafeFilename($filename.".".$fullImageExt);
                if(file_exists($customImagePath.$safeFilename)){
                    continue ;
                }

                try{
                    $image = WideImage::load($fullImage)->resize($width, $height,@$fit,'down');
                    if($fullImageSize['mime']=='image/jpeg'){
                        $imgString = $image->asString('jpg',droppicsBase::getParam('jpg_quality',80));
                        JFile::write($customImagePath.$safeFilename, $imgString);
                    }elseif($fullImageSize['mime']=='image/png'){
                        $imgString = $image->asString('png',droppicsBase::getParam('png_quality',9));
                        JFile::write($customImagePath.$safeFilename, $imgString);
                    }elseif($fullImageSize['mime']=='image/gif'){
                        $imgString = $image->asString('gif');
                        JFile::write($customImagePath.$safeFilename, $imgString);
                    }else{
                        throw new Exception('Wrong image type');
                    }
                }catch (Exception $e){
                    $this->exit_status('error while writing image');
                }
                $finalInfo = getimagesize($customImagePath.$safeFilename);
                $id = $model->addCustomPicture($safeFilename,$custom_title,$id_picture,$finalInfo[0],$finalInfo[1]);
                if(!$id){
                    $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_SAVE_TO_DB'));
                }
                $customObj =  new stdClass();
                $customObj->id =  $id;
                $customObj->id_picture=  $id_picture;
                $customObj->file=  $safeFilename;
                $customObj->title=  $custom_title;
                $customObj->width=  $finalInfo[0];
                $customObj->height=  $finalInfo[1];
                $results[] = $customObj;
            }
        }

        return $results;
    }



    public function generateCustomSizeWithListCus($listCus) {
        $wtm_images = droppicsBase::getParam('watermarks_images');
        $wtm_image_logo = $this->getPathFile(droppicsBase::getParam('watermarks_category_images'));
        $check_image_logo_exit = true;
        if(!in_array(strtolower(JFile::getExt($wtm_image_logo)),$this->allowed_ext)){
            $check_image_logo_exit = false;
        }
        $wtm_apply_on = droppicsBase::getParam('apply_watermark_on');
        $fullImagePath ='';
        $fullImageName ='';
        if (!$wtm_apply_on){$wtm_apply_on = array('full');}

        if (empty($listCus[0])) {
            return;
        }
        foreach ($listCus as $obj_pic_repleate) {
            $id_picture = $obj_pic_repleate->id_picture;
            $filenames = $obj_pic_repleate->file;
            $filename = pathinfo($filenames, PATHINFO_FILENAME);
            $height = $obj_pic_repleate->height;
            $width = $obj_pic_repleate->width;
            $name_change_file_wtm = pathinfo($filenames, PATHINFO_FILENAME) . 'imageswatermark' . '.' . pathinfo($filenames, PATHINFO_EXTENSION);

            if ($id_picture <= 0) {
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE'));
            }

            $model = $this->getModel();
            $picture = $model->getPicture($id_picture);
            if ($filename === '') {
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILENAME'));
            }

            $fullImagePath = droppicsBase::getFullPicturePath($picture->id_gallery);
            $fullImage = $fullImagePath . $picture->file;
            $fullImageName = $picture->file;
            $fullImageExt = strtolower(JFile::getExt($picture->file));

            $customImagePath = droppicsBase::getCustomPath($picture->id_gallery);

            if (!in_array($fullImageExt, $this->allowed_ext)) {
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'), array('allowed ' => $this->allowed_ext));
            }
            $safeFilename = droppicsBase::makeSafeFilename($filename . '.' . $fullImageExt);

            if (file_exists($customImagePath . $filenames)) {
                unlink($customImagePath . $filenames);
            }
            if (file_exists($customImagePath . $name_change_file_wtm)) {
                unlink($customImagePath . $name_change_file_wtm);
            }
            $this->copyFileWithNewName($customImagePath,$filenames,'initimage');
            if($wtm_images && $check_image_logo_exit && in_array('full',$wtm_apply_on) && in_array('all',$wtm_apply_on) == false) {
                $name_change_file_wtms = pathinfo($fullImageName, PATHINFO_FILENAME) . 'imageswatermark' . '.' . pathinfo($fullImageName, PATHINFO_EXTENSION);
                $fullImage = $fullImagePath.$name_change_file_wtms;
            }
            $fullImageSize = getimagesize($fullImage);
            $this->canEdit($picture->id_gallery);
            if (($height === 0 && $width === 0) || $width > $fullImageSize[0] || $height > $fullImageSize[1]) {
                $this->exit_status(JText::sprintf('COM_DROPPICS_CTRL_FILES_WRONG_SIZE', $fullImageSize[0], $fullImageSize[1]));
            }
            if ($height === 0) {
                $height = $fullImageSize[1];
            }
            if ($width === 0) {
                $width = $fullImageSize[0];
            }
            try {
                $image = WideImage::load($fullImage)->resize($width, $height, @$fit, 'down');
                if ($fullImageSize['mime'] == 'image/jpeg') {
                    $imgString = $image->asString('jpg', droppicsBase::getParam('jpg_quality', 80));
                    JFile::write($customImagePath . $safeFilename, $imgString);
                } elseif ($fullImageSize['mime'] == 'image/png') {
                    $imgString = $image->asString('png', droppicsBase::getParam('png_quality', 9));
                    JFile::write($customImagePath . $safeFilename, $imgString);
                } elseif ($fullImageSize['mime'] == 'image/gif') {
                    $imgString = $image->asString('gif');
                    JFile::write($customImagePath . $safeFilename, $imgString);
                } else {
                    throw new Exception('Wrong image type');
                }
            } catch (Exception $e) {
                $this->exit_status('error while writing image');
            }
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($customImagePath,$filenames,'custom');}
        }
        if($wtm_images && $check_image_logo_exit && in_array('full',$wtm_apply_on) && in_array('all',$wtm_apply_on) ==
            false) {
            $this->copyFileWithNewName($fullImagePath, $fullImageName, 'full');
        }
    }
    /**
     * Delete a picture
     */
    public function delete(){
        $pictures = JFactory::getApplication()->input->get('pictures', null,'array');
        $deletedPictures = array();
        foreach ($pictures as $pic) {
            $model = $this->getModel();
            $picture = $model->getPicture((int)$pic);
            if($picture!==false){
                $this->canEdit($picture->id_gallery);

                $full_dir = droppicsBase::getFullPicturePath($picture->id_gallery);
                $picture_dir = droppicsBase::getPicturePath($picture->id_gallery);
                $thumbnail_dir = droppicsBase::getThumbnailPath($picture->id_gallery);
                $custom_dir = droppicsBase::getCustomPath($picture->id_gallery);
                if(file_exists($full_dir.$picture->file)){
                    JFile::delete($full_dir.$picture->file);
                }
                if(file_exists($picture_dir.$picture->file)){
                    JFile::delete($picture_dir.$picture->file);
                }
                if(file_exists($thumbnail_dir.$picture->file)){
                    JFile::delete($thumbnail_dir.$picture->file);
                }

                $customs = $model->getCustomPictures($picture->id);
                foreach ($customs as $custom){
                    JFile::delete($custom_dir.$custom->file);
                    $model->removeCustom($custom->id);
                }

                if($model->removePicture($picture->id)){
                        $deletedPictures[] = $picture->id;
                }else{}
            }
        }
        echo json_encode($deletedPictures);
        JFactory::getApplication()->close();
    }

    /**
     * Delete a custom picture
     */
    public function deleteCustom(){
        $id = JFactory::getApplication()->input->getInt('id', 0);
        $model = $this->getModel();
        $custom = $model->getCustomPicture($id);

        $picture = $picture = $model->getPicture((int)$custom->id_picture);
        $this->canEdit($picture->id_gallery);

        $custom_dir = droppicsBase::getCustomPath($picture->id_gallery);

        JFile::delete($custom_dir.$custom->file);
        if($model->removeCustom($custom->id)){
            $this->exit_status(true);
        }
        $this->exit_status('error while deleting image');
    }

    /**
     * Reorder gallery
     */
    public function reorder(){
        $model = $this->getModel();
        $pictures = JRequest::getString('order',2);
        $pictures = json_decode($pictures);

        foreach ($pictures as $key => $picture) {
            $pic = $model->getPicture($picture);
            $this->canEdit($pic->id_gallery);
        }


        if($model->reorder($pictures)){
                $return = true;
        }else{
            $return = false;
        }
        echo json_encode($return);
        JFactory::getApplication()->close();
    }


    /**
     * Generate all pictures thumbnails in database
     */
    public function regeneratePictures(){
        $step = 2;
        require_once JPATH_COMPONENT.'/helpers/droppics.php';
        $canDo	= DroppicsHelper::getActions();
        if (!$canDo->get('core.admin')) {
            $this->exit_status('not permitted');
        }
        $model = $this->getModel();
        $pictures = $model->getAllPictures();

        if(empty($pictures)){
            $this->exit_status(true,array('end'=>true,'processed'=>0));
        }

        $nb = JRequest::getInt('nb',0);

        $processed=0;
        for ($index = $nb*$step; $index < $step*($nb+1) && $index < count($pictures); $index++) {
            set_time_limit(10);
            $full_dir = droppicsBase::getFullPicturePath($pictures[$index]->id_gallery);
            $imageInfo = getimagesize($full_dir.$pictures[$index]->file);
            try{
                $this->generatePicture($pictures[$index]->file, $imageInfo,$pictures[$index]->id_gallery);
                $this->generateCustomSize($pictures[$index]->id);
                $model = $this->getModel();
                $list_custom_picture = $model->getCustomPictures($pictures[$index]->id);
                if(!empty($list_custom_picture)) {
                     $this->generateCustomSizeWithListCus($list_custom_picture);
                }
            }catch (Exception $e){
                $this->exit_status('error while writing image');
            }


            $processed++;
        }

        if(($index) == count($pictures)){

            droppicsBase::setParams(
                    array(
                        'updated'=>'',
                        'lastmodified'=> time(),
                        'thumbnail_width_old'=>droppicsBase::getParam('thumbnail_width'),
                        'thumbnail_height_old'=>droppicsBase::getParam('thumbnail_height'),
                        'picture_width_old'=>droppicsBase::getParam('picture_width'),
                        'picture_height_old'=>droppicsBase::getParam('picture_height'),
                        'full_width_old'=>droppicsBase::getParam('full_width'),
                        'full_height_old'=>droppicsBase::getParam('full_height'),
                        'jpg_quality_old'=>droppicsBase::getParam('jpg_quality'),
                        'png_quality_old'=>droppicsBase::getParam('png_quality'),
                        'watermarks_category_images_old'=> droppicsBase::getParam('watermarks_category_images'),
                        'watermark_position_old'=>droppicsBase::getParam('watermark_position'),
                        'apply_watermark_on_old'=>implode(",",droppicsBase::getParam('apply_watermark_on',array(0))),
                        'watermarks_images_old'=>droppicsBase::getParam('watermarks_images',0)
                    ));
            $this->exit_status(true,array('end'=>true,'processed'=>$processed,'progress'=>100));
        }
        $this->exit_status(true,array('end'=>false,'processed'=>$processed,'progress'=>(($nb+1)*$step)/count($pictures)*100));

    }

    private function generatePicture($newname,$imageInfo,$id_gallery){
        $wtm_images = droppicsBase::getParam('watermarks_images');
        $wtm_image_logo = $this->getPathFile(droppicsBase::getParam('watermarks_category_images'));
        $check_image_logo_exit = true;
        if(!in_array(strtolower(JFile::getExt($wtm_image_logo)),$this->allowed_ext)){
            $check_image_logo_exit = false;
        }
        $wtm_apply_on = droppicsBase::getParam('apply_watermark_on');
        if (!$wtm_apply_on){$wtm_apply_on = array('full');}

        $full_dir = droppicsBase::getFullPicturePath($id_gallery);
        $picture_dir = droppicsBase::getPicturePath($id_gallery);
        $thumbnail_dir = droppicsBase::getThumbnailPath($id_gallery);
        $this->copyFileWithNewName($full_dir, $newname, 'initimage');
        $this->copyFileWithNewName($thumbnail_dir, $newname, 'initimage');
        $this->copyFileWithNewName($picture_dir,$newname,'initimage');

        $image = WideImage::load($full_dir.$newname)->resize(droppicsBase::getParam('full_width',1200), droppicsBase::getParam('full_height',1000),'inside','down');
        $cropping = array(1=>array('left','top'),2=>array('center','top'),3=>array('right','top'),4=>array('left','middle'),5=>array('center','middle'),6=>array('right','middle'),7=>array('bottom','top'),8=>array('bottom','middle'),9=>array('bottom','right'));
        $doCrop = array_key_exists(droppicsBase::getParam('cropping',0),$cropping);


        $fit = $doCrop?'outside':'inside';
        if($imageInfo['mime']=='image/jpeg'){
            $imgString = $image->asString('jpg',droppicsBase::getParam('jpg_quality',80));
            JFile::write($full_dir.$newname, $imgString);
            if ($wtm_images && in_array('all',$wtm_apply_on) && $check_image_logo_exit){
                $this->copyFileWithNewName($full_dir,$newname,'full');
            }
            $image = WideImage::load($full_dir . $newname)->resize(droppicsBase::getParam('thumbnail_width', 180), droppicsBase::getParam('thumbnail_height', 180), $fit, 'down');
            if($doCrop){
                $image = $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('thumbnail_width',180),droppicsBase::getParam('thumbnail_height',180));
            }
            $imgString = $image->asString('jpg',droppicsBase::getParam('jpg_quality',80));
            JFile::write($thumbnail_dir.$newname,$imgString );
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($thumbnail_dir,$newname,'thumbnails');}
            $image = WideImage::load($full_dir.$newname)->resize(droppicsBase::getParam('picture_width',800), droppicsBase::getParam('picture_height',600),$fit,'down');
            if($doCrop){
                $image = $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('picture_width',180),droppicsBase::getParam('picture_height',180));
            }
            $imgString = $image->asString('jpg',droppicsBase::getParam('jpg_quality',80));
            JFile::write($picture_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($picture_dir,$newname,'custom');}
            if($wtm_images && in_array('full',$wtm_apply_on) && in_array('all',$wtm_apply_on) == false && $check_image_logo_exit) {
                $this->copyFileWithNewName($full_dir, $newname, 'full');
            }
        }elseif($imageInfo['mime']=='image/png'){
            $imgString = $image->asString('png',droppicsBase::getParam('png_quality',9));
            JFile::write($full_dir.$newname, $imgString);
            if ($wtm_images && in_array('all',$wtm_apply_on) && $check_image_logo_exit) {
                $this->copyFileWithNewName($full_dir, $newname, 'full');
            }
            $image = WideImage::load($full_dir . $newname)->resize(droppicsBase::getParam('thumbnail_width', 180), droppicsBase::getParam('thumbnail_height', 180), $fit, 'down');
            if($doCrop){
                $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('thumbnail_width',180),droppicsBase::getParam('thumbnail_height',180));
            }
            $imgString = $image->asString('png',droppicsBase::getParam('png_quality',9));
            JFile::write($thumbnail_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($thumbnail_dir,$newname,'thumbnails');}
            $image = WideImage::load($full_dir.$newname)->resize(droppicsBase::getParam('picture_width',800), droppicsBase::getParam('picture_height',600),$fit,'down');
            if($doCrop){
                $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('picture_width',180),droppicsBase::getParam('picture_height',180));
            }
            $imgString = $image->asString('png',droppicsBase::getParam('png_quality',9));
            JFile::write($picture_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($picture_dir,$newname,'custom');}
            if($wtm_images && in_array('full',$wtm_apply_on) && in_array('all',$wtm_apply_on) == false && $check_image_logo_exit) {
                $this->copyFileWithNewName($full_dir, $newname, 'full');
            }
        }elseif($imageInfo['mime']=='image/gif'){
            $imgString = $image->asString('gif');
            JFile::write($full_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit && in_array('all',$wtm_apply_on)) {
                $this->copyFileWithNewName($full_dir, $newname, 'full');
            }
            $image = WideImage::load($full_dir . $newname)->resize(droppicsBase::getParam('thumbnail_width', 180), droppicsBase::getParam('thumbnail_height', 180), $fit, 'down');
            if($doCrop){
                $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('thumbnail_width',180),droppicsBase::getParam('thumbnail_height',180));
            }
            $imgString = $image->asString('gif');
            JFile::write($thumbnail_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($thumbnail_dir,$newname,'thumbnails');}
            $image = WideImage::load($full_dir.$newname)->resize(droppicsBase::getParam('picture_width',800), droppicsBase::getParam('picture_height',600),$fit,'down');
            if($doCrop){
                $image->crop($cropping[droppicsBase::getParam('cropping',0)][0],$cropping[droppicsBase::getParam('cropping',0)][1],droppicsBase::getParam('picture_width',180),droppicsBase::getParam('picture_height',180));
            }
            $imgString = $image->asString('gif');
            JFile::write($picture_dir.$newname, $imgString);
            if ($wtm_images && $check_image_logo_exit){$this->copyFileWithNewName($picture_dir,$newname,'custom');}
            if($wtm_images && $check_image_logo_exit && in_array('full',$wtm_apply_on) && in_array('all',$wtm_apply_on) == false) {
                $this->copyFileWithNewName($full_dir, $newname, 'full');
            }
        }else{
            throw new Exception('Wrong image type');
        }
    }

    /**
     * Return a json response
     * @param $status
     * @param array $datas array of datas to return with the json string
     *
     */
    private function exit_status($status,$datas=array()){
            $response = array('response'=>$status,'datas'=>$datas);
//            $this->setRedirect('index.php?option=com_droppics&view=files&layout=result&format=json&error='.$error);
//            $this->redirect();
            echo json_encode($response);
            JFactory::getApplication()->close();
    }

    /**
     * Check if the current user has permission on the current gallery
     * @param type $id_gallery
     */
    private function canEdit($id_gallery){
        $model = $this->getModel('category');
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $gallery = $model->getItem($id_gallery);
                if($gallery->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }
    }

    public function copyfile()
    {
        $input = JFactory::getApplication()->input;
        $id_gallery = $input->getInt('id_gallery', 0);
        $id_file = $input->getString('id_file', null);
        $model = $this->getModel();
        $modelFile = $this->getModel('picture');
        $gallery_path_dest = droppicsBase::getPicturePath($id_gallery);
        $gallery_path_full_dest = droppicsBase::getFullPicturePath($id_gallery);
        $gallery_path_thum_dest = droppicsBase::getThumbnailPath($id_gallery);
        $file = $modelFile->getItem($id_file);
        $gallery_path_current = droppicsBase::getPicturePath($file->id_gallery);
        $gallery_path_full_current = droppicsBase::getFullPicturePath($file->id_gallery);
        $gallery_path_thum_current = droppicsBase::getThumbnailPath($file->id_gallery);

        if ($file->id_gallery != $id_gallery) {
            $newname = uniqid() . '.' . strtolower(JFile::getExt($file->file));
            if ($model->addPicture($newname,$id_gallery,$file->alt)) {
                // move file
                $file_current = $gallery_path_current . $file->file;
                $file_full_current = $gallery_path_full_current . $file->file;
                $file_thum_current = $gallery_path_thum_current . $file->file;

                $file_dest = $gallery_path_dest . $newname;
                $file_full_dest = $gallery_path_full_dest . $newname;
                $file_thum_dest = $gallery_path_thum_dest . $newname;

                if (!file_exists($gallery_path_dest)) {
                    JFolder::create($gallery_path_dest);
                    JFolder::create($gallery_path_full_dest);
                    JFolder::create($gallery_path_thum_dest);
                    $data = '<html><body bgcolor="#FFFFFF"></body></html>';
                    JFile::write($gallery_path_dest . 'index.html', $data);
                    JFile::write($gallery_path_full_dest . 'index.html', $data);
                    JFile::write($gallery_path_thum_dest . 'index.html', $data);
                }
                if (is_file($file_current)) {
                    JFile::copy($file_current, $file_dest);
                    JFile::copy($file_full_current , $file_full_dest);
                    JFile::copy($file_thum_current, $file_thum_dest);
                }

            }
        }
        $this->exit_status(true);
        JFactory::getApplication()->close();
    }
    public function movefile()
    {
        $input = JFactory::getApplication()->input;
        $id_gallery = $input->getInt('id_gallery', 0);
        $id_file = $input->getString('id_file', null);
        $id_file =  explode(",",$id_file);
        $count_idfile = count($id_file);
        $model = $this->getModel();
        $modelFile = $this->getModel('picture');
        $gallery_path_dest = droppicsBase::getPicturePath($id_gallery);
        $gallery_path_full_dest = droppicsBase::getFullPicturePath($id_gallery);
        $gallery_path_thum_dest = droppicsBase::getThumbnailPath($id_gallery);
        if (!file_exists($gallery_path_dest)) {
            JFolder::create($gallery_path_dest);
            JFolder::create($gallery_path_full_dest);
            JFolder::create($gallery_path_thum_dest);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($gallery_path_dest . 'index.html', $data);
            JFile::write($gallery_path_full_dest . 'index.html', $data);
            JFile::write($gallery_path_thum_dest . 'index.html', $data);
        }

        foreach ($id_file as $vl_id_file) {
            $file = $modelFile->getItem($vl_id_file);
            $gallery_path_current = droppicsBase::getPicturePath($file->id_gallery);
            $gallery_path_full_current = droppicsBase::getFullPicturePath($file->id_gallery);
            $gallery_path_thum_current = droppicsBase::getThumbnailPath($file->id_gallery);
            if ($model->addPicture($file->file, $id_gallery, $file->alt)) {
                // move file
                $file_current = $gallery_path_current . $file->file;
                $file_full_current = $gallery_path_full_current . $file->file;
                $file_thum_current = $gallery_path_thum_current . $file->file;

                $file_dest = $gallery_path_dest . $file->file;
                $file_full_dest = $gallery_path_full_dest . $file->file;
                $file_thum_dest = $gallery_path_thum_dest . $file->file;

                if (is_file($file_current) && $model->removePicture($vl_id_file) !== false) {
                    JFile::move($file_current, $file_dest);
                    JFile::move($file_thum_current, $file_thum_dest);
                    JFile::move($file_full_current, $file_full_dest);
                }
            }
        }
        $this->exit_status(true, array('id_file'=> $id_file));
        JFactory::getApplication()->close();
    }

    public function uploadReplaceFile(){
        $id_gallery = JFactory::getApplication()->input->getInt('id_gallery', 0);
        $id_picture_old = JFactory::getApplication()->input->getInt('id_picture_old', 0);
        $name_file_old = JFactory::getApplication()->input->getString('name_file_old','');
        $name_file_old = (strpos($name_file_old,'?') !== false)? substr($name_file_old,0, strpos($name_file_old,'?')): $name_file_old ;
        $cus_id_checked = JFactory::getApplication()->input->getString('cus_id_checked','');
        $name_change_file_wtm = pathinfo($name_file_old, PATHINFO_FILENAME).'imageswatermark'.'.'.pathinfo($name_file_old,
                PATHINFO_EXTENSION);

        if($id_gallery<=0){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_GALLERY'));
        }

        $this->canEdit($id_gallery);

        //todo: créer un répertoire spécial pour les galleries
        $picture_dir = droppicsBase::getPicturePath($id_gallery);
        if(!file_exists($picture_dir)){
            JFolder::create($picture_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($picture_dir.'index.html', $data);
        }
        $full_dir = droppicsBase::getFullPicturePath($id_gallery);
        if(!file_exists($full_dir)){
            JFolder::create($full_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($full_dir.'index.html', $data);

        }
        $thumbnail_dir = droppicsBase::getThumbnailPath($id_gallery);
        if(!file_exists($thumbnail_dir)){
            JFolder::create($thumbnail_dir);
            $data = '<html><body bgcolor="#FFFFFF"></body></html>';
            JFile::write($thumbnail_dir.'index.html', $data);
        }

        if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
            $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_HTTP_RESPONSE'));
        }
        // delete file part category
        if(file_exists($picture_dir.$name_file_old)) {
            unlink($picture_dir . $name_file_old);
        }
        if(file_exists($picture_dir.$name_change_file_wtm)){
            unlink($picture_dir.$name_change_file_wtm);
        }
        if(file_exists($full_dir.$name_file_old)){
            unlink($full_dir.$name_file_old);
        }
        if(file_exists($full_dir.$name_change_file_wtm)){
            unlink($full_dir.$name_change_file_wtm);
        }
        if(file_exists($thumbnail_dir.$name_file_old)) {
            unlink($thumbnail_dir . $name_file_old);
        }
        if(file_exists($thumbnail_dir.$name_change_file_wtm)){
             unlink($thumbnail_dir.$name_change_file_wtm);
        }

        if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){

            $pic = $_FILES['pic'];
            if(!in_array(strtolower(JFile::getExt($pic['name'])),$this->allowed_ext)){
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_WRONG_FILE_EXTENSION'),array('allowed '=> $this->allowed_ext));
            }

            $newname = $name_file_old;
            if(file_exists($full_dir.$newname)){
                $newname = uniqid().'.'.strtolower(JFile::getExt($pic['name']));
            }

            if(!JFile::upload($pic['tmp_name'], $full_dir.$newname)){
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_MOVE_FILE'));
            }
            $imageInfo = getimagesize($full_dir.$newname);
            if($imageInfo===false){
                JFile::delete($picture_dir.$newname);
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'));
            }
            try{
                $this->generatePicture($newname, $imageInfo,$id_gallery);
            }catch (Exception $e){
                JFile::delete($full_dir.$newname);
                JFile::delete($thumbnail_dir.$newname);
                JFile::delete($picture_dir.$newname);
                $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_CANT_CREATE_FILE'),$e);
            }

            $model = $this->getModel();
            $custom_picture_checked = $model->getCustomPicture($cus_id_checked);
            $list_custom_picture = $model->getCustomPictures($id_picture_old);
            if(!empty($list_custom_picture)) {
                if (empty($custom_picture_checked)) {
                    $custom_picture_checked = $list_custom_picture[0];
                }
                $customs = $this->generateCustomSizeWithListCus($list_custom_picture);
            }

            //update lastmodified parameter

            droppicsBase::setParams(array('lastmodified'=> time() ) );

            $this->exit_status(true,array('id_picture'=>$id_picture_old,'name'=>$newname,'picture'=>droppicsBase::getPictureURL($id_gallery).$newname,'thumbnail'=>droppicsBase::getPictureURL($id_gallery).'thumbnails/'.$newname.'?ver='.time(),'customschecked'=> $custom_picture_checked,'customs'=>json_encode($list_custom_picture)));
        }
        $this->exit_status(JText::_('COM_DROPPICS_CTRL_FILES_ERROR_UPLOAD'));
    }

    // save copy image and watermarks
    private function copyFileWithNewName($pathdir,$fname,$wtmApplyOn){
        $wtm_image_logo = $this->getPathFile(droppicsBase::getParam('watermarks_category_images'));
        $wtm_position = droppicsBase::getParam('watermark_position',0);
        $wtm_apply_on = droppicsBase::getParam('apply_watermark_on');
        if (!$wtm_apply_on){$wtm_apply_on = array('full');}

        $check_name_wtm =  'imageswatermark';
        $name_change_file_wtm = pathinfo($fname, PATHINFO_FILENAME).$check_name_wtm.'.'.pathinfo($fname,
                PATHINFO_EXTENSION);
        $file = $pathdir.$fname;
        $newfile = $pathdir.$name_change_file_wtm;

        if ($wtmApplyOn == 'initimage') {
            if (strpos($pathdir,'full')) {
                if (file_exists($newfile)) {
                    if (unlink($file)) {
                        if (copy($newfile, $file)) {
                            unlink($newfile);
                            return;
                        }
                    }
                }
            } else {
                if (file_exists($newfile)) {
                    unlink($newfile);
                }
                if (file_exists($file)) {
                    {
                        unlink($file);
                    }
                }
            }
            return;
        }

        if (in_array('all', $wtm_apply_on)){
            if (file_exists($newfile) && strpos($pathdir,'full')){
                if (copy($newfile, $file)) {
                    $this->watermark($file,$wtm_image_logo,$wtm_position);
                    return $newfile;
                }
            }else{
                if (copy($file, $newfile) && strpos($pathdir,'full')) {
                    $this->watermark($file,$wtm_image_logo,$wtm_position);
                    return $newfile;
                }
            }
        }else{
            if (in_array($wtmApplyOn, $wtm_apply_on) == false ){
                if(file_exists($newfile)){
                    unlink($file);
                    copy($newfile,$file);
                    unlink($newfile);
                }
            }else {
                if (file_exists($newfile)) {
                    if (unlink($file)) {
                        if (copy($newfile, $file)) {
                            $this->watermark($file,$wtm_image_logo,$wtm_position);
                            return $newfile;
                        }
                    }
                }
                else {
                    if (file_exists($file)) {
                        if (copy($file, $newfile)) {
                            $this->watermark($file, $wtm_image_logo, $wtm_position);
                            return $newfile;
                        }
                    }
                }
            }
        }
        return false;
    }
    private function getPathFile($mediadir){
        if (file_exists(dirname(COM_MEDIA_BASE).'/'.$mediadir)){
            return  dirname(COM_MEDIA_BASE).'/'.$mediadir;
        }else if (file_exists(COM_MEDIA_BASE.'/'.$mediadir)){
            return  COM_MEDIA_BASE.'/'.$mediadir;
        }
        return false;
    }

    protected function watermark($image_path,$logoImage_path,$position)
    {

        if (!file_exists($image_path)) {
            die("Image does not exist.");
        }

        try {
            // Find base image size
            $image = $this->imagecreatefrom($image_path);
            $logoImage = $this->imagecreatefrom($logoImage_path);
            list($image_x, $image_y) = getimagesize($image_path);
            list($logo_x, $logo_y) = getimagesize($logoImage_path);

            if ($position === 'wtmcenter' || $position === 0) {
                $watermark_pos_x = ($image_x - $logo_x) / 2; //watermark left
                $watermark_pos_y = ($image_y - $logo_y) / 2; //watermark bottom
            }
            if ($position === 'wtmtopleft') {
                $watermark_pos_x = 0;
                $watermark_pos_y = 0 ;
            }
            if ($position === 'wtmtopright') {
                $watermark_pos_x = $image_x - $logo_x ;
                $watermark_pos_y = 0 ;

            }
            if ($position === 'wtmbtright') {
                $watermark_pos_x = $image_x - $logo_x ;
                $watermark_pos_y = $image_y - $logo_y  ;
            }
             if ($position === 'wtmbtleft') {
                 $watermark_pos_x = 0 ;
                 $watermark_pos_y = $image_y - $logo_y ;
             }

            imagecopy($image, $logoImage, $watermark_pos_x, $watermark_pos_y, 0, 0, $logo_x, $logo_y);

            // Output to the browser
            $imageInfo = getimagesize($image_path);
            switch (strtolower($imageInfo['mime'])) {
                case 'image/jpeg':
                case 'image/pjpeg':
                    header("Content-Type: image/jpeg");
                    imagejpeg($image, $image_path);
                    break;
                case 'image/png':
                    header("Content-Type: image/png");
                    imagepng($image, $image_path);
                    break;
                case 'image/gif':
                    header("Content-Type: image/gif");
                    imagegif($image, $image_path);
                    break;
                default:
                    die("Image is of unsupported type.");
            }
            // Overlay watermark
            // Destroy the images
            imagedestroy($image);
            imagedestroy($logoImage);
        } catch (Exception $e) {
            return;
        }
    }

    protected function imagecreatefrom($image)
    {
        $size = getimagesize($image);
        // Load image from file
        switch (strtolower($size['mime'])) {
            case 'image/jpeg':
            case 'image/pjpeg':
                return imagecreatefromjpeg($image);
                break;
            case 'image/png':
                return imagecreatefrompng($image);
                break;
            case 'image/gif':
                return imagecreatefromgif($image);
                break;
            default:
                die("Image is of unsupported type.");
        }
    }

    public function upAltPictures(){
        $pic_Data =   JFactory::getApplication()->input->get("pic_Data", array(), 'ARRAY');
        $model = $this->getModel('files');
        $check_update = false;
        if ($pic_Data['mbulk_copy'] == 'all'){
            if ($pic_Data['gallery_id']=='0'){
                $check_update = $model->updateAltPictures();
            }else{
                $check_update = $model->updateAltPictures(null,null,$pic_Data['gallery_id']);
            }
        }else{
            if ($pic_Data['gallery_id']=='0'){
                if(!empty($pic_Data['pics'])){
                    foreach ($pic_Data['pics'] as $key){
                        $check_update = $model->updateAltPictures($key['pic_id'],$key['pic_name'],null);
                    }
                }
            }else{
                if(!empty($pic_Data['pics'])){
                    foreach ($pic_Data['pics'] as $key){
                        $check_update = $model->updateAltPictures($key['pic_id'],$key['pic_name'],$pic_Data['gallery_id']);
                    }
                }
            }
        }
        if ($check_update){
            $this->exit_status(JText::_('Update success'));
        }
    }

    public function upTitlePictures(){
        $pic_Data =   JFactory::getApplication()->input->get("pic_Data", array(), 'ARRAY');
        $model = $this->getModel('files');
        $check_update = false;
        if ($pic_Data['mbulk_copy'] == 'all'){
            if ($pic_Data['gallery_id']=='0'){
                $check_update = $model->updateTitlePictures(null,null,null);
            }else{
                $check_update = $model->updateTitlePictures(null,null,$pic_Data['gallery_id']);
            }
        }else{
            if ($pic_Data['gallery_id']=='0'){
                if(!empty($pic_Data['pics'])){
                    foreach ($pic_Data['pics'] as $key){
                        $check_update = $model->updateTitlePictures($key['pic_id'],$key['pic_name'],null);
                    }
                }
            }else{
                if(!empty($pic_Data['pics'])){
                    foreach ($pic_Data['pics'] as $key){
                        $check_update = $model->updateTitlePictures($key['pic_id'],$key['pic_name'],$pic_Data['gallery_id']);
                    }
                }
            }
        }
        if ($check_update){
            $this->exit_status(JText::_('Update success'));
        }
    }

    public function renamePicture(){
        $pic_Data =   JFactory::getApplication()->input->get("pic_Data", array(), 'ARRAY');
        $model = $this->getModel('files');
        $check_update = false;
        if(isset($pic_Data)){
            $check_update = $model->renamePictures($pic_Data['pics'][0]['pic_id'],$pic_Data['pics'][0]['pic_name'],$pic_Data['gallery_id']);
        }
        if ($check_update){
            $this->exit_status(JText::_('Update success'));
        }
    }

    public function upCaptionPictures(){
        $pic_Data =   JFactory::getApplication()->input->get("pic_Data", array(), 'ARRAY');
        $model = $this->getModel('files');
        $params = $model->getParamsPicture($pic_Data['pics'][0]['pic_id']);
        $params = (array)json_decode($params->params);
        $check_update = false;
        if (!isset($params['content_custom_title'])){
            if (isset($params[0])){
                unset($params[0]);
            }
            $params['content_custom_title'] = $pic_Data['pics'][0]['pic_caption'];
        }else{
            $params['content_custom_title'] = $pic_Data['pics'][0]['pic_caption'];
        }
        $check_update = $model->updateParamsPicture($params,$pic_Data['pics'][0]['pic_id']);
        if ($check_update){
            $this->exit_status(JText::_('Update success'));
        }
    }
}