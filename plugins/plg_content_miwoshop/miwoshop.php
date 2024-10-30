<?php 
/**
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permision
defined('MIWI') or die ('Restricted access');

mimport('framework.plugin.plugin');
mimport('framework.plugin.helper');

class plgContentMiwoshop extends MPlugin {

    public $p_params = null;

	public function onContentPrepare($context, &$row, &$params, $limitstart) {
		return $this->onPrepareContent($row, $params, $limitstart);
	}
	
	public function onPrepareContent(&$row, &$params, $limitstart) {
		if (MFactory::getApplication()->isAdmin()) {
			return true;
		}
		
		$file = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
		if (!file_exists($file)) {
			return true;
		}
		
		require_once($file);
	
		$plugin = MPluginHelper::getPlugin('content', 'miwoshop');

        $this->p_params = new MRegistry($plugin->params);

		$regex = '/{miwoshop\s*.*?}/i';
		
		// find all instances of plugin and put in $matches
		preg_match_all($regex, $row->text, $matches);

		// Number of plugins
		$count = count($matches[0]);

		// plugin only processes if there are any instances of the plugin in the text
		if ($count) {
			self::_processMatches($row, $matches, $count, $regex);
		}
		
		return true;
	}
	
	public function _processMatches(&$row, &$matches, $count, $regex) {
        $row->text = '<div id="p_notification"></div>'.$row->text;

        $image = $this->p_params->get('show_image', 1);
        $name = $this->p_params->get('show_name', 1);
        $price = $this->p_params->get('show_price', 1);
        $rating = $this->p_params->get('show_rating', 1);
        $button = $this->p_params->get('show_button', 1);
        $options = '';

		for ($i = 0; $i < $count; $i++) {
			$attribs = str_replace('miwoshop', '', $matches[0][$i]);
            $attribs = str_replace('{', '', $attribs);
            $attribs = str_replace('}', '', $attribs);
            $attribs = explode(',', trim($attribs));

            foreach ($attribs as $attrib) {
                $array = explode('=', $attrib);

                ${$array[0]} = $array[1];
            }

            if (empty($id)) {
                continue;
            }

			$product = MiwoShop::get('db')->getRecord($id);
            if (empty($product)) {
                continue;
            }

			$content = self::_renderProduct($product, $image, $name, $price, $rating, $button, $options);
			
			$row->text = str_replace($matches[0][$i], $content, $row->text);
		}

		// removes tags without matching module positions
		$row->text = preg_replace($regex, '', $row->text);
	}
	
	public function _renderProduct($row, $show_image, $show_name, $show_price, $show_rating, $show_button, $opts) {
        $oc_config = MiwoShop::get('opencart')->get('config');
        $oc_registry = MiwoShop::get('opencart')->get('registry');
        $oc_customer = MiwoShop::get('opencart')->get('customer');
        $oc_tax = MiwoShop::get('opencart')->get('tax');
        $oc_currency = MiwoShop::get('opencart')->get('currency');
        $oc_language = MiwoShop::get('opencart')->get('language');

        require_once(modification(MPATH_MIWOSHOP_OC.'/catalog/model/tool/image.php'));
        $oc_img_tool = new ModelToolImage($oc_registry);

        MHtml::_('jquery.framework');
        MiwoShop::get('base')->addHeader(MPATH_MIWOSHOP_SITE.'/assets/js/product.js', false);

        if (strpos($show_image, ':')) {
            $img_array = explode(':', $show_image);

            $show_image = $img_array[0];
            $image_width = $img_array[1];
            $image_height = $img_array[2];
        }

        if ($show_image && $row['image']) {
            $image_width = isset($image_width) ? $image_width : $this->p_params->get('image_width', 80);
            $image_height = isset($image_height) ? $image_height : $this->p_params->get('image_height', 80);

            $image = $oc_img_tool->resize($row['image'], $image_width, $image_height);
        }
        else {
            $image = false;
        }

        if ($show_name) {
            $name = $row['name'];
        }
        else {
            $name = false;
        }

        if ($show_price && (($oc_config->get('config_customer_price') && $oc_customer->isLogged()) || !$oc_config->get('config_customer_price'))) {
            $price = $oc_currency->format($oc_tax->calculate($row['price'], $row['tax_class_id'], $oc_config->get('config_tax')));
        }
        else {
            $price = false;
        }

        if ($show_price && (float)$row['special']) {
            $special = $oc_currency->format($oc_tax->calculate($row['special'], $row['tax_class_id'], $oc_config->get('config_tax')));
        }
        else {
            $special = false;
        }

        if ($show_rating && $oc_config->get('config_review_status')) {
            $rating = $row['rating'];
        }
        else {
            $rating = false;
        }

        if ($show_button) {
            $button = true;
            $button_cart = $oc_language->get('button_cart');
        }
        else {
            $button = false;
        }

        $oc_language->load('module/product');

        ############## options ###############
        $text_select = $oc_language->get('text_select');
        $options = array();
        $product_info = MiwoShop::get('opencart')->loadModelFunction('catalog/product/getProduct', $row['product_id']);
        $_options = MiwoShop::get('opencart')->loadModelFunction('catalog/product/getProductOptions', $row['product_id']);
        $opts = $this->getOptionVarArray($opts);

        foreach ($_options as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();

                if (isset($option['option_value'])) {
                    $option_values = $option['option_value'];
                } elseif (isset($option['product_option_value'])) {
                    $option_values = $option['product_option_value'];
                } else {
                    $option_values = array();
                }
                
                foreach ($option_values as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if ((($oc_config->get('config_customer_price') && $oc_customer->isLogged()) || !$oc_config->get('config_customer_price')) && (float)$option_value['price']) {
                            $_price = $oc_currency->format($oc_tax->calculate($option_value['price'], $product_info['tax_class_id'], $oc_config->get('config_tax') ? 'P' : false));
                        } else {
                            $_price = false;
                        }

                        $option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id'         => $option_value['option_value_id'],
                            'name'                    => $option_value['name'],
                            'image'                   => $oc_img_tool->resize($option_value['image'], 50, 50),
                            'price'                   => $_price,
                            'price_prefix'            => $option_value['price_prefix']
                        );
                    }
                }

                $options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id'         => $option['option_id'],
                    'name'              => $option['name'],
                    'type'              => $option['type'],
                    'option_value'      => $option_value_data,
                    'required'          => $option['required']
                );
            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id'         => $option['option_id'],
                    'name'              => $option['name'],
                    'type'              => $option['type'],
                    'option_value'      => $option['option_value'],
                    'required'          => $option['required']
                );
            }

            $keys = array_keys($options);
            $last_key = end($keys);

            if (isset($opts[$option['product_option_id']])) {
                $options[$last_key]['show'] = $opts[$option['product_option_id']]['show'];
            }
            else{
                $options[$last_key]['show'] = 1;
            }
        }
        #########################################

        $product = array(
            'product_id' => $row['product_id'],
            'thumb'   	 => $image,
            'name'    	 => $name,
            'price'   	 => $price,
            'special' 	 => $special,
            'rating'     => $rating,
            'reviews'    => sprintf($oc_language->get('text_reviews'), (int)$row['reviews']),
            'button'     => $button,
            'options'    => $options,
            'href'    	 => MiwoShop::get('router')->route('index.php?route=product/product&product_id=' . $row['product_id']),
        );

        $show_box = $this->p_params->get('show_box', 0);
        $show_heading = $this->p_params->get('show_heading', 0);
        $heading_title = $oc_language->get('heading_title');

        if (file_exists(DIR_TEMPLATE . $oc_config->get('config_template') . '/template/product/external_product.tpl')) {
            $template = $oc_config->get('config_template') . '/template/product/external_product.tpl';
        }
        else {
            $template = 'default/template/product/external_product.tpl';
        }

        ob_start();

        require(MPATH_MIWOSHOP_OC.'/catalog/view/theme/'.$template);

        $output = ob_get_contents();

        ob_end_clean();

		return $output;
	}

    public function getProductOptionsData($product_id){
        $options = MiwoShop::get('opencart')->loadModelFunction('catalog/product/getProductOptions', $product_id);
        foreach ($options as $option) {
            if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                $option_value_data = array();

                foreach ($option['option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        $option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id'         => $option_value['option_value_id'],
                            'name'                    => $option_value['name'],
                            'image'                   => MiwoShop::get('opencart')->loadModelFunction('tool/image/resize', array($option_value['image'], 50, 50)),
                            'price'                   => $option_value['price'],
                            'price_prefix'            => $option_value['price_prefix']
                        );
                    }
                }

                $product_options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id'         => $option['option_id'],
                    'name'              => $option['name'],
                    'type'              => $option['type'],
                    'option_value'      => $option_value_data,
                    'required'          => $option['required']
                );
            } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                $product_options[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'option_id'         => $option['option_id'],
                    'name'              => $option['name'],
                    'type'              => $option['type'],
                    'option_value'      => $option['option_value'],
                    'required'          => $option['required']
                );
            }
        }

        return $product_options;
    }

    public function getOptionVarArray($opts) {
        $result_opts = array();

        if(empty($opts)){
            return $result_opts;
        }

        $_opts = explode('|', $opts);

        foreach($_opts as $_opt) {
            $_opt = explode(':',$_opt);

            $id = preg_replace("/[^0-9]/","",$_opt[0]);;
            $result_opts[$id]['id'] = $id;
            $result_opts[$id]['show'] = $_opt[1];
        }

        return $result_opts;
    }



}