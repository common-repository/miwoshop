<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $href_return; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="button btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($bulk_linking_status) { ?>
    <div class="alert alert-info"><?php echo $text_load_listings ?></div>
    <div class="well">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <label class="control-label" for="marketplace_select"><?php echo $text_choose_marketplace; ?></label>
            <select name="marketplace_select" id="marketplace_select" class="form-control">
              <?php foreach ($marketplaces as $marketplace) { ?>
              <option value="<?php echo $marketplace['link']; ?>" <?php if ($marketplace['code'] == $marketplace_code) { echo ' selected'; } ?>><?php echo $marketplace['name'] ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <?php if (in_array($marketplace_code, $marketplaces_processing)) { ?>
          <div class="pull-right"> <a class="button button-primary" disabled="disabled"><i class="fa fa-cog fa-lg fa-spin"></i> <?php echo $text_loading; ?></a> </div>
          <?php } else { ?>
          <div class="pull-right"> <a id="button-load-listings" class="button button-primary" href="<?php echo $marketplaces[$marketplace_code]['href_load_listings'] ?>"><?php echo $button_load ?></a> </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <form id="bulk-link-form" class="form-horizontal">
      <div id="text-<?php echo $marketplace_code; ?>">
        <?php if (!in_array($marketplace_code, $marketplaces_processing)) { ?>
        <?php if ($unlinked_products) { ?>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th></th>
              <th class="text-center" colspan="4"><?php echo $text_amazon ?></th>
              <th class="text-center" colspan="4"><?php echo $text_local ?></th>
            </tr>
            <tr>
              <th class="text-center"><input type="checkbox" class="master-checkbox" value="<?php echo $marketplace['code'] ?>"/></th>
              <th class="text-left"><?php echo $column_asin ?></th>
              <th class="text-left"><?php echo $column_sku ?></th>
              <th class="text-left"><?php echo $column_quantity ?></th>
              <th class="text-right"><?php echo $column_price ?></th>
              <th class="text-left"><?php echo $column_name ?></th>
              <th class="text-left"><?php echo $column_sku ?></th>
              <th class="text-left"><?php echo $column_quantity ?></th>
              <th class="text-left"><?php echo $column_combination ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $row = 0; ?>
            <?php foreach ($unlinked_products as $product) { ?>
            <?php $row++ ?>
            <tr>
              <td class="text-center"><input type="checkbox" class="link-checkbox link-checkbox-<?php echo $marketplace['code'] ?>"/></td>
              <td class="text-left"><a href="<?php echo $product['href_amazon'] ?>" target="_blank"><?php echo $product['asin'] ?></a></td>
              <td class="text-left"><?php echo $product['amazon_sku'] ?></td>
              <td class="text-left"><?php echo $product['amazon_quantity'] ?></td>
              <td class="text-right"><?php echo $product['amazon_price'] ?></td>
              <td class="text-left"><a href="<?php echo $product['href_product'] ?>" target="_blank"><?php echo $product['name'] ?></a></td>
              <td class="text-left"><?php echo $product['sku'] ?></td>
              <td class="text-left"><?php echo $product['quantity'] ?></td>
              <td class="text-left"><?php echo $product['combination'] ?></td>
              <input type="hidden" name="link[<?php echo $row ?>][amazon_sku]" value="<?php echo $product['amazon_sku'] ?>"/>
              <input type="hidden" name="link[<?php echo $row ?>][product_id]" value="<?php echo $product['product_id'] ?>"/>
              <input type="hidden" name="link[<?php echo $row ?>][var]" value="<?php echo $product['var'] ?>"/>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
        <?php } ?>
        <?php } ?>
      </div>
    </form>
    <?php } else { ?>
    <div class="warning"><?php echo $error_bulk_link_permission ?></div>
    <?php } ?>
  </div>
</div>
<script type="text/javascript"><!--
$('#button-load-listings').bind('click', function (e) {
  e.preventDefault();

  $.ajax({
    url: $(this).attr('href'),
    dataType: 'json',
    beforeSend: function () {
      $('#button-load-listings').empty().html('<i class="fa fa-cog fa-lg fa-spin"></i> <?php echo $text_loading; ?>').attr('disabled','disabled');
      $('.alert-danger, .alert-success').remove();
    },
    success: function (json) {
      if (json['status'] == 1) {
        $('.alert-info').after('<div class="alert alert-success">' + json['message'] + '</div>');
      } else {
        $('.alert-info').after('<div class="alert alert-danger">' + json['message'] + '</div>');
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {
      if (xhr.status != 0) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
    }
  });

  return false;
});

$('.master-checkbox').click(function () {
  var marketplace = $(this).val();
  if ($(this).is(':checked')) {
    $('.link-checkbox-' + marketplace).attr('checked', 'checked');
  } else {
    $('.link-checkbox-' + marketplace).removeAttr('checked');
  }
});

$('.link-button').click(function (e) {
  e.preventDefault();

  $.ajax({
    url: '<?php echo html_entity_decode($href_do_bulk_linking) ?>',
    dataType: 'json',
    type: 'POST',
    data: $('.link-checkbox:checked').parent().siblings('input[type="hidden"]').serialize(),
    success: function (json) {
      document.location.reload(true);
    },
    error: function(xhr, ajaxOptions, thrownError) {
      if (xhr.status != 0) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
    }
  });
});

$('#marketplace_select').bind('change', function() {
  location = $('#marketplace_select').val();
});
//--></script>
<?php echo $footer; ?>