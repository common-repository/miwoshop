<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die ('Restricted access');

require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');

$base = MiwoShop::get('base');

if (!$base->checkRequirements('module')) {
    return;
}

$outputs = MiwoShop::get('opencart')->loadModule($params->get('module', 'miwoshopcart'), $params->get('layout_id', '14'));

foreach($outputs as $output) {
    if (is_object($output) || empty($output)) {
        return;
    }

    //$output = @preg_replace('#(<h3>)(.*)(</h3>)#e', "", $output);
    $output = @preg_replace('/(<h3>)(.*)(<\/h3>)/', "", $output);

    $output = $base->replaceOutput($output, 'module');

    echo $output;
}