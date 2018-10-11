<?php
require dirname(__FILE__).'/vendor/autoload_52.php';

/**
 * @copyright Copyright (C) 2012 TrekkSoft AG. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

if (!defined('ENT_HTML401')) {
    define('ENT_HTML401', 0);
}

/**
 * TrekkSoft plugin.
 *
 * @package Joomla.Plugin
 * @subpackage Content.trekksoft
 */
class plgContentTrekksoft extends JPlugin
{
    /**
     * @return string
     */
    public function getAccountName()
    {
        return (string)$this->params->get('account');
    }
    
    /**
     * @return string
     */
    public function getPrimaryDomain()
    {
        return (string)$this->params->get('domain');
    }
    
    /**
     * @return string
     */
    public function getLanguage()
    {
        return (string)$this->params->get('language');
    }

    public function getTargetUrl()
    {
        return '//'.$this->getHost().'/'.$this->getLanguage().'/api/public';
    }

    public function getHost()
    {
        if($this->getPrimaryDomain()){
            return $this->getPrimaryDomain();
        }

        return $this->getAccountName().'.trekksoft.'.($_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? 'dev' : 'com');
    }

    /**
     * Add JavaScript file
     */
    function onBeforeRender()
    {
        $accountName = $this->getAccountName();
        $primaryDomain = $this->getPrimaryDomain();
        
        if (empty($accountName) && empty($primaryDomain)) {
            return;
        }
        
        $document = JFactory::getDocument();
        $document->addScript($this->getTargetUrl());
    }


    /**
     * Substitute TrekkSoft wordpress-like smart code.
     */
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        if ($app->isSite()) { //render tag on frontend only

            $callback = new MyCallback($this);
            $body = preg_replace_callback(
                '/\[trekksoft(.+?)\]/',
                array($callback, 'callback'),
                JResponse::getBody()
            );

            JResponse::setBody($body);
        }
    }
}

class MyCallback {
    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function callback($matches) {
        $plugin = $this->plugin;

        $widgetGenerator = new TrekkSoft_Widget_Generator();
        $accountName = $plugin->getAccountName();
        $primaryDomain = $plugin->getPrimaryDomain();
        $defaultOptions = $widgetGenerator->getDefaultOptions()+array('language'=>$plugin->getLanguage());
        $host = $plugin->getHost();

        if (empty($accountName) && empty($primaryDomain)) {
            return 'Please go to "Admin" > "Extensions" > "Plug-in Manager" > "TrekkSoft" and configure your account.';
        } else {
            try {
                $attr = html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML401);
                $optionsAsXml = new SimpleXMLElement('<element ' . $attr . ' />');
            } catch (Exception $e) {
                return $matches[0];
            }

            $options = $defaultOptions;
            foreach ($optionsAsXml->attributes() as $key => $value) {
                $options[$key] = (string)$value;
            }

            return $widgetGenerator->generateEmbedCode($host, $options);
        }
    }
}
