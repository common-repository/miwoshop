<style type="text/css">
    select{
        width: 100% !important;
    }
</style>

<?php if ($show_box) { ?>
<div class="box_oc">
<?php } ?>

<?php if ($show_heading) { ?>
<div class="box-heading"><?php echo $heading_title; ?></div>
<?php } ?>

<div class="box-content">
	<div class="box-product" style="margin: 2px;">
		<div style="margin-bottom: 5px; margin-right: 0px;">
			<?php if ($product['thumb']) { ?>
				<div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
			<?php } ?>

            <?php if ($product['name']) { ?>
			    <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
            <?php } ?>

			<?php if ($product['price']) { ?>
				<div class="price">
				    <?php if (!$product['special']) { ?>
				        <?php echo $product['price']; ?>
				    <?php } else { ?>
				        <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
				    <?php } ?>
				</div>
			<?php } ?>

            <?php if ($product['options']) { ?>
                <div class="options">
                    <?php foreach ($options as $option) { ?>
                        <?php if ($option['type'] == 'select') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>">
                                    <?php if ($option['required']) { ?>
                                        <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <select class="option" name="option_oc[<?php echo $option['product_option_id']; ?>]">
                                        <option value=""><?php echo $text_select; ?></option>
                                        <?php foreach ($option['option_value'] as $option_value) { ?>
                                            <option value="<?php echo $option_value['product_option_value_id']; ?>">
                                                <?php echo $option_value['name']; ?>
                                                <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                <?php } ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'radio') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" >
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <?php foreach ($option['option_value'] as $option_value) { ?>
                                    <input class="option" type="radio" name="option_oc[<?php echo $option['product_option_id']; ?>]"
                                           value="<?php echo $option_value['product_option_value_id']; ?>"
                                           id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                                    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                        <?php if ($option_value['price']) { ?>
                                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                        <?php } ?>
                                    </label>
                                    <br/>
                                    <?php } ?>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'checkbox') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" >
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <?php foreach ($option['option_value'] as $option_value) { ?>
                                    <input class="option" type="checkbox" name="option_oc[<?php echo $option['product_option_id']; ?>][]"
                                           value="<?php echo $option_value['product_option_value_id']; ?>"
                                           id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                                    <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                        <?php if ($option_value['price']) { ?>
                                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                        <?php } ?>
                                    </label>
                                    <br/>
                                    <?php } ?>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'image') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" >
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <table class="option-image">
                                        <?php foreach ($option['option_value'] as $option_value) { ?>
                                        <tr>
                                            <td style="width: 1px;">
                                                <input class="option" type="radio" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                                            </td>
                                            <td>
                                                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img  src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>"/></label>
                                            </td>
                                            <td>
                                                <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                                    <?php if ($option_value['price']) { ?>
                                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                                    <?php } ?>
                                                </label>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'text') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" >
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <input class="option" type="text" name="option_oc[<?php echo $option['product_option_id']; ?>]"
                                           value="<?php echo $option['option_value']; ?>"/>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'textarea') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" >
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <textarea class="option" name="option_oc[<?php echo $option['product_option_id']; ?>]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'file') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <input type="button" value="<?php echo $button_upload; ?>"
                                           id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
                                    <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value=""/>
                                </div>
                                <br/>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'date') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <input type="text" name="option_oc[<?php echo $option['product_option_id']; ?>]"
                                           value="<?php echo $option['option_value']; ?>" class="date"/>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'datetime') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <input type="text" name="option_oc[<?php echo $option['product_option_id']; ?>]"
                                           value="<?php echo $option['option_value']; ?>" class="datetime"/>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                        <?php if ($option['type'] == 'time') { ?>
                            <?php if ($option['show'] == '1') { ?>
                                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                                    <?php if ($option['required']) { ?>
                                    <span class="required">*</span>
                                    <?php } ?>
                                    <b><?php echo $option['name']; ?>:</b><br/>
                                    <input type="text" name="option_oc[<?php echo $option['product_option_id']; ?>]"
                                           value="<?php echo $option['option_value']; ?>" class="time"/>
                                </div>
                                <br/>
                            <?php } else { ?>
                                <input type="hidden" name="option_oc[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['default_value'] ?>" />
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>

			<?php if ($product['rating']) { ?>
				<div class="rating"><img src="<?php echo MURL_MIWOSHOP; ?>/site/opencart/catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
			<?php } ?>

            <?php if ($product['button']) { ?>
			    <div class="cart"><a onclick="addProductToCart('<?php echo $product['product_id']; ?>', '<?php echo count($product['options']); ?>');" class="<?php echo MiwoShop::getButton(); ?>"><span><?php echo $button_cart; ?></span></a></div>
            <?php } ?>
        </div>
	</div>
</div>

<?php if ($show_box) { ?>
</div>
<?php } ?>
