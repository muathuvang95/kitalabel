<?php if (!defined('ABSPATH')) exit; ?>

<div class="nbd-field-header" style="background: none">
    <label for='nbd-field-<?php echo $field['id']; ?>'>
        <?php echo $field['general']['title']; ?>
        <?php if( $field['general']['required'] == 'y' ): ?>
        <span class="nbd-required">*</span>
        <?php endif; ?>
    </label> 
</div>