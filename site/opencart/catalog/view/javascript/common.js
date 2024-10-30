function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

function fixCartButtonText(){
    var groups = jQuery('.miwoshop .product-thumb .button-group');
    if(groups.length > 0) {
        if(jQuery(groups).first().width() < 190) {
            groups.each(function( index ) {
                jQuery(this).children().first().children().last().css('display', 'none');
            });

            jQuery('.miwoshop .product-thumb .button-group button').css('width', '33.33333%')
        }
        else{
            groups.each(function( index ) {
                jQuery(this).children().first().children().last().css('display', 'inline');
                jQuery(this).children().first().css('width', '60%')

            });

            jQuery('.miwoshop .product-thumb .button-group button + button ').css('width', '20%')
        }
    }
}

jQuery(window).on('resize', function () {
    fixCartButtonText();
});


jQuery(document).ready(function() {

	var pageInitialized  = window.pageInitialized ;
	if(pageInitialized  === undefined){
		window.pageInitialized  = 1;
	}
	else {
		//to avoid loading twice 
		return;
	}

	// Adding the clear Fix
	cols1 = jQuery('#column-right, #column-left').length;
	
	if (cols1 == 2) {
		jQuery('#content_oc .product-layout:nth-child(2n+2)').after('<div class="clearfix visible-md visible-sm"></div>');
	} else if (cols1 == 1) {
		jQuery('#content_oc .product-layout:nth-child(3n+3)').after('<div class="clearfix visible-lg"></div>');
	} else {
		jQuery('#content_oc .product-layout:nth-child(4n+4)').after('<div class="clearfix"></div>');
	}
	
	// Highlight any found errors
	jQuery('.text-danger').each(function() {
		var element = jQuery(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
		
	// Currency
	jQuery('#currency .currency-select').on('click', function(e) {
		e.preventDefault();

		jQuery('#currency input[name=\'code\']').attr('value', jQuery(this).attr('name'));

		jQuery('#currency').submit();
	});

	// Language
	jQuery('#language a').on('click', function(e) {
		e.preventDefault();

		jQuery('#language input[name=\'code\']').attr('value', jQuery(this).attr('href'));

		jQuery('#language').submit();
	});

	/* Search */
	jQuery('#search_oc input[name=\'search_oc\']').parent().find('button').on('click', function() {
		url = jQuery('base').attr('href') + 'index.php?option=com_miwoshop&route=product/search';

		var value = jQuery('header input[name=\'search_oc\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	jQuery('#search_oc input[name=\'search_oc\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			jQuery('header input[name=\'search_oc\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	jQuery('#menu_oc .dropdown-menu').each(function() {
		var menu = jQuery('#menu_oc').offset();
		var dropdown = jQuery(this).parent().offset();

		var i = (dropdown.left + jQuery(this).outerWidth()) - (menu.left + jQuery('#menu_oc').outerWidth());

		if (i > 0) {
			jQuery(this).css('margin-left', '-' + (i + 5) + 'px');
		}
	});
	
    jQuery('.miwoshop #top .dropdown a.dropdown-toggle, .miwoshop #cart a.dropdown-toggle').click(function (e) {
        jQuery('.miwoshop #top .dropdown a.dropdown-toggle, .miwoshop #cart a.dropdown-toggle').parent().removeClass('open')

        var parent = jQuery(this).parent();
        parent.toggleClass('open');

        e.preventDefault();
        return false;
    });


	// Product List
	jQuery('#list-view').click(function() {
		jQuery('#content_oc .product-layout > .clearfix').remove();

		jQuery('#content_oc .product-layout').attr('class', 'product-layout product-list col-xs-12');

		localStorage.setItem('display', 'list');

        fixCartButtonText();
	});

	// Product Grid
	jQuery('#grid-view').click(function() {
		jQuery('#content_oc .product-layout > .clearfix').remove();

		// What a shame bootstrap does not take into account dynamically loaded columns
		cols = jQuery('#column-right, #column-left').length;

		if (cols == 2) {
			jQuery('#content_oc .product-layout').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			jQuery('#content_oc .product-layout').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			jQuery('#content_oc .product-layout').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		 localStorage.setItem('display', 'grid');

        fixCartButtonText();
	});

	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}

	// tooltips on hover
	jQuery('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	jQuery(document).ajaxStop(function() {
		jQuery('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});

    fixCartButtonText();
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=checkout/cart/add&format=raw&tmpl=component',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				//jQuery('#cart > button').button('loading');
			},
			success: function(json) {
				jQuery('.alert, .text-danger').remove();

				//jQuery('#cart > button').button('reset');

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					jQuery('#content_oc').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					jQuery('#cart-total,#cart > a > span').html(json['total']);

					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');

					jQuery('#cart > ul,#module_cart > ul').load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=common/cart/info&format=raw&tmpl=component ul li');
				}
			}
		});
	},
	'update': function(key, quantity) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=checkout/cart/edit&format=raw&tmpl=component',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				//jQuery('#cart > button').button('loading');
			},
			success: function(json) {
				//jQuery('#cart > button').button('reset');

				jQuery('#cart-total,#cart > a > span').html(json['total']);

                var redirect = document.getElementById('remove-redirect');

                if (redirect != null) {
                    location = json['location'];
                } else {
					jQuery('#cart > ul,#module_cart > ul').load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=common/cart/info&format=raw&tmpl=component ul li');
				}
			}
		});
	},
	'remove': function(key) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=checkout/cart/remove&format=raw&tmpl=component',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				//jQuery('#cart > button').button('loading');
			},
			success: function(json) {
				//jQuery('#cart > button').button('reset');

				jQuery('#cart-total,#cart > a > span').html(json['total']);

                var redirect = document.getElementById('remove-redirect');

				if (redirect != null) {
					location = json['location'];
				} else {
					jQuery('#cart > ul,#module_cart > ul').load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=common/cart/info&format=raw&tmpl=component ul li');
				}
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=checkout/cart/remove&format=raw&tmpl=component',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				//jQuery('#cart > button').button('loading');
			},
			complete: function() {
				//jQuery('#cart > button').button('reset');
			},
			success: function(json) {
				jQuery('#cart-total,#cart > a > span').html(json['total']);

                var redirect = document.getElementById('remove-redirect');

                if (redirect != null) {
                    location = json['location'];
                } else {
					jQuery('#cart > ul,#module_cart > ul').load(miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=common/cart/info&format=raw&tmpl=component ul li');
				}
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=account/wishlist/add&format=raw&tmpl=component',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				jQuery('.alert').remove();

				if (json['success']) {
					jQuery('#content_oc').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				if (json['info']) {
					jQuery('#content_oc').parent().before('<div class="alert alert-info"><i class="fa fa-info-circle"></i> ' + json['info'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				jQuery('#wishlist-total').html(json['total']);

				jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		jQuery.ajax({
			url: miwiajaxurl + '?action=miwoshop&option=com_miwoshop&route=product/compare/add&format=raw&tmpl=component',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				jQuery('.alert').remove();

				if (json['success']) {
					jQuery('#content_oc').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					jQuery('#compare-total').html(json['total']);

					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
jQuery(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	jQuery('#modal-agree').remove();

	var element = this;

	jQuery.ajax({
		url: jQuery(element).attr('href') + '&format=raw&tmpl=component',
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + jQuery(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';

			jQuery('body').append(html);

			jQuery('#modal-agree').modal('show');
		}
	});
});

/* Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();
	
			$.extend(this, option);
	
			$(this).attr('autocomplete', 'off');
			
			// Focus
			$(this).on('focus', function() {
				this.request();
			});
			
			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);				
			});
			
			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}				
			});
			
			// Click
			this.click = function(event) {
				event.preventDefault();
	
				value = $(event.target).parent().attr('data-value');
	
				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}
			
			// Show
			this.show = function() {
				var pos = $(this).position();
	
				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});
	
				$(this).siblings('ul.dropdown-menu').show();
			}
			
			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}		
			
			// Request
			this.request = function() {
				clearTimeout(this.timer);
		
				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}
			
			// Response
			this.response = function(json) {
				html = '';
	
				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}
	
					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}
	
					// Get all the ones with a categories
					var category = new Array();
	
					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}
	
							category[json[i]['category']]['item'].push(json[i]);
						}
					}
	
					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';
	
						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}
	
				if (html) {
					this.show();
				} else {
					this.hide();
				}
	
				$(this).siblings('ul.dropdown-menu').html(html);
			}
			
			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));	
			
		});
	}
})(window.jQuery);


