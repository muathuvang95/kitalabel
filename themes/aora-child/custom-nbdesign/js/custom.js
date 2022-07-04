// jQuery(document).ready(function($) {
//     var is_blocked = function( $node ) {
//         return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
//     };

//     /**
//      * Block a node visually for processing.
//      *
//      * @param {JQuery Object} $node
//      */
//     var block = function( $node ) {
//         if ( ! is_blocked( $node ) ) {
//             $node.addClass( 'processing' ).block( {
//                 message: null,
//                 overlayCSS: {
//                     background: '#fff',
//                     opacity: 0.6
//                 }
//             } );
//         }
//     };

//     /**
//      * Unblock a node after processing is complete.
//      *
//      * @param {JQuery Object} $node
//      */
//     var unblock = function( $node ) {
//         $node.removeClass( 'processing' ).unblock();
//     };
//     $('.nb-custom-qty-side').change( function(e) {
//         var min_qty = parseInt($(this).data('min-qty'));
//         var item_key = $(this).data('item-key');
//         var $a = $( e.currentTarget );
//         var $form = $a.parents( 'form' );
//         var qty_sum = 0;
//         jQuery('input[data-item-key='+item_key+']').each(function(key , val) {
//             var qty_side = jQuery(val).val();
//             qty_sum += parseInt(qty_side);
//         })
//         var qty_old = min_qty - qty_sum + parseInt($(this).val());
//         block($form);
//         if(qty_sum >= min_qty) {
//             $.ajax({
//                 type : "post", 
//                 dataType : "json", 
//                 url : nb_custom.url,
//                 data : {
//                     action: "nb_ajax_qty_cart",
//                     min_qty : min_qty,
//                     item_key : item_key,
//                     data:     $form.serialize(),
//                 },
//                 context: this,
//                 success: function(response) {
//                     if(response.success) {
//                         if(response.data.flag) {
//                             $('input[name="cart['+item_key+'][qty]"]').parent().parent().parent().find('.product-price').html(response.data.price_item);
//                             $('input[name="cart['+item_key+'][qty]"]').parent().parent().parent().find('.product-subtotal.price').html(response.data.wc_price);
//                             $('input[name="cart['+item_key+'][qty]"]').val(response.data.qty);
//                             $('.cart-subtotal>td').html(response.data.cart_subtotal);
//                             $('.order-total>td').html(response.data.cart_total);
//                             unblock($form);
//                         } else {
//                             unblock($form);
//                             alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
//                         }
//                     }
//                 },
//                 error: function( jqXHR, textStatus, errorThrown ){
//                     console.log( 'The following error occured: ' + textStatus, errorThrown );
//                 }
//             })
//         } else {
//             $(this).val(qty_old);
//             unblock($form);
//             alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
//         }
        
//     })
//     $('.nb-wrap-comment .nb-note textarea.textarea-note').change( function() {
//         var item_key = $(this).data('key');
//         var content = $(this).val();
//         var $form = $('form');
//          block($form);
//         $.ajax({
//             type : "post", 
//             dataType : "json", 
//             url : nb_custom.url,
//             data : {
//                 action: "nb_custom_processing_cart",
//                 item_key : item_key,
//                 content : content
//             },
//             context: this,
//             success: function(response) {
//                 if(response.success) {
//                     if(response.data.flag) {
//                         unblock($form);
//                     } else {
//                         unblock($form);
//                     }
//                 }
//             },
//             error: function( jqXHR, textStatus, errorThrown ){
//                 console.log( 'The following error occured: ' + textStatus, errorThrown );
//             }
//         })  
//     });
//     $('.cs-input-checkbox input[type="checkbox"]').click( function() {
//         var item_key = $(this).data('key');
//         var roll_form = $('#roll-form-'+item_key+':checked').val();
//         var $form = $('form');
//         if( typeof roll_form == 'undefined' ) roll_form = 'off';
//          block($form);
//         $.ajax({
//             type : "post", 
//             dataType : "json", 
//             url : nb_custom.url,
//             data : {
//                 action: "nb_custom_processing_cart",
//                 item_key : item_key,
//                 roll_form : roll_form
//             },
//             context: this,
//             success: function(response) {
//                 if(response.success) {
//                     if(response.data.flag) {
//                         unblock($form);
//                     } else {
//                         unblock($form);
//                     }
//                 }
//             },
//             error: function( jqXHR, textStatus, errorThrown ){
//                 console.log( 'The following error occured: ' + textStatus, errorThrown );
//             }
//         })  
//     });
//     $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').mouseover(function() {
//         $(this).addClass('active');
//     })
//     $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').mouseleave(function (e) {
//         $('.nb-wrap-comment .nb-roll-form .nb-cs-help-tip').removeClass('active');
//     });
// })
// function kita_change_qty(e) {
//     var min_qty = parseInt(e.getAttribute('data-min-qty'));
//     var qty = parseInt(e.value );
//     console.log(qty, min_qty);
//     if( qty < min_qty ) {
//         e.value = min_qty;
//         alert(" Can't set quantity for each the side less than the min quantity: " + min_qty);
//     }
// }

jQuery(document).ready(function($) {
    $('.kita_login_popup_wrapper button.woocommerce-Button[name="login"]').on('click', function(e) {
        $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').removeClass('error');
        $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').addClass('loading');
        $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').text('Sending user info, please wait...');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url : nb_custom.url,
            data: { 
                'action': 'nb_custom_login_ajax',
                'username': $('.kita_login_popup_wrapper input#username[name="username"]').val(), 
                'password': $('.kita_login_popup_wrapper input#password[name="password"]').val(),
                'rememberme': $('.kita_login_popup_wrapper input#password[name="rememberme"]').val(),
                'woocommerce-login-nonce': $('.kita_login_popup_wrapper input#woocommerce-login-nonce[name="woocommerce-login-nonce"]').val(),
            },
            context: this,
            success: function(data){
                $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').removeClass('loading');
                if (data.data.loggedin == true){
                    $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').removeClass('loading');
                    $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').addClass('success');
                    $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').html(data.data.message);
                    $('.kita_login_popup_form').fadeOut();
                    $('.wrapper-container').removeClass('kita-popup-show');
                    document.location.href = nb_custom.homepage;
                } else {
                    $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').addClass('error');
                    $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').html(data.data.message);
                }
            },
            error: function( jqXHR, textStatus, errorThrown ){
                $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').removeClass('loading');
                $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').addClass('error');
                $('.kita_login_popup_wrapper .woocommerce-notices-wrapper').text('Login error.');
            }
        });
        e.preventDefault();
    });
});