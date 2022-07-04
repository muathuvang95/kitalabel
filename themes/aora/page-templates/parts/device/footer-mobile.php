<?php
    /**
     * aora_before_topbar_mobile hook
     */
    do_action( 'aora_before_footer_mobile' );
?>
<div class="footer-device-mobile d-xl-none clearfix">

    <?php
        /**
        * aora_before_footer_mobile hook
        */
        do_action( 'aora_before_footer_mobile' );

        /**
        * Hook: aora_footer_mobile_content.
        *
        * @hooked aora_the_custom_list_menu_icon - 10
        */

        do_action( 'aora_footer_mobile_content' );

        /**
        * aora_after_footer_mobile hook
        */
        do_action( 'aora_after_footer_mobile' );
    ?>

</div>