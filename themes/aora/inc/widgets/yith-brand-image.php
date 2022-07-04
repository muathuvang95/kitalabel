<?php
if( !defined('TBAY_ELEMENTOR_ACTIVED') ) return;

class Tbay_Widget_Yith_Brand_Images extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'aora_product_brand',
            esc_html__('Aora Product Brand Images', 'aora'),
            array( 'description' => esc_html__( 'Show YITH product brand images(Only applicable to product single pages)', 'aora' ), )
        );
        $this->widgetName = 'aora_product_brand';
    }
 
    public function getTemplate() {
        $this->template = 'product-brand-image.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {


    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();

        return $instance;

    }
}