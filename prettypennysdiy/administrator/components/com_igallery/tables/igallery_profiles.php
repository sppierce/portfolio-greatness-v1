<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class Tableigallery_profiles extends JTable
{
	function Tableigallery_profiles(& $db)
	{
		parent::__construct('#__igallery_profiles', 'id', $db);
		
		if($fields = $this->getFields())
		{
			foreach ($fields as $name => $v)
			{
				if(property_exists($this, $name))
				{
					if($this->$name == null)
					{
						$this->$name = $v->Default;
					}
				}
			}
		}
	}
	
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_igallery.profile.'.(int)$this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->name;
	}
	
	protected function _getAssetParentId($table = null, $id = null)
	{
		$assetId = null;
		$db		= $this->getDbo();

		$query	= $db->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where('name = '.$db->quote('com_igallery'));
		$db->setQuery($query);
		if($result = $db->loadResult())
		{
			$assetId = (int)$result;
		}
		
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
	
	public function bind($array, $ignore = '')
	{
		if (isset($array['rules']) && is_array($array['rules']))
		{
			jimport('joomla.access.rules');
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}
}
?>
