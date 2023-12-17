jQuery( function( $ ) {
  'use strict';

  class ProductItem {
    initAddButtonQuantity() {
      
    }

    // custom kitalabel
    kitaCustomInitAddButtonQuantity() {

    }

    initOnChangeQuantity(callback) {
      this.initAddButtonQuantity();

      //custom kitalabel
      var is_blocked = function( $node ) {
          return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
      };

      /**
       * Block a node visually for processing.
       *
       * @param {JQuery Object} $node
       */
      var block = function( $node ) {
          if ( ! is_blocked( $node ) ) {
              $node.addClass( 'processing' ).block( {
                  message: null,
                  overlayCSS: {
                      background: '#fff',
                      opacity: 0.6
                  }
              } );
          }
      };

      /**
       * Unblock a node after processing is complete.
       *
       * @param {JQuery Object} $node
       */
      var unblock = function( $node ) {
          $node.removeClass( 'processing' ).unblock();
      };

      var kita_refresh_fragments = function () {
          jQuery( "body.woocommerce-cart [name='update_cart']" ).removeAttr( 'disabled' );
          jQuery( "body.woocommerce-cart [name='update_cart']" ).trigger( 'click' );
      }

      $('.nb-wrap-comment .nb-note textarea.textarea-note').change( function() {
          var item_key = $(this).data('key');
          var content = $(this).val();
          var $form = $('form');
           block($form);
          $.ajax({
              type : "post", 
              dataType : "json", 
              url : nb_custom.url,
              data : {
                  action: "nb_custom_processing_cart",
                  item_key : item_key,
                  content : content
              },
              context: this,
              success: function(response) {
                  if(response.success) {
                      if(response.data.flag) {
                          unblock($form);
                      } else {
                          unblock($form);
                      }
                  }
              },
              error: function( jqXHR, textStatus, errorThrown ){
                  console.log( 'The following error occured: ' + textStatus, errorThrown );
              }
          })  
      });
      $('form.woocommerce-cart-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
          e.preventDefault();
          return false;
        }
      })
      $('.cs-input-checkbox input[type="checkbox"]').click( function() {
          var item_key = $(this).data('key');
          var roll_form = $('#roll-form-'+item_key+':checked').val();
          var $form = $('form');
          if( typeof roll_form == 'undefined' ) roll_form = 'off';
           block($form);
          $.ajax({
              type : "post", 
              dataType : "json", 
              url : nb_custom.url,
              data : {
                  action: "nb_custom_processing_cart",
                  item_key : item_key,
                  roll_form : roll_form
              },
              context: this,
              success: function(response) {
                  if(response.success) {
                      if(response.data.flag) {
                          unblock($form);
                      } else {
                          unblock($form);
                      }
                  }
              },
              error: function( jqXHR, textStatus, errorThrown ){
                  console.log( 'The following error occured: ' + textStatus, errorThrown );
              }
          })  
      });
      $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').mouseover(function() {
          $(this).addClass('active');
      })
      $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').mouseleave(function (e) {
          $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').removeClass('active');
      });

      this.kitaCustomInitAddButtonQuantity();

      var sefl = this;
      $('.nb-custom-qty-side').change( function(e) {
        var min_qty = parseInt($(this).data('min-qty'));
        var item_key = $(this).data('item-key');
        var $a = $( e.currentTarget );
        var $form = $a.parents( 'form' );
        var qty_sum = 0;
        jQuery('input[data-item-key='+item_key+']').each(function(key , val) {
            var qty_side = jQuery(val).val();
            qty_sum += parseInt(qty_side);
        })
        var qty_old = min_qty - qty_sum + parseInt($(this).val());
        if(qty_sum >= min_qty) {
          block($form);
          $.ajax({
              type : "post", 
              dataType : "json", 
              url : nb_custom.url,
              data : {
                action: "kitalabel_ajax_qty_cart",
                min_qty : min_qty,
                item_key : item_key,
                data:     $form.serialize(),
              },
              context: this,
              success: function(response) {
                if(response.success) {
                  if(response.data.flag) {
                    unblock($form);
                    kita_refresh_fragments();
                    $(sefl).parent().find('input').trigger("change");
                  } else {
                    alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
                    unblock($form);
                  }
                }
              },
              error: function( jqXHR, textStatus, errorThrown ){
                console.log( 'The following error occured: ' + textStatus, errorThrown );
              }
          })
        } else {
          $(this).val(qty_old);
          alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
        }
          
      });

      $('.trash-icon-upload-design').on('click', function(e) {
        e.preventDefault();
        var input_el = $(this).parent().parent().find('input.nb-custom-qty-side');
        var item_key = input_el.data('item-key');
        var min_qty = input_el.data('min-qty');
        var design_index = $(this).data('design-index');
        var $a = $( e.currentTarget );
        var $form = $a.parents( 'form' );

        // check total quantity
        var qty_sum = 0;
        jQuery('input[data-item-key='+item_key+']').each(function(key , val) {
            if(design_index != key) {
                var qty_side = jQuery(val).val();
                qty_sum += parseInt(qty_side);
            }
        })

        if(qty_sum < min_qty) {
            return alert("You cannot delete this side, change the total number of remaining sides to be greater than: " + min_qty + " to continue." );
        }

        block($form);

        $.ajax({
            type : "post", 
            dataType : "json", 
            url : nb_custom.url,
            data : {
              action: "kitalabel_delete_upload_design_cart",
              design_index : design_index,
              item_key : item_key,
            },
            context: this,
            success: function(response) {
              if(response.success) {
                if(response.data.flag) {
                  unblock($form);
                  kita_refresh_fragments();
                } else {
                  alert("You cannot delete this side, change the total number of remaining sides to be greater than: " + min_qty + " to continue." );
                  unblock($form);
                }
              }
            },
            error: function( jqXHR, textStatus, errorThrown ){
              console.log( 'The following error occured: ' + textStatus, errorThrown );
            }
        })

      })

      $('.edit-upload-design').on('click', function(e) {
        e.preventDefault();
        $(this).parent().find('input[type="file"]').click();
      });

      $('.nb-cart_item_design input[type="file"].kita-upload-file').on('change', function(e) {
        e.preventDefault();
        var files = this.files;
        var input_el = $(this).parent().parent().find('input.nb-custom-qty-side');
        var item_key = input_el.data('item-key');
        var design_index = $(this).data('design-index');
        var $a = $( e.currentTarget );
        var $form = $a.parents( 'form' );


        block($form);

        var formData = new FormData();
        formData.append("action", "kitalabel_edit_upload_design_cart");
        formData.append("design_index", design_index);
        formData.append("item_key", item_key);
        formData.append("file", files[0]);

        $.ajax({
            type : "post", 
            processData: false,
            contentType: false,
            url : nb_custom.url,
            data : formData,
            context: this,
            success: function(response) {
              if(response.success) {
                if(response.data.flag) {
                  unblock($form);
                  kita_refresh_fragments();
                } else {
                  alert("Something went wrong!" );
                  unblock($form);
                }
              }
            },
            error: function( jqXHR, textStatus, errorThrown ){
              console.log( 'The following error occured: ' + textStatus, errorThrown );
            }
        })

      })

      $('.button.show-add-upload-design').on('click', function(e) {
        e.preventDefault();
        $('.tambah-variant-options').show();
      });

      $('.button.cancel-add-upload-design').on('click', function(e) {
        e.preventDefault();
        $('.tambah-variant-options').hide();
      });



      $('.tambah-variant-options .add-upload-design').on('click', function(e) {
        e.preventDefault();

        var parent_el = $(this).parent().parent();
        var files = parent_el.find('input[name="variant-file"]').prop('files');
        var name = parent_el.find('input[name="variant-name"]').val();
        var qty = parent_el.find('input[name="variant-qty"]').val();
        var item_key = $(this).data('item-key');

        if(!files.length) {
          return alert("Invalid file!" );
        }

        if(!name) {
          return alert("Invalid name!" );
        }

        if(!qty) {
          return alert("Invalid quantity!" );
        }

        var $a = $( e.currentTarget );
        var $form = $a.parents( 'form' );


        block($form);

        var formData = new FormData();
        formData.append("action", "kitalabel_add_upload_design_cart");
        formData.append("qty", qty);
        formData.append("item_key", item_key);
        formData.append("name", name);
        formData.append("file", files[0]);

        $.ajax({
            type : "post", 
            processData: false,
            contentType: false,
            url : nb_custom.url,
            data : formData,
            context: this,
            success: function(response) {
              if(response.success) {
                if(response.data.flag) {
                  unblock($form);
                  kita_refresh_fragments();
                } else {
                  alert("Something went wrong!" );
                  unblock($form);
                }
              }
            },
            error: function( jqXHR, textStatus, errorThrown ){
              console.log( 'The following error occured: ' + textStatus, errorThrown );
            }
        })

      })

      $(document).off('click', '.nb-plus, .nb-minus').on('click', '.nb-plus, .nb-minus', function (event) {
        event.preventDefault();
        var qty = jQuery(this).closest('.nbu-quantity').find('.qty'),
            currentVal = parseFloat(qty.val()),
            max = $(qty).attr("max"),
            min = $(qty).data("min-qty"),
            item_key = $(qty).data("item-key"),
            step = $(qty).attr("step");
        currentVal = !currentVal || currentVal === '' || currentVal === 'NaN' ? 0 : currentVal;
        max = max === '' || max === 'NaN' ? '' : max;
        min = min === '' || min === 'NaN' ? 0 : min;
        step = step === 'any' || step === '' || step === undefined || parseFloat(step) === NaN ? 1 : step;

        if ($(this).is('.nb-plus')) {
          if (max && (max == currentVal || currentVal > max)) {
            qty.val(max);
          } else {
            qty.val(currentVal + parseFloat(step));
          }
        } else {
          if (min && (min == currentVal || currentVal < min)) {
            qty.val(min);
            alert(" Can't set quantity for each the side less than the min quantity: " + min);
          } else if (currentVal > 0) {
            qty.val(currentVal - parseFloat(step));
          }
        }
        if (callback && typeof callback == "function") {
          $(sefl).parent().find('input').trigger("change");
          callback();
        }
      });
      $('.nb-input-custom-upload').change( function() {
        var qty = $(this).val();
        var $form = $(this).parents( 'form' );
        var item_key = $(this).data("item-key");
        var min_qty = $(this).data("min-qty");
        if( qty < min_qty ) {
          $(this).val(min_qty);
          alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
        }
        if ( callback && typeof callback == "function" ) {
          $(sefl).parent().find('input').trigger("change");
          callback();
        }
      })

      $(document).off('click', '.plus, .minus').on('click', '.plus, .minus', function (event) {
        event.preventDefault();
        var qty = jQuery(this).closest('.quantity').find('.qty'),
            currentVal = parseFloat(qty.val()),
            max = $(qty).attr("max"),
            min = $(qty).attr("min"),
            step = $(qty).attr("step");
        currentVal = !currentVal || currentVal === '' || currentVal === 'NaN' ? 0 : currentVal;
        max = max === '' || max === 'NaN' ? '' : max;
        min = min === '' || min === 'NaN' ? 0 : min;
        step = step === 'any' || step === '' || step === undefined || parseFloat(step) === NaN ? 1 : step;

        if ($(this).is('.plus')) {
          if (max && (max == currentVal || currentVal > max)) {
            qty.val(max);
          } else {
            qty.val(currentVal + parseFloat(step));
          }
        } else {
          if (min && (min == currentVal || currentVal < min)) {
            qty.val(min);
          } else if (currentVal > 0) {
            qty.val(currentVal - parseFloat(step));
          }
        }

        if (callback && typeof callback == "function") {
          $(this).parent().find('input').trigger("change");
          callback();
        }
      });
    }

    _initSwatches() {
      if ($('.tbay-swatches-wrapper li a').length === 0) return;
      $('body').on('click', '.tbay-swatches-wrapper li a', function (event) {
        event.preventDefault();
        let $active = false;
        let $parent = $(this).closest('.product-block');

        if ($parent.find('.tbay-product-slider-gallery').hasClass('slick-initialized')) {
          var $image = $parent.find('.image .slick-current img:eq(0)');
        } else {
          var $image = $parent.find('.image img:eq(0)');
        }

        if (!$(this).closest('ul').hasClass('active')) {
          $(this).closest('ul').addClass('active');
          $image.attr('data-old', $image.attr('src'));
        }

        if (!$(this).hasClass('selected')) {
          $(this).closest('ul').find('li a').each(function () {
            if ($(this).hasClass('selected')) {
              $(this).removeClass('selected');
            }
          });
          $(this).addClass('selected');
          $parent.addClass('product-swatched');
          $active = true;
        } else {
          $image.attr('src', $image.data('old'));
          $(this).removeClass('selected');
          $parent.removeClass('product-swatched');
        }

        if (!$active) return;

        if (typeof $(this).data('imageSrc') !== 'undefined') {
          $image.attr('src', $(this).data('imageSrc'));
        }

        if (typeof $(this).data('imageSrcset') !== 'undefined') {
          $image.attr('srcset', $(this).data('imageSrcset'));
        }

        if (typeof $(this).data('imageSizes') !== 'undefined') {
          $image.attr('sizes', $(this).data('imageSizes'));
        }
      });
    }

    _initQuantityMode() {
      $(".woocommerce .products").on("click", ".quantity .qty", function () {
        return false;
      });
      $(".woocommerce .products").on("change input", ".quantity .qty", function () {
        var add_to_cart_button = $(this).parents(".product").find(".add_to_cart_button");
        add_to_cart_button.attr("data-quantity", $(this).val());
      });
      $(".woocommerce .products").on("keypress", ".quantity .qty", function (e) {
        if ((e.which || e.keyCode) === 13) {
          $(this).parents(".product").find(".add_to_cart_button").trigger("click");
        }
      });
    }

  }

  class Cart {
  constructor() {
    this._initEventChangeQuantity();

    $(document.body).on('updated_wc_div', () => {
      this._initEventChangeQuantity();

      if (typeof wc_add_to_cart_variation_params !== 'undefined') {
        $('.variations_form').each(function () {
          $(this).wc_variation_form();
        });
      }
    });
    $(document.body).on('cart_page_refreshed', () => {
      this._initEventChangeQuantity();
    });
    $(document.body).on('tbay_display_mode', () => {
      this._initEventChangeQuantity();
    });
  }

  _initEventChangeQuantity() {
    if ($("body.woocommerce-cart [name='update_cart']").length === 0) {
      new ProductItem().initOnChangeQuantity(() => {});
    } else {
      new ProductItem().initOnChangeQuantity(() => {
        $('.woocommerce-cart-form :input[name="update_cart"]').prop('disabled', false);

          $("[name='update_cart']").trigger('click');
      });
    }
  }

}

  /*! Magnific Popup - v1.1.0 - 2016-02-20
  * http://dimsemenov.com/plugins/magnific-popup/
  * Copyright (c) 2016 Dmitry Semenov; */
  (function (factory) {
  if (typeof define === 'function' && define.amd) {
   define(['jquery'], factory);
   } else if (typeof exports === 'object') {
   factory(require('jquery'));
   } else {
   factory(window.jQuery || window.Zepto);
   }
   });


  jQuery(document).ready(() => {
    var product_item = new ProductItem();

    product_item._initSwatches();

    product_item._initQuantityMode();

    jQuery(document.body).trigger('tawcvs_initialized');
    new Cart();

  });

  var AddButtonQuantity = function ($scope, $) {
    var product_item = new ProductItem();
    product_item.initAddButtonQuantity();
  };

});