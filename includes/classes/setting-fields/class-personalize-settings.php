<?php
/**
 * The personalize-settings-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Fields;

/**
 * Personalize_Settings class file.
 */
class Personalize_Settings {

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

		$this->setup_personalize_settings_hooks();
	}
	/**
	 * Function is used to define personalize-settings hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_personalize_settings_hooks() {

		add_action( 'admin_init', array( $this, 'better_by_default_personalize_page_init' ), PHP_INT_MAX );
	}

	/**
	 * Function is used register settings.
	 */
	public function better_by_default_personalize_page_init() {
		$personalize_settings_array = array(
			array(
				'field_id'               => 'admin_color_branding',
				'field_name'             => 'admin_color_branding',
				'field_slug'             => 'admin-color-branding',
				'field_title'            => __( 'Admin Color Branding', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Adds a custom admin color scheme to the WordPress dashboard, aligning the interface with the site\'s specific brand colors.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'user_account_style',
				'field_name'             => 'user_account_style',
				'field_slug'             => 'user-account-style',
				'field_title'            => __( 'User Account Style', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_description'      => 'Modifies the styling of the account menu in the admin bar to display a larger circled avatar image.',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_base_color',
				'field_name'             => 'admin_color_scheme_base_color',
				'field_slug'             => 'admin-color-scheme-base-color',
				'field_title'            => __( 'Base Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_icon_color',
				'field_name'             => 'admin_color_scheme_icon_color',
				'field_slug'             => 'admin-color-scheme-icon-color',
				'field_title'            => __( 'Icon Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_text_color',
				'field_name'             => 'admin_color_scheme_text_color',
				'field_slug'             => 'admin-color-scheme-text-color',
				'field_title'            => __( 'Text Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_highlight_color',
				'field_name'             => 'admin_color_scheme_highlight_color',
				'field_slug'             => 'admin-color-scheme-highlight-color',
				'field_title'            => __( 'Highlight Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_accent_color',
				'field_name'             => 'admin_color_scheme_accent_color',
				'field_slug'             => 'admin-color-scheme-accent-color',
				'field_title'            => __( 'Accent Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'admin_color_scheme_link_color',
				'field_name'             => 'admin_color_scheme_link_color',
				'field_slug'             => 'admin-color-scheme-link-color',
				'field_title'            => __( 'Link Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'site_identity_on_login_page',
				'field_name'             => 'site_identity_on_login_page',
				'field_title'            => __( 'Site Identity on Login Page', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'site-identity-on-login-page',
				'field_description'      => __( 'Enable this setting to customize the branding on the login page by changing the logo and login page color.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'login_highlight_color',
				'field_name'             => 'login_highlight_color',
				'field_title'            => __( 'Login Highlight Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_slug'             => 'login-highlight-color',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_highlight_color_hover',
				'field_name'             => 'login_highlight_color_hover',
				'field_title'            => __( 'Login Highlight Hover Color', 'better-by-default' ),
				'field_type'             => 'color-picker',
				'field_slug'             => 'login-highlight-color-hover',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_header_image_url',
				'field_name'             => 'login_header_image_url',
				'field_title'            => __( 'Login Image URL', 'better-by-default' ),
				'field_type'             => 'file',
				'field_slug'             => 'login-header-image-url',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_header_image_url',
				'field_name'             => 'login_header_image_url',
				'field_title'            => __( 'Login Image URL', 'better-by-default' ),    // Login Image URL Setting.
				'field_type'             => 'file',
				'field_slug'             => 'login-header-image-url',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_header_image_size',
				'field_name'             => 'login_header_image_size',
				'field_title'            => __( 'Login Header Image Size', 'better-by-default' ),    // Login Header Image Size Setting.
				'field_type'             => 'text',
				'field_slug'             => 'login-header-image-size',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'disable_back_to_blog_link',
				'field_name'             => 'disable_back_to_blog_link',
				'field_title'            => '',
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'disable-back-to-blog-link',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_label'            => __( 'Disable Back To blog link', 'better-by-default' ),
			),
			array(
				'field_id'          => 'site_identity_description',
				'field_name'        => 'site_identity_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'site-identity-description',
				'field_description' => '<div class="better-by-default-warning"><strong>Note</strong>: The <strong>Login Highlight Color</strong> and <strong>Login Highlight Hover Color</strong> are interdependent, as are the <strong>Login Image URL</strong> and <strong>Login Header Image Size</strong> settings. Adjust them together for consistent branding.</div>',
				'class'             => 'better-by-default-description site-identity-description personalize',
			),
			array(
				'field_id'               => 'enable_duplication',
				'field_name'             => 'enable_duplication',
				'field_title'            => __( 'Content Duplication', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'enable-duplication',
				'field_description'      => __( 'Allow easy one-click duplication of pages, posts, and custom post types, including all related metadata.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'customize_admin_menu',
				'field_name'             => 'customize_admin_menu',
				'field_title'            => __( 'Admin Menu Organizer', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'customize-admin-menu',
				'field_description'      => __( 'Rearrange the admin menu and customize it by modifying menu item titles, icons, and colors, or by hiding specific items as needed.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'          => 'custom_menu_order',
				'field_name'        => 'custom_menu_order',
				'field_title'       => '',
				'field_type'        => 'sortable_menu',
				'field_slug'        => 'custom-menu-order',
				'field_description' => '',
				'class'             => 'better-by-default-sortable better-by-default-hide-th custom-menu-order',
			),
			array(
				'field_id'               => 'disable_block_editor',
				'field_name'             => 'disable_block_editor',
				'field_title'            => __( 'Disable Block Editor', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-block-editor',
				'field_description'      => __( 'Disable the Block Editor for selected post types, reverting to the classic editor experience. This allows for a more familiar editing interface if preferred.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),

		);

		$fields = new Fields();

		foreach ( $personalize_settings_array as $value ) {
			$field_slug = isset( $value['field_slug'] ) ? $value['field_slug'] : '';
			add_settings_field(
				$value['field_name'], // id.
				$value['field_title'], // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS,
					'field_id'               => isset( $value['field_id'] ) ? $value['field_id'] : '',
					'field_slug'             => $field_slug,
					'field_name'             => BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS . '[' . $value['field_name'] . ']',
					'field_description'      => isset( $value['field_description'] ) ? $value['field_description'] : '',
					'field_options_wrapper'  => isset( $value['field_options_wrapper'] ) ? $value['field_options_wrapper'] : true,
					'field_options_moreless' => isset( $value['field_options_moreless'] ) ? $value['field_options_moreless'] : true,
					'class'                  => isset( $value['class'] ) ? $value['class'] : 'better-by-default-toggle personalize ' . $field_slug,
					'field_type'             => $value['field_type'],
					'editor_settings'        => isset( $value['editor_settings'] ) ? $value['editor_settings'] : array(),
					'parent_field_id'        => isset( $value['parent_field_id'] ) ? $value['parent_field_id'] : '',
					'field_prefix'           => isset( $value['field_prefix'] ) ? $value['field_prefix'] : '',
					'field_suffix'           => isset( $value['field_suffix'] ) ? $value['field_suffix'] : '',
					'field_placeholder'      => isset( $value['field_placeholder'] ) ? $value['field_placeholder'] : '',
					'default_value'          => isset( $value['default_value'] ) ? $value['default_value'] : '',
					'field_label'            => isset( $value['field_label'] ) ? $value['field_label'] : '',

				)
			);
		}

		$common_methhods              = new \BetterByDefault\Inc\Common_Methods();
		$better_by_default_post_types = $common_methhods->get_post_types( true );

		$post_types_array = array();
		$field_id         = 'disable_block_editor_for';
		$field_slug       = 'disable-block-editor-for';
		foreach ( $better_by_default_post_types as $slug => $label ) {

			$post_types_array[] = array(
				'field_id'               => $slug,
				'field_name'             => BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS . '[' . $field_id . '][' . $slug . ']',
				'field_title'            => $label,
				'field_type'             => 'render_checkbox_subfield',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			);
		}

		foreach ( $post_types_array as $post_type ) {

			add_settings_field(
				$post_type['field_name'], // id.
				'', // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS,
					'field_id'               => isset( $post_type['field_id'] ) ? $post_type['field_id'] : '',
					'parent_field_id'        => $field_id,
					'field_name'             => $post_type['field_name'],
					'field_description'      => isset( $post_type['field_description'] ) ? $post_type['field_description'] : '',
					'field_options_wrapper'  => isset( $post_type['field_options_wrapper'] ) ? $post_type['field_options_wrapper'] : false,
					'field_options_moreless' => isset( $post_type['field_options_moreless'] ) ? $post_type['field_options_moreless'] : false,
					'class'                  => 'better-by-default-checkbox better-by-default-hide-th admin-interface ' . $field_slug . ' ' . $post_type['field_id'],
					'field_type'             => $post_type['field_type'],
					'field_label'            => isset( $post_type['field_title'] ) ? $post_type['field_title'] : '',
				)
			);
		}
	}
}
