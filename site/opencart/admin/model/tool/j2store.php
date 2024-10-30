<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

class ModelToolJ2store extends Model {
  	
  	public function importCategories($post){
		$db = MFactory::getDBO();
		$jstatus = MiwoShop::get('base')->is30();

        $q = "SELECT name, language_id FROM #__miwoshop_language ORDER BY language_id";
        $db->setQuery($q);
        $shoplangs = $db->loadAssocList();
	 
		$cat = "SELECT id, title, parent_id, description, created_time, modified_time, params, published, metadesc, metakey FROM #__categories WHERE extension = 'com_content' ORDER BY id";
		$db->setQuery($cat);
		$cats = $db->loadAssocList();
		
		if (empty($cats)) {
			echo '<strong>No category to import.</strong>';
			exit;
		}
		
		$i = 0;
		foreach($cats as $cat) {
            $params = @json_decode($cat['params'], true);
            if (!empty($params['image'])) {
                $params['image'] = str_replace('\/', '/', $params['image']);
                $image_name = explode('/', $params['image']);
                $last = array_pop($image_name);

                $imagges = '';
                foreach($image_name AS $imagge){
                    $imagges .= $imagge.'/';
                }
                if(!empty($imagges)){
                    $this->_copyFiles(MPATH_SITE.'/'.$imagges, $last);
                }
            }
            if($cat['parent_id'] == 1){
                $cat['parent_id'] = 0;
            }

			$datec = ($jstatus) ? MFactory::getDate($cat['created_time'])->toSql() : MFactory::getDate($cat['created_time'])->toMySQL();
			$datem = ($jstatus) ? MFactory::getDate($cat['modified_time'])->toSql() : MFactory::getDate($cat['modified_time'])->toMySQL();
			$cat_name = ($jstatus) ? $db->escape(htmlspecialchars($cat['title'])) : $db->getEscaped(htmlspecialchars($cat['title']));
			$cat_desc = ($jstatus) ? $db->escape(htmlspecialchars($cat['description'])) : $db->getEscaped(htmlspecialchars($cat['description']));
			$meta_desc = ($jstatus) ? $db->escape(htmlspecialchars($cat['metadesc'])) : $db->getEscaped(htmlspecialchars($cat['metadesc']));
			$meta_key = ($jstatus) ? $db->escape(htmlspecialchars($cat['metakey'])) : $db->getEscaped(htmlspecialchars($cat['metakey']));

			$cat_image = empty($last) ? '' : (($jstatus) ? 'catalog/'.$db->escape($last) : 'catalog/'.$db->getEscaped($last));
			
			$q = "INSERT IGNORE INTO `#__miwoshop_category` ( `category_id` , `image` , `parent_id` , `sort_order` , `date_added` , `date_modified` , `status`) VALUES ('". $cat['id']."', '".$cat_image."', '".$cat['parent_id']."', '0', '".$datec."', '".$datem."', '".$cat['published']."')";
			$db->setQuery($q);
			$db->query();
			
            foreach($shoplangs AS $shoplang){
				$q = "INSERT IGNORE INTO `#__miwoshop_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_description`, `meta_keyword`) VALUES ('". $cat['id']."', '".$shoplang['language_id']."', '".$cat_name."', '".$cat_desc."', '".$meta_desc."', '".$meta_key."')";
				$db->setQuery($q);
				$db->query();
			}
			
			$q = "INSERT IGNORE INTO `#__miwoshop_category_to_store` (`category_id` , `store_id`) VALUES ('".$cat['id']."' , '0')";
			$db->setQuery($q);
			$db->query();

			echo 'Importing <i>' . $cat['title'] .'</i> : Completed.<br />';
			$i++;
		}
		
		self::_addCategoryPath();
		
		echo '<strong>Categories has been imported successfully.</strong><br />';
		exit;
	}
  	
