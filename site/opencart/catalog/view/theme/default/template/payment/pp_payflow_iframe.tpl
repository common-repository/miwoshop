<?php if ($checkout_method == 'iframe') { ?>
  <iframe src="<?php echo $iframe_url ?>" scrolling="no" width="560px" height="540px" frameBorder="0"></iframe>
<?php } else { ?>
  <div class="buttons">
    <div class="pull-right">
      <a class="button button-primary" href="<?php echo $iframe_url ?>"><?php echo $button_confirm ?></a>
    </div>
  </div>
<?php } ?>