<!-- <?php if (!defined('ABSPATH')) exit; ?>
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
</div> -->
<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-header" style="background: none">
    <span>
        <?php
        if(isset($field['nbd_type']) && $field['nbd_type'] == 'size') {
            echo '<img style="max-height: 24px" src="'. CUSTOM_KITALABEL_URL . 'templates/options-builder/icons/size.svg' .'">';
        } else if(isset($field['nbd_type']) && ($field['nbd_type'] == 'area' || $field['nbd_type'] == 'color') ) {
            echo '<img style="max-height: 24px" src="'. CUSTOM_KITALABEL_URL . 'templates/options-builder/icons/vector.svg' .'">';
        } else if(!isset($field['nbd_type']) ) {
            echo '<img style="max-height: 24px" src="'. CUSTOM_KITALABEL_URL . 'templates/options-builder/icons/vector.svg' .'">';
        }
        ?>
    </span>
    <label for='nbd-field-<?php echo $field['id']; ?>'>
        <?php echo $field['general']['title']; ?>
        <?php if( $field['general']['required'] == 'y' ): ?>
        <span class="nbd-required">*</span>
        <?php endif; ?>
    </label> 
</div>