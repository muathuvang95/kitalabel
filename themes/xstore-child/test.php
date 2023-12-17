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

function kita_get_field_by_id($fields, $field_id) {
	foreach($fields as $key => $field){
        if( $field['id'] == $field_id ) return $field;
    }
    return false;
}

$item_key = 'ce09f8a4e2cd4d99ee67aa701b42fc4a';

$design_index    = 0;

$passed = false;

if( $item_key ) {
    $cart_items = WC()->cart->get_cart();

    if( isset( $cart_items[$item_key] )) {
        $cart_item = $cart_items[$item_key];
        logg($cart_item['nbo_meta']['option_price']['fields']['f1628323181962']['val']['files']);
        // $nbd_field = $cart_item['nbo_meta']['field'] ;

        // if( isset( $cart_item['nbo_meta'] ) ) {
        //     $fields = unserialize( base64_decode( $cart_item['nbo_meta']['options']['fields']) ) ;

        //     if( isset( $fields['combination'] ) && isset( $fields['combination']['options']) && count($fields['combination']['options']) > 0 ) {
        //         $item_combination = $fields['combination'];
        //         $upload_fields = false;
        //         if( isset( $cart_item['nbo_meta'] ) && isset( $cart_item['nbo_meta']['option_price'] ) && isset( $cart_item['nbo_meta']['option_price']['fields'] ) ) {
        //         	$quantity = $cart_item['quantity'];
        //         	$sum_qty = 0;
		//             foreach($cart_item['nbo_meta']['option_price']['fields'] as $key => $val)  {
		//                 if(isset($val['is_custom_upload']) && $val['is_custom_upload'] == 1) {
		//                 	$files = $val['value_name']['files'];
		//                 	if(isset($files[$design_index])) {
		//                 		$file_data = explode('/', $files[$design_index]);
		//                 		if(count($file_data) == 2) {
		//                 			$folder_upload = $file_data[0];
		//                 		}
		//                 	}
		//                 	logg($files);
		//                 }
		// 		    }

	    //             // if( $passed ) {
		//             //     WC()->cart->cart_contents[ $item_key ] = $cart_item;
		//             //     WC()->cart->cart_contents[ $item_key ]['nbo_meta']['field'] = $nbd_field;
		//             //     WC()->cart->set_quantity( $item_key, $sum_qty );
		//             //     WC()->cart->set_session();
		// 	        // }
		//         }
        //     }
        // }
        $cart_item['nbo_meta']['option_price']['fields']['f1628323181962']['val']['files'][1] = 'c4ca4238a0b923820dcc509a6f75849b/1702575433fd9042c9.pdf';
        $cart_item['nbo_meta']['option_price']['fields']['f1628323181962']['value_name']['files'][1] = 'c4ca4238a0b923820dcc509a6f75849b/1702575433fd9042c9.pdf';
        $cart_item['nbo_meta']['field']['f1628323181962']['files'][1] = 'c4ca4238a0b923820dcc509a6f75849b/1702575433fd9042c9.pdf';
    
        $cart_content = WC()->cart->cart_contents;
        $cart_content[$item_key] = $cart_item;
        logg($cart_content);
        WC()->cart->set_cart_contents($cart_content);
    }
}
