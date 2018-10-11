<?php
/**
 * Droppics
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barr?re (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.form.formfield' );

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldJutranslation extends JFormField {

    /**
     * Ju Translation input
     */
    protected function getInput() {
        include_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_droppics' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'jutranslation.php');
        return Jutranslation::getInput();
    }

}