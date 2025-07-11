<?php
/**
 * The protect-settings-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Fields;

/**
 * Protect_Settings class file.
 */
class Protect_Settings {

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
	public function better_by_default_protect_page_init() {

		$protect_settings_array = array(
			array(
				'field_id'               => 'limit_login_attempts',
				'field_name'             => 'limit_login_attempts',
				'field_title'            => __( 'Limit Login Attempts', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'limit-login-attempts',
				'field_description'      => 'Protect against brute force attacks by restricting the number of failed login attempts for each IP address. This helps enhance security by blocking potentially malicious access attempts.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'failed_login_attempts',
				'field_name'             => 'failed_login_attempts',
				'field_title'            => '',
				'field_type'             => 'number',
				'field_slug'             => 'failed-login-attempts',
				'field_description'      => 'failed login attempts allowed before 15 minutes lockout',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_lockout_maxcount',
				'field_name'             => 'login_lockout_maxcount',
				'field_title'            => '',
				'field_type'             => 'number',
				'field_slug'             => 'login-lockout-maxcount',
				'field_description'      => 'lockout(s) will block further login attempts for 24 hours',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'login_attempts_log_table',
				'field_name'             => 'login_attempts_log_table',
				'field_title'            => __( 'Failed login attempts:', 'better-by-default' ),
				'field_type'             => 'render_datatable',
				'field_slug'             => 'login-attempts-log-table',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'table_name'             => 'better_by_default_failed_logins',
			),
			array(
				'field_id'               => 'disable_xml_rpc',
				'field_name'             => 'disable_xml_rpc',
				'field_title'            => __( 'Disable XML RPC', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'disable-xml-rpc',
				'field_description'      => 'Disables XML-RPC functionality in WordPress, preventing remote access via XML-RPC requests and enhancing security by mitigating potential attack vectors such as brute force attacks and unauthorized access attempts.',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'security_headers',
				'field_name'             => 'security_headers',
				'field_title'            => __( 'Security Headers', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'security-headers',
				'field_description'      => 'Security headers are HTTP response headers that enhance the security of your website by instructing browsers how to behave when handling your site’s content. They can help prevent attacks such as cross-site scripting (XSS), clickjacking, and other code injection attacks.',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),

			array(
				'field_id'               => 'x_frame_options',
				'field_name'             => 'x_frame_options',
				'field_title'            => __( 'X-Frame-Origins', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'x-frame-options',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. SAMEORIGIN, DENY', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text x-frame-options',
				'default_value'          => 'SAMEORIGIN',
			),
			array(
				'field_id'               => 'x_xss_protection',
				'field_name'             => 'x_xss_protection',
				'field_title'            => __( 'X-Xss-Protection', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'x_xss_protection',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. SAMEORIGIN, DENY', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text',
				'default_value'          => 'SAMEORIGIN',
			),
			array(
				'field_id'               => 'x_xss_protection',
				'field_name'             => 'x_xss_protection',
				'field_title'            => __( 'X-Xss-Protection', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'x-xss-protection',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. 1; mode=block', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text',
				'default_value'          => '1; mode=block',
			),
			array(
				'field_id'               => 'x_content_type_options',
				'field_name'             => 'x_content_type_options',
				'field_title'            => __( 'X-Content-Type-Options', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'x-content-type-options',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. nosniff', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text',
				'default_value'          => 'nosniff',
			),
			array(
				'field_id'               => 'referrer_policy',
				'field_name'             => 'referrer_policy',
				'field_title'            => __( 'Referrer-Policy', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'referrer-policy',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. strict-origin-when-cross-origin', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text',
				'default_value'          => 'strict-origin-when-cross-origin',
			),
			array(
				'field_id'               => 'content_security_policy',
				'field_name'             => 'content_security_policy',
				'field_title'            => __( 'Content-Security-Policy', 'better-by-default' ),
				'field_type'             => 'textarea_subfield',
				'field_slug'             => 'content-security-policy',
				'field_rows'             => 5,
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( "e.g. default-src https: data: 'unsafe-inline' 'unsafe-eval'; child-src https: data: blob:; connect-src https: data: blob: ; font-src https: data:; img-src https: data: blob:; media-src https: data: blob:; object-src https:; script-src https: data: blob: 'unsafe-inline' 'unsafe-eval'; style-src https: 'unsafe-inline'; block-all-mixed-content; upgrade-insecure-requests", 'better-by-default' ),
				'class'                  => 'better-by-default-textarea',
				'field_description'      => __( "default-src https: data: 'unsafe-inline' 'unsafe-eval'; child-src https: data: blob:; connect-src https: data: blob: ; font-src https: data:; img-src https: data: blob:; media-src https: data: blob:; object-src https:; script-src https: data: blob: 'unsafe-inline' 'unsafe-eval'; style-src https: 'unsafe-inline'; block-all-mixed-content; upgrade-insecure-requests", 'better-by-default' ),
			),
			array(
				'field_id'               => 'permissions_policy',
				'field_name'             => 'permissions_policy',
				'field_title'            => __( 'Permissions-Policy', 'better-by-default' ),
				'field_type'             => 'textarea_subfield',
				'field_slug'             => 'permissions-policy',
				'field_rows'             => 5,
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_placeholder'      => __( 'e.g. camera=(), fullscreen=*, geolocation=(self), microphone=()', 'better-by-default' ),
				'class'                  => 'better-by-default-textarea',
				'field_description'      => 'camera=(), fullscreen=*, geolocation=(self), microphone=()',
			),
			array(
				'field_id'               => 'x_pingback',
				'field_name'             => 'x_pingback',
				'field_title'            => __( 'X-Pingback', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'x-pingback',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'true',
			),
			array(
				'field_id'               => 'x_hacker',
				'field_name'             => 'x_hacker',
				'field_title'            => __( 'X-Hacker', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'x-hacker',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'false',
			),
			array(
				'field_id'               => 'x_powered_by',
				'field_name'             => 'x_powered_by',
				'field_title'            => __( 'X-Powered-By', 'better-by-default' ),
				'field_type'             => 'render_checkbox_subfield',
				'field_slug'             => 'x-powered-by',
				'field_description'      => '',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'default_value'          => 'false',
			),
			array(
				'field_id'               => 'rest_api_access_control',
				'field_name'             => 'rest_api_access_control',
				'field_title'            => __( 'REST API Access Control', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'rest-api-access-control',
				'field_description'      => '<div class="tooltip_wrap"><p>' . __( 'Enable and choose the API access option for users.', 'better-by-default' ) . '</p><div class="tooltip" data-tooltip="' . esc_attr__( 'When user select the 3rd option then User need to logged in to access the /users endpoint.', 'better-by-default' ) . '"> <i class="dashicons-info-outline dashicons"></i></div></div>',
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'          => 'rest_api_access_control_options',
				'field_name'        => 'rest_api_access_control_options',
				'field_title'       => '',
				'field_type'        => 'radio_buttons_subfield',
				'field_slug'        => 'rest-api-access-control-options',
				'field_description' => '',
				'field_radios'      => array(
					__( 'Publicly accessible', 'better-by-default' ) => 'rest_api_everyone',
					__( 'Show Rest API to Logged in User', 'better-by-default' )   => 'rest_api_logged_in_user',
					__( 'Show Rest API to everyone except /users endpoint.', 'better-by-default' )   => 'rest_api_except_users_endpoint',
				),
				'default_value'     => 'rest_api_everyone',
			),
			array(
				'field_id'               => 'change_login_url',
				'field_name'             => 'change_login_url',
				'field_title'            => __( 'Customize Login URL', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'change-login-url',
				// translators: %s: default login URL.
				'field_description'      => __( 'Change the default login URL to a custom one to enhance security and reduce unauthorized access attempts. This makes it harder for attackers to find the login page.', 'better-by-default' ),
				'field_options_wrapper'  => true,
				'field_options_moreless' => true,
			),
			array(
				'field_id'               => 'custom_login_slug',
				'field_name'             => 'custom_login_slug',
				'field_title'            => __( 'New login URL', 'better-by-default' ),
				'field_type'             => 'text_subfield',
				'field_slug'             => 'custom-login-slug',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
				'field_prefix'           => site_url() . '/',
				'field_suffix'           => '/',
				'field_placeholder'      => __( 'e.g. backend', 'better-by-default' ),
				'field_description'      => '',
				'class'                  => 'better-by-default-text with-prefix-suffix custom-login-slug',
				'default_value'          => 'backend',
			),
			array(
				'field_id'          => 'change_login_url_description',
				'field_name'        => 'change_login_url_description',
				'field_title'       => '',
				'field_type'        => 'description_subfield',
				'field_slug'        => 'change-login-url-description',
				'field_description' => __( '<div class="better-by-default-warning">This feature <strong>only works for/with the default WordPress login page</strong>. It does not support using custom login page you manually created with a page builder or with another plugin.<br /><br />And obviously, to improve security, please <strong>use something other than \'login\'</strong> for the custom login slug.</div>', 'better-by-default' ),
				'class'             => 'better-by-default-description change-login-url-description',
			),
			array(
				'field_id'               => 'reserved_usernames',
				'field_name'             => 'reserved_usernames',
				'field_title'            => __( 'Reserved Usernames', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'reserved-usernames',
				'field_description'      => '<div class="tooltip_wrap"><p>' . __( 'Enable this setting to prevent users from registering usernames that are restricted or reserved. This ensures certain names remain unavailable for use.', 'better-by-default' ) . '</p><div class="tooltip" data-tooltip="' . esc_attr__( 'Ex. - admin, dev, dns, ftp, null, privacy, root, spam, support, tech, unsubscribe, www, administrator, user, username, demo, sql, guest, test, mysql, client, backup, blog, login, pass, password, tester, user2', 'better-by-default' ) . '"> <i class="dashicons-info-outline dashicons"></i></div></div>',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
			array(
				'field_id'               => 'strong_password',
				'field_name'             => 'strong_password',
				'field_title'            => __( 'Strong Password', 'better-by-default' ),
				'field_type'             => 'checkbox-toggle',
				'field_slug'             => 'storng-password',
				'field_description'      => '<div class="tooltip_wrap"><p>' . __( 'Enable this setting to require complex passwords, strengthening account security and reducing vulnerability to attacks.', 'better-by-default' ) . '</p><div class="tooltip" data-tooltip="' . esc_attr__( 'Ex. - 123456, Password, password, 12345678, 12345, 123456789, letmein, 1234567, admin, welcome, monkey, login, abc123, 123123, dragon, passw0rd, master, hello, freedom, whatever, 654321, password1, 1234', 'better-by-default' ) . '"> <i class="dashicons-info-outline dashicons"></i></div></div>',
				'field_options_wrapper'  => false,
				'field_options_moreless' => false,
			),
		);

		$fields = new Fields();

		foreach ( $protect_settings_array as $value ) {
			$field_slug = isset( $value['field_slug'] ) ? $value['field_slug'] : '';
			add_settings_field(
				$value['field_name'], // id.
				$value['field_title'], // title.
				array( $fields, 'add_field' ), // callback.
				'better-by-default-setting-page', // page.
				'better_by_default_setting_section', // section.
				array(
					'option_name'            => BETTER_BY_DEFAULT_PROTECT_OPTIONS,
					'field_id'               => isset( $value['field_id'] ) ? $value['field_id'] : '',
					'field_slug'             => $field_slug,
					'field_name'             => BETTER_BY_DEFAULT_PROTECT_OPTIONS . '[' . $value['field_name'] . ']',
					'field_description'      => isset( $value['field_description'] ) ? $value['field_description'] : '',
					'field_options_wrapper'  => isset( $value['field_options_wrapper'] ) ? $value['field_options_wrapper'] : false,
					'field_options_moreless' => isset( $value['field_options_moreless'] ) ? $value['field_options_moreless'] : false,
					'class'                  => 'better-by-default-toggle protect ' . $field_slug,
					'field_type'             => $value['field_type'],
					'table_name'             => isset( $value['table_name'] ) ? $value['table_name'] : '',
					'default_value'          => isset( $value['default_value'] ) ? $value['default_value'] : '',
					'field_radios'           => isset( $value['field_radios'] ) ? $value['field_radios'] : '',
					'field_prefix'           => isset( $value['field_prefix'] ) ? $value['field_prefix'] : '',
					'field_suffix'           => isset( $value['field_suffix'] ) ? $value['field_suffix'] : '',
					'field_placeholder'      => isset( $value['field_placeholder'] ) ? $value['field_placeholder'] : '',
				)
			);
		}
	}
}
