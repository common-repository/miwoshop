<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

//No Permision
defined('MIWI') or die('Restricted access');

mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

mimport('framework.form.formfield');

class MFormFieldMiwoshopModule extends MFormField {

    protected $type = 'MiwoshopModule';

    protected function getInput() {
        $files = MFolder::files(MPATH_WP_PLG.'/miwoshop/site/opencart/catalog/controller/module', '', false, false, array('index.html', 'cart.php'));

        if (empty($files) || !is_array($files)) {
            return 'No modules created.';
        }

        $options = array();

        foreach ($files as $file) {
            $_title = '';
            $_value = MFile::stripExt($file);

            $_file = MPATH_WP_PLG.'/miwoshop/site/opencart/catalog/language/english/module/'.$file;
            if (MFile::exists($_file)) {
                require_once($_file);

                if (isset($_['heading_title'])) {
                    $_title = $_['heading_title'];
                }
            }

            if (empty($_title)) {
                $_title = ucwords(str_replace('_', ' ', $_value));
            }

            $_title .= " ({$_value})";

            $options[] = array('value' => $_value, 'text' => $_title);
        }

        return MHtml::_('select.genericlist', $options, $this->name, 'class="inputbox" style="width:200px !important;"', 'value', 'text', $this->value, $this->name);
    }
}
