<?php
require_once 'Mobile_Detect.php';
if ( ! defined( 'ABSPATH' ) || function_exists('Aora_Elementor_Templae_Kita') ) {
    exit; // Exit if accessed directly.
}
use Elementor\Controls_Manager;
class Aora_Elementor_Templae_Kita extends Aora_Elementor_Carousel_Base {

    public function get_name() {
        return 'template-kita';
    }

    public function get_title() {
        return __( 'Template Kita', 'aora' );
    }

    public function get_icon() {
        return 'eicon-products';
    }
    /**
     * Retrieve the list of scripts the image carousel widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.3.0
     * @access public
     *
     * @return array Widget scripts dependencies.
    */  
    public function get_categories() {
        return [ 'aora-elements', 'woocommerce-elements'];
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'general',
            [
                'label' => esc_html__( 'General', 'aora' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Number of products', 'aora'),
                'type' => Controls_Manager::NUMBER,
                'description' => esc_html__( 'Number of products to show ( -1 = all )', 'aora' ),
                'default' => 8,
                'min'  => -1
            ]
        );
        $this->add_control(
            'advanced',
            [
                'label' => esc_html__('Advanced', 'aora'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'aora'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'grid',
                'options'   => [
                    'grid'      => esc_html__('Grid', 'aora'), 
                    'carousel'  => esc_html__('Carousel', 'aora'), 
                ],
            ]
        );
        $this->end_controls_section();
        $this->add_control_responsive();
    }



    protected function render() {
        $detect = new Mobile_Detect;
        $settings           = $this->get_settings_for_display();
        $limit              = $settings['limit'];
        $layoutType         = $settings['layout_type'];
        $columns            = $settings['column'];
        $col_desktop        = $settings['col_desktop'];
        $col_desktopsmall   = $settings['col_desktopsmall'];
        $col_landscape      = $settings ['col_landscape'];
        $html = '';
        $atts = array();
        extract(
            shortcode_atts(
            array(
                'accordionstyle' => 'style1',
                'num_template_design' => $limit,
                'title_template_design' => '',
                'description_template_design' => '',
                'template_per_view' => 2,
                'color_arrow' => '#CC7272',
                'padding_template_design' => '0px',
                'template_direction' => 'DESC',
            ),
            $atts
            )
        );
    global $wpdb;
    if ( is_plugin_active('web-to-print-online-designer/nbdesigner.php') ) {
        $sql = "SELECT p.ID, p.post_title, t.id AS tid, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail FROM {$wpdb->prefix}nbdesigner_templates AS t";     
        $sql .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
        $sql .= " WHERE t.publish = 1 AND p.post_status = 'publish' AND publish = 1"; 
        $sql .= " ORDER BY t.created_date ".$template_direction;
        $sql .= " LIMIT ".$num_template_design;
        $posts = $wpdb->get_results($sql, 'ARRAY_A');
        $listTemplates = array();
        
        foreach ($posts as $p) {
            $path_preview = NBDESIGNER_CUSTOMER_DIR .'/'.$p['folder']. '/preview';
            if( $p['thumbnail'] ){
                $image = wp_get_attachment_url( $p['thumbnail'] );
            }else{
                $listThumb = Nbdesigner_IO::get_list_images($path_preview);
                $image = '';
                if(count($listThumb)){
                $image = Nbdesigner_IO::wp_convert_path_to_url(reset($listThumb));
                }                
            }
            $title = $p['name'] ?  $p['name'] : $p['post_title'];
            $listTemplates[] = array('tid' => $p['tid'], 'id' => $p['ID'], 'title' => $title, 'image' => $image, 'folder' => $p['folder'], 'product_id' => $p['product_id'], 'variation_id' => $p['variation_id'], 'user_id' => $p['user_id']);
        }
        $html.='<div class="template-online-design">';
        if($accordionstyle=="style1") {
            if($title_template_design){
                $html.='<h3>'.$title_template_design.'</h3>';
            }
            $html.='<div>';
            if($description_template_design){
                $html.='<div class="col-xs-12 col-md-12 col-lg-8 des">'.$description_template_design.'</div>';
                $html.='<div class="col-xs-12 col-md-12 col-lg-4">';
            }else{
                $html.='<div class="col-xs-12 col-md-12">';
            }
            $html.='</div>';
            if(count($listTemplates)>0) {
                $UrlPageNBD = getUrlPageNBD('create');
                $templatess = '';
                $item = '';
                
                // layout type 
                if ($layoutType == 'carousel') {
                    $templatess.='<div class="owl-carousel slick-slider" id="template-kita">';
                    $item.= '<div class="item">';
                } else if ($layoutType == 'grid'){
                    $templatess.='<div class="list-item-kita" style="width">';
                    // check Mobile Detect 
                    if ($detect->isTablet()) {
                        $item.= '<div class="item" style="width: '. 100/$col_desktopsmall.'%">';
                    } else if ($detect->isMobile()) {
                        $item.= '<div class="item" style="width: '. 100/$col_landscape.'%">';
                    } else {
                        $item.= '<div class="item" style="width: '. 100/$columns.'%">';
                    }
                }
                $html.= $templatess;
                    foreach ($listTemplates as $key => $temp) {
                        $link_template = add_query_arg(array(
                            'product_id' => $temp['product_id'],
                            'variation_id' => $temp['variation_id'],
                            'reference'  =>  $temp['folder']
                        ), $UrlPageNBD);
                        
                        $html.= $item;
                        $html.='<a href="'.$link_template.'" class="thumbnail">';
                        $html.='<img src="'.$temp['image'].'" alt="'.$temp['title'].'">';
                        $html.='</a>';
                        $html.='</div>';
                    }
                $html.='</div>';
            }
            $html.='</div>';
            } else {
                $html.='<div class="vc-printshop-template-online">';
                $html.='<div class="swiper-container vc-template-od">';
                $html.='<div class="swiper-wrapper" data-per="'.$template_per_view.'" data-color="'.$color_arrow.'">';
                if(count($listTemplates)>0) {
                    $UrlPageNBD = getUrlPageNBD('create');
                    foreach ($listTemplates as $key => $temp) {
                    $link_template = add_query_arg(array(
                        'product_id' => $temp['product_id'],
                        'variation_id' => $temp['variation_id'],
                        'reference'  =>  $temp['folder']
                    ), $UrlPageNBD);
                    $html.='<div class="swiper-slide">';
                    $html.='<a href="'.$link_template.'" class="thumbnail" style="padding: '.$padding_template_design.';">';
                    $html.='<img src="'.$temp['image'].'" alt="'.$temp['title'].'">';
                    $html.='</a>';
                    $html.='</div>';
                    }
                }
                $html.='</div>';
                // $html.='<div class="swiper-pagination"></div>';
                $html.='<div class="wrap-swiper-button-next"><div class="swiper-button-next-2"></div></div>';
                $html.='<div class="wrap-swiper-button-prev"><div class="swiper-button-prev-2"></div></div>';
                $html.='</div>';
                $html.='</div>';
            }
            $html.='</div>';
            }
            $slideToShow = (int)$col_landscape;
            $html.= '<script src="'.get_template_directory_uri().'/js/custom-slick.js"></script>';
            $html.= '<script src="'.get_template_directory_uri().'/js/slick.min.js"></script>';
            $html.= "<script>jQuery('.slick-slider').slick({
                dots: true,
                infinite: false,
                slidesToShow:  $slideToShow,
                slidesToScroll: 1,
                margin: 0,
                padding: 0,
            })</script>";
        echo $html;
    }
}
$widgets_manager->register_widget_type(new Aora_Elementor_Templae_Kita());

