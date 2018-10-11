<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		13
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2015 Brandon IT Consulting. All rights reserved.
 *
 * @changelog	v10 added articlesave pagetype
 *					added profilesave pagetype
 *					added weblinkgo pagetype
 *					caused search/searchresults pagetypes to only trigger on GET 
 *						requests (Joomla initially takes the request as a post,
 *						then rewrites the URL and redirects as a GET)
 *					fixed groups and groupids to work under 1.7 and 2.5 (2.5 system is different)
 *				v11 added detection for Trident (IE11) user agents
 * 					included detection of tags pages from Chameleon version of JomGenius
 *					various other minor fixes from Chameleon version of JomGenius
 *				v12 improved detection of Safari user agents
 *				v13 improved detection of client IP address through 2 new types of proxy servers
 */

class JomGeniusClassCore extends JomGeniusParent {
	
	var $timezone = 'default';
	
	var $months	= array( 'DUMMY','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec' );
	var $monthregex = 'jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
	var $days	= array( 'sun','mon','tue','wed','thu','fri','sat' );
	var $dayregex = 'sun|mon|tue|wed|thu|fri|sat';
	
	
	var $Itemid;
	var $view;
	var $option;
	var $category_id;
	var $section_id;
	var $layout;
	var $task;
	var $id;	
	
	// should be set on an instance before the inTimeSpan thing is used.
	function setTimezone( $timezone ) {
		$this->timezone = $timezone;
	}
	
	function __construct() {
		$jinput				= JFactory::getApplication()->input;
		$this->Itemid		= $jinput->get('Itemid');
		$this->view			= $jinput->get('view', '', 'WORD');
		$this->option		= $jinput->get('option');
		$this->layout		= $jinput->get('layout');
		$this->task			= $jinput->get('task');
		$this->id			= $jinput->get('id',null,'');// 3rd null is the filter, forces to default that can handle arrays eg for tags view
	}
	
	function shouldInstantiate() {
		return true;
	}

	
	/* particular methods for this component */
	
	/**
	 * A generic function that knows how to get lots of different info about the environment.
	 */
	function info( $type ) {
		
		$type = str_replace( '_', '', $type );
		
		// some special handling, that need not hit the database
		switch( $type ) {
			case 'pagetype'			: return $this->pageType();
			case 'browser'			:
			case 'browsertype'		:
			case 'browserversion'	:
			case 'browseros'		: return $this->browserInfo( $type );
			
			case 'categorizr'		: return $this->categorizr( $type );
			
			case 'referer'			:
			case 'referrer'			:
			case 'domain'			:
			case 'url'				:
			case 'uri'				:
			case 'clientip'			:
			case 'issecure'			: return $this->environmentInfo( $type );
			
			case 'browserlanguage'	:
			case 'frontendlanguage'	: return $this->languageInfo( $type );
			
			case 'userid'			:
			case 'userfullname'		:
			case 'userloginname'	:
			case 'useremail'		:
			case 'isloggedin'			:
			case 'isnotloggedin'		:
			case 'isguest'			:
			case 'userlastvisitdate':
			case 'userregisterdate'	:
			case 'minutessincelastvisit'	:
			case 'minutessinceregistration'	:
			case 'dayssincelastvisit'		:
			case 'dayssinceregistration'	:
			case 'groups'			:
			case 'groupids'			:
										return $this->userInfo( $type );
			
			default:
		}
		
		// are there some more things that we might need to calculate?
		return null;
	}
	
