<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');
 
class ModelPaymentEasysocialpoints extends Model {

  	public function getMethod($address, $total) {
		$this->load->language('payment/easysocialpoints');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('easysocialpoints_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		$file = MPATH_ROOT.'/components/com_easysocial/easysocial.php';
		
		if ($this->config->get('easysocialpoints_total') > 0 && $this->config->get('easysocialpoints_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('easysocialpoints_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	
		
		$file = MPATH_ROOT.'/components/com_easysocial/easysocial.php';

        if(file_exists($file)){
            $user       = MFactory::getUser();
            $userPoint  = $this->getESPoints($user->id);
            $amount     = $total * $this->config->get('easysocialpoints_points');

            if($userPoint < $amount){
                $status = false;
            }

        } else {
            $status = false;
        }
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'easysocialpoints',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('easysocialpoints_sort_order')
      		);
    	}
   
    	return $method_data;
  	}

    public function getESPointID() {
        $query = $this->db->query("SELECT id FROM #__social_points WHERE command = 'miwoshop.gateway' AND extension = 'com_miwoshop'");
        $point_id = $query->row['id'];

        return $point_id;
    }

    public function getESPoints($userId) {
        $query = $this->db->query("SELECT SUM(points) AS points FROM #__social_points_history WHERE user_id = '" . $userId."' AND state = '1'");
        $points = $query->row['points'];
        if ($query->row['points'] <  1){ $points = 0; }

        return $points;
    }


    public function updateESPoints($points, $user_id) {

       $point_id = $this->getESPointID();

       $result = $this->db->query("INSERT INTO #__social_points_history (`points_id`, `user_id`, `points`, `created`, `state`) VALUES('".$point_id."','".$user_id."','".$points."', NOW(),'1' )");

       return $result;

    }
}
?>