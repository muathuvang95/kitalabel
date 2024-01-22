<?php
/**
* Template Name: Upload file modern

*/

get_header();

$class_row = ( get_post_meta( $post->ID, 'tbay_page_layout', true ) === 'main-right' ) ? 'flex-row-reverse' : '';

$product_id = isset( $_GET['product_id'] ) ? $_GET['product_id'] : 0;

$is_quote = isset( $_GET['is_quote'] ) ? $_GET['is_quote'] : 0;

$quantity = isset( $_GET['quantity'] ) ? $_GET['quantity'] : 1;

if( $product_id == 0) {
	get_template_part( 404 ); exit();
}
$args = array(
	'post_type' => 'product',
	'p'			=> $product_id,
);

$param = $_SERVER['QUERY_STRING'];

parse_str($param, $query);

if(!empty($query['product_id'])) {
	unset($query['product_id']);
}

if(!empty($query['is_quote'])) {
	unset($query['is_quote']);
}

if(!empty($query['quantity'])) {
	unset($query['quantity']);
}

$order_label_page = nbdesigner_get_option('nbd_order_label_page_id');

$order_label_link = $order_label_page ? get_permalink($order_label_page) : home_url() . '/order-label';

$the_query = new WP_Query( $args );
wp_reset_postdata();

$appid              = "kita-app-" . time() . rand( 1, 1000 );
$option_id 			= kitalabel_get_product_option($product_id);
$uploadConfig 		= array();
$comments 			= array();
$variant_id 		= '';
$variant 			= 1;
$option_selected 	= array();
$area_name 			= '';
$size_name 			= '';
$material_name 		= '';
$allowed_type 		= '';
$min_qty 			= 1;