	function pageType() {
		// com_content ones are also found in jomgenius/content.php, but we replicate them here.
		// The aim is that in this method we cover all page types in an out-of-the-box Joomla installation.
		
		switch ( $this->option ) {
			case 'com_content':
				switch ( $this->view ) {
					case 'form':
						if ($this->layout == 'edit' and JFactory::getApplication()->input->get('a_id') != null ) return 'articleedit';
						return 'articlenew';
					case 'featured':
						return 'featured';
					case 'article':
						if ($this->task == 'article.save') return 'articlesave';
						return 'article';
					case 'category':
						if ($this->layout == 'blog' ) return 'categoryblog';
						return 'categorylist';
					case 'categories':
						return 'categories';
					case 'archive': // ??? untested
						return 'archive';
				}
				// J3.2 and perhaps earlier, does not use $view in article save.
				if ($this->task == 'article.save') {
					return 'articlesave';
				}
				return '';
			case 'com_users':
				switch( $this->view ) {
					case 'reset':
						return 'reset';
					case 'remind':
						return 'remind';
					case 'registration':
						return 'registration';
					case 'login':
						return 'login';
					case 'profile':
						if ($this->task == 'profile.save') return 'profilesave';
						if (JFactory::getApplication()->input->get('layout') == 'edit') return 'profileedit';
						else return "profile";
				}
				return '';
			case 'com_contact':
				switch ( $this->view ) {
					case 'contact':
						return 'contact';
					case 'category':
						return 'contactcategory';
					case 'categories':
						return 'contactcategories';
					case 'featured':
						return 'contactfeatured';
				}
				return '';
			case 'com_weblinks':
				if ( $this->task == "weblink.go" ) return 'weblinkgo';
				switch ( $this->view ) {
					case 'form':
						if ( JFactory::getApplication()->input->get('w_id') != null ) return 'weblinkedit';
						return 'weblinknew';
					case 'categories':
						return 'weblinkscategories';
					case 'category':
						return 'weblinkscategory';
					case 'weblink':
						if ( $this->layout == 'form' ) {
							return 'weblinkform';
						}
						return 'weblink'; // for the moment before it gets redirected...
						// it would also be useful to be able to trap a newly submitted web link. At present
						// this shows up "weblinkform" then quickly redirects to the category view.
				}
				return '';
			case 'com_poll':
				switch ( $this->view ) {
					case 'poll':
						return 'poll';
				}
				return '';
			case 'com_wrapper':
				switch ( $this->view ) {
					case 'wrapper':
						return 'wrapper';
				}
				return '';
			case 'com_tags':
				switch ( $this->view ) {
					case 'tag':
						if ($this->layout == 'list') return 'tagslist';
						return 'tags';
					case 'tags':
						return 'tagsall';
				}
				return '';
			case 'com_search':
			case 'com_finder':
				switch ( $this->view ) {
					case 'search':
						// a search causes a POST then a GET in quick succession. I have decided to
						// only trap the 2nd request (the GET).
						if ($_SERVER['REQUEST_METHOD'] == 'POST') return '';
						$input = JFactory::getApplication()->input;
						if ( $input->get('searchword') == '' and $input->get('q') == '') {
							return 'search';
						} else {
							return 'searchresults';
						}
				}
				return '';
			case 'com_newsfeeds':
				switch ( $this->view ) {
					case 'categories':
						return 'newsfeedscategories';
					case 'category':
						return 'newsfeedscategory';
					case 'newsfeed':
						return 'newsfeed';
				}
				return '';
		}
		return '';
	}	
	
	function categorizr( $type ) {
		include_once 'categorizr.php';
		return jomgenius_categorizr();
	}
	
