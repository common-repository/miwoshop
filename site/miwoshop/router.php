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

if (!class_exists('MiwisoftComponentRouterBase')) {
    if (class_exists('JComponentRouterBase')) {
        abstract class MiwisoftComponentRouterBase extends JComponentRouterBase {}
    }
    else {
        class MiwisoftComponentRouterBase {}
    }
}

class MiwoShopRouter extends MiwisoftComponentRouterBase
{

    static $cats = array();
    static $path = array();

    public function build(&$query) {
    	return $this->buildRoute($query);
    }

    public function parse(&$segments) {
    	return $this->parseRoute($segments);
    }

    public function buildRoute(&$query)
    {
        $Itemid = null;
        $segments = array();

        $requests = MRequest::get();

        if (isset($query['lang']) && $requests['language'] != $query['lang']) {
            foreach ($requests as $request_key => $request_value) {
                if ($request_key == 'language' || $request_key == 'miwoshop_store_id') {
                    continue;
                }

                $query[$request_key] = $request_value;
            }
        }

        $menu = $this->getMenu();

        $_get_itemid = 0;
        if($menu->getActive()){
            $_get_itemid = $menu->getActive()->id;
        }

        if(isset($query['_lang_code'])) {
            $_default = $menu->getDefault($query['_lang_code']);
            $query['Itemid'] = $_default->id;
            unset($query['_lang_code']);
        }

        $_get_route = MRequest::getVar('route', '');

        if( isset($query['Itemid']) and $_get_itemid != $query['Itemid'] and $_get_route == 'product/category' and isset($query['route']) and $query['route'] == 'product/product' ){
            $query['Itemid'] = $_get_itemid;
        }

        if (!empty($query['Itemid'])) {
            $Itemid = $query['Itemid'];
        } else {
            $Itemid = $this->getItemid();
        }

        if (empty($Itemid)) {
            $a_menu = $menu->getActive();
        } else {
            $a_menu = $menu->getItem($Itemid);
        }

        if (!empty($query['view'])) {
            if ($query['view'] == 'admin') {
                unset($query['view']);

                return $segments;
            }
            $_route = $this->getRoute($query['view'], false);
			if (!empty($_route)) {
				$query['route'] = $_route;
				unset($query['view']);
			}
			else {
				$segments[] = $query['view'];
				unset($query['view']);
			}
        }

        if (isset($query['route'])) {
            switch ($query['route']) {
                case 'product/product':
                    if (is_object($a_menu) and $a_menu->query['view'] == 'product' and $a_menu->query['product_id'] == @$query['product_id']) {
                        unset($query['path']);
                        unset($query['product_id']);
                        unset($query['manufacturer_id']);
                        break;
                    }

                    $segments[] = 'product';

                    if (isset($query['product_id'])) {
                        $id = $query['product_id'];
                        $name = MiwoShop::get('db')->getRecordAlias($id, 'product', $query);

                        if (!empty($name)) {
                            $segments[] = $id . ':' . $name;
                        } else {
                            $segments[] = $id;
                        }

                        unset($query['path']);
                        unset($query['product_id']);
                        unset($query['manufacturer_id']);
                        unset($query['sort']);
                        unset($query['order']);
                        unset($query['filter_name']);
                        unset($query['filter_tag']);
                        unset($query['limit']);
                        unset($query['page']);

                    }

                    break;
                case 'product/category':
                    $_path = explode('_', @$query['path']);
                    $m_id = end($_path);

                    if (is_object($a_menu) and $a_menu->query['view'] == 'category' and $a_menu->query['path'] == $m_id) {
                        unset($query['path']);
                        break;
                    }

                    $segments[] = 'category';

                    if (isset($query['path'])) {
                        $id = $query['path'];

                        if (strpos($id, '_')) {
                            $old_id = $id;
							$_id = explode('_', $id);
                            $id = end($_id);

                            self::$cats[$id] = $old_id;
                        } else {
                            self::$cats[$id] = $id;
                        }

                        $name = MiwoShop::get('db')->getRecordAlias($id, 'category', $query);

                        if (!empty($name)) {
                            $segments[] = $id . ':' . $name;
                        } else {
                            $segments[] = $id;
                        }

                        unset($query['path']);
                    }

                    break;
                case 'product/manufacturer/info':
                    if (is_object($a_menu) and $a_menu->query['view'] == 'manufacturer' and $a_menu->query['manufacturer_id'] == @$query['manufacturer_id']) {
                        unset($query['manufacturer_id']);
                        break;
                    }

                    $segments[] = 'manufacturer';

                    if (isset($query['manufacturer_id'])) {
                        $id = $query['manufacturer_id'];
                        $name = MiwoShop::get('db')->getRecordAlias($id, 'manufacturer', $query);

                        if (!empty($name)) {
                            $segments[] = $id . ':' . $name;
                        } else {
                            $segments[] = $id;
                        }

                        unset($query['manufacturer_id']);
                    }

                    break;
                case 'information/information':
                    if (is_object($a_menu) and $a_menu->query['view'] == 'information' and $a_menu->query['information_id'] == @$query['information_id']) {
                        unset($query['information_id']);
                        break;
                    }

                    $segments[] = 'information';

                    if (isset($query['information_id'])) {
                        $id = $query['information_id'];
                        $name = MiwoShop::get('db')->getRecordAlias($id, 'information', $query);

                        if (!empty($name)) {
                            $segments[] = $id . ':' . $name;
                        } else {
                            $segments[] = $id;
                        }

                        unset($query['information_id']);
                    }

                    break;
                case 'common/home':
                    break;
                default:
                    $_view = $this->getView($query['route']);

                    $_itemid_r = $this->getItemid($_view);
                    $_itemid_h = $this->getItemid('home');

                    if (($_itemid_r == $_itemid_h)) {
                        $segments[] = $query['route'];
                    }

                    break;
            }

            if(isset($query['_lang'])){
                $array_notset = array('Itemid', 'option');
                foreach ($query as $key => $value) {
                    if(!in_array($key, $array_notset)) {
                       unset($query[$key]);
                    }
                }
            }

            unset($query['route']);
        }

        foreach($segments as $key => $segment) {
            $segments[$key] = str_replace(':', '-', $segment);
        }

        return $segments;
    }

