<?php

/**
 * Template Name: Test
 * 
 */

function logg($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function _get_product_option( $product_id ){
    $enable = get_post_meta( $product_id, '_nbo_enable', true );
    if( !$enable ) return false;
    $option_id = get_transient( 'nbo_product_'.$product_id );
    if( false === $option_id ){
        global $wpdb;
        $sql = "SELECT id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM {$wpdb->prefix}nbdesigner_options WHERE published = 1";
        $options = $wpdb->get_results($sql, 'ARRAY_A');
        if($options){
            $_options = array();
            foreach( $options as $option ){
                $execute_option = true;
                $from_date = false;
                if( isset($option['date_from']) ){
                    $from_date = empty( $option['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_from'] ), false ) );
                }
                $to_date = false;
                if( isset($option['date_to']) ){
                    $to_date = empty( $option['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_to'] ), false ) );
                }
                $now  = current_time( 'timestamp' );
                if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                    $execute_option = false;
                } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                    $execute_option = false;
                } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                    $execute_option = false;
                }
                if( $execute_option ){
                    if( $option['apply_for'] == 'p' ){
                        $products = unserialize($option['product_ids']);
                        $execute_option = in_array($product_id, $products) ? true : false;
                    }else {
                        $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
                        $product = wc_get_product($product_id);
                        $product_categories = $product->get_category_ids();
                        $intersect = array_intersect($product_categories, $categories);
                        $execute_option = ( count($intersect) > 0 ) ? true : false;
                    }
                }
                if( $execute_option ){
                    $_options[] = $option;
                }
            }
            $_options = array_reverse( $_options );
            $option_priority = 0;
            foreach( $_options as $_option ){
                if( $_option['priority'] > $option_priority ){
                    $option_priority = $_option['priority'];
                    $option_id = $_option['id'];
                }
            }
            if( $option_id ){
                set_transient( 'nbo_product_'.$product_id , $option_id );
            }
        }
    }
    return $option_id;
}

function _get_option( $id ){
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
    $sql .= " WHERE id = " . esc_sql($id);
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result[0]) ? $result[0] : false;
}

function _get_field_by_id( $option_fields, $field_id ){
    foreach($option_fields['fields'] as $key => $field){
        if( $field['id'] == $field_id ) return $field;
    }
}

function _nb_sort_quantity_breaks($a, $b) {
    if ($a['qty'] == $b['qty']) {
        return 0;
    }
    return ($a['qty'] < $b['qty']) ? -1 : 1;
}
function _get_break_by_qty($quantity, $quantity_breaks) {
    $price = 0;
    $qty = 1;
    if( !empty($quantity_breaks) && count($quantity_breaks) > 0 ) {
        usort($quantity_breaks, "_nb_sort_quantity_breaks" );
        for ($i = 0; $i < count($quantity_breaks); $i++) {
            if ($i === count($quantity_breaks) - 1 && (float)$quantity_breaks[$i]['price'] > 0 ) {
                $price = (float)$quantity_breaks[$i]['price'];
                $qty = (int)$quantity_breaks[$i]['qty'];
                break;
            }
            if ( $quantity >= $quantity_breaks[$i]['qty'] && $quantity < $quantity_breaks[$i + 1]['qty'] && (float)$quantity_breaks[$i]['price'] > 0 ) {
                $price = (float)$quantity_breaks[$i]['price'];
                $qty = (int)$quantity_breaks[$i]['qty'];
                break;
            }
        }
    }
    return array(
        'price' => $price,
        'qty' => $qty,
    );
}

function _calculate_price($cart_item, $item_key, $product_id) {
    $option_id = _get_product_option($product_id);
    $options        = _get_option( $option_id );

    if( nbd_is_base64_string( $options['fields'] ) ){
        $options['fields'] = base64_decode( $options['fields'] );
    }

    $option_fields  = maybe_unserialize( $options['fields'] );
    $nbd_fields = !empty($cart_item['nbo_meta']['option_price']['fields']) ? $cart_item['nbo_meta']['option_price']['fields'] : array();

    $item_combination_options = isset($option_fields['combination']) && isset($option_fields['combination']['options']) ? $option_fields['combination']['options'] : array();

    $fields = maybe_unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;
    if( !empty($item_combination_options) && !empty($nbd_fields) ) {
        foreach($nbd_fields as $key => $val) {
            $_origin_field   = _get_field_by_id( $option_fields, $key );
            if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'area' ) {
                $_area_name = $val['value_name'];
                $area_name = $val['value_name'];
                if( $_area_name == 'Square' || $_area_name == 'Circle' ) {
                    $area_name = 'Square + Circle';
                }
                if( $_area_name == 'Rectangle' || $_area_name == 'Oval' ) {
                    $area_name = 'Rectangle + Oval';
                }
            }
            if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'size' ) {
                $size_name = $val['value_name'];
            }
            if( isset($_origin_field['nbd_type']) && $_origin_field['nbd_type'] == 'color' ) {
                $material_name = $val['value_name'];
            }
        }

        if( isset($area_name) && isset($size_name) && isset($material_name) ) {

        	if(!empty($item_combination_options[$area_name][$size_name][$material_name])) {
        		$option_selected = $item_combination_options[$area_name][$size_name][$material_name];
        	} else if(!empty($item_combination_options['default'])) {
        		$option_selected = $item_combination_options['default'];
        	}
        	if(!empty($option_selected['qty_breaks'])) {
        		$new_price = _get_break_by_qty(1600, $option_selected['qty_breaks'])['price'];
        		$old_price = 0;
        		if(!empty($fields['combination']['combination_selected'])) {
        			$old_price = _get_break_by_qty(1600, $fields['combination']['combination_selected']['qty_breaks'])['price'];
        		}
        		logg($new_price);
        		logg($old_price);
        	}
        }

    }
}

$item_key = '0a30377953efa65781efeb5070417ef9';

$cart_items = WC()->cart->get_cart();
$cart_item = $cart_items[$item_key];
$product_id = $cart_item['product_id'];

_calculate_price($cart_item, $item_key, $product_id);
// $fields = 