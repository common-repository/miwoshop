<?php
/**
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('MIWI') or die('Restricted access');

// Import Libraries
mimport('framework.application.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.archive');
mimport('framework.installer.installer');

class com_MiwoshopInstallerScript {

    private $_current_version = null;
    private $_is_new_installation = true;

    public function preflight($type, $parent) {
        if($type == 'upgrade') {
            $old = dirname(__FILE__);
            $upg = MPATH_WP_CNT . '/upgrade';
            $tmp = $upg.'/miwitmp';
            $new = $upg.'/miwoshop.tmp/miwoshop';

            if(MFolder::exists(MPath::clean($tmp))) {
                MFolder::delete(MPath::clean($tmp));
            }

            # copy old files to tmp
            MFolder::copy(MPath::clean($old), MPath::clean($tmp));

            # combine old and new filed in tmp
            MFolder::copy(MPath::clean($new), MPath::clean($tmp));

            # copy tpm files to new
            MFolder::copy(MPath::clean($tmp), MPath::clean($new));

            #delete tmp
            MFolder::delete(MPath::clean($tmp));
        }

        $db = MFactory::getDBO();
        $db->setQuery('SELECT option_value FROM #__options WHERE option_name = "miwoshop"');
        $config = $db->loadResult();

        if (!empty($config)) {
            $this->_is_new_installation = false;

            $miwoshop_xml = MPATH_WP_PLG . '/miwoshop/site/miwoshop/miwoshop.xml';

            if (MFile::exists($miwoshop_xml)) {
                $xml = simplexml_load_file($miwoshop_xml, 'SimpleXMLElement');
                $this->_current_version = (string)$xml->version;
            }
        }
    }
	
	public function postflight($type, $parent) {
        $src = MPATH_WP_PLG . '/miwoshop/site';
		
        require_once($src . '/miwoshop/miwoshop.php');
			
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwoshop/languages'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwoshop/languages'), MPath::clean(MPATH_MIWI.'/languages'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwoshop/languages'));
		}
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwoshop/modules'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwoshop/modules'), MPath::clean(MPATH_MIWI.'/modules'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwoshop/modules'));
		}
		if (MFolder::exists(MPath::clean(MPATH_WP_PLG.'/miwoshop/plugins'))) {
			MFolder::copy(MPath::clean(MPATH_WP_PLG.'/miwoshop/plugins'), MPath::clean(MPATH_MIWI.'/plugins'), null, true);
			MFolder::delete(MPath::clean(MPATH_WP_PLG.'/miwoshop/plugins'));
		}

		#todo delete this part next version
		if ($type == 'upgrade') {
			return;
		}
		##################################
		##################################

        if ($this->_is_new_installation) {
            $this->_installMiwoshop();
        }
        else {
            $this->_updateMiwoshop();
        }
	}

    protected function _installMiwoshop() {
		/***********************************************************************************************
		* ---------------------------------------------------------------------------------------------
		* DATABASE INSTALLATION SECTION
		* ---------------------------------------------------------------------------------------------
		***********************************************************************************************/
        $config = new stdClass();

        $config->pid = '';
        $config->enable_vqmod_cache = '1';
        $config->show_header = '1';
        $config->show_footer = '1';
        $config->show_cats_menu = '0';
        $config->trigger_content_plg = '0';
        $config->fix_ie_cache = '0';
        $config->miwoshop_display = '0';
        $config->button_class = 'button_oc';
        $config->comments = '0';
        $config->mijosef_integration = '0';
        $config->account_sync_done = '0';
        $config->alias_sync_done = '1';

        $reg = new MRegistry($config);
        $config = $reg->toString();

		$db = MFactory::getDbo();
        $db->setQuery('INSERT INTO `#__options` (option_name, option_value) VALUES ("miwoshop", '.$db->Quote($config).')');
        $db->query();

        $settings = serialize($reg->toArray());
        $db->setQuery("INSERT INTO `#__miwoshop_setting` SET store_id = 0, `code` = 'config', `key` = 'config_miwoshop', `value` = ".$db->Quote($settings).", `serialized` = 1");
        $db->query();

		MiwoShop::get('install')->createTables();
		MiwoShop::get('install')->createUserTables();
		MiwoShop::get('install')->createApiUser();
		MiwoShop::get('install')->createGroupTables();
        MiwoShop::get('install')->createIntegrationTables();
		MiwoShop::get('install')->addPage();

        if (empty($this->_current_version)) {
            return;
        }

        if ($this->_current_version = '1.0.0') {
            return;
        }		
    }

    protected function _updateMiwoshop() {
        if (empty($this->_current_version)) {
            return;
        }

		if (version_compare($this->_current_version, '3.0.0') == -1) {
            MiwoShop::get('install')->upgrade300();
        }
    }

    public function uninstall($parent) {
		$db  = MFactory::getDBO();
		$src = __FILE__;
	}
}