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

class MFormFieldMiwoshopManufacturer extends MFormField {

    protected $type = 'MiwoshopManufacturer';

    protected function getInput() {
        $db = MFactory::getDbo();
        $query = "SELECT DISTINCT manufacturer_id, name FROM #__miwoshop_manufacturer ORDER BY name";
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if (empty($rows)) {
            return 'No manufacturers created.';
        }

        foreach ($rows as $row){
            $options[] = array('manufacturer_id' => $row->manufacturer_id, 'name' => $row->name);
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'manufacturer_id', 'name', $this->value, $this->name);
    }
}
