<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-custom-field-header">
    <label for='nbd-field-<?php echo $field['id']; ?>'>
        <?php echo $field['general']['title']; ?>
        <?php if( $field['general']['required'] == 'y' ): ?>
        <span class="nbd-required">*</span>
        <?php endif; ?>
    </label> 
    <div class="number-options">
        <div class="number">
            {{nbd_fields['<?php echo $field['id']; ?>'].number_op}}
        </div>
    </div>
</div>