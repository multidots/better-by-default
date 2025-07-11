<?php
/**
 * The content-duplication-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Content_Duplication class file.
 */
class Content_Duplication {

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

		$this->setup_content_duplication_hooks();
	}
	/**
	 * Function is used to define content-duplication hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_content_duplication_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );
		add_action( 'admin_action_duplicate_content', array( $this, 'better_by_default_duplicate_content' ) );
		add_filter( 'page_row_actions', array( $this, 'better_by_default_add_duplication_action_link' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'better_by_default_add_duplication_action_link' ), 10, 2 );
		add_action( 'admin_bar_menu', array( $this, 'better_by_default_add_admin_bar_duplication_link' ), 100 );
	}

	/**
	 * Enable duplication of pages, posts and custom posts
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_duplicate_content() {
		$allow_duplication = false;
		if ( current_user_can( 'edit_posts' ) ) {
			$allow_duplication = true;
		}

		$original_post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$nonce            = filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( wp_verify_nonce( $nonce, 'better-by-default-duplicate-' . $original_post_id ) && $allow_duplication ) {
			$original_post         = get_post( $original_post_id );
			$post_type             = $original_post->post_type;
			$common_methods        = new \BetterByDefault\Inc\Common_Methods();
			$is_woocommerce_active = $common_methods->is_woocommerce_active();
			if ( ( 'product' !== $post_type || 'product' === $post_type ) && ! $is_woocommerce_active ) {
				// Set some attributes for the duplicate post.
				$new_post_title_suffix = __( 'DUPLICATE', 'better-by-default' );
				$new_post_status       = 'draft';
				$current_user          = wp_get_current_user();
				$new_post_author_id    = $current_user->ID;
				// Create the duplicate post and store the ID.
				$args        = array(
					'comment_status' => $original_post->comment_status,
					'ping_status'    => $original_post->ping_status,
					'post_author'    => $new_post_author_id,
					'post_content'   => str_replace( '\\', '\\\\', $original_post->post_content ),
					'post_excerpt'   => $original_post->post_excerpt,
					'post_parent'    => $original_post->post_parent,
					'post_password'  => $original_post->post_password,
					'post_status'    => $new_post_status,
					'post_title'     => $original_post->post_title . ' (' . $new_post_title_suffix . ')',
					'post_type'      => $original_post->post_type,
					'to_ping'        => $original_post->to_ping,
					'menu_order'     => $original_post->menu_order,
				);
				$new_post_id = wp_insert_post( $args );
				// Copy over the taxonomies.
				$original_taxonomies = get_object_taxonomies( $original_post->post_type );
				if ( ! empty( $original_taxonomies ) && is_array( $original_taxonomies ) ) {
					foreach ( $original_taxonomies as $taxonomy ) {
						$original_post_terms = wp_get_object_terms(
							$original_post_id,
							$taxonomy,
							array(
								'fields' => 'slugs',
							)
						);
						wp_set_object_terms(
							$new_post_id,
							$original_post_terms,
							$taxonomy,
							false
						);
					}
				}
				// Copy over the post meta.
				$original_post_metas = get_post_meta( $original_post_id );
				// all meta keys and the corresponding values.
				if ( ! empty( $original_post_metas ) ) {
					foreach ( $original_post_metas as $meta_key => $meta_values ) {
						foreach ( $meta_values as $meta_value ) {
							update_post_meta( $new_post_id, $meta_key, wp_slash( maybe_unserialize( $meta_value ) ) );
						}
					}
				}
			}
			$options                          = $this->options;
			$duplication_redirect_destination = ( isset( $options['duplication_redirect_destination'] ) ? $options['duplication_redirect_destination'] : 'list' );
			switch ( $duplication_redirect_destination ) {
				case 'edit':
					// Redirect to edit screen of the duplicate post.
					wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
					exit;
				case 'list':
					// Redirect to list table of the corresponding post type of original post.
					if ( 'post' === $post_type ) {
						wp_safe_redirect( admin_url( 'edit.php' ) );
						exit;
					} else {
						wp_safe_redirect( admin_url( 'edit.php?post_type=' . $post_type ) );
						exit;
					}
			}
		} else {
			wp_die( 'You do not have permission to perform this action.' );
		}
	}

	/**
	 * Add row action link to perform duplication in page/post list tables.
	 *
	 * @param array  $actions An array of row action links.
	 * @param object $post The post object.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_add_duplication_action_link( $actions, $post ) {
		$duplication_link_locations = $this->get_duplication_link_locations();
		$allow_duplication          = $this->is_user_allowed_to_duplicate_content();
		$post_type                  = $post->post_type;
		$post_type_is_duplicable    = $this->is_post_type_duplicable( $post_type );
		$post_title                 = isset( $post->post_title ) ? $post->post_title : '';
		if ( $allow_duplication && $post_type_is_duplicable && ! empty( $post_title ) ) {
			// Not WooCommerce product.
			if ( in_array( 'post-action', $duplication_link_locations, true ) ) {
				$actions['better-by-default-duplicate'] = '<a href="admin.php?action=duplicate_content&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'better-by-default-duplicate-' . $post->ID ) . '" title="' . __( 'Duplicate this as draft', 'better-by-default' ) . '">' . __( 'Duplicate', 'better-by-default' ) . '</a>';
			}
		}
		return $actions;
	}

	/**
	 * Add admin bar duplicate link
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The admin bar object.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_add_admin_bar_duplication_link( \WP_Admin_Bar $wp_admin_bar ) {
		$duplication_link_locations = $this->get_duplication_link_locations();
		$allow_duplication          = $this->is_user_allowed_to_duplicate_content();
		global $pagenow, $typenow, $post;
		$inapplicable_post_types = array( 'attachment' );
		$post_type_is_duplicable = $this->is_post_type_duplicable( $typenow );
		if ( $allow_duplication && $post_type_is_duplicable ) {
			if ( 'post.php' === $pagenow && ( ! in_array( $typenow, $inapplicable_post_types, true ) || is_singular() ) ) {
				if ( in_array( 'admin-bar', $duplication_link_locations, true ) ) {
					if ( is_object( $post ) ) {
						$common_methods           = new \BetterByDefault\Inc\Common_Methods();
						$post_type_singular_label = $common_methods->get_post_type_singular_label( $post );
						if ( property_exists( $post, 'ID' ) ) {
							$wp_admin_bar->add_menu(
								array(
									'id'     => 'duplicate-content',
									'parent' => null,
									'group'  => null,
									'title'  => sprintf(
									/* translators: %s is the singular label for the post type */
										__( 'Duplicate %s', 'better-by-default' ),
										$post_type_singular_label
									),
									'href'   => admin_url( 'admin.php?action=duplicate_content&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'better-by-default-duplicate-' . $post->ID ) ),
								)
							);
						}
					}
				}
			}
		}
	}

	/**
	 * Check at which locations duplication link should enabled
	 *
	 * @since 1.0.0
	 */
	public function get_duplication_link_locations() {
		$duplication_link_locations = array( 'post-action', 'admin-bar' );
		return $duplication_link_locations;
	}

	/**
	 * Check if a user role is allowed to duplicate content
	 *
	 * @since 1.0.0
	 */
	public function is_user_allowed_to_duplicate_content() {
		$allow_duplication = false;
		if ( current_user_can( 'edit_posts' ) ) {
			$allow_duplication = true;
		}
		return $allow_duplication;
	}

	/**
	 * Check if the post type can be duplicated
	 *
	 * @param string $post_type The post type.
	 *
	 * @since 1.0.0
	 */
	public function is_post_type_duplicable( $post_type ) {

		$common_methods                            = new \BetterByDefault\Inc\Common_Methods();
		$better_by_default_public_post_types       = $common_methods->get_post_types();
		$is_woocommerce_active                     = $common_methods->is_woocommerce_active();
		$better_by_default_public_post_types_slugs = array();
		if ( is_array( $better_by_default_public_post_types ) ) {
			foreach ( $better_by_default_public_post_types as $post_type_slug => $post_type_label ) {
				$better_by_default_public_post_types_slugs[] = $post_type_slug;
			}
		}

		if ( ( in_array( $post_type, $better_by_default_public_post_types_slugs, true ) ) ) {
			if ( ( 'product' !== $post_type || 'product' === $post_type ) && ! $is_woocommerce_active ) {
				return true;
			}
		} else {
			return false;
		}
	}
}