	function browserInfo( $type ) {

		if (!defined('MM_USER_AGENT')) {
			$UA  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$BB  = ( false !== stripos( $UA, 'blackberry' ) );
			$CH  = strstr($UA, 'Chrome/') ? true : false;
			$CHV = $CH ? preg_match('#Chrome/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)#',
			         $UA, $matches) : false;
			$CHV = $CHV ? $matches[1] : false;
			$IP  = strstr($UA, 'iPhone') ? true : false;
			$IPOD  = strstr($UA, 'iPod') ? true : false;
			$IPAD  = strstr($UA, 'iPad') ? true : false;
			$OW  = strstr($UA, 'OmniWeb') ? true : false;
			$OWV = $OW ? preg_match('#OmniWeb/v([0-9]+(\.[0-9]+)?)#',
			         $UA, $matches) : false;
			$OWV = $OWV ? $matches[1] : false;
			$AN  = stristr($UA, 'Android') ? true : false;
			$SF  = !$CH && strstr($UA, 'Safari') ? true : false;
			$OP  = strstr($UA, 'Opera') ? true : false;
			$OPV = $OP ? preg_split('/opera\//i', $UA) : false;
			$OPV = $OPV ? floatval($OPV[1]) : false;
			$FF  = strstr($UA, 'Firefox') ? true : false;
			$FFV = $FF ? preg_split('/firefox\//i', $UA) : false;
			$FFV = $FFV ? floatval($FFV[1]) : false;
			
			$IE  = strstr($UA, 'MSIE') ? true : false;
			$IEV = $IE ? preg_split('/msie/i', $UA) : false;
			$IEV = $IEV ? floatval($IEV[1]) : false;
			
			if (! $IE ) { // not the old style that contains msie in the UA...
				$IE	 = stristr($UA, 'trident/') ? true : false;
				$IEV = $IE ? preg_split('/rv:/i', $UA) : false;
				$IEV = $IEV ? floatval($IEV[1]) : false;
			}
			
			$GB  = stristr( $UA, 'Googlebot') ? true : false; // google crawler
			$GM  = stristr( $UA, 'Mediapartners-Google' ) ? true : false; // adsense
			$YS  = stristr( $UA, 'Yahoo! Slurp' ) ? true : false; // yahoo crawler
			$MB  = stristr( $UA, 'msnbot' ) ? true : false; // msn / bing crawler
			$TE  = stristr( $UA, 'teoma' ) ? true : false; // Teoma / Ask Jeeves
			$BOT = ( $GB || $GM || $YS || $MB || $TE
				|| stristr( $UA, 'bot' ) 
				|| stristr( $UA, 'crawler' ) 
				|| stristr( $UA, 'spider' ) );
			
			$PS  = stristr( $UA, 'playstation' ) ? true : false;
			$WII = stristr( $UA, 'wii' ) ? true : false;
			$XBOX = stristr( $UA, 'xbox' ) ? true : false;
			$CONSOLE = ($PS || $WII || $XBOX);
			
			$SFV = '';
			if ($SF) {
			  $early_versions = array(
			    '85'=>1.0,
			    '125'=>1.2,
			    '312'=>1.3,
			    '412'=>2.0,
			    '416'=>2.0,
			    '417'=>2.0,
			    '419'=>2.0
			  );
			  $SFV = preg_match( '#(Version/([0-9.]+))? *(Mobile/[a-zA-Z0-9]+)? Safari/([0-9]+)#', $UA, $matches);
			  if ( count($matches) == 5 ) {
			    if ( array_key_exists( $matches[4], $early_versions ) ) {
			      $SFV = $early_versions[ $matches[4] ];
			    } else if ($matches[2] != '') {
			      $SFV = $matches[2];
			    }
			  }
			}

			define( 'MM_USER_AGENT', $UA );
			define( 'MM_BROWSER_BLACKBERRY', $BB );
			define( 'MM_BROWSER_CHROME', $CH );
			define( 'MM_BROWSER_CHROME_VERSION', $CHV );
			define( 'MM_BROWSER_OMNIWEB', $OW );
			define( 'MM_BROWSER_OMNIWEB_VERSION', $OWV );
			define( 'MM_BROWSER_IPHONE', $IP );
			define( 'MM_BROWSER_IPOD', $IPOD );
			define( 'MM_BROWSER_IPAD', $IPAD );
			define( 'MM_BROWSER_ANDROID', $AN );
			define( 'MM_BROWSER_SAFARI', $SF );
			define( 'MM_BROWSER_SAFARI_VERSION', $SFV );
			define( 'MM_BROWSER_OPERA', $OP );
			define( 'MM_BROWSER_OPERA_VERSION', $OPV );
			define( 'MM_BROWSER_FIREFOX', $FF );
			define( 'MM_BROWSER_FIREFOX_VERSION', $FFV );
			define( 'MM_BROWSER_IE', $IE );
			define( 'MM_BROWSER_IE_VERSION', $IEV );

			define( 'MM_BROWSER_GOOGLEBOT', $GB );
			define( 'MM_BROWSER_GOOGLE_MEDIAPARTNERS', $GM );
			define( 'MM_BROWSER_YAHOO_SLURP', $YS );
			define( 'MM_BROWSER_MSNBOT', $MB );
			define( 'MM_BROWSER_TEOMA', $TE );
			define( 'MM_BROWSER_BOT', $BOT );

			define( 'MM_BROWSER_PS', $PS );
			define( 'MM_BROWSER_WII', $WII );
			define( 'MM_BROWSER_XBOX', $XBOX );
			define( 'MM_BROWSER_CONSOLE', $CONSOLE );

			$os = '';
			if ( $BOT ) $os = 'bot';
			else if ( $CONSOLE ) $os = 'console';
			else if ( stristr( $UA, 'windows' ) ) $os = 'windows';
			else if ( stristr( $UA, 'macintosh' ) ) $os = 'mac';
			else if ( stristr( $UA, 'linux' ) ) $os = 'linux';

			define( 'MM_BROWSER_OS', $os );
		}

		$returns = array();
		
		if ( $type == 'browser' ) { // get the major browser types (incl major revisions)
			// only browsers with interesting versioning get mentioned here.
			if ( MM_BROWSER_BLACKBERRY )	$returns[] = 'blackberry'; // blackberry is also safari, but we trump safari by putting this first.
			if ( MM_BROWSER_FIREFOX )	$returns[] = 'firefox' . (int)MM_BROWSER_FIREFOX_VERSION;
			if ( MM_BROWSER_IE )		$returns[] = 'ie' .      (int)MM_BROWSER_IE_VERSION;
			if ( MM_BROWSER_CHROME )	$returns[] = 'chrome' .  (int)MM_BROWSER_CHROME_VERSION;
			if ( MM_BROWSER_OPERA )		$returns[] = 'opera' .   (int)MM_BROWSER_OPERA_VERSION;
			if ( MM_BROWSER_OMNIWEB )	$returns[] = 'omniweb' . (int)MM_BROWSER_OMNIWEB_VERSION;
			if ( MM_BROWSER_SAFARI )	$returns[] = 'safari' .  (int)MM_BROWSER_SAFARI_VERSION;
		}

		// don't add these ones for "browser" as they are added above with version numbers
		if ( $type == 'browsertype' ) {
			if ( MM_BROWSER_FIREFOX )	$returns[] = 'firefox';
			if ( MM_BROWSER_BLACKBERRY )	$returns[] = 'blackberry';
			if ( MM_BROWSER_IE )		$returns[] = 'ie';
			if ( MM_BROWSER_CHROME )	$returns[] = 'chrome';
			if ( MM_BROWSER_OPERA )		$returns[] = 'opera';
			if ( MM_BROWSER_OMNIWEB )	$returns[] = 'omniweb';
			if ( MM_BROWSER_SAFARI )	$returns[] = 'safari';
		}
		
		// just get the main browser name. iPod, iPad trump Safari.
		if ( $type == 'browsertype' or $type == 'browser' ) {
			if ( MM_BROWSER_IPOD )		$returns[] = 'ipod'; // ipod before iphone, as user agent contains "iphone" as well.
			if ( MM_BROWSER_IPHONE )	$returns[] = 'iphone';
			if ( MM_BROWSER_IPAD )		$returns[] = 'ipad';
			if ( MM_BROWSER_ANDROID )	$returns[] = 'android';
			if ( MM_BROWSER_GOOGLEBOT )	$returns[] = 'googlebot';					
			if ( MM_BROWSER_GOOGLE_MEDIAPARTNERS )	$returns[] = 'google mediapartners';					
			if ( MM_BROWSER_YAHOO_SLURP )	$returns[] = 'yahoo slurp';					
			if ( MM_BROWSER_MSNBOT )		$returns[] = 'msnbot';					
			if ( MM_BROWSER_TEOMA )		$returns[] = 'teoma';					
			if ( MM_BROWSER_PS )		$returns[] = 'playstation';					
			if ( MM_BROWSER_WII )		$returns[] = 'wii';					
			if ( MM_BROWSER_XBOX )		$returns[] = 'xbox';					
			return $returns;
		}
		
		if ( $type == 'browserversion' ) {
			// only ones with interesting/useful version numbers...
			if ( MM_BROWSER_IE )		return MM_BROWSER_IE_VERSION;
			if ( MM_BROWSER_FIREFOX )	return MM_BROWSER_FIREFOX_VERSION;
			if ( MM_BROWSER_OPERA )		return MM_BROWSER_OPERA_VERSION;
			if ( MM_BROWSER_CHROME )	return MM_BROWSER_CHROME_VERSION;
			if ( MM_BROWSER_OMNIWEB )	return MM_BROWSER_OMNIWEB_VERSION;

			if ( MM_BROWSER_SAFARI )	return MM_BROWSER_SAFARI_VERSION;
			if ( MM_BROWSER_IPOD )		return ''; // ipod before iphone, as user agent contains "iphone" as well.
			if ( MM_BROWSER_IPHONE )	return '';
			if ( MM_BROWSER_IPAD )		return '';

			return '';
		}
		
		
		if ( $type == 'browseros' ) {
			return MM_BROWSER_OS;
		}
		
		return '';
	}
	
