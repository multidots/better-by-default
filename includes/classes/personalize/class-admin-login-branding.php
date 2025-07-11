<?php
/**
 * The admin-login-branding-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Utils\Color_Utils;

/**
 * Admin_Login_Branding class file.
 */
class Admin_Login_Branding {

	use Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options of the admin login branding.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var array $options The options of the admin login branding.
	 */
	private $options;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */

	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'BETTER_BY_DEFAULT_VERSION' ) ) {
			$this->version = BETTER_BY_DEFAULT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_admin_login_branding_hooks();
	}
	/**
	 * Function is used to define setup_admin_login_branding hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_login_branding_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		// Displays custom branding as configured, or using the site icon if set.
		add_action( 'login_head', array( $this, 'better_by_default_admin_login_ui_callback' ) );
	}

	/**
	 * Admin login UI callback.
	 *
	 * @return void
	 */
	public function better_by_default_admin_login_ui_callback() {

		$color_utils = Color_Utils::instance();
		$options     = $this->options;

		$highlight_color           = isset( $options['login_highlight_color'] ) && ! empty( $options['login_highlight_color'] ) ? $options['login_highlight_color'] : '';
		$highlight_color_hover     = isset( $options['login_highlight_color_hover'] ) && ! empty( $options['login_highlight_color_hover'] ) ? $options['login_highlight_color_hover'] : '';
		$header_image_url          = isset( $options['login_header_image_url'] ) && ! empty( $options['login_header_image_url'] ) ? $options['login_header_image_url'] : '';
		$header_image_size         = isset( $options['login_header_image_size'] ) && ! empty( $options['login_header_image_size'] ) ? $options['login_header_image_size'] : '';
		$disable_back_to_blog_link = isset( $options['disable_back_to_blog_link'] ) && ! empty( $options['disable_back_to_blog_link'] ) ? $options['disable_back_to_blog_link'] : '';

		// Use site icon if no header image is set.
		$header_image_url  = ! $header_image_url ? get_site_icon_url( 192 ) : $header_image_url;
		$header_image_size = ! $header_image_size ? 192 : $header_image_size;

		if ( $highlight_color && $highlight_color_hover ) {
			$highlight_color_shadow = $color_utils::to_css_string(
				$color_utils::set_rgb_opacity(
					$color_utils::hex_to_rgb( $highlight_color ),
					80
				)
			);

			wp_enqueue_style( 'better_by_default_admin_login_ui_style', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

			$better_by_default_admin_login_ui_css = '
			a {
				color: ' . esc_attr( $highlight_color ) . ';
			}

			a:hover,
			a:active {
				color: ' . esc_attr( $highlight_color_hover ) . ';
			}

			.login #nav a:hover,
			.login #backtoblog a:hover,
			.login h1 a:hover {
				color: ' . esc_attr( $highlight_color_hover ) . ';
			}

			input[type="text"]:focus,
			input[type="password"]:focus,
			input[type="color"]:focus,
			input[type="date"]:focus,
			input[type="datetime"]:focus,
			input[type="datetime-local"]:focus,
			input[type="email"]:focus,
			input[type="month"]:focus,
			input[type="number"]:focus,
			input[type="search"]:focus,
			input[type="tel"]:focus,
			input[type="time"]:focus,
			input[type="url"]:focus,
			input[type="week"]:focus,
			input[type="checkbox"]:focus,
			input[type="radio"]:focus,
			select:focus,
			textarea:focus {
				border-color: ' . esc_attr( $highlight_color ) . ';
				-webkit-box-shadow: 0 0 2px ' . esc_attr( $highlight_color_shadow ) . ';
				box-shadow: 0 0 2px ' . esc_attr( $highlight_color_shadow ) . ';
			}

			.wp-core-ui .button-primary {
				background: ' . esc_attr( $highlight_color ) . ';
				border-color: ' . esc_attr( $highlight_color_hover ) . ';
				-webkit-box-shadow: 0 1px 0 ' . esc_attr( $highlight_color_hover ) . ';
				box-shadow: 0 1px 0 ' . esc_attr( $highlight_color_hover ) . ';
				color: #fff;
				text-shadow: 0 -1px 1px ' . esc_attr( $highlight_color_hover ) . ',
					1px 0 1px ' . esc_attr( $highlight_color_hover ) . ',
					0 1px 1px ' . esc_attr( $highlight_color_hover ) . ',
					-1px 0 1px ' . esc_attr( $highlight_color_hover ) . ';
			}

			.wp-core-ui .button-primary.hover,
			.wp-core-ui .button-primary:hover,
			.wp-core-ui .button-primary.focus,
			.wp-core-ui .button-primary:focus {
				background: ' . esc_attr( $highlight_color_hover ) . ';
				border-color: ' . esc_attr( $highlight_color_hover ) . ';
				color: #fff;
			}

			.wp-core-ui .button-primary.focus,
			.wp-core-ui .button-primary:focus {
				-webkit-box-shadow: 0 1px 0 ' . esc_attr( $highlight_color_hover ) . ',
					0 0 2px 1px ' . esc_attr( $highlight_color_hover ) . ';
				box-shadow: 0 1px 0 ' . esc_attr( $highlight_color_hover ) . ',
					0 0 2px 1px ' . esc_attr( $highlight_color_hover ) . ';
			}

			.wp-core-ui .button-primary.active,
			.wp-core-ui .button-primary.active:hover,
			.wp-core-ui .button-primary.active:focus,
			.wp-core-ui .button-primary:active {
				background: ' . esc_attr( $highlight_color ) . ';
				border-color: ' . esc_attr( $highlight_color_hover ) . ';
				-webkit-box-shadow: inset 0 2px 0 ' . esc_attr( $highlight_color_hover ) . ';
				box-shadow: inset 0 2px 0 ' . esc_attr( $highlight_color_hover ) . ';
			}

			.wp-core-ui .button-primary[disabled],
			.wp-core-ui .button-primary:disabled,
			.wp-core-ui .button-primary-disabled,
			.wp-core-ui .button-primary.disabled {
				opacity: 0.8;
				color: #ffffff !important;
				background: ' . esc_attr( $highlight_color ) . ';
				border-color: ' . esc_attr( $highlight_color_hover ) . ';
			}

			.wp-core-ui .button.button-primary.button-hero {
				-webkit-box-shadow: 0 2px 0 ' . esc_attr( $highlight_color_hover ) . ';
				box-shadow: 0 2px 0 ' . esc_attr( $highlight_color_hover ) . ';
			}

			.wp-core-ui .button.button-primary.button-hero.active,
			.wp-core-ui .button.button-primary.button-hero.active:hover,
			.wp-core-ui .button.button-primary.button-hero.active:focus,
			.wp-core-ui .button.button-primary.button-hero:active {
				-webkit-box-shadow: inset 0 3px 0 ' . esc_attr( $highlight_color_hover ) . ';
				box-shadow: inset 0 3px 0 ' . esc_attr( $highlight_color_hover ) . ';
			}';
			wp_add_inline_style( 'better_by_default_admin_login_ui_style', $better_by_default_admin_login_ui_css );	
		}

		// If header image is available, display it instead of the WordPress logo and replace the link accordingly.
		if ( $header_image_url && $header_image_size ) {
			if ( is_string( $header_image_size ) && str_contains( $header_image_size, 'x' ) ) {
				$parts               = explode( 'x', $header_image_size );
				$header_image_width  = (int) $parts[0];
				$header_image_height = (int) $parts[1];
			} else {
				$header_image_width  = (int) $header_image_size;
				$header_image_height = $header_image_width;
			}

			wp_enqueue_style( 'add_header_image_style', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

			$add_header_image_css = "
			.login h1 a {
				background-image: url('" . esc_url( $header_image_url ) . "');
				background-size: " . esc_attr( $header_image_width ) . "px " . esc_attr( $header_image_height ) . "px;
				width: " . esc_attr( $header_image_width ) . "px;
				height: " . esc_attr( $header_image_height ) . "px;
			}";
			wp_add_inline_style( 'add_header_image_style', $add_header_image_css );	

			add_filter(
				'login_headerurl',
				static function () {
					return home_url( '/' );
				}
			);
			add_filter(
				'login_headertext',
				static function () {
					return get_bloginfo( 'name', 'display' );
				}
			);
		}

		if ( ! empty( $disable_back_to_blog_link ) && ( $disable_back_to_blog_link || 'true' === $disable_back_to_blog_link ) ) {
				wp_enqueue_style('disable-back-to-blog-style', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all');

				$custom_css = '#backtoblog { display: none; }';
				wp_add_inline_style( 'disable-back-to-blog-style', $custom_css );
		}

	}
}
