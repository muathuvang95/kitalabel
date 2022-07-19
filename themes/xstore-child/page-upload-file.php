<?php

get_header();

$class_row = ( get_post_meta( $post->ID, 'tbay_page_layout', true ) === 'main-right' ) ? 'flex-row-reverse' : '';

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
$variant  = isset( $_GET['variant'] ) ? $_GET['variant'] : 1;
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
<section id="main-container" class="nb-main-container">
	<div class="row <?php echo esc_attr($class_row); ?>">
		<div id="main-content" class="main-page">
			<div id="main" class="site-main">
				<?php
				while ( have_posts() ) : the_post();
					the_content();
				?>
					<div class="kita-uf-nbo-option-container kita-custom-page-upload">
						<i class="fas fa-spinner fa-spin"></i>
						<?php if ( $the_query->have_posts() ): ?>
							<?php while ( $the_query->have_posts() ): $the_query->the_post();
								$id = get_the_ID();
							?>
								<div class="kita-uf-nbo-option-wrapper-<?php echo $id;?>">
									<form class="upload-form" action="<?php echo site_url();?>/upload-file/" method="post" enctype="multipart/form-data">
										<div class="kita-content">
											<?php the_content(); ?>
										</div>
										<div class="kita-options-selected">
											<div class="kita-title-block">
												<span>1.</span> Berikut ini adalah spesifikasi label Anda, silahakan download file <span>guideline</span> di bawah ini
											</div>
											<div class="kita-col-options-wrap">
												<div class="kita-col-options">
													<div class="row">
														<div class="col-md-6">
															<ul class="kita-list kita-list-options">
																<li class="kita-heading">Spesifikasi Label</li>
																<li>
																	<table>
																		<tbody>
																			<?php 
																			if($area_name) {
																			?>
																				<tr>
																					<td><label for="" class="nbd-label"><?php echo $area_name; ?></label></td>
																					<td class="nbd-option-selected">
																						: <span class="name"><?php echo $area_name; ?></span>
																						<input value="<?php echo $area_val; ?>" name="nbd-field[<?php echo $area_id; ?>]" type="hidden" >
																					</td>
																				</tr>
																			<?php
																			}
																			if($size_name) {
																			?>
																				<tr>
																					<td><label for="" class="nbd-label"><?php echo $size_title; ?></label></td>
																					<td class="nbd-option-selected">
																						: <span class="name"><?php echo $size_name; ?></span>
																						<input value="<?php echo $size_val; ?>" name="nbd-field[<?php echo $size_id; ?>]" type="hidden" >
																					</td>
																				</tr>
																			<?php
																			}
																			if($material_name) {
																			?>
																				<tr>
																					<td><label for="" class="nbd-label"><?php echo $material_title; ?></label></td>
																					<td class="nbd-option-selected">
																						: <span class="name"><?php echo $material_name; ?></span>
																						<input value="<?php echo $material_val; ?>" name="nbd-field[<?php echo $material_id; ?>]" type="hidden" >
																					</td>
																				</tr>
																			<?php
																			}
																			if($finishing_name) {
																			?>
																				<tr>
																					<td><label for="" class="nbd-label"><?php echo $finishing_title; ?></label></td>
																					<td class="nbd-option-selected">
																						: <span class="name"><?php echo $finishing_name; ?></span>
																						<input value="<?php echo $finishing_val; ?>" name="nbd-field[<?php echo $finishing_id; ?>]" type="hidden" >
																					</td>
																				</tr>																				
																			<?php
																			}
																			?>
																		</tbody>
																	</table>
																</li>
															</ul>
														</div>
														<div class="kita-col-guide col-md-6">
															<ul class="kita-list kita-list-files">
																<?php if($link_ai) {
																?>
																<li>
																	<a href="<?php echo $link_ai; ?>" class="nbd-option-selected" download>
																		<img src="<?php echo CUSTOM_KITALABEL_URL.'assets/images/guideline_ai.png'; ?>" alt="guideline_ai">
																		<span class="name">Guideline .Ai</span>
																	</a>
																		
																</li>
																	<?php
																}
																if($link_pdf) {
																?>
																<li>
																	<a href="<?php echo $link_pdf; ?>" class="nbd-option-selected" download>
																		<img src="<?php echo CUSTOM_KITALABEL_URL.'assets/images/guideline_pdf.png'; ?>" alt="guideline_pdf">
																		<span class="name">Guideline .Pdf</span>
																	</a>
																</li>
																<?php
																}?>
															</ul>			
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="kita-button-upload">
											<div class="kita-title-block">
												<span>2.</span> Silahkan upload file <span>Guideline</span> yang sudah dipasangi desain pada kolom dibawah ini
											</div>
											<ul class="kita-list-uploadfiles">
												<?php 
												for($i=0; $i < $variant; $i++){
													if( count( $uploads ) > 0 ) {
														echo '<li class="kita-uploadfile">';
														$allowed_type = str_replace(',' , ', .' , $uploads["allow_type"]);
														$allowed_type = '.'.$allowed_type;
														$variant_index = $i + 1;
														$quantity_variant_per = intdiv($quantity, $variant);
														if($variant_index == $variant ) $quantity_variant_per = $quantity_variant_per + ($quantity % $variant);
														echo '<input class="kita-variant-name" value="Variant '.$variant_index.'" type="text" name="nbd-field['.$uploads["id"].'][variant]['.$i.']" />';
														echo '<input class="kita-variant-qtys" value="'.$quantity_variant_per.'" type="hidden" name="nbd-field['.$uploads["id"].'][qty]['.$i.']" />';
														echo '<input class="kita-variant-qtys" value="'.$quantity.'" type="hidden" name="nbd-field['.$uploads["id"].'][min_qty]" />';
														echo '<div class="result-upload"><svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
  <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
</svg>'.$uploads["desc"].'</div>';
														echo '<div class="format">File Formats: <span>'.$uploads["allow_type"].'</span></div>';
														echo '<label class="title">'.$uploads["title"].'<input style="display:none!important;" type="file" class="button-upload" name="nbd-field['.$uploads["id"].']['.$i.']"  minsize="'.$uploads["min"].'" maxsize="'.$uploads["max"].'" accept="'.$allowed_type.'"></label>';
														echo '</li>';
													}
												} 
												?>
											</ul>
										</div>
										<div class="kita-text-comment">
											<?php if( count( $comments) > 0 ) {
												echo '<textarea name="nbd-field['.$comments["id"].']" rows="3" maxlength="'.$comments["max"].'" placeholder="'.$comments["placeholder"].'"></textarea>';
											}?>
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
		$('.kita-uploadfile input.button-upload').on('change' , function(event) {
			var files = $(this).prop('files');
			var file = files[0];
			var files_name = file.name;
			if(files_name.length > 20) {
				files_name = '...'+files_name.substr(files_name.length - 20, files_name.length);
			}
			var file_name_tag = '<li class="file-name" style="color:#EF8C04"><b>'+files_name+'</b></li>';
			$(this).parent().parent().find('.result-upload').html(file_name_tag);
			var is_enable = true;
			if ( $('.kita-tac-wrapper #cs-argee-condition').is(":checked") ) {
				var upload = $('.kita-uploadfile .result-upload').html();
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
				// var uploads = $('.kita-uploadfile .result-upload').html();
				var uploads = $('.kita-uploadfile');
				uploads.each(function() {
					var upload = $(this).find('input.button-upload').val();
					if( upload ) {
						$('.single_add_to_cart_button').prop('disabled', false);
						is_enable = false;
					}
				})
			} 
			if( is_enable ) {
				$('.single_add_to_cart_button').prop('disabled', true);
			}
		})
		$(".kita-uploadfile").on("dragover", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-uploadfile").on("dragleave", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-uploadfile").on("drop", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		    $('input.button-upload').prop( 'files' , event.originalEvent.dataTransfer.files );
		    $(this).find('input.button-upload').trigger('change');
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