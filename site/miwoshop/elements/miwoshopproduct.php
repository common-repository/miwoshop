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

class MFormFieldMiwoshopProduct extends MFormField {

    protected $type = 'MiwoshopProduct';

    protected function getInput() {
        MHtml::_('behavior.framework');
        MHtml::_('behavior.modal', 'a.modal');

        $script = array();
        $script[] = '	function jSelectProducts(title, object) {';
        $script[] = '		document.id("'.$this->id.'_id").value = id;';
        $script[] = '		document.id("'.$this->id.'_name").value = title;';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';
        MFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        $html = "";
        $doc 		=& MFactory::getDocument();
        $fieldName	= $control_name ? $control_name.'['.$name.']' : $name;
        $title = MText::_('Select a Product');

        $db = jFactory::getDBO();
        $q = "SELECT pd.product_id AS id, pd.name, pd.description, c.category_id AS cid FROM #__miwoshop_product AS p".
            " LEFT JOIN #__miwoshop_product_description AS pd ON p.product_id = pd.product_id".
            " LEFT JOIN #__miwoshop_product_to_category AS c ON p.product_id = c.product_id"
            ."WHERE p.status = '1'";
        $db->setQuery($q);
        $prods = $db->loadObjectList();

        if ($prods) {
            foreach ($prods AS $prod){
                $title = $prod->name;
            }
        }
        else {
            $title = MText::_('Select a Product');
        }

        $link = 'index.php?option=com_miwoshop&task=products&tmpl=component&object='.$this->name;

        $html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
        $html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.MText::_('Select a Product').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.MText::_('Select').'</a></div></div>'."\n";
        $html .= "\n".'<input type="hidden" id="'.$this->id.'_id" name="'.$this->name.'" value="'.(int)$this->value.'" />';

        return $html;
    }
}
