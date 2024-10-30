<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');
 
class ModelPaymentAlphauserpoints extends Model {

  	public function getMethod($address, $total) {
		$this->language->load('payment/alphauserpoints');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('alphauserpoints_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('alphauserpoints_total') > 0 && $this->config->get('alphauserpoints_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('alphauserpoints_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		$file = MPATH_ROOT.'/components/com_alphauserpoints/alphauserpoints.php';

        if(file_exists($file)){
            $user       = MFactory::getUser();
            $userPoint  = $this->getAUPPoints($user->id);
            $amount     = $total * $this->config->get('alphauserpoints_points');

            if($userPoint < $amount){
                $status = false;
            }

        } else {
            $status = false;
        }
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'alphauserpoints',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('alphauserpoints_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
	
	public function getAUPPoints($userId) {
		$query 		= $this->db->query("SELECT points FROM #__alpha_userpoints WHERE userid = '". $userId ."'");
        $points 	= $query->row['points'];

        return $points;
	}
	
	public function bookAUPPoints($userId, $points, $description) {
		$now = strftime("%Y-%m-%d %H:%M:%S");

		// Get user info
		$query 				= $this->db->query("SELECT referreid, points FROM #__alpha_userpoints WHERE userid = '". $userId ."'");
        $referreId 			= $query->row['referreid'];
        $currentPoints 		= $query->row['points'];

		// Update user points
		$newPoints  		= $currentPoints + $points;
		$update  			= $this->db->query("UPDATE #__alpha_userpoints SET  points = '" . $newPoints ."' , last_update '" . $now ."'  WHERE userid = '" . $user_id ."'");

		// Add details
		$insert 			= $this->db->query("INSERT INTO #__alpha_userpoints_details (`referreid`, `points`, `insert_date`, `status`, `rule`, `approved`, `datareference`) VALUES('".$referreId."','".$points."','".$now."', '1', '1', '1', '".$description."' )");

		if(($update) && ($insert)){
			return true;
		}
		return false;
	}	
}
?>