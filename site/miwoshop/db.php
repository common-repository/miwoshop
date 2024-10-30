<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG .'/miwoshop/site/miwoshop/miwoshop.php');

class MiwoShopDb {

    public function getRecord($id, $type = 'product') {
        $id = intval($id);

        if (empty($id)) {
            return '';
        }

        static $rows = array('product' => array(), 'category' => array(), 'manufacturer' => array(), 'information' => array());

        if (!isset($rows[$type][$id])) {
            $rows[$type][$id] = MiwoShop::get('opencart')->loadModelFunction('catalog/'.$type.'/get'.ucfirst($type), $id);
        }

        return $rows[$type][$id];
   	}

	public function getRecordName($id, $type = 'product') {
        $field = 'name';
        if ($type == 'information') {
            $field = 'title';
        }

        $record = $this->getRecord($id, $type);

        if (!empty($record) && is_array($record)) {
            return $record[$field];
        }
        else {
            return '';
        }
	}

	public function getRecordAlias($id, $type = 'product', $_query = array()) {
        $id = intval($id);

        if (empty($id)) {
            return '';
        }

        static $rows = array('product' => array(), 'category' => array(), 'manufacturer' => array(), 'information' => array());

        if (!isset($rows[$type][$id])) {
            $query = $type.'_id='.$id;

            $lang_id = MiwoShop::get('opencart')->get('config')->get('config_language_id');
            if(isset($_query['_lang'])){
                $lang_id = $_query['_lang'];
            }
            $_name = $this->run("SELECT keyword FROM #__miwoshop_url_alias WHERE query = '{$query}' AND language_id = '{$lang_id}' LIMIT 1", 'loadResult');

            if (empty($_name)) {
                $_name = $this->getRecordName($id, $type);
            }
			
			$alias = html_entity_decode($_name, ENT_QUOTES, 'UTF-8');
        
			if (MFactory::getConfig()->get('unicodeslugs') == 1) {
				$alias = MFilterOutput::stringURLUnicodeSlug($alias);
			}
			else {
				$alias = MFilterOutput::stringURLSafe($alias);
			}

            $rows[$type][$id] = $alias;
        }

        return $rows[$type][$id];
	}

    public function getProductCategoryId($id) {
        $id = intval($id);

        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = $this->run("SELECT category_id FROM #__miwoshop_product_to_category WHERE product_id = ". (int) $id." LIMIT 1", 'loadResult');
        }

