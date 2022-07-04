<?php

get_header();
$sidebar_configs = aora_tbay_get_page_layout_configs();

$class_row = ( get_post_meta( $post->ID, 'tbay_page_layout', true ) === 'main-right' ) ? 'flex-row-reverse' : '';

aora_tbay_render_breadcrumbs();
$product_id = isset( $_GET['product_id'] ) ? $_GET['product_id'] : 0;
if( $product_id == 0) {
	get_template_part( 404 ); exit();
}
$args = array(
	'post_type' => 'product',
	'p'			=> $product_id,
);
$the_query = new WP_Query( $args );
wp_reset_postdata();

$area_val 		= isset( $_GET['area'] ) ? $_GET['area'] : 0;
$area_id 		= 0;
$area_name 		= '';
$_area_name 		= '';
$area_title 	= '';
$size_val 		= isset( $_GET['size'] ) ? $_GET['size'] : 0;
$size_id 		= 0;
$size_name 		= '';
$size_title 	= '';
$link_ai 		= '';
$link_pdf 		= '';
$material_val 	= isset( $_GET['material'] ) ? $_GET['material'] : 0;
$material_id 	= 0;
$material_name 	= '';
$material_title = '';
$finishing_val  = isset( $_GET['finishing'] ) ? $_GET['finishing'] : 0;
$finishing_id 	= 0;
$finishing_name = '';
$finishing_title= '';
$areas 			= array();
$groups1 		= array();
$groups2 		= array();
$option_id 		= kitalabel_get_product_option($product_id);
$uploads 		= array();
$comments 		= array();
$quantity 		= 1;
if($option_id) {
	$_options 	= kitalabel_get_option($option_id);
	$options = unserialize($_options['fields']);
	if(isset($options['groups'] )) {
		$groups = $options['groups'];
		if( isset($options['groups'][0] ) ) {
			$groups1 = $options['groups'][0]['fields'];
		}
		if( isset($options['groups'][1] ) ) {
			$groups2 = $options['groups'][1]['fields'];
		}
	}
	if( isset($options['fields']) && is_array($options['fields']) ) {
		foreach( $options['fields'] as $field) {
			$option_field = $field['general']['attributes']['options'];
			if( isset($field['nbd_type']) && $field['nbd_type'] == 'area' && in_array( $field['id'] , $groups1 ) ) {
				$area_id 	= $field['id'];
				$area_title = $field['general']['title'];
				if(isset($option_field[$area_val])) { 
					$area_name = $option_field[$area_val]['name'];
					$_area_name = $option_field[$area_val]['name'];
					if( $area_name == 'Square' || $area_name == 'Circle' ) {
						$_area_name = 'Square + Circle';
					}
					if( $area_name == 'Rectangle' || $area_name == 'Oval' ) {
						$_area_name = 'Rectangle + Oval';
					}
				}
				if(is_array( $option_field) ) {
					foreach( $option_field as $op ) {
						if( !isset($op['coming_soon']) || ( isset($op['coming_soon']) && $op['coming_soon'] != 'on') ){
							$areas[] = $op['name'];
						}
					}
				}
			}
			if( isset($field['nbd_type']) && $field['nbd_type'] == 'size' && in_array( $field['id'] , $groups1 ) ) {
				if( isset( $field['conditional']['depend'][0] ) &&  $field['conditional']['depend'][0]['id'] == $area_id && $field['conditional']['depend'][0]['val'] == $area_val) {
					$size_id 	= $field['id'];
					$size_title = $field['general']['title'];
					if( isset($option_field[$size_val]) ) { 
						$size_name = $option_field[$size_val]['name'];
						if( isset($option_field[$size_val]['guideline_ai'])) {
							$link_ai = $option_field[$size_val]['guideline_ai']['link'];
						}
						if( isset($option_field[$size_val]['guideline_pdf']) ) {
							$link_pdf = $option_field[$size_val]['guideline_pdf']['link'];
						}
					}
				}
			}
			if( isset($field['nbd_type']) && $field['nbd_type'] == 'color' && in_array( $field['id'] , $groups2 ) ) {
				$material_id 	= $field['id'];
				$material_title = $field['general']['title'];
				if(isset($option_field[$material_val])) $material_name = $option_field[$material_val]['name'];
			}
			if( !isset($field['nbd_type'])  && in_array( $field['id'] , $groups2 ) ) {
				if( isset( $field['conditional']['depend'][0] ) &&  $field['conditional']['depend'][0]['id'] == $material_id  ) {
					if( $field['conditional']['depend'][0]['val'] == $material_val && $field['conditional']['depend'][0]['operator'] == 'i' ) {
						$finishing_id 	= $field['id'];
						$finishing_title = $field['general']['title'];
						if(isset($option_field[$finishing_val])) $finishing_name = $option_field[$finishing_val]['name'];
					}
					if( $field['conditional']['depend'][0]['val'] != $material_val && $field['conditional']['depend'][0]['operator'] == 'n' ) {
						$finishing_id 	= $field['id'];
						$finishing_title = $field['general']['title'];
						if(isset($option_field[$finishing_val])) $finishing_name = $option_field[$finishing_val]['name'];
					}
				}
			}
			if( !isset($field['nbd_type']) ) {
				if( $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'u' ) {
					$uploads = array(
						'title' 		=>  $field['general']['title'],
						'desc' 			=>  $field['general']['description'],
						'allow_type' 	=>  $field['general']['upload_option']['allow_type'],
						'id' 			=>  $field['id'],
						'min' 			=>  $field['general']['upload_option']['min_size'],
						'max' 			=>  $field['general']['upload_option']['max_size'],
					);
				}
				if( $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'a' ) {
					$comments = array(
						'title' 		=>  $field['general']['title'],
						'desc' 			=>  $field['general']['description'],
						'id' 			=>  $field['id'],
						'min' 			=>  $field['general']['text_option']['min'],
						'max' 			=>  $field['general']['text_option']['max'],
						'placeholder'	=>  $field['general']['placeholder'],
					);
				}
			}
		}
		
	}
	if( isset($options['combination'])) {  
        if( isset($_area_name) && isset($size_name) && isset($material_name)  && isset($options['combination']['options']) ) {
            $side = $options['combination']['options'][$_area_name][$size_name][$material_name];
            if(!isset($side) && isset($options['combination']['options']['default'])) {
                $side = $options['combination']['options']['default'];
            }
        }
        if( isset($side) ) {
            $quantity = (int) $side['qty'];
        }
    }
}

