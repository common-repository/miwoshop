<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.user.user');
mimport('framework.user.helper');
require_once(MPATH_WP_PLG .'/miwoshop/site/miwoshop/miwoshop.php');

if ( !function_exists('wp_get_current_user') ) { #miwo
    require_once(ABSPATH. 'wp-includes/pluggable.php');
}

class MiwoShopUser {

    public function createOAccountFromJ($user_id, $is_login = false) {
        static $cache = array();

        if (empty($user_id)) {
            return false;
        }

        if (isset($cache[$user_id])) {
            return $cache[$user_id];
        }

        $wp_user = get_user_by('id', $user_id);

        if (empty($wp_user)) {
            return false;
        }

        $db = MiwoShop::get('db');

        $username = $wp_user->user_login;
        $email = $wp_user->user_email;
        $password = $this->getCleanPassword($wp_user->user_pass);

        $fname = $wp_user->user_login;
        if(!empty($wp_user->first_name)){
            $fname = $wp_user->first_name;
        }

        $lname = '';
        if(!empty($wp_user->last_name)){
            $lname = $wp_user->last_name;
        }

        $status = empty($wp_user->user_status) ? 1 : 0;

        $customer_id = $this->getOCustomerIdFromJUser($wp_user->ID, $email);
        $customer_exists = $this->getOCustomerById($customer_id);

        $_id = null;

        foreach ($wp_user->roles as $group_id) {
            $_id = $this->getOCustomerGroupIdByJGroup($group_id);

            if (!empty($_id)) {
                $customer_group_id = $_id;
                break;
            }
        }

        if (empty($customer_group_id)) {
            $customer_group_id = 1;
        }

        if (!empty($customer_exists)) {
		
            $db->run("UPDATE #__miwoshop_customer SET firstname = '".$fname."', lastname = '".$lname."', email = '".$email."',  password = '".$password."', customer_group_id = '".$customer_group_id."', status = ".$status." WHERE customer_id = '".$customer_id."'", 'query');
		}
        else {
            $db->run("INSERT INTO #__miwoshop_customer SET firstname = '".$fname."', lastname = '".$lname."', email = '".$email."', telephone = '', fax = '', password = '".$password."', newsletter = '0', customer_group_id = '".$customer_group_id."', status = '".$status."', approved = '".$status."', date_added = NOW()", 'query');
            $customer_id = $db->run('', 'getLastId');

            $db->run("INSERT IGNORE INTO #__miwoshop_muser_ocustomer_map SET muser_id = '{$wp_user->ID}', ocustomer_id = '".$customer_id."'", 'query');

            $db->run("INSERT INTO #__miwoshop_address SET customer_id = '" . (int)$customer_id . "', firstname = '".$fname."', lastname = '".$lname."'", 'query');

            $address_id = $db->run('', 'getLastId');

            $db->run("UPDATE #__miwoshop_customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'", 'query');

        }


        if (!is_super_admin( $wp_user->ID )) {
            return true;
        }

        $_id = null;

        foreach ($wp_user->roles as $group_id) {
            $_id = $this->getOUserGroupIdByJGroup($group_id);

            if (!empty($_id)) {
                $user_group_id = $_id;
                break;
            }
        }

        if (empty($user_group_id)) {
            return true;
        }

        $user_id = $this->getOUserIdFromJUser($wp_user->ID, $username, $email);
        $user_exists = $this->getOUserById($user_id);

        if (!empty($user_exists)){
            $db->run("UPDATE #__miwoshop_user SET username = '".$username."', password = '".$password."', email = '".$email."', firstname = '".$fname."', lastname = '".$lname."', user_group_id = '".$user_group_id."' WHERE user_id = '".$user_id."'", 'query');
        }
        else {
            $db->run("INSERT INTO #__miwoshop_user SET firstname = '".$fname."', lastname = '".$lname."', email = '".$email."', username = '".$username."', password = '".$password."', user_group_id = '".$user_group_id."', status = '".$status."', date_added = NOW()", 'query');
            $user_id = $db->run('', 'getLastId');

            $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$wp_user->ID}', ouser_id = '".$user_id."'", 'query');
        }

