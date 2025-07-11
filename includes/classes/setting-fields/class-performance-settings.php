<?php
/**
 * The performance-settings-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the performance-settings-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Fields;

/**
 * Performance_Settings class file.
 */
class Performance_Settings {

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
	}

	/**
	 * Function is used register settings.
	 */
	public function better_by_default_performance_page_init() {

		$performance_settings_array = array(
			array(
				'field_id'               => 'disable_obscure_wp_head_items',
				'field_name'             => 'disable_obscure_wp_head_items',
				'field_title'            => __( 'Disable Obscure WP Head Items', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-obscure-wp-head-items',
				'field_description'      => 'Disables the inclusion of obscure or non-essential items in the <head> section and HTTP headers of WordPress pages, such as meta tags, links, and scripts, to clean up HTML output, enhance page load times, and reduce security risks.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
				'default_value'          => 'true',
			),
			array(
				'field_id'               => 'remove_shortlinks',
				'field_name'             => 'remove_shortlinks',
				'field_title'            => __( 'Remove ShortLinks', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-shortlinks',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_rss_links',
				'field_name'             => 'remove_rss_links',
				'field_title'            => __( 'Remove RSS Links', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-rss-links',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_rest_api_links',
				'field_name'             => 'remove_rest_api_links',
				'field_title'            => __( 'Remove REST API Links', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-rest-api-links',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_rsd_wlw_links',
				'field_name'             => 'remove_rsd_wlw_links',
				'field_title'            => __( 'Remove RSD / WLW Links', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-rsd-wlw-links',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_oembed_links',
				'field_name'             => 'remove_oembed_links',
				'field_title'            => __( 'Remove oEmbed Links', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-oembed-links',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_generator_tag',
				'field_name'             => 'remove_generator_tag',
				'field_title'            => __( 'Remove Generator Tag', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-generator-tag',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_emoji_scripts',
				'field_name'             => 'remove_emoji_scripts',
				'field_title'            => __( 'Remove emoji scripts', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-emoji-scripts',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'remove_pingback',
				'field_name'             => 'remove_pingback',
				'field_title'            => __( 'Remove Pingback', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'remove-pingback',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),

			array(
				'field_id'               => 'enable_lazy_load_embeds',
				'field_name'             => 'enable_lazy_load_embeds',
				'field_title'            => __( 'Enable Lazy Load for Embeds', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'enable-lazy-load-embeds',
				'field_description'      => 'Enables lazy loading for iframes and YouTube videos, improving page load times by deferring the loading of these elements until they are in the viewport.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
				'default_value'          => 'true',
			),
			array(
				'field_id'          => 'lazy_load_description',
				'field_name'        => 'lazy_load_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'lazy-load-description',
				'field_description' => __( 'Check the checkbox for the elements where you want to enable lazy loading.', 'better-by-default' ),
				'class'             => 'better-by-default-description lazy-load-description',
			),
			array(
				'field_id'               => 'lazy_load_youtube',
				'field_name'             => 'lazy_load_youtube',
				'field_title'            => __( 'Youtube', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'lazy-load-youtube',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'lazy_load_iframe',
				'field_name'             => 'lazy_load_iframe',
				'field_title'            => __( 'Iframe', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'lazy-load-iframe',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
				'class'                  => 'better-by-default-half',
				'hide_title'             => true,
			),
			array(
				'field_id'               => 'enable_critical_css',
				'field_name'             => 'enable_critical_css',
				'field_title'            => __( 'Enable Critical CSS Optimization', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'enable-critical-css',
				'field_description'      => __( 'Toggle to enable or disable Critical CSS optimization for faster page rendering.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
				'default_value'          => 'true',
			),
			array(
				'field_id'          => 'common_critical_css',
				'field_name'        => 'common_critical_css',
				'field_title'       => __( 'Common Critical CSS Rules', 'better-by-default' ),
				'field_type'        => 'textarea_subfield',
				'field_slug'        => 'common-critical-css',
				'field_placeholder' => __( 'Enter the CSS rules for critical elements to improve initial page rendering.', 'better-by-default' ),
				'field_rows'        => 6,
			),
			array(
				'field_id'          => 'critical_css_description',
				'field_name'        => 'critical_css_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'critical-css-description',
				'field_description' => __( 'Check the box for the post types where you want to enable the Critical CSS field.', 'better-by-default' ),

			),
		);

		$fields = new Fields();

		foreach ( $performance_settings_array as $value ) {
			// Sanitize and validate the values to avoid potential vulnerabilities.
			$field_slug = isset( $value['field_slug'] ) ? sanitize_text_field( $value['field_slug'] ) : '';
			$class      = isset( $value['class'] ) ? ' ' . sanitize_html_class( $value['class'] ) : '';
			$title      = isset( $value['field_title'] ) ? sanitize_text_field( $value['field_title'] ) : '';
			$hide_title = isset( $value['hide_title'] ) ? $value['hide_title'] : false;
			// Special condition for specific fields where the title is empty.
			if ( $hide_title ) {
				$title = '';
			}

			// Safely add settings fields with appropriate parameters.
			add_settings_field(
				sanitize_key( $value['field_name'] ),
				esc_html( $title ),
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page',
				'better_by_default_setting_section',
				array(
					'option_name'            => BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS,
					'field_id'               => isset( $value['field_id'] ) ? sanitize_key( $value['field_id'] ) : '',
					'field_slug'             => $field_slug,
					'field_name'             => BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS . '[' . sanitize_key( $value['field_name'] ) . ']',
					'field_description'      => isset( $value['field_description'] ) ? esc_html( $value['field_description'] ) : '',
					'field_options_wrapper'  => isset( $value['field_options_wrapper'] ) ? boolval( $value['field_options_wrapper'] ) : false,
					'field_options_moreless' => isset( $value['field_options_moreless'] ) ? boolval( $value['field_options_moreless'] ) : false,
					'class'                  => 'better-by-default-toggle performances ' . esc_attr( $field_slug . $class ),
					'field_type'             => isset( $value['field_type'] ) ? sanitize_text_field( $value['field_type'] ) : '',
					'default_value'          => isset( $value['default_value'] ) ? sanitize_text_field( $value['default_value'] ) : 'false',
					'field_label'            => isset( $value['field_title'] ) ? esc_html( $value['field_title'] ) : '',
				)
			);
		}

		$common_methhods              = new \BetterByDefault\Inc\Common_Methods();
		$better_by_default_post_types = $common_methhods->get_post_types( true );

		$post_types_array = array();
		$field_id         = 'critical_css_for';
		$field_slug       = 'critical-css-for';
		if ( ! empty( $better_by_default_post_types ) && is_array( $better_by_default_post_types ) ) {
			foreach ( $better_by_default_post_types as $slug => $label ) {
				$post_types_array[] = array(
					'field_id'               => $slug,
					'field_name'             => BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS . '[' . $field_id . '][' . $slug . ']',
					'field_title'            => $label,
					'field_type'             => 'render_checkbox_subfield',
					'field_description'      => '',
					'field_options_wrapper'  => false,
					'field_options_moreless' => false,
				);
			}

			if ( ! empty( $post_types_array ) && is_array( $post_types_array ) ) {
				foreach ( $post_types_array as $post_type ) {
					add_settings_field(
						$post_type['field_name'], // id.
						'', // title.
						array( $fields, 'add_field' ), // callback.
						'better-by-default-setting-page', // page.
						'better_by_default_setting_section', // section.
						array(
							'option_name'            => BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS,
							'field_id'               => isset( $post_type['field_id'] ) ? $post_type['field_id'] : '',
							'parent_field_id'        => $field_id,
							'field_name'             => $post_type['field_name'],
							'field_description'      => isset( $post_type['field_description'] ) ? $post_type['field_description'] : '',
							'field_options_wrapper'  => isset( $post_type['field_options_wrapper'] ) ? $post_type['field_options_wrapper'] : false,
							'field_options_moreless' => isset( $post_type['field_options_moreless'] ) ? $post_type['field_options_moreless'] : false,
							'class'                  => 'better-by-default-checkbox better-by-default-hide-th better-by-default-half admin-interface ' . $field_slug . ' ' . $post_type['field_id'],
							'field_type'             => $post_type['field_type'],
							'field_label'            => isset( $post_type['field_title'] ) ? $post_type['field_title'] : '',
						)
					);
				}
			}
		}
	}
}
