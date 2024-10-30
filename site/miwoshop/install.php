<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
require_once(MPATH_WP_PLG .'/miwoshop/site/miwoshop/miwoshop.php');

class MiwoShopInstall {

	public function createTables() {
		$db = MiwoShop::get('db')->getDbo();

		$tables	= $db->getTableList();
		$miwoshop_address = $db->getPrefix().'miwoshop_address';
		if (!is_array($tables) || in_array($miwoshop_address, $tables)) {
			return;
		}

		$this->_runSqlFile(MPATH_MIWOSHOP_ADMIN.'/install.sql');
	}

	public function createUserTables() {
        $db = MiwoShop::get('db');
        $jdb = MiwoShop::get('db')->getDbo();

        $this->_createUserMapTables();

        $tables	= $jdb->getTableList();
		$miwoshop_user = $jdb->getPrefix().'miwoshop_user';
		if (!is_array($tables) || in_array($miwoshop_user, $tables)) {
			return;
		}

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_user` (
		  `user_id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_group_id` int(11) NOT NULL,
		  `username` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `password` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `salt` varchar(9) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `firstname` varchar(32) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `lastname` varchar(32) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `email` varchar(96) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `code` varchar(32) COLLATE utf8_general_ci NOT NULL,
		  `ip` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
		  `status` tinyint(1) NOT NULL,
		  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`user_id`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();
	}

    public function _createUserMapTables() {
        $db = MiwoShop::get('db');
        $jdb = MiwoShop::get('db')->getDbo();

        $tables	= $jdb->getTableList();
        $miwoshop_muser_ocustomer_map = $jdb->getPrefix().'miwoshop_muser_ocustomer_map';
        if (!is_array($tables) || in_array($miwoshop_muser_ocustomer_map, $tables)) {
            return;
        }

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_muser_ocustomer_map` (
          `muser_id` INT(11) NOT NULL,
          `ocustomer_id` INT(11) NOT NULL,
          PRIMARY KEY (`muser_id`),
          UNIQUE (`ocustomer_id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_muser_ouser_map` (
          `muser_id` INT(11) NOT NULL,
          `ouser_id` INT(11) NOT NULL,
          PRIMARY KEY (`muser_id`),
          UNIQUE (`ouser_id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();
    }

	public function createApiUser(){
		$jdb = MiwoShop::get('db')->getDbo();
		
		// create order API user
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$api_username = '';
		$api_password = '';

		for ($i = 0; $i < 64; $i++) {
			$api_username .= $characters[rand(0, strlen($characters) - 1)];
		}

		for ($i = 0; $i < 256; $i++) {
			$api_password .= $characters[rand(0, strlen($characters) - 1)];
		}

		$jdb->setQuery("INSERT INTO `#__miwoshop_api` SET username = '" . $jdb->escape($api_username) . "', `password` = '" . $jdb->escape($api_password) . "', status = 1, date_added = NOW(), date_modified = NOW()");
		$jdb->query();
		
		$api_id = $jdb->insertid();

		$jdb->setQuery("DELETE FROM `#__miwoshop_setting` WHERE `key` = 'config_api_id'");
		$jdb->query();
		$jdb->setQuery("INSERT INTO `#__miwoshop_setting` SET `code` = 'config', `key` = 'config_api_id', value = '" . (int)$api_id . "'");
		$jdb->query();
	}
	
	public function createGroupTables() {
        $db = MiwoShop::get('db');
        $jdb = MiwoShop::get('db')->getDbo();

		$tables	= $jdb->getTableList();
		$miwoshop_mgroup_cgroup_map = $jdb->getPrefix().'miwoshop_mgroup_cgroup_map';
		if (!is_array($tables) || in_array($miwoshop_mgroup_cgroup_map, $tables)) {
			return;
		}

        $registered = 'subscriber';
        $publisher = 'author';
        $administrator = 'administrator';

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_mgroup_cgroup_map` (
		  `mgroup_id` VARCHAR(255) NOT NULL,
		  `cgroup_id` INT(11) NOT NULL,
		  PRIMARY KEY (`cgroup_id`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();

		$customer_groups = $db->run('SELECT customer_group_id FROM #__miwoshop_customer_group', 'loadColumn');
		if (!empty($customer_groups)) {
            foreach ($customer_groups as $customer_group) {
                $j_group = $registered;
                if ($customer_group == 6) {
                    $j_group = $publisher;
                }

                $db->run("INSERT INTO #__miwoshop_mgroup_cgroup_map SET mgroup_id = '{$j_group}', cgroup_id = '{$customer_group}'", 'query');
            }
        }

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_mgroup_ugroup_map` (
      		  `mgroup_id` VARCHAR(255) NOT NULL,
      		  `ugroup_id` INT(11) NOT NULL,
      		  PRIMARY KEY (`ugroup_id`)
      		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();

        $user_groups = $db->run('SELECT user_group_id FROM #__miwoshop_user_group', 'loadColumn');
		if (!empty($user_groups)) {
            foreach ($user_groups as $user_group) {
                $db->run("INSERT INTO #__miwoshop_mgroup_ugroup_map SET mgroup_id = '{$administrator}', ugroup_id = '{$user_group}'", 'query');
            }
        }
	}

    public function createIntegrationTables() {
        $db = MiwoShop::get('db');
        $jdb = MiwoShop::get('db')->getDbo();

        $tables	= $jdb->getTableList();
        $miwoshop_j_integrations = $jdb->getPrefix().'miwoshop_j_integrations';
        if (!is_array($tables) || in_array($miwoshop_j_integrations, $tables)) {
            return;
        }

        $jdb->setQuery("CREATE TABLE IF NOT EXISTS `#__miwoshop_j_integrations` (
			`product_id` INT NOT NULL,
			`content` TEXT NOT NULL
			) CHARSET=utf8 COLLATE=utf8_general_ci;");
        $jdb->query();
    }

    public function addPage(){
        $page_content="<!-- MiwoShop Shortcode. Please do not remove to shopping cart work properly. -->[miwoshop]<!-- MiwoShop Shortcode End. -->";
        add_option("miwoshop_page_id",'','','yes');

        $miwoshop_post  = array();
        $_tmp_page      = null;

        $id = get_option("miwoshop_page_id");

        if (!empty($id) && $id > 0) {
            $_tmp_page = get_post($id);
        }

        if ($_tmp_page != null){
            $miwoshop_post['ID']            = $id;
            $miwoshop_post['post_status']   = 'publish';

            wp_update_post($miwoshop_post);
        }
        else{
            $miwoshop_post['post_title']    = 'Shop';
            $miwoshop_post['post_content']  = $page_content;
            $miwoshop_post['post_status']   = 'publish';
            $miwoshop_post['post_author']   = 1;
            $miwoshop_post['post_type']     = 'page';
            $miwoshop_post['comment_status']= 'closed';

            $id = wp_insert_post($miwoshop_post);
            update_option('miwoshop_page_id',$id);
        }
    }

    public function _runSqlFile($sql_file) {
        $db = MiwoShop::get('db')->getDbo();
		
        if (!file_exists($sql_file)) {
            return;
        }

        $buffer = file_get_contents($sql_file);

        if ($buffer === false) {
            return;
        }

        $queries = $db->splitSql($buffer);

        if (count($queries) == 0) {
            return;
        }

        foreach ($queries as $query) {
            $query = trim($query);

            if ($query != '' && $query{0} != '#') {
                $db->setQuery($query);

                if (!$db->query()) {
                    MError::raiseWarning(1, 'JInstaller::install: '.MText::_('SQL Error')." ".$db->stderr(true));
                    return;
                }
            }
        }
    }

	public function upgrade300(){
        
   	}

    public function checkLanguage(){
        $db = MiwoShop::get('db');

        $oc_langs   = self::getOcLanguages();
        $j_langs    = self::getInstalledJoomlaLanguages();
        $j_contents = self::getLanguageList();

        foreach ($oc_langs as $key => $oc_lang) {
            if(isset($j_langs[$key]) and !isset($j_contents[$key])) {
                $db->run("INSERT INTO #__languages SET lang_code = '".$j_langs[$key]['tag']."', title = '".$j_langs[$key]['name']."', title_native = '".$j_langs[$key]['name']."', sef ='".$j_langs[$key]['code']."', image ='".$j_langs[$key]['code']."', published = 1, access = 1, ordering = 0", 'query');
            }
        }
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

        //$results = MiwoShop::get('db')->run("SELECT name, element FROM #__extensions WHERE type = 'language' AND state = 0 AND enabled = 1 AND client_id= ". (int) $client, 'loadAssocList');
        $results = array();

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
	
	public function getLanguageList() {

		$language_data = array();

		$results = MiwoShop::get('db')->run("SELECT * FROM #__languages ORDER BY ordering, title", 'loadAssocList');

		foreach ($results as $result) {
			$language_data[$result['sef']] = array(
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
        

        return $language_data;
    }
	
    private function _getPath($cat_id, $path = array()){
        $jdb = MiwoShop::get('db')->getDbo();
		
        $jdb->setQuery("SELECT parent_id FROM `#__miwoshop_category` WHERE category_id = ".$cat_id);
        $parent_id = $jdb->loadResult();

        if ((int)$parent_id != 0) {
            $path[] = $parent_id;
            $path = self::_getPath($parent_id, $path);
        }

        return $path;
    }
}