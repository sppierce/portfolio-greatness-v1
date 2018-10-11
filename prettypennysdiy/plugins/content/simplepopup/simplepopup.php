<?php
/**
 * Simple PopUp - Joomla Plugin
 * 
 * @package    Joomla
 * @subpackage Plugin
 * @author Anders Wasén
 * @link http://wasen.net/
 * @license		GNU/GPL, see LICENSE.php
 * plg_simplefilegallery is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.plugin.plugin');

define('SPU_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'simplepopup');

class plgContentSimplePopUp extends JPlugin
{
   /**
    * Constructor
    *
    * For php4 compatability we must not use the __constructor as a constructor for
    * plugins because func_get_args ( void ) returns a copy of all passed arguments
    * NOT references.  This causes problems with cross-referencing necessary for the
    * observer design pattern.
    */
	
	
    function __construct( &$subject, $config )
    {

			parent::__construct( $subject, $config );
 
            // load plugin parameters
            $this->_plugin = JPluginHelper::getPlugin( 'content', 'simplepopup' );
            //$this->params = new JParameter( $this->_plugin->params );
			
    }
 
	function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		JPlugin::loadLanguage( 'plg_content_simplepopup', JPATH_ADMINISTRATOR );		//Load the plugin language file - not in contructor in case plugin called by third party components
		$application = JFactory::getApplication();

		$this->spuindex = -1;
		$this->spuindexinit = 0;
		
		if (version_compare(JVERSION, '3.0', 'ge')) {
		//echo "VERSION 3!";
			$this->popupjqueryJ3 = true;
		} else {
			$this->popupjqueryJ3 = false;
			//echo "VERSION OLDER";
		}

		$regex = "#{simplepopup\b(.*?)\}(.*?){/simplepopup}#s";
		
		$article->text = preg_replace_callback( $regex, array('plgContentSimplePopUp', 'render'), $article->text, -1, $count );
		
	}
	
	
	function render( &$matches )
    {
		
		$html = '';
		
		$this->spuindex += 1;
		
		$spu_debug = $this->params->get( 'spu_debug', '0' );
		$spu_showpdfdl = $this->params->get( 'spu_showpdfdl', '0' );
		
		if ($spu_debug === '1') {
			echo '<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
			echo '~~~~~~~~~~~~~ Simple PopUp - DEBUGGING ~~~~~~~~~~~~~<br/>';
			echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
			$tmp = '';
			$ix = 0;
			do {
				if (!isset($matches[$ix])) break;
				$matches[$ix] = trim($matches[$ix]);
				$tmp = $matches[$ix];
				echo '<br/>['.$ix.']='.$tmp;
				$ix++;
			} while (strlen($tmp) > 0);
			
			echo '<br/>spuindex = ' . $this->spuindex.'<br/>';
		}
		
		// Message is always in index zero. Remove brackets with RegExp
		$bracket_reg = '/{+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^}]*\/*\s*}+/i';
		$this->popupmsg = preg_replace( $bracket_reg, '', $matches[0] );
		
		// Clear all "session" vars
		$this->popup = 'true';
		$this->popupurl = '';
		$this->popupvideo = '';
		$this->popupautoplay = '';
		$this->popupmulti = 'false';
		$this->popupname = '';
		$this->popupanchor = '';
		$this->popuprel = '';
		$this->popuphidden = '';
		$this->popuptitle = '';
		$this->resizeOnWindowResize = 'true';
		$this->popupcookie = '';
		$this->popuptextalign = '';
		$this->popupwidth = '';
		$this->popupheight = '';
		$this->popupautodimensions = '';
		$this->popupscrolling = 'auto';
		$this->popupcssclass = 'spu_content';
		$this->popuparticleid = 0;
		$this->popuptitleposition = 'outside';
		$this->popuphideonoverlayclick = 'false';
		$this->popuphideoncontentclick = 'false';
		$this->popupclosebutton = 'true';
		$this->popuptransitionin = 'elastic';
		$this->popuptransitionout = 'elastic';
		$this->popupspeedin = 300;
		$this->popupspeedout = 300;
		$this->popupoverlayshow = 'true';
		$this->popupoverlayopacity = 0.3;
		$this->popupoverlaycolor = '#666';
		$this->popupjqueryloading = '';
		$this->popupboxstyle = 'fancybox';
		$this->popupmime = '';
		$this->delayopen = 0;
		$this->delayclose = 0;
		
		// Check if there are any other parameters
		if (isset($matches[1])) {
			// Get all params and put into array
			
			// Trim away any leading or trailing spaces
			$matches[1] = trim($matches[1]);
			
			$tmp = '';
			$container = '';
			// Now loop through parameter string to fix any quoted strings with spaces and replae them with [[sp]] to not mess up the regexp array
			for ($i = 0; $i < strlen($matches[1]); $i++) {
				$tmp = substr($matches[1], $i, 1);
				// Match any quote you may find
				if ($tmp === '"' || $tmp === '\'' || ord($tmp) == 96 || ord($tmp) == 180 || ord($tmp) == 239 || ord($tmp) == 145 || ord($tmp) == 146 || ord($tmp) == 147 || ord($tmp) == 148) {
					// Change the quote to always be a regular double-quote
					$tmp = '"';
					$container .= $tmp;
					// Keep going until we find the end quote
					for ($j = $i+1; $j < strlen($matches[1]); $j++) {
						// Raise $i also for each new char picked from the string
						$i++;
						$tmp = substr($matches[1], $j, 1);
						// Look for the closing quote and add a regular double-quote and then bail out
						if ($tmp === '"' || $tmp === '\'' || ord($tmp) == 96 || ord($tmp) == 180 || ord($tmp) == 239 || ord($tmp) == 145 || ord($tmp) == 146 || ord($tmp) == 147 || ord($tmp) == 148) {
							$tmp = '"';
							$container .= $tmp;
							break;
						}
						// Swap any space in a quoted string to [[sp]] instead, change back below in the title
						$tmp = str_replace(' ', '[[sp]]', $tmp);
						$container .= $tmp;
					}
				} else {
					// This is outside of any quotes
					$container .= $tmp;
				}
			}
			// Not sure where those 194 chars comes from but better get rid of them!
			$container = str_replace(chr(194), '', $container);
			//echo "NOW WE GOT: [".$container."]<br/>";
			
			preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $container, $spuparams);
			if ($spu_debug === '1') print_r($spuparams);
			
			
			//$spuparams = explode(' ', $matches[1]);
			for ($ix = 0; $ix < count($spuparams[0]); $ix++) {
				if ($spu_debug === '1') echo "spuparams[$ix]=".$spuparams[0][$ix]."<br/>";
				// Get rid of &nbsp;
				$spuvals = trim(str_replace(chr(160), '', $spuparams[0][$ix]));

				$pos = strpos($spuvals, '=');
				if ($pos !== false) {
					
					if (strpos(strtolower($spuvals), 'url=') !== false) {
						$spuvalstmp = explode('=', $spuvals);
						if (count($spuvalstmp) > 2) {
							$spuvals = $spuvalstmp[0]."=";
							for ($i = 1; $i < count($spuvalstmp); $i++) {
								$spuvals .= $spuvalstmp[$i];
								// Swap any equal signs in URL's to [eq] to not mess up the split between key/value pair below
								if ($i < (count($spuvalstmp) -1)) $spuvals .= "[eq]";
							}
						}
					}
					
					
					$spuvals = explode('=', $spuvals);
					$spuval = $spuvals[1];

					switch (strtolower($spuvals[0])) {
						case 'hidden':
							$this->popuphidden = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popuphidden=".$this->popuphidden."<br/>";
							break;
						case 'title':
							$title = str_replace('\'', '', str_replace('"', '', $spuval));
							$title = str_replace('[[sp]]', ' ', $title);
							$this->popuptitle = $title;
							if ($spu_debug === '1') echo "popuptitle=".$this->popuptitle."<br/>";
							break;
						case 'gallery':
							$this->popuprel = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popuprel=".$this->popuprel."<br/>";
							break;
						case 'link':
							$this->popupanchor = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popupanchor=".$this->popupanchor."<br/>";
							break;
						case 'url':
							$spuval = str_replace('[eq]', '=', $spuval);
							$this->popupurl = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popupurl=".$this->popupurl."<br/>";
							break;
						case 'multi':
							$spuval = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if ($spuval === 'true') $this->popupmulti = 'true';
							if ($spu_debug === '1') echo "popupmulti=".$this->popupmulti."<br/>";
							break;
						case 'name':
							$this->popupname = str_replace('\'', '', str_replace('"', '', $spuval));
							//No Spaces in name!
							$this->popupname = str_replace(' ', '', $this->popupname);
							if ($spu_debug === '1') echo "popupname=[".$this->popupname."]<br/>";
							break;
						case 'popup':
							$spuval = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if ($spuval === 'false') $this->popup = 'false';
							if ($spu_debug === '1') echo "popup=".$this->popup."<br/>";
							break;
						case 'video':
							$this->popupvideo = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							//No Spaces in popupvideo!
							$this->popupvideo = str_replace(' ', '', $this->popupvideo);
							if ($this->popupvideo !== 'true') $this->popupvideo = '';
							if ($spu_debug === '1') echo "popupvideo=[".$this->popupvideo."]<br/>";
							break;
						case 'autoplay':
							$this->popupautoplay = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							//No Spaces in popupvideo!
							$this->popupautoplay = str_replace(' ', '', $this->popupautoplay);
							if ($this->popupautoplay !== 'true') $this->popupautoplay = '';
							if ($spu_debug === '1') echo "popupautoplay=[".$this->popupautoplay."]<br/>";
							break;
						case 'cookie':
							$this->popupcookie = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupcookie)) $this->popupcookie = '';
							if ($spu_debug === '1') echo "popupcookie=[".$this->popupcookie."]<br/>";
							break;
						case 'textalign':
							$this->popuptextalign = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('left right center', $this->popuptextalign) === false) $this->popuptextalign = '';
							if ($spu_debug === '1') echo "popuptextalign=[".$this->popuptextalign."]<br/>";
							break;
						case 'width':
							$this->popupwidth = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupwidth)) $this->popupwidth = '';
							if ($spu_debug === '1') echo "popupwidth=[".$this->popupwidth."]<br/>";
							break;
						case 'height':
							$this->popupheight = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupheight)) $this->popupheight = '';
							if ($spu_debug === '1') echo "popupheight=[".$this->popupheight."]<br/>";
							break;
						case 'autodimensions':
							$this->popupautodimensions = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('true false', $this->popupautodimensions) === false) $this->popupautodimensions = '';
							if ($spu_debug === '1') echo "popupautodimensions=[".$this->popupautodimensions."]<br/>";
							break;
						case 'scrolling':
							$this->popupscrolling = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('auto yes no', $this->popupscrolling) === false) $this->popupscrolling = 'auto';
							if ($spu_debug === '1') echo "popupscrolling=[".$this->popupscrolling."]<br/>";
							break;
						case 'cssclass':
							$this->popupcssclass = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if ($spu_debug === '1') echo "popupcssclass=[".$this->popupcssclass."]<br/>";
							break;
						case 'articleid':
							$this->popuparticleid = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popuparticleid)) $this->popuparticleid = 0;
							if ($spu_debug === '1') echo "popuparticleid=[".$this->popuparticleid."]<br/>";
							
							if ($this->popuparticleid > 0) {
								$db  = JFactory::getDBO();
								$query = 'SELECT CONCAT(a.introtext, a.fulltext) FROM #__content AS a WHERE a.state=1 AND a.id='.(int)$this->popuparticleid;
								$db->setQuery($query);
								$rows = $db->loadResult();
								$this->popupmsg = $rows;
							}
							break;
						case 'titleposition':
							$this->popuptitleposition = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('inside outside over', $this->popuptitleposition) === false) $this->popuptitleposition = 'outside';
							if ($spu_debug === '1') echo "popuptitleposition=[".$this->popuptitleposition."]<br/>";
							break;
						case 'hideonoverlayclick':
							$this->popuphideonoverlayclick = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('true false', $this->popuphideonoverlayclick) === false) $this->popuphideonoverlayclick = 'false';
							if ($spu_debug === '1') echo "popuphideonoverlayclick=[".$this->popuphideonoverlayclick."]<br/>";
							break;
						case 'hideoncontentclick':
							$this->popuphideoncontentclick = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('true false', $this->popuphideoncontentclick) === false) $this->popuphideoncontentclick = 'false';
							if ($spu_debug === '1') echo "popuphideoncontentclick=[".$this->popuphideoncontentclick."]<br/>";
							break;
						case 'closebutton':
							$this->popupclosebutton = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('true false', $this->popupclosebutton) === false) $this->popupclosebutton = 'true';
							if ($spu_debug === '1') echo "popupclosebutton=[".$this->popupclosebutton."]<br/>";
							break;
						case 'transitionin':
							$this->popuptransitionin = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('elastic fade none', $this->popuptransitionin) === false) $this->popuptransitionin = 'elastic';
							if ($spu_debug === '1') echo "popuptransitionin=[".$this->popuptransitionin."]<br/>";
							break;
						case 'transitionout':
							$this->popuptransitionout = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('elastic fade none', $this->popuptransitionout) === false) $this->popuptransitionout = 'elastic';
							if ($spu_debug === '1') echo "popuptransitionout=[".$this->popuptransitionout."]<br/>";
							break;
						case 'speedin':
							$this->popupspeedin = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupspeedin)) $this->popupspeedin = 300;
							if ($spu_debug === '1') echo "popupspeedin=[".$this->popupspeedin."]<br/>";
							break;
						case 'speedout':
							$this->popupspeedout = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupspeedout)) $this->popupspeedout = 300;
							if ($spu_debug === '1') echo "popupspeedout=[".$this->popupspeedout."]<br/>";
							break;
						case 'overlayshow':
							$this->popupoverlayshow = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('true false', $this->popupoverlayshow) === false) $this->popupoverlayshow = 'true';
							if ($spu_debug === '1') echo "popupoverlayshow=[".$this->popupoverlayshow."]<br/>";
							break;
						case 'overlayopacity':
							$this->popupoverlayopacity = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->popupoverlayopacity)) $this->popupoverlayopacity = 0.3;
							if ($spu_debug === '1') echo "popupoverlayopacity=[".$this->popupoverlayopacity."]<br/>";
							break;
						case 'overlaycolor':
							$this->popupoverlaycolor = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if ($spu_debug === '1') echo "popupoverlaycolor=[".$this->popupoverlaycolor."]<br/>";
							break;
						case 'jqueryloading':
							$this->popupjqueryloading = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('all fancybox none', $this->popupjqueryloading) === false) $this->popupjqueryloading = '';
							if ($spu_debug === '1') echo "popupjqueryloading=[".$this->popupjqueryloading."]<br/>";
							break;
						case 'boxstyle':
							$this->popupboxstyle = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (strpos('fancybox lightbox iframe pdf', $this->popupboxstyle) === false) $this->popupboxstyle = 'fancybox';
							if ($spu_debug === '1') echo "popupboxstyle=[".$this->popupboxstyle."]<br/>";
							break;
						case 'mime':
							$this->popupmime = strtolower(str_replace('\'', '', str_replace('"', '', $spuval)));
							if (!strstr($this->popupmime, '/')) $this->popupmime = '';
							if ($spu_debug === '1') echo "popupmime=[".$this->popupmime."]<br/>";
							break;
						case 'delayopen':
							$this->delayopen = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->delayopen)) $this->delayopen = 0;
							if ($spu_debug === '1') echo "delayopen=[".$this->delayopen."]<br/>";
							break;
						case 'delayclose':
							$this->delayclose = str_replace('\'', '', str_replace('"', '', $spuval));
							if (!is_numeric($this->delayclose)) $this->delayclose = 0;
							if ($spu_debug === '1') echo "delayclose=[".$this->delayclose."]<br/>";
							break;
					}
				}
			}
			if ($this->popupboxstyle === 'pdf') {
				// If we want to use the PDF viewer then change the URL to include it and change box to iframe
				$options = '';
				if ($spu_showpdfdl === '1') $options = '&hidetb=1';
				$this->popupurl = JUri::root().'plugins/content/simplepopup/simplepopup/pdfjs/web/viewer.html?file='.$this->popupurl.$options;
				$this->popupboxstyle = 'iframe';
				if ($spu_debug === '1') echo "popupboxstyle set to 'iframe' and PDF URL modified to [".$this->popupurl."]<br/>";
			}
		}
		
		if ($this->popupvideo === 'true' && strpos(strtolower($this->popupurl), 'youtube.com')) {
			// Fix the Youtube URL...
			// FROM: http://www.youtube.com/watch?v=eFysVOBriHk
			// TO:   http://www.youtube.com/v/ZeStnz5c2GI?fs=1&autoplay=1
			
			if ($this->popupautoplay === 'true'  && strpos($this->popupurl, 'autoplay') == 0) $this->popupautoplay = '&autoplay=1';
			
			if (strpos($this->popupurl, 'watch?v=')) {
				$spuvals = explode('=', $this->popupurl);
				$this->popupurl = 'http://www.youtube.com/v/'.$spuvals[count($spuvals)-1];
				if (strpos($this->popupurl, 'fs=') == 0) $this->popupurl .= '?fs=1';
				$this->popupurl .= $this->popupautoplay;
			}
		}
		
		// Prevent pop-up if SFU is uploading, else SFU info pop-up is blocked by this...
		if (isset($_SESSION['sfu_mid'])) {
			$mid = $_SESSION["sfu_mid"];
			if (isset($_FILES["uploadedfile$mid"]["name"])) {
				if ($_FILES["uploadedfile$mid"]["name"] > 0) {
					$this->popup = 'false';
				}
			}
		}
		
		if (plgContentSimplePopUp::getMobileBrowser()) $this->resizeOnWindowResize = 'false';
		
		if ($spu_debug === '1') {
			if ($this->resizeOnWindowResize == 'false') {
				echo "Mobile browser detected!<br/>";
			} else {
				echo "Standard browser is used!<br/>";
			}
		}
		
		ob_start();

		
		$rel = '';
		$hidden = '';
		$title = '';
		$url = '';
		
		if (strlen($this->popupanchor) > 0) {
			
			$this->spuindexinit += 1;
			if (strlen($this->popuprel) > 0) $rel = ' rel="'.$this->popuprel.'"';
			if (strtolower($this->popuphidden) === 'true') $hidden = ' style="display: none;"';
			// Title is loaded in teh FB properties now
			//if (strlen($this->popuptitle) > 0) $title = ' title="'.$this->popuptitle.'"';
			$url = ' href="#spucontent'.$this->popupanchor.'"';

			echo '<a id="'.$this->popupanchor.'"'.$rel.$hidden.$title.$url.'>'.$this->popupmsg.'</a>', chr(10);
		}
		
		// Create a name if it doesn't exist
		if(strlen($this->popupname) == 0) $this->popupname = 'spu'.uniqid();
			
        if (is_readable(SPU_PATH.DIRECTORY_SEPARATOR.'default.php') && $this->spuindex == $this->spuindexinit && strlen($this->popupanchor) == 0) {
			
			// Make default anchor
			if ($this->popup !== 'false' ) {
				$rel = '';
				$hidden = '';
				$title = '';
				$url = '';
				// Title is loaded in teh FB properties now
				//if (strlen($this->popuptitle) > 0) $title = ' title="'.$this->popuptitle.'"';
				if (strlen($this->popuprel) > 0) $rel = ' rel="'.$this->popuprel.'"';
				$hidden = ' style="display: none;"';
				$url = ' href="#spucontent'.$this->popupname.'"';
				
				echo '<a id="'.$this->popupname.'"'.$rel.$hidden.$title.$url.'>SPUPOPUPNO1</a>', chr(10);
			}
		
			//Load first popup through default.php
			include(SPU_PATH.DIRECTORY_SEPARATOR.'default.php');
		} elseif ($this->spuindex > $this->spuindexinit && strlen($this->popupanchor) == 0) {
			echo plgContentSimplePopUp::addPopUp($this->spuindex);
		} else {
			//JError::raiseError(500, JText::_('Failed to load default.php'));
		}
		$html = ob_get_clean();
        
		if ($spu_debug === '1') echo '<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
		
        return $html;
		
    }
	
	function getMobileBrowser() {
		$ret = false;
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/android.+mobile|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) $ret = true;
		return $ret;
	
	}


	function addPopUp($idx) {
		
		$html2 = '';
		$spu_autodimensions = $this->params->get( 'spu_autodimensions', 'false' );
		
		if (strlen($this->popuptextalign) > 0) {
			$spu_aligntext = $this->popuptextalign;
		} else {
			$spu_aligntext = $this->params->get( 'spu_aligntext', 'center' );
		}
		if (strlen($this->popupwidth) > 0) {
			$spu_boxwidth = $this->popupwidth;
		} else {
			$spu_boxwidth = $this->params->get( 'spu_boxwidth', '400' );
		}
		if (strlen($this->popupheight) > 0) {
			$spu_boxheight = $this->popupheight;
		} else {
			$spu_boxheight = $this->params->get( 'spu_boxheight', 'auto' );
		}
		if (!is_numeric($spu_boxheight)) $spu_boxheight = "'auto'";
		if (!is_numeric($spu_boxwidth)) $spu_boxwidth = "'auto'";
		if (strlen($this->popupautodimensions) > 0) {
			$spu_autodimensions = $this->popupautodimensions;
		} else {
			$spu_autodimensions = $this->params->get( 'spu_autodimensions', 'false' );
		}
		
		echo '<script language="javascript" type="text/javascript">', chr(10);
		echo '<!--', chr(10);
		echo '	jQuery(document).ready(function() {', chr(10);
		
		echo '		var spuurl'.$this->popupname.' = \''.$this->popupurl.'\';', chr(10);
		
		if ($this->popupvideo !== 'true' && $this->popupboxstyle === 'iframe') {
			echo '		jQuery(\'#'.$this->popupname.'\').attr(\'href\', \''.$this->popupurl.'\');', chr(10);
		}
		
		echo '		jQuery(\'#'.$this->popupname.'\').fancybox({', chr(10);
		
		if ($this->popupboxstyle === 'iframe' && strlen($this->popupmime) > 0) {
			echo '		\'type\'          : \'html\',', chr(10);
			echo '		\'margin\'        : 0,', chr(10);
			echo '		\'title\'         : jQuery(\'#fancybox-wrap>div\').html()+\' '.$this->popuptitle.'\',', chr(10);
			echo '		\'transitionIn\'  : \'elastic\',', chr(10);
			echo '		\'transitionOut\' : \'fade\', // for cross-browser compatibility', chr(10);
			echo '		\'onStart\'       : function(selectedArray, selectedIndex, selectedOpts) {', chr(10);
			
			echo '		var height = Math.round( jQuery(window).height() * 0.9 );', chr(10);
			echo '		var width = Math.round( jQuery(window).width() * 0.9 );', chr(10);
			if ($spu_autodimensions === 'false') {
				if (is_numeric($spu_boxwidth)) echo '			width = '.$spu_boxwidth.';', chr(10);
				if (is_numeric($spu_boxheight)) echo '			height = '.$spu_boxheight.';', chr(10);
			}
			echo '			selectedOpts.content = \'<embed src="'.$this->popupurl.'#nameddest=self&page=1&view=FitH, 0&zoom=80,0,0" type="'.$this->popupmime.'" height="\' + height + \'" width="\' + width + \'" />\'', chr(10);
			echo '		}', chr(10);
		} else {
		
			
			echo '			\'titleShow\'			: true,', chr(10);
			echo '			\'scrolling\'			: \''.$this->popupscrolling.'\',', chr(10);
			echo '			\'transitionIn\'		: \''.$this->popuptransitionin.'\',', chr(10);
			echo '			\'transitionOut\'		: \''.$this->popuptransitionout.'\',', chr(10);
			echo '			\'speedIn\'				: '.$this->popupspeedin.',', chr(10);
			echo '			\'speedOut\'			: '.$this->popupspeedout.',', chr(10);
			echo '			\'hideOnOverlayClick\': '.$this->popuphideonoverlayclick.',', chr(10);
			echo '			\'hideOnContentClick\': '.$this->popuphideoncontentclick.',', chr(10);
			if ($this->popupboxstyle === 'lightbox') {
				echo '			\'showCloseButton\'	: false,', chr(10);
				echo '			\'titlePosition\' 	: \'inside\',', chr(10);
				echo '			\'titleFormat\'		: formatTitle,', chr(10);
			} else {
				echo '			\'showCloseButton\'	: '.$this->popupclosebutton.',', chr(10);
				echo '			\'titlePosition\'		: \''.$this->popuptitleposition.'\',', chr(10);
			}
			if ($this->popupboxstyle === 'iframe') {
				echo '			\'type\'	: \'iframe\',', chr(10);
			}
			if ($spu_autodimensions === 'false') {
				echo '			\'autoDimensions\'	: false,', chr(10);
				echo '			\'width\'	: '.$spu_boxwidth.',', chr(10);
				echo '			\'height\'	: '.$spu_boxheight.',', chr(10);
			} else {
				echo '			\'autoDimensions\'	: true,', chr(10);
			}
			if ($this->popupvideo === 'true') {
				echo '			\'href\'		: spuurl'.$this->popupname.'.replace(new RegExp("watch\\?v=", "i"), \'v/\'),', chr(10);
				echo '			\'type\'		: \'swf\',', chr(10);
				echo '			\'swf\'			: {', chr(10);
				echo '				\'wmode\'		: \'transparent\',', chr(10);
				echo '				\'allowfullscreen\'	: \'true\'', chr(10);
				echo '			},', chr(10);
			}
			echo '			\'title\'					: jQuery(\'#fancybox-wrap>div\').html()+\' '.$this->popuptitle.'\',', chr(10);
			echo '			\'resizeOnWindowResize\'	: '.$this->resizeOnWindowResize.',', chr(10);
			echo '			\'centerOnScroll\'			: '.$this->resizeOnWindowResize.',', chr(10);
			echo '			\'overlayShow\'				: '.$this->popupoverlayshow.',', chr(10);
			echo '			\'overlayOpacity\'			: '.$this->popupoverlayopacity.',', chr(10);
			echo '			\'overlayColor\'			: \''.$this->popupoverlaycolor.'\'', chr(10);
			
		} // End PDF
		
		if($this->delayclose > 0) {
			echo '			,\'onComplete\':function(){', chr(10);
			echo '				setTimeout(function() {', chr(10);
			echo '					jQuery.fancybox.close();', chr(10);
			echo '			 }, '.$this->delayclose.');', chr(10);
			echo '			}', chr(10);
		}
		
		echo '		});', chr(10);
					
		echo '	});', chr(10);
		
		echo '	-->', chr(10);
		echo '</script>', chr(10);
		
		if ($this->popupvideo !== 'true' && $this->popupboxstyle !== 'iframe') {
			echo '<div style="display: none;">', chr(10);
			echo '	<div id="spucontent'.$this->popupname.'" class="'.$this->popupcssclass.'" style="text-align: '.$spu_aligntext.'">', chr(10);
			
			if(strlen($this->popupurl) > 0) {
				$pagecontent = file_get_contents($this->popupurl, FILE_TEXT);
				$pagecontent = mb_convert_encoding($pagecontent, 'UTF-8', mb_detect_encoding($pagecontent, 'UTF-8, ISO-8859-1', true));

				if ($pagecontent === false) $pagecontent = 'URL ('.$this->popupurl.') failed to load. Please inform the site administrator!';
				$this->popupmsg = $pagecontent;
			}
			
			echo '		'.$this->popupmsg, chr(10);
			echo '	</div>', chr(10);
			echo '</div>', chr(10);
		}
		
		
		echo $html2;
		
		return false;
	
	}
 
}
?>
