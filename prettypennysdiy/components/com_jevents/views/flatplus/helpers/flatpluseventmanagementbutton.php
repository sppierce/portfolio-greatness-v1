<?php
defined('_JEXEC') or die('Restricted access');

function FlatPlusEventManagementButton($view, $row)
{
   	JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
	?>
	<a href="#my-modal" data-toggle="modal"  data-target="#action_dialogJQ<?php echo $row->rp_id();?>"  title="<?php echo JText::_('JEV_E_EDIT', true); ?>">
		<?PHP echo "<img src='". JURI::root() . "components/".JEV_COM_COMPONENT."/views/flatplus/assets/images/edit.png' alt='".JText::_('JEV_E_EDIT') . "' />"?>

	</a>
	<?php
}