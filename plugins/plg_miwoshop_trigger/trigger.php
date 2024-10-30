<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');

class plgMiwoshopTrigger extends MPlugin {

    public $hasView = false;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		
		$file = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
		
		if (file_exists($file)) {
			require_once($file);
		}
	}

    public function onMiwoshopAfterProductSave($data, $product_id, $isNew) {
        $data['id'] = $product_id;
        $results = MiwoShop::get('base')->trigger('onFinderAfterSave', array('com_miwoshop.product', $data, $isNew), 'finder');
    }

    public function onMiwoshopAfterProductDelete($product_id) {
        $results = MiwoShop::get('base')->trigger('onFinderAfterDelete', array('com_miwoshop.product', $product_id), 'finder');
    }

    public function onMiwoshopAfterCategorySave($data, $category_id, $isNew) {
		if (!$isNew) {
            $results = MiwoShop::get('base')->trigger('onFinderChangeState', array('com_miwoshop.category', $category_id, $data['status']), 'finder');
        }
    }
}