        return true;
    }

    public function deleteOAccountFromJ($user) {
        $db = MiwoShop::get('db');

        $ouser_id = $this->getOUserIdFromJUser((int)$user['id']);
        if (!empty($ouser_id)) {
            $db->run("DELETE FROM #__miwoshop_user WHERE user_id = '".(int)$ouser_id."'", 'query');
            $db->run("DELETE FROM #__miwoshop_muser_ouser_map WHERE ouser_id = '".(int)$ouser_id."'", 'query');
        }

        $customer_id = $this->getOCustomerIdFromJUser((int)$user['id']);

        if (empty($customer_id)) {
            return true;
        }

        $db->run("DELETE FROM #__miwoshop_muser_ocustomer_map WHERE ocustomer_id = '".(int)$customer_id."'", 'query');

        $db->run("DELETE FROM #__miwoshop_customer WHERE customer_id = '".(int)$customer_id."'", 'query');
        $db->run("DELETE FROM #__miwoshop_customer_reward WHERE customer_id = '" . (int)$customer_id . "'", 'query');
        $db->run("DELETE FROM #__miwoshop_customer_transaction WHERE customer_id = '" . (int)$customer_id . "'", 'query');
        $db->run("DELETE FROM #__miwoshop_customer_ip WHERE customer_id = '" . (int)$customer_id . "'", 'query');
        $db->run("DELETE FROM #__miwoshop_address WHERE customer_id = '".(int)$customer_id."'", 'query');

        return true;
    }

    public function loginOFromJ($_class, $user = null, $is_backend = false) {
        $wp_user = wp_get_current_user();

        $type = strtolower(get_class($_class)).'_id';
        if (empty($wp_user->ID)  || $_class->isLogged() || isset($_class->session->data[$type])) {
            return;
        }

        if(empty($user) and $is_backend){
            $user     = $this->getOUserByEmail($wp_user->user_email);
        }

        if(empty($user) and !$is_backend){
            $user     = $this->getOCustomerByEmail($wp_user->user_email);
        }

        if (empty($user)) {
            $user = array();
            $user['id'] = $wp_user->ID;
            $user['name'] = $wp_user->first_name . ' ' . $wp_user->last_name;
            $user['username'] = $wp_user->user_login;
            $user['email'] = $wp_user->user_email;
            $user['password'] = $this->getCleanPassword($wp_user->user_pass);
            $user['groups'] = $wp_user->roles;
            $user['block'] =  empty($wp_user->user_status) ? 1 : 0;

            $this->createOAccountFromJ($wp_user->ID, true);
        }

        $login_var = $user['email'];
        if($is_backend) {
            $login_var = $user['username'];
        }

        $_class->login($login_var, $user['password']);
    }

    public function logoutOFromJ() {
        $customer = MiwoShop::get('opencart')->get('customer');
        $user = MiwoShop::get('opencart')->get('user');

        if (is_object($customer)) {
            $customer->logout();
        }

        if (is_object($user)) {
            $user->logout();
        }

        session_destroy();
    }

    public function redirectOAfterLoginFromJ($_class) {
        $redirected = MFactory::getSession()->get('miwoshop.login.redirected');
        if ($redirected == true) {
            return;
        }

        $token = md5(mt_rand());
        $_class->session->data['token'] = $token;
        $_class->request->set['token'] = $token;

        $option = MRequest::getCmd('option');
        if ($option != 'com_miwoshop') {
            return;
        }

        $link = 'index.php?option=com_miwoshop';

        $route = MRequest::getString('route');
        if (!empty($route)) {
            $link .= '&route='.$route;
        }
		
        $order_id = MRequest::getInt('order_id');
        if (!empty($order_id)) {
            $link .= '&order_id='.$order_id;
        }
		
        $customer_id = MRequest::getInt('customer_id');
        if (!empty($customer_id)) {
            $link .= '&customer_id='.$customer_id;
        }
		
        $product_id = MRequest::getInt('product_id');
        if (!empty($product_id)) {
            $link .= '&product_id='.$product_id;
        }
		
        $category_id = MRequest::getInt('category_id');
        if (!empty($category_id)) {
            $link .= '&category_id='.$category_id;
        }

        $view = MRequest::getWord('view');
        if (!empty($view)) {
            $link .= '&view='.$view;
        }

        $Itemid = MRequest::getInt('Itemid');
        if (!empty($Itemid)) {
            $link .= '&Itemid='.$Itemid;
        }

        MFactory::getSession()->set('miwoshop.login.redirected', true);

        MFactory::getApplication()->redirect($link);
    }

    //-------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------

	public function createJUserFromO($userdata, $o_id) {
        $update         = false;
        $wp_userdata    = array();

        if ( empty($userdata['email']) or (empty($userdata['password']) and empty($o_id)) ) {
            return;
        }

        $ex_user = $this->getJUserByEmail($userdata['email']);

        if($ex_user->ID){
            $update             = true;
            $wp_userdata['ID']  = $ex_user->ID;
        }

        $status = empty($userdata['block']) ? 1 : 0;

        $wp_userdata['user_login']  = $userdata['email'];
        $wp_userdata['user_email']  = $userdata['email'];
        $wp_userdata['first_name']  = $userdata['firstname'];
        $wp_userdata['last_name']   = $userdata['lastname'];
        $wp_userdata['user_status'] = $status;
        if(!empty($userdata['password'])) {
            $wp_userdata['user_pass'] = $userdata['password'];
        } else if ($update == true) {
            $wp_userdata['user_pass'] = $ex_user->data->user_pass;
        }

        if (isset($userdata['customer_group_id'])) {
            $wp_userdata['role'] = $this->getJGroupIdOfCGroup($userdata['customer_group_id']);
        }

        if (isset($userdata['user_group_id'])) {
            $wp_userdata['role'] = $this->getJGroupIdOfUGroup($userdata['user_group_id']);
        }

        if(is_multisite()) {
            $wp_user_id = wpmu_create_user($wp_userdata['user_login'], $wp_userdata['user_pass'], $wp_userdata['user_email']);
        }
        else{
            $wp_user_id = wp_insert_user($wp_userdata);
        }

        $wp_user = get_user_by('id', $wp_user_id);

        if (!$update){
            $db = MiwoShop::get('db');

            if (isset($userdata['user_group_id'])) {
                $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$wp_user_id}', ouser_id = '".$o_id."'", 'query');
                $db->run("UPDATE #__miwoshop_user SET password = '". $wp_user->user_pass ."' WHERE user_id = '". $o_id ."' ", 'query');
            }
            else {
                $db->run("INSERT IGNORE INTO #__miwoshop_muser_ocustomer_map SET muser_id = '{$wp_user_id}', ocustomer_id = '".$o_id."'", 'query');
                $db->run("UPDATE #__miwoshop_customer SET password = '". $wp_user->user_pass ."' WHERE customer_id = '". $o_id ."' ", 'query');
            }
        }

        return true;
	}

    public function updateJUserFromO($j_user_id, $data, $o_id = 0) {
        $this->createJUserFromO($data, $o_id);
    }

    public function updateJUserPasswordFromO($email, $password) {
        $db = MiwoShop::get('db');
        $encrypted_password = $this->encryptPassword($password);

        $db->run("UPDATE #__users SET user_pass = '". $encrypted_password ."' WHERE user_email = '".$db->run($email, 'escape')."'", 'query');

        $user = $this->getOUserByEmail($email);
        if (!empty($user)) {
            $db->run("UPDATE #__miwoshop_user SET password = '". $encrypted_password ."' WHERE email = '".$db->run($email, 'escape')."'", 'query');
        }
		
		return $encrypted_password;
    }

    public function approveJUserFromO($cust_id) {
        $user_id = $this->getJUserIdFromOCustomer($cust_id);

        if (empty($user_id)) {
            return;
        }

        $wp_userdata['user_status'] = 0;

        $wp_user_id = wp_update_user($wp_userdata);
    }

	public function deleteJUserFromO($id, $is_ouser = false) {
		$delete_muser = true;
		
        if ($is_ouser == true) {
            $muser_id = $this->getJUserIdFromOUser($id);
        }
        else {
            $muser_id = $this->getJUserIdFromOCustomer($id);
        }

        if (empty($muser_id)) {
            return;
        }
		
		if ($is_ouser == true) {
			MiwoShop::get('db')->run("DELETE FROM #__miwoshop_muser_ouser_map WHERE muser_id = {$muser_id}", 'query');

            $ocustomer_id = $this->getOCustomerIdFromJUser($muser_id);
            if (!empty($ocustomer_id)) {
                $delete_muser = false;
            }
		}
		else {
			MiwoShop::get('db')->run("DELETE FROM #__miwoshop_muser_ocustomer_map WHERE muser_id = {$muser_id}", 'query');
			
			$ouser_id = $this->getOUserIdFromJUser($muser_id);
			if (!empty($ouser_id)) {
                $delete_muser = false;
            }
		}
		
		if ($delete_muser == true) {
            wp_delete_user($muser_id);
		}
	}
	
    public function loginJFromO($var, $password, $func_suffix = 'Email') {
        global $_wp_user;

        $function = 'getJUserBy'.$func_suffix;
        $wp_user = $this->$function($var);

        if(empty($wp_user->ID)){
            return;
        }

        if(!empty($_wp_user) and $wp_user->ID == $_wp_user->ID) {
            return;
        }

        $credentials['user_login']      = $wp_user->user_login;
        $credentials['user_password']   = $password;
        $credentials['remember']        = false;

        $loggedin_user = wp_signon($credentials);

        return;
    }

    public function logoutJFromO() {
        $user_id = MFactory::getUser()->get('id');

        if (empty($user_id)) {
            return;
        }

        wp_clear_auth_cookie();
        wp_set_current_user(0);
    }

    //-------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------

    public function getOCustomerById($id) {
        $id = (int)$id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$id])) {
            $cache[$id] = $db->run("SELECT * FROM #__miwoshop_customer WHERE customer_id = {$id}");
        }

        return $cache[$id];
    }

    public function getOCustomerByEmail($email) {
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$email])) {
            $_email = $db->run($email, 'Quote');

            $cache[$email] = $db->run("SELECT * FROM #__miwoshop_customer WHERE email = {$_email}");
        }

        return $cache[$email];
    }

    public function getOCustomerIdFromJUser($muser_id, $email = '') {
        $muser_id = (int)$muser_id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$muser_id])) {
            $cache[$muser_id] = MiwoShop::get('db')->run("SELECT ocustomer_id FROM #__miwoshop_muser_ocustomer_map WHERE muser_id = {$muser_id}", 'loadResult');

            if (empty($cache[$muser_id])) {
                if (!empty($email)) {
                    $o_customer = $this->getOCustomerByEmail($email);

                    if (!empty($o_customer['customer_id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ocustomer_map SET muser_id = '{$muser_id}', ocustomer_id = '".$o_customer['customer_id']."'", 'query');

                        $cache[$muser_id] = $o_customer['customer_id'];
                    }
                }
            }
        }

        return $cache[$muser_id];
    }

    public function getOCustomerGroupIdByJGroup($mgroup_id) {
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$mgroup_id])) {
            $cache[$mgroup_id] = $db->run("SELECT cgroup_id FROM #__miwoshop_mgroup_cgroup_map WHERE mgroup_id = '{$mgroup_id}'", 'loadResult');
        }

        return $cache[$mgroup_id];
    }

    public function getOUserIdFromJUser($muser_id, $username = '', $email = '') {
        $muser_id = (int)$muser_id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$muser_id])) {
            $cache[$muser_id] = $db->run("SELECT ouser_id FROM #__miwoshop_muser_ouser_map WHERE muser_id = {$muser_id}", 'loadResult');

            if (empty($cache[$muser_id])) {
                if (!empty($email)) {
                    $o_user = $this->getOUserByEmail($email);

                    if (!empty($o_user['user_id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$muser_id}', ouser_id = '".$o_user['user_id']."'", 'query');

                        $cache[$muser_id] = $o_user['id'];
                    }
                }

                if (empty($cache[$muser_id]) && !empty($username)) {
                    $o_user = $this->getOUserByUsername($username);

                    if (!empty($o_user['user_id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$muser_id}', ouser_id = '".$o_user['user_id']."'", 'query');

                        $cache[$muser_id] = $o_user['id'];
                    }
                }
            }
        }

        return $cache[$muser_id];
    }

    public function getOUserById($id) {
        $id = (int)$id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$id])) {
            $cache[$id] = $db->run("SELECT * FROM #__miwoshop_user WHERE user_id = {$id}");
        }

        return $cache[$id];
    }

    public function getOUserByEmail($email) {
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$email])) {
            $_email = $db->run($email, 'Quote');

            $cache[$email] = $db->run("SELECT * FROM #__miwoshop_user WHERE email = {$_email}");
        }

        return $cache[$email];
    }

    public function getOUserByUsername($username) {
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$username])) {
            $_username = $db->run($username, 'Quote');

            $cache[$username] = $db->run("SELECT * FROM #__miwoshop_user WHERE username = {$_username}");
        }

        return $cache[$username];
    }

    public function getOUserGroupIdByJGroup($mgroup_id) {
        static $cache;

        $mgroup_id = (int)$mgroup_id;

        if (!isset($cache[$mgroup_id])) {
            $cache[$mgroup_id] = MiwoShop::get('db')->run("SELECT ugroup_id FROM #__miwoshop_mgroup_ugroup_map WHERE mgroup_id = {$mgroup_id}", 'loadResult');
        }

        return $cache[$mgroup_id];
    }

    //-------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------

    public function getJUserByEmail($email) {
        $db = MiwoShop::get('db');

        static $cache;

        if (!empty($cache[$email])) {
            return $cache[$email];
        }

        $cache[$email] = get_user_by('email', $db->run($email, 'escape'));

        return $cache[$email];
    }

    public function getJUserByUsername($username) {
        $db = MiwoShop::get('db');

        static $cache;

        if (isset($cache[$username])) {
            return $cache[$username];
        }

        $cache[$username] = get_user_by('login', $db->run($username, 'escape'));

        return $cache[$username];
    }

    public function getJUserIdFromOCustomer($customer_id, $email = '') {
        $customer_id = (int)$customer_id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$customer_id])) {
            $cache[$customer_id] = $db->run("SELECT muser_id FROM #__miwoshop_muser_ocustomer_map WHERE ocustomer_id = {$customer_id}", 'loadResult');

            if (empty($cache[$customer_id])) {
                if (!empty($email)) {
                    $j_user = $this->getJUserByEmail($email);

                    if (!empty($j_user['id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ocustomer_map SET muser_id = '{$j_user['id']}', ocustomer_id = '".$customer_id."'", 'query');

                        $cache[$customer_id] = $j_user['id'];
                    }
                }
            }
        }

        return $cache[$customer_id];
    }

    public function getJUserIdFromOUser($ouser_id, $username = '', $email = '') {
        $ouser_id = (int)$ouser_id;
        $db = MiwoShop::get('db');

        static $cache;

        if (!isset($cache[$ouser_id])) {
            $cache[$ouser_id] = $db->run("SELECT muser_id FROM #__miwoshop_muser_ouser_map WHERE ouser_id = {$ouser_id}", 'loadResult');

            if (empty($cache[$ouser_id])) {
                if (!empty($email)) {
                    $j_user = $this->getJUserByEmail($email);
                    
                    if (!empty($j_user['id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$j_user['id']}', ouser_id = '".$ouser_id."'", 'query');

                        $cache[$ouser_id] = $j_user['id'];
                    }
                }
                
                if (empty($cache[$ouser_id]) && !empty($username)) {
                    $j_user = $this->getJUserByUsername($username);

                    if (!empty($j_user['id'])) {
                        $db->run("INSERT IGNORE INTO #__miwoshop_muser_ouser_map SET muser_id = '{$j_user['id']}', ouser_id = '".$ouser_id."'", 'query');

                        $cache[$ouser_id] = $j_user['id'];
                    }
                }
            }
        }

        return $cache[$ouser_id];
    }

	public function getJGroupId($data) {
        if (isset($data['user_group_id'])) {
            $j_user_group_id = $this->getJGroupIdOfUGroup((int)$data['user_group_id']);
        }
        else {
            $o_customer_group_id = (int)MiwoShop::get('opencart')->get('config')->get('config_customer_group_id');

            if (!empty($data['customer_group_id'])) {
                $o_customer_group_id = (int)$data['customer_group_id'];
            }

            $j_user_group_id = $this->getJGroupIdOfCGroup((int)$o_customer_group_id);
        }

        return $j_user_group_id;
    }

    public function getJGroupIdOfCGroup($customer_group_id) {
        static $cache;

        $customer_group_id = (int)$customer_group_id;

        if (!isset($cache[$customer_group_id])) {
            $cache[$customer_group_id] = MiwoShop::get('db')->run("SELECT mgroup_id FROM #__miwoshop_mgroup_cgroup_map WHERE cgroup_id = {$customer_group_id}", 'loadResult');
        }

        if (empty($cache[$customer_group_id])) {
            $cache[$customer_group_id] = 2;
        }

        return $cache[$customer_group_id];
    }

    public function getJGroupIdOfUGroup($user_group_id) {
        static $cache;

        $user_group_id = (int)$user_group_id;

        if (!isset($cache[$user_group_id])) {
            $cache[$user_group_id] = MiwoShop::get('db')->run("SELECT mgroup_id FROM #__miwoshop_mgroup_ugroup_map WHERE ugroup_id = {$user_group_id}", 'loadResult');
        }

        if (empty($cache[$user_group_id])) {
            $cache[$user_group_id] = 6;
        }

        return $cache[$user_group_id];
    }

    public function getJoomlaUserGroups() {
        return MHtml::_('user.groups', true);
    }

    //-------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------

    public function encryptPassword($password) {
        $encrypted_password = wp_hash_password($password);

        return $encrypted_password;
    }

    public function getEncryptedOPassword($var, $password, $func_suffix = 'Email') {
        #joomladan login olmuş kullanıcının şifresini(cyrpted) döndürür
        $function = 'getJUserBy'.$func_suffix;
        $wp_user = $this->$function($var);

        if (empty($wp_user)) {
            return;
        }

        if($wp_user->user_pass == $password){
            return $password;
        }

        $result = wp_check_password($password, $wp_user->user_pass, $wp_user->ID);

        if($result) {
            return $wp_user->user_pass;
        }
        else{
            return;
        }

    }

    public function getCleanPassword($password) {
        if (substr($password, 0, 4) != '$2y$' and substr($password, 0, 8) != '{SHA256}' and strpos($password, ':')) {
            $a = explode(':', $password);
			$password = $a[0];
        }

        return $password;
    }

    public function getBlockStatus($data) {
        $block = '1';

        $oc_config =  MiwoShop::get('opencart')->get('config');
       
        if (isset($data['status'])) {
            if ($data['status'] == '1') {
                $block = '0';
            }
        }
        else {
            $db = MiwoShop::get('db');
            $approval = $db->run('SELECT approval FROM #__miwoshop_customer_group WHERE customer_group_id = '.$oc_config->get('config_customer_group_id'), 'loadResult');
            
            if (!$approval) {
                $block = '0';
            }
        }

        return $block;
    }

    public function getLastName($name) {
        $lname = '';

        if (!is_array($name)) {
            $name = explode(' ', $name);
        }

        if (count($name) > 1) {
            for($i = 1; $i <= count($name); $i++){
                if (!isset($name[$i])) {
                    continue;
                }

                if ($i == 1) {
                    $lname = $name[$i];
                }
                else {
                    $lname = $lname." ".$name[$i];
                }
            }
        }

        return $lname;
    }

    public function addJUsersToO() {
        MiwoShop::get('install')->createUserTables();
    }

    public function synchronizeAccountsManually($redirect = true) {
        $db = MiwoShop::get('db');

		$users = $db->run('SELECT ID FROM #__users', 'loadAssocList');

		if (!empty($users)) {
			foreach ($users as $user) {
				$this->createOAccountFromJ($user['ID']);
			}
		}

		MiwoShop::get('base')->setConfig('account_sync_done', '1');

        if($redirect) {
            MFactory::getApplication()->redirect(MRoute::_('index.php?option=com_miwoshop&ctrl=syncdone'), MText::_('COM_MIWOSHOP_ACCOUNT_SYNC_DONE'));
        }
    }

    # WP HOOKS
    //-------------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------------

    public function user_register($user_id){
        $this->createOAccountFromJ($user_id);
    }

    public function user_update($user_id){
        $this->createOAccountFromJ($user_id);
    }

    public function user_logout(){
        $this->logoutOFromJ();
    }

    public function delete_miwouser($user_id){
        $this->deleteOAccountFromJ(array('id'=>$user_id));
    }

    public function reset_password_wp($user, $new_pass){
        $db = MiwoShop::get('db');

        $o_user_id = $this->getOUserByEmail($user->user_email);
        $o_cus_id = $this->getOCustomerByEmail($user->user_email);

        if (!empty($o_user_id)) {
            $db->run("UPDATE #__miwoshop_user SET password = '". $new_pass ."' WHERE user_id = '". $o_user_id ."' ", 'query');
        }

        if(!empty($o_cus_id)) {
            $db->run("UPDATE #__miwoshop_customer SET password = '". $new_pass ."' WHERE customer_id = '". $o_cus_id ."' ", 'query');
        }
    }

    public function isCustomerApproved($user) {
        $db = MiwoShop::get('db');

        $o_customer_id = $this->getOCustomerIdFromJUser($user->ID);

        if(empty($o_customer_id)) {
            return $user;
        }

   		$result = $db->run("SELECT DISTINCT approved FROM #__miwoshop_customer c  WHERE c.customer_id = '" . (int)$o_customer_id . "'", 'loadResult');

        if(!empty($result)) {
            return $user;
        }

        return new WP_Error('need_approve', MText::_('COM_MIWOSHOP_AFFILIATE_LOGIN_ERROR_APPROVED'));
   	}

}