        return $cache[$id];
    }

    public function getParentCategoryId($id) {
        $id = intval($id);

        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = $this->run("SELECT parent_id FROM #__miwoshop_category WHERE category_id = ". (int) $id." LIMIT 1", 'loadResult');
        }

        return $cache[$id];
    }

    public function getCategoryNames($id) {
        $id = intval($id);

        static $cache = array();

        if (!isset($cache[$id])) {
            $p_id = $id;
            $categories = array();

            $config = MiwoShop::get('opencart')->get('config');
            if (is_object($config)) {
                $lang = ' AND cd.language_id = '.$config->get('config_language_id');
            }
            else {
                $lang = '';
            }

            while ($p_id > 0) {
                $row = $this->run("SELECT cd.name, c.parent_id, c.category_id AS id "
                    ."FROM #__miwoshop_category AS c, #__miwoshop_category_description AS cd "
                    ."WHERE c.category_id = cd.category_id "
                    ."AND c.category_id = {$p_id} "
                    ."{$lang}", 'loadObject');

                array_unshift($categories, $row);
                $p_id = $row->parent_id;
            }

            $cache[$id] = $categories;
        }

        return $cache[$id];
    }

    public function getLanguageList($published = null) {
        static $language_data;
        static $language_data_all;

        if(empty($language_data_all) and $published == 'all'){
            $language_data_all = array();
            if(MFactory::isW()){ #miwo
                $language_data_all = $this->_getOcLanguageList('all');
            }
            else {
                $language_data_all = $this->_getLanguageList('all');
            }

            return $language_data_all;
        }
        else if(!empty($language_data_all) and $published == 'all'){
            return $language_data_all;
        }

        if(empty($language_data) and $published == null){
            $language_data = array();
            if(MFactory::isW()){ #miwo
                $language_data = $this->_getOcLanguageList();
            }
            else {
                $language_data = $this->_getLanguageList();
            }

            return $language_data;
        }
        if(!empty($language_data) and $published == null){
            return $language_data;
        }
    }

    public function _getLanguageList($published = null) {
        $_language_data = array();

        $where = 'WHERE published = 1';
        if($published == 'all'){
            $where = '';
        }

        $results = MiwoShop::get('db')->run("SELECT * FROM #__languages {$where} ORDER BY ordering, title", 'loadAssocList');

        foreach ($results as $result) {
            $_language_data[$result['sef']] = array(
                'language_id' => $result['lang_id'],
                'name'        => $result['title_native'],
                'code'        => $result['sef'],
                'locale'      => $result['lang_code'],
                'image'       => $result['image'].'.gif',
                'directory'   => 'english',
                'filename'    => 'english',
                'sort_order'  => $result['ordering'],
                'status'      => $result['published']
            );
        }

        return $_language_data;
    }

    public function _getOcLanguageList($published = null) { #miwo
        $_language_data = array();

        $where = 'WHERE status = 1';
        if($published == 'all'){
            $where = '';
        }

        $results = MiwoShop::get('db')->run("SELECT * FROM #__miwoshop_language {$where} ORDER BY sort_order, name", 'loadAssocList');

        foreach ($results as $result) {
            $_language_data[$result['code']] = $result;
        }

        return $_language_data;
    }

    public function getLanguage($var, $is_code = false) {
        $lang = null;
        $rows = $this->getLanguageList('all');

        foreach ($rows as $row) {
            if ($is_code == true) {
                $v = 'locale';
            }
            else {
                $v = 'language_id';
            }

            if (strpos($row[$v], $var) !== false) { #miwo
                $lang = $row;
                break;
            }
        }

        return $lang;
    }

    public function run($q, $f = 'loadAssoc', $p = null) {
   		$db = $this->getDbo();

   		switch($f) {
   			case 'escape':
   				$result = $db->$f($q);

   				break;
   			case 'Quote':
   			case 'getEscaped':
				if ($f == 'getEscaped') {
                    $f = 'escape';
                }

   				$result = $db->$f($q);

   				break;
   			case 'getLastId':
   				$result = $db->insertid();
   				break;
   			default:
   				$db->setQuery($q);
				if ($f == 'loadResultArray') {
					$f = 'loadColumn';
				}

   				if (empty($p)) {
   					$result = $db->$f();
   				}
   				else {
   					$result = $db->$f($p);
   				}
				
   				break;
   		}

   		return $result;
   	}

    public function getDbo() {
        static $db;

        if (!isset($db)) {
            $config = MiwoShop::getClass('base')->getConfig();

            if ($config->get('multistore', 0) == 0) {
                $db = MFactory::getDbo();
            }
            else {
                mimport('framework.database.database');

                $j_config = MiwoShop::getClass('base')->getMConfig();

                $options = array();
                $options['website']   	= MiwoShop::get()->getFullUrl();
                $options['driver']   	= $config->get('multistore_driver', $j_config->dbtype);
                $options['host']     	= $config->get('multistore_host', $j_config->host);
                $options['user']     	= $config->get('multistore_user', $j_config->user);
                $options['password'] 	= $config->get('multistore_password', $j_config->password);
                $options['database'] 	= $config->get('multistore_database', $j_config->db);
                $options['prefix']   	= $config->get('multistore_prefix', $j_config->dbprefix);

                $db	= MDatabase::getInstance($options);
            }
        }

        return $db;
    }

    public function getDbAttribs($name) {
        static $db;

        if (!isset($db)) {
            $db = array();

            $config = MiwoShop::get()->getConfig();
            //$j_config = MiwoShop::get()->getMConfig();
            global $wpdb;


            if ($config->get('multistore', 0) == 0) {
                $db['driver']   	= '';
                $db['host']     	= $wpdb->dbhost;
                $db['user']     	= $wpdb->dbuser;
                $db['password'] 	= $wpdb->dbpassword;
                $db['database'] 	= $wpdb->dbname;
                $db['prefix']   	= $wpdb->prefix;
            }
            else {
                $db['driver']   	= $config->get('multistore_driver', '');
                $db['host']     	= $config->get('multistore_host', $wpdb->dbhost);
                $db['user']     	= $config->get('multistore_user', $wpdb->dbuser);
                $db['password'] 	= $config->get('multistore_password', $wpdb->dbpassword);
                $db['database'] 	= $config->get('multistore_database', $wpdb->dbname);
                $db['prefix']   	= $config->get('multistore_prefix', $wpdb->prefix);
            }
        }

        return $db[$name];
    }
	
	public function convertToGenaral_ci() {
        $db = MFactory::getDbo();
        $db_name =  MiwoShop::getClass('db')->getDbAttribs('database');

        $query2 = "SELECT CONCAT('ALTER TABLE ',TABLE_SCHEMA,'.',TABLE_NAME,' CHANGE `',COLUMN_NAME,'` `',COLUMN_NAME,'` ',COLUMN_TYPE,' CHARACTER SET utf8 COLLATE utf8_general_ci;') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '". $db_name ."' AND TABLE_NAME LIKE '%miwoshop%' AND COLLATION_NAME = 'utf8_bin'";

        $db->setQuery($query2);
        $tmp = $db->loadColumn();

        if(!empty($tmp)) {

            foreach($tmp as $_query) {
                $db->setQuery($_query);
                $db->query();
            }
            $_SESSION['is_db_sync'] = true;
        }

        $mainframe = MFactory::getApplication();
        $mainframe->redirect('index.php?option=com_miwoshop&ctrl=syncdone', MText::_('COM_MIWOSHOP_DB_SYNC_DONE'));
    }

    public function isDbSync(){
        if(isset($_SESSION['is_db_sync'])){
            return $_SESSION['is_db_sync'];
        }

        $db = MFactory::getDbo();
        $db_name =  MiwoShop::getClass('db')->getDbAttribs('database');
        $query = "select * from information_schema.COLUMNS WHERE TABLE_SCHEMA = '". $db_name ."' AND TABLE_NAME LIKE '%miwoshop%' AND COLLATION_NAME = 'utf8_bin'";
        $db->setQuery($query);
        $tmp = $db->loadRowList();

        if(!empty($tmp)){
            $_SESSION['is_db_sync'] = false;
            return false;
        }
        else{
            $_SESSION['is_db_sync'] = true;
            return true;
        }

        return true;
    }

    public function getOcLanguages() {
        $language_data = array();

        $results = MiwoShop::get('db')->run("SELECT * FROM #__miwoshop_language WHERE status = 1 ORDER BY sort_order, name", 'loadAssocList');

        foreach ($results as $result) {
            $language_data[$result['code']] = array(
                'language_id' => $result['language_id'],
                'name'        => $result['name'],
                'code'        => $result['code'],
                'locale'      => $result['locale'],
                'image'       => $result['image'],
                'directory'   => $result['directory'],
                'filename'    => $result['filename'],
                'sort_order'  => $result['sort_order'],
                'status'      => $result['status']
            );
        }

        return $language_data;
    }

    public function getInstalledJoomlaLanguages($client = 0) {

        $langlist = array();

        $results = MiwoShop::get('db')->run("SELECT name, element FROM #__extensions WHERE type = 'language' AND state = 0 AND enabled = 1 AND client_id= ". (int) $client, 'loadAssocList');

        foreach ($results as $result) {
            $_result = explode('-', $result['element']);

            if($result['element'] == 'pt-BR'){
                $_result[0] = strtolower($result['element']);
            }

            $langlist[$_result[0]] = array(
                'code' => $_result[0],
                'tag'  => $result['element'],
                'name'  => $result['name']
            );
        }

        return $langlist;
    }

    public function getRecordAliases($id, $type = 'product') {
        $id = intval($id);

        if (empty($id)) {
            return '';
        }

        static $rows = array('product' => array(), 'category' => array(), 'manufacturer' => array(), 'information' => array());

        if (!isset($rows[$type][$id])) {
            $query = $type.'_id='.$id;

            $_names = $this->run("SELECT keyword, language_id FROM #__miwoshop_url_alias WHERE query = '{$query}'", 'loadAssocList');
			$aliases = array();
            foreach($_names as $name){

                $alias = $name['keyword'];

                if (MFactory::getConfig()->get('unicodeslugs') == 1) {
                    $alias = MFilterOutput::stringURLUnicodeSlug($alias);
                }
                else {
                    $alias = MFilterOutput::stringURLSafe($alias);
                }

                $aliases[$name['language_id']] = $alias;
            }

            $rows[$type][$id] = $aliases;

        }

        return $rows[$type][$id];
    }
}