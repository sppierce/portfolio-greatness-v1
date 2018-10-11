<?php
/**
 * @package     pwebbox
 * @version 	2.0.0
 *
 * @copyright   Copyright (C) 2015 Perfect Web. All rights reserved. http://www.perfect-web.co
 * @license     GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

if (file_exists(dirname(__FILE__) . '/form/perfectinstaller.php'))
    require_once dirname(__FILE__) . '/form/perfectinstaller.php';
elseif (file_exists(JPATH_ROOT . '/modules/mod_pwebbox/perfectinstaller.php'))
    require_once JPATH_ROOT . '/modules/mod_pwebbox/perfectinstaller.php';
else
    return false;

class plgEverything_in_everywayCookie_policyInstallerScript extends PerfectInstaller
{

    /**
     * Called on installation
     *
     * @param   JAdapterInstance $adapter The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install(JAdapterInstance $adapter)
    {

        parent::install($adapter);
        $this->createArticleWithCookiePolicy();
    }

    protected function createArticleWithCookiePolicy()
    {

        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);

        $query->select($db->quoteName(array('a.id')))
            ->from($db->quoteName('#__content', 'a'))
            ->where($db->quoteName('a.title') . ' = ' . $db->quote('Cookie Policy')); // Quoted query.

        $db->setQuery($query);
        $article = $db->loadObject();

        if ($article == null) {
            $table = JTable::getInstance('Content', 'JTable', array());

            $html = "<h1 class='uk-article-title'>Cookies</h1>"
                . "<div><p style='text-align: justify;'>A Cookie file is – according to Wikipedia - a small piece of data sent from a website and stored in a user's web browser while a user is browsing a website. When the user browses the same website in the future, the data stored in the cookie can be retrieved by the website to notify the website of the user's previous activity. Cookies were designed to be a reliable mechanism for websites to remember the state of the website or activity the user had taken in the past. This can include clicking particular buttons, logging in, or a record of which pages were visited by the user even months or years ago. More information on that topic can be find on Wikipedia.</p>"
                . "<p>Purposes of storage and gaining access to cookies:</p><ul>"
                . "<li>Website personalisation (for example: saving font size, sight challenged version of website or template version)</li>"
                . "<li>Saving data or user’s decisions (for example: no need to enter login and password on every website, remembering login during the next visit, keeping information on products added to cart)</li>"
                . "<li>Social websites integration (for example: displaying your friends, fans or post publishing on Facebook or Google+ directly from the website)</li>"
                . "<li>Adjusting adverts that are display on the website</li>"
                . "<li>Creating website’s statistics and flow statistics between different websites</li>"
                . "</ul><p style='text-align: justify;'>Below one can find links to sources showing how to set the conditions of storage and gaining access to cookies already stored in user’s device for the most popular internet browsers.</p>"
                . "<ul><li>Firefox</li><li>Chrome</li><li>Internet Explorer</li><li>Opera</li><li>Safari</li></ul>"
                . "<p style='text-align: justify;'>Due to vast number of technological solutions it is not possible to publish clear guidelines how to set the conditions of storage and gaining access to cookies using settings of all available devices and software installed on them. However, in most cases, select \"Tools\" or \"Settings\" and there find the section that corresponds to the configuration settings for cookies or for the management of privacy. Detailed information is usually provided by the manufacturer of the device or browser in a manual or on their website.</p>"
                . "</div>";


            $data = array(
                'catid' => 1,
                'title' => 'Cookie Policy',
                'introtext' => '',
                'fulltext' => $html,
                'state' => 1,
            );

            if (!$table->bind($data)) {
                $this->setError($table->getError());
                return false;
            }

            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }

            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }
        }
    }
}