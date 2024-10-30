<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

mimport('framework.form.formfield');

class MFormFieldMiwoshopStore extends MFormField {

    protected $type = 'MiwoshopStore';

    protected function getInput() {
        $db = MFactory::getDbo();
        $query = "SELECT DISTINCT store_id, name FROM #__miwoshop_store ORDER BY name";
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $options[] = array('miwoshop_store_id' => 0, 'name' => 'Default');

        if (!empty($rows)) {
            foreach ($rows as $row){
                $options[] = array('miwoshop_store_id' => $row->store_id, 'name' => $row->name);
            }
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'miwoshop_store_id', 'name', $this->value, $this->name);
    }
}
