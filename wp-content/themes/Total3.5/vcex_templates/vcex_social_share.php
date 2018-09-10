<?php
/**
 * Visual Composer Social Share
 *
 * @package Total WordPress Theme
 * @subpackage VC Templates
 * @version 4.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Helps speed up rendering in backend of VC
if ( is_admin() && ! wp_doing_ajax() ) {
	return;
}

if ( ! function_exists( 'vc_map_get_attributes' ) || ! function_exists( 'vc_param_group_parse_atts' ) ) {
	vcex_function_needed_notice();
	return;
}

$atts = vc_map_get_attributes( 'vcex_social_share', $atts );

$sites = ! empty( $atts['sites'] ) ? (array) vc_param_group_parse_atts( $atts['sites'] ) : '';

if ( ! $sites ) {
	return;
}

$output = '';

// Singular data
if ( $post_id = wpex_get_current_post_id() ) {
	$title = html_entity_decode( wpex_get_esc_title() );
}

// Most likely an archive
else {
	$title   = get_the_archive_title();
	$summary = get_the_archive_description();
}

$url = apply_filters( 'wpex_social_share_url', wpex_get_current_url() );

$wrap_classes = 'wpex-social-share-wrap clr position-horizontal';
if ( isset( $atts['visibility'] ) ) {
	$wrap_classes .= ' ' .  $atts['visibility'];
} ?>

<div class="<?php echo $wrap_classes; ?>">

	<ul class="wpex-social-share position-horizontal style-<?php echo esc_attr( $atts['style'] ); ?> clr">

		<?php
		// Loop through sites
		foreach ( $sites as $k => $v ) :

			// Twitter
			if ( 'twitter' == $v['site'] ) {

				// Get SEO meta and use instead if they exist
				if ( defined( 'WPSEO_VERSION' ) ) {
					if ( $meta = get_post_meta( $post_id, '_yoast_wpseo_twitter-title', true ) ) {
						$title = $meta;
					}
					if ( $meta = get_post_meta( $post_id, '_yoast_wpseo_twitter-description', true ) ) {
						$title = $title .': '. $meta;
						$title = $title;
					}
				}

				// Get twitter handle
				$handle = wpex_get_mod( 'social_share_twitter_handle' ); ?>

				<li class="share-twitter">
					<a href="https://twitter.com/share?text=<?php echo rawurlencode( $title ); ?>&amp;url=<?php echo rawurlencode( esc_url( $url ) ); ?><?php if ( $handle ) echo '&amp;via='. esc_attr( $handle ); ?>" title="<?php esc_html_e( 'Share on Twitter', 'total' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-twitter"></span>
						<span class="social-share-button-text"><?php esc_html_e( 'Tweet', 'total' ); ?></span>
					</a>
				</li>

			<?php }
			// Facebook
			elseif ( 'facebook' == $v['site'] ) { ?>

				<li class="share-facebook">
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( esc_url( $url ) ); ?>" title="<?php esc_html_e( 'Share on Facebook', 'total' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-facebook"></span>
						<span class="social-share-button-text"><?php esc_html_e( 'Share', 'total' ); ?></span>
					</a>
				</li>

			<?php }
			// Google+
			elseif ( 'google_plus' == $v['site'] ) { ?>

				<li class="share-googleplus">
					<a href="https://plus.google.com/share?url=<?php echo rawurlencode( esc_url( $url ) ); ?>" title="<?php esc_html_e( 'Share on Google+', 'total' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-google-plus"></span>
						<span class="social-share-button-text"><?php esc_html_e( 'Plus one', 'total' ); ?></span>
					</a>
				</li>

			<?php }
			// Pinterest
			elseif ( 'pinterest' == $v['site'] ) {

				$summary = wp_strip_all_tags( wpex_get_excerpt( array(
					'post_id'         => $post_id,
					'length'          => '40',
					'echo'            => false,
					'ignore_more_tag' => true,
					'more'            => '',
					'context'         => 'social_share',
				) ) ); ?>

				<li class="share-pinterest">
					<a href="https://www.pinterest.com/pin/create/button/?url=<?php echo rawurlencode( esc_url( $url ) ); ?>&amp;media=<?php echo wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ); ?>&amp;description=<?php echo rawurlencode( $summary ); ?>" title="<?php esc_html_e( 'Share on Pinterest', 'total' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-pinterest"></span>
						<span class="social-share-button-text"><?php esc_html_e( 'Pin It', 'total' ); ?></span>
					</a>
				</li>

			<?php }
			// LinkedIn
			elseif ( 'linkedin' == $v['site'] ) {

				$summary = wp_strip_all_tags( wpex_get_excerpt( array(
					'post_id'         => $post_id,
					'length'          => '40',
					'echo'            => false,
					'ignore_more_tag' => true,
					'more'            => '',
					'context'         => 'social_share',
				) ) ); ?>

				<li class="share-linkedin">
					<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo rawurlencode( esc_url( $url ) ); ?>&amp;title=<?php echo rawurlencode( $title ); ?>&amp;summary=<?php echo rawurlencode( $summary ); ?>&amp;source=<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_html_e( 'Share on LinkedIn', 'total' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-linkedin"></span>
						<span class="social-share-button-text"><?php esc_html_e( 'Share', 'total' ); ?></span>
					</a>
				</li>

			<?php } ?>

		<?php endforeach; ?>

	</ul>

</div>