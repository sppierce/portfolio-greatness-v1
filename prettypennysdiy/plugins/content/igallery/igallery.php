<?php
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentIgallery extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
        if( JRequest::getCmd('option', null) == 'com_finder' )
        {
            return;
        }

        $lang =& JFactory::getLanguage();
    	$lang->load('com_igallery', JPATH_ADMINISTRATOR);
		
		$view = JRequest::getCmd('view', null);
		$layout = JRequest::getCmd('layout', null);
	
		JRequest::setVar('view', 'category');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('igsource', 'plugin');

        preg_match_all('#\{igallery(.*?)\}#ism',$article->text, $matches);
	
		foreach( $matches[1] as $pluginParams )
		{
            if( preg_match('#.*?\sid=([0-9]+).*?#is', $pluginParams, $uid) )
            {
                JRequest::setVar('iguniqueid',$uid[1]);
            }
            else if(preg_match('#.*?\sid="([0-9]+)".*?#is', $pluginParams, $uidJ15) )
            {
                JRequest::setVar('iguniqueid',$uidJ15[1]);
            }
            else
            {
                JError::raise(2, 400, 'required id');
            }

            if( preg_match('#.*?cid=([0-9]+).*?#is', $pluginParams, $cid) )
            {
                JRequest::setVar('igid',$cid[1]);
            }
            else if(preg_match('#.*?cid="([0-9]+)".*?#is', $pluginParams, $cidJ15) )
            {
                JRequest::setVar('igid',$cidJ15[1]);
            }
            else
            {
                JError::raise(2, 400, 'required category');
            }

            if( preg_match('#.*?pid=([0-9]+).*?#is', $pluginParams, $pid) )
            {
                JRequest::setVar('igpid',$pid[1]);
            }
            else if(preg_match('#.*?pid="([0-9]+)".*?#is', $pluginParams, $pidJ15) )
            {
                JRequest::setVar('igpid',$pidJ15[1]);
            }
            else
            {
                JError::raise(2, 400, 'required profile');
            }

            if( preg_match('#.*?type=([a-z_]+).*?#is', $pluginParams, $type) )
            {
                JRequest::setVar('igtype',$type[1]);
            }
            else if(preg_match('#.*?type="([a-z_]+)".*?#is', $pluginParams, $typeJ15) )
            {
                if($typeJ15[1] == 'classic')
                {
                    $typeJ15[1] = 'category';
                }
                JRequest::setVar('igtype', $typeJ15[1]);
            }
            else
            {
                JError::raise(2, 400, 'required type');
            }

            if( preg_match('#.*?children=([0-9]+).*?#is', $pluginParams, $children) )
            {
                JRequest::setVar('igchild',$children[1]);
            }
            else if(preg_match('#.*?children="([0-9]+)".*?#is', $pluginParams, $childrenJ15) )
            {
                JRequest::setVar('igchild',$childrenJ15[1]);
            }

            if( preg_match('#.*?addlinks=([0-9]+).*?#is', $pluginParams, $addlinks) )
            {
                JRequest::setVar('igaddlinks',$addlinks[1]);
            }

            if( preg_match('#.*?tags=([0-9a-zA-Z,\- ]+).*?#is', $pluginParams, $tags) )
            {
                JRequest::setVar('igtags',$tags[1]);
            }
            else if(preg_match('#.*?tags="([0-9a-zA-Z, ]+)".*?#is', $pluginParams, $tagsJ15) )
            {
                JRequest::setVar('igtags',$tagsJ15[1]);
            }

            if( preg_match('#.*?limit=([0-9]+).*?#is', $pluginParams, $limit) )
            {
                JRequest::setVar('iglimit',$limit[1]);
            }
            else if(preg_match('#.*?limit="([0-9]+)".*?#is', $pluginParams, $limitJ15) )
            {
                JRequest::setVar('iglimit',$limitJ15[1]);
            }
	
			require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/defines.php');
	        require_once(IG_COMPONENT.'/controller.php');
	
	        $controller = new IgController();
	
			ob_start();
			$controller->execute('display');
			$pluginHtml = ob_get_contents();
			ob_end_clean();
	
			$article->text = str_replace('{igallery'.$pluginParams.'}', $pluginHtml, $article->text);
		}
	
		if($view != null)
		{
			JRequest::setVar('view', $view);
		}
	
		if($layout != null)
		{
			JRequest::setVar('layout', $layout);
		}
	
		return true;
	}
	
}