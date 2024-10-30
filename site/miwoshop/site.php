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

$base = MiwoShop::get('base');

if (!$base->checkRequirements('site')) {
    return;
}

$view = MRequest::getCmd('view');
$route = MRequest::getString('route');

if (empty($route)) {
    $_route = MiwoShop::get('router')->getRoute($view);
	
    $route = $_route;
	MRequest::setVar('route', $_route);
	MRequest::setVar('route', $_route, 'get');
}

ob_start();
require_once(MPATH_MIWOSHOP_OC.'/index.php');
$output = ob_get_contents();
ob_end_clean();

$output = $base->replaceOutput($output, 'site');

echo $output;

if ($base->isAjax($output) == true) {
	exit();
}

$base->loadPathway($route);