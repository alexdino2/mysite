<?php
/**
 * Instagram Slider Widget
 *
 * Learn more: http://codex.wordpress.org/Widgets_API
 *
 * @package Total WordPress Theme
 * @subpackage Widgets
 * @version 3.3.3
 */

// Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

// Start widget class
if ( ! class_exists( 'WPEX_Instagram_Grid_Widget' ) ) {
	class WPEX_Instagram_Grid_Widget extends WP_Widget {
		
		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			$branding = wpex_get_theme_branding();
			$branding = $branding ? $branding . ' - ' : '';
			parent::__construct(
				'wpex_insagram_slider',
				$branding . esc_html__( 'Instagram Grid', 'total' )
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 * @since 1.0.0
		 *
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			// Args
			$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$username = empty( $instance['username'] ) ? '' : $instance['username'];
			$number   = empty( $instance['number'] ) ? 9 : $instance['number'];
			$columns  = empty( $instance['columns'] ) ? '3' : $instance['columns'];
			$target   = empty( $instance['target'] ) ? ' target="_blank"' : $instance['target'];

			// Exclude current post
			if ( is_singular() ) {
				$exclude = array( get_the_ID() );
			} else {
				$exclude = NULL;
			}

			// Before widget hook
			echo $args['before_widget'];

			// Display widget title
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			// Display notice for username not added
			if ( ! $username ) {

				echo '<p>'. esc_html__( 'Please enter an instagram username for your widget.', 'total' ) .'</p>';

			} else {

				// Get instagram images
				$media_array = $this->scrape_instagram( $username, $number );

				// Display error message
				if ( is_wp_error( $media_array ) ) {

					echo strip_tags( $media_array->get_error_message() );

				}

				// Display instagram slider
				elseif ( is_array( $media_array ) ) { ?>

					<div class="wpex-instagram-grid-widget wpex-clr">

						<ul class="wpex-clr wpex-row gap-10">

						<?php
						$count = 0;
						foreach ( $media_array as $item ) {
							$image = ! empty( $item['thumbnail_src'] ) ? $item['thumbnail_src'] : $item['display_src'];
							if ( $image ) {
								$count++;
								echo '<li class="col wpex-clr span_1_of_'. esc_attr( $columns ) .' count-'. esc_attr( $count ) .'">
										<a href="'. esc_url( $item['link'] ) .'" title="'. esc_attr( $item['description'] ) .'"'. esc_attr( $target ) .'>
											<img src="'. esc_url( $image ) .'"  alt="'. esc_attr( $item['description'] ) .'" />
										</a>
									</li>';
								if ( $columns == $count ) {
									$count = 0;
								}
							}
						} ?>

						</ul><!-- .wpex-instagram-slider-widget-slider -->
						
					</div><!-- .wpex-instagram-slider-widget -->

			<?php }

			}

			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {

			// Get instance
			$instance             = $old_instance;
			$instance['title']    = strip_tags( $new_instance['title'] );
			$instance['username'] = isset( $new_instance['username'] ) ? trim( strip_tags( $new_instance['username'] ) ) : '';
			$instance['number']   = ! empty( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 9;
			$instance['target']   = $new_instance['target'] == 'blank' ? $new_instance['target'] : '';
			$instance['columns']  = isset( $new_instance['columns'] ) ? intval( $new_instance['columns'] ) : '';

			// Delete transient
			if ( isset( $instance['username'] ) ) {
				delete_transient( 'wpex-instagram-widget-new-'. sanitize_title_with_dashes( $instance['username'] ) );
			}

			// Return instance
			return $instance;

		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			extract( wp_parse_args( ( array ) $instance, array(
				'title'    => esc_html__( 'Instagram', 'total' ),
				'username' => '',
				'number'   => '9',
				'columns'  => '3',
				'target'   => '_self'
			) ) ); ?>
			
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'total' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( 'Username', 'total' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" /></label></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns', 'total' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" class="widefat">
					<option value="1" <?php selected( '1', $columns ) ?>>1</option>
					<option value="2" <?php selected( '2', $columns ) ?>>2</option>
					<option value="3" <?php selected( '3', $columns ) ?>>3</option>
					<option value="4" <?php selected( '4', $columns ) ?>>4</option>
					<option value="5" <?php selected( '5', $columns ) ?>>5</option>
				</select>
			</p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of photos', 'total' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" /></label></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in', 'total' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
					<option value="_self" <?php selected( '_self', $target ) ?>><?php esc_html_e( 'Current window', 'total' ); ?></option>
					<option value="_blank" <?php selected( '_blank', $target ) ?>><?php esc_html_e( 'New window', 'total' ); ?></option>
				</select>
			</p>

			<p>
				<strong><?php esc_html_e( 'Cache Notice', 'total' ); ?></strong>:<?php esc_html_e( 'The instagram feed is refreshed every 2 hours. However, you can click the save button below to clear the transient and refresh it instantly.', 'total' ); ?>
			</p>

			<?php
		}

		/**
		 * Get instagram items
		 *
		 * @since 1.0.0
		 * @link  https://gist.github.com/cosmocatalano/4544576
		 */
		function scrape_instagram( $username, $slice = 4 ) {

			$username           = strtolower( $username );
			$sanitized_username = sanitize_title_with_dashes( $username );
			$transient_name     = 'wpex-instagram-widget-transient-'. $sanitized_username;
			$instagram          = get_transient( $transient_name );

			if ( ! empty( $_GET['theme_clear_transients'] ) ) {
				$instagram = delete_transient( $transient_name );
			}

			if ( ! $instagram ) {

				$remote = wp_remote_get( 'http://instagram.com/'. trim( $username ) );

				if ( is_wp_error( $remote ) ) {
					return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'total' ) );
				}

				if ( 200 != wp_remote_retrieve_response_code( $remote ) ) {
					return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'total' ) );
				}

				$shards      = explode( 'window._sharedData = ', $remote['body'] );
				$insta_json  = explode( ';</script>', $shards[1] );
				$insta_array = json_decode( $insta_json[0], TRUE );

				if ( ! $insta_array ) {
					return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'total' ) );
				}

				// Old style
				if ( isset( $insta_array['entry_data']['UserProfile'][0]['userMedia'] ) ) {
					$images = $insta_array['entry_data']['UserProfile'][0]['userMedia'];
					$type = 'old';

				}

				// New style
				elseif ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
					$images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
					$type = 'new';
				}

				// Invalid json data
				else {
					return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'total' ) );
				}

				// Invalid data
				if ( ! is_array( $images ) ) {
					return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'total' ) );
				}

				$instagram = array();

				switch ( $type ) {

					case 'old':

						foreach ( $images as $image ) {
							if ( $image['user']['username'] == $username ) {
								$image['link']						    = preg_replace( "/^http:/i", "", $image['link'] );
								$image['images']['thumbnail']		    = preg_replace( "/^http:/i", "", $image['images']['thumbnail'] );
								$image['images']['standard_resolution'] = preg_replace( "/^http:/i", "", $image['images']['standard_resolution'] );
								$image['images']['low_resolution']	    = preg_replace( "/^http:/i", "", $image['images']['low_resolution'] );
								$instagram[] = array(
									'description' => $image['caption']['text'],
									'link'        => $image['link'],
									'time'        => $image['created_time'],
									'comments'    => $image['comments']['count'],
									'likes'       => $image['likes']['count'],
									'thumbnail'   => $image['images']['thumbnail'],
									'large'       => $image['images']['standard_resolution'],
									'small'       => $image['images']['low_resolution'],
									'type'        => $image['type'],
								);
							}
						}

					break;

					default:

						foreach ( $images as $image ) {

							$image['display_src'] = preg_replace( "/^http:/i", "", $image['display_src'] );

							if ( $image['is_video']  == true ) {
								$type = 'video';
							} else {
								$type = 'image';
							}

							$instagram[] = array(
								'description'   => esc_html__( 'Instagram Image', 'total' ),
								'link'		    => '//instagram.com/p/' . $image['code'],
								'time'		    => $image['date'],
								'comments'	    => $image['comments']['count'],
								'likes'		    => $image['likes']['count'],
								'thumbnail_src' => isset( $image['thumbnail_src'] ) ? $image['thumbnail_src'] : '',
								'display_src'   => $image['display_src'],
								'type'		    => $type,
							);

						}

					break;

				}

				// Set transient if not empty
				if ( ! empty( $instagram ) ) {
					$instagram = serialize( $instagram );
					set_transient(
						$transient_name,
						$instagram,
						apply_filters( 'wpex_instagram_widget_cache_time', 2 * HOUR_IN_SECONDS )
					);
				}

			}

			// Return array
			if ( ! empty( $instagram )  ) {
				$instagram = unserialize( $instagram );
				return array_slice( $instagram, 0, $slice );
			}

			// No images returned
			else {

				return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'total' ) );

			}

		}


	}
}
register_widget( 'WPEX_Instagram_Grid_Widget' );