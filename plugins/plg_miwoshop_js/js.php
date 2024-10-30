<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU GPL, based on AceShop, www.joomace.net
*/

// no direct access
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');
mimport('framework.environment.browser');
mimport('framework.application.module.helper');

class plgMiwoshopJs extends MPlugin {

	public $p_params = null;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);

        $miwoshop = MPATH_WP_PLG .'/miwoshop/site/miwoshop/miwoshop.php';
        $library = MPATH_WP_PLG .'/miwoshop/site/opencart/config.php';
        if (!file_exists($miwoshop) or !file_exists($library)) {
            return;
        }

		require_once($miwoshop);

        $plugin = MPluginHelper::getPlugin('miwoshop', 'miwoshopjs');
		
		if (!is_object($plugin)) {
			$plugin = new stdClass();
			$plugin->params = '';
		}
		
        $this->p_params = new MRegistry($plugin->params);
	}

    public function onInit(){
        
    }
}