<?php
/**
 * Post meta (date, author, comments, etc) for custom post types.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 3.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get meta blocks
 *
 * @see   framework/core.php
 * @link  http://wpexplorer-themes.com/total/snippets/alter-meta-sections/
 * @since 3.2.0
 */
$blocks = wpex_single_meta_blocks();

// Make sure we have blocks and it's an array
if ( ! empty( $blocks ) && is_array( $blocks ) ) : ?>

	<ul class="meta clr">

		<?php
		// Loop through meta sections
		foreach ( $blocks as $block ) : ?>

			<?php if ( 'date' == $block ) : ?>
				<li class="meta-date"><span class="fa fa-clock-o"></span><time class="updated" datetime="<?php esc_attr( the_date( 'Y-m-d' ) ); ?>"<?php wpex_schema_markup( 'publish_date' ); ?>><?php echo get_the_date(); ?></time></li>
			<?php endif; ?>

			<?php if ( 'author' == $block ) : ?>
				<li class="meta-author"><span class="fa fa-user"></span><span class="vcard author"<?php wpex_schema_markup( 'author_name' ); ?>><span class="fn"><?php the_author_posts_link(); ?></span></span></li>
			<?php endif; ?>

			<?php if ( 'categories' == $block
					&& $categories = wpex_list_post_terms( wpex_get_post_type_cat_tax(), true, false ) ) : ?>
				<li class="meta-category"><span class="fa fa-folder-o"></span><?php echo $categories; ?></li>
			<?php endif; ?>

			<?php if ( 'comments' == $block && comments_open() && ! post_password_required() ): ?>
				<li class="meta-comments comment-scroll"><span class="fa fa-comment-o"></span><?php comments_popup_link( esc_html__( '0 Comments', 'total' ), esc_html__( '1 Comment',  'total' ), esc_html__( '% Comments', 'total' ), 'comments-link' ); ?></li>
			<?php endif; ?>

		<?php endforeach; ?>

	</ul><!-- .meta -->

<?php endif; ?>