<?php
/**
 * Portfolio single media template part
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get portfolio video
$video = wpex_get_portfolio_post_video();

// Get portfolio attachment ( gallery images )
$attachments = wpex_get_gallery_ids( get_the_ID() );

// Get portfolio thumbnail
$thumbnail = ( ! $video ) ? wpex_get_portfolio_post_thumbnail() : ''; ?>

<div id="portfolio-single-media" class="wpex-clr">

    <?php
    // Display slider if there are $attachments
    if ( $attachments ) :

        get_template_part( 'partials/portfolio/portfolio-single-gallery' );

    // Display Post Video if defined
    elseif ( $video ) : ?>
    
        <?php echo $video; ?>
    
    <?php
    // Otherwise display post thumbnail
    elseif ( $thumbnail ) : ?>

        <?php
        // Load lightbox styles
        wpex_enqueue_ilightbox_skin(); ?>

        <a href="<?php wpex_lightbox_image(); ?>" title="<?php wpex_esc_title(); ?>" class="wpex-lightbox">
            <?php echo $thumbnail; ?>
        </a>

    <?php endif; ?>

</div><!-- .portfolio-entry-media -->