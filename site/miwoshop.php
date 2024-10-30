<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// no direct access
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

if (MiwoShop::get('base')->isAdmin()) {
    require_once(MPATH_MIWOSHOP_LIB.'/admin2.php');
}
else {
    require_once(MPATH_MIWOSHOP_LIB.'/site.php');
}
