<?php
/**
 * The admin-menu-organization-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Admin_Menu_Organization class file.
 */
class Admin_Menu_Organization {

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
	 * Options retrieved from settings.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The default title of 'Posts' menu item.
	 *
	 * @var string
	 */
	private $posts_default_title;

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

		$this->setup_admin_menu_organization_hooks();
	}
	/**
	 * Function is used to define admin-menu-organization hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_menu_organization_hooks() {
		global $wp_post_types;
		$this->options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		$options = $this->options;

		if ( ! empty( $options ) && array_key_exists( 'custom_menu_order', $options ) ) {
			add_filter( 'custom_menu_order', '__return_true', PHP_INT_MAX );
			add_filter( 'menu_order', array( $this, 'render_custom_menu_order' ), PHP_INT_MAX );
		}
		if ( ! empty( $options ) && array_key_exists( 'custom_menu_titles', $options ) ) {
			add_action( 'admin_menu', array( $this, 'apply_custom_menu_item_titles' ), 999995 );
			// For 'Posts' menu, if the title has been changed, try changing the labels for it everywhere.
			$custom_menu_titles = explode( ',', $options['custom_menu_titles'] );
			foreach ( $custom_menu_titles as $custom_menu_title ) {
				if ( false !== strpos( $custom_menu_title, 'menu-posts__' ) ) {
					$custom_menu_title         = explode( '__', $custom_menu_title );
					$posts_custom_title        = $custom_menu_title[1];
					$posts_default_title       = isset( $wp_post_types['post']->label ) ? $wp_post_types['post']->label : '';
					$this->posts_default_title = $posts_default_title;
					if ( $posts_default_title !== $posts_custom_title ) {
						add_filter( 'post_type_labels_post', array( $this, 'change_post_labels' ) );
						add_action( 'init', array( $this, 'change_post_object_label' ) );
						add_action( 'admin_menu', array( $this, 'change_post_menu_label' ), PHP_INT_MAX );
						add_action( 'admin_bar_menu', array( $this, 'change_wp_admin_bar' ), 80 );
					}
				}
			}
		}



		if ( ! empty( $options ) && ( array_key_exists( 'custom_menu_hidden', $options ) || array_key_exists( 'custom_menu_always_hidden', $options ) ) ) {
			add_action( 'admin_menu', array( $this, 'hide_menu_items' ), 999996 );
			add_action( 'admin_menu', array( $this, 'add_hidden_menu_toggle' ), 999997 );
			add_action( 'admin_head', array( $this, 'admin_menu_color_styles' ) );
		}
	}

	/**
	 * Render custom menu order
	 *
	 * @since   1.0.0
	 */
	public function render_custom_menu_order() {
		global $menu;
		$options = $this->options;
		// Get current menu order. We're not using the default $menu_order which uses index.php, edit.php as array values.
		$current_menu_order = array();
		foreach ( $menu as $menu_info ) {
			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}
			$current_menu_order[] = array( $menu_item_id, $menu_info[2] );
		}
		// Get custom menu order.
		$custom_menu_order = $options['custom_menu_order'];
		// comma separated.
		$custom_menu_order = explode( ',', $custom_menu_order );
		// array of menu ID, e.g. menu-dashboard.
		// Return menu order for rendering.
		$rendered_menu_order = array();
		// Render menu based on items saved in custom menu order.
		foreach ( $custom_menu_order as $custom_menu_item_id ) {
			foreach ( $current_menu_order as $current_menu_item ) {
				if ( $custom_menu_item_id === $current_menu_item[0] ) {
					$rendered_menu_order[] = $current_menu_item[1];
				}
			}
		}
		// Add items from current menu not already part of custom menu order, e.g. new plugin activated and adds new menu item.
		foreach ( $current_menu_order as $current_menu_item ) {
			if ( ! in_array( $current_menu_item[0], $custom_menu_order, true ) ) {
				$rendered_menu_order[] = $current_menu_item[1];
			}
		}
		return $rendered_menu_order;
	}

	/**
	 * Apply custom menu item titles
	 *
	 * @since   1.0.0
	 */
	public function apply_custom_menu_item_titles() {
		global $menu;
		$options = $this->options;
		// Get custom menu item titles.
		$custom_menu_titles = $options['custom_menu_titles'];
		$custom_menu_titles = explode( ',', $custom_menu_titles );
		foreach ( $menu as $menu_key => $menu_info ) {
			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}
			// Get defaul/custom menu item title.
			foreach ( $custom_menu_titles as $custom_menu_title ) {
				// At this point, $custom_menu_title value looks like toplevel_page_snippets__Code Snippets.
				$custom_menu_title = explode( '__', $custom_menu_title );
				if ( $custom_menu_title[0] === $menu_item_id ) {
					$menu_item_title = $custom_menu_title[1];
					// e.g. Code Snippets.
					break;
					// stop foreach loop so $menu_item_title is not overwritten in the next iteration.
				} else {
					$menu_item_title = $menu_info[0];
				}
			}
			$menu[$menu_key][0] = $menu_item_title;		//phpcs:ignore
		}
	}

	/**
	 * Get custom title for 'Posts' menu item
	 *
	 * @since   1.0.0
	 */
	public function get_posts_custom_title() {
		$post_object         = get_post_type_object( 'post' );
		$posts_default_title = '';
		if ( is_object( $post_object ) ) {
			if ( property_exists( $post_object, 'label' ) ) {
				$posts_default_title = $post_object->label;
			} else {
				$posts_default_title = $post_object->labels->name;
			}
		}
		$posts_custom_title = $posts_default_title;
		$options            = $this->options;
		$custom_menu_titles = ( isset( $options['custom_menu_titles'] ) ? explode( ',', $options['custom_menu_titles'] ) : array() );
		if ( ! empty( $custom_menu_titles ) ) {
			foreach ( $custom_menu_titles as $custom_menu_title ) {
				if ( false !== strpos( $custom_menu_title, 'menu-posts__' ) ) {
					$custom_menu_title  = explode( '__', $custom_menu_title );
					$posts_custom_title = $custom_menu_title[1];
				}
			}
		}
		return $posts_custom_title;
	}

	/**
	 * For 'Posts', apply custom label
	 *
	 * @param object $labels Post type labels.
	 *
	 * @since   1.0.0
	 */
	public function change_post_labels( $labels ) {
		$post_object                = get_post_type_object( 'post' );
		$posts_default_title_plural = '';
		if ( is_object( $post_object ) ) {
			if ( property_exists( $post_object, 'label' ) ) {
				$posts_default_title_plural = $post_object->label;
			} else {
				$posts_default_title_plural = $post_object->labels->name;
			}
		}
		$posts_default_title_singular = isset( $post_object->labels->singular_name ) ? $post_object->labels->singular_name : '';
		$posts_custom_title           = $this->get_posts_custom_title();
		foreach ( $labels as $key => $label ) {
			if ( null === $label ) {
				continue;
			}
			$labels->{$key} = str_replace( array( $posts_default_title_plural, $posts_default_title_singular ), $posts_custom_title, $label );
		}
		return $labels;
	}

	/**
	 * For 'Posts', apply custom label in post object
	 *
	 * @since   1.0.0
	 */
	public function change_post_object_label() {
		global $wp_post_types;
		$posts_custom_title    = $this->get_posts_custom_title();
		$labels                =& $wp_post_types['post']->labels;
		$labels->name          = $posts_custom_title;
		$labels->singular_name = $posts_custom_title;
		$labels->add_new       = __( 'Add New', 'better-by-default' );
		$labels->add_new_item  = __( 'Add New', 'better-by-default' );
		$labels->edit_item     = __( 'Edit', 'better-by-default' );
		$labels->new_item      = $posts_custom_title;
		$labels->view_item     = __( 'View', 'better-by-default' );
		$labels->search_items  = sprintf(
			/* translators: %s is the post type label */
			'Search %s',
			$posts_custom_title
		);
		$labels->not_found = sprintf(
			/* translators: %s is the post type label */
			'No %s found',
			strtolower( $posts_custom_title )
		);
		$labels->not_found_in_trash = sprintf(
			/* translators: %s is the post type label */
			'No %s found in Trash',
			strtolower( $posts_custom_title )
		);
	}

	/**
	 * For 'Posts', apply custom label in menu and submenu
	 *
	 * @since   1.0.0
	 */
	public function change_post_menu_label() {
		global $submenu;
		$posts_custom_title  = $this->get_posts_custom_title();
		$posts_default_title = $this->posts_default_title;
		if ( ! empty( $posts_custom_title ) ) {
			$submenu['edit.php'][5][0] = sprintf( 	//phpcs:ignore
				/* translators: %s is the post type label */
				'All %s',
				$posts_custom_title
			);
		} else {
			$submenu['edit.php'][5][0] = sprintf( 	//phpcs:ignore
				/* translators: %s is the post type label */
				'All %s',
				$posts_default_title
			);
		}
	}

	/**
	 * For 'Posts', apply custom label in admin bar
	 *
	 * @param object $wp_admin_bar WP_Admin_Bar object.
	 *
	 * @since   1.0.0
	 */
	public function change_wp_admin_bar( $wp_admin_bar ) {
		$posts_custom_title = $this->get_posts_custom_title();
		$new_post_node      = $wp_admin_bar->get_node( 'new-post' );
		if ( $new_post_node ) {
			$new_post_node->title = $posts_custom_title;
			$wp_admin_bar->add_node( $new_post_node );
		}
	}

	/**
	 * Hide parent menu items by adding class(es) to hide them
	 *
	 * @since   1.0.0
	 */
	public function hide_menu_items() {
		global $menu;
		$options               = $this->options;
		$common_methods        = new \BetterByDefault\Inc\Common_Methods();
		$menu_hidden_by_toggle = $common_methods->get_menu_hidden_by_toggle();
		foreach ( $menu as $menu_key => $menu_info ) {

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}

			// Append 'hidden' class to hide menu item until toggled.
			if ( in_array( $menu_item_id, $menu_hidden_by_toggle, true ) ) {
				$menu[$menu_key][4] = $menu_info[4] . ' hidden better_by_default_hidden_menu';		//phpcs:ignore
				if ( isset( $menu_info[5] ) && isset( $options['customize_admin_menu_dashicons'][ $menu_info[5] ] ) && ! empty( $options['customize_admin_menu_dashicons'][ $menu_info[5] ] ) ) {
					$menu[$menu_key][6] = 'dashicons-'. $options['customize_admin_menu_dashicons'][$menu_info[5]];	// phpcs:ignore
				}
			} elseif ( isset( $menu_info[5] ) && isset( $options['customize_admin_menu_dashicons'][ $menu_info[5] ] ) && ! empty( $options['customize_admin_menu_dashicons'][ $menu_info[5] ] ) ) {
				$menu[$menu_key][4] = $menu_info[4] . ' '. $options['customize_admin_menu_dashicons'][$menu_info[5]];	// phpcs:ignore
				$menu[$menu_key][6] = 'dashicons-'. $options['customize_admin_menu_dashicons'][$menu_info[5]];	// phpcs:ignore
			}
		}
	}

	/**
	 * Add toggle to show hidden menu items
	 *
	 * @since   1.0.0
	 */
	public function add_hidden_menu_toggle() {
		global $current_user;
		$options                                   = $this->options;
		$common_methods                            = new \BetterByDefault\Inc\Common_Methods();
		$menu_hidden_by_toggle                     = $common_methods->get_menu_hidden_by_toggle();
		$submenu_hidden_by_toggle                  = array();
		$user_capabilities_to_show_menu_toggle_for = $common_methods->get_user_capabilities_to_show_menu_toggle_for();
		$current_user_capabilities                 = '';
		$current_user_roles                        = $current_user->roles;
		foreach ( $current_user_roles as $current_user_role ) {
			$current_user_role_capabilities = get_role( $current_user_role )->capabilities;
			$current_user_role_capabilities = array_keys( $current_user_role_capabilities );
			$current_user_role_capabilities = implode( ',', $current_user_role_capabilities );
			$current_user_capabilities     .= $current_user_role_capabilities;
		}
		$current_user_capabilities = array_unique( explode( ',', $current_user_capabilities ) );
		$show_toggle_menu          = false;
		foreach ( $user_capabilities_to_show_menu_toggle_for as $user_capability_to_show_menu_toggle_for ) {
			if ( in_array( $user_capability_to_show_menu_toggle_for, $current_user_capabilities, true ) ) {
				$show_toggle_menu = true;
				break;
			}
		}
		if ( ( ! empty( $menu_hidden_by_toggle ) || ! empty( $submenu_hidden_by_toggle ) ) && $show_toggle_menu ) {
			$custom_menu_titles   = $options['custom_menu_titles'];
			$custom_menu_titles   = explode( ',', $custom_menu_titles );
			$show_all_menu_title  = 'Show All';
			$show_less_menu_title = 'Show Less';
			foreach ( $custom_menu_titles as $custom_menu_title ) {
				$custom_menu_title = trim( $custom_menu_title );
				// Check for 'show_hidden_menu'.
				if ( false !== strpos( $custom_menu_title, 'toplevel_page_better_by_default_show_hidden_menu' ) ) {
					$exploded_menu_title = explode( '__', $custom_menu_title );
					if ( isset( $exploded_menu_title[1] ) ) {
						$show_all_menu_title = trim( $exploded_menu_title[1] ); // Trim the result to remove extra spaces/newlines.
					}
				}

				// Check for 'hide_hidden_menu'.
				if ( false !== strpos( $custom_menu_title, 'toplevel_page_better_by_default_hide_hidden_menu' ) ) {
					$exploded_menu_title = explode( '__', $custom_menu_title );
					if ( isset( $exploded_menu_title[1] ) ) {
						$show_less_menu_title = trim( $exploded_menu_title[1] ); // Trim the result to remove extra spaces/newlines.
					}
				}
			}
			add_menu_page(
				$show_all_menu_title,
				$show_all_menu_title,
				'read',
				'better_by_default_show_hidden_menu',
				function () {
					return false;
				},
				'dashicons-arrow-down-alt2',
				300
			);
			add_menu_page(
				$show_less_menu_title,
				$show_less_menu_title,
				'read',
				'better_by_default_hide_hidden_menu',
				function () {
					return false;
				},
				'dashicons-arrow-up-alt2',
				301
			);
		}
	}

	/**
	 * Admin menu color styles.
	 *
	 * @return void
	 */
	public function admin_menu_color_styles() {
		$options = $this->options;
	
		if (isset($options['customize_admin_menu_colors']) && !empty($options['customize_admin_menu_colors'])) {
			$customize_admin_menu_colors = $options['customize_admin_menu_colors'];
			// Enqueue the stylesheet.
			wp_enqueue_style('admin_menu_login_color', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all');
	
			$custom_admin_css = '';
	
			foreach ($customize_admin_menu_colors as $menu_key => $menu_color) {
				// Generate the custom CSS for each menu item.
				$custom_admin_css .= '#adminmenu li#' . esc_attr($menu_key) . ' a { background-color: ' . esc_attr($menu_color) . '; }';
			}

			// Add inline styles.
			if (!empty($custom_admin_css)) {
				wp_add_inline_style('admin_menu_login_color', $custom_admin_css);
			}
		}
	}
	
}