	function environmentInfo( $type ) {
		
		switch ( $type ) {
			case 'referer'		:
			case 'referrer'		:
				return array_key_exists( 'HTTP_REFERER', $_SERVER ) ?  $_SERVER['HTTP_REFERER'] : '';
			
			case 'domain'		:
				return array_key_exists( 'HTTP_HOST', $_SERVER ) ?  $_SERVER['HTTP_HOST'] : '';
				
			case 'issecure'		:
				return ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off'
				    || ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['SERVER_PORT'] == 443 ) );
				
			case 'url'			:
			case 'uri'			:
				$uri = JUri::getInstance();
				return $uri->toString();
			
			case 'clientip'		:
				return JomGeniusClassCore::getUserIP();
				/*
				$proxy = '';
				$ip = '';
				if (@$_SERVER['HTTP_X_REAL_IP']) {
					$ip = @$_SERVER['HTTP_X_REAL_IP'];
				} else if (@$_SERVER['HTTP_X_SUCURI_CLIENTIP']) {
					$ip = @$_SERVER['HTTP_X_SUCURI_CLIENTIP'];
				} else if (@$_SERVER['HTTP_X_FORWARDED_FOR']) {
					if (@$_SERVER['HTTP_CLIENT_IP']) {
						$proxy = $_SERVER['HTTP_CLIENT_IP'];
					} else {
						$proxy = @$_SERVER['REMOTE_ADDR'];
					}
					$ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					if (@$_SERVER['HTTP_CLIENT_IP']) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} else {
						$ip = @$_SERVER['REMOTE_ADDR'];
					}
				}
				// $ip is the actual client address even if behind proxy (though can be spoofed)
				// $proxy is the proxy IP address, if detected.
				return $ip;
				*/
			
		}
	}
	
	function getUserIP() {
		if ( isset( $_SERVER ) ) {
			if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
				return $_SERVER['HTTP_X_REAL_IP'];
			} else if ( isset( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ) ) {
				return $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
			} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if ( $ip != '' && JString::strtolower($ip) != 'unknown' ) {
					$addresses = explode( ',', $ip );
					// format is client, proxy1, ... proxy(n-1), where proxy(n) is given remote address of request.
					// Although these headers can be forged, the choice is made here to accept the validity of the
					// headers, and take the reported client IP address.
					// Beware: this is therefore not satisfactory for use in security-critical situations where
					// the IP address is used as a security measure.
					return trim( $addresses[0] );  
				}
			}

			if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '' )
				return $_SERVER['HTTP_CLIENT_IP'];

			return @$_SERVER['REMOTE_ADDR'];
		}

		if ( $ip = getenv( 'HTTP_X_FORWARDED_FOR' )) {
			if ( JString::strtolower($ip) != 'unknown' ) {
				$addresses = explode( ',', $ip );
				return trim( $addresses[ count($addresses) - 1 ] );
			}
		}

		if ($ip = getenv('HTTP_CLIENT_IP')) {
			return $ip;
		}

		return getenv('REMOTE_ADDR');
	}
	
	function languageInfo( $type ) {
		switch( $type ) {
			case 'browserlanguage'	:
				$languages = explode(",",array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '');
				if (count($languages)) {
					$language = JString::strtolower($languages[0]);
					$l = explode( ";", trim( $language ) );
					return @$l[0];
				}
				return '';
				
			case 'frontendlanguage'	:
				$langObj = JFactory::getLanguage();
				return JString::strtolower($langObj->getTag());
		}
	}
	
	function userInfo( $type ) {
		$user 	= JFactory::getUser();
		
		switch( $type ) {
			case 'userid'			:
				return $user->id;
				
			case 'userfullname'			:
				return $user->name;

			case 'userloginname'			:
				return $user->username;

			case 'useremail'			:
				return $user->email;

			case 'isloggedin'			:
				return $user->guest == 0;
				
			case 'isnotloggedin'		:
			case 'isguest'			:
				return $user->guest == 1;
				
			case 'groups'			:
				if ( version_compare( JVERSION, '2.5', '<' ) ) {
					return is_array($user->groups) ? array_keys($user->groups) : array();
				} else if ( version_compare( JVERSION, '3.0', '<' ) ) { // J2.5
					// Joomla 2.5 and up, we need to retrieve it from the database.
					static $groups_by_name = null;
					if ($groups_by_name != null) return $groups_by_name;
				
					$db		= JFactory::getDbo();
					$query	= $db->getQuery(true); 
					$query->select("ug.title");
					$query->from("#__user_usergroup_map ugm, #__usergroups ug");
					$query->where("ugm.group_id = ug.id");
					$query->where("ugm.user_id = " .(int)$user->id);

					$db->setQuery($query);
					$groups_by_name = $db->loadResultArray();
					return $groups_by_name;
				} else { // J3.0
					$ids = array_values($user->getAuthorisedGroups());
					if (count($ids)) {
						$db		= JFactory::getDbo();
						$query	= $db->getQuery(true); 
						$query->select("title");
						$query->from("#__usergroups");
						$query->where("id in (" .implode(',',$ids ) . ')' );

						$db->setQuery($query);
						return $db->loadColumn();
					} else {
						return array();
					}
				}

			case 'groupids'			:
				if ( version_compare( JVERSION, '2.5', '<' ) ) {
					return is_array($user->groups) ? array_values($user->groups) : array();
				} else if ( version_compare( JVERSION, '3.0', '<' ) ) { // J2.5
				// Joomla 2.5 and up, we need to retrieve it from the database.
					static $groups_by_id = null;
					if ($groups_by_id != null) return $groups_by_id;
				
					$db		= JFactory::getDbo();
					$query	= $db->getQuery(true); 
					$query->select("group_id");
					$query->from("#__user_usergroup_map");
					$query->where("user_id = " .(int)$user->id);

					$db->setQuery($query);
					$groups_by_id = $db->loadResultArray();
					return $groups_by_id;
				} else { // J3.0
					return array_values($user->getAuthorisedGroups());
				}

			case 'userlastvisitdate'			:
				return $user->lastvisitDate;

			case 'userregisterdate'			:
				return $user->registerDate;

			case 'minutessincelastvisit'		:
				return $this->timeSince( $user->lastvisitDate, 'minutes' ); // minutes

			case 'minutessinceregistration'		:
				return $this->timeSince( $user->registerDate, 'minutes' ); // minutes

			case 'dayssincelastvisit'		:
				return $this->timeSince( $user->lastvisitDate, 'days' ); // days

			case 'dayssinceregistration'		:
				return $this->timeSince( $user->registerDate, 'days' ); // days

		}
	}
		
	/**
	 * inTimeSpan returns a true or false depending on whether the current date/time/day-of-week is within the given time period.
	 * $from - a date/time combination that can include dates, times, or days of week, e.g. Wednesday 01:00
	 * $to - likewise.
	 * e.g. "mon 01:00:00 - mon 10:04:40"; "23:00 - 01:00"
	 * "mon"
	 * "feb 11"
	 * "mon 11:00 - tue 01:00"
	 * "23:00 - 01:00" (i.e. every night of the week since no day is specified)
	 * "mon 12:00 - mon 15:00"
	 * "tue 12:00:30 - mon 01:00" (wraps around the week)
	 */
	function inTimeSpan( $span ) {
		$spans = explode( ';', $span ); // so we can put several into one

		// ascertain local date/time according to timezone set.
		if ($this->timezone == "default") {
			$config				= JFactory::getConfig();
			if ( version_compare( JVERSION, '3.0', '<' ) ) { // J1.7, 2.5
				$offset				= $config->getValue('config.offset') * 3600;
			} else {
				$offset				= $config->get('config.offset') * 3600;
			}
		} else {
			$offset				= $this->timezone_offset( $this->timezone );
		}
		$now_time				= time() + $offset; // presented as a GMT timestamp + offset

		if (!defined('MM_DAY_OF_WEEK'))		define('MM_DAY_OF_WEEK', gmdate('w',$now_time)); // 0=Sunday, 6-Saturday
		if (!defined('MM_DAY_OF_MONTH'))	define('MM_DAY_OF_MONTH', gmdate('j',$now_time)); // 1-31
		if (!defined('MM_MONTH'))			define('MM_MONTH', gmdate('n',$now_time)); // 1-12
		if (!defined('MM_YEAR'))			define('MM_YEAR', gmdate('Y',$now_time));
		if (!defined('MM_HOUR'))			define('MM_HOUR', gmdate('G',$now_time)); // 0-23
		if (!defined('MM_MINUTE'))			define('MM_MINUTE', (int)gmdate('i',$now_time)); // 0-59
		if (!defined('MM_SECOND'))			define('MM_SECOND', (int)gmdate('s',$now_time)); // 0-59
		if (!defined('MM_TIME'))			define('MM_TIME', gmdate('Gis',$now_time)); // 164523 = 16:45:23 = 4:45:23 PM
		if (!defined('MM_DATE'))			define('MM_DATE', gmdate('Ymd',$now_time)); // 20081225 = 25th Dec 2008
		// derived dates/times
		// MM_DAY_TIME has no leading zeros, is always the same number of digits,
		// and can be compared as numeric or string. e.g. 2085959 = tuesday 08:59:59
		if (!defined('MM_DAY_TIME'))		define('MM_DAY_TIME', gmdate('wHis',$now_time));
		// MM_MONTH_DATE_TIME has leading zeros and always has the same number of digits.
		// it can be compared as a string. e.g. 0309085959 is March 9th, 08:59:59
		if (!defined('MM_MONTH_DATE_TIME'))	define('MM_MONTH_DATE_TIME', gmdate('mdHis',$now_time)); // 1085959 = monday 08:59:59

		// if the current time is inside any one of the spans, we return true,
		// else we skip to the next one. Finally we return false if we didn't
		// match any of them.
		
		foreach ( $spans as $span ) {
			$startend = explode( '-', $span ); // when we include full dates we need to refine this so it does not split '2010-09-15' etc.
			$c = count( $startend );
			
			if ( $c == 1 ) { // no span
				
				$start = $this->_parseTimePeriod( strtolower( trim( $startend[0] ) ), true  );
				$end   = $this->_parseTimePeriod( strtolower( trim( $startend[0] ) ), false );
				
			} else if ( $c == 2 ) {
				// parse 1st item and determine what form it's in
				// parse 2nd item and determine what form it's in
				// if they are in the same form, compare them
				
				$start = $this->_parseTimePeriod( strtolower( trim( $startend[0] ) ), true );
				$end = $this->_parseTimePeriod( strtolower( trim( $startend[1] ) ), false );
			}
			
			if ( $c == 1 or $c == 2 ) {

				if ( $start->type == 'ERROR' or $end->type == 'ERROR' ) {
					return false;
				}
				// for now, insist that the types of start and end are the same
				if ( $start->type != $end->type ) {
					return false;
				}
				switch ( $start->type ) {
					
					case 'MONTH_DATE_TIME':
						if ( $start->data < $end->data ) {
							if ( $start->data <= MM_MONTH_DATE_TIME and MM_MONTH_DATE_TIME < $end->data ) return true;
						} else if ( $start->data <= MM_MONTH_DATE_TIME or MM_MONTH_DATE_TIME < $end->data ) return true;
						break;
					
					case 'WEEKDAY_TIME':
						if ( $start->data < $end->data ) {
							if ( $start->data <= MM_DAY_TIME and MM_DAY_TIME < $end->data ) return true;
						} else if ( $start->data <= MM_DAY_TIME or MM_DAY_TIME < $end->data ) return true;
						break;
					
					case 'TIME':
						if ( $start->data < $end->data ) {
							if ( $start->data <= MM_TIME and MM_TIME < $end->data ) return true;
						} else if ( $start->data <= MM_TIME or MM_TIME < $end->data ) return true;
						break;
				}
				
				
			} else {
				return false; // what to do???? FIXME.
			}
		}
		return false;
	}
	
	function _parseTimePeriod( $period, $is_start ) {
		// returns an object $o, with the following:
		// $o->type = "MONTH_DATE_TIME" | "WEEKDAY_TIME" | TIME
		// $o->data = the data
		$o = new stdClass();
		$error = new stdClass();
		$error->type = 'ERROR';
		
		// CHECK FOR 'wed' OR 'jan'
		if ( preg_match( '#^[a-z][a-z][a-z]$#', $period) ) {
			if ( in_array( $period, $this->months ) ) {
				$o->type = 'MONTH_DATE_TIME';
				$month = array_search( $period, $this->months );
				// if this is the end of period, then we take the next month so we can do "< endpoint"
				if ( !$is_start ) {
					$month++;
					if ( $month == 13 ) $month = 1;
				}
				if ( $month < 10 ) {
					$month = '0' . (int)$month;
				}
				$o->data = $month . '01000000';
				return $o;
			}
			if ( in_array( $period, $this->days ) ) {
				$o->type = 'WEEKDAY_TIME';
				$day = array_search( $period, $this->days );
				// if this is the end of period, then we take the next day (wrap around from 7 to 0) so we can do "< endpoint"
				if ( !$is_start ) {
					$day++;
					if ( $day == 7 ) $day = 0;
				}
				$o->data = $day . '000000';
				return $o;
			}
			return $error;
		}
		
		// CHECK FOR 'jan 30' AND 'jan 30 08:59' AND 'jan 30 08:59:59'
		if ( preg_match( '#^(' . $this->monthregex . ') +([1-9][0-9]*)( +([0-9][0-9]?):([0-9][0-9])(:([0-9][0-9]))?)?$#', $period, $matches ) ) {
			$c = count( $matches );
			// [1] = jan
			// [2] = 30
			// [4] = 08
			// [5] = 59
			// [7] = 59
			
			if ( $c == 3 ) { // month and day only
				$o->type = 'MONTH_DATE_TIME';
				$o->data = array_search( $matches[1], $this->months );
				if ( $o->data < 10 ) $o->data = '0' . (int)$o->data; // 0-pad
				$day = $matches[2];
				if ( !$is_start ) $day++; // if end of period, add 1 to the day. We don't roll it over - too hard. Trust that it will still work.
				if ( $day < 10 ) $day = '0' . (int)$day;
				$o->data .= $day;
				$o->data .= '000000'; // add hours, minutes and seconds
				return $o;
			}
			
			if ( $c == 6 ) { // month, day, hour, minute
				$o->type = 'MONTH_DATE_TIME';
				$o->data = '';
				$month = array_search( $matches[1], $this->months );
				$day = $matches[2];
				$hour = $matches[4];
				$minute = $matches[5];
				if (!$is_start) {
					$minute++; // if end of period, add 1 to the minute then roll it over.
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$day++;
						$hour = 0;
					}
					if ( $day > 31 ) {
						$month++;
						$day = 1;
					}
					if ( $month > 12 ) {
						$month = 1; // no need to roll year as we are not using it.
					}
				}
				
				if ( $month < 10 ) $month = '0' . (int)$month; // 0-pad
				$o->data .= $month;

				if ($day < 10) $day = '0' . (int)$day;
				$o->data .= $day;

				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;
				
				$o->data .= '00'; // add seconds
				return $o;
			}
			
			if ( $c == 8 ) { // month, day, hour, minute, second
				$o->type = 'MONTH_DATE_TIME';
				$o->data = '';
				$month = array_search( $matches[1], $this->months );
				$day = $matches[2];
				$hour = $matches[4];
				$minute = $matches[5];
				$second = $matches[7];
				if (!$is_start) {
					$second++; // if end of period, add 1 to the seconds then roll it over.
					if ( $second > 59 ) {
						$minute++;
						$second = 0;
					}
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$day++;
						$hour = 0;
					}
					if ( $day > 31 ) {
						$month++;
						$day = 1;
					}
					if ( $month > 12 ) {
						$month = 1; // no need to roll year as we are not using it.
					}
				}
				
				if ( $month < 10 ) $month = '0' . (int)$month; // 0-pad
				$o->data .= $month;

				if ($day < 10) $day = '0' . (int)$day;
				$o->data .= $day;

				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;

				if ($second < 10) $second = '0' . (int)$second;
				$o->data .= $second;
				
				return $o;
			}
			
			return $error;
		}

		// CHECK FOR 'mon 08:59' AND 'mon 08:59:59'
		if ( preg_match( '#^(' . $this->dayregex . ') +([0-9][0-9]?):([0-9][0-9])(:([0-9][0-9]))?$#', $period, $matches ) ) {
			$c = count( $matches );
			// [1] = mon
			// [2] = 08
			// [3] = 59
			// [5] = 59
			
			if ( $c == 4 ) { // day of week, hour, minute
				$o->type = 'WEEKDAY_TIME';
				$o->data = '';
				$day = array_search( $matches[1], $this->days );
				$hour = $matches[2];
				$minute = $matches[3];
				if (!$is_start) {
					$minute++; // if end of period, add 1 to the minute then roll it over.
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$day++;
						$hour = 0;
					}
					if ( $day > 6 ) {
						$day = 0;
					}
				}
				
				$o->data .= $day;

				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;
				
				$o->data .= '00'; // add seconds
				return $o;
			}
			
			if ( $c == 6 ) { // day of week, hour, minute, second
				$o->type = 'WEEKDAY_TIME';
				$o->data = '';
				$day = array_search( $matches[1], $this->days );
				$hour = $matches[2];
				$minute = $matches[3];
				$second = $matches[5];
				if (!$is_start) {
					$second++; // if end of period, add 1 to the seconds then roll it over.
					if ( $second > 59 ) {
						$minute++;
						$second = 0;
					}
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$day++;
						$hour = 0;
					}
					if ( $day > 6 ) {
						$day = 0;
					}
				}
				
				$o->data .= $day;

				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;

				if ($second < 10) $second = '0' . (int)$second;
				$o->data .= $second;
				
				return $o;
			}
			
			
			// CHECK FOR 'mon'
			if ( preg_match( '#^(' . $this->dayregex . ')$#', $period, $matches ) ) {
				$c = count( $matches );
				// [1] = mon

				if ( $c == 2 ) { // day of week
					$o->type = 'WEEKDAY_TIME';
					$o->data = '';
					$day = array_search( $matches[1], $this->days );
					
					if ( !$is_start ) {
						$day++;
						if ( $day > 6 ) {
							$day = 0;
						}
					}

					$o->data .= $day;

					$o->data .= '000000'; // add hour, minutes, seconds (midnight in period)
					return $o;
				}
			}
			
			return $error;
		}
		
		
		// CHECK FOR '08:59' AND '08:59:59'
		if ( preg_match( '#^([0-9][0-9]?):([0-9][0-9])(:([0-9][0-9]))?$#', $period, $matches ) ) {
			$c = count( $matches );
			// [1] = 08
			// [2] = 59
			// [4] = 59
			
			if ( $c == 3 ) { // hour, minute
				$o->type = 'TIME';
				$o->data = '';
				$hour = $matches[1];
				$minute = $matches[2];
				if (!$is_start) {
					$minute++; // if end of period, add 1 to the minute then roll it over.
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$hour = 0;
					}
				}
				
				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;
				
				$o->data .= '00'; // add seconds
				return $o;
			}
			
			if ( $c == 5 ) { // hour, minute, second
				$o->type = 'TIME';
				$hour = $matches[1];
				$minute = $matches[2];
				$second = $matches[4];
				if (!$is_start) {
					$second++; // if end of period, add 1 to the seconds then roll it over.
					if ( $second > 59 ) {
						$minute++;
						$second = 0;
					}
					if ( $minute > 59 ) {
						$hour++;
						$minute = 0;
					}
					if ( $hour > 23 ) {
						$hour = 0;
					}
				}

				if ($hour < 10) $hour = '0' . (int)$hour;
				$o->data .= $hour;
				
				if ($minute < 10) $minute = '0' . (int)$minute;
				$o->data .= $minute;

				if ($second < 10) $second = '0' . (int)$second;
				$o->data .= $second;
				
				return $o;
			}
			return $error;
		}
		
		
		
	}
	
	
	/**
	 * pass this a zone, and it passes back the seconds offset for the current time in that zone.
	 */
	function timezone_offset( $zone ) {
		if ( function_exists( 'date_offset_get' ) and function_exists( 'date_create' ) and function_exists( 'timezone_open' ) ) {
			$offset = date_offset_get( date_create( "now", timezone_open( $zone ) ) );
		} else {
			$old_tz = getenv('TZ');
			putenv("TZ=$zone");
			$offset = date('Z', time());
			putenv("TZ=$old_tz");
		}
    	return $offset;
	}
	
}