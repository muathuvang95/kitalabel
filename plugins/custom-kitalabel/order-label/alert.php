<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style type="text/css">
    .kita-back-home {
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
        background: #FF8900;
        color: #fff;
        border: 1px solid #FF8900;
        text-transform: capitalize;
        font-weight: 600;
    }
    .nbd-alert-body {
        min-width: 450px;
    }
    .nbd-alert-body .nbd-alert-wrapper {
        text-align: center;
    }
    .nbd-alert-body .nbd-alert-wrapper p {
        margin-bottom: 0;
        display: inline-block;
    }
    .nbd-alert-body .kita-quote-title {
        margin: 20px 0 10px 0;
    }
    .nbd-alert .close-popup {
        background-color: #FF8900;
    }
</style>
<div class="nbd-alert" id="nbdq-alert-popup" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="nbd-alert-head">
            <i class="close-popup">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>close</title>
                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                </svg>
            </i>
        </div>
        <div class="nbd-alert-body">
            <div class="nbd-alert-wrapper">
                <div>
                    <img src="<?php echo CUSTOM_KITALABEL_URL . 'order-label/quote-alert.svg' ?>" alt="quote-alert">
                </div>
                <h4 class="kita-quote-title"><?php _e( 'Special Label Request.', 'web-to-print-online-designer' ); ?></h4>
                <div>We will process your special label request. We <span style="color: #FF8900">will contact you via the email</span> associated with your account. check email regularly.</div>
                <div class="kita-alert-action">
                    <a class="button kita-back-home" href="<?php echo home_url();?>"><?php _e('Understand', 'web-to-print-online-designer'); ?></a>  
                </div>
            </div>
        </div>
    </div>
</div>
