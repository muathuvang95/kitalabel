<?php
/**
* Template Name: Order label page

*/
get_header();

echo '<div class="container">';

Kitalabel_Order_Label::instance()->option_fields();

woocommerce_output_product_data_tabs();

echo '</div>';

get_footer();