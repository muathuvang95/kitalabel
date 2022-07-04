<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="u-columns row justify-content-center" id="customer_login">
	<div class="log-form col-lg-4 m-auto">
		<ul class="nav nav-tabs" role="tablist">
		    <li role="presentation"><a href="#login" aria-controls="login" role="tab" class="active" data-toggle="tab"><?php esc_html_e('Login', 'aora'); ?></a></li>

		    <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
			    <li role="presentation"><a href="#register" aria-controls="register" role="tab" data-toggle="tab"><?php esc_html_e('Register', 'aora'); ?></a></li>
			<?php endif; ?>
	  	</ul>
		<div class="tab-content">
	    	<div role="tabpanel" class="tab-pane active" id="login">

				<form id="login" class="woocommerce-form woocommerce-form-login login" method="post">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<span class="sub-title"><?php esc_html_e( 'Enter your username and password to login.', 'aora' ); ?></span>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" placeholder="<?php esc_attr_e('Username or email', 'aora'); ?>" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" placeholder="<?php esc_attr_e('Password', 'aora'); ?>" autocomplete="current-password" />
					</p>
					<?php do_action( 'woocommerce_login_form' ); ?>
					<p class="d-flex justify-content-between pt-1">
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'aora' ); ?></span>
						</label>
						<span class="woocommerce-LostPassword lost_password">
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost password?', 'aora' ); ?></a>
						</span>
					</p>
					<p class="form-row last">
						<button type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Login', 'aora' ); ?>"><?php esc_html_e( 'Login', 'aora' ); ?></button>
					</p>
					
					
				</form>

				<?php if( class_exists('NextendSocialLogin') ) { ?>
					<div class="log-with-social">
						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</div>
				<?php } ?>

				<div class="no-account">
					<p><?php esc_html_e('Don\'t have an account', 'aora'); ?></p>
					<a href="#" class="kita-sign-up-now-link"><?php esc_html_e('Sign up now', 'aora'); ?></a>
				</div>
			</div>

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
			<div role="tabpanel" class="tab-pane" id="register">

				<form id="register" method="post" class="woocommerce-form woocommerce-form-register register">

					<span class="sub-title"><?php esc_html_e( 'Enter your email and password to register.', 'aora' ); ?></span>
					
					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" placeholder="<?php esc_attr_e('Username', 'aora'); ?>"autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
						</p>

					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" placeholder="<?php esc_attr_e('Email address', 'aora'); ?>" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" placeholder="<?php esc_attr_e('Password', 'aora'); ?>" autocomplete="new-password" />
						</p>

	      			<?php else : ?>
	      
	      				<p><?php esc_html_e( 'A password will be sent to your email address.', 'aora' ); ?></p>
	      
	      			<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="woocommerce-form-row form-row last">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'aora' ); ?>"><?php esc_html_e( 'Register', 'aora' ); ?></button>
					</p>
					
					<?php do_action('woocommerce_after_button_resgiter'); ?>
				</form>

				<?php if( class_exists('NextendSocialLogin') ) { ?>
					<div class="log-with-social">
						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</div>
				<?php } ?>

				<div class="no-account">
					<p><?php esc_html_e('Already have an account', 'aora'); ?></p>
					<a href="#" class="kita-sign-in-now-link"><?php esc_html_e('Sign in now', 'aora'); ?></a>
				</div>
			</div>
		<?php endif; ?>
		</div>

		
	</div>
	
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
