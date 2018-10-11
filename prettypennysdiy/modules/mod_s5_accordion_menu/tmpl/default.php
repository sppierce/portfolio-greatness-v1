<?php

/**

 * @version		$Id: default.php 19594 2010-11-20 05:06:08Z ian $

 * @package		Joomla.Site

 * @subpackage	mod_menu

 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.

 * @license		GNU General Public License version 2 or later; see LICENSE.txt

 */



// No direct access.

defined('_JEXEC') or die;

 

// Note. It is important to remove spaces between elements.

//echo '<pre>';

//print_r($list);exit;





$mod_s5_accordionurl = JURI::root().'modules/mod_s5_accordion_menu/';



$doc =& JFactory::getDocument();

$doc->addCustomTag('<link href="'. $mod_s5_accordionurl .'css/s5_accordion_menu.css" rel="stylesheet" type="text/css" media="screen" />');


		 

?>



<div id="s5_accordion_menu">

<div>

 

    <?php  

$x = 0;

foreach ($list as $i => &$item) :

	$class = '';

	if ($item->id == $active_id) {

		$class .= 'current';

	}

	else {

	$class .= 'not_current';

	}

 

	if($x==1 && $item->level==1){

	

		$x = 0;

		

	}

	if($item->level==1){

		if ($class == "current") {

			echo "<h3 id='$class' class='s5_am_toggler'>";

		}

		else {

			echo "<h3 class='s5_am_toggler'>";

		}

	} 

	 if ($item->level!=1) { 

		if ($class == "current") {

			echo "<li id='$class' class='s5_am_inner_li active'>";

		}

		else {

			echo "<li class='s5_am_inner_li'>";

		}

	 }

	// Render the menu item.

	switch ($item->type) :

		case 'separator':

		case 'url':

		case 'component':

			require JModuleHelper::getLayoutPath('mod_s5_accordion_menu', 'default_'.$item->type);

			break;



		default:

			require JModuleHelper::getLayoutPath('mod_s5_accordion_menu', 'default_url');

			break;

	endswitch;

	

	if($item->level==1 && $item->deeper==0){



	  echo "</h3><div class='s5_accordion_menu_element' style='display: none; border:none; overflow: hidden; padding: 0px; margin: 0px'></div>";

	   continue;

	} 

	

	 if($item->level==1 && $item->deeper==1){



	   echo "</h3><div class='s5_accordion_menu_element' style='display: none; border:none; overflow: hidden; padding: 0px; margin: 0px'>";

	  

	} 

 



	//if ($item->level!=1) { 

		 // The next item is deeper.

		if ($item->deeper) {

			echo '<ul class="s5_am_innermenu">';

		}

		// The next item is shallower.

		else if ($item->shallower) {

			//if(!($list[$i+1]->level==1 && $item->deeper==0))

		 	echo '</li>';

			if((@$list[$i+1]) && !(@$list[$i+1]->level==1 && $item->deeper==0)){
				
				 
				
				echo str_repeat('</ul></li>', $item->level_diff);
				
			}else{
				
				  
				
				echo str_repeat('</ul>', $item->level_diff);
				
			}  

				

		}

		// The next item is on the same level.

		else {

		//	if(!($list[$i+1]->level==1 && $item->deeper==0))

			echo '</li>';

		}

		

		if((@$list[$i+1]->level==1 && $item->deeper==0 ) || !(@$list[$i+1])){
		//if((@$list[$i+1]->level==1 && $item->deeper==0 ) ){	
		//if((@$list[$i+1]->level==1 && $item->deeper==0 ) || (!(@$list[$i+1]) ) ){
		 
		 
		 echo "</div>";
		 
		}
		 

	//}

$x++;

endforeach;

?>

  

</div>

</div>