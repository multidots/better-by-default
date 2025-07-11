<?php
/**
 * The settings-sections-fields-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the settings-sections-fields-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Setting_Fields\Miscellaneous_Settings;
use BetterByDefault\Inc\Setting_Fields\About_Settings;
use BetterByDefault\Inc\Setting_Fields\Simplify_Settings;
use BetterByDefault\Inc\Setting_Fields\Protect_Settings;
use BetterByDefault\Inc\Setting_Fields\Performance_Settings;
use BetterByDefault\Inc\Setting_Fields\Personalize_Settings;
/**
 * Settings_Sections_Fields class file.
 */
class Settings_Sections_Fields {

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

		$this->setup_settings_page_hooks();
	}
	/**
	 * Function is used to define setup_settings_page hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_settings_page_hooks() {

		add_action( 'admin_init', array( $this, 'better_by_default_admin_site_enhancements_init' ) );
		add_action( 'admin_menu', array( $this, 'better_by_default_add_plugin_page' ) );
	}

	/**
	 * Function is used to create plugin page
	 */
	public function better_by_default_add_plugin_page() {

		add_menu_page(
			__( 'Better By Default', 'better-by-default' ), // page_title.
			__( 'Better By Default', 'better-by-default' ), // menu_title.
			'manage_options', // capability.
			'better-by-default-settings', // menu_slug.
			array( $this, 'create_admin_site_enhancements_page' ), // callback function.
			BETTER_BY_DEFAULT_URL . 'assets/src/images/BBD-icon.svg', // icon_url.
			2 // position.
		);
	}

	/**
	 * Function is used register settings.
	 */
	public function better_by_default_admin_site_enhancements_init() {

		// Register Miscellaneous settings.
		register_setting( // phpcs:ignore
			'better_by_default_option_group', // option_group.
			'better_by_default_miscellaneous_option', // option_name.
			array( $this, 'better_by_default_sanitize' ) // sanitize_callback.
		);

		// Register Simplify settings.
		register_setting( // phpcs:ignore
			'better_by_default_option_group', // option_group.
			'better_by_default_simplify_option', // option_name.
			array( $this, 'better_by_default_sanitize' ) // sanitize_callback.
		);

		// Register Personalize settings.
		register_setting( // phpcs:ignore
			'better_by_default_option_group', // option_group.
			'better_by_default_personalize_option', // option_name.
			array( $this, 'better_by_default_sanitize' ) // sanitize_callback.
		);

		// Register Performance settings.
		register_setting( // phpcs:ignore
			'better_by_default_option_group', // option_group.
			'better_by_default_performance_option', // option_name.
			array( $this, 'better_by_default_sanitize' ) // sanitize_callback.
		);

		// Register Protect settings.
		register_setting( // phpcs:ignore
			'better_by_default_option_group', // option_group.
			'better_by_default_protect_option', // option_name.
			array( $this, 'better_by_default_sanitize' ) // sanitize_callback.
		);

		// Add Settings Section.
		add_settings_section(
			'better_by_default_setting_section', // id.
			'', // title.
			'', // callback.
			'better-by-default-setting-page' // page.
		);

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-miscellaneous-settings.php';
		$miscellaneous = new Miscellaneous_Settings();
		$miscellaneous->better_by_default_miscellaneous_page_init();

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-about-settings.php';
		$about = new About_Settings();
		$about->better_by_default_about_page_init();

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-simplify-settings.php';
		$simplify = new Simplify_Settings();
		$simplify->better_by_default_simplify_page_init();

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-personalize-settings.php';
		$personalize = new Personalize_Settings();
		$personalize->better_by_default_personalize_page_init();

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-protect-settings.php';
		$protect = new Protect_Settings();
		$protect->better_by_default_protect_page_init();

		require_once BETTER_BY_DEFAULT_DIR . 'includes/classes/setting-fields/class-performance-settings.php';
		$performance = new Performance_Settings();
		$performance->better_by_default_performance_page_init();
	}

	/**
	 * function is used to sanitize the input.
	 *
	 * @param [type] $input
	 * @return void
	 */
	public function better_by_default_sanitize( $input ) {

		$sanitized_input = array();

		// Miscellaneous settings inputs:

		// Sanitize boolean values (true/false)
		$sanitized_input['default_template_network_site'] = isset( $input['default_template_network_site'] ) ? (bool) $input['default_template_network_site'] : false;
		$sanitized_input['maintenance_mode'] = isset( $input['maintenance_mode'] ) ? (bool) $input['maintenance_mode'] : false;
		$sanitized_input['activity_log'] = isset( $input['activity_log'] ) ? (bool) $input['activity_log'] : false;
		$sanitized_input['enable_public_page_preview'] = isset( $input['enable_public_page_preview'] ) ? (bool) $input['enable_public_page_preview'] : false;
		$sanitized_input['disable_crawling'] = isset( $input['disable_crawling'] ) ? (bool) $input['disable_crawling'] : false;
		$sanitized_input['cache_functionality'] = isset( $input['cache_functionality'] ) ? (bool) $input['cache_functionality'] : false;
	
		// Sanitize text fields (strings)
		$sanitized_input['maintenance_page_heading'] = isset( $input['maintenance_page_heading'] ) ? sanitize_text_field( $input['maintenance_page_heading'] ) : '';
		$sanitized_input['maintenance_page_description'] = isset( $input['maintenance_page_description'] ) ? sanitize_textarea_field( $input['maintenance_page_description'] ) : '';
	
		// Sanitize URL (for page_cache field)
		$sanitized_input['page_cache'] = isset($input['page_cache']) ? esc_url_raw($input['page_cache']) : '';
	

		if (isset($input['bbd_disable_dashboard_widgets'])) {
			$sanitized_input['bbd_disable_dashboard_widgets'] = (bool) $input['bbd_disable_dashboard_widgets'];
		}
	
		if (isset($input['disable_comments'])) {
			$sanitized_input['disable_comments'] = (bool) $input['disable_comments'];
		}
	
		if (isset($input['disable_post_tags'])) {
			$sanitized_input['disable_post_tags'] = (bool) $input['disable_post_tags'];
		}
	
		if (isset($input['custom_admin_footer_text'])) {
			$sanitized_input['custom_admin_footer_text'] = (bool) $input['custom_admin_footer_text'];
		}
	
		if (isset($input['hide_admin_bar'])) {
			$sanitized_input['hide_admin_bar'] = (bool) $input['hide_admin_bar'];
		}
	
		if (isset($input['customize_list_tables'])) {
			$sanitized_input['customize_list_tables'] = (bool) $input['customize_list_tables'];
		}
	
		if (isset($input['enable_search_by_title'])) {
			$sanitized_input['enable_search_by_title'] = (bool) $input['enable_search_by_title'];
		}
	
		if (isset($input['enable_last_login_column'])) {
			$sanitized_input['enable_last_login_column'] = (bool) $input['enable_last_login_column'];
		}

		// Simplify settings inputs:
	
		// Sanitize nested arrays like 'bbd_disabled_dashboard_widgets'
		if (isset($input['bbd_disabled_dashboard_widgets']) && is_array($input['bbd_disabled_dashboard_widgets'])) {
			$sanitized_input['bbd_disabled_dashboard_widgets'] = array();
			foreach ($input['bbd_disabled_dashboard_widgets'] as $key => $value) {
				$sanitized_input['bbd_disabled_dashboard_widgets'][$key] = (bool) $value;
			}
		}

		if (isset($input['extra_list_table_columns']) && is_array($input['extra_list_table_columns'])) {
			$sanitized_input['extra_list_table_columns'] = array();
			foreach ($input['extra_list_table_columns'] as $key => $value) {
				$sanitized_input['extra_list_table_columns'][$key] = (bool) $value;
			}
		}
	
		// Sanitize 'hide_admin_bar_for' nested array
		if (isset($input['hide_admin_bar_for']) && is_array($input['hide_admin_bar_for'])) {
			$sanitized_input['hide_admin_bar_for'] = array();
			foreach ($input['hide_admin_bar_for'] as $role => $value) {
				$sanitized_input['hide_admin_bar_for'][$role] = (bool) $value;
			}
		}
	
		// Sanitize text fields (e.g., 'custom_admin_footer_left' and 'custom_admin_footer_right')
		if (isset($input['custom_admin_footer_left'])) {
			$sanitized_input['custom_admin_footer_left'] = wp_kses_post($input['custom_admin_footer_left']); // Allow some HTML tags
		}
	
		if (isset($input['custom_admin_footer_right'])) {
			$sanitized_input['custom_admin_footer_right'] = wp_kses_post($input['custom_admin_footer_right']); // Allow some HTML tags
		}
	
		// Handle any other text fields
		if (isset($input['custom_admin_footer_text'])) {
			$sanitized_input['custom_admin_footer_text'] = sanitize_text_field($input['custom_admin_footer_text']);
		}


		// Personalize settings inputs:

		// Sanitize boolean values (true/false)
		if (isset($input['admin_color_branding'])) {
			$sanitized_input['admin_color_branding'] = (bool) $input['admin_color_branding'];
		}
	
		if (isset($input['site_identity_on_login_page'])) {
			$sanitized_input['site_identity_on_login_page'] = (bool) $input['site_identity_on_login_page'];
		}
	
		if (isset($input['disable_back_to_blog_link'])) {
			$sanitized_input['disable_back_to_blog_link'] = (bool) $input['disable_back_to_blog_link'];
		}
	
		if (isset($input['user_account_style'])) {
			$sanitized_input['user_account_style'] = (bool) $input['user_account_style'];
		}
	
		if (isset($input['enable_duplication'])) {
			$sanitized_input['enable_duplication'] = (bool) $input['enable_duplication'];
		}
	
		if (isset($input['customize_admin_menu'])) {
			$sanitized_input['customize_admin_menu'] = (bool) $input['customize_admin_menu'];
		}
	
		if (isset($input['disable_block_editor'])) {
			$sanitized_input['disable_block_editor'] = (bool) $input['disable_block_editor'];
		}
	
		// Sanitize color fields (admin color scheme)
		if (isset($input['admin_color_scheme_base_color'])) {
			$sanitized_input['admin_color_scheme_base_color'] = sanitize_hex_color($input['admin_color_scheme_base_color']);
		}
	
		if (isset($input['admin_color_scheme_icon_color'])) {
			$sanitized_input['admin_color_scheme_icon_color'] = sanitize_hex_color($input['admin_color_scheme_icon_color']);
		}
	
		if (isset($input['admin_color_scheme_text_color'])) {
			$sanitized_input['admin_color_scheme_text_color'] = sanitize_hex_color($input['admin_color_scheme_text_color']);
		}
	
		if (isset($input['admin_color_scheme_highlight_color'])) {
			$sanitized_input['admin_color_scheme_highlight_color'] = sanitize_hex_color($input['admin_color_scheme_highlight_color']);
		}
	
		if (isset($input['admin_color_scheme_accent_color'])) {
			$sanitized_input['admin_color_scheme_accent_color'] = sanitize_hex_color($input['admin_color_scheme_accent_color']);
		}
	
		if (isset($input['admin_color_scheme_link_color'])) {
			$sanitized_input['admin_color_scheme_link_color'] = sanitize_hex_color($input['admin_color_scheme_link_color']);
		}
	
		if (isset($input['login_highlight_color'])) {
			$sanitized_input['login_highlight_color'] = sanitize_hex_color($input['login_highlight_color']);
		}
	
		if (isset($input['login_highlight_color_hover'])) {
			$sanitized_input['login_highlight_color_hover'] = sanitize_hex_color($input['login_highlight_color_hover']);
		}
	
		// Sanitize login header image URL
		if (isset($input['login_header_image_url'])) {
			$sanitized_input['login_header_image_url'] = esc_url_raw($input['login_header_image_url']);
		}
	
		// Sanitize login header image size (assuming it's an integer)
		if (isset($input['login_header_image_size'])) {
			$sanitized_input['login_header_image_size'] = (int) $input['login_header_image_size'];
		}
	
		// Sanitize menu order (a string of comma-separated menu IDs)
		if (isset($input['custom_menu_order'])) {
			$sanitized_input['custom_menu_order'] = sanitize_text_field($input['custom_menu_order']);
		}
	
		// Sanitize custom menu titles (comma-separated "menu-slug__Title" pairs)
		if (isset($input['custom_menu_titles'])) {
			$sanitized_input['custom_menu_titles'] = sanitize_text_field($input['custom_menu_titles']);
		}
	
		// Sanitize hidden menu items (comma-separated list)
		if (isset($input['custom_menu_hidden'])) {
			$sanitized_input['custom_menu_hidden'] = sanitize_text_field($input['custom_menu_hidden']);
		}
	
		// Sanitize hidden menu icons (comma-separated list)
		if (isset($input['custom_menu_icons_hidden'])) {
			$sanitized_input['custom_menu_icons_hidden'] = sanitize_text_field($input['custom_menu_icons_hidden']);
		}
	
		// Sanitize nested arrays for 'customize_admin_menu_colors'
		if (isset($input['customize_admin_menu_colors']) && is_array($input['customize_admin_menu_colors'])) {
			$sanitized_input['customize_admin_menu_colors'] = array();
			foreach ($input['customize_admin_menu_colors'] as $menu_item => $color) {
				$sanitized_input['customize_admin_menu_colors'][$menu_item] = sanitize_hex_color($color);
			}
		}
	
		// Sanitize nested arrays for 'customize_admin_menu_dashicons'
		if (isset($input['customize_admin_menu_dashicons']) && is_array($input['customize_admin_menu_dashicons'])) {
			$sanitized_input['customize_admin_menu_dashicons'] = array();
			foreach ($input['customize_admin_menu_dashicons'] as $menu_item => $dashicon) {
				$sanitized_input['customize_admin_menu_dashicons'][$menu_item] = sanitize_text_field($dashicon);
			}
		}
	
		// Sanitize 'disable_block_editor_for' nested array
		if (isset($input['disable_block_editor_for']) && is_array($input['disable_block_editor_for'])) {
			$sanitized_input['disable_block_editor_for'] = array();
			foreach ($input['disable_block_editor_for'] as $post_type => $disable) {
				$sanitized_input['disable_block_editor_for'][$post_type] = (bool) $disable;
			}
		}

		// Performance settings inputs:

		// Sanitize boolean values (true/false)
		if (isset($input['disable_obscure_wp_head_items'])) {
			$sanitized_input['disable_obscure_wp_head_items'] = (bool) $input['disable_obscure_wp_head_items'];
		}
	
		if (isset($input['remove_shortlinks'])) {
			$sanitized_input['remove_shortlinks'] = (bool) $input['remove_shortlinks'];
		}
	
		if (isset($input['remove_rss_links'])) {
			$sanitized_input['remove_rss_links'] = (bool) $input['remove_rss_links'];
		}
	
		if (isset($input['remove_rest_api_links'])) {
			$sanitized_input['remove_rest_api_links'] = (bool) $input['remove_rest_api_links'];
		}
	
		if (isset($input['remove_rsd_wlw_links'])) {
			$sanitized_input['remove_rsd_wlw_links'] = (bool) $input['remove_rsd_wlw_links'];
		}
	
		if (isset($input['remove_oembed_links'])) {
			$sanitized_input['remove_oembed_links'] = (bool) $input['remove_oembed_links'];
		}
	
		if (isset($input['remove_generator_tag'])) {
			$sanitized_input['remove_generator_tag'] = (bool) $input['remove_generator_tag'];
		}
	
		if (isset($input['remove_emoji_scripts'])) {
			$sanitized_input['remove_emoji_scripts'] = (bool) $input['remove_emoji_scripts'];
		}
	
		if (isset($input['remove_pingback'])) {
			$sanitized_input['remove_pingback'] = (bool) $input['remove_pingback'];
		}
	
		if (isset($input['enable_lazy_load_embeds'])) {
			$sanitized_input['enable_lazy_load_embeds'] = (bool) $input['enable_lazy_load_embeds'];
		}
	
		if (isset($input['lazy_load_youtube'])) {
			$sanitized_input['lazy_load_youtube'] = (bool) $input['lazy_load_youtube'];
		}
	
		if (isset($input['lazy_load_iframe'])) {
			$sanitized_input['lazy_load_iframe'] = (bool) $input['lazy_load_iframe'];
		}
	
		if (isset($input['enable_critical_css'])) {
			$sanitized_input['enable_critical_css'] = (bool) $input['enable_critical_css'];
		}
	
		// Sanitize text field (e.g., 'common_critical_css')
		if (isset($input['common_critical_css'])) {
			$sanitized_input['common_critical_css'] = sanitize_text_field($input['common_critical_css']);
		}
	
		// Sanitize nested arrays for 'critical_css_for'
		if (isset($input['critical_css_for']) && is_array($input['critical_css_for'])) {
			$sanitized_input['critical_css_for'] = array();
			foreach ($input['critical_css_for'] as $post_type => $enabled) {
				$sanitized_input['critical_css_for'][$post_type] = (bool) $enabled;
			}
		}

		// Protect settings inputs:
		
		// Sanitize boolean values (true/false)
		if (isset($input['limit_login_attempts'])) {
			$sanitized_input['limit_login_attempts'] = (bool) $input['limit_login_attempts'];
		}
	
		if (isset($input['disable_xml_rpc'])) {
			$sanitized_input['disable_xml_rpc'] = (bool) $input['disable_xml_rpc'];
		}
	
		if (isset($input['security_headers'])) {
			$sanitized_input['security_headers'] = (bool) $input['security_headers'];
		}
	
		if (isset($input['x_pingback'])) {
			$sanitized_input['x_pingback'] = (bool) $input['x_pingback'];
		}
	
		if (isset($input['x_hacker'])) {
			$sanitized_input['x_hacker'] = (bool) $input['x_hacker'];
		}
	
		if (isset($input['x_powered_by'])) {
			$sanitized_input['x_powered_by'] = (bool) $input['x_powered_by'];
		}
	
		if (isset($input['rest_api_access_control'])) {
			$sanitized_input['rest_api_access_control'] = (bool) $input['rest_api_access_control'];
		}
	
		if (isset($input['change_login_url'])) {
			$sanitized_input['change_login_url'] = (bool) $input['change_login_url'];
		}
	
		if (isset($input['reserved_usernames'])) {
			$sanitized_input['reserved_usernames'] = (bool) $input['reserved_usernames'];
		}
	
		if (isset($input['strong_password'])) {
			$sanitized_input['strong_password'] = (bool) $input['strong_password'];
		}
	
		// Sanitize integer values (e.g., 'failed_login_attempts' and 'login_lockout_maxcount')
		if (isset($input['failed_login_attempts'])) {
			$sanitized_input['failed_login_attempts'] = absint($input['failed_login_attempts']);
		}
	
		if (isset($input['login_lockout_maxcount'])) {
			$sanitized_input['login_lockout_maxcount'] = absint($input['login_lockout_maxcount']);
		}
	
		// Sanitize security headers and related strings
		if (isset($input['x_frame_options'])) {
			$sanitized_input['x_frame_options'] = sanitize_text_field($input['x_frame_options']);
		}
	
		if (isset($input['x_content_type_options'])) {
			$sanitized_input['x_content_type_options'] = sanitize_text_field($input['x_content_type_options']);
		}
	
		if (isset($input['x_xss_protection'])) {
			$sanitized_input['x_xss_protection'] = sanitize_text_field($input['x_xss_protection']);
		}
	
		if (isset($input['referrer_policy'])) {
			$sanitized_input['referrer_policy'] = sanitize_text_field($input['referrer_policy']);
		}
	
		if (isset($input['content_security_policy'])) {
			// Content Security Policy should be sanitized carefully, but this example assumes it's safe text input.
			$sanitized_input['content_security_policy'] = sanitize_textarea_field($input['content_security_policy']);
		}
	
		if (isset($input['permissions_policy'])) {
			$sanitized_input['permissions_policy'] = sanitize_textarea_field($input['permissions_policy']);
		}
	
		// Sanitize 'rest_api_access_control_options' - expected to be a string value (e.g., 'rest_api_everyone')
		if (isset($input['rest_api_access_control_options'])) {
			$sanitized_input['rest_api_access_control_options'] = sanitize_text_field($input['rest_api_access_control_options']);
		}
	
		// Sanitize 'custom_login_slug' - expected to be a simple string (slug)
		if (isset($input['custom_login_slug'])) {
			$sanitized_input['custom_login_slug'] = sanitize_text_field($input['custom_login_slug']);
		}

		return $sanitized_input;

	}


	/**
	 * Function is used to create admin page.
	 */
	public function create_admin_site_enhancements_page() {
		?>
		<span><?php settings_errors( '', false, true ); ?></span>
		<div id="better-by-default-header" class="better-by-default-header" style="position: sticky; width: auto;">
			<div class="better-by-default-header-left">
				<a href="#" target="_blank" class="main-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/BBD.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo" alt="md logo" /> </a>
				<a href="#" target="_blank" class="main-logo mobile-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/BBD-icon.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo mobile-logo-img" alt="md logo" /> </a>
				<!-- <h1 class="better-by-default-heading">
					Better By Default
				</h1> -->
				<div class="header-tab-more">
					<span class="bbd-tab bbd-more-tab"><i class="bbd-icon bbd-icon-more"></i>More Tools <i class="bbd-icon bbd-icon-dropdown"></i></span>
					<ul class="header-dd">
						<li>
							<a class="bbd-tab bbd-header-tab-bbd-tools bbd-tab__reset" href="#">Default Settings</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="better-by-default-header-right">
				<a class="button button-primary better-by-default-save-button">Save Changes</a>
				<div class="better-by-default-saving-changes" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#2271b1" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"></path><path fill="#2271b1" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"></animateTransform></path></svg></div>
				<div class="better-by-default-changes-saved" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path fill="seagreen" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zM9.29 16.29L5.7 12.7a.996.996 0 1 1 1.41-1.41L10 14.17l6.88-6.88a.996.996 0 1 1 1.41 1.41l-7.59 7.59a.996.996 0 0 1-1.41 0z"></path></svg></div>
				<a href="https://www.multidots.com/" target="_blank" class="md-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/MD-Logo.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo" alt="md logo" /> </a>
			</div>
		</div>	
		<form action='options.php' enctype="multipart/form-data" method='post'>
			<div class="main-better-by-default-wrap">
				<div class="main-bbd-desc">
					<p>Make WordPress simpler, personalized, optimized, and secure—right out of the box. Better by default, for a better experience. Improve site efficiency and user interactions effortlessly, ensuring that every aspect of WordPress works for you, not against you. Embrace a cleaner, faster, and more reliable WordPress without the hassle.</p>
				</div>
				<div class="tabs">
					<header class="tab-buttons">
						<button class="tab-button" data-tab="simplify">
							<span>Simplify</span>
						</button>
						<button class="tab-button" data-tab="personalize">
							<span>Personalize</span>
						</button>
						<button class="tab-button" data-tab="performance">
							<span>Performance</span>
						</button>
						<button class="tab-button" data-tab="protect">
							<span>Protect</span>
						</button>
						<button class="tab-button" data-tab="miscellaneous">
							<span>Miscellaneous</span>
						</button>
						<button class="tab-button" data-tab="about">
							<span>About</span>
						</button>
					</header>
					<div class="tab-contents">
						<div class="tab-content" data-tab="simplify">
							<section class="better-by-default-fields fields-simplify">
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-content" data-tab="personalize">
							<section class="better-by-default-fields fields-personalize">
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-content" data-tab="performance">
							<section class="better-by-default-fields fields-performance">
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-content" data-tab="protect">
							<section class="better-by-default-fields fields-protect">
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-content" data-tab="miscellaneous">
							<section class="better-by-default-fields fields-miscellaneous"> 
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-content" data-tab="about">
							<section class="better-by-default-fields fields-about"> 
								<table class="form-table" role="presentation">
									<tbody></tbody>
								</table>
							</section>
						</div>
						<div class="tab-loader"><div class="loader"></div></div>
					</div>
					<div style="display:none">
						<?php
							settings_fields( 'better_by_default_option_group' );
							do_settings_sections( 'better-by-default-setting-page' );
							submit_button(
								__( 'Save Changes', 'better-by-default' ),
								'primary',
								'submit',
								true,
								array(
									'id' => 'better-by-default-submit',
								)
							);

						?>
					</div>
				</div>
				<div class="bbd-bottom">
					<p><?php echo wp_kses_post( __( 'Crafted by the experts at <a href="https://www.multidots.com/" target="_blank">Multidots</a>, designed for professionals who build with WordPress.', 'better-by-default' ) ); ?></p>
				</div>
			</div>
		</form>
		<?php
	}
}
