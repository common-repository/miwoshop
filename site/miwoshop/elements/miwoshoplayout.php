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

class MFormFieldMiwoshopLayout extends MFormField {

    protected $type = 'MiwoshopLayout';

    protected function getInput() {
        $db = MFactory::getDbo();
        $db->setQuery("SELECT layout_id, name FROM #__miwoshop_layout");
        $rows = $db->loadObjectList();

        if (empty($rows)) {
            return 'No layouts created.';
        }

        $options = array();
        foreach ($rows as $row){
            $options[] = array('value' => $row->layout_id, 'text' => $row->name);
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox" style="width:150px !important;"', 'value', 'text', $this->value, $this->name);
    }
}
