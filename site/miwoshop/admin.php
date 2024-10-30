<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG .'/miwoshop/site/miwoshop/miwoshop.php');

$ctrl = MRequest::getWord('ctrl');
$view = MRequest::getString('view');
$route = MRequest::getString('route');

$base = MiwoShop::get('base');
$document = MFactory::getDocument();
$mainframe = MFactory::getApplication();
$toolbar = MToolBar::getInstance('toolbar');

if (!$base->checkRequirements('admin')) {
    return;
}

$document->addStyleSheet(MURL_MIWOSHOP . '/admin/assets/css/miwoshop.css');
MToolBarHelper::title(MText::_('MiwoShop'), 'miwoshop');

$installed_ms_version = $base->getMiwoshopVersion();
$latest_ms_version = $base->getLatestMiwoshopVersion();
$ms_version_status = version_compare($installed_ms_version, $latest_ms_version);

$wizard = $base->getConfig()->get('wizard', 0);
if (empty($wizard) and strpos($route, 'common/wizard') === false and $base->isAjax() != true) {
    $mainframe->redirect(MRoute::_('index.php?option=com_miwoshop&route=common/wizard'), '', '');
}

if ($view == 'upgrade') {
	$mainframe->redirect(MRoute::_('index.php?option=com_miwoshop&route=common/upgrade'), '', ''); #miwo
}
else if ($view == 'support') {
	$mainframe->redirect(MRoute::_('index.php?option=com_miwoshop&route=common/support'), '', ''); #miwo
}

$redirected = MFactory::getSession()->get('miwoshop.login.redirected');
if (empty($ctrl) && !$redirected && ($base->getConfig()->get('account_sync_done', 0) == 0)) {
    MError::raiseWarning('100', MText::sprintf('COM_MIWOSHOP_ACCOUNT_SYNC_WARN', '<a href="' . MRoute::_('index.php?option=com_miwoshop&ctrl=sync' ) . '">', '</a>')); #miwo
}

if ($ctrl == 'sync') {
    MiwoShop::get('user')->synchronizeAccountsManually();
}

if (isset($_GET['token'])) {
	$_SESSION['token'] = $_GET['token'];
}

if (isset($_SESSION['token']) && !isset($_GET['token'])) {
	$_GET['token'] = $_SESSION['token'];
}

ob_start();

require_once(MPATH_MIWOSHOP_OC.'/admin/index.php');
$output = ob_get_contents();

ob_end_clean();

$output = $base->replaceOutput($output, 'admin');

echo $output;

if ($base->isAjax($output) == true) {
	mexit();
}