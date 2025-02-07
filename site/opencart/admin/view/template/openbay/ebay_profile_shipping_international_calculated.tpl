<input type="hidden" value="<?php echo (int)$data['international']['calculated']['count']; ?>" id="calculated_count_international" />
<?php if (isset($data['international']['calculated']['service_id'])) { ?>
<?php foreach($data['international']['calculated']['service_id'] as $key => $service){ ?>
<div class="well" id="international_calculated_<?php echo $key; ?>">
  <div class="row form-group">
    <div class="col-sm-1 text-right"><label class="control-label"><?php echo $text_shipping_service; ?></label></div>
    <div class="col-sm-11">
      <select name="data[international][calculated][service_id][<?php echo $key; ?>]" class="form-control">
        <?php foreach($data['international']['flat']['types']['service'] as $service_key => $service_type) { ?>
        <option value="<?php echo $service_key; ?>" <?php if ($service_key == $service) { echo ' selected'; } ?>><?php echo $service_type['description']; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="row form-group">
    <div class="col-sm-1 text-right">
      <label class="control-label"><?php echo $text_shipping_zones; ?></label>
    </div>
    <div class="col-sm-10">
      <label class="checkbox-inline">
        <input type="checkbox" name="data[international][calculated][shipto][<?php echo $key; ?>][]" value="Worldwide" <?php echo isset($data['international']['calculated']['shipto'][$key]) && in_array('Worldwide', $data['international']['calculated']['shipto'][$key]) ? 'checked="checked"' : '' ?> /> <?php echo $text_shipping_worldwide; ?>
      </label>
      <?php foreach($zones as $zone) { ?>
      <label class="checkbox-inline">
        <input type="checkbox" name="data[international][calculated][shipto][<?php echo $key; ?>][]" value="<?php echo $zone['shipping_location']; ?>" <?php echo isset($data['international']['calculated']['shipto'][$key]) && in_array($zone['shipping_location'], $data['international']['calculated']['shipto'][$key]) ? 'checked="checked"' : ''; ?> /> <?php echo $zone['description']; ?>
      </label>
      <?php } ?>
    </div>
  </div>
  <div class="row form-group">
    <div class="col-sm-3 pull-right text-right">
      <a onclick="removeShipping('international','<?php echo $key; ?>', 'calculated');" class="button btn-danger"><i class="fa fa-minus-circle"></i> <?php echo $button_delete; ?></a>
    </div>
  </div>
</div>
<?php } ?>
<?php } ?>