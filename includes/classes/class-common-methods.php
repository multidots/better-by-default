<?php
/**
 * The Common_Methods plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Common_Methods class File.
 */
class Common_Methods {


	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      BetterByDefault_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the common-methods functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// load class.
	}


	/**
	 * Get IP of the current visitor/user. In use by at least the Limit Login Attempts feature.
	 * This takes a best guess of the visitor's actual IP address.
	 * Takes into account numerous HTTP proxy headers due to variations
	 * in how different ISPs handle IP addresses in headers between hops.
	 *
	 * @param string $return_type 'ip' to return the IP address, 'header' to return the header name.
	 * @link https://stackoverflow.com/q/1634782
	 * @since 1.0.0
	 */
	public function get_user_ip_address( $return_type = 'ip' ) {
		$http_client_ip = filter_input( INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_VALIDATE_IP );

		$http_client_ip = isset( $http_client_ip ) && ! empty( $http_client_ip ) ? $http_client_ip : '';
		if ( ! empty( $http_client_ip ) && $this->is_ip_valid( $http_client_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_client_ip;
				case 'header':
					return 'HTTP_CLIENT_IP';
			}
		}
		// Check if Cloudflare is used as a proxy.
		// Ref: https://developers.cloudflare.com/fundamentals/reference/http-request-headers/#x-forwarded-for.
		$cf_connecting_ip = filter_input( INPUT_SERVER, 'CF_CONNECTING_IP', FILTER_VALIDATE_IP );
		$cf_connecting_ip = isset( $cf_connecting_ip ) && ! empty( $cf_connecting_ip ) ? $cf_connecting_ip : '';
		if ( ! empty( $cf_connecting_ip ) && $this->is_ip_valid( $cf_connecting_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $cf_connecting_ip;
				case 'header':
					return 'CF_CONNECTING_IP';
			}
		}

		$http_cf_connecting_ip = filter_input( INPUT_SERVER, 'HTTP_CF_CONNECTING_IP', FILTER_VALIDATE_IP );
		$http_cf_connecting_ip = isset( $http_cf_connecting_ip ) && ! empty( $http_cf_connecting_ip ) ? $http_cf_connecting_ip : '';
		if ( ! empty( $http_cf_connecting_ip ) && $this->is_ip_valid( $http_cf_connecting_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_cf_connecting_ip;
				case 'header':
					return 'HTTP_CF_CONNECTING_IP';
			}
		}

		$true_client_ip = filter_input( INPUT_SERVER, 'TRUE_CLIENT_IP', FILTER_VALIDATE_IP );
		$true_client_ip = isset( $true_client_ip ) && ! empty( $true_client_ip ) ? $true_client_ip : '';
		if ( ! empty( $true_client_ip ) && $this->is_ip_valid( $true_client_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $true_client_ip;
				case 'header':
					return 'TRUE_CLIENT_IP';
			}
		}

		$http_true_client_ip = filter_input( INPUT_SERVER, 'HTTP_TRUE_CLIENT_IP', FILTER_VALIDATE_IP );
		$http_true_client_ip = isset( $http_true_client_ip ) && ! empty( $http_true_client_ip ) ? $http_true_client_ip : '';
		if ( ! empty( $http_true_client_ip ) && $this->is_ip_valid( $http_true_client_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_true_client_ip;
				case 'header':
					return 'HTTP_TRUE_CLIENT_IP';
			}
		}
		// Check for IPs passing through proxies.
		$http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP );
		$http_x_forwarded_for = isset( $http_x_forwarded_for ) && ! empty( $http_x_forwarded_for ) ? $http_x_forwarded_for : '';
		if ( ! empty( $http_x_forwarded_for ) ) {
			// Check if multiple IP addresses exist in var.
			$ip_list = explode( ',', $http_x_forwarded_for );
			if ( is_array( $ip_list ) && count( $ip_list ) > 1 ) {
				foreach ( $ip_list as $ip ) {
					if ( $this->is_ip_valid( trim( $ip ) ) ) {
						switch ( $return_type ) {
							case 'ip':
								return sanitize_text_field( trim( $ip ) );
							case 'header':
								return 'HTTP_X_FORWARDED_FOR (multiple IPs)';
						}
					}
				}
			} else {
				switch ( $return_type ) {
					case 'ip':
						return $http_x_forwarded_for;
					case 'header':
						return 'HTTP_X_FORWARDED_FOR';
				}
			}
		}

		$http_x_forwarded = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED', FILTER_VALIDATE_IP );
		$http_x_forwarded = isset( $http_x_forwarded ) && ! empty( $http_x_forwarded ) ? $http_x_forwarded : '';
		if ( ! empty( $http_x_forwarded ) && $this->is_ip_valid( $http_x_forwarded ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_x_forwarded;
				case 'header':
					return 'HTTP_X_FORWARDED';
			}
		}

		$http_x_cluster_client_ip = filter_input( INPUT_SERVER, 'HTTP_X_CLUSTER_CLIENT_IP', FILTER_VALIDATE_IP );
		$http_x_cluster_client_ip = isset( $http_x_cluster_client_ip ) && ! empty( $http_x_cluster_client_ip ) ? $http_x_cluster_client_ip : '';
		if ( ! empty( $http_x_cluster_client_ip ) && $this->is_ip_valid( $http_x_cluster_client_ip ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_x_cluster_client_ip;
				case 'header':
					return 'HTTP_X_CLUSTER_CLIENT_IP';
			}
		}

		$http_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_FORWARDED_FOR', FILTER_VALIDATE_IP );
		$http_forwarded_for = isset( $http_forwarded_for ) && ! empty( $http_forwarded_for ) ? $http_forwarded_for : '';
		if ( ! empty( $http_forwarded_for ) && $this->is_ip_valid( $http_forwarded_for ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_forwarded_for;
				case 'header':
					return 'HTTP_FORWARDED_FOR';
			}
		}

		$http_forwarded = filter_input( INPUT_SERVER, 'HTTP_FORWARDED', FILTER_VALIDATE_IP );
		$http_forwarded = isset( $http_forwarded ) && ! empty( $http_forwarded ) ? $http_forwarded : '';
		if ( ! empty( $http_forwarded ) && $this->is_ip_valid( $http_forwarded ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $http_forwarded;
				case 'header':
					return 'HTTP_FORWARDED';
			}
		}
		// Return unreliable IP address since all else failed.
		$remote_addr = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP );
		$remote_addr = isset( $remote_addr ) && ! empty( $remote_addr ) ? $remote_addr : '';
		if ( ! empty( $remote_addr ) && $this->is_ip_valid( $remote_addr ) ) {
			switch ( $return_type ) {
				case 'ip':
					return $remote_addr;
				case 'header':
					return 'REMOTE_ADDR';
			}
		}
	}

	/**
	 * Check if the supplied IP address is valid or not
	 *
	 * @param  string $ip an IP address.
	 * @link https://stackoverflow.com/q/1634782
	 * @return boolean true if supplied address is valid IP, and false otherwise
	 */
	public function is_ip_valid( $ip ) {
		if ( empty( $ip ) ) {
			return false;
		}
		// Ref: https://www.php.net/manual/en/filter.filters.validate.php.
		if ( false === filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Convert number of seconds into hours, minutes, seconds. In use by at least the Limit Login Attempts feature.
	 *
	 * @param int    $seconds         Number of seconds to convert.
	 * @param string $conversion_type Type of conversion to perform.
	 * @since 1.0.0
	 */
	public function seconds_to_period( $seconds, $conversion_type ) {
		// Create DateTime objects.
		$period_start = new \DateTime( '@0' );
		$period_end   = new \DateTime( "@{$seconds}" );

		// Calculate and return the period.
		if ( 'to-days-hours-minutes-seconds' === $conversion_type ) {
			return esc_html( $period_start->diff( $period_end )->format( '%a days, %h hours, %i minutes and %s seconds' ) );
		} elseif ( 'to-hours-minutes-seconds' === $conversion_type ) {
			return esc_html( $period_start->diff( $period_end )->format( '%h hours, %i minutes and %s seconds' ) );
		} elseif ( 'to-minutes-seconds' === $conversion_type ) {
			return esc_html( $period_start->diff( $period_end )->format( '%i minutes and %s seconds' ) );
		} else {
			return esc_html( $period_start->diff( $period_end )->format( '%a days, %h hours, %i minutes and %s seconds' ) );
		}
	}

	/**
	 * Remove html tags and content inside the tags from a string
	 *
	 * @param string $str The string to strip HTML tags and content from.
	 * @since 1.0.0
	 */
	public function strip_html_tags_and_content( $str ) {
		// Strip HTML tags and content inside them.
		if ( ! is_null( $str ) ) {
			$str = preg_replace( '@<(\\w+)\\b.*?>.*?</\\1>@si', '', $str );
			// Strip any remaining HTML, CSS, and JS tags.
			$str = wp_strip_all_tags( $str );
		}
		return $str;
	}

	/**
	 * Get menu hidden by toggle
	 *
	 * @since 1.0.0
	 */
	public function get_menu_hidden_by_toggle() {
		$menu_hidden_by_toggle = array();
		$options               = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );
		if ( ! empty( $options ) && array_key_exists( 'custom_menu_hidden', $options ) ) {
			$menu_hidden           = $options['custom_menu_hidden'];
			$menu_hidden           = explode( ',', $menu_hidden );
			$menu_hidden_by_toggle = array();
			foreach ( $menu_hidden as $menu_id ) {
				$menu_hidden_by_toggle[] = $this->restore_menu_item_id( $menu_id );
			}
		}
		return $menu_hidden_by_toggle;
	}

	/**
	 * Get user capabilities for which the "Show All/Less" menu toggle should be shown for
	 *
	 * @since 1.0.0
	 */
	public function get_user_capabilities_to_show_menu_toggle_for() {
		global $menu;
		$user_capabilities_menus_are_hidden_for = array();
		$menu_hidden_by_toggle                  = $this->get_menu_hidden_by_toggle();
		foreach ( $menu as $menu_info ) {
			foreach ( $menu_hidden_by_toggle as $hidden_menu_id ) {
				if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
					$menu_item_id = $menu_info[2];
				} else {
					$menu_item_id = $menu_info[5];
				}
				if ( $menu_item_id === $hidden_menu_id ) {
					$user_capabilities_menus_are_hidden_for[] = $menu_info[1];
				}
			}
		}
		$user_capabilities_menus_are_hidden_for = array_unique( $user_capabilities_menus_are_hidden_for );
		return $user_capabilities_menus_are_hidden_for;
	}

	/**
	 * Transform menu item's ID
	 *
	 * @param string $menu_item_id The menu item ID.
	 * @since 1.0.0
	 */
	public function transform_menu_item_id( $menu_item_id ) {
		// Transform e.g. edit.php?post_type=page ==> edit__php___post_type____page.
		$menu_item_id_transformed = str_replace(
			array(
				'.',
				'?',
				'=/',
				'=',
				'&',
				'/',
			),
			array(
				'__',
				'___',
				'_______',
				'____',
				'_____',
				'______',
			),
			$menu_item_id
		);
		return $menu_item_id_transformed;
	}

	/**
	 * Transform menu item's ID
	 *
	 * @param string $menu_item_id_transformed The transformed menu item ID.
	 * @since 1.0.0
	 */
	public function restore_menu_item_id( $menu_item_id_transformed ) {
		// Transform e.g. edit__php___post_type____page ==> edit.php?post_type=page.
		$menu_item_id = str_replace(
			array(
				'_______',
				'______',
				'_____',
				'____',
				'___',
				'__',
			),
			array(
				'=/',
				'/',
				'&',
				'=',
				'?',
				'.',
			),
			$menu_item_id_transformed
		);
		return $menu_item_id;
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @since 1.0.0
	 */
	public function is_woocommerce_active() {
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the singular label from a $post object
	 *
	 * @param object $post The post object.
	 * @since 1.0.0
	 */
	public function get_post_type_singular_label( $post ) {
		$post_type_singular_label = '';
		if ( property_exists( $post, 'post_type' ) ) {
			$post_type_object = get_post_type_object( $post->post_type );
			if ( is_object( $post_type_object ) && property_exists( $post_type_object, 'label' ) ) {
				$post_type_singular_label = $post_type_object->labels->singular_name;
			}
		}
		return $post_type_singular_label;
	}

	/**
	 * Get the singular label of a post type
	 *
	 * @param [boolean] $has_editor Whether the post type has an editor.
	 * @since 1.0.0
	 */
	public function get_post_types( $has_editor = false ) {
		$better_by_default_public_post_types = array();
		$public_post_type_names              = get_post_types(
			array(
				'public'       => true,
				'show_in_rest' => true,
			),
			'names'
		);
		foreach ( $public_post_type_names as $post_type_name ) {

			if ( 'attachment' === $post_type_name ) {
				continue;
			}
			if ( $has_editor ) {
				if ( post_type_supports( $post_type_name, 'editor' ) ) {
					$post_type_object                                       = get_post_type_object( $post_type_name );
					$better_by_default_public_post_types[ $post_type_name ] = $post_type_object->label;
				}
			} else {
				$post_type_object                                       = get_post_type_object( $post_type_name );
				$better_by_default_public_post_types[ $post_type_name ] = $post_type_object->label;
			}
		}
		asort( $better_by_default_public_post_types );
		return $better_by_default_public_post_types;
	}
}
