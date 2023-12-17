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

$order_id = 14060;
echo basename('http://kitalabel.loc/wp-content/uploads/nbdesigner/uploads/c4ca4238a0b923820dcc509a6f75849b/1702833312649a066d.png');
// $fields = 