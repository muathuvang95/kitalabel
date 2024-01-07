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

$the_query = new WP_Query( $args );
wp_reset_postdata();

$appid              = "kita-app-" . time() . rand( 1, 1000 );
$option_id 			= kitalabel_get_product_option($product_id);
$uploadConfig 			= array();
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
					$option_selected[$field_id] = array(
						'title'			=> $field['general']['title'],
						'value' 		=> $value,
						'show' 			=> true,
						'value_name'	=> $option_field[$value]['name'],
					);
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
																								<input value="<?php echo $field_option['value']; ?>" name="nbd-field[<?php echo $field_id; ?>]" type="hidden" >
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
																	<a href="<?php echo home_url() . '/order-label'; ?>" class="button">
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