  	public function _addCategoryPath() {
        $db = MFactory::getDBO();
		$db->setQuery("SELECT id FROM `#__categories` WHERE extension = 'com_content'");
		$categories = $db->loadObjectList();

		if (!empty($categories)){
			foreach($categories as $category){
				$path = self::_getPath($category->id, array($category->id));
				$path = array_reverse($path);
				
				foreach($path as $key => $_path){
                    if($_path == 1){
                        $_path = 0;
                    }
					$db->setQuery("INSERT IGNORE INTO `#__miwoshop_category_path` (`category_id`, `path_id`, `level`) VALUES('{$category->id}','{$_path}','{$key}')");
					$db->query();
				}
			}
		}
	}

    private function _getPath($cat_id, $path = array()){
        $db = MFactory::getDBO();
		
        $db->setQuery("SELECT parent_id FROM `#__categories` WHERE extension = 'com_content' AND id = ".$cat_id);
        $parent_id = $db->loadResult();

        if ((int)$parent_id != 1) {
            $path[] = $parent_id;
            $path = self::_getPath($parent_id, $path);
        }

        return $path;
    }
  	
  	public function importProducts($post) {
		$db = MFactory::getDBO();
		$jstatus = MiwoShop::get('base')->is30();

        $q = "SELECT name, language_id FROM #__miwoshop_language ORDER BY language_id";
        $db->setQuery($q);
        $shoplangs = $db->loadAssocList();

		$q = "SELECT c.id, c.title, c.alias, c.introtext, c.fulltext, c.metadesc, c.metakey, c.catid, c.state, c.hits, c.attribs, c.publish_up, c.modified, c.created,".
            " p.main_image, p.item_price, p.item_sku, p.additional_image, p.product_enabled, p.item_length, p.item_width, p.item_height, p.item_length_class_id, p.item_weight, p.item_weight_class_id".
            " FROM #__content AS c, #__j2store_prices AS p WHERE c.id = p.article_id";
		$db->setQuery($q);
		$pros = $db->loadAssocList();
		
		if (empty($pros)) {
			echo '<strong>No product to import.</strong>';
			exit;
		}
		
		foreach($pros AS $pro){
            $image_name = explode('/', $pro['main_image']);
            $last = array_pop($image_name);

            $imagges = '';
            foreach($image_name AS $imagge){
                $imagges .= $imagge.'/';
            }
            if(!empty($imagges)){
                $this->_copyFiles(MPATH_SITE.'/'.$imagges, $last);
            }

            $pro_image = empty($last) ? '' : (($jstatus) ? 'catalog/'.$db->escape($last) : 'catalog/'.$db->getEscaped($last));

            $additional_image = @json_decode($pro['additional_image'], true);
            for($i =0; $i < count($additional_image); $i++){
                if(isset($additional_image[$i]) && $additional_image[$i]){
                    $add_image = str_replace('\/', '/', $additional_image[$i]);
                    $image_name = explode('/', $add_image);
                    $extra_image = array_pop($image_name);

                    $imagges = '';
                    foreach($image_name AS $imagge){
                        $imagges .= $imagge.'/';
                    }
                    if(!empty($imagges)){
                        $this->_copyFiles(MPATH_SITE.'/'.$imagges, $extra_image);
                    }

                    $media_id = $i+1;
                    $pro_extra_image = (($jstatus) ? 'catalog/'.$db->escape($extra_image) : 'catalog/'.$db->getEscaped($extra_image));
                    $q = "INSERT IGNORE INTO `#__miwoshop_product_image` (`product_image_id` , `product_id` , `image` , `sort_order`) VALUES ('".$media_id."' , '".$pro['id']."' , '".$pro_extra_image."' , '".$i."')";
                    $db->setQuery($q);
                    $db->query();
                }
            }

            $pro_length_class_id = $pro['item_length_class_id'];
            $pro_weight_class_id = $pro['item_weight_class_id'];

            if(!empty($pro_length_class_id)){
                switch($pro_length_class_id){
                    case '2':
                        $pro_length_class_id = '3';
                        break;
                    case '3':
                        $pro_length_class_id = '2';
                        break;
                    default:
                        $pro_length_class_id = '1';
                        break;
                }
            }

            if(!empty($pro_weight_class_id)){
                switch($pro_weight_class_id){
                    case '1':
                        $pro_weight_class_id = '1';
                        break;
                    case '2':
                        $pro_weight_class_id = '2';
                        break;
                    case '3':
                        $pro_weight_class_id = '6';
                        break;
                    case '4':
                        $pro_weight_class_id = '5';
                        break;
                    default:
                        $pro_weight_class_id = '1';
                        break;
                }
            }

			$datec = ($jstatus) ? MFactory::getDate($pro['created'])->toSql() : MFactory::getDate($pro['created'])->toMySQL();
			$datem = ($jstatus) ? MFactory::getDate($pro['modified'])->toSql() : MFactory::getDate($pro['modified'])->toMySQL();
			$datea = ($jstatus) ? MFactory::getDate($pro['publish_up'])->toSql() : MFactory::getDate($pro['publish_up'])->toMySQL();

			$pro_name = ($jstatus) ? $db->escape(htmlspecialchars($pro['title'])) : $db->getEscaped(htmlspecialchars($pro['title']));
			$pro_desc = ($jstatus) ? $db->escape(htmlspecialchars($pro['introtext'].'<br/>'.$pro['fulltext'])) : $db->getEscaped(htmlspecialchars($pro['introtext'].'<br/>'.$pro['fulltext']));
            $meta_desc = ($jstatus) ? $db->escape(htmlspecialchars($pro['metadesc'])) : $db->getEscaped(htmlspecialchars($pro['metadesc']));
            $meta_key = ($jstatus) ? $db->escape(htmlspecialchars($pro['metakey'])) : $db->getEscaped(htmlspecialchars($pro['metakey']));

			$q = "INSERT IGNORE INTO `#__miwoshop_product` (`product_id`, `model`, `sku`, `location`, `quantity`, `stock_status_id`, `image`, `shipping`, `price`, `weight`, `weight_class_id`, `length_class_id`, `length`, `width`, `height`, `status`, `date_added`, `date_modified`, `date_available`, `viewed`) VALUES ('".$pro['id']."', '".$pro['alias']."', '".$pro['item_sku']."', '', '9999', '7', '".$pro_image."', '1', '".$pro['item_price']."', '".$pro['item_weight']."', '".$pro_weight_class_id."', '".$pro_length_class_id."', '".$pro['item_length']."', '".$pro['item_width']."', '".$pro['item_height']."', '".$pro['product_enabled']."', '".$datec."', '".$datem."',  '".$datea."', '".$pro['hits']."')";
			$db->setQuery($q);
			$db->query();

            foreach($shoplangs AS $shoplang){
				$q = "INSERT IGNORE INTO `#__miwoshop_product_description` (`product_id` , `language_id` , `name` , `description`, `meta_description`, `meta_keyword`) VALUES ('".$pro['id']."','".$shoplang['language_id']."','".$pro_name."','".$pro_desc."','".$meta_desc."','".$meta_key."')";
				$db->setQuery($q);
				$db->query();
			}

			$q = "INSERT IGNORE INTO `#__miwoshop_product_to_store` (`product_id`, `store_id`) VALUES ('".$pro['id']."' , '0')";
			$db->setQuery($q);
			$db->query();

            $q = "INSERT IGNORE INTO `#__miwoshop_product_to_category` (`product_id`, `category_id`) VALUES ('".$pro['id']."', '".$pro['catid']."')";
            $db->setQuery($q);
            $db->query();
	    
			echo 'Importing <i>' . $pro['title'] .'</i> : Completed.<br />';
		}

		echo '<strong>Products has been imported successfully.</strong><br />';
		exit;
	}

    public function _copyFiles($dir, $thumb_name) {
        foreach (glob($dir . "*") as $filename) {
            if (MFolder::exists($filename)) {
                continue;
            }

            if($thumb_name == basename($filename)) {
                if (!MFile::copy($filename, DIR_IMAGE . 'catalog/' . basename($filename))) {
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
                else {
                    echo 'Copying <i>' . $filename . '</i> : Completed.<br />';
                }
            }
        }
    }
}