<?php
/*
Template Name: Dashboard
*/

// bbPress fix while they update things...
if ( is_singular() || ( function_exists( 'is_bbpress' ) && is_bbpress() ) ) {
	get_template_part( 'singular' );
	return;
}

// Get header
get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content" role="main">
                            
                            <?php wpex_hook_content_top(); ?>
                            
                                <?php
				// YOUR POST LOOP STARTS HERE
				while ( have_posts() ) : the_post(); ?>

					<?php if ( has_post_thumbnail() && wpex_get_mod( 'page_featured_image' ) ) : ?>

						<div id="page-featured-img" class="clr">
							<?php the_post_thumbnail(); ?>
						</div><!-- #page-featured-img -->

					<?php endif; ?>

					<div class="entry-content entry clr">
						<?php the_content(); ?>
                                                    
                                            
                                            
					</div><!-- .entry-content -->

				<?php
				// YOUR POST LOOP ENDS HERE
				endwhile; ?>
                            
                            
                            
                            <?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

		<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->
	
<?php get_footer(); ?>