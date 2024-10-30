<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// no direct access
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG . '/miwoshop/site/miwoshop/miwoshop.php');

$base = MiwoShop::get('base');
$document = MFactory::getDocument();
$mainframe = MFactory::getApplication();

$document->addStyleSheet(MPATH_WP_PLG . '/miwoshop/admin/assets/css/miwoshop.css');

MRequest::setVar('view', 'admin');

$_lang = MFactory::getLanguage();
$_lang->load('com_miwoshop', MPATH_ADMINISTRATOR, 'en-GB', true);
$_lang->load('com_miwoshop', MPATH_ADMINISTRATOR, $_lang->getDefault(), true);
$_lang->load('com_miwoshop', MPATH_ADMINISTRATOR, null, true);

$output = '<div class="miwi_paid"><strong>'. MText::sprintf('MLIB_X_PRO_MEMBERS', 'Frontend Management') .'</strong><br /><br />'. MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoshop-wordpress-shopping-cart#pricing','MiwoShop') .'</div>';
echo $output;