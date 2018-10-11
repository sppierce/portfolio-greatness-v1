<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
JLoader::register ( 'JevJoomlaVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php" );

class plgJEventsjevfacebooksocial extends JPlugin {
    public static function tagsDone($done = false) {
        static $tagsdone = false;
        if ($done) {
            $tagsdone = true;
        }
        return $tagsdone;
    }
    function onLocationDisplay(&$location){
        if( in_array('jevlocations',$this->params->get('whenenabled',array('jevents'))) ) {
            if (file_exists(JPATH_SITE . '/components/com_jevlocations/jevlocations.php')) {
                self::onDisplayCustomFields($location);
            }
        }
    }

    function onDisplayCustomFields(&$row) {

        // skip for event invitations, reminders etc.
        if (strpos(JRequest::getCmd("option",""), "com_rsvppro")!==false){
            return;
        }

        $task = JRequest::getString("task", "");

        $allowed_views = array('icalrepeat.detail', 'icalevent.detail', 'locations.detail');

        if (!in_array($task, $allowed_views)) {
            return;
        }



        // Check if we are on the locations details
        if (isset($row->loc_id)) {
            $comp = "com_jevlocations";
        } else {
            $comp = "com_jevents";
        }

        $lang = JFactory::getLanguage ();
        $lang->load ( "plg_jevents_jevfacebooksocial", JPATH_ADMINISTRATOR );

        $Itemid = JRequest::getInt ( 'Itemid' );
        $uri = JURI::getInstance ( JURI::base () );
        $root = $uri->toString ( array (
            'scheme',
            'host',
            'port'
        ) );
        $tmpl = "";
        if (JRequest::getString("tmpl","")=="component"){
            $tmpl = "&tmpl=component";
        }
        //Check if event details or locations for link generation.
        if ($comp == "com_jevlocations") {
            $link 	= JRoute::_( 'index.php?option=com_jevlocations&task=locations.detail&loc_id='. $row->loc_id . $tmpl ."&se=1"."&title=".JApplication::stringURLSafe($row->title));
        } else {
            $link = $root . JRoute::_($row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false, $Itemid), false);
        }

        $debug = $this->params->get ( "debug", "0" );
        if (JBrowser::getInstance ()->isSSLConnection ()) {
            $ssl = 'https://';
        } else {
            $ssl = 'http://';
        }
        $showfaces = $this->params->get ( "showfaces", 1 ) ? "true" : "false";
        $likeshare = $this->params->get ( "like_wshare", 1 ) ? "true" : "false";
        $layout = $this->params->get ( "layoutstyle", "standard" );
        $width = $this->params->get ( "width", 450 );
        $verb = $this->params->get ( "verb", "like" );
        $fappid = $this->params->get ( "fappid", "" );
        $commentslang = $this->params->get ( "commentslang", "en_US" );
        $colourscheme = $this->params->get ( "colourscheme", "light" );

        ?>
        <!--  Intialise the facebook APP  -->
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '<?php echo $fappid; ?>',
                    status     : true,
                    xfbml      : true,
                    version    : 'v2.0'
                });
            };
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                <?php if ($debug == 0) { ?>
                js.src = "//connect.facebook.net/<?php echo $commentslang; ?>/sdk.js";
                <?php } else {?>
                js.src = "//connect.facebook.net/<?php echo $commentslang; ?>/all/debug.js";
                <?php }?>
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <?php
        // Make sure tmpl=component is removed from url:
        $link = str_replace(array('&tmpl=component', '?tmpl=component'), '', $link );

        if ($this->params->get ( "like", 1 )) {
            $html = '<div class="fb-like" data-href="' . $link . '" data-layout="' . $this->params->get ( "like_layoutstyle", "standard") . '" data-action="' . $verb . '" data-show-faces="' . $showfaces . '" data-share="' . $likeshare . '"></div>';

            $row->_fblike = $html;
        } else {
            $row->_fblike = "";
        }

        if ($this->params->get ( "share", 1 )) {
            $row->_fbshare =  '<div class="fb-share-button" data-href="' . $link . '" data-type="' . $this->params->get ( "share_layoutstyle", "button") . '"></div>';
        }

        if ($this->params->get ( "comments", 0 )) {
            $width = $this->params->get ( "width", 450 );
            ob_start ();
            ?>
            <div class="fb-comments" data-href="<?php echo $link; ?>" data-numposts="<?php echo $this->params->get ( "cnumber_of_posts", 5); ?>" data-colorscheme="light"></div>
            <?php
            $row->_fbcomments = ob_get_clean ();
        } else {
            $row->_fbcomments = "";
        }

        if ($this->params->get ( "like", 1 ) || $this->params->get ( "share", 1 )) {
            // check if detail page layout is enabled

            static $template_name;
            static $template;

            if (! isset ( $template )) {
                $db = JFactory::getDBO ();
                $db->setQuery ( "SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote ( $template_name ) . " AND value<>'' AND " . 'language in (' . $db->quote ( JFactory::getLanguage ()->getTag () ) . ',' . $db->quote ( '*' ) . ')' );
                // $db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= 'icalevent.detail_body'");
                $template = $db->loadObject ();
            }
            if (is_null ( $template ) || $template->value == "") {
                $mainframe = JFactory::getApplication (); // RSH 11/11/10 Make J!1.6 compatible
                // add facebook meta tags to page

                if (method_exists($row, "title")) {
                    $title = $row->title();
                    $description = $row->content();
                    $categoryimage = $row->getCategoryImageUrl ();
                } else {
                    $title = $row->title;
                    $description = $row->description;
                    $categoryimage = "";
                    $row->_imageurl1 = $row->image;
                }

                $facebooktags = "\n" . '<!--facebook tags by JEvents-->' . "\n\t";
                $facebooktags .= '<meta property="og:type" content="event" />' . "\n\t";
                $facebooktags .= '<meta property="event:start_time" content="'.$row->yup .'-' . $row->mup . '-' . $row->dup . 'T' . $row->hup . ':' . $row->minup . ':' . $row->sup . '" />' . "\n\t";
                $facebooktags .= '<meta property="og:url" content="'. JURI::current() . '"/>' .  "\n\t";
                $facebooktags .= '<meta property="og:title" content="' . $title . '" />' . "\n\t";
                $desc = htmlspecialchars ( strip_tags ( $description ) );
                $desc = str_replace ( "\n", "", $desc );
                $desc = str_replace ( "\r", "", $desc );
                $length = 200; // modify for desired width
                if (strlen ( $desc ) >= $length) {
                    $desc = substr ( $desc, 0, strpos ( wordwrap ( $desc, $length ), "\n" ) );
                }
                $facebooktags .= '<meta property="og:description" content="' . $desc . '" />' . "\n\t";
                $facebooktags .= '<meta property="og:site_name" content="' . $mainframe->getCfg ( 'sitename' ) . '" />' . "\n\t";

                if (isset($row->_imageurl1) && $row->_imageurl1 != "" && $this->params->get("catoreventimage", 1) == 1 || $categoryimage == "")
                {
                    $customimage = $row->_imageurl1;

                    if (strpos($customimage, "/")===0) $customimage = substr ($customimage, 1);
                    //$customimage = (strpos($customimage, "http://")===false && strpos($customimage, "https://")===false)? JURI::root().$customimage: $customimage;
                    $facebooktags .= '<meta property="og:image" content="' . JURI::base() . $customimage . '" />';
                } elseif (isset($categoryimage) && $categoryimage != "" ) {
                    $catimage = $categoryimage;
                    if (strpos($catimage, "/")===0) $catimage = substr ($catimage, 1);
                    $catimage = (strpos($catimage, "http://")===false && strpos($catimage, "https://")===false)? JURI::root().$catimage: $catimage;
                    $facebooktags .= '<meta property="og:image" content="' . $catimage . '" />';
                }

                $facebooktags .= '<meta property="fb:app_id" content="'.$fappid.'" />';

                $doc = JFactory::getDocument ();
                if (is_callable ( array (
                    $doc,
                    "addCustomTag"
                ) )) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }
            }

            return $row->_fblike . $row->_fbshare . $row->_fbcomments;
        }
    }
    static function fieldNameArray($layout = 'detail') {

        if ($layout != "detail")
            return array ();
        $labels = array ();
        $values = array ();
        $labels [] = JText::_ ( "JEV_FACEBOOK_SOCIAL_LIKE", true );
        $values [] = "JEV_SOCIAL_FBLIKE";
        $labels [] = JText::_ ( "JEV_FACEBOOK_SOCIAL_SHARE", true );
        $values [] = "JEV_SOCIAL_FBSHARE";
        $labels [] = JText::_ ( "JEV_FACEBOOK_SOCIAL_COMMENTS", true );
        $values [] = "JEV_SOCIAL_FBCMT";

        $return = array ();
        $return ['group'] = JText::_ ( "JEV_FACEBOOK_SOCIAL_OUTPUT", true );
        $return ['values'] = $values;
        $return ['labels'] = $labels;

        return $return;
    }
    static function substitutefield($row, $code) {
        if (method_exists($row, "title")) {
            $title = $row->title();
            $description = $row->content();
            $categoryimage = $row->getCategoryImageUrl();
        } else {
            $title = $row->title;
            $description = $row->description;
            $categoryimage = "";
            $row->_imageurl1 = $row->image;
        }

        $mainframe = JFactory::getApplication (); // RSH 11/11/10 Make J!1.6 compatible
        // add facebook meta tags to page
        $facebooktags = "\n" . '<!--facebook tags by JEvents-->' . "\n\t";
        $facebooktags .= '<meta property="og:type" content="event" />' . "\n\t";
        $facebooktags .= '<meta property="event:start_time" content="'.$row->yup .'-' . $row->mup . '-' . $row->dup . 'T' . $row->hup . ':' . $row->minup . ':' . $row->sup . '" />' . "\n\t";
        $facebooktags .= '<meta property="og:url" content="'. JURI::current() . '"/>' .  "\n\t";
        $facebooktags .= '<meta property="og:title" content="' . $title . '" />' . "\n\t";
        $desc = htmlspecialchars ( strip_tags ( $description ) );
        $desc = str_replace ( "\n", "", $desc );
        $desc = str_replace ( "\r", "", $desc );
        $length = 200; // modify for desired width
        if (strlen ( $desc ) >= $length) {
            $desc = substr ( $desc, 0, strpos ( wordwrap ( $desc, $length ), "\n" ) );
        }
        // $desc = "Event Description";
        $facebooktags .= '<meta property="og:description" content="' . $desc . '" />' . "\n\t";
        $facebooktags .= '<meta property="og:site_name" content="' . $mainframe->getCfg ( 'sitename' ) . '" />' . "\n\t";
        if (isset($row->_imageurl1) && $row->_imageurl1 != "")
        {
            $customimage = $row->_imageurl1;
            if (strpos($customimage, "/")===0) $customimage = substr ($customimage, 1);
            $customimage = (strpos($customimage, "http://")===false && strpos($customimage, "https://")===false)? JURI::root().$customimage: $customimage;
            $facebooktags .= '<meta property="og:image" content="' . JURI::base() . $customimage . '" />';
        }
        $doc = JFactory::getDocument ();

        if ($code == "JEV_SOCIAL_FBLIKE") {
            if (isset ( $row->_fblike )) {

                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_fblike;
            }
            else if (isset( $row->_jevlocation->_fblike)) {
                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_jevlocation->_fblike;
            }
            return "";
        }

        if ($code == "JEV_SOCIAL_FBSHARE") {
            if (isset ( $row->_fbshare )) {

                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_fbshare;
            } else if (isset ( $row->_jevlocation->_fbshare)) {
                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_jevlocation->_fbshare;
            }
            return "";
        }
        if ($code == "JEV_SOCIAL_FBCMT") {
            if (isset ( $row->_fbcomments )) {
                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_fbcomments;
            } else if (isset ( $row->_jevlocation->_fbcomments )) {
                if (is_callable ( array (
                        $doc,
                        "addCustomTag"
                    ) ) && ! plgJEventsjevfacebooksocial::tagsDone ()) {
                    $doc->addCustomTag ( $facebooktags );
                    plgJEventsjevfacebooksocial::tagsDone ( true );
                }

                return $row->_jevlocation->_fbcomments;
            }
            return "";
        }
    }
}
