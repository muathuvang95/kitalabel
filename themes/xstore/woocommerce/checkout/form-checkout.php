<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php wc_print_notices(); ?>

    <div class="before-checkout-form">
		<?php
		do_action( 'woocommerce_before_checkout_form', $checkout );
		?>
    </div>
<?php

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'xstore' ) ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <div class="row">
            <div class="col-md-7 clearfix">
				<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
					
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                    <div id="customer_details">

                        <div class="col-1">
							
							<?php do_action( 'woocommerce_checkout_billing' ); ?>

                        </div>

                        <div class="col-2">
							
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>

                        </div>

                    </div>
					
					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				
				<?php endif; ?>
            </div>

            <div class="col-md-5 cart-order-details">
                <div class="order-review">
                    <h3 class="step-title"><span><?php esc_html_e( 'Your order', 'xstore' ); ?></span></h3>
					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    </div>
					
					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                </div>
            </div>
        </div>

    </form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>