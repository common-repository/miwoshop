<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

class ModelToolWoocommerce extends Model {
  	
  	public function importCategories($post){
        //static $lang_cache = array();
		$db = MFactory::getDBO();
		//$oc_lang_id = (int)$this->config->get('config_language_id');
		$jstatus = MiwoShop::get('base')->is30();

        $q = "SELECT name, language_id FROM #__miwoshop_language ORDER BY language_id";
        $db->setQuery($q);
        $shoplangs = $db->loadAssocList();
		
        $cat = "SELECT t.term_id AS category_id, t.name AS category_name, tt.parent, tt.description AS category_desc FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'product_cat' ORDER BY t.term_id";
		$db->setQuery($cat);
		$cats = $db->loadAssocList();
		
		if (empty($cats)) {
			echo '<strong>No category to import.</strong>';
			exit;
		}

		$i = 0;
		foreach($cats as $cat) {
			$cat_image = '';
			
			$q = "SELECT meta_value FROM `#__woocommerce_termmeta` WHERE meta_key = 'thumbnail_id' AND woocommerce_term_id = ".$cat['category_id'];
			$db->setQuery($q);
			$cat_image_id = $db->loadResult();

			if(!empty($cat_image_id)){
				$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID={$cat_image_id}";
				$db->setQuery($q);
				$result_img = $db->loadResult();
				
				if(!empty($result_img)){
					self::copyCatImages($result_img);
					
					$images = explode('/' , $result_img);
					$last1 = array_pop($images);
					
					$cat_image = empty($last1) ? '' : (($jstatus) ? 'data/'.$db->escape($last1) : 'data/'.$db->getEscaped($last1));
				}
			}
			
			$cat_name = ($jstatus) ? $db->escape(htmlspecialchars($cat['category_name'])) : $db->getEscaped(htmlspecialchars($cat['category_name']));
			$cat_desc = ($jstatus) ? $db->escape(htmlspecialchars($cat['category_desc'])) : $db->getEscaped(htmlspecialchars($cat['category_desc']));
			
			$q = "INSERT IGNORE INTO `#__miwoshop_category` ( `category_id` , `image` , `parent_id` , `sort_order` , `status`) VALUES ('". $cat['category_id']."', '".$cat_image."', '".$cat['parent']."', '0', '1')";
			$db->setQuery($q);
			$db->query();

            foreach($shoplangs AS $shoplang){
                $q = "INSERT IGNORE INTO `#__miwoshop_category_description` (`category_id`, `language_id`, `name`, `description`) VALUES ('". $cat['category_id']."', '".$shoplang['language_id']."', '".$cat_name."', '".$cat_desc."')";
                $db->setQuery($q);
                $db->query();
            }

			$q = "INSERT IGNORE INTO `#__miwoshop_category_to_store` (`category_id` , `store_id`) VALUES ('".$cat['category_id']."' , '0')";
			$db->setQuery($q);
			$db->query();

			echo 'Importing <i>' . $cat['category_name'] .'</i> : Completed.<br />';
			$i++;
		}
		
		self::_addCategoryPath();
		
		echo '<strong>Categories has been imported successfully.</strong><br />';
		exit;
	}

  	public function _addCategoryPath() {
        $db = MFactory::getDBO();
		$db->setQuery("SELECT t.term_id AS id FROM `#__terms` AS t INNER JOIN `#__term_taxonomy` AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'product_cat' ORDER BY t.term_id");
		$categories = $db->loadObjectList();

		if (!empty($categories)){
			foreach($categories as $category){
				$path = self::_getPath($category->id, array($category->id));
				$path = array_reverse($path);
				
				foreach($path as $key => $_path){
                    $db->setQuery("INSERT IGNORE INTO `#__miwoshop_category_path` (`category_id`, `path_id`, `level`) VALUES ('{$category->id}','{$_path}','{$key}')");
					$db->query();
				}
			}
		}
	}

    private function _getPath($cat_id, $path = array()){
        $db = MFactory::getDBO();
        $db->setQuery("SELECT tt.parent FROM `#__terms` AS t INNER JOIN `#__term_taxonomy` AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'product_cat' AND t.term_id = ".$cat_id);
        $parent_id = $db->loadResult();

        if ((int)$parent_id != 0) {
            $path[] = $parent_id;
            $path = self::_getPath($parent_id, $path);
        }

        return $path;
    }
  	