    public function parseRoute(&$segments)
    {
        $vars = array();

        if (empty($segments)) {
            //return $vars;
        }

        $c = count($segments);
        if ($c == 1) {
            $vars['view'] = $segments[0];
            $vars['route'] = $this->getRoute($segments[0]);

            $menu = $this->getMenu();
            $active_menu = $menu->getActive();

            if($segments[0] == 'success' and !empty($active_menu) and !empty($active_menu->query['view'])){
                $vars['view'] = '';
                $vars['route'] = $active_menu->query['view'].'/success';
            }

            if (MiwoShop::get('base')->is30()) {
                MRequest::set($vars, 'get');
            }

            return $vars;
        }

        $route = '';

        foreach ($segments as $segment) {
            if ($segment == 'product' and strpos($segments[1], ':')) {
                $route = 'product/product';

                list($id, $alias) = explode(':', $segments[1], 2);
                $vars['product_id'] = $id;
                break;
            }

            if ($segment == 'category' and strpos($segments[1], ':')) {
                $route = 'product/category';

                list($id, $alias) = explode(':', $segments[1], 2);

                $id = isset(self::$cats[$id]) ? self::$cats[$id] : $id;

                $parent_id = MiwoShop::get('db')->getParentCategoryId($id);
                while ($parent_id != 0) {
                    $id = $parent_id . '_' . $id;

                    $parent_id = MiwoShop::get('db')->getParentCategoryId($parent_id);
                }

                $vars['path'] = $id;

                break;
            }

            if ($segment == 'manufacturer' and strpos($segments[1], ':')) {
                $route = 'product/manufacturer/info';

                list($id, $alias) = explode(':', $segments[1], 2);

                $vars['manufacturer_id'] = $id;

                break;
            }

            if ($segment == 'information' and strpos($segments[1], ':')) {
                $route = 'information/information';

                list($id, $alias) = explode(':', $segments[1], 2);
                $vars['information_id'] = $id;

                break;
            }

            if ($segment == 'admin') {
                $vars['view'] = 'admin';
            }

            $route .= '/' . $segment;
        }

        if (!empty($route)) {
            $route = ltrim($route, '/');

            $vars['route'] = $route;
        }

        if (MiwoShop::get('base')->is30()) {
            MRequest::set($vars, 'get');
        }

        return $vars;
    }