if($option_id) {
	$_options 	= kitalabel_get_option($option_id);
	$options = unserialize($_options['fields']);
	if( !empty($options['fields']) ) {

		foreach( $options['fields'] as $field) {
			$field_id = $field['id'];
			$option_field = $field['general']['attributes']['options'];

			if(isset($query[$field_id])) {
				$value = $query[$field_id];

				if( isset($field['nbd_type']) && ( $field['nbd_type'] == 'page' || $field['nbd_type'] == 'page1' || $field['nbd_type'] == 'page2' || $field['nbd_type'] == 'dimension' ) ) {
					if($field['nbd_type'] == 'page' || $field['nbd_type'] == 'page1' || $field['nbd_type'] == 'page2') {
						$variant = (int) $value;
						$variant_id = $field_id;
					}
					if($field['nbd_type'] == 'dimension') {
						$option_selected[$field_id] = array(
							'title'			=> $field['general']['title'],
							'value' 		=> $value,
							'show' 			=> true,
							'value_name'	=> $value,
						);
					}
				} else {
					if(!isset($field['nbd_type']) && $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'u' && $value ) {
						$file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$value );
						$basename = basename($value);

						$option_selected[$field_id] = array(
							'title'			=> $field['general']['title'],
							'value' 		=> $value,
							'show' 			=> true,
							'is_upload' 	=> true,
							'value_name'	=> '<a class="kita-link-design" href="' . $file_url . '">' . $basename . '</a>',
						);
					} else {
						$option_selected[$field_id] = array(
							'title'			=> $field['general']['title'],
							'value' 		=> $value,
							'show' 			=> true,
							'value_name'	=> !empty($option_field[$value]['name']) ? $option_field[$value]['name'] : '',
						);
					}
				}

				if( isset($field['nbd_type']) && $field['nbd_type'] == 'area' ) {
					if(isset($option_field[$value])) { 
						$_area_name = $option_field[$value]['name'];
						$area_name = $option_field[$value]['name'];
						if( $_area_name == 'Square' || $_area_name == 'Circle' ) {
							$area_name = 'Square + Circle';
						}
						if( $_area_name == 'Rectangle' || $_area_name == 'Oval' ) {
							$area_name = 'Rectangle + Oval';
						}
					}
				}
				if( isset($field['nbd_type']) && $field['nbd_type'] == 'size' ) {
					if( isset($option_field[$value]) ) { 
						$size_name = $option_field[$value]['name'];
						if( isset($option_field[$value]['guideline_ai'])) {
							$link_ai = $option_field[$value]['guideline_ai']['link'];
						}
						if( isset($option_field[$value]['guideline_pdf']) ) {
							$link_pdf = $option_field[$value]['guideline_pdf']['link'];
						}
					}
				}
				if( isset($field['nbd_type']) && $field['nbd_type'] == 'color' ) {
					if(isset($option_field[$value])) {
						$material_name = $option_field[$value]['name'];
					}
				}
			}

			if( !isset($field['nbd_type']) ) {
				if( $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'u' ) {
					$uploadConfig = array(
						'title' 		=>  $field['general']['title'],
						'desc' 			=>  $field['general']['description'],
						'allow_type' 	=>  $field['general']['upload_option']['allow_type'],
						'id' 			=>  $field['id'],
						'min' 			=>  $field['general']['upload_option']['min_size'],
						'max' 			=>  $field['general']['upload_option']['max_size'],
					);
					$allowed_type = str_replace(',' , ', .' , $uploadConfig["allow_type"]);
					$allowed_type = '.'.$allowed_type;
					$uploadConfig['_allow_type'] = $allowed_type;
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
        if( !empty($options['combination']['options'][$area_name][$size_name][$material_name]) ) {
            $side = $options['combination']['options'][$area_name][$size_name][$material_name];
            if(!isset($side) && isset($options['combination']['options']['default'])) {
                $side = $options['combination']['options']['default'];
            }
            $min_qty = isset($side['qty']) && $side['qty'] ? (int) $side['qty'] : 1;
        }
    }
}

if($quantity < $min_qty) {
	$quantity = $min_qty;
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
									<form class="upload-form cart" action="<?php echo site_url();?>/upload-file-modern/" method="post" enctype="multipart/form-data">
										<div class="kita-page-upload-wrap" id="<?php echo $appid; ?>" ng-app="uploadApp">
											<div ng-controller="uploadCtrl" ng-form="kitaForm" id="upload-ctrl-<?php echo $appid; ?>" ng-cloak>
												<div class="row">
													<div class="col-md-6">
														<div class="kita-options-selected">
															<h3 class="kita-heading">Spesifikasi Label</h3>
															<div class="kita-col-options-wrap">
																<div class="kita-col-options">
																	<table>
																		<tbody>
																			<?php
																			if(!empty($option_selected)) {
																				foreach($option_selected as $field_id => $field_option ) {
																					?>
																						<tr>
																							<td class="nbd-option-selected-name"><label for="" class="nbd-label"><?php echo $field_option['title']; ?></label></td>
																							<td class="nbd-option-selected-value">
																								: <span class="name"><?php echo $field_option['value_name']; ?></span>
																								<input value="<?php echo $field_option['value']; ?>" name="nbd-field[<?php echo $field_id; ?>]<?php echo !empty($field_option['is_upload']) && $field_option['is_upload'] ? '[upload_file]' : ''; ?>" type="hidden" >
																							</td>
																						</tr>
																					<?php
																				}
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
																	<?php if(isset($link_ai)): ?>
																		<a href="<?php echo $link_ai; ?>" class="kita-upload-guide" download>
																			<i class="kita-upload-file-icon kita-upload-file-ai">
																				<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
																					<path d="M3.16895 3.5166H28.8349V28.4826H3.16895V3.5166Z" fill="#1C0A00"/>
																					<path d="M3.169 3.51661H28.835V28.4826H3.169V3.51661ZM2 29.6496H30V2.34961H2V29.6496ZM20.34 12.0796C20.34 11.9866 20.375 11.9396 20.48 11.9396H22.312C22.405 11.9396 22.452 11.9746 22.452 12.0796V21.2846C22.452 21.3776 22.429 21.4246 22.312 21.4246H20.505C20.388 21.4246 20.353 21.3666 20.353 21.2726V12.0796H20.34ZM20.212 9.43161C20.212 9.116 20.3374 8.81332 20.5605 8.59015C20.7837 8.36698 21.0864 8.24161 21.402 8.24161C21.7176 8.24161 22.0203 8.36698 22.2435 8.59015C22.4666 8.81332 22.592 9.116 22.592 9.43161C22.6035 9.59283 22.5799 9.75464 22.5227 9.90581C22.4655 10.057 22.3761 10.1939 22.2607 10.3071C22.1453 10.4203 22.0067 10.5071 21.8544 10.5614C21.7022 10.6157 21.54 10.6362 21.379 10.6216C21.2213 10.6315 21.0633 10.6073 20.9158 10.5506C20.7682 10.494 20.6347 10.4062 20.5241 10.2933C20.4136 10.1804 20.3286 10.0449 20.2751 9.89625C20.2216 9.74756 20.2008 9.58908 20.214 9.43161H20.212ZM14.962 15.9186C14.635 14.6186 13.862 11.8006 13.574 10.4356H13.551C13.306 11.8006 12.688 14.1106 12.198 15.9186H14.962ZM11.719 17.8086L10.797 21.3086C10.774 21.4016 10.739 21.4256 10.622 21.4256H8.909C8.792 21.4256 8.769 21.3906 8.792 21.2506L12.105 9.65061C12.1856 9.33435 12.225 9.00897 12.222 8.68261C12.222 8.60061 12.257 8.56561 12.315 8.56561H14.765C14.847 8.56561 14.882 8.58861 14.905 8.68261L18.615 21.2706C18.638 21.3636 18.615 21.4226 18.522 21.4226H16.585C16.492 21.4226 16.433 21.3996 16.41 21.3226L15.453 17.8106H11.72L11.719 17.8086Z" fill="#FF7F18"/>
																				</svg>
																			</i>
																			<span class="name">Guideline .Ai</span>
																		</a>
																	<?php endif; ?>
																</div>
																<div class="col-md-6">
																	<?php if(isset($link_pdf)): ?>
																		<a href="<?php echo $link_pdf; ?>" class="kita-upload-guide" download>
																			<i class="kita-upload-file-icon kita-upload-file-pdf">
																				<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg">
																					<path d="M24.5999 2.07227L30.1639 7.87227V29.9283H9.37891V30.0003H30.2349V7.94527L24.5999 2.07227Z" fill="#909090"/>
																					<path d="M24.5311 2H9.30811V29.928H30.1641V7.873L24.5311 2Z" fill="#F4F4F4"/>
																					<path d="M9.15489 3.5H2.76489V10.327H22.8649V3.5H9.15489Z" fill="#7A7B7C"/>
																					<path d="M22.972 10.2109H2.89502V3.37891H22.972V10.2109Z" fill="#DD2025"/>
																					<path d="M9.55212 4.5337H8.24512V9.3337H9.27312V7.7147L9.50012 7.7277C9.72069 7.7239 9.93919 7.68439 10.1471 7.6107C10.3294 7.54799 10.4971 7.449 10.6401 7.3197C10.7856 7.19652 10.9003 7.04105 10.9751 6.8657C11.0754 6.57419 11.1112 6.2644 11.0801 5.9577C11.0739 5.7386 11.0355 5.52162 10.9661 5.3137C10.903 5.1636 10.8093 5.0283 10.691 4.91639C10.5728 4.80448 10.4325 4.71842 10.2791 4.6637C10.1465 4.61569 10.0095 4.58086 9.87012 4.5597C9.76454 4.54341 9.65794 4.53472 9.55112 4.5337M9.36212 6.8277H9.27312V5.3477H9.46612C9.5513 5.34155 9.63677 5.35463 9.71622 5.38596C9.79566 5.41729 9.86706 5.46607 9.92512 5.5287C10.0454 5.68971 10.1097 5.8857 10.1081 6.0867C10.1081 6.3327 10.1081 6.5557 9.88612 6.7127C9.72618 6.80066 9.54414 6.84127 9.36212 6.8277ZM13.0331 4.5207C12.9221 4.5207 12.8141 4.5287 12.7381 4.5317L12.5001 4.5377H11.7201V9.3377H12.6381C12.9889 9.34731 13.3382 9.28785 13.6661 9.1627C13.93 9.05802 14.1637 8.88928 14.3461 8.6717C14.5235 8.45215 14.6508 8.19653 14.7191 7.9227C14.7977 7.61256 14.836 7.29361 14.8331 6.9737C14.8525 6.59585 14.8233 6.21709 14.7461 5.8467C14.6729 5.57406 14.5358 5.32279 14.3461 5.1137C14.1973 4.94486 14.0152 4.80867 13.8111 4.7137C13.6359 4.63261 13.4516 4.57285 13.2621 4.5357C13.1868 4.52325 13.1105 4.51756 13.0341 4.5187M12.8521 8.4557H12.7521V5.3917H12.7651C12.9713 5.36798 13.1799 5.40517 13.3651 5.4987C13.5008 5.60702 13.6113 5.7435 13.6891 5.8987C13.7731 6.06203 13.8215 6.2413 13.8311 6.4247C13.8401 6.6447 13.8311 6.8247 13.8311 6.9737C13.8352 7.14533 13.8241 7.317 13.7981 7.4867C13.7673 7.66093 13.7103 7.8295 13.6291 7.9867C13.5372 8.13285 13.413 8.256 13.2661 8.3467C13.1427 8.42649 12.9966 8.46372 12.8501 8.4527M17.9301 4.5377H15.5001V9.3377H16.5281V7.4337H17.8281V6.5417H16.5281V5.4297H17.9281V4.5377" fill="#464648"/>
																					<path d="M22.2809 20.2545C22.2809 20.2545 25.4689 19.6765 25.4689 20.7655C25.4689 21.8545 23.4939 21.4115 22.2809 20.2545ZM19.9239 20.3375C19.4174 20.4494 18.9238 20.6133 18.4509 20.8265L18.8509 19.9265C19.2509 19.0265 19.6659 17.7995 19.6659 17.7995C20.1432 18.6029 20.6986 19.3572 21.3239 20.0515C20.8524 20.1218 20.385 20.218 19.9239 20.3395V20.3375ZM18.6619 13.8375C18.6619 12.8885 18.9689 12.6295 19.2079 12.6295C19.4469 12.6295 19.7159 12.7445 19.7249 13.5685C19.6471 14.3971 19.4736 15.2138 19.2079 16.0025C18.8441 15.3404 18.6557 14.5961 18.6609 13.8405L18.6619 13.8375ZM14.0129 24.3535C13.0349 23.7685 16.0639 21.9675 16.6129 21.9095C16.6099 21.9105 15.0369 24.9655 14.0129 24.3535ZM26.3999 20.8945C26.3899 20.7945 26.2999 19.6875 24.3299 19.7345C23.5088 19.7213 22.6881 19.7792 21.8769 19.9075C21.0912 19.1159 20.4146 18.223 19.8649 17.2525C20.2112 16.2519 20.4208 15.2092 20.4879 14.1525C20.4589 12.9525 20.1719 12.2645 19.2519 12.2745C18.3319 12.2845 18.1979 13.0895 18.3189 14.2875C18.4375 15.0926 18.661 15.8786 18.9839 16.6255C18.9839 16.6255 18.5589 17.9485 17.9969 19.2645C17.4349 20.5805 17.0509 21.2705 17.0509 21.2705C16.0737 21.5887 15.1537 22.0613 14.3259 22.6705C13.5019 23.4375 13.1669 24.0265 13.6009 24.6155C13.9749 25.1235 15.2839 25.2385 16.4539 23.7055C17.0756 22.9137 17.6435 22.0812 18.1539 21.2135C18.1539 21.2135 19.9379 20.7245 20.4929 20.5905C21.0479 20.4565 21.7189 20.3505 21.7189 20.3505C21.7189 20.3505 23.3479 21.9895 24.9189 21.9315C26.4899 21.8735 26.4139 20.9925 26.4039 20.8965" fill="#DD2025"/>
																					<path d="M24.4541 2.07715V7.95015H30.0871L24.4541 2.07715Z" fill="#909090"/>
																					<path d="M24.531 2V7.873H30.164L24.531 2Z" fill="#F4F4F4"/>
																					<path d="M9.47497 4.45655H8.16797V9.25655H9.19997V7.63855L9.42797 7.65155C9.64854 7.64775 9.86704 7.60824 10.075 7.53455C10.2573 7.47182 10.425 7.37283 10.568 7.24355C10.7124 7.12004 10.826 6.9646 10.9 6.78955C11.0003 6.49805 11.0361 6.18825 11.005 5.88155C10.9987 5.66246 10.9603 5.44547 10.891 5.23755C10.8278 5.08745 10.7342 4.95215 10.6159 4.84024C10.4976 4.72833 10.3573 4.64228 10.204 4.58755C10.0708 4.53908 9.9331 4.50391 9.79297 4.48255C9.68739 4.46626 9.58079 4.45757 9.47397 4.45655M9.28497 6.75055H9.19597V5.27055H9.38997C9.47515 5.26441 9.56062 5.27748 9.64007 5.30881C9.71952 5.34014 9.79091 5.38892 9.84897 5.45155C9.96929 5.61256 10.0336 5.80855 10.032 6.00955C10.032 6.25555 10.032 6.47855 9.80997 6.63555C9.65004 6.72351 9.46799 6.76312 9.28597 6.74955M12.956 4.44355C12.845 4.44355 12.737 4.45155 12.661 4.45455L12.426 4.46055H11.646V9.26055H12.564C12.9148 9.27016 13.2641 9.2107 13.592 9.08555C13.8559 8.98087 14.0896 8.81213 14.272 8.59455C14.4493 8.375 14.5766 8.11939 14.645 7.84555C14.7235 7.53542 14.7618 7.21646 14.759 6.89655C14.7784 6.51871 14.7491 6.13994 14.672 5.76955C14.5988 5.49691 14.4616 5.24564 14.272 5.03655C14.1232 4.86771 13.941 4.73152 13.737 4.63655C13.5617 4.55546 13.3774 4.4957 13.188 4.45855C13.1126 4.4461 13.0363 4.44041 12.96 4.44155M12.778 8.37855H12.678V5.31455H12.691C12.8971 5.29083 13.1057 5.32803 13.291 5.42155C13.4266 5.52987 13.5372 5.66635 13.615 5.82155C13.6989 5.98488 13.7473 6.16415 13.757 6.34755C13.766 6.56755 13.757 6.74755 13.757 6.89655C13.761 7.06818 13.75 7.23985 13.724 7.40955C13.6931 7.58378 13.6362 7.75235 13.555 7.90955C13.4631 8.0557 13.3389 8.17886 13.192 8.26955C13.0686 8.34935 12.9225 8.38657 12.776 8.37555M17.853 4.46055H15.423V9.26055H16.451V7.35655H17.751V6.46455H16.451V5.35255H17.851V4.46055" fill="white"/>
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
																<li class="kita-uploadfile-wrap" ng-repeat="(index, uploadOption) in uploadOptions">
																	<div class="kita-variant-fields">
																        <input class="kita-variant-name" value="{{uploadOption.name}}" type="text" name="nbd-field[{{uploadOption.id}}][variant][{{index}}]">
																        <input class="kita-variant-qtys" value="{{uploadOption.quantity}}" type="hidden" name="nbd-field[{{uploadOption.id}}][qty][{{index}}]" autocomplete="off">
																        <input class="kita-variant-qtys" value="<?php echo $min_qty; ?>" type="hidden" name="nbd-field[{{uploadOption.id}}][min_qty]" autocomplete="off">
																        <a class="btn-remove-option" ng-click="removeUploadOption(index)">
																        	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
																			  	<path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
																			</svg>
																        </a>
																    </div>
																    <label class="upload-zone" nbd-dnd-file="uploadFile(files, index)">
																    	<div ng-show="!uploadOption.fileName">
																	        <div class="result-upload">
																	            <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
																	                <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"></path>
																	                <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"></path>
																	            </svg>
																	            <p>{{uploadOption.desc}}</p>
																	        </div>
																	        <div class="format">
																	            File Formats: <span>{{uploadOption.allow_type}}</span>
																	        </div>
																        </div>
																        <div class="file-name" ng-show="uploadOption.fileName">
																        	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-richtext" viewBox="0 0 16 16">
																			  	<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
																			  	<path d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5m0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5m1.639-3.708 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V8.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V8s1.54-1.274 1.639-1.208M6.25 6a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5"/>
																			</svg>
																			<p>
																				{{uploadOption.fileName}}
																			</p>
																        </div>
																        <input style="display:none!important;" type="file" class="button-upload" name="nbd-field[{{uploadOption.id}}][{{index}}]" minsize="{{uploadOption.min}}" maxsize="{{uploadOption.min}}" accept="{{uploadOption._allow_type}}">
																    </label>
																</li>
															</ul>
															<a ng-click="addUploadOption()" class="button kita-add-variant">+ Tambah Variant</a>
														</div>
														<div class="kita-add-to-cart">
															<input type="hidden" name="is_from_kita_upload_form" value="1">
															<input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
															<div class="row">
																<div class="col-md-6">
																	<a href="<?php echo $order_label_link; ?>" class="button">
																		Kembali
																	</a>
																</div>
																<div class="col-md-6">
																	<?php if(!$is_quote): ?>
																	<button type="submit" name="add-to-cart" disabled value="<?php echo $id;?>" class="single_add_to_cart_button button alt">
																		Checkout
																	</button>
																	<?php endif; ?>
																	<?php if($is_quote): ?>
																	<div class="nbdq-add-a-quote">
																	    <button data-id="<?php echo $product_id; ?>" disabled class="kita-add-a-quote-button button alt" id="nbdq-quote-btn">
																	    	<span><?php _e( 'checkout', 'web-to-print-online-designer' ); ?></span>
																	    	<div class="kita-loading" style="display: none;">Loading...</div>
																	    </button>
																	</div>
																	<?php endif; ?>
																</div>
															</div>
														</div>
													</div>
												</div>
												<?php do_action('page_upload_file_modern'); ?>
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
	angular.module('uploadApp', []).controller('uploadCtrl', function( $scope, $timeout ) {
		$scope.variant = parseInt(<?php echo $variant; ?>);
		$scope.quantity = parseInt(<?php echo $quantity; ?>);
		$scope.uploadConfig = <?php echo json_encode( $uploadConfig ); ?>;
		$scope.uploadOptions = [];
		$scope.variantName = "<?php esc_html_e('Variant', 'web-to-print-online-designer'); ?>";
		$scope.init = function() {
			if($scope.variant > 0) {
				var uploadOptions = [];

				for(i = 0; i < $scope.variant; i++){
					var index = i + 1;

					uploadOptions.push({
						...$scope.uploadConfig,
						name: $scope.variantName + ' ' + index,
						fileName: '',
						quantity: 1,
					});
	            }

	            $scope.uploadOptions = $scope.resetQuantity(uploadOptions);
			}
		};
		$scope.updateApp = function(){
	        if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
	    };
		$scope.addUploadOption = function() {
			var index = $scope.uploadOptions.length + 1;

			$scope.uploadOptions.push({
				...$scope.uploadConfig,
				name: $scope.variantName + ' ' + index,
				fileName: '',
				quantity: 1,
			});

			$scope.variant = $scope.variant + 1;
			$scope.uploadOptions = $scope.resetQuantity($scope.uploadOptions);
		}
		$scope.removeUploadOption = function(index) {
			if($scope.uploadOptions.length <= 1) {
				return alert('<?php esc_html_e('Can not delete!', 'web-to-print-online-designer'); ?>')
			}
			$scope.variant = $scope.variant - 1;

			$scope.uploadOptions.splice(index, 1);
			$scope.uploadOptions = $scope.resetQuantity($scope.uploadOptions);
		}
		
		$scope.resetQuantity = function(uploadOptions) {
			var side_qty = Math.floor($scope.quantity / $scope.variant);
			var remainder_qty = $scope.quantity % $scope.variant;
			var _uploadOptions = [];
			var can_add_to_cart = true;

			angular.forEach(uploadOptions, function(uploadOption, index) {
				var quantity = side_qty;
				if(index == $scope.variant - 1 && remainder_qty) {
					quantity = side_qty + remainder_qty
				}
				if(!uploadOption.fileName) {
					can_add_to_cart = false;
				}
				_uploadOptions.push({...uploadOption, quantity: quantity});
			})

			if(can_add_to_cart) {
				jQuery('.single_add_to_cart_button').prop('disabled', false);
				jQuery('.kita-add-a-quote-button').prop('disabled', false);
			} else {
				jQuery('.single_add_to_cart_button').prop('disabled', true);
				jQuery('.kita-add-a-quote-button').prop('disabled', true);
			}

			return _uploadOptions;
		}

		$scope.uploadFile = function(files, index) {
			var file = files[0];
			var file_name = file.name;
			if(file_name.length > 20) {
				file_name = '...'+file_name.substr(file_name.length - 20, file_name.length);
			}

			$scope.uploadOptions[index].fileName = file_name;

			var can_add_to_cart = true;

			angular.forEach($scope.uploadOptions, function(uploadOption) {
				if(!uploadOption.fileName) {
					can_add_to_cart = false;
				}
			})

			if(can_add_to_cart) {
				jQuery('.single_add_to_cart_button').prop('disabled', false);
				jQuery('.kita-add-a-quote-button').prop('disabled', false);
			}

			$scope.updateApp();
		}

		$scope.init();
	}).directive("nbdDndFile", ['$timeout', function($timeout) {
	    return {
	        restrict: "A",
	        scope: {
	            uploadFile: '&nbdDndFile'
	        },
	        link: function(scope, element) {
	            $timeout(function() {
	                var dropArea = jQuery(element),
	                Input = dropArea.find('input[type="file"]');

	                dropArea.on("dragover", function(event) {
					    event.preventDefault();  
					    event.stopPropagation();
					});

					dropArea.on("dragleave", function(event) {
					    event.preventDefault();  
					    event.stopPropagation();
					});
	                dropArea.on("drop", function(event) {
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
					Input.on('change', function(){
	                    handleFiles(this.files);
	                });
	                function handleFiles(files) {
	                    if(files.length > 0) scope.uploadFile({files: files});
	                }
	            });
	        }
	    };
	}]);
</script>

<?php get_footer(); ?>

