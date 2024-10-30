<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

$main_full_url = MiwoShop::getClass('base')->getFullUrl();
$j_config = MiwoShop::getClass('base')->getMConfig();

$_suffix = 'wp-admin/'; #miwo
if (!MiwoShop::getClass()->isAdmin('joomla')) {
    $_suffix = '';
}

// HTTP
define("HTTP_SERVER", $main_full_url.$_suffix);
define('HTTP_CATALOG', $main_full_url);
define("HTTP_IMAGE", MURL_MIWOSHOP . '/site/opencart/image/'); #miwo

// HTTPS
define("HTTPS_SERVER", $main_full_url.$_suffix);
define('HTTPS_CATALOG', $main_full_url);
define("HTTPS_IMAGE", MURL_MIWOSHOP . '/site/opencart/image/'); #miwo

define("HTTP_SERVER_TEMP", $main_full_url);

// DIR
define("DIR_APPLICATION", MPATH_MIWOSHOP_OC.'/admin/');
define("DIR_SYSTEM", MPATH_MIWOSHOP_OC.'/system/');
define("DIR_DATABASE", MPATH_MIWOSHOP_OC.'/system/database/');
define("DIR_LANGUAGE", MPATH_MIWOSHOP_OC.'/admin/language/');
define("DIR_TEMPLATE", MPATH_MIWOSHOP_OC.'/admin/view/template/');
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