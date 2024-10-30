<?php if ($manufacturers) { ?>
<h3><?php echo $heading_title; ?></h3>
<div class="">
<select class="miwoshop_manufacturer" onchange="location=this.options[this.selectedIndex].value;">
  <option value=""><?php echo $text_select; ?></option>
  <?php foreach ($manufacturers as $manufacturer) { ?>
  <option value="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></option>
  <?php } ?>
</select>
</div>
<?php } ?>