    public function rewrite($url)
    {
        if (!strpos($url, 'page')) {
            $url = $this->route($url);
        } else {
            if (!strpos($url, 'option') and MiwoShop::get('base')->isAdmin('miwoshop')) {
                $url = str_replace('&amp;', '&', $url);
                $url = str_replace('index.php?route=', 'index.php?option=com_miwoshop&view=admin&route=', $url);
            }
        }

        return $url;
    }

    public function route($url)
    {
        $uri = MFactory::getURI();
        $app = MFactory::getApplication();
        $oc_config = MiwoShop::get('opencart')->get('config');

        $url = str_replace('&amp;', '&', $url);
        $url = str_replace('//index.php', '/index.php', $url);
        $url = str_replace('index.php?token=', 'index.php?option=com_miwoshop&token=', $url);
        $url = str_replace('index.php?route=', 'index.php?option=com_miwoshop&route=', $url);

        if ($app->isSite()) {
            $domain = MiwoShop::get('base')->getDomain();
            $full_url = MiwoShop::get('base')->getFullUrl();

            if (($oc_config->get('config_secure') == 1) and (substr($url, 0, 5) == 'https') and (substr($domain, 0, 5) != 'https')) {
                $domain = str_replace('http://', 'https://', $domain);
            }
			
			if(strpos($url, '/callback')) {
                return $url;
            }

            $url = str_replace($full_url, '', $url);
            $url = str_replace(str_replace('http', 'https', $full_url), '', $url);

            if (substr($url, 0, 10) == 'index.php?') {
                $url = str_replace('index.php?', '', $url);
                parse_str($url, $vars);

                if (!isset($vars['Itemid'])) {
                    $id = 0;

                    if (!isset($vars['view']) and !isset($vars['route'])) {
                        $view = 'home';
                    }

                    if (MiwoShop::get('base')->isAdmin('miwoshop')) {
                        $view = 'admin';
                    } else if (isset($vars['route'])) {
                        if ($vars['route'] == 'product/category') {
                            $path_array = explode('_', $vars['path']);
                            $id = end($path_array);
                        } elseif ($vars['route'] == 'product/product') {
                            $id = $vars['product_id'];
                            $__itemid = $uri->getVar('Itemid', 0);
                            if(MRequest::getVar('route', '') == 'product/category' and !empty($__itemid)) {
                                $Itemid = $__itemid;
                            }
                        } elseif ($vars['route'] == 'product/manufacturer/info') {
                            $id = $vars['manufacturer_id'];
                        } elseif ($vars['route'] == 'information/information') {
                            $id = $vars['information_id'];
                        }

                        $view = $this->getView($vars['route']);
                    }

                    if(empty($Itemid)){
                        $Itemid = $this->getItemid($view, $id);
                    }

                    if (!empty($Itemid)) {
                        $vars['Itemid'] = $Itemid;
                    }
                }

                if (strpos($url, 'captcha')) {
                    $vars['tmpl'] = 'component';
                    $vars['format'] = 'raw';
                }

                if ($this->_addLangCode($url)) {
                    $_lang_id = (int)MiwoShop::getClass('opencart')->get('config')->get('config_language_id');
                    $_lang = MiwoShop::getClass('db')->getLanguage($_lang_id);

                    if (!empty($_lang['code'])) {
                        $vars['lang'] = $_lang['code'];
                    }
                }

                if (MiwoShop::get('base')->isAdmin('miwoshop') and (!isset($vars['view']) || $vars['view'] != 'admin')) {
                    $vars['view'] = 'admin';
                }

                if (isset($vars['view']) and isset($vars['route']) and ($vars['view'] != 'admin')) {
                    unset($vars['view']);
                }

                unset($vars['miwoshop_store_id']);

                $url = 'index.php';
                foreach ($vars as $var => $val) {
                    $sign = '&';

                    if ($var == 'option') {
                        $sign = '?';
                    }

                    $url .= $sign . $var . '=' . $val;
                }

                $ssl_checkouts = array('checkout/simplecheckout', 'checkout/simplified_checkout');
                if (($oc_config->get('config_secure') == 1) and isset($vars['route']) and in_array($vars['route'], $ssl_checkouts)) {
                    $domain = str_replace('http://', 'https://', $domain);
                }

                if (MiwoShop::get('base')->is30() and isset($vars['route']) and !isset($_GET['product_id']) and isset($_GET['route']) and $vars['route'] == $_GET['route']) {
                    MRequest::set($vars, 'get');
                }
            }

			if ((defined('DOING_AJAX') && DOING_AJAX) && get_site_option('permalink_structure')) {
				$page_id = MFactory::getWOption('miwoshop_page_id');
				$post = MFactory::getWPost($page_id);
				$url = str_replace('index.php', $post->post_name, $url);
			} else {
				$url = MRoute::_($url);
			}
            $url = str_replace('&amp;', '&', $url);

            $_base = MFactory::getUri()->base(true);
            $url = str_replace($_base, '', $url);
            //for external links
            $out = strpos($url, '#outurl');

            if ($out === false) {
                $url = $domain . ltrim($url, '/');
            } else {
                $url = str_replace('#outurl', '', $url);
            }
        } else {
            $domain = MiwoShop::get('base')->getDomain();
            $full_url = MiwoShop::get('base')->getFullUrl();

            if (($oc_config->get('config_secure') == 1) and (substr($url, 0, 5) == 'https') and (substr($domain, 0, 5) != 'https')) {
                $domain = str_replace('http://', 'https://', $domain);
            }

            $url = str_replace($full_url, '', $url);
            $url = str_replace(str_replace('http', 'https', $full_url), '', $url);
            $url = str_replace('wp-admin/', '', $url);

            if (MiwoShop::get()->isExternal()) {
                $url .= '&view=' . MRequest::getCmd('view');
            }

            $url = MRoute::_($url);
        }

        return $url;
    }

