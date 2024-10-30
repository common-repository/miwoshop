<?php
/**
 * @package		MiwoShop
 * @copyright	2009-2016 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

$view = MRequest::getCmd('view');

MHTML::_('behavior.switcher');

// Load submenus
$views = array( '&route=setting/store'			=> MText::_('COM_MIWOSHOP_SETTINGS'),
				'&route=catalog/category'		=> MText::_('COM_MIWOSHOP_CATEGORIES'),
                '&route=catalog/product'		=> MText::_('COM_MIWOSHOP_PRODUCTS'),
				'&route=marketing/coupon'		=> MText::_('COM_MIWOSHOP_COUPONS'),
				'&route=sale/customer'			=> MText::_('COM_MIWOSHOP_CUSTOMERS'),
                '&route=sale/order'			    => MText::_('COM_MIWOSHOP_ORDERS'),
				'&route=marketing/affiliate'	=> MText::_('COM_MIWOSHOP_AFFILIATES'),
				'&route=marketing/contact'		=> MText::_('COM_MIWOSHOP_MAILING'),
				'&route=common/upgrade'			=> MText::_('COM_MIWOSHOP_UPGRADE'),
				'&route=common/support'			=> MText::_('COM_MIWOSHOP_SUPPORT')
				);

if (!class_exists('JSubMenuHelper')) {
    return;
}

foreach($views as $key => $val) {
	if ($key == '') {
		$active	= ($view == $key);
		
		$img = 'icon-16-miwovideos.png';
	}
	else {
	    $a = explode('&', $key);
	  	$c = explode('=', $a[1]);
	
		$active	= ($view == $c[1]);
	
		$img = 'icon-16-miwovideos-'.$c[1].'.png';
	}
	
	JSubMenuHelper::addEntry('<img src="'. MPATH_WP_PLG .'/miwoshop/site/assets/images/'.$img.'" style="margin-right: 2px;" align="absmiddle" />&nbsp;'.$val, 'index.php?option=com_miwoshop'.$key, $active);
}