  	public function importProducts($post) {
        //static $lang_cache = array();
		$db = MFactory::getDBO();
		//$oc_lang_id = (int)$this->config->get('config_language_id');
		$jstatus = MiwoShop::get('base')->is30();

        $q = "SELECT name, language_id FROM #__miwoshop_language ORDER BY language_id";
        $db->setQuery($q);
        $shoplangs = $db->loadAssocList();
		
		$q = "SELECT ID AS product_id, post_title, post_content, post_name AS product_sku, post_date, post_modified, post_status AS published, menu_order FROM #__posts WHERE post_type = 'product' AND post_parent = '0' AND post_status != 'trash' ORDER BY ID";
		$db->setQuery($q);
		$pros = $db->loadAssocList();
		
		if (empty($pros)) {
			echo '<strong>No product to import.</strong>';
			exit;
		}
		
		foreach($pros as $pro){
			$pro_image = $pro_sale_price = $pro_reg_price = $weight = $length = $width = $height = $pro_sku = $pro_desc = '';
			
			$q = "SELECT meta_key, meta_value FROM `#__postmeta` WHERE post_id = ".$pro['product_id'];
			$db->setQuery($q);
			$metas = $db->loadAssocList();
			
			foreach($metas as $meta){
				switch($meta['meta_key']){
					case "_weight":
						$weight = $meta['meta_value'];
						break;
					case "_length":
						$length = $meta['meta_value'];
						break;
					case "_width":
						$width = $meta['meta_value'];
						break;
					case "_height":
						$height = $meta['meta_value'];
						break;
					case "_sku":
						$pro_sku = $meta['meta_value'];
						break;
					case "_stock":
						$stock_quantity = $meta['meta_value'];
						break;
					case "_stock_status":
						$stock_status = $meta['meta_value'];
						break;
					case "_sale_price":
						$pro_sale_price = $meta['meta_value'];
						break;
					case "_price":
					case "_regular_price":
						$pro_reg_price = $meta['meta_value'];
						break;
					case "_desc":
						$pro_desc = ($jstatus) ? $db->escape(htmlspecialchars($meta['meta_value'])) : $db->getEscaped(htmlspecialchars($meta['meta_value']));
						break;
					case "_product_image_gallery":
						$gallery_ids = $meta['meta_value'];
						break;
					case "_thumbnail_id":
						$pro_image_id = $meta['meta_value'];
						//self::copyProImagesId($meta['meta_value']);
						break;
				}
			}
			
			if(!empty($stock_status)){
				if($stock_status == 'instock'){
					$prod_stock = '7';
				}
				elseif( $stock_status== 'outofstock'){
					$prod_stock = '5';
				}
			}
			else {
				$prod_stock = '7';
			}
			
			if(!empty($pro_sale_price)){
                $pro_reg_price = $pro_sale_price;
			}

			if(empty($stock_quantity)){
				$stock_quantity = '999999';
			}
			
			if(!empty($pro['published'])){
				if($pro['published'] == 'publish'){
					$pro_publish = '1';
				}
				if($pro['published'] == 'pending'){
					$pro_publish = '0';
				}
			}
			else {
				$pro_publish = '1';
			}
			
			if(!empty($pro_image_id)){
				$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$pro_image_id;
				$db->setQuery($q);
				$result_img = $db->loadResult();
				
				if(!empty($result_img)){
                    self::copyProImages($result_img);

					$images = explode('/' , $result_img);
					$last1 = array_pop($images);
					
					$pro_image = empty($last1) ? '' : (($jstatus) ? 'data/'.$db->escape($last1) : 'data/'.$db->getEscaped($last1));
				}
			}
		
			if(!empty($gallery_ids)){
				$gallery_idss = explode(",", $gallery_ids);
				$i = 0;
				
				foreach($gallery_idss as $imgres) {
					$q = "SELECT ID, guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$imgres;
					$db->setQuery($q);
					$result_img = $db->loadObject();
                    self::copyProImages($result_img->guid);
					
					$gal_img = explode('/' , $result_img->guid);
					$last1 = array_pop($gal_img);
				
					$ptjc = "INSERT IGNORE INTO `#__miwoshop_product_image` (`product_image_id`, `product_id`, `image`, `sort_order`) VALUES ('".$result_img->ID."', '".$pro['product_id']."', 'data/".$last1."', '".$i."')";
					$db->setQuery($ptjc);
					$db->query();
					
					$i++;
				}
			}
			
			$datec = ($jstatus) ? MFactory::getDate($pro['post_date'])->toSql() : MFactory::getDate($pro['post_date'])->toMySQL();
			$datem = ($jstatus) ? MFactory::getDate($pro['post_modified'])->toSql() : MFactory::getDate($pro['post_modified'])->toMySQL();
			
			$pro_name = ($jstatus) ? $db->escape(htmlspecialchars($pro['post_title'])) : $db->getEscaped(htmlspecialchars($pro['post_title']));
			$pro_desc = ($jstatus) ? $db->escape(htmlspecialchars($pro['post_content'])) : $db->getEscaped(htmlspecialchars($pro['post_content']));
			
			$q = "INSERT IGNORE INTO `#__miwoshop_product` (`product_id`, `model`, `sku`, `location`, `quantity`, `stock_status_id`, `image`, `shipping`, `price`, `tax_class_id`, `status`,".
				" `weight`, `weight_class_id`, `length`, `length_class_id`, `width`, `height`, `date_added`, `date_modified`, `date_available`, `sort_order`)".
				" VALUES ('".$pro['product_id']."', '".$pro_sku."', '".$pro_sku."', '', '".$stock_quantity."', '".$prod_stock."', '".$pro_image."', '1', '".$pro_reg_price."', '', '".$pro_publish.
				"', '".$weight."', '1', '".$length."', '1', '".$width."', '".$height."', '".$datec."', '".$datem."',  '".$datec."', '".$pro['menu_order']."')";
			$db->setQuery($q);
			$db->query();

            foreach($shoplangs AS $shoplang){
                $q = "INSERT IGNORE INTO `#__miwoshop_product_description` (`product_id` , `language_id` , `name` , `description`) VALUES ('".$pro['product_id']."' , '".$shoplang['language_id']."' , '".$pro_name."' , '".$pro_desc."')";
                $db->setQuery($q);
                $db->query();
            }
			
			$q = "INSERT IGNORE INTO `#__miwoshop_product_to_store` (`product_id`, `store_id`) VALUES ('".$pro['product_id']."' , '0')";
			$db->setQuery($q);
			$db->query();
	    
			echo 'Importing <i>' . $pro['post_title'] .'</i> : Completed.<br />';
		}
	  
		$q = "SELECT r.object_id, r.term_taxonomy_id FROM `#__posts` AS p INNER JOIN `#__term_relationships` AS r ON p.ID = r.object_id WHERE p.post_status != 'trash' AND p.post_type = 'product'";
		$db->setQuery($q);
		$results = $db->loadAssocList();
		
		if (!empty($results)) {
			foreach($results as $ptcs) {
				$ptjc = "INSERT IGNORE INTO `#__miwoshop_product_to_category` (`product_id`, `category_id`) VALUES ('".$ptcs['object_id']."', '".$ptcs['term_taxonomy_id']."')";
				$db->setQuery($ptjc);
				$db->query();
			}
		}
		
		echo '<strong>Products has been imported successfully.</strong><br />';
		exit;
	}

