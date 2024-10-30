<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted Access');

// Imports
mimport('framework.installer.installer');
mimport('framework.installer.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

class ModelCommonUpgrade extends Model {

    // Upgrade
    public function upgrade() {
        $base = Miwoshop::get('base');
        $utility = Miwoshop::get('utility');

        $type = MRequest::getCmd('type');
        if ($type == 'upload') {
            $userfile = MRequest::getVar('install_package', null, 'files', 'array');
            $package = $utility->getPackageFromUpload($userfile);
        }
        elseif ($type == 'server') {
            $package = $utility->getPackageFromServer('index.php?option=com_mijoextensions&view=download&model=com_miwoshop&version='.$base->getMiwoshopVersion().'&pid='.$base->getConfig()->get('pid'));
        }

        if (!$package || empty($package['dir'])) {
            //$this->setState('message', 'Unable to find install package.');
            return false;
        }

        # Miwi Framework
	    $src = $package['dir'].'/miwi';
        $dest = MPATH_WP_CNT.'/miwi';
        if (!MFolder::exists($dest)) {
            MFolder::copy($src, $dest);
            MFolder::delete($src);
        }
        elseif (MFolder::exists($src) and MFolder::exists($dest)) {
            require_once(MPATH_WP_PLG.'/miwoshop/miwoshop.php');
            $src_version  = MShop::getMiwiVersion($src.'/versions.xml');
            $dest_version = MShop::getMiwiVersion($dest.'/versions.xml');
            if (version_compare($src_version, $dest_version, 'gt')) {
                MFolder::copy($src, $dest, '', true);
                MFolder::delete($src);
            }
            else {
                MFolder::delete($src);
            }
        }
		
		MFolder::copy($package['dir'], MPath::clean(MPATH_WP_PLG.'/miwoshop'), null, true);
        MFolder::delete($package['dir']);

        $script_file = MPATH_WP_PLG.'/miwoshop/script.php';
        if (MFile::exists($script_file)) {
            require_once($script_file);

            $installer_class = 'com_MiwoshopInstallerScript';

            $installer = new $installer_class();

            if (method_exists($installer, 'preflight')) {
                $installer->preflight(null, null);
            }

            if (method_exists($installer, 'postflight')) {
                $installer->postflight(null, null);
            }
        }

        return true;
    }
}