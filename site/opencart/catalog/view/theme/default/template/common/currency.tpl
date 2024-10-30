<?php if (count($currencies) > 1) { ?>
<div class="pull-left">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="currency">
  <div class="nav pull-left top-links">
    <ul class="list-inline">
      <li class="dropdown"><a href="javascript::void(0);" title="<?php echo $text_currency; ?>" class="dropdown-toggle" data-toggle="dropdown">
        <?php foreach ($currencies as $currency) { ?>
        <?php if ($currency['symbol_left'] && $currency['code'] == $code) { ?>
        <strong><?php echo $currency['symbol_left']; ?></strong>
        <?php } elseif ($currency['symbol_right'] && $currency['code'] == $code) { ?>
        <strong><?php echo $currency['symbol_right']; ?></strong>
        <?php } ?>
        <?php } ?>
        <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_currency; ?></span> <span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-left">
          <?php foreach ($currencies as $currency) { ?>
          <?php if ($currency['symbol_left']) { ?>
          <li><a class="currency-select" href="javascript::void(0);" name="<?php echo $currency['code']; ?>"><?php echo $currency['symbol_left']; ?> <?php echo $currency['title']; ?></a></li>
          <?php } else { ?>
          <li><a class="currency-select" href="javascript::void(0);" class="currency-select" name="<?php echo $currency['code']; ?>"><?php echo $currency['symbol_right']; ?> <?php echo $currency['title']; ?></a></li>
          <?php } ?>
          <?php } ?>
        </ul>
      </li>
    </ul>
  </div>
  <input type="hidden" name="code" value="" />
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
</form>
</div>
<?php } ?>

