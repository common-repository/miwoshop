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

class MFormFieldMiwoshopInformation extends MFormField {

    protected $type = 'MiwoshopInformation';

    protected function getInput() {
        $db = MFactory::getDbo();

        $query = "SELECT DISTINCT id.information_id AS id, id.title AS name "
                ."FROM #__miwoshop_information AS i, #__miwoshop_information_description AS id "
                ."WHERE i.status = '1' "
                //."AND id.language_id = '1' "
                ."ORDER BY i.sort_order, id.title";

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if (empty($rows)) {
            return 'No information pages created.';
        }

        foreach ($rows as $row){
            $options[] = array('id' => $row->id, 'name' => $row->name);
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'id', 'name', $this->value, $this->name);
    }
}
