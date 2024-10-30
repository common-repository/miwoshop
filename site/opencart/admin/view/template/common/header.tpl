<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

<?php MiwoShop::get('base')->addHeader(MPATH_MIWOSHOP_OC . '/admin/view/stylesheet/stylesheet.css'); ?>
<?php MiwoShop::get('base')->addHeader(MPATH_MIWOSHOP_OC . '/admin/view/stylesheet/override.css'); ?>
<?php MiwoShop::get('base')->addHeader(MPATH_MIWI . '/plugins/plg_miwoshop_js/js/bootstrap/css/bootstrap.css'); ?>

<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/summernote/summernote.css" rel="stylesheet">
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />

<?php foreach ($styles as $style) { ?>
<link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/less-1.7.4.min.js"></script>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<script type="text/javascript" src="view/javascript/jquery/datetimepicker/moment.js" ></script>
<script type="text/javascript" src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>

<script src="view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>

</head>
<body>
<div id="container">
<?php if ($logged) { ?>
<header id="header" class="navbar navbar-static-top">
  <div id="miwoshop-search-div" class="col-sm-4 col-md-4 pull-left">
    <?php echo $search; ?>
  </div>
  <ul class="nav pull-right">
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><?php if(!empty($alert_order)) { ?><span class="label label-danger pull-left"><?php echo $alert_order; ?></span><?php } ?><i class="fa fa-shopping-cart fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_order; ?></li>
        <li><a href="<?php echo $order_status; ?>" style="display: block; overflow: auto;"><span class="label label-warning pull-right"><?php echo $order_status_total; ?></span><?php echo $text_order_status; ?></a></li>
        <li><a href="<?php echo $complete_status; ?>"><span class="label label-success pull-right"><?php echo $complete_status_total; ?></span><?php echo $text_complete_status; ?></a></li>
        <li><a href="<?php echo $return; ?>"><span class="label label-danger pull-right"><?php echo $return_total; ?></span><?php echo $text_return; ?></a></li>
      </ul>
    </li>
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><?php if(!empty($alert_customer)) { ?><span class="label label-danger pull-left"><?php echo $alert_customer; ?></span><?php } ?><i class="fa fa-user fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_customer; ?></li>
        <li><a href="<?php echo $online; ?>"><span class="label label-success pull-right"><?php echo $online_total; ?></span><?php echo $text_online; ?></a></li>
        <li><a href="<?php echo $customer_approval; ?>"><span class="label label-danger pull-right"><?php echo $customer_total; ?></span><?php echo $text_approval; ?></a></li>
      </ul>
    </li>
    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><?php if(!empty($alert_product)) { ?><span class="label label-danger pull-left"><?php echo $alert_product; ?></span><?php } ?><i class="fa fa-bell fa-lg"></i></a>
      <ul class="dropdown-menu dropdown-menu-right alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_product; ?></li>
        <li><a href="<?php echo $product; ?>"><span class="label label-danger pull-right"><?php echo $product_total; ?></span><?php echo $text_stock; ?></a></li>
        <li><a href="<?php echo $review; ?>"><span class="label label-danger pull-right"><?php echo $review_total; ?></span><?php echo $text_review; ?></a></li>
        <li class="divider"></li>
        <li class="dropdown-header"><?php echo $text_affiliate; ?></li>
        <li><a href="<?php echo $affiliate_approval; ?>"><span class="label label-danger pull-right"><?php echo $affiliate_total; ?></span><?php echo $text_approval; ?></a></li>
      </ul>
    </li>
    <li class="dropdown"><a href="http://miwisoft.com/support"><i class="fa fa-life-ring fa-lg"></i></a></li>
    <li id="header-profile" class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown">
        <img width="25" height="25" src="<?php echo $image; ?>" alt="<?php echo $firstname; ?> <?php echo $lastname; ?>" title="<?php echo $username; ?>" class="img-circle" />
        <span class="online-user"><?php echo $firstname; ?> <?php echo $lastname; ?></span>
      </a>
        <ul class="dropdown-menu dropdown-menu-right">
        <li>
          <div class="header-profile">
            <h4><a href="<?php echo $url_user ?>"><?php echo $firstname; ?> <?php echo $lastname; ?></a></h4>
            <small><?php echo $user_group; ?></small>
          </div>
        </li>
        <li class="divider"></li>
        <li class="dropdown-header"><?php echo $text_store; ?> <i class="fa fa-shopping-cart"></i></li>
        <?php foreach ($stores as $store) { ?>
        <li><a href="<?php echo $store['href']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
        <?php } ?>
        <li class="divider"></li>
      </ul>
    </li>
  </ul>
</header>
<?php } ?>