?>
<section id="main-container" class="<?php echo esc_attr( apply_filters('aora_tbay_page_content_class', 'container') );?>">
	<div class="row <?php echo esc_attr($class_row); ?>">
		<?php if ( isset($sidebar_configs['sidebar']) && is_active_sidebar($sidebar_configs['sidebar']['id']) ) : ?>
		<div class="<?php echo esc_attr($sidebar_configs['sidebar']['class']) ;?>">
		  	<aside class="sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		   		<?php dynamic_sidebar( $sidebar_configs['sidebar']['id'] ); ?>
		  	</aside>
		</div>
		<?php endif; ?>
		<div id="main-content" class="main-page <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<div id="main" class="site-main">
				<?php
				while ( have_posts() ) : the_post();
					the_content();
				?>
					<?php if ( count($areas) > 0 ): ?>
						<ul class="list-options-area">
							<?php foreach( $areas as $area) {
									$activate_class = $area == $area_name ? ' activate' : '';
									echo '<li class="option-area'.$activate_class.' " >'.$area.'</li>';
								}
							?>	
						</ul>
					<?php endif; ?>
					<div class="kita-uf-nbo-option-container kita-custom-page-upload">
						<i class="fas fa-spinner fa-spin"></i>
						<?php if ( $the_query->have_posts() ): ?>
							<?php while ( $the_query->have_posts() ): $the_query->the_post();
								$id = get_the_ID();
							?>
								<div class="kita-uf-nbo-option-wrapper-<?php echo $id;?><?php echo $activate_class;?>">
									<form class="upload-form" action="<?php echo site_url();?>/upload-file/" method="post" enctype="multipart/form-data">
										<div class="kita-options-selected">
											<div class="row">
												<div class="kita-col-options col-md-6">
													<ul class="kita-list kita-list-options">
														<li>
															<?php if($size_name) {
																?>
																<label for="" class="nbd-label"><?php echo $size_title; ?></label>
																<div class="nbd-option-selected"><span class="name"><?php echo $size_name; ?></span></div>
																<?php
															}?>
															<input value="<?php echo $size_val; ?>" name="nbd-field[<?php echo $size_id; ?>]" type="hidden" >
														</li>
														<li>
															<?php if($material_name) {
																?>
																<div for="" class="nbd-label"><?php echo $material_title; ?></label>
																<div class="nbd-option-selected"><span class="name"><?php echo $material_name; ?></span></div>
																<?php
															}?>
															<input value="<?php echo $material_val; ?>" name="nbd-field[<?php echo $material_id; ?>]" type="hidden" >
														</li>
														<li>
															<?php if($finishing_name) {
																?>
																<label for="" class="nbd-label"><?php echo $finishing_title; ?></label>
																<div class="nbd-option-selected"><span class="name"><?php echo $finishing_name; ?></span></div>
																<?php
															}?>
															<input value="<?php echo $finishing_val; ?>" name="nbd-field[<?php echo $finishing_id; ?>]" type="hidden" >
														</li>
														<input value="<?php echo $area_val; ?>" name="nbd-field[<?php echo $area_id; ?>]" type="hidden" >
													</ul>
												</div>
												<div class="kita-col-options col-md-6">
													<ul class="kita-list kita-list-files">
														<?php if($link_ai) {
														?>
														<li>
															<a href="<?php echo $link_ai; ?>" class="nbd-option-selected" download>
																<img src="<?php echo get_stylesheet_directory_uri().'/images/guideline_ai.png'; ?>" alt="guideline_ai">
																<span class="name">Guideline .Ai</span>
															</a>
																
														</li>
															<?php
														}
														if($link_pdf) {
														?>
														<li>
															<a href="<?php echo $link_pdf; ?>" class="nbd-option-selected" download>
																<img src="<?php echo get_stylesheet_directory_uri().'/images/guideline_pdf.png'; ?>" alt="guideline_pdf">
																<span class="name">Guideline .Pdf</span>
															</a>
														</li>
														<?php
														}?>
													</ul>			
												</div>
											</div>
										</div>
										<div class="kita-button-upload">
											<?php if( count( $uploads ) > 0 ) {
												$allowed_type = str_replace(',' , ', .' , $uploads["allow_type"]);
												$allowed_type = '.'.$allowed_type;
												echo '<label class="title">'.$uploads["title"].'<input style="display:none!important;" type="file" class="button-upload" name="nbd-field['.$uploads["id"].'][]" multiple  minsize="'.$uploads["min"].'" maxsize="'.$uploads["max"].'" accept="'.$allowed_type.'"></label><div class="result-upload"></div>';
												echo '<div class="desc">'.$uploads["desc"].'</div>';
												echo '<div class="format">File Formats: <span>'.$uploads["allow_type"].'</span></div>';
											}?>
										</div>
										<div class="kita-text-comment">
											<?php if( count( $comments) > 0 ) {
												echo '<textarea name="nbd-field['.$comments["id"].']" rows="3" maxlength="'.$comments["max"].'" placeholder="'.$comments["placeholder"].'"></textarea>';
											}?>
										</div>
										<div class="kita-content">
											<?php the_content(); ?>
										</div>
										<div class="kita-tac-wrapper">
											<label class="cs-checkbox-agree" for="cs-argee-condition"><?php esc_html_e('I agree with terms and condition and privacy policy', 'aora');?>
								                <input type="checkbox" id="cs-argee-condition" name="kita-uf-tac">
								                <span class="checkmark"></span>
								            </label>
										</div>
										<input type="hidden" name="is_from_kita_upload_form" value="1">
										<input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
										<button type="submit" name="add-to-cart" disabled value="<?php echo $id;?>" class="single_add_to_cart_button button alt"><?php esc_html_e('Proceed', 'aora');?></button>
									</form>
								</div>
							<?php endwhile;?>
						<?php endif; ?>
					</div>

				<?php endwhile;?>

			</div><!-- .site-main -->
		</div><!-- .content-area -->
	</div>
