<?php
/**
 * The simplify-settings-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Fields;

/**
 * Simplify_Settings class file.
 */
class Simplify_Settings {

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
	public function better_by_default_simplify_page_init() {
		global $wp_version, $wp_roles;
		$roles                   = $wp_roles->get_names();
		$simplify_settings_array = array(
			array(
				'field_id'               => 'bbd_disable_dashboard_widgets',
				'field_name'             => 'bbd_disable_dashboard_widgets',
				'field_slug'             => 'bbd-disable-dashboard-widgets',
				'field_title'            => __( 'Disable Dashboard Widgets', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Enhance dashboard performance by completely disabling selected widgets. Disabled widgets won’t load resources or appear in Screen Options.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
				'default_value'          => 'true',
			),
			// @codingStandardsIgnoreStart
			// array(
			// 	'field_id'               => 'disable_auto_update',
			// 	'field_name'             => 'disable_auto_update',
			// 	'field_title'            => __( 'Disable All Updates', 'better-by-default' ),
			// 	'field_type'             => 'checkbox-toggle',
			// 	'field_slug'             => 'disable-auto-update',
			// 	'field_description'      => 'Prevent all updates from occurring, ensuring no new features or security patches are applied. This setting will keep the current version unchanged until manually re-enabled.',
			// 	'field_options_wrapper'  => false,
			// 	'field_options_moreless' => false,
			// 	'default_value'          => 'true',
			// ),
			// @codingStandardsIgnoreEnd
			array(
				'field_id'               => 'disable_comments',
				'field_name'             => 'disable_comments',
				'field_title'            => __( 'Disable Comments', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-comments',
				'field_description'      => 'Disables comments on all publicly available post types and pages, while also preventing any existing comments from being displayed on the front end.',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
			),
			array(
				'field_id'               => 'disable_post_tags',
				'field_name'             => 'disable_post_tags',
				'field_title'            => __( 'Disable Post Tags', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-post-tags',
				'field_description'      => 'Disables post tags in WordPress, preventing their addition and display, which simplifies content management and streamlines the user interface by removing unnecessary tagging functionality.',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
			),
			array(
				'field_id'               => 'custom_admin_footer_text',
				'field_name'             => 'custom_admin_footer_text',
				'field_slug'             => 'custom-admin-footer-text',
				'field_title'            => __( 'Customize Admin Footer Text', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Personalize the footer text in the WordPress admin area to feature your own message or brand identity.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
				'default_value'          => 'false',
			),
			array(
				'field_id'               => 'custom_admin_footer_right',
				'field_name'             => 'custom_admin_footer_right',
				'field_slug'             => 'custom-admin-footer-right',
				'field_title'            => __( 'Right Side Footer Text', 'better-by-default' ),
				'field_type'             => 'wpeditor_subfield',
				'field_description'      => sprintf( '<b>Version %s</b>', $wp_version ),
				'editor_settings'        => array(
					'media_buttons'  => false,
					'textarea_name'  => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[custom_admin_footer_right]',
					'textarea_rows'  => 3,
					'tiny_mce'       => true,
					'tinymce'        => array(
						'toolbar1' => 'bold,italic,underline',
					),
					'editor_css'     => '',
					'quicktags'      => false,
					'default_editor' => 'tinymce',
				),
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'custom_admin_footer_left',
				'field_name'             => 'custom_admin_footer_left',
				'field_slug'             => 'custom-admin-footer-left',
				'field_title'            => __( 'Left Footer Text', 'better-by-default' ),
				'field_type'             => 'wpeditor_subfield',
				'field_description'      => __( '<em>Thank you for creating with <a href="https://wordpress.org/">WordPress</a></em>.', 'better-by-default' ),
				'editor_settings'        => array(
					'media_buttons'  => false,
					'textarea_name'  => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[custom_admin_footer_left]',
					'textarea_rows'  => 3,
					'tiny_mce'       => true,
					'tinymce'        => array(
						'toolbar1' => 'bold,italic,underline',
					),
					'editor_css'     => '',
					'quicktags'      => false,
					'default_editor' => 'tinymce',
				),
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'hide_admin_bar',
				'field_name'             => 'hide_admin_bar',
				'field_slug'             => 'hide-admin-bar',
				'field_title'            => __( 'Disable Admin Bar', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Hide the WordPress admin bar from the top of the website for specific users or for all users.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'customize_list_tables',
				'field_name'             => 'customize_list_tables',
				'field_slug'             => 'customize-list-tables',
				'field_title'            => __( 'Customize List Tables', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Improve the functionality and appearance of WordPress list tables by adding custom columns, making columns sortable, and integrating additional data.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'enable_search_by_title',
				'field_name'             => 'enable_search_by_title',
				'field_slug'             => 'enable-search-by-title',
				'field_title'            => __( 'Enable Search by Title', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => __( 'Add the ability to search by post title in the WordPress admin area.', 'better-by-default' ),
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
			),
			array(
				'field_id'               => 'enable_last_login_column',
				'field_name'             => 'enable_last_login_column',
				'field_title'            => __( 'Show Last Login', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'enable-last-login-column',
				'field_description'      => '<div class="better-by-default-info">Logs when users last log in and displays the date and time in the users list table. This helps track user activity and enhances administrative insights.<br><br><p class="better-by-default-notice"><strong>Notice: </strong>This functionality does not work in the VIP environment when the site is a multisite and the plugin is activated on individual sites instead of being network-activated.</p></div>',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
			),
		);

		$fields = new Fields();

		foreach ( $simplify_settings_array as $value ) {
			$field_slug = isset( $value['field_slug'] ) ? $value['field_slug'] : '';
			add_settings_field(
				$value['field_name'], // id.
				$value['field_title'], // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS,
					'field_id'               => isset( $value['field_id'] ) ? $value['field_id'] : '',
					'field_slug'             => $field_slug,
					'field_name'             => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[' . $value['field_name'] . ']',
					'field_description'      => isset( $value['field_description'] ) ? $value['field_description'] : '',
					'field_options_wrapper'  => isset( $value['field_options_wrapper'] ) ? $value['field_options_wrapper'] : true,
					'field_options_moreless' => isset( $value['field_options_moreless'] ) ? $value['field_options_moreless'] : true,
					'class'                  => 'better-by-default-toggle admin-interface ' . $field_slug,
					'field_type'             => $value['field_type'],
					'editor_settings'        => isset( $value['editor_settings'] ) ? $value['editor_settings'] : array(),
					'parent_field_id'        => isset( $value['parent_field_id'] ) ? $value['parent_field_id'] : '',
					'default_value'          => isset( $value['default_value'] ) ? $value['default_value'] : false,
				)
			);
		}

		$field_id                = 'bbd_disabled_dashboard_widgets';
		$dashboard_widgets_array = array(
			array(
				'field_id'               => 'disable_welcome_panel_in_dashboard',
				'field_name'             => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[' . $field_id . '][disable_welcome_panel_in_dashboard]',
				'field_title'            => __( 'Welcome to WordPress', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => true,
			),
		);

		$options_extra = get_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, array() );

		if ( ! empty( $options_extra ) && array_key_exists( 'dashboard_widgets', $options_extra ) && ! empty( $options_extra['dashboard_widgets'] ) ) {

			$dashboard_widgets = $options_extra['dashboard_widgets'];
		} else {
			$disable_dashboard_widgets          = new \BetterByDefault\Inc\Simplify\Disable_Dashboard_Widgets();
			$dashboard_widgets                  = $disable_dashboard_widgets->get_dashboard_widgets();
			$options_extra['dashboard_widgets'] = $dashboard_widgets;
			update_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, $options_extra, true );
		}

		foreach ( $dashboard_widgets as $widget ) {
			$dashboard_widgets_array[] = array(
				'field_id'               => $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'],
				'field_name'             => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[' . $field_id . '][' . $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'] . ']',
				'field_title'            => $widget['title'],
				'field_type'             => 'render_checkbox_subfield',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => false,
			);
		}
		foreach ( $dashboard_widgets_array as $widget_list ) {
			add_settings_field(
				$widget_list['field_name'], // id.
				'', // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS,
					'field_id'               => isset( $widget_list['field_id'] ) ? $widget_list['field_id'] : '',
					'parent_field_id'        => isset( $field_id ) && ! empty( $field_id ) ? $field_id : '',
					'field_name'             => $widget_list['field_name'],
					'field_description'      => isset( $widget_list['field_description'] ) ? $widget_list['field_description'] : '',
					'field_options_wrapper'  => isset( $widget_list['field_options_wrapper'] ) ? $widget_list['field_options_wrapper'] : false,
					'field_options_moreless' => isset( $widget_list['field_options_moreless'] ) ? $widget_list['field_options_moreless'] : false,
					'class'                  => 'better-by-default-checkbox better-by-default-hide-th admin-interface disabled-dashboard-widgets ' . $widget_list['field_id'],
					'field_type'             => $widget_list['field_type'],
					'field_label'            => isset( $widget_list['field_title'] ) ? $widget_list['field_title'] : '',
					'default_value'          => isset( $widget_list['default_value'] ) ? $widget_list['default_value'] : false,
				)
			);
		}

		if ( is_array( $roles ) ) {
			$field_id   = 'hide_admin_bar_for';
			$field_slug = 'hide-admin-bar-for';
			foreach ( $roles as $role_slug => $role_label ) {
				add_settings_field(
					$field_id . '_' . $role_slug,
					'', // Field title.
					array( $fields, 'add_field' ), // callback.
					'better-by-default-setting-page', // page.
					'better_by_default_setting_section', // section.
					array(
						'option_name'     => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS,
						'parent_field_id' => $field_id,
						'field_id'        => $role_slug,
						'field_name'      => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[' . $field_id . '][' . $role_slug . ']',
						'field_label'     => $role_label,
						'field_type'      => 'render_checkbox_subfield',
						'class'           => 'better-by-default-checkbox better-by-default-hide-th better-by-default-half admin-interface ' . $field_slug . ' ' . $role_slug,
					)
				);
			}
		}

		$list_table_columns_array = array(
			array(
				'field_id'          => 'show_id_column',
				'field_name'        => 'show_id_column',
				'field_slug'        => 'show-id-column',
				'field_title'       => __( 'Show ID column', 'better-by-default' ),
				'field_description' => '',
			),
			array(
				'field_id'          => 'show_featured_image_column',
				'field_name'        => 'show_featured_image_column',
				'field_slug'        => 'show-feature-image-column',
				'field_title'       => __( 'Show Featured Image Column', 'better-by-default' ),
				'field_description' => '',
			),
			array(
				'field_id'          => 'show_excerpt_column',
				'field_name'        => 'show_excerpt_column',
				'field_slug'        => 'show-excerpt-column',
				'field_title'       => __( 'Show Excerpt Column', 'better-by-default' ),
				'field_description' => '',
			),
			array(
				'field_id'          => 'show_file_size_column',
				'field_name'        => 'show_file_size_column',
				'field_slug'        => 'show-file-size-column',
				'field_title'       => __( 'Show file size column in media library', 'better-by-default' ),
				'field_description' => '',
			),
		);
		foreach ( $list_table_columns_array as $column_name ) {
			$field_id = 'extra_list_table_columns';
			add_settings_field(
				$column_name['field_name'], // id.
				'', // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'       => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS,
					'field_id'          => isset( $column_name['field_id'] ) ? $column_name['field_id'] : '',
					'parent_field_id'   => $field_id,
					'field_name'        => BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS . '[' . $field_id . '][' . $column_name['field_name'] . ']',
					'field_description' => isset( $column_name['field_description'] ) ? $column_name['field_description'] : '',
					'class'             => 'better-by-default-checkbox better-by-default-hide-th admin-interface extra-list-table-columns ' . $column_name['field_slug'],
					'field_type'        => 'render_checkbox_subfield',
					'field_label'       => isset( $column_name['field_title'] ) ? $column_name['field_title'] : '',
				)
			);
		}
	}
}
