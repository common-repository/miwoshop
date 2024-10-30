<style type="text/css">
    .dropdown .caret {
        margin-top: 8px !important;
    }

    select, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
        height: 30px !important;
        margin-bottom: 10px !important;
    }

    select, input, textarea {
        width: 300px;
    }

    #thumb-image{
        margin-bottom: 10px;;
    }

    .wizard_bottom {
        margin-left: 33%;
        padding-top: 20px;
    }

    table{
        width: 100%;
    }

</style>

<?php MiwoShop::get('base')->addHeader(MPATH_MIWOSHOP_OC . '/admin/view/stylesheet/wizard.css'); ?>

<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<?php MiwoShop::get('base')->addHeader(MPATH_MIWI . '/plugins/plg_miwoshop_js/js/bootstrap/css/bootstrap.css'); ?>

<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="view/javascript/common.js" ></script>

<div class="panel panel-default" style="margin: 50px">
  <div class="panel-heading" style="height: 60px; background: #EFEFEF;">
      <h2 class="panel-title pull-left"><img src="view/image/wizard.png" alt="" /> <?php echo $heading_title; ?></h2>
      <a class="btn btn-small skip pull-right" href="<?php echo $link_skip; ?>"><?php echo $text_skip; ?></a>
  </div>
  <div id="content_oc" class="panel-body" style="padding-left: 200px">
      <form action="<?php echo $link_action; ?>" method="post">
          <fieldset class="wizard_step">
              <legend><?php echo $text_step_1; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td style="width: 200px"><?php echo $text_personal_id; ?></td>
                          <td><input type="password" readonly="true" name="pid" value=""/></td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <fieldset class="wizard_step">
              <legend><?php echo $text_step_2; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td style="width: 200px"><?php echo $text_logo; ?></td>
                          <td>
                              <div class="form-group">
                                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                                  <input type="hidden" name="logo" value="<?php echo $image; ?>" id="input-image" />
                                </div>
                              </div>
                          </td>
                      </tr>
                      <tr>
                          <td><?php echo $text_name; ?></td>
                          <td><input type="text" name="name" /></td>
                      </tr>                        <tr>
                          <td><?php echo $text_mail; ?></td>
                          <td><input type="text" name="mail" /></td>
                      </tr>                        <tr>
                          <td><?php echo $text_telephone; ?></td>
                          <td><input type="text" name="telephone" /></td>
                      </tr>                        <tr>
                          <td><?php echo $text_address; ?></td>
                          <td><textarea name="address" ></textarea></td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <fieldset class="wizard_step">
              <legend><?php echo $text_step_3; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td style="width: 200px"><?php echo $text_country; ?></td>
                          <td>
                              <select name="country_id">
                              <?php foreach ($countries as $country) { ?>
                                  <?php if ($country['country_id'] == $country_id) { ?>
                                  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                                  <?php } ?>
                              <?php } ?>
                              </select>
                          </td>
                      </tr>
                      <tr>
                          <td><?php echo $text_zone; ?></td>
                          <td>
                              <select name="zone_id"></select>
                          </td>
                      </tr>
                      <tr>
                          <td><?php echo $text_currency; ?></td>
                          <td>
                              <select name="currency">
                              <?php foreach ($currencies as $currency) { ?>
                                  <?php if ($currency['code'] == $config_currency) { ?>
                                  <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
                                  <?php } else { ?>
                                  <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                                  <?php } ?>
                              <?php } ?>
                              </select>
                          </td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <fieldset class="wizard_step">
              <legend><?php echo $text_step_4; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td style="width: 200px"><?php echo $text_product_display; ?></td>
                          <td>
                              <div class="row" id="radios">
                                <div class="col-xs-12">
                                   <div class="form-group">
                                      <div class="col-md-6">
                                         <input name="miwoshop_display" type='hidden' value="0"/>
                                         <div class="btn-group" data-toggle="buttons">
                                            <button type="button" class="btn btn-default" data-radio-name="radio" value="1"><?php echo $text_grid; ?></button>
                                            <button type="button" class="btn btn btn-success active" data-radio-name="radio" value="0"><?php echo $text_list; ?></button>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                             </div>
                          </td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <?php if(!empty($lang)) { ?>
          <fieldset class="wizard_step">
              <legend><?php echo $text_step_7; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td colspan="2"><?php echo $text_language_info; ?></td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <?php } ?>
          <?php if(!empty($mig_coms)) { ?>
          <fieldset  class="wizard_step">
              <legend><?php echo $text_step_6; ?></legend>
              <div class="wizard_step_content">
                  <table>
                      <tr>
                          <td style="width: 200px"><?php echo $text_migration; ?></td>
                          <td>
                              <select name="migration">
                                  <option value="0"><?php echo $text_select; ?></option>
                                  <?php foreach ($mig_coms as $mig_com) { ?>
                                  <option value="<?php echo $mig_com['link']; ?>"><?php echo $mig_com['name']; ?></option>
                                  <?php } ?>
                              </select>
                          </td>
                      </tr>
                  </table>
              </div>
          </fieldset>
          <?php } ?>
          <div class="wizard_bottom">
              <input type="hidden" id="redirect" name="redirect" value="<?php echo $link_new_product; ?>" />
              <button type="submit" id="btn-save" class="btn btn-large btn-success" ><?php echo $text_save_and_new_product; ?></button>
          </div>
      </form>
  </div>
