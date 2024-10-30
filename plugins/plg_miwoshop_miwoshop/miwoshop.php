<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');

class plgMiwoshopMiwoshop extends MPlugin {

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		
		$file = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
		
		if (file_exists($file)) {
			require_once($file);
		}
	}
	
	public function getViewHtml($integration){

        if(!isset($integration['miwoshop_add'])){
            $integration['miwoshop_add'] = "";
        }

		$html  = '<fieldset style="width:47%; float: left; margin: 5px;">';
        $html .=    '<legend>MiwoShop</legend>';
        $html .=        '<table class="form">';
        $html .=            '<tr>';
        $html .=                '<td><strong>Customer Group</strong></br> </br> 5=8,3=3 </br>(orderstatusid=customergroupid)</td>';
        $html .=                '<td><textarea name="content[miwoshop][add]" style="width:350px; height:60px !important;">'. $integration['miwoshop_add'] .'</textarea></td>';
        $html .=            '</tr>';
        $html .=        '</table>';
        $html .= '</fieldset>';
		
		return $html;
	}
	
    public function onMiwoshopBeforeOrderStatusUpdate($data, $order_id, $order_status_id, $notify) {
        $results = self::_updateGroup($data, $order_id, $order_status_id, $notify);
    }

    public function onMiwoshopBeforeOrderConfirm($data, &$order_id, &$order_status_id, &$notify) {
        $results = self::_updateGroup($data, $order_id, $order_status_id, $notify);
    }

    private function _updateGroup($data, $order_id, $order_status_id, $notify){
        $db = MFactory::getDBO();
        $db->setQuery("SELECT * FROM #__miwoshop_order_product WHERE order_id = " . $order_id);
        $order_products = $db->loadAssocList();
		
		if (empty($order_products)) {
			return;
		}
       
		foreach($order_products as $order_product)
        {
            $order_product =  MiwoShop::get('base')->getIntegrations($order_product['product_id']);
            if (isset($order_product->miwoshop)) {
                $tmp = $order_product->miwoshop;

                if(isset($tmp->add) && isset($tmp->add->$order_status_id)){
                    $groups = $tmp->add->$order_status_id;
                    self::_addUserToGroup($order_id, $groups);
                }
            }
        }
    }
	
	protected function _addUserToGroup( $order_id, $groupid )
    {
        if(!isset($groupid[0])){
            return true;
        }
		
		$customer_info = MiwoShop::get('db')->run("SELECT customer_id, email FROM #__miwoshop_order WHERE order_id = {$order_id}", 'loadRow');
		$db = MFactory::getDbo();
        
		$query = 'UPDATE #__miwoshop_customer SET `customer_group_id` = ' . $groupid[0] . ' WHERE `customer_id` = ' . $customer_info[0];
        
        $db->setQuery( $query );
        $db->query();
    }
}