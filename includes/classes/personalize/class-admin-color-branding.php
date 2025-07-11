<?php
/**
 * The admin-color-branding-specific functionality of the plugin.
 *
 * @package    better-by-defaultdefault
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Utils\Color_Utils;

/**
 * Admin_Color_Branding class file.
 */
class Admin_Color_Branding {

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
	 * The setting options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $options    The setting options of this plugin.
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

		$this->setup_admin_color_branding_hooks();
	}
	/**
	 * Function is used to define setup_admin_color_branding hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_color_branding_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		add_action( 'admin_init', array( $this, 'better_by_default_admin_ui_color_callback' ) );
	}

	/**
	 * Admin color branding.
	 *
	 * @return void
	 */
	public function better_by_default_admin_ui_color_callback() {
		$color_utils = Color_Utils::instance();

		$better_by_default_admin_ui_option_name = $this->options;

		// Required colors.
		$colors = array(
			'base_color'      => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_base_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_base_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_base_color'] : '#1d2327',
			'icon_color'      => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_icon_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_icon_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_icon_color'] : '#a7aaad',
			'text_color'      => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_text_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_text_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_text_color'] : '#fff',
			'highlight_color' => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_highlight_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_highlight_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_highlight_color'] : '#2271b1',
			'accent_color'    => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_accent_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_accent_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_accent_color'] : '#d63638',
			'link_color'      => ( isset( $better_by_default_admin_ui_option_name['admin_color_scheme_link_color'] ) && ! empty( $better_by_default_admin_ui_option_name['admin_color_scheme_link_color'] ) ) ? $better_by_default_admin_ui_option_name['admin_color_scheme_link_color'] : '#0073aa',
		);

		// Computed colors.
		$colors['base_color_alt']      = $color_utils::lighten_hsl(
			$color_utils::hex_to_hsl( $colors['base_color'] ),
			7
		);
		$colors['highlight_color_alt'] = $color_utils::darken_hsl(
			$color_utils::hex_to_hsl( $colors['highlight_color'] ),
			10
		);
		$colors['accent_color_alt']    = $color_utils::lighten_hsl(
			$color_utils::hex_to_hsl( $colors['accent_color'] ),
			10
		);
		$colors['link_color_alt']      = $color_utils::lighten_hsl(
			$color_utils::hex_to_hsl( $colors['link_color'] ),
			10
		);

		// Block editor colors (not used in color scheme CSS file).
		$block_editor_colors                         = array();
		$block_editor_colors['color']                = $color_utils::hex_to_hsl( $colors['accent_color'] );
		$block_editor_colors['color-darker-10']      = $color_utils::darken_hsl( $block_editor_colors['color'], 5 );
		$block_editor_colors['color-darker-20']      = $color_utils::darken_hsl( $block_editor_colors['color'], 10 );
		$block_editor_colors['color--rgb']           = $color_utils::hsl_to_rgb( $block_editor_colors['color'] );
		$block_editor_colors['color-darker-10--rgb'] = $color_utils::hsl_to_rgb( $block_editor_colors['color-darker-10'] );
		$block_editor_colors['color-darker-20--rgb'] = $color_utils::hsl_to_rgb( $block_editor_colors['color-darker-20'] );

		// Turn all HSL colors back into HEX colors as they were only modified for color processing.
		foreach ( $colors as $color_id => $color_value ) {
			if ( is_array( $color_value ) && isset( $color_value['h'] ) ) {
				$colors[ $color_id ] = $color_utils::hsl_to_hex( $color_value );
			}
		}
		foreach ( $block_editor_colors as $color_var => $color_value ) {
			if ( is_array( $color_value ) && isset( $color_value['h'] ) ) {
				$block_editor_colors[ $color_var ] = $color_utils::hsl_to_hex( $color_value );
			}
		}

		wp_admin_css_color(
			'brand',
			_x( 'Brand', 'admin color scheme', 'better-by-default' ),
			BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/admin-color-branding-colors.css',
			array(
				$color_utils::to_css_string( $colors['highlight_color'] ),
				$color_utils::to_css_string( $colors['base_color'] ),
				$color_utils::to_css_string( $colors['accent_color'] ),
			),
			array(
				'base'    => $colors['icon_color'],
				'focus'   => $colors['text_color'],
				'current' => $colors['text_color'],
			)
		);

		/*
		 * If the color scheme is currently in use, make sure to include the corresponding CSS variables.
		 * Also include them when on the profile page, to immediately reflect the correct colors if the user changes
		 * the color scheme.
		 */

		$current_color_scheme = get_user_option( 'admin_color' );

		if ( 'brand' === $current_color_scheme || isset( $GLOBALS['pagenow'] ) ) {
			$inline_css = ':root {';
			foreach ( $colors as $color_id => $color_value ) {
				$inline_css .= ' --brand-color-scheme-' . str_replace( '_', '-', $color_id ) . ':';
				$inline_css .= ' ' . esc_attr( $color_utils::to_css_string( $color_value ) ) . ';';
			}
			foreach ( $block_editor_colors as $color_var => $color_value ) {
				$inline_css .= ' --wp-admin-theme-' . $color_var . ':';
				$inline_css .= ' ' . esc_attr( $color_utils::to_css_string( $color_value ) ) . ';';
			}
			$inline_css .= ' }';

			wp_add_inline_style( 'colors', $inline_css );

		}

		// Override the default color scheme.
		add_filter(
			'get_user_option_admin_color',
			static function () {
				return 'brand';
			},
			5,
			1
		);
	}
}
