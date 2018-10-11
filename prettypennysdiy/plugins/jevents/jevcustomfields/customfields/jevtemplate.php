<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
JLoader::register('JevJoomlaVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php");


jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package		JEvents.fields
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldJEVTemplate extends JFormFieldList {

    /**
     * The form field type.s
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'JEVTemplate';

    public function getInput() {
        JFactory::getLanguage()->load('plg_jevents_jevcustomfields', JPATH_ADMINISTRATOR);

        return parent::getInput();
    }

    public function getOptions() {
        // Initialize variables.
        $options = array();

        jimport('joomla.filesystem.folder');
        $templates = JFolder::files(__DIR__ . "/templates/", ".xml");
        // only offer extra fields templates if there is more than one available
        if (count($templates) > 0) {

            // this loads the language strings ! BIZZARE!
            JPluginHelper::importPlugin('jevents');

            $options = array();
            $options[] = JHTML::_('select.option', "", JText::_("JEV_SELECT_TEMPLATE"), 'value', 'text');
            foreach ($templates as $template) {
                if ($template == "fieldssample.xml" || $template == "fieldssample16.xml" || $template == "all_fields.xml")
                    continue;
                $options[] = JHTML::_('select.option', $template, ucfirst(str_replace(".xml", "", $template)), 'value', 'text');
            }
        }
        return $options;
    }

}
