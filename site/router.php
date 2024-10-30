<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

//No Permision
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

function MiwoshopBuildRoute(&$query) {
	return MiwoShop::get('router')->buildRoute($query);
}

function MiwoshopParseRoute($segments) {
	return MiwoShop::get('router')->parseRoute($segments);
}