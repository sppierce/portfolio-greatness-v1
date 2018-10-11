<?php
/**
* @version 1.6.1
* @package DJ Like Box
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
*
*
* DJ Like Box is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Like Box is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Like Box. If not, see <http://www.gnu.org/licenses/>.
*
*/

// no direct access
defined('_JEXEC') or die ('Restricted access');

class modDJLikeBoxHelper {
	
	static function getStyleSheetPath (&$params) {
		
		jimport('joomla.filesystem.file');
		
		$file = JPATH_BASE . DS . 'modules' . DS . 'mod_dj_likebox' . DS . 'css' . DS . 'dj_likebox.css';
		
		if(!JFile::exists($file)) return;
		
		$fileContent =  JFile::read($file);
		
		if ($fileContent != $params->get('css')) {
				  		
			JFile::write($file, $params->get('css'));
		}
		
		$cssPath = JURI::base() . 'modules/mod_dj_likebox/css/dj_likebox.css';
	  	$cssCounter = filemtime($file);
	  	
		$cssPath = $cssPath.'?'.$cssCounter;
		
		return $cssPath;
	}
	
}