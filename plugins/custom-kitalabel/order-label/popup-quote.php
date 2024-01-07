<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style type="text/css">
    .nbdq-popup-body .button {
        display: block;
        text-align: center;
        margin: 0 auto;
        height: auto;
        font-size: initial;
        padding: 12px 8px;
        border-radius: 5px;
        font-style: unset;
        width: 100%;
        line-height: 1;
        background: #fff;
        color: #FF8900;
        border: 1px solid #FF8900;
        text-transform: capitalize;
        font-weight: 600;
        width: 50%;
    }
    .nbdq-popup-body .raq-send-request {
        display: inline-block;
        background-color: #FF8900;
        color: #ffffff;
    }
    .nbdq-popup-body .kita-back {
        margin-right: 10px;
    }
    .nbdq-popup-body .kita-footer {
        display: flex;
    }
</style>
<div class="nbdq-popup" id="nbdq-form-popup" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="nbdq-popup-head">
            <i class="close-popup" style="display: none">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>close</title>
                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                </svg>
            </i>
        </div>
        <div class="nbdq-popup-body">
            <div class="nbdq-notification">

            </div>
            <div class="nbdq-form-wrapper">
                <form id="nbdq-form" name="nbdq-form" >
                <?php
                    foreach ( $fields as $key => $field ) {
                        if ( isset( $field['enabled'] ) && $field['enabled'] ) {
                            woocommerce_form_field( $key, $field, NBD_Request_Quote()->get_form_value( $key, $field ) );
                        }
                    }
                    if ( ! is_user_logged_in() && 'yes' == $enable_registration ) :
                ?>
                <div class="woocommerce-account-fields">
                    <p class="form-row form-row-wide create-account">
                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                            <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" type="checkbox" name="createaccount" value="1"/>
                            <span><?php _e( 'Create an account?', 'web-to-print-online-designer' ); ?></span>
                        </label>
                    </p>
                </div>
                <div class="create-account">
                    <?php foreach ( $account_fields as $key => $field ) : ?>
                        <?php woocommerce_form_field( $key, $field, '' ); ?>
                    <?php endforeach; ?>
                    <div class="clear"></div>
                </div>
                <?php 
                    endif; 
                    if( nbdesigner_get_option('nbdesigner_enable_recaptcha_quote', 'no') == 'yes' && nbdesigner_get_option('nbdesigner_recaptcha_key', '') != '' && nbdesigner_get_option('nbdesigner_recaptcha_secret_key', '') != '' ):
                ?>
                <p class="form-row form-row form-row-wide">
                    <div class="g-recaptcha" id="recaptcha_quote" data-callback="nbdqRecaptchaCallback" data-sitekey="<?php echo nbdesigner_get_option('nbdesigner_recaptcha_key'); ?>"></div>
                </p>
                <?php endif; ?>
                <p class="form-row form-row-wide kita-footer">
                    <span class="kita-back button">
                        <?php _e( 'Back', 'web-to-print-online-designer' ); ?>
                    </span>
                    <input type="hidden" id="nbdq-mail-wpnonce" name="nbdq_mail_wpnonce" value="<?php echo wp_create_nonce( 'nbdq-form-request' ) ?>">
                    <input class="button raq-send-request" type="submit" value="<?php _e( 'Request Label', 'web-to-print-online-designer' ); ?>">
                </p>
            </form>
            </div>
        </div>
    </div>
</div>
