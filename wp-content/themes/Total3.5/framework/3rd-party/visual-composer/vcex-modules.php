<?php
/**
 * Visual Composer Custom Modules
 *
 * @package Total WordPress Theme
 * @subpackage Visual Composer
 * @version 4.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPEX_Visual_Composer_Extension' ) ) {

	class WPEX_Visual_Composer_Extension {

		/**
		 * Start things up
		 *
		 * @since 4.5
		 */
		public function __construct() {
			add_action( 'vc_after_mapping', array( $this, 'load_classes' ), 0 );
		}

		/**
		 * Load custom module classes
		 *
		 * @since 4.5
		 */
		public function load_classes() {
			
			$modules = array(

				'shortcode',
				'spacing',
				'divider',
				'divider_dots',
				'divider_multicolor',
				'heading',
				'button',
				'multi_buttons',
				'leader',
				'animated_text',
				'icon_box',
				'teaser',
				'feature',
				'list_item',
				'bullets',
				'pricing',
				'skillbar',
				'icon',
				'milestone',
				'countdown',
				
				'image',
				'image_banner',
				'image_swap',
				'image_before_after',
				'image_galleryslider',
				'image_flexslider',
				'image_carousel',
				'image_grid',
				
				'recent_news',
				'blog_grid',
				'blog_carousel',
				
				'post_type_grid',
				'post_type_archive',
				'post_type_slider',
				'post_type_carousel',

				'callout',
				
				'post_terms',
				'terms_grid',
				'terms_carousel',
				
				'users_grid',
				'social_links',
				'navbar',
				'searchbar',
				'login_form',
				'form_shortcode',
				'newsletter_form',

				'breadcrumbs',
				'post_media',
				'post_meta',
				'post_comments',
				'post_content',
				'post_next_prev',
				'social_share',

				'grid_item-post_video',
				'grid_item-post_meta',
				'grid_item-post_excerpt',
				'grid_item-post_terms',

			);

			if ( class_exists( 'Clever_Mega_Menu' ) ) {
				//$modules[] = 'clever-mega-menu'; @todo
			}
			
			$modules = apply_filters( 'vcex_builder_modules', $modules );

			if ( ! empty( $modules ) ) {
				foreach ( $modules as $key => $val ) {
					if ( is_array( $val ) ) {
						$condition = isset( $val['condition'] ) ? $val['condition'] : true;
						$file      = isset( $val['file'] ) ? $val['file'] : WPEX_VCEX_DIR . 'shortcodes/' . $key . '.php';
						if ( $condition ) {
							require_once $file;
						}
					} else {
						$file = WPEX_VCEX_DIR . 'shortcodes/' . $val . '.php';
						require_once $file;
					}
				}
			}

		}

	}

}
new WPEX_Visual_Composer_Extension;