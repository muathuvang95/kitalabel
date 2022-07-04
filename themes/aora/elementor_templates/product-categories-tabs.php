<?php 
/**
 * Templates Name: Elementor
 * Widget: Product Categories Tabs
 */

extract( $settings );

// if( empty($categories) ) return;

$this->settings_layout();

?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    <?php 
        $this->render_element_heading(); 
        
        $this->render_tabs_title($categories_tabs);
        $this->render_product_tabs_content($categories_tabs);
        
        
        
        $this->render_item_button();
    ?>
</div>