<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');

class plgMiwoshopSql extends MPlugin {

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		
		$file = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
		
		if (file_exists($file)) {
			require_once($file);
		}
	}
	
	public function getViewHtml($integration){

        if(!isset($integration['sql'])){
            $integration['sql'] = "";
        }

		$html  = '<fieldset style="width:47%; float: left; margin: 5px;">';
        $html .=    '<legend>SQL Query</legend>';
        $html .=        '<table class="form">';
        $html .=            '<tr>';
        $html .=                "<td><strong>Run Custom Query</strong></br> </br> 5~INSERT INTO ...|5~INSERT INTO ... </br>(orderstatusid=sqlquery)</td>";
        $html .=                '<td><textarea name="content[sql][sql]" style="width:350px; height:60px !important;">'. $integration['sql'] .'</textarea></td>';
        $html .=            '</tr>';
        $html .=        '</table>';
        $html .=    '</fieldset>';
		
		return $html;
	}
	
    public function onMiwoshopBeforeOrderStatusUpdate($data, $order_id, $order_status_id, $notify) {
        if(isset($_SESSION['OrderStatusUpdate'.$order_id])){
            unset($_SESSION['OrderStatusUpdate'.$order_id]);
            return;
        }

        $results = self::_runQuery($data, $order_id, $order_status_id, $notify);
        $_SESSION['OrderStatusUpdate'.$order_id] = 1;
    }

    public function onMiwoshopBeforeOrderConfirm($data, &$order_id, &$order_status_id, &$notify) {
        $results = self::_runQuery($data, $order_id, $order_status_id, $notify);
    }

    private function _runQuery($data, $order_id, $order_status_id, $notify){
        $db = MFactory::getDBO();

        $db->setQuery("SELECT * FROM #__miwoshop_order_product WHERE order_id = " . $order_id);
        $order_products = $db->loadAssocList();
		
		if (empty($order_products)) {
			return;
		}

        foreach($order_products as $order_product)
        {
            $order_product_intg = MiwoShop::get('base')->getIntegrations($order_product['product_id']);
            if (isset($order_product_intg->sql)) {
                $tmp = $order_product_intg->sql;

                if(isset($tmp->sql) && isset($tmp->sql->$order_status_id)){
                    $query = $tmp->sql->$order_status_id;
                    $querys = $db->splitSql($query);

                    foreach($querys as $_query){
                        $db->setQuery($_query);
                        $db->query();
                    }

                }
            }
        }
    }
}