    public function copyCatImages($results) {
        /*$db = MFactory::getDBO();
        $q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$id;
        $db->setQuery($q);
        $results = $db->loadResult();*/

        $images = explode('/' , $results);
        $last1 = array_pop($images);
        $last2 = array_pop($images);
        $last3 = array_pop($images);

        $cat_images = MPATH_MEDIA.'/'.$last3.'/'.$last2.'/';

        self::_copyImages($cat_images, $last1);
        return;
    }
	
	public function copyProImages($results) {
        /*$db = MFactory::getDBO();
		$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$id;
		$db->setQuery($q);
		$results = $db->loadResult();*/
		
		$images = explode('/' , $results);
		$last1 = array_pop($images);
		$last2 = array_pop($images);
		$last3 = array_pop($images);
	
		$pro_images = MPATH_MEDIA.'/'.$last3.'/'.$last2.'/';
		
		self::_copyImages($pro_images, $last1);
        return;
	}

	public function copyProImagesId($id) {
        $db = MFactory::getDBO();
		$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$id;
		$db->setQuery($q);
		$results = $db->loadResult();

		$images = explode('/' , $results);
		$last1 = array_pop($images);
		$last2 = array_pop($images);
		$last3 = array_pop($images);

		$pro_images = MPATH_MEDIA.'/'.$last3.'/'.$last2.'/';

		self::_copyImages($pro_images, $last1);
        return;
	}
	
	public function _copyImages($dir, $image_name) {
		foreach (glob($dir . "*") as $filename) {
			if (MFolder::exists($filename)) {
				continue;
			}

            if (file_exists(DIR_IMAGE.'data/'.$image_name)) {
                continue;
            }

            if($image_name == basename($filename)){
                if (!MFile::copy($filename, DIR_IMAGE . 'data/' . basename($filename))){
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
                else {
                    echo 'Copying <i>' . $filename . '</i> : Completed.<br />';
                }
            }
		}
	}
}