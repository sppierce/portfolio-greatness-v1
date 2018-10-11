<?php
/**
* @version 3.6.0
* @package PWebJ3UI
* @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. http://www.perfect-web.co
* @license GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
* @author Piotr Moćko
*/

// no direct access 
defined('_JEXEC') or die ('Restricted access');

define('JPATH_LIBRARIES_PWEB', dirname(__FILE__) . '/libraries');

class plgSystemPwebj3ui extends JPlugin
{
	protected $_active = null;
	protected $_isMobile = null;
	protected $_removeIncludes = array();
	protected $_removeRegExIncludes = array();

	public function onAfterInitialise()
	{
		// disable plugin on J! < 2.5.5
		if (version_compare(JVERSION, '2.5.5') == -1) 
		{
			$this->_active = false;
			return;
		}
		
		$load_jhttp = $this->params->get('load_jhttp', 1);
		$verify_peer = $this->params->get('verify_peer', 1);
		if (!defined('PWEB_JHTTP_VERIFY_PEER'))
			define('PWEB_JHTTP_VERIFY_PEER', $verify_peer ? true : false);

		// load libraries
        if (version_compare(JVERSION, '3.7.0') == -1)
        {
            // fixed JOAuth2Client
            JLoader::import('joomla.oauth2.client', JPATH_LIBRARIES_PWEB);
            JLoader::register('JOAuth2Client', JPATH_LIBRARIES_PWEB . '/joomla/oauth2/client.php', false);
        }

		if (version_compare(JVERSION, '3.6.0') == -1 OR !$verify_peer OR $load_jhttp)
		{
            if (version_compare(JVERSION, '3.0.0') == -1)
            {
                JLoader::import('joomla.http.transport', JPATH_LIBRARIES_PWEB);
                JLoader::register('JHttpTransport', JPATH_LIBRARIES_PWEB.'/joomla/http/transport.php', true);
            }
            // fixed HTTP transport
            JLoader::import('joomla.http.transport.curl', JPATH_LIBRARIES_PWEB);
			JLoader::register('JHttpTransportCurl', JPATH_LIBRARIES_PWEB.'/joomla/http/transport/curl.php', true);

            JLoader::import('joomla.http.transport.socket', JPATH_LIBRARIES_PWEB);
            JLoader::register('JHttpTransportSocket', JPATH_LIBRARIES_PWEB.'/joomla/http/transport/socket.php', true);

            JLoader::import('joomla.http.transport.stream', JPATH_LIBRARIES_PWEB);
            JLoader::register('JHttpTransportStream', JPATH_LIBRARIES_PWEB.'/joomla/http/transport/stream.php', true);

            if (version_compare(JVERSION, '3.1.0') == -1)
            {
                // add JResponseJson
                JLoader::import('cms.response.json', JPATH_LIBRARIES_PWEB);
                JLoader::register('JResponseJson', JPATH_LIBRARIES_PWEB.'/cms/response/json.php', false);

                if (version_compare(JVERSION, '3.0.0') == -1)
                {
                    // add JHtml classes: bootstrap, dropdown, formbehavior, jquery, sortablelist
                    JLoader::registerPrefix('J', JPATH_LIBRARIES_PWEB . '/cms');

                    if (version_compare(JVERSION, '2.5.15') == -1)
                    {
                        // add JHttpFactory
                        JLoader::import('joomla.http.factory', JPATH_LIBRARIES_PWEB);
                        JLoader::register('JHttpFactory', JPATH_LIBRARIES_PWEB.'/joomla/http/factory.php', false);
                    }
                }
            }
		}

		// check where to load plugin
		$app = JFactory::getApplication();
		$load = $this->params->get('enable', 1);
		$isAdmin = ($app->isAdmin() OR strpos(JPATH_BASE, 'administrator') !== false);
		if ( ($load == 1 AND $isAdmin) OR ($load == 2 AND !$isAdmin) ) {
			$this->_active = false;
			return;
		}

		$doc = JFactory::getDocument();
		if ($doc->getType() != 'html') {
			$this->_active = false;
			return;
		}
		
		// enable plugin
		$this->_active = true;
		
		// detect browser
		jimport('joomla.environment.browser');
		$this->_isMobile = JBrowser::getInstance()->isMobile();
		
		// load jQuery
		$load = $this->params->get('load_jquery', 0);
		if ($load == 1 OR ($load == 2 AND $this->_isMobile == false) OR ($load == 3 AND $this->_isMobile == true)) 
			JHtml::_('jquery.framework');
		
		// load jQuery UI
		$load = $this->params->get('load_jquery_ui', 0);
		if ($load == 1 OR ($load == 2 AND $this->_isMobile == false) OR ($load == 3 AND $this->_isMobile == true)) 
			JHtml::_('jquery.ui');
		
		// load Bootstrap JS
		$load = $this->params->get('load_bootstrap_js', 0);
		if ($load == 1 OR ($load == 2 AND $this->_isMobile == false) OR ($load == 3 AND $this->_isMobile == true)) 
			JHtml::_('bootstrap.framework');
		
		// load Bootstrap CSS
		$load = $this->params->get('load_bootstrap_css', 0);
		if ($load == 1 OR ($load == 2 AND $this->_isMobile == false) OR ($load == 3 AND $this->_isMobile == true)) 
			JHtml::_('bootstrap.loadCss');
	}
	
