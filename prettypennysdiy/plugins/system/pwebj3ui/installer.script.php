<?php
/**
* @version 3.6.0
* @package PWebJ3UI
* @copyright © 2016 Perfect Web sp. z o.o., All rights reserved. https://www.perfect-web.co
* @license GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
* @author Piotr Moćko
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgsystempwebj3uiInstallerScript
{

    protected $manifest = null;
    protected $old_manifest = null;
    protected $extension = null;

	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter) 
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
	}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		$parent = $adapter->getParent();
        $this->manifest = $parent->getManifest();
        $this->loadExtensionFromManifest();

        if ($route == 'update' || $route == 'uninstall')
        {
            $this->loadExtensionId();
        }

        $this->loadExtensionManifestCache();
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
    {
		if ($route == 'install' || $route == 'discover_install' || $route == 'update')
		{
			if ($route == 'install' || $route == 'discover_install')
			{
				$this->loadExtensionId();
				$this->enableExtension();
			}

			// remove unused files from plugin
			$plugin_path = JPATH_ROOT . '/plugins/system/pwebj3ui/';

			if (JFolder::exists($plugin_path.'media/jui'))
				JFolder::delete($plugin_path.'media/jui');
			if (JFolder::exists($plugin_path.'media/system'))
				JFolder::delete($plugin_path.'media/system');

			if (version_compare(JVERSION, '2.5.15') >= 0)
			{
				if (JFile::exists($plugin_path.'libraries/joomla/http/factory.php'))
					JFile::delete($plugin_path.'libraries/joomla/http/factory.php');

				if (version_compare(JVERSION, '3.0.0') >= 0)
				{
					if (JFolder::exists($plugin_path.'libraries/cms/html'))
						JFolder::delete($plugin_path.'libraries/cms/html');
					if (JFile::exists($plugin_path.'libraries/joomla/http/transport.php'))
						JFile::delete($plugin_path.'libraries/joomla/http/transport.php');

					if (version_compare(JVERSION, '3.1.0') >= 0)
					{
						if (JFolder::exists($plugin_path.'libraries/cms'))
							JFolder::delete($plugin_path.'libraries/cms');
					}

					if (version_compare(JVERSION, '3.7.0') >= 0)
					{
						if (JFolder::exists($plugin_path.'libraries/joomla/oauth2'))
                            JFolder::delete($plugin_path.'libraries/joomla/oauth2');
					}
				}
			}
		}
    }

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter) 
	{
		if (version_compare(JVERSION, '3.0.0') == -1) 
		{
			$this->copyMedia(true);
		}
		else
		{
			if (version_compare(JVERSION, '3.1.4') == -1) 
			{
				// Update Bootstrap to version 2.3.2
				$this->copyBootstrap();
			}
		}
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter) 
	{
		if (version_compare(JVERSION, '3.0.0') == -1) 
		{
			$this->copyMedia();
		}
		elseif (version_compare(JVERSION, '3.1.4') == -1) 
		{
			// Update Bootstrap to version 2.3.2
			$this->copyBootstrap();
		}
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter) 
	{
		if (version_compare(JVERSION, '3.0.0') == -1) 
		{
			// remove JUI
			if (JFolder::exists(JPATH_ROOT.'/media/jui'))
			{
				JFolder::delete(JPATH_ROOT.'/media/jui');
			}

			// restore previous JUI if there is a copy
			if (JFolder::exists(JPATH_ROOT.'/media/jui-bak'))
			{
				JFolder::move(JPATH_ROOT.'/media/jui-bak', JPATH_ROOT.'/media/jui');
			}
		}
		elseif (version_compare(JVERSION, '3.1.4') == -1) 
		{
			// Restore Bootstrap version 2.1.0
			$this->restoreBootstrap();
		}
	}

	protected function copyMedia($backup = false)
	{
		$plugin_path = JPATH_ROOT.'/plugins/system/pwebj3ui/media/';
		
		if (JFolder::exists(JPATH_ROOT.'/media/jui'))
		{
			if ($backup)
			{
				JFolder::move(JPATH_ROOT.'/media/jui', JPATH_ROOT.'/media/jui-bak');
				JFolder::move($plugin_path.'jui', JPATH_ROOT.'/media/jui');
			}
			else 
			{
				JFolder::copy($plugin_path.'jui', JPATH_ROOT.'/media/jui', '', true);
			}
		}
		else 
		{
			JFolder::move($plugin_path.'jui', JPATH_ROOT.'/media/jui');
		}
		
		JFolder::copy($plugin_path.'system', JPATH_ROOT.'/media/system', '', true);
	}

	protected function copyBootstrap()
	{
		$plugin_path = JPATH_ROOT.'/plugins/system/pwebj3ui/media/jui/js/';
		$media_path = JPATH_ROOT.'/media/jui/js/';
		
		if (!JFile::exists($media_path.'bootstrap.min.js.bak'))
		{
			JFile::copy($media_path.'bootstrap.js', $media_path.'bootstrap.js.bak');
			JFile::copy($media_path.'bootstrap.min.js', $media_path.'bootstrap.min.js.bak');
		}
		
		JFile::copy($plugin_path.'bootstrap.js', $media_path.'bootstrap.js');
		JFile::copy($plugin_path.'bootstrap.min.js', $media_path.'bootstrap.min.js');
	}

	protected function restoreBootstrap()
	{
		$media_path = JPATH_ROOT.'/media/jui/js/';
		
		if (JFile::exists($media_path.'bootstrap.min.js.bak'))
		{
			JFile::move($media_path.'bootstrap.js.bak', $media_path.'bootstrap.js');
			JFile::move($media_path.'bootstrap.min.js.bak', $media_path.'bootstrap.min.js');
		}
	}

	protected function enableExtension()
	{
		if (is_object($this->extension) && $this->extension->extension_id)
		{
			$this->extension->enabled = 1;

			if ($this->extension instanceof JTableExtension)
			{
				$this->extension->store();
			}
			else
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true)
					->update('#__extensions')
					->set($db->quoteName('enabled') . ' = 1')
					->where($db->quoteName('extension_id') . ' = ' . (int) $this->extension->extension_id);

				$db->setQuery($query);
				try
				{
					$db->execute();
				}
				catch (Exception $e)
				{

				}
			}
		}
	}

	/**
     * Get Akeeba Release System update stream id
     *
     * @return int
     */
    protected function getUpdateStreamId()
    {
        return isset($this->manifest->perfect_update_id) ? (int) $this->manifest->perfect_update_id : 0;
    }

    protected function loadExtensionFromManifest()
    {
        if (!isset($this->extension) || empty($this->extension))
        {
            $this->extension = JTable::getInstance('extension');

            $this->extension->type = strtolower((string) $this->manifest->attributes()->type);
            $this->extension->folder = isset($this->manifest->attributes()->group) ? strtolower((string) $this->manifest->attributes()->group) : '';
            $this->extension->client_id = 0;

            if ($cname = (string) $this->manifest->attributes()->client)
            {
                // Attempt to map the client to a base path
                $client = JApplicationHelper::getClientInfo($cname, true);
                if ($client !== false)
                {
                    $this->extension->client_id = $client->id;
                }
            }

            $type = $this->extension->type;
            if ($type == 'component')
            {
                $name = strtolower(JFilterInput::getInstance()->clean((string) $this->manifest->name, 'cmd'));
                if (substr($name, 0, 4) == 'com_')
                {
                    $this->extension->element = $name;
                }
                else
                {
                    $this->extension->element = 'com_' . $name;
                }
            }
            elseif ($type == 'package')
            {
                $this->extension->element = 'pkg_' . strtolower(JFilterInput::getInstance()->clean((string) $this->manifest->packagename, 'cmd'));
            }
            elseif ($type == 'module' || $type == 'plugin')
            {
                if (count($this->manifest->files->children()))
                {
                    foreach ($this->manifest->files->children() as $file)
                    {
                        if ((string) $file->attributes()->$type)
                        {
                            $this->extension->element = strtolower((string) $file->attributes()->$type);
                            break;
                        }
                    }
                }
            }

            if (!$this->extension->element)
            {
                $this->extension->element = strtolower(str_replace('InstallerScript', '', __CLASS__));
            }
        }
    }

    protected function loadExtensionId()
    {
        if (!isset($this->extension->extension_id) || empty($this->extension->extension_id))
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                    ->select('extension_id')
                    ->from('#__extensions')
                    ->where(array(
                $db->quoteName('type') . ' = ' . $db->quote($this->extension->type),
                $db->quoteName('element') . ' = ' . $db->quote($this->extension->element),
                $db->quoteName('folder') . ' = ' . $db->quote($this->extension->folder),
                $db->quoteName('client_id') . ' = ' . $db->quote($this->extension->client_id)
            ));

            $db->setQuery($query);
            try
            {
                $this->extension->extension_id = (int) $db->loadResult();
            }
            catch (Exception $e)
            {
                $this->extension->extension_id = 0;
            }
        }

        return ($this->extension->extension_id > 0);
    }

    protected function loadExtensionManifestCache()
    {
        if (!isset($this->old_manifest) || empty($this->old_manifest))
        {
            jimport('joomla.registry.registry');

            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                    ->select('manifest_cache')
                    ->from('#__extensions');

            if ($this->extension->extension_id)
            {
                $query->where($db->quoteName('extension_id') . ' = ' . (int) $this->extension->extension_id);
            }
            else
            {
                $query->where(array(
                    $db->quoteName('type') . ' = ' . $db->quote($this->extension->type),
                    $db->quoteName('element') . ' = ' . $db->quote($this->extension->element),
                    $db->quoteName('folder') . ' = ' . $db->quote($this->extension->folder),
                    $db->quoteName('client_id') . ' = ' . $db->quote($this->extension->client_id)
                ));
            }

            $db->setQuery($query);
            try
            {
                $manifest_cache = $db->loadResult();
            }
            catch (Exception $e)
            {
                $manifest_cache = null;
            }

            $this->old_manifest = new JRegistry($manifest_cache);
        }
    }

    protected function getUpdateSites()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('us.update_site_id AS id, ' . (version_compare(JVERSION, '3.2.2', '>=') ? 'us.extra_query' : 'us.location') . ' AS server'
                        . ', e.type, e.element, e.folder, e.client_id AS client')
                ->from('#__update_sites_extensions AS ue')
                ->join('LEFT', '#__extensions AS e ON ue.extension_id = e.extension_id')
                ->join('INNER', '#__update_sites AS us ON us.update_site_id = ue.update_site_id')
                ->where('us.location LIKE ' . $db->quote('https://www.perfect-web.co/index.php?option=com_ars&view=update&task=stream&format=xml&id=%'));

        $db->setQuery($query);
        try
        {
            $update_sites = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            $update_sites = null;
        }

        return $update_sites ? $update_sites : array();
    }

    protected function updateUpdateSite($update_site_id, $url_query, $version = null, $dlid = null)
    {
        $db = JFactory::getDBO();

        $update_site = new stdClass();
        $update_site->update_site_id = $update_site_id;

        //parse url of extra_query ( basically extracting vars )
        $url = parse_url($url_query);

        if (version_compare(JVERSION, '3.2.2', '>='))
        {
            $url_query = isset($url['path']) ? $url['path'] : '';
        }
        else
        {
            $url_query = isset($url['query']) ? $url['query'] : '';
        }

        parse_str($url_query, $url_vars);

        if ($version !== null)
            $url_vars['version'] = $version;

        $url_vars['jversion'] = JVERSION;
        $url_vars['host'] = JUri::root();

        if ($dlid !== null)
        {
            if (isset($url_vars['dlid']) AND $url_vars['dlid'] != $dlid)
            {
                // purge updates cache after changing Download ID
                $query = $db->getQuery(true)
                        ->delete('#__updates')
                        ->where('update_site_id = ' . (int) $update_site_id);
                $db->setQuery($query);
                try
                {
                    $db->execute();
                }
                catch (Exception $e)
                {
                    
                }
            }
            $url_vars['dlid'] = $dlid;
        }

        if (version_compare(JVERSION, '3.2.2', '>='))
        {
            $url['path'] = http_build_query($url_vars);
            $update_site->extra_query = $url['path'];
        }
        else
        {
            $url['query'] = http_build_query($url_vars);
            $update_site->location = $url['scheme'] . '://' . $url['host'] . $url['path'] . '?' . $url['query'];
        }

        try
        {
            return $db->updateObject('#__update_sites', $update_site, 'update_site_id');
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    protected function createUpdateSite($name, $version = null, $dlid = null)
    {
        if (!$this->loadExtensionId() || !($update_stream_id = $this->getUpdateStreamId()))
        {
            return false;
        }

        $db = JFactory::getDBO();

        $update_site = new stdClass();
        $update_site->name = $name;
        $update_site->type = 'extension';
        $update_site->enabled = 1;
        $update_site->location = 'https://www.perfect-web.co/index.php?option=com_ars&view=update&task=stream&format=xml&id=' . $update_stream_id;

        $url_query = array(
            'version' => $version ? $version : '1.0.0',
            'jversion' => JVERSION,
            'host' => JUri::root()
        );
        if ($dlid !== null)
            $url_query['dlid'] = $dlid;

        if (version_compare(JVERSION, '3.2.2', '>='))
        {
            $update_site->extra_query = http_build_query($url_query);
        }
        else
        {
            $update_site->location .= http_build_query($url_query);
        }

        try
        {
            $db->insertObject('#__update_sites', $update_site, 'update_site_id');

            $update_site_extension = new stdClass();
            $update_site_extension->update_site_id = $update_site->update_site_id;
            $update_site_extension->extension_id = $this->extension->extension_id;
            $db->insertObject('#__update_sites_extensions', $update_site_extension, 'update_site_id');
        }
        catch (Exception $e)
        {
            return false;
        }
        return true;
    }
}