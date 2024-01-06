<?php
/**
* Template Name: Upload file modern

*/

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
										<div class="kita-page-upload-wrap">
											<div class="row">
												<div class="col-md-6">
													<div class="kita-options-selected">
														<h3 class="kita-heading">Spesifikasi Label</h3>
														<div class="kita-col-options-wrap">
															<div class="kita-col-options">
																<table>
																	<tbody>
																		<?php 
																		if($area_name) {
																		?>
																			<tr>
																				<td class="nbd-option-selected-name"><label for="" class="nbd-label"><?php echo $area_title; ?></label></td>
																				<td class="nbd-option-selected-value">
																					: <span class="name"><?php echo $area_name; ?></span>
																					<input value="<?php echo $area_val; ?>" name="nbd-field[<?php echo $area_id; ?>]" type="hidden" >
																				</td>
																			</tr>
																		<?php
																		}
																		if($size_name) {
																		?>
																			<tr>
																				<td class="nbd-option-selected-name"><label for="" class="nbd-label"><?php echo $size_title; ?></label></td>
																				<td class="nbd-option-selected-value">
																					: <span class="name"><?php echo $size_name; ?></span>
																					<input value="<?php echo $size_val; ?>" name="nbd-field[<?php echo $size_id; ?>]" type="hidden" >
																				</td>
																			</tr>
																		<?php
																		}
																		if($material_name) {
																		?>
																			<tr>
																				<td class="nbd-option-selected-name"><label for="" class="nbd-label"><?php echo $material_title; ?></label></td>
																				<td class="nbd-option-selected-value">
																					: <span class="name"><?php echo $material_name; ?></span>
																					<input value="<?php echo $material_val; ?>" name="nbd-field[<?php echo $material_id; ?>]" type="hidden" >
																				</td>
																			</tr>
																		<?php
																		}
																		if($finishing_name) {
																		?>
																			<tr>
																				<td class="nbd-option-selected-name"><label for="" class="nbd-label"><?php echo $finishing_title; ?></label></td>
																				<td class="nbd-option-selected-value">
																					: <span class="name"><?php echo $finishing_name; ?></span>
																					<input value="<?php echo $finishing_val; ?>" name="nbd-field[<?php echo $finishing_id; ?>]" type="hidden" >
																				</td>
																			</tr>																				
																		<?php
																		}
																		?>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="kita-col-guide">
														<div class="kita-list kita-list-files row">
															<div class="col-md-6">
																<?php if($link_ai): ?>
																	<a href="<?php echo $link_ai; ?>" class="kita-upload-guide" download>
																		<i class="kita-upload-file-icon kita-upload-file-ai">
																			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-ai" viewBox="0 0 16 16">
																			  	<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2H6v-1h6a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.113 14.82.8 15.85H0l1.342-3.999h.926l1.336 3.999h-.841l-.314-1.028H1.113Zm1.178-.588-.49-1.617h-.034l-.49 1.617h1.014Zm2.425-2.382v3.999h-.791V11.85h.79Z"/>
																			</svg>
																		</i>
																		<span class="name">Guideline .Ai</span>
																	</a>
																<?php endif; ?>
															</div>
															<div class="col-md-6">
																<?php if($link_pdf): ?>
																	<a href="<?php echo $link_pdf; ?>" class="kita-upload-guide" download>
																		<i class="kita-upload-file-icon kita-upload-file-pdf">
																			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
																			  	<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
																			  	<path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
																			</svg>
																		</i>
																		<span class="name">Guideline .Pdf</span>
																	</a>
																<?php endif; ?>
															</div>
														</div>			
													</div>
													<div class="kita-content-wrap">
														<?php the_content(); ?>
													</div>
												</div>
												<div class="col-md-6">
													<div class="kita-button-upload">
														<h3 class="kita-heading">Upload Design Kamu</h3>
														<ul class="kita-list-uploadfiles">
															<?php 
															for($i=0; $i < $variant; $i++){
																if( count( $uploads ) > 0 ) {
																	echo '<li class="kita-uploadfile-wrap">';
																	$allowed_type = str_replace(',' , ', .' , $uploads["allow_type"]);
																	$allowed_type = '.'.$allowed_type;
																	$variant_index = $i + 1;
																	$quantity_variant_per = intdiv($quantity, $variant);
																	if($variant_index == $variant ) $quantity_variant_per = $quantity_variant_per + ($quantity % $variant);
																	echo '<div class="kita-variant-fields"><input class="kita-variant-name" value="Variant '.$variant_index.'" type="text" name="nbd-field['.$uploads["id"].'][variant]['.$i.']" />';
																	echo '<input class="kita-variant-qtys" value="'.$quantity_variant_per.'" type="hidden" name="nbd-field['.$uploads["id"].'][qty]['.$i.']" />';
																	echo '<input class="kita-variant-qtys" value="'.$quantity.'" type="hidden" name="nbd-field['.$uploads["id"].'][min_qty]" /></div>';
																	echo '<label class="upload-zone"><div class="result-upload"><svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
			  <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
			  <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
			</svg><p>'.$uploads["desc"].'</p></div>';
																	echo '<div class="format">File Formats: <span>'.$uploads["allow_type"].'</span></div>';
																	echo '<input style="display:none!important;" type="file" class="button-upload" name="nbd-field['.$uploads["id"].']['.$i.']"  minsize="'.$uploads["min"].'" maxsize="'.$uploads["max"].'" accept="'.$allowed_type.'"></label>';
																	echo '</li>';
																}
															} 
															?>
														</ul>
														<a class="button kita-add-variant">+ Tambah Variant</a>
													</div>
													<div class="kita-add-to-cart">
														<input type="hidden" name="is_from_kita_upload_form" value="1">
														<input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
														<div class="row">
															<div class="col-md-6">
																<a class="button">
																	Kembali
																</a>
															</div>
															<div class="col-md-6">
																<button type="submit" name="add-to-cart" disabled value="<?php echo $id;?>" class="single_add_to_cart_button button alt">
																	Checkout
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
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
		$('.kita-uploadfile-wrap input.button-upload').on('change' , function(event) {
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
				var upload = $('.kita-uploadfile-wrap .result-upload').html();
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
				// var uploads = $('.kita-uploadfile-wrap .result-upload').html();
				var uploads = $('.kita-uploadfile-wrap');
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
		$(".kita-uploadfile-wrap .upload-zone").on("dragover", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-uploadfile-wrap .upload-zone").on("dragleave", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		});

		$(".kita-uploadfile-wrap .upload-zone").on("drop", function(event) {
		    event.preventDefault();  
		    event.stopPropagation();
		    var file = event.originalEvent.dataTransfer.files[0];
		    var extension = file.type.split("/").pop() == 'postscript' ? 'ai' : file.type.split("/").pop();
		    var accept = '<?php echo $allowed_type; ?>';

		    if(accept.indexOf(extension) < 0) {
		    	alert('Invalid file');
		    } else {
		    	$(this).find('input.button-upload').prop( 'files' , event.originalEvent.dataTransfer.files );
		    	$(this).find('input.button-upload').trigger('change');
		    }
		});
	})
</script>

<?php get_footer(); ?>

