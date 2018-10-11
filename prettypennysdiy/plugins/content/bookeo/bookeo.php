<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.event.plugin' );
class plgContentBookeo extends JPlugin
{
	
public function onContentPrepare($context, &$row, &$params, $page = 0)
{

	$db =& JFactory::getDBO();
	// simple performance check to determine whether bot should process further
	if ( JString::strpos( $row->text, 'bookeo' ) === false ) {
		return true;
	}

	// Get plugin info
	$plugin =& JPluginHelper::getPlugin('content', 'bookeo');
	
 	// expression to search for
 	$regex = '/{bookeo}/i';
 	
 	
 	$regex_type_2 =  '/{bookeo:\s*.*?}/i';
 	
 	$pluginParams = $this->params;

	// check whether plugin has been unpublished
	if ( !$pluginParams->get( 'enabled', 1 ) ) {
		$row->text = preg_replace( $regex, '', $row->text );
		return true;
	}
	//get widget code
	$widgetcode=$pluginParams->get('widgetcode','ABCDEFG');
 	// find all instances of plugin and put in $matches
	$first_match = preg_match_all( $regex, $row->text, $first_matches );
	$second_match = preg_match_all( $regex_type_2, $row->text, $second_matches );
	// Number of plugins
 	$first_count = count( $first_matches[0] );
	$second_count = count( $second_matches[0] );
 	// plugin only processes if there are any instances of the plugin in the text
 	if ( $first_count && $first_match ) {
		// Get plugin parameters
	 	$style	= $pluginParams->def( 'style', 'raw' );
 		$this->_plgContentProcessBookeo1( $row, $first_matches, $first_count, $regex, $style , $widgetcode);
	}
	else if($second_count && $second_match)
	{
		$style	= $pluginParams->def( 'style', 'raw' );
		$this->_plgContentProcessBookeo( $row, $second_matches, $second_count, $regex_type_2, $style , $widgetcode);
	}
}
private function _plgContentProcessBookeo ( &$row, &$matches, $count, $regex, $style='raw', $widgetcode )
{
	for ( $i=0; $i < $count; $i++ )
	{
		$load = str_replace( 'bookeo:', '', $matches[0][$i] );
 		$load = str_replace( '{', '', $load );
 		$load = str_replace( '}', '', $load );
 		$load = trim( $load );
			
		$regexid	= '/[^\d]/i';
		$regexstyle	=	'/[\d]|bookeo|{|}/i';
		
 		$id	= preg_replace( $regexid, '', $matches[0][$i] );
 		
 		$realstyle	= preg_replace( $regexstyle, '', $matches[0][$i] );
 		trim($realstyle);
 		
 		if($realstyle==' '){
 			$realstyle	= $style;
 		}
 		$text = $row->text;
 		
 		$widgetcode1=$widgetcode.'&'.$load;
		$module		= $this->_plgContentFetchModule( $id, $realstyle, $widgetcode1 );
		
		$row->text 	= str_replace($matches[0][$i], $module, $row->text );
 	}

  	// removes tags without matching module positions
	$row->text = preg_replace( $regex, '', $row->text );
}

private function _plgContentProcessBookeo1 ( &$row, &$matches, $count, $regex, $style='raw', $widgetcode )
{
	for ( $i=0; $i < $count; $i++ )
	{
		$regexid	= '/[^\d]/i';
		$regexstyle	=	'/[\d]|bookeo|{|}/i';

		$id	= preg_replace( $regexid, '', $matches[0][$i] );
			
		$realstyle	= preg_replace( $regexstyle, '', $matches[0][$i] );
		trim($realstyle);
			
		if($realstyle==' '){
			$realstyle	= $style;
		}
			
		$module		= $this->_plgContentFetchModule( $id, $realstyle, $widgetcode );

		$row->text 	= str_replace($matches[0][$i], $module, $row->text );
	}

	// removes tags without matching module positions
	$row->text = preg_replace( $regex, '', $row->text );
}

private function _plgContentFetchModule( $mid, $style='raw' ,$widgetcode )
{
	//global $mainframe, $Itemid;
	$widgetcode=str_replace("&amp;","&",$widgetcode);
	return '<script type="text/javascript" src="https://bookeo.com/widget.js?a='.$widgetcode.'"></script>';
}
}
