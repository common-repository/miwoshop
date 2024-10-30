<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

class ControllerModuleMiwiCategoryhome extends Controller {

	public function index($setting) {
		$this->load->language('module/miwi_categoryhome');

    	$data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('catalog/category');

		$this->load->model('tool/image');

		$data['categories'] = $this->getCategories($setting);

		if (file_exists(MPATH_WP_CNT.'/themes/'.MiwoShop::getTmpl().'/html/com_miwoshop/module/miwi_categoryhome.tpl')) {
            $this->response->setOutput($this->load->view('/templates/'.MiwoShop::getTmpl().'/html/com_miwoshop/module/miwi_categoryhome.tpl'));
        }
        else if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/miwi_categoryhome.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/miwi_categoryhome.tpl', $data);
		} else {
			return $this->load->view('default/template/module/miwi_categoryhome.tpl', $data);
		}
  	}

	protected function getCategories($setting) {
		$categories = array();
		
		$results = $this->model_catalog_category->getCategories($setting['category_id']);

        if (empty($results)) {
            return $categories;
        }
		
		$i = 0;
		foreach ($results as $result) {
            $categories[$i]['href'] = $this->url->link('product/category', 'path=' . $result['category_id']);

			if ($result['image']) {
                $image = $result['image'];
            } else {
                $image = 'placeholder.png';
            }

            $categories[$i]['thumb'] = $this->model_tool_image->resize($image, $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));

			$cname = strlen($result['name']);
			
			$name = $result['name'];
			$cname_text = '';
			if (isset($setting['category_name_limit'])) {
				$name = utf8_substr($result['name'], 0, $setting['category_name_limit']);
				$cname_text = ($cname > $setting['category_name_limit']) ? '..' : '' ;
			}

            $categories[$i]['name'] = $name . $cname_text;
			
            $i++;
		}
		
		return $categories;
	}		
}
?>