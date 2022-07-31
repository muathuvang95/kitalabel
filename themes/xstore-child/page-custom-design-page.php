<?php
/**
* Template Name: Custom design

*/
get_header();
echo '<div class="container">';

_nb_show_option_fields();

woocommerce_output_product_data_tabs();

echo '</div>';
get_footer();