	public function onAfterRender() 
	{
		$app = JFactory::getApplication();
		
		if ($this->_active === false) {
			return;
		}
		elseif ($this->_active === null)
		{
			// check where to load plugin
			$load = $this->params->get('enable', 1);
			$isAdmin = ($app->isAdmin() OR strpos(JPATH_BASE, 'administrator') !== false);
			if ( ($load == 1 AND $isAdmin) OR ($load == 2 AND !$isAdmin) )
				return;
			
			$doc = JFactory::getDocument();
			if ($doc->getType() != 'html')
				return;
		}
		
		// remove files from list
		if ($this->params->get('remove', 0))
		{
			$this->_removeIncludes = explode(',', trim((string)$this->params->get( $this->_isMobile ? 'remove_mobile_includes' : 'remove_includes' )));
		}
		
		// remove jquery files: jquery-1.10.1.js, jquery-1.10.1.min.js, jquery.js, jquery.min.js
		$remove = $this->params->get('remove_jquery', 0);
		if ($remove == 1 OR ($remove == 2 AND $this->_isMobile == false) OR ($remove == 3 AND $this->_isMobile == true)) 
		{
			$this->_removeIncludes[] = 'jquery.js';
			$this->_removeIncludes[] = 'jquery.min.js';
			$this->_removeIncludes[] = 'jquery-latest.js';
			$this->_removeIncludes[] = 'jquery-latest.min.js';
			$this->_removeRegExIncludes[] = '/jquery-\d+(\.\d+)*(\.min)?\.js/i';
		}
		
		// perform removal
		if (count($this->_removeIncludes))
		{
			if (version_compare(JVERSION, '3.2.0') == -1) 
			{
				JResponse::setBody( preg_replace_callback(
					'/<script\s[^>]*?\\s*src\\s*=\\s*("[^"]*"|\'[^\']\'|[^\\s>]*).+?<\/script>/is', 
					array(&$this, '_removeIncludesCallback'), 
					JResponse::getBody())
				);
			}
			else 
			{
				$app->setBody( preg_replace_callback(
					'/<script\s[^>]*?\\s*src\\s*=\\s*("[^"]*"|\'[^\']\'|[^\\s>]*).+?<\/script>/is', 
					array(&$this, '_removeIncludesCallback'), 
					$app->getBody())
				);
			}
		}
	}
	
	protected function _removeIncludesCallback($matches) 
	{
		if (strpos($matches[1], 'media/jui/js/') === false AND strpos($matches[1], 'media/system/js/') === false) 
		{
			foreach ($this->_removeIncludes as $include)
				if (!empty($include) AND strpos($matches[1], $include) !== false)
					return '';
			
			foreach ($this->_removeRegExIncludes as $include)
				if (preg_match($include, $matches[1]) === true)
					return '';
		}
		return $matches[0];
	}
}
