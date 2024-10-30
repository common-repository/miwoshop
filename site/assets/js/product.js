jQuery(document).ready(function() {
    jQuery('.success img, .warning img, .attention img, .information img').live('click', function() {
        jQuery(this).parent().fadeOut('slow', function() {
            jQuery(this).remove();
        });
    });
});

function addProductToCart(product_id, product_options) {
    var post_data = 'product_id=' + product_id + '&quantity=1';

    if (product_options) {
        var options_array = jQuery('[name^=option_oc]');

        for (var i in options_array) {
            if(isNaN(i)){
                break;
            }
            
            var option_array = options_array[i];

            post_data = post_data + '&' + option_array.name + '=' + option_array.value;
        }
    }

    jQuery.ajax({
        url: 'index.php?option=com_miwoshop&format=raw&tmpl=component&route=checkout/cart/add',
        type: 'post',
        data: post_data,
        dataType: 'json',
        success: function(json) {
            jQuery('.success, .warning, .attention, .information, .error').remove();

            if (json['redirect']) {
                location = json['redirect'];
            }

            if (json['success']) {
                jQuery('#p_notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="'+ wpcontenturl +'/plugins/miwoshop/site/opencart/catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

                jQuery('.success').fadeIn('slow');

                jQuery('#cart-total,#cart > a > span').html(json['total']);

                jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
				
				jQuery('#cart > ul,#module_cart > ul').load('index.php?option=com_miwoshop&route=common/cart/info&format=raw&tmpl=component ul li');
            }
        }
    });
}