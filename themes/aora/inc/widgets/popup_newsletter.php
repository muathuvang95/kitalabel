<?php
if( !defined('TBAY_ELEMENTOR_ACTIVED') ) return;

class Tbay_Widget_Popup_Newsletter extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'aora_popup_newsletter',
            esc_html__('Aora Popup Newsletter', 'aora'),
            array( 'description' => esc_html__( 'Show Popup Newsletter', 'aora' ), )
        );
        $this->widgetName = 'popup_newsletter';
        add_action('admin_enqueue_scripts', array($this, 'scripts'));
    }
    
    public function scripts() {
        $suffix = (aora_tbay_get_config('minified_js', false)) ? '.min' : AORA_MIN_JS;
        wp_enqueue_script( 'tbay-upload', TBAY_ELEMENTOR_URL . 'assets/upload.js', array( 'jquery', 'wp-pointer' ), TBAY_ELEMENTOR_VERSION, true );
        wp_enqueue_script( 'aora-admin', AORA_SCRIPTS . '/admin/admin' . $suffix . '.js', array( 'jquery' ), TBAY_ELEMENTOR_VERSION, true );
    }

    public function getTemplate() {
        $this->template = 'popup-newsletter.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $list_socials = array(
            'facebook'      => 'Facebook',
            'twitter'       => 'Twitter',
            'youtube-play'  => 'Youtube',
            'pinterest'     => 'Pinterest',
            'linkedin'      => 'LinkedIn'
        );
        $defaults = array(
            'title' => 'Newsletter', 
            'description' => "Put your content here", 
            'message' => esc_html__('Message Close', 'aora'), 
            'image' => '', 
            'socials' => array());
        $instance = wp_parse_args( (array) $instance, $defaults );
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><strong><?php esc_html_e('Title:', 'aora');?></strong></label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr( $instance['title'] ) ; ?>" class="widefat" />
        </p>
                

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e( 'Description:', 'aora' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>"  cols="20" rows="3"><?php echo trim( $instance['description'] ) ; ?></textarea>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'message' )); ?>"><?php esc_html_e( 'Message Close:', 'aora' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id( 'message' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'message' )); ?>"  cols="20" rows="3"><?php echo trim( $instance['message'] ) ; ?></textarea>
        </p>

        <label for="<?php echo esc_attr($this->get_field_id( 'image' )); ?>"><?php esc_html_e( 'Image:', 'aora' ); ?></label>
        <div class="screenshot">
            <?php if ( isset($instance['image']) && $instance['image'] ) { ?>
                <img src="<?php echo esc_url($instance['image']); ?>">
            <?php } ?>
        </div>
        <input class="widefat upload_image" id="<?php echo esc_attr($this->get_field_id( 'image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'image' )); ?>" type="hidden" value="<?php echo esc_attr($instance['image']); ?>" />
        <div class="upload_image_action">
            <input type="button" class="button add-image" value="Add">
            <input type="button" class="button remove-image" value="Remove">
        </div>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'socials' )); ?>"><?php esc_html_e('Select socials:','aora'); ?></label>
            <br>
        <?php
            foreach ($list_socials as $key => $value):
                $checked = (isset($instance['socials'][$key]['status']) && ($instance['socials'][$key]['status'])) ? 1: 0;
                $link = (isset($instance['socials'][$key]['page_url'])) ? $instance['socials'][$key]['page_url']: '';
            ?>
                <p>
                <input class="checkbox" type="checkbox" <?php checked( $checked, 1 ); ?> id="<?php echo esc_attr( $key ); ?>"
                    name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][status]" />
                    <label for="<?php echo esc_attr($this->get_field_name('socials') ); ?>[<?php echo esc_attr( $key ); ?>][status]">
                        <?php echo esc_html__('Show ','aora').esc_html( $value ); ?>
                    </label>
                <input type="hidden" name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][name]" value=<?php echo esc_attr( $value ); ?> />
                </p>

                <?php 
                   $check_value =  ($checked)? 'block': 'none';
                ?>
                <p style="display: <?php echo trim($check_value); ?>" id="<?php echo esc_attr($this->get_field_id($key)); ?>" class="text_url <?php echo esc_attr( $key ); ?>">
                    <label for="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]">
                        <?php echo esc_html( $value ).' '.esc_html__('Page URL:','aora').' '; ?>
                    </label>
                    <input class="widefat" type="text"
                        id="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]"
                        name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]"
                        value="<?php echo esc_attr($link); ?>"
                    />
                </p>
            <?php endforeach; ?>
        </p>
        
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance                   = $old_instance;
        $instance['title']          = ( ! empty( $new_instance['title'] ) ) ? ( $new_instance['title'] ) : '';
        $instance['description']    = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['message']        = ( ! empty( $new_instance['message'] ) ) ? strip_tags( $new_instance['message'] ) : '';
        $instance['image']          = ( ! empty( $new_instance['image'] ) ) ? strip_tags( $new_instance['image'] ) : '';
        $instance['socials']        = $new_instance['socials'];
        return $instance;

    }
}