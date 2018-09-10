<?php
/**
 * Sanitize inputted data
 *
 * @package Total WordPress Theme
 * @subpackage Framework
 * @version 4.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPEX_Sanitize_Data {

	/**
	 * Parses data
	 *
	 * @since 2.0.0
	 */
	public function parse_data( $data, $type ) {
		$type = str_replace( '-', '_', $type );
		if ( method_exists( $this, $type ) ) {
			return $this->$type( $data );
		} else {
			return $data;
		}
	}

	/**
	 * Boolean
	 *
	 * @since 2.0.0
	 */
	private function boolean( $data ) {
		if ( ! $data ) {
			return false;
		}
		if ( 'true' == $data || 'yes' == $data ) {
			return true;
		}
		if ( 'false' == $data || 'no' == $data ) {
			return false;
		}
	}

	/**
	 * Pixels
	 *
	 * @since 2.0.0
	 */
	private function px( $data ) {
		if ( 'none' == $data ) {
			return '0';
		} else {
			return floatval( $data ) . 'px'; // Not sure why we used floatval but lets leave it incase
		}
	}

	/**
	 * Font Size
	 *
	 * @since 2.0.0
	 */
	private function font_size( $data ) {
		return wpex_sanitize_font_size( $data );
	}

	/**
	 * Font Weight
	 *
	 * @since 2.0.0
	 */
	private function font_weight( $data ) {
		if ( 'normal' == $data ) {
			return '400';
		} elseif ( 'semibold' == $data ) {
			return '600';
		} elseif ( 'bold' == $data ) {
			return '700';
		} elseif ( 'bolder' == $data ) {
			return '900';
		} else {
			return esc_html( $data );
		}
	}

	/**
	 * Hex Color
	 *
	 * @since 2.0.0
	 */
	private function hex_color( $data ) {
		if ( ! $data ) {
			return null;
		} elseif ( 'none' == $data ) {
			return 'transparent';
		} elseif ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $data ) ) {
			return $data;
		} else {
			return null;
		}
	}

	/**
	 * Border Radius
	 *
	 * @since 2.0.0
	 */
	private function border_radius( $data ) {
		if ( 'none' == $data ) {
			return '0';
		} elseif ( strpos( $data, 'px' ) ) {
			return $data;
		} elseif ( strpos( $data, '%' ) ) {
			if ( '50%' == $data ) {
				return $data;
			} else {
				return str_replace( '%', 'px', $data );
			}
		} else {
			return intval( $data ) .'px';
		}
	}

	/**
	 * Pixel or Percent
	 *
	 * @since 2.0.0
	 */
	private function px_pct( $data ) {
		if ( 'none' == $data || '0px' == $data ) {
			return '0';
		} elseif ( strpos( $data, '%' ) ) {
			return wp_strip_all_tags( $data );
		} elseif ( $data = floatval( $data ) ) {
			return wp_strip_all_tags( $data ) .'px';
		}
	}

	/**
	 * Opacity
	 *
	 * @since 2.0.0
	 */
	private function opacity( $data ) {
		if ( ! is_numeric( $data ) || $data > 1 ) {
			return;
		} else {
			return $data;
		}
	}

	/**
	 * HTML
	 *
	 * @since 3.3.0
	 */
	private function html( $data ) {
		return wp_kses_post( $data );
	}

	/**
	 * Image
	 *
	 * @since 2.0.0
	 */
	private function img( $data ) {
		return wp_kses( $data, array(
			'img' => array(
				'src'    => array(),
				'alt'    => array(),
				'srcset' => array(),
				'id'     => array(),
				'class'  => array(),
				'height' => array(),
				'width'  => array(),
				'data'   => array(),
			),
		) );
	}

	/**
	 * Image from setting
	 *
	 * @since 3.5.0
	 */
	private function image_src_from_mod( $data ) {
		if ( is_numeric( $data ) ) {
			$data = wp_get_attachment_image_src( $data, 'full' );
			$data = $data[0];
		} else {
			$data = esc_url( $data );
		}
		return $data;
	}

	/**
	 * Background Style
	 *
	 * @since 3.5.0
	 */
	private function background_style_css( $data ) {
		if ( $data == 'stretched' ) {
			return '-webkit-background-size: cover;
					-moz-background-size: cover;
					-o-background-size: cover;
					background-size: cover;
					background-position: center center;
					background-attachment: fixed;
					background-repeat: no-repeat;';
		} elseif ( $data == 'cover' ) {
			return 'background-position: center center;
					-webkit-background-size: cover;
					-moz-background-size: cover;
					-o-background-size: cover;
					background-size: cover;';
		} elseif ( $data == 'repeat' ) {
			return 'background-repeat:repeat;';
		} elseif ( $data == 'repeat-y' ) {
			return 'background-position: center center;background-repeat:repeat-y;';
		} elseif ( $data == 'fixed' ) {
			return 'background-repeat: no-repeat; background-position: center center; background-attachment: fixed;';
		} elseif ( $data == 'fixed-top' ) {
			return 'background-repeat: no-repeat; background-position: center top; background-attachment: fixed;';
		} elseif ( $data == 'fixed-bottom' ) {
			return 'background-repeat: no-repeat; background-position: center bottom; background-attachment: fixed;';
		} else {
			return 'background-repeat:'. $data .';';
		}
	}

	/**
	 * Embed URL
	 *
	 * @since 2.0.0
	 */
	private function embed_url( $url ) {
		return wpex_get_video_embed_url( $url );
	}

} // End Class

// Helper function runs the WPEX_Sanitize_Data class
function wpex_sanitize_data( $data = '', $type = '' ) {
	if ( $data && $type ) {
		$class = new WPEX_Sanitize_Data();
		return $class->parse_data( $data, $type );
	}
}