</div>

<script type="text/javascript"><!--
function image_upload(field, thumb) {
	jQuery('#dialog').remove();

	jQuery('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

	jQuery('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if (jQuery('#' + field).attr('value')) {
				jQuery.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent(jQuery('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						jQuery('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>

<script type="text/javascript"><!--
jQuery('select[name=\'country_id\']').bind('change', function() {
	jQuery.ajax({
		url: 'index.php?route=setting/store/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			jQuery('select[name=\'country_id\']').after('<span class="wait">&nbsp;<img src="view/image/loading.gif" alt="" /></span>');
		},
		complete: function() {
			jQuery('.wait').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				jQuery('#postcode-required').show();
			} else {
				jQuery('#postcode-required').hide();
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
	      				html += ' selected="selected"';
	    			}

	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			jQuery('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

jQuery('select[name=\'country_id\']').trigger('change');

jQuery('select[name=\'migration\']').bind('change', function() {
    if(this.value == 0) {
        jQuery('#btn-save').text('<?php echo $text_save_and_new_product; ?>');
        jQuery('#redirect').attr('value', '<?php echo $link_new_product; ?>');
    } else {
        jQuery('#btn-save').text('<?php echo $text_save_and_migration; ?>');
        jQuery('#redirect').attr('value', this.value);
    }
});
//--></script>

<script type="text/javascript">
    jQuery('.btn[data-radio-name="radio"]').click(function() {
        jQuery('.btn[data-radio-name="'+jQuery(this).data('radioName')+'"]').removeClass('btn-success');
        jQuery('.btn[data-radio-name="'+jQuery(this).data('radioName')+'"]').removeClass('active');
        jQuery(this).addClass('btn-success')

        jQuery('input[name="miwoshop_display"]').val( jQuery(this).val());
    });

    jQuery('.btn[data-radio-name="radio-menu"]').click(function() {
        jQuery('.btn[data-radio-name="'+jQuery(this).data('radioName')+'"]').removeClass('btn-danger');
        jQuery('.btn[data-radio-name="'+jQuery(this).data('radioName')+'"]').removeClass('btn-success');
        jQuery('.btn[data-radio-name="'+jQuery(this).data('radioName')+'"]').removeClass('active');

        if(jQuery(this).val() == 0){
            jQuery(this).addClass('btn-danger');
        }
        else{
            jQuery(this).addClass('btn-success');
        }

        jQuery('input[name="home_menu"]').val( jQuery(this).val());
    });


</script>