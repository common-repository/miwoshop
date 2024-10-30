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

jQuery(window).on('resize', function () {
   if(window.innerWidth < 768) {
       jQuery('#header_oc').removeClass('wide');
       jQuery('#column-left').removeClass('active');
       jQuery('#header_oc').addClass('short');
   }
   else{
       if (localStorage.getItem('column-left') == 'active') {
           jQuery('#header_oc').addClass('wide');
           jQuery('#column-left').addClass('active');
           jQuery('#header_oc').removeClass('short');
       }
   }
});

jQuery(document).ready(function() {
	//Form Submit for IE Browser
	jQuery('button[type=\'submit\']').on('click', function() {
		jQuery("form[id*='form-']").submit();
	});

	// Highlight any found errors
	jQuery('.text-danger').each(function() {
		var element = jQuery(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Set last page opened on the menu
	jQuery('#menu_oc a[href]').on('click', function() {
		sessionStorage.setItem('menu', jQuery(this).attr('href'));
	});

	if (!sessionStorage.getItem('menu')) {
		jQuery('#menu_oc #dashboard').addClass('active');
	} else {
		// Sets active and open to selected page in the left column menu.
		jQuery('#menu_oc a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li').addClass('active open');
	}

	if (localStorage.getItem('column-left') == 'active') {
		jQuery('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');

		jQuery('#column-left').addClass('active');
        jQuery('#header_oc').addClass('wide');

		// Slide Down Menu
		jQuery('#menu_oc li.active').has('ul').children('ul').addClass('collapse in');
		jQuery('#menu_oc li').not('.active').has('ul').children('ul').addClass('collapse');
	} else {
        jQuery('#header_oc').addClass('short');

		jQuery('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');
		
		jQuery('#menu_oc li li.active').has('ul').children('ul').addClass('collapse in');
		jQuery('#menu_oc li li').not('.active').has('ul').children('ul').addClass('collapse');
	}

	// Menu button
	jQuery('#button-menu').on('click', function() {
		// Checks if the left column is active or not.
		if (jQuery('#column-left').hasClass('active')) {
			localStorage.setItem('column-left', '');

			jQuery('#button-menu i').replaceWith('<i class="fa fa-indent fa-lg"></i>');

			jQuery('#column-left').removeClass('active');
            jQuery('#header_oc').addClass('short');
            jQuery('#header_oc').removeClass('wide');


			jQuery('#menu_oc > li > ul').removeClass('in collapse');
			jQuery('#menu_oc > li > ul').removeAttr('style');
		} else {
			localStorage.setItem('column-left', 'active');

			jQuery('#button-menu i').replaceWith('<i class="fa fa-dedent fa-lg"></i>');


			jQuery('#column-left').addClass('active');
            jQuery('#header_oc').addClass('wide');
            jQuery('#header_oc').removeClass('short');


			// Add the slide down to open menu items
			jQuery('#menu_oc li.open').has('ul').children('ul').addClass('collapse in');
			jQuery('#menu_oc li').not('.open').has('ul').children('ul').addClass('collapse');
		}
	});

	// Menu
	jQuery('#menu_oc').find('li').has('ul').children('a').on('click', function() {
		if (jQuery('#column-left').hasClass('active') || !jQuery(this).parent().parent().is('#menu_oc')) {
			jQuery(this).parent('li').toggleClass('open');

            if(jQuery(this).parent('li').children('ul.collapse').hasClass('in')) {
                jQuery(this).parent('li').children('ul.collapse').removeClass('in');
            }
            else{
                jQuery(this).parent('li').children('ul.collapse').addClass('in');
            }

			jQuery(this).parent('li').siblings().removeClass('open').children('ul.collapse').removeClass('in');

        }
	});
	
	// Override summernotes image manager
	jQuery('button[data-event=\'showImageDialog\']').attr('data-toggle', 'image').removeAttr('data-event');
	
	jQuery(document).delegate('button[data-toggle=\'image\']', 'click', function() {
		jQuery('#modal-image').remove();
		
		jQuery(this).parents('.note-editor').find('.note-editable').focus();
				
		jQuery.ajax({
			url: 'admin-ajax.php?action=miwoshop&client=admin&option=com_miwoshop&route=common/filemanager&format=raw&tmpl=component&token=' + getURLVar('token'),
			dataType: 'html',
			beforeSend: function() {
				jQuery('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				jQuery('#button-image').prop('disabled', true);
			},
			complete: function() {
				jQuery('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
				jQuery('#button-image').prop('disabled', false);
			},
			success: function(html) {
				jQuery('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                var a = jQuery('#modal-image');

				jQuery('#modal-image').modal('show');
			}
		});	
	});

	// Image Manager
	jQuery(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();
	
		var element = this;

		jQuery(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" id="button-image" class="button button-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="button btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});
		
		jQuery(element).popover('toggle');		
	
		jQuery('#button-image').on('click', function() {
			jQuery('#modal-image').remove();
	
			jQuery.ajax({
				url: 'admin-ajax.php?action=miwoshop&client=admin&option=com_miwoshop&route=common/filemanager&format=raw&tmpl=component&token=' + getURLVar('token') + '&target=' + jQuery(element).parent().find('input').attr('id') + '&thumb=' + jQuery(element).attr('id'),
				dataType: 'html',
				beforeSend: function() {
					jQuery('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					jQuery('#button-image').prop('disabled', true);
				},
				complete: function() {
					jQuery('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
					jQuery('#button-image').prop('disabled', false);
				},
				success: function(html) {
					jQuery('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                    var a = jQuery('#modal-image');

					jQuery('#modal-image').modal('show');
				}
			});
	
			jQuery(element).popover('hide');
		});
	
		jQuery('#button-clear').on('click', function() {
			jQuery(element).find('img').attr('src', jQuery(element).find('img').attr('data-placeholder'));
			
			jQuery(element).parent().find('input').attr('value', '');
	
			jQuery(element).popover('hide');
		});
	});
	
	// tooltips on hover
	jQuery('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});

	// Makes tooltips work on ajax generated content
	jQuery(document).ajaxStop(function() {
		jQuery('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});	
});

// Autocomplete */
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
                        if(category[i].constructor !== Array) {
                            break;
                        }

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
