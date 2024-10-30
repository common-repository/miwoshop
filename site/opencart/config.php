<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

$main_full_url = MiwoShop::getClass()->getFullUrl();
$j_config = MiwoShop::getClass()->getMConfig();

define("HTTP_SERVER_TEMP", $main_full_url);

define("DIR_APPLICATION", MPATH_MIWOSHOP_OC.'/catalog/');
define("DIR_SYSTEM", MPATH_MIWOSHOP_OC.'/system/');
define("DIR_DATABASE", MPATH_MIWOSHOP_OC.'/system/database/');
define("DIR_LANGUAGE", MPATH_MIWOSHOP_OC.'/catalog/language/');
define("DIR_TEMPLATE", MPATH_MIWOSHOP_OC.'/catalog/view/theme/');
define("DIR_CONFIG", MPATH_MIWOSHOP_OC.'/system/config/');
define("DIR_IMAGE", MPATH_MIWOSHOP_OC.'/image/');
define("DIR_CACHE", MPATH_MIWI.'/cache/com_miwoshop/'); #miwo
define("DIR_DOWNLOAD", MPATH_MIWOSHOP_OC.'/system/download/');
define("DIR_LOGS", MPATH_MIWOSHOP_OC.'/system/logs/');
define("DIR_CATALOG", MPATH_MIWOSHOP_OC.'/catalog/');
define('DIR_MODIFICATION', MPATH_MIWOSHOP_OC.'/system/modification/');
define('DIR_UPLOAD', MPATH_MIWOSHOP_OC.'/system/upload/');

// DB
define("DB_DRIVER", 'mysql');
define("DB_HOSTNAME", MiwoShop::getClass('db')->getDbAttribs('host'));
define("DB_USERNAME", MiwoShop::getClass('db')->getDbAttribs('user'));
if (!defined("DB_PASSWORD")) define("DB_PASSWORD", MiwoShop::getClass('db')->getDbAttribs('password'));
define("DB_DATABASE", MiwoShop::getClass('db')->getDbAttribs('database'));
define("DB_PREFIX", '#__miwoshop_');