    public function getItemid($view = 'home', $record_id = 0, $with_name = false)
    {
        static $ids = array();
        static $store_id;
        static $items;

        if (!isset($store_id)) {
            $store_id = MiwoShop::get('base')->getStoreId();
        }

        if (!isset($items)) {
            $component = MComponentHelper::getComponent('com_miwoshop');

            $items = $this->getMenu()->getItems('component_id', $component->id);
        }

        if (!isset($ids[$view][$record_id]) and is_array($items)) {
            if ($view == 'product') {
                $cat_id = MiwoShop::get('db')->getProductCategoryId($record_id);
                $needles = array(
                    'product' => (int)$record_id,
                    'category' => (int)$cat_id
                );
            } else if ($view == 'category') {
                $needles = array(
                    'category' => (int)$record_id
                );
            } else if ($view == 'manufacturer') {
                $needles = array(
                    'manufacturer' => (int)$record_id
                );
            } else if ($view == 'information') {
                $needles = array(
                    'information' => (int)$record_id
                );
            } else {
                $needles = array(
                    $view => $record_id
                );
            }

            $menu_id = $this->_findItemId($needles, $items, $store_id);

            $ids[$view][$record_id] = $menu_id;
        }

        $Itemid = '';

        if (empty($ids[$view][$record_id])) {
            return $Itemid;
        }

        $Itemid = $ids[$view][$record_id];

        if ($with_name == true) {
            $Itemid = '&Itemid=' . $Itemid;
        }

        return $Itemid;
    }

