<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');
 
class ModelPaymentJomsocialpoints extends Model {

  	public function getMethod($address, $total) {
		$this->load->language('payment/jomsocialpoints');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('jomsocialpoints_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('jomsocialpoints_total') > 0 && $this->config->get('jomsocialpoints_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('jomsocialpoints_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	
		
		$file = MPATH_ROOT.'/components/com_community/community.php';

        if(file_exists($file)){
            $user       = MFactory::getUser();
            $userPoint  = $this->getJSPoints($user->id);
            $amount     = $total * $this->config->get('jomsocialpoints_points');

            if($userPoint < $amount){
                $status = false;
            }

        } else {
            $status = false;
        }
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'jomsocialpoints',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('jomsocialpoints_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
	
		
	public function getJSPoints($userId) {
		$query = $this->db->query("SELECT points FROM #__community_users WHERE userid = '" . $userId."'");
        $points = $query->row['points'];
        if ($query->row['points'] <  1){ $points = 0; }

        return $points;
	}
	
	public function updateJSPoints($points, $user_id) {
		$result = $this->db->query("UPDATE #__community_users SET  points = '" . $points ."' WHERE userid = '" . $user_id ."'");

		return $result;
	}
}
?>