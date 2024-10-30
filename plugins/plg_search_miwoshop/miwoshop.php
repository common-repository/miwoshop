<?php
/**
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permision
defined('MIWI') or die ('Restricted access');

class plgSearchMiwoshop extends MPlugin {

	public function onContentSearchAreas(){
		return $this->onSearchAreas();
	}

	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null, $context = null) {
		return $this->onSearch($text, $phrase, $ordering, $areas, $context);
	}
	
	public function onSearchAreas() {
		MFactory::getLanguage()->load('com_miwoshop', MPATH_ADMINISTRATOR);
		
		static $areas = array('miwoshop' => 'COM_MIWOSHOP_PRODUCTS');
		
		return $areas;
	}
	
	public function onSearch($text, $phrase = '', $ordering = '', $areas = null, $context = null) {
        if($context != 'miwoshop') {
            return array();
        }

		$file = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
		
		if (!file_exists($file)) {
			return array();
		}
		
		require_once($file);
	
		$plugin = MPluginHelper::getPlugin('search', 'miwoshop');

		$params = new MRegistry($plugin->params);

		$text = MString::trim($text);
		if ($text == '') {
			return array();
		}

        $text = MString::strtolower($text);

		$db = MiwoShop::get('db')->getDbo();
		
		$limit = $params->get('search_limit', 50);

        switch ($ordering) {
            case 'oldest':
                $order_by = 'p.date_added ASC';
                break;
            case 'popular':
                $order_by = 'p.viewed DESC';
                break;
            case 'alpha':
                $order_by = 'pd.name ASC';
                break;
            case 'category':
                $order_by = 'cd.name ASC, pd.name ASC';
                break;
            case 'newest':
            default :
                $order_by = 'p.date_added DESC';
                break;
        }
		
		$store_id = MRequest::getInt('miwoshop_store_id', null);
		if (is_null($store_id)) {
			$store_id = (int) MiwoShop::get('opencart')->get('config')->get('config_store_id');
		}

        $language_id = (int) MiwoShop::get('opencart')->get('config')->get('config_language_id');

	    $query = "SELECT DISTINCT p.product_id AS ID, pd.name AS post_title, pd.description AS post_content, cd.name AS section, p.image, pt.tag, p.date_added AS post_date "
				."FROM #__miwoshop_product AS p "
				."INNER JOIN #__miwoshop_product_description AS pd ON p.product_id = pd.product_id "
				."LEFT JOIN #__miwoshop_product_to_store AS ps ON p.product_id = ps.product_id "
				."LEFT JOIN #__miwoshop_product_to_category AS pc ON p.product_id = pc.product_id "
				."LEFT JOIN #__miwoshop_category_description AS cd ON (pc.category_id = cd.category_id AND cd.language_id = {$language_id}) "
				."LEFT JOIN #__miwoshop_category_to_store AS cs ON (pc.category_id = cs.category_id AND cs.store_id = {$store_id}) "
				."LEFT JOIN #__miwoshop_product_tag AS pt ON p.product_id = pt.product_id "
				."WHERE (LOWER(pd.name) LIKE '%" . $db->getEscaped($text) . "%' OR
				        LOWER(pd.description) LIKE '%" . $db->getEscaped($text). "%' OR
				        LOWER(pt.tag) LIKE '%" . $db->getEscaped($text). "%') "
				."AND p.status = '1' "
				."AND p.date_available <= NOW() "
				."AND ps.store_id = {$store_id} "
                ."AND pd.language_id = '" . $language_id . "' "
				."GROUP BY p.product_id "
				."ORDER BY {$order_by} "
				."LIMIT ".$limit;
	   
	    $db->setQuery($query);
	    $results = $db->loadObjectList();

        $ret = array();

        if (empty($results)) {
            return $ret;
        }

        foreach($results as $key => $result) {
            $results[$key]->href = MiwoShop::get('router')->route('index.php?route=product/product&product_id=' . $result->ID);
            $results[$key]->post_title = html_entity_decode($result->post_title);
            $results[$key]->post_content = html_entity_decode($result->post_content);
        }
		
		return $results;
	}
}