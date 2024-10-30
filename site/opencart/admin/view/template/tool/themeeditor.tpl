<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Theme Editor</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-exchange"></i>Theme Editor</h3>
      </div>
      <div class="panel-body">
          <div class="miwi_paid">
            <strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'Theme Editor'); ?></strong>
            <br /><br />
            <?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoshop-wordpress-shopping-cart#pricing','MiwoShop'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>