<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ModelCommonWizard extends Model {
    public function save($data){

        $miwi_settings = $this->getMiwoSettings();

        $miwi_settings['wizard'] = 1;

        if(isset($data['pid'])) {
            $miwi_settings['pid'] = $data['pid'];
        }

        if(isset($data['miwoshop_display'])) {
            $miwi_settings['miwoshop_display'] = $data['miwoshop_display'];
        }

        if(empty($miwi_settings['account_sync_done'])) {
            MiwoShop::get('user')->synchronizeAccountsManually(false);
            $miwi_settings['account_sync_done'] = 1;
        }

        $data['config']['config_miwoshop'] = serialize($miwi_settings);

        $this->saveConfig($data['config']);

        $result['success'] = 'Success';
        return $result;
    }

    public function getInstalledComponents($components) {
        $result = $this->db->query("SELECT DISTINCT element FROM #__extensions WHERE element IN('".implode("', '", $components)."') AND type='component'");
        return $result->rows;
    }

    public function getLanguageCount() {
        $result = $this->db->query("SELECT COUNT(element) as count FROM #__extensions WHERE type='language' AND enabled='1' AND client_id='1'");
        return $result->row['count'];
    }

    private function addMenu(){
        return true;
        $data = array();
        $data['menutype'] = 'mainmenu';
        $data['title'] = 'Shop';
        $data['alias'] = 'shop';
        $data['path'] = 'shop';
        $data['link'] = 'index.php?option=com_miwoshop&view=home';
        $data['type'] = 'component';
        $data['published'] = 1;
        $data['parent_id'] = 1;
        $data['level'] = 1;
        $data['access'] = 1;
        $data['client_id'] = 0;
        $data['language'] = '*';
        $data['params'] = '{"miwoshop_store_id":"0","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}';

        $db = MFactory::getDbo();
        $db->setQuery("SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' and `element`='com_miwoshop'");
        $data['component_id'] = $db->loadResult();

        $db->setQuery("SELECT menutype FROM #__menu_types WHERE menutype='mainmenu'");
        $mainMenu = $db->loadResult();

        if(!empty($mainMenu)){
            MTable::addIncludePath(MPATH_ADMINISTRATOR.'/components/com_menus/tables/');
            $table = MTable::getInstance('Menu', 'MenusTable');

            $table->setLocation($data['parent_id'], 'last-child');

            if (!$table->bind($data)) {
                return false;
            }

            if (!$table->check()) {
                return false;
            }

            if (!$table->store()) {
                return false;
            }

            if (!$table->rebuildPath($table->id)) {
                return false;
            }
        }

        return true;
    }

    private function saveConfig($data){
        $keys = array_keys($data);

        $result = $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` IN ('".implode("', '", $keys)."')");

        if(!$result) {
            return false;
        }

        $sql = "INSERT INTO " . DB_PREFIX . "setting (`store_id`, `code`, `key`, `value`, `serialized`) VALUES";

        foreach($data as $key => $value) {
            $serialized = 0;
            if($key == 'config_miwoshop') {
                $serialized = 1;
            }

            $values[]= "('0', 'config', '".$key."', '".$value."', '".$serialized."')";
        }

        $sql .= implode(',', $values);

        $this->db->query($sql);
    }

    private function getMiwoSettings(){
        $query = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "setting WHERE `key`='config_miwoshop' ");

        $result = array();

        if(!empty($query->row)) {
            $result = unserialize($query->row['value']);
        }

        return $result;
    }


}