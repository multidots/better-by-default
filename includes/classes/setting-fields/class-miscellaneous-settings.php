<?php
/**
 * The miscellaneous-settings-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the miscellaneous-settings-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Fields;

/**
 * Miscellaneous_Settings class file.
 */
class Miscellaneous_Settings {

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
	 * Function is used add fields.
	 */
	public function better_by_default_miscellaneous_page_init() {

		$miscellaneous_settings_array = array(
			array(
				'field_id'               => 'default_template_network_site',
				'field_name'             => 'default_template_network_site',
				'field_title'            => __( 'Default Template For Network Site', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'default-template-network-site',
				'field_description'      => 'Enable default template for network site.',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'maintenance_mode',
				'field_name'             => 'maintenance_mode',
				'field_title'            => __( 'Maintenance Mode ', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'maintenance-mode',
				'field_description'      => 'Enable this feature to temporarily block user access during system maintenance or updates.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'          => 'maintenance_page_heading',
				'field_name'        => 'maintenance_page_heading',
				'field_title'       => __( 'Title', 'better-by-default' ),
				'field_type'        => 'text_subfield',
				'field_slug'        => 'maintenance-page-heading',
				'field_prefix'      => '',
				'field_suffix'      => '',
				'field_description' => '',
				'field_placeholder' => __( 'We\'ll be back soon.', 'better-by-default' ),
				'default_value'     => 'We\'ll be back soon.',
			),
			array(
				'field_id'          => 'maintenance_page_description',
				'field_name'        => 'maintenance_page_description',
				'field_title'       => __( 'Short Description', 'better-by-default' ),
				'field_type'        => 'textarea_subfield',
				'field_slug'        => 'maintenance-page-description',
				'field_prefix'      => '',
				'field_suffix'      => '',
				'field_description' => 'This site is currently undergoing extended maintenance. We appreciate your patience and understanding during this time.',
				'field_rows'        => 5,
			),
			array(
				'field_id'               => 'activity_log',
				'field_name'             => 'activity_log',
				'field_title'            => __( 'Activity Log', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'activity-log',
				'field_description'      => 'Activate this feature to show the user activity log of the site.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'          => 'activity_log_description',
				'field_name'        => 'activity_log_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'activity-log-description',
				'field_description' => '<div class="better-by-default-info"> You can access the activity logs by following this link: <a href="' . esc_url( admin_url( 'admin.php?page=activity-logs' ) ) . '" target="_blank">View Activity Logs</a><br><br><p class="better-by-default-notice"><strong>Notice: </strong>This log is designed to monitor and record general user actions and activity on your site. It includes logs for plugin and theme activations, deactivations, and related user actions but does not track modifications to plugin or theme settings. Additionally, it does not log post or page updates, metadata changes, or other content modifications. The focus is primarily on user interactions, general site activity, and key events related to plugins and themes.</p></div>',
				'class'             => 'better-by-default-description activity-log-description',
			),
			array(
				'field_id'               => 'enable_public_page_preview',
				'field_name'             => 'enable_public_page_preview',
				'field_title'            => __( 'Enable Public Page Preview', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'enable-public-page-preview',
				'field_description'      => __( 'Allow visitors to view unpublished pages before they go live. This feature lets users share draft content with others for feedback or review, enhancing collaboration and ensuring quality before publication.', 'better-by-default' ),
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'disable_crawling',
				'field_name'             => 'disable_crawling',
				'field_title'            => __( 'Disable Crawling', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-crawling',
				'field_description'      => __( 'Enable this toggle to prevent search engines from crawling and indexing your site.', 'better-by-default' ),
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),

			array(
				'field_id'               => 'cache_functionality',
				'field_name'             => 'cache_functionality',
				'field_title'            => __( 'Cache Settings', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'cache-functionality',
				'field_description'      => __( 'Please enable this toggle to activate the Flush Object Cache and Page Cache features. This will help optimize site performance by storing frequently accessed data, allowing for faster load times and improved user experience.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),

			array(
				'field_id'               => 'flush_object_cache',
				'field_name'             => 'flush_object_cache',
				'field_title'            => '<div class="tooltip_wrap"><p>' . __( 'Flush Object Cache', 'better-by-default' ) . '</p> <div class="tooltip" data-tooltip="' . esc_attr__( 'Click this button to flush the object cache for the site.', 'better-by-default' ) . '"> <i class="dashicons-info-outline dashicons"></i></div></div>',
				'field_type'             => 'button_subfield',
				'field_slug'             => 'flush-object-cache',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'button_title'           => __( 'Flush', 'better-by-default' ),
			),

			array(
				'field_id'          => 'page_cache',
				'field_name'        => 'page_cache',
				'field_title'       => '<div class="page-cache-content tooltip_wrap"><p>' . __( 'Page Cache', 'better-by-default' ) . '</p> <div class="tooltip" data-tooltip="' . esc_attr__( 'Enter each URL on a new line to purge the cache.', 'better-by-default' ) . '"> <i class="dashicons-info-outline dashicons"></i></div></div>',
				'field_type'        => 'textarea_subfield',
				'field_slug'        => 'page-cache',
				'field_placeholder' => __( 'Enter each url in a new line.', 'better-by-default' ),
				'field_rows'        => 5,
				'field_button'      => true,
				'button_title'      => __( 'Purge', 'better-by-default' ),
			),
			array(
				'field_id'          => 'page_cache_description',
				'field_name'        => 'page_cache_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'page-cache-description',
				'field_description' => '<div class="better-by-default-notice"><strong>Notice: </strong>The Page Cache feature is specifically designed for <strong>WordPress VIP environments</strong>. Ensure that you are in a VIP setup to take full advantage of this functionality.</div>',
				'class'             => 'better-by-default-description page-cache-description',
			),

		);

		$fields = new Fields();

		foreach ( $miscellaneous_settings_array as $value ) {
			$field_slug = isset( $value['field_slug'] ) ? $value['field_slug'] : '';
			add_settings_field(
				isset( $value['field_name'] ) ? $value['field_name'] : '', // id.
				isset( $value['field_title'] ) ? $value['field_title'] : '', // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS,
					'field_id'               => isset( $value['field_id'] ) ? $value['field_id'] : '',
					'field_slug'             => $field_slug,
					'field_name'             => BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS . '[' . $value['field_name'] . ']',
					'field_description'      => isset( $value['field_description'] ) ? $value['field_description'] : '',
					'field_options_wrapper'  => isset( $value['field_options_wrapper'] ) ? $value['field_options_wrapper'] : false,
					'field_options_moreless' => isset( $value['field_options_moreless'] ) ? $value['field_options_moreless'] : false,
					'class'                  => 'better-by-default-toggle miscellaneous ' . $field_slug,
					'field_type'             => $value['field_type'],
					'default_value'          => isset( $value['default_value'] ) ? $value['default_value'] : '',
					'field_prefix'           => isset( $value['field_prefix'] ) ? $value['field_prefix'] : '',
					'field_suffix'           => isset( $value['field_suffix'] ) ? $value['field_suffix'] : '',
					'field_placeholder'      => isset( $value['field_placeholder'] ) ? $value['field_placeholder'] : '',
					'field_rows'             => isset( $value['field_rows'] ) ? $value['field_rows'] : 5,
					'table_name'             => isset( $value['table_name'] ) ? $value['table_name'] : '',
					'button_title'           => isset( $value['button_title'] ) ? $value['button_title'] : '',
					'field_button'           => isset( $value['field_button'] ) ? $value['field_button'] : false,
				)
			);
		}
	}
}
