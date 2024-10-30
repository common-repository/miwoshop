<?php
/**
 * @package		MiwoShop
 * @copyright	2009-2016 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

define('MPATH_MIWOSHOP_OC', MPATH_WP_PLG.'/miwoshop/site/opencart');
define('MPATH_MIWOSHOP_LIB', MPATH_WP_PLG.'/miwoshop/site/miwoshop');
define('MPATH_MIWOSHOP_SITE', MPATH_WP_PLG.'/miwoshop/site');
define('MPATH_MIWOSHOP_ADMIN', MPATH_WP_PLG.'/miwoshop/admin');

if (MFactory::$application->isAdmin()) {
    $_side = MPATH_ADMINISTRATOR;
}
else {
    $_side = MPATH_SITE;
}

$_lang = MFactory::getLanguage();
$_lang->load('com_miwoshop', $_side, 'en-GB', true);
$_lang->load('com_miwoshop', $_side, $_lang->getDefault(), true);
$_lang->load('com_miwoshop', $_side, null, true);

$_lang = MFactory::getLanguage();
$_lang->load('com_miwoshop.old', MPATH_ADMINISTRATOR, 'en-GB', true);
$_lang->load('com_miwoshop.old', MPATH_ADMINISTRATOR, $_lang->getDefault(), true);
$_lang->load('com_miwoshop.old', MPATH_ADMINISTRATOR, null, true);