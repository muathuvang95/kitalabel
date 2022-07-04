<?php
if ( !function_exists( 'aora_tbay_post_metaboxes' ) ) {
    function aora_tbay_post_metaboxes(){
        $prefix = 'tbay_post_';

        $cmb2 = new_cmb2_box( array(
            'id'                        => 'post_format_standard_post_meta',
            'title'                     => esc_html__( 'Format Setting', 'aora' ),
            'object_types'              => array( 'post' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'autosave'                  => true,
        ) );

        $cmb2->add_field( array(
            'id'   => "{$prefix}gallery_files",
            'name' => esc_html__( 'Images Gallery', 'aora' ),
            'type' => 'file_list',
        ) );

        $cmb2->add_field( array(
            'id'   => "{$prefix}video_link",
            'name' => esc_html__( 'Video Link', 'aora' ),
            'type' => 'oembed',
        ) );

        $cmb2->add_field( array(
            'id'   => "{$prefix}audio_link",
            'name' => esc_html__( 'Audio Link', 'aora' ),
            'type' => 'oembed',
        ) );

    }
    add_action( 'cmb2_admin_init', 'aora_tbay_post_metaboxes', 20 );
}

if (!function_exists('aora_tbay_standard_post_meta')) {
    function aora_tbay_standard_post_meta($post_id)
    {
        global $post;
        $prefix = 'tbay_post_';
        $type = get_post_format();

        $old = array(
            'gallery_files',
            'video_link',
            'link_text',
            'link_link',
            'audio_link',
        );
        
        $data = array( 'gallery' => array('gallery_files'),
                    'video' =>  array('video_link'),
                    'audio' =>  array('audio_link'));

        $new = array();

        if (isset($data[$type])) {
            foreach ($data[$type] as $key => $value) {
                $new[$prefix.$value] = $_POST[$prefix.$value];
            }
        }


        foreach ($old as $key => $value) {
            if (isset($_POST[$prefix.$value])) {
                unset($_POST[$prefix.$value]);
            }
        }
        if ($new) {
            $_POST = array_merge($_POST, $new);
        }
    }
    add_action("cmb2_meta_post_format_standard_post_meta_before_save_post", 'aora_tbay_standard_post_meta', 9);
}