    protected function _findItemId($needles, $items, $store_id, $recursive_cats = true)
    {
        static $home_id;
        static $menu_ids = array();

        $menu = $this->getMenu();
        $menu_id = null;

        foreach ($needles as $needle => $id) {
            if (!empty($menu_ids[$needle][$id])) {
                $menu_id = $menu_ids[$needle][$id];
                break;
            }

            foreach ($items as $item) {
                $params = $item->params instanceof MRegistry ? $item->params : $menu->getParams($item->id);

                if ($params->get('miwoshop_store_id', 0) != $store_id) {
                    continue;
                }

                if ($needle == 'product') {
                    if (isset($item->query['view']) and (@$item->query['view'] == $needle) and (@$item->query['product_id'] == $id)) {
                        $menu_id = $item->id;
                        $menu_ids[$needle][$id] = $menu_id;
                        break;
                    }
                } else if ($needle == 'category') {
                    if (isset($item->query['view']) and (@$item->query['view'] == $needle)) {
                        if (@$item->query['path'] == $id) {
                            $menu_id = $item->id;
                            $menu_ids[$needle][$id] = $menu_id;
                            break;
                        } else if ($recursive_cats == true) {
                            $parent_id = MiwoShop::get('db')->getParentCategoryId($id);

                            if ($parent_id != 0) {
                                $needles = array(
                                    'category' => (int)$parent_id
                                );

                                $menu_id = $this->_findItemId($needles, $items, $store_id);
                                $menu_ids[$needle][$id] = $menu_id;
                            }
                        }
                    }
                } else if ($needle == 'manufacturer') {
                    if (isset($item->query['view']) and (@$item->query['view'] == $needle) and (@$item->query['manufacturer_id'] == $id)) {
                        $menu_id = $item->id;
                        $menu_ids[$needle][$id] = $menu_id;
                        break;
                    }
                } else if ($needle == 'information') {
                    if (isset($item->query['view']) and (@$item->query['view'] == $needle) and (@$item->query['information_id'] == $id)) {
                        $menu_id = $item->id;
                        $menu_ids[$needle][$id] = $menu_id;
                        break;
                    }
                } else {
                    if (isset($item->query['view']) and @$item->query['view'] == $needle) {
                        $menu_id = $item->id;
                        $menu_ids[$needle][$id] = $menu_id;
                        break;
                    }
                }

                if (isset($item->query['view']) and empty($home_id) and @$item->query['view'] == 'home') {
                    $home_id = $item->id;
                    $menu_ids[$needle][$id] = $menu_id;
                }
            }

            if (!empty($menu_id)) {
                break;
            }
        }

        if (empty($menu_id) and !empty($home_id)) {
            $menu_id = $home_id;
        }

        return $menu_id;
    }

    public function getMenu()
    {
        mimport('framework.application.menu');
        $options = array();

        $menu = MMenu::getInstance('site', $options);

        if (MError::isError($menu)) {
            $null = null;
            return $null;
        }

        return $menu;
    }

    public function generateAlias($title)
    {
        $alias = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

        if (MFactory::getConfig()->get('unicodeslugs') == 1) {
            $alias = MFilterOutput::stringURLUnicodeSlug($alias);
        } else {
            $alias = MFilterOutput::stringURLSafe($alias);
        }

        if (trim(str_replace('-', '', $alias)) == '') {
            $mainframe = MFactory::getApplication();

            $date = MFactory::getDate();

            if (MiwoShop::get('base')->is30()) {
                $date->setTimezone($mainframe->getCfg('offset'));
                $alias = $date->format("%Y-%m-%d-%H-%M-%S");
            } else {
                $date->setOffset($mainframe->getCfg('offset'));
                $alias = $date->toFormat("%Y-%m-%d-%H-%M-%S");
            }
        }

        return $alias;
    }

    public function getView($route, $use_default = true)
    {
        $view = '';

        switch ($route) {
            case 'common/home':
                $view = 'home';
                break;
            case 'account/account':
                $view = 'account';
                break;
            case 'checkout/cart':
                $view = 'cart';
                break;
            case 'checkout/checkout':
                $view = 'checkout';
                break;
            case 'account/wishlist':
                $view = 'wishlist';
                break;
            case 'information/contact':
                $view = 'contact';
                break;
            case 'product/product':
                $view = 'product';
                break;
            case 'product/category':
                $view = 'category';
                break;
            case 'product/compare':
                $view = 'compare';
                break;
            case 'product/manufacturer/info':
                $view = 'manufacturer';
                break;
            case 'product/manufacturer':
                $view = 'manufacturers';
                break;
            case 'account/login':
                $view = 'login';
                break;
            case 'account/register':
                $view = 'registration';
                break;
            case 'account/order':
                $view = 'orders';
                break;
            case 'account/download':
                $view = 'downloads';
                break;
            case 'product/search':
                $view = 'search';
                break;
            case 'account/newsletter':
                $view = 'newsletter';
                break;
            case 'account/voucher':
                $view = 'voucher';
                break;
            case 'information/sitemap':
                $view = 'sitemap';
                break;
            case 'account/return/add':
                $view = 'returns';
                break;
            case 'affiliate/account':
                $view = 'affiliates';
                break;
            case 'product/special':
                $view = 'specials';
                break;
            case 'information/information':
                $view = 'information';
                break;
            case 'admin':
                $view = 'admin';
                break;
            case 'product/latest':
                $view = 'latest';
                break;
            case 'product/popular':
                $view = 'popular';
                break;
            case 'product/bestseller':
                $view = 'bestseller';
                break;
            default:
                if ($use_default == true) {
                    $view = 'home';
                }
                break;
        }

        return $view;
    }