</section>
<script>
	jQuery(document).ready(function($) {
		$('.kita-button-upload input.button-upload').on('change' , function(event) {
			var files = $(this).prop('files');
			if(files.length > 0) {
				var files_name = '<ul>';
				for( var i=0; i<files.length; i++) {
					var file = files[i];
					file_name = '<li class="file-name" style="color:#0040ff"><b>'+file.name+'</b></li>';
					files_name += file_name;
				}
				files_name += '</ul>';
				$('.kita-button-upload .result-upload').html(files_name);
			}	
			var is_enable = true;
			if ( $('.kita-tac-wrapper #cs-argee-condition').is(":checked") ) {
				var upload = $('.kita-button-upload .result-upload').html();
				if( upload ) {
					$('.single_add_to_cart_button').prop('disabled', false);
					is_enable = false;
				}
			} 
			if( is_enable ) {
				$('.single_add_to_cart_button').prop('disabled', true);
			}
		})
		$('.kita-tac-wrapper #cs-argee-condition').on('change' , function(event) {
			var is_enable = true;
			if ( $(this).is(":checked") ) {
				var upload = $('.kita-button-upload .result-upload').html();
				if( upload ) {
					$('.single_add_to_cart_button').prop('disabled', false);
					is_enable = false;
				}
			} 
			if( is_enable ) {
				$('.single_add_to_cart_button').prop('disabled', true);
			}
		})
		$(".kita-button-upload").on("dragover", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-button-upload").on("dragleave", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-button-upload").on("drop", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		    $('input.button-upload').prop( 'files' , event.originalEvent.dataTransfer.files );
		    $('input.button-upload').trigger('change');
		});
	})
</script>
<?php 
	if($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
		echo do_shortcode( '[pafe-template id="8250"]' );
	}
	else {
		echo do_shortcode( '[pafe-template id="8962"]' );
	}
?>
<?php get_footer(); ?>