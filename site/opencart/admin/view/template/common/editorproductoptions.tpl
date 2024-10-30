<div class="options">
    <table>
        <tr>
            <td><b>Name</b></td>
            <td><b>Show</b></td>
        </tr>
        <?php foreach ($options as $option) { ?>
        <tr>
            <td>
                <?php if ($option['required']) { ?>
                <span class="required">*</span>
                <?php } ?>
                <b><?php echo $option['name']; ?>:</b></td>
            <td>
                <input type="checkbox" name="option_oc[<?php echo $option['product_option_id']; ?>]" id="show_option_oc[<?php echo $option['product_option_id']; ?>]">
            </td>
        </tr>
        <?php } ?>
    </table>
</div>