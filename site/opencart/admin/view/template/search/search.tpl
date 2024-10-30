<form id="miwoshop-search" class="navbar-form" role="search">
  <div class="input-group">
    <div class="input-group-btn">
      <a class="button btn-default dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="glyphicon glyphicon-search"></i>
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-left alerts-dropdown">
        <li class="dropdown-header"><?php echo $text_search_options; ?></li>
        <li><a onclick="setOption('catalog', '<?php echo $text_catalog_placeholder; ?>'); return false;"><i class="fa fa-book"></i><span><?php echo $text_catalog; ?></span></a></li>
        <li><a onclick="setOption('customers', '<?php echo $text_customers_placeholder; ?>'); return false;"><i class="fa fa-group"></i><span><?php echo $text_customers; ?></span></a></li>
        <li><a onclick="setOption('orders', '<?php echo $text_orders_placeholder; ?>'); return false;"><i class="fa fa-credit-card"></i><span><?php echo $text_orders; ?></span></a></li>
      </ul>
    </div>
    <input id="miwoshop-search-input" type="text" class="form-control" placeholder="Search" name="query" autocomplete="off">
    <input id="miwoshop-search-option" type="hidden" name="search-option" value="catalog">
    <div id="loader-search"><img src="view/image/loader-search.gif"></div>
  </div>
</form>
<div id="miwoshop-search-result"></div>

<script type="text/javascript">
    function setOption(option, text) {
        jQuery('#miwoshop-search-option').val(option);
        jQuery('#miwoshop-search-input').attr('placeholder', text);
    }

    jQuery('#miwoshop-search-input').keyup(function(){
        var option = jQuery('#miwoshop-search-option').val();
        var length = 3;

        if(option == 'orders') {
            length = 1;
        }

        if(this.value.length < length) {
            return false;
        }

        if(jQuery.support.leadingWhitespace == false) {
              return false;
        }

        jQuery('#loader-search').css('display', 'block');

        jQuery.ajax({
            type: 'get',
            url: 'index.php?route=search/search/search',
            data: jQuery('#miwoshop-search').serialize(),
            dataType: 'json',
            success:function(json){
                jQuery('#miwoshop-search-result').css('display', 'block');
                jQuery('#loader-search').css('display', 'none');

                if(json['error']) {
                    jQuery('#miwoshop-search-result').html(json['error'])
                    return;
                }

                jQuery('#miwoshop-search-result').html(json['result'])
            }
        });
    });

    jQuery(document).mouseup(function (e) {
        var container = jQuery('#miwoshop-search-result');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
	
	jQuery('#miwoshop-search').submit(function(e) {
		e.preventDefault();
	});
</script>