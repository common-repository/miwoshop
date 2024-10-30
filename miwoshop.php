<?php
/*
Plugin Name: MiwoShop
Plugin URI: http://miwisoft.com
Description: MiwoShop is a powerful shopping cart that is designed user friendly and extremely powerful out of the box with tons of built-in features.
Author: Miwisoft
Version: 3.1.0
Author URI: http://miwisoft.com/miwoshop
*/

defined('ABSPATH') or die('MIWI');

if (!class_exists('MWordpress')) {
    require_once(dirname(__FILE__) . '/wordpress.php');
}

final class MShop extends MWordpress {

    public function __construct() {
	    if (!defined('MURL_MIWOSHOP')) {
			define('MURL_MIWOSHOP', plugins_url('', __FILE__));
		}

	    if (!defined('MIWOSHOP_PACK')) {
			define('MIWOSHOP_PACK', 'lite');
		}
        
		parent::__construct('miwoshop', '33.0001', false);
        
        if (!defined('MID_PATH')) {
            $url = str_replace(str_replace('\\', '/', ABSPATH), '', str_replace('\\', '/', MPATH_WP_PLG));
            define('MID_PATH', $url);
        }
    }
	
    public function initialise() {
        $miwi = MPATH_WP_CNT.'/miwi/initialise.php';

        if (!file_exists($miwi)) {
            return false;
        }

        require_once($miwi);

        $this->app = MFactory::getApplication();

        $this->app->initialise();

        mimport('framework.plugin.helper');
        mimport('framework.application.component.helper');
        MPluginHelper::importPlugin('miwoshop');

        require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');
        $m_user = MiwoShop::get('user');
		
		add_action('wp_head', array($this, 'pageUrl'), 999);

        # wordpress user hooks
        add_action('profile_update', array($m_user, 'user_update'));
        add_action('user_register', array($m_user, 'user_register'));
        add_action('wp_logout', array($m_user, 'user_logout'));
        add_action('deleted_user', array($m_user, 'delete_miwouser'));
        add_action('reset_password', array($m_user, 'reset_password_wp'));
        ##############################################

        add_filter('wp_authenticate_user', array($m_user, 'isCustomerApproved'), 10, 2);

		# auto upgrade
        mimport('joomla.application.component.helper');
        $config = MiwoShop::get('base')->getConfig();

		if(!empty($config) and file_exists(MPATH_WP_CNT.'/miwi/autoupdate.php')) {
			$pid = $config->get('pid');
			if(!empty($pid)) {
				$path = 'http://miwisoft.com/index.php?option=com_mijoextensions&view=download&pack=upgrade&model=' . $this->context.'&pid=' . $pid;
				require_once(MPATH_WP_CNT.'/miwi/autoupdate.php');
				new MiwisoftAutoUpdate($path, $this->context);
			}
		}
		##############

        $dispatcher = MDispatcher::getInstance();
        $dispatcher->trigger('onInit', array());
    }
	
	public function pageUrl(){
        $p_id = MFactory::getWOption('miwoshop_page_id');
        echo '<script type="text/javascript">
        var miwoshop_page_id = \''. $p_id .'\';
        </script>';
    }
}

$mshop = new MShop();

register_activation_hook(__FILE__, array($mshop, 'activate'));
register_deactivation_hook(__FILE__, array($mshop, 'deactivate'));