    public function getRoute($view, $use_default = true)
    {
        $route = '';

        switch ($view) {
            case 'home':
                $route = 'common/home';
                break;
            case 'account':
                $route = 'account/account';
                break;
            case 'cart':
                $route = 'checkout/cart';
                break;
            case 'checkout':
                $route = 'checkout/checkout';
                break;
            case 'wishlist':
                $route = 'account/wishlist';
                break;
            case 'contact':
                $route = 'information/contact';
                break;
            case 'product':
                $route = 'product/product';
                break;
            case 'category':
                $route = 'product/category';
                break;
            case 'compare':
                $route = 'product/compare';
                break;
            case 'manufacturer':
                $route = 'product/manufacturer/info';
                break;
            case 'manufacturers':
                $route = 'product/manufacturer';
                break;
            case 'login':
                $route = 'account/login';
                break;
            case 'registration':
                $route = 'account/register';
                break;
            case 'orders':
                $route = 'account/order';
                break;
            case 'downloads':
                $route = 'account/download';
                break;
            case 'search':
                $route = 'product/search';
                break;
            case 'newsletter':
                $route = 'account/newsletter';
                break;
            case 'voucher':
                $route = 'account/voucher';
                break;
            case 'sitemap':
                $route = 'information/sitemap';
                break;
            case 'returns':
                $route = 'account/return/add';
                break;
            case 'affiliates':
                $route = 'affiliate/account';
                break;
            case 'specials':
                $route = 'product/special';
                break;
            case 'information':
                $route = 'information/information';
                break;
            case 'admin':
                $route = 'admin';
                break;
            case 'latest':
                $route = 'product/latest';
                break;
            case 'popular':
                $route = 'product/popular';
                break;
            case 'bestseller':
                $route = 'product/bestseller';
                break;
            default:
                if ($use_default == true) {
                    $route = 'common/home';
                }
                break;
        }

        return $route;
    }

    public function _cleanTitle($text)
    {
        $replace = array("&quot;");

        foreach ($replace as $value) {
            $text = str_replace($value, "", $text);
        }

        return $text;
    }

    public function _addLangCode($url)
    {
        if (strpos($url, '&lang=')) {
            return false;
        }

        if (MiwoShop::get('base')->isAdmin('miwoshop')) {
            return false;
        }

        if (MiwoShop::get('base')->isMiwosefInstalled() and (Miwosef::getConfig()->multilang == 1)) {
            return true;
        }

        if (MiwoShop::get('base')->isSh404sefInstalled() and (Sh404sefFactory::getConfig()->enableMultiLingualSupport == 1)) {
            return true;
        }

        if (MiwoShop::get('base')->isJoomsefInstalled() and (SEFConfig::getConfig()->langEnable)) {
            return true;
        }

        if (MiwoShop::get('base')->plgEnabled('system', 'languagefilter')) {
            return true;
        }

        return false;
    }

    function _getParentCat($id)
    {
        static $cats = array();

        if (!empty($cats)) {
            return $cats[$id];
        }

        $sql = "SELECT category_id, parent_id FROM #__miwoshop_category WHERE status = 1";
        $jdb = MFactory::getDbo();
        $jdb->setQuery($sql);
        $_cats = $jdb->loadRowList();


        foreach ($_cats as $_cat) {
            $cats[$_cat[0]] = $_cat[1];
        }

        return $cats[$id];
    }
}