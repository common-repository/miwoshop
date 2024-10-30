<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

//No Permision
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

mimport('framework.form.formfield');

class MFormFieldMiwoshopCategory extends MFormField {

    protected $type = 'MiwoshopCategory';

    protected function getInput() {
        $db 		= MFactory::getDbo();
        $lang_id 	= Miwoshop::get('opencart')->get('config')->get('config_language_id', 1);

        $query = "SELECT cp.category_id AS id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id
                  FROM #__miwoshop_category_path cp
                    LEFT JOIN #__miwoshop_category c ON (cp.category_id = c.category_id)
                    LEFT JOIN #__miwoshop_category_description cd1 ON (cp.path_id = cd1.category_id)
                    LEFT JOIN #__miwoshop_category_description cd2 ON (cp.category_id = cd2.category_id)
                  WHERE c.status = '1' AND cd1.language_id=". $lang_id ." AND cd2.language_id=". $lang_id ."
                  GROUP BY cp.category_id ORDER BY name";
        
		$db->setQuery($query);
        $rows = $db->loadObjectList();

        if (empty($rows)) {
            return 'No categories created.';
        }
		
		/*
        // Collect childrens
        $children = array();
        foreach ($rows as $row) {
            // Not subcategories
            if (empty($row->parent)) {
                $row->parent = 0;
            }

            $pt = $row->parent;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $row);
            $children[$pt] = $list;
        }

        // Not subcategories
        if (empty($rows[0]->parent)) {
            $rows[0]->parent = 0;
        }

        // Build Tree
        $tree = MiwoShop::get('base')->buildIndentTree(intval($rows[0]->parent), '', array(), $children);
		*/
		
        foreach ($rows as $item){
            $options[] = array('path' => $item->id, 'name' => $item->name);
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'path', 'name', $this->value, $this->name);
    }
}
