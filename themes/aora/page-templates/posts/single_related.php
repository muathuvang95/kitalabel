<?php 

$text_domain               = esc_html__(' comments','aora');    
$thumbsize       = isset($thumbnail_size_size) ? $thumbnail_size_size : 'medium';
if( get_comments_number() == 1) {
    $text_domain = esc_html__(' comment','aora');
}

?>
<div class="post item-post single-reladted">   
    <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
        <a href="<?php the_permalink(); ?>"  class="entry-image">
            <?php
                if ( aora_elementor_is_activated() ) {
                    the_post_thumbnail($thumbsize);
                    aora_tbay_icon_post_formats(); 
                } else {
                    the_post_thumbnail();
                    aora_tbay_icon_post_formats(); 
                }
            ?>
        </a> 
    </figure>
    <div class="entry-header">

        <?php if ( get_the_title() ) : ?>

            <ul class="entry-meta-list">
                <li class="entry-date"><i class="tb-icon tb-icon-clock"></i><?php echo aora_time_link(); ?></li>
                <?php if(get_the_category_list()) {
                    ?>
                    <li class="entry-category"><?php aora_the_post_category_full() ?></li>
                <?php } ?>
            </ul>
                        
            <h3 class="entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
        <?php endif; ?>
        

    </div>
</div>
