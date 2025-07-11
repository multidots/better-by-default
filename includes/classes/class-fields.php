<?php
/**
 * The fields-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Fields class file.
 */
class Fields {

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
	 * Generate Fields.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function add_field( $args ) {
		switch ( $args['field_type'] ) {
			case 'text':
				$this->text_callback( $args );
				break;
			case 'file':
				$this->file_callback( $args );
				break;
			case 'checkbox-toggle':
				$this->checkbox_toggle_callback( $args );
				break;
			case 'render_checkbox_subfield':
				$this->render_checkbox_plain( $args );
				break;
			case 'color-picker':
				$this->color_picker_callback( $args );
				break;
			case 'number':
				$this->number_callback( $args );
				break;
			case 'text_subfield':
				$this->render_text_subfield( $args );
				break;
			case 'description_subfield':
				$this->render_description_subfield( $args );
				break;
			case 'sortable_menu':
				$this->render_sortable_menu( $args );
				break;
			case 'password_subfield':
				$this->render_password_subfield( $args );
				break;
			case 'textarea_subfield':
				$this->render_textarea_subfield( $args );
				break;
			case 'render_datatable':
				$this->render_datatable_callback( $args );
				break;
			case 'radio_buttons_subfield':
				$this->render_radio_buttons_subfield( $args );
				break;
			case 'wpeditor_subfield':
				$this->render_wpeditor_subfield( $args );
				break;
			case 'button_subfield':
				$this->render_button_subfield( $args );
				break;
		}
	}

	/**
	 * Generate textbox.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function text_callback( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}
		$field_name         = $args['field_name'];
		$field_description  = $args['field_description'];
		$field_option_value = ( ! empty( $options ) && array_key_exists( $args['field_id'], $options ) ? $options[ $args['field_id'] ] : '' );
		?>
		<input type="<?php echo esc_attr( $args['field_type'] ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo isset( $field_option_value ) ? esc_attr( $field_option_value ) : ''; ?>" />
		<label for="<?php echo esc_attr( $field_name ); ?>"><?php esc_html( $field_description ); ?></label>
		<?php
	}

	/**
	 * Generate image.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function file_callback( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name         = $args['field_name'];
		$field_option_value = ( ! empty( $options ) && array_key_exists( $args['field_id'], $options ) ? $options[ $args['field_id'] ] : '' );
		$image_src          = ! empty( $field_option_value ) ? $field_option_value : '';

		?>
		<div class="image-upload-wrap">
			<img class="better_by_default_img" name="<?php echo esc_attr( $field_name ); ?>" src="<?php echo esc_url( $image_src ); ?>"
				<?php if ( ! empty( $field_option_value ) ) : ?>
					width="250px" height="150px"
				<?php endif; ?>
			/>
			<input class="better_by_default_img_url" type="hidden" name="<?php echo esc_attr( $field_name ); ?>" size="60" value="<?php echo esc_url( $image_src ); ?>" >
			<a href="#" class="better_by_default_img_upload"><button><?php esc_html_e( 'Upload Logo', 'better-by-default' ); ?></button></a>
			<a href="#" class="better_by_default_img_remove" style="display:<?php echo ! empty( $image_src ) ? 'initial' : 'none'; ?>"><button><?php esc_html_e( 'Remove', 'better-by-default' ); ?></button></a>
		</div>
		<?php
	}

	/**
	 * Render checkbox field as a toggle/switcher
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function checkbox_toggle_callback( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name        = $args['field_name'];
		$field_description = $args['field_description'];
		$default_value     = 'false';
		if ( empty( $options ) ) {
			$default_value = isset( $args['default_value'] ) ? $args['default_value'] : 'false';
		}

		$field_option_value = ( ! empty( $options ) && array_key_exists( $args['field_id'], $options ) ? $options[ $args['field_id'] ] : $default_value );
		$field_option_value = ( true === $field_option_value || 'true' === $field_option_value ) ? true : false;

		?>
		<input type="checkbox" value="true" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-field-checkbox better-by-default-field-checkbox_main" name="<?php echo esc_attr( $field_name ); ?>" <?php checked( $field_option_value, true ); ?> >
		<label for="<?php echo esc_attr( $field_name ); ?>"></label>
	
		<?php if ( array_key_exists( 'field_options_wrapper', $args ) && $args['field_options_wrapper'] ) : ?>
			<?php if ( array_key_exists( 'field_options_moreless', $args ) && $args['field_options_moreless'] ) : ?>
				<div class="better-by-default-field-with-options field-show-more">
					<a id="<?php echo esc_attr( $args['field_slug'] ); ?>-show-moreless" class="show-more-less show-more" href="#"><?php esc_html_e( 'More', 'better-by-default' ); ?> + </a> <!-- &#9660; -->
					<div class="better-by-default-field-options-wrapper wrapper-show-more">
			<?php else : ?>
				<div class="better-by-default-field-with-options">
					<div class="better-by-defaultdefaultdefault-field-options-wrapper">
			<?php endif; ?>
		<?php endif; ?>
	
		<div class="better-by-default-field-description"><?php echo wp_kses_post( $field_description ); ?></div>
	
		<?php if ( array_key_exists( 'field_options_wrapper', $args ) && $args['field_options_wrapper'] ) : ?>
			<div class="better-by-default-subfields" style="display:none"></div>
			</div> <!-- Close better-by-default-field-options-wrapper -->
			</div> <!-- Close better-by-default-field-with-options -->
			<?php
		endif;
	}

	/**
	 * Render checkbox field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.9.0
	 */
	public function render_checkbox_plain( $args ) {

		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name    = $args['field_name'];
		$field_label   = isset( $args['field_label'] ) ? $args['field_label'] : '';
		$default_value = 'false';
		if ( empty( $options ) ) {
			$default_value = isset( $args['default_value'] ) ? $args['default_value'] : 'false';
		}

		$parent_field_id = isset( $args['parent_field_id'] ) ? $args['parent_field_id'] : '';

		if ( ! empty( $parent_field_id ) ) {
			$field_option_value = ( isset( $options[ $args['parent_field_id'] ][ $args['field_id'] ] ) ? $options[ $args['parent_field_id'] ][ $args['field_id'] ] : $default_value );
		} else {
			$field_option_value = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $default_value );
		}

		$field_option_value = ( true === $field_option_value || 'true' === $field_option_value ) ? true : false;

		switch ( $args['field_id'] ) {
			case 'login_page_disable_registration':
				$default_value = ( 1 === get_option( 'users_can_register' ) ? false : true );
				break;
		}
		?>
		<input type="checkbox" value="true" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-checkbox" name="<?php echo esc_attr( $field_name ); ?>" <?php checked( $field_option_value, true ); ?>>
		<label for="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-checkbox-label"><?php echo wp_kses_post( $field_label ); ?></label> 
								<?php
	}

	/**
	 * Generate Color Picker.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function color_picker_callback( $args ) {

		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name         = $args['field_name'];
		$field_option_value = ( ! empty( $options ) && array_key_exists( $args['field_id'], $options ) ? $options[ $args['field_id'] ] : false );

		?>
		<input type="text" class="color-picker" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo isset( $field_option_value ) ? esc_attr( $field_option_value ) : ''; ?>" />
		<label for="<?php echo esc_attr( $field_name ); ?>"></label>
		<?php
	}

	/**
	 * Generate Number Textbox.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function number_callback( $args ) {

		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_description  = $args['field_description'];
		$field_option_value = ( ! empty( $options ) && array_key_exists( $args['field_id'], $options ) && ! empty( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '' );
		?>
		<div class="better-by-default-subfield-number-wrapper">
			<input type="<?php echo esc_attr( $args['field_type'] ); ?>" min="1" max="100" id="<?php echo esc_attr( $args['field_name'] ); ?>" name="<?php echo esc_attr( $args['field_name'] ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
			<div class="better-by-default-subfield-number-description"><?php echo wp_kses_post( $field_description ); ?></div>
		</div>
		<?php
	}

	/**
	 * Render text field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_text_subfield( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name            = $args['field_name'];
		$field_width_classname = ( isset( $args['field_width_classname'] ) ? $args['field_width_classname'] : '' );
		$field_prefix          = $args['field_prefix'];
		$field_suffix          = $args['field_suffix'];
		$field_placeholder     = ( isset( $args['field_placeholder'] ) ? $args['field_placeholder'] : '' );
		$default_value         = ( isset( $args['default_value'] ) ? $args['default_value'] : '' );
		$field_description     = $args['field_description'];
		$field_option_value    = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $default_value );

		if ( ! empty( $field_prefix ) && ! empty( $field_suffix ) ) {
			$field_classname = ' with-prefix with-suffix';
		} elseif ( ! empty( $field_prefix ) && empty( $field_suffix ) ) {
			$field_classname = ' with-prefix';
		} elseif ( empty( $field_prefix ) && ! empty( $field_suffix ) ) {
			$field_classname = ' with-suffix';
		} else {
			$field_classname = '';
		}
		if ( ! empty( $field_width_classname ) ) {
			$field_classname .= ' ' . $field_width_classname;
		}
		?>
		<span><?php echo wp_kses_post( $field_prefix ); ?></span>
		<input type="text" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text<?php echo esc_attr( $field_classname ); ?>" name="<?php echo esc_attr( $field_name ); ?>" placeholder="<?php echo esc_attr( $field_placeholder ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
		<span><?php echo wp_kses_post( $field_suffix ); ?></span>
		<label for="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-checkbox-label"><?php echo esc_html( $field_description ); ?></label> 
								<?php
	}

	/**
	 * Render password field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_password_subfield( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name            = $args['field_name'];
		$field_width_classname = ( isset( $args['field_width_classname'] ) ? $args['field_width_classname'] : '' );
		$field_prefix          = $args['field_prefix'];
		$field_suffix          = $args['field_suffix'];
		$field_placeholder     = ( isset( $args['field_placeholder'] ) ? $args['field_placeholder'] : '' );
		$default_value         = ( isset( $args['default_value'] ) ? $args['default_value'] : '' );

		$field_description  = $args['field_description'];
		$field_option_value = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $default_value );
		if ( ! empty( $field_prefix ) && ! empty( $field_suffix ) ) {
			$field_classname = ' with-prefix with-suffix';
		} elseif ( ! empty( $field_prefix ) && empty( $field_suffix ) ) {
			$field_classname = ' with-prefix';
		} elseif ( empty( $field_prefix ) && ! empty( $field_suffix ) ) {
			$field_classname = ' with-suffix';
		} else {
			$field_classname = '';
		}
		if ( ! empty( $field_width_classname ) ) {
			$field_classname .= ' ' . $field_width_classname;
		}
		?>
		<div class="password-field">
			<?php echo wp_kses_post( $field_prefix ); ?>
			<div class="password-container">
				<input type="password" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text password-input <?php echo esc_attr( $field_classname ); ?>" name="<?php echo esc_attr( $field_name ); ?>" placeholder="<?php echo esc_attr( $field_placeholder ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
				<button type="button" id="toggle-password"><span id="eye-icon" class="dashicons dashicons-visibility"></span></button><!-- Eye icon -->
			</div>
			<?php echo wp_kses_post( $field_suffix ); ?>
			<label for="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-checkbox-label"><?php echo esc_html( $field_description ); ?></label>
		</div>
		<?php
	}

	/**
	 * Render description field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_description_subfield( $args ) {
		$field_description = isset( $args['field_description'] ) ? $args['field_description'] : '';
		$field_id          = isset( $args['field_id'] ) ? $args['field_id'] : '';

		if ( 'activity_log_description' === $field_id ) {
			$options      = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );
			$activity_log = isset( $options['activity_log'] ) && ! empty( $options['activity_log'] ) ? (bool) $options['activity_log'] : false;
			if ( true === $activity_log ) {
				?>
				<div class="better-by-default-subfield-description"><?php echo wp_kses_post( $field_description ); ?></div>
				<?php
			}
		} elseif ( 'activity_log_description' !== $field_id ) {
			?>
			<div class="better-by-default-subfield-description"><?php echo wp_kses_post( $field_description ); ?></div>
			<?php
		}
	}

	/**
	 * Render textarea field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_textarea_subfield( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name         = $args['field_name'];
		$field_placeholder  = ( isset( $args['field_placeholder'] ) ? $args['field_placeholder'] : '' );
		$field_description  = $args['field_description'];
		$field_option_value = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $field_description );
		$field_button       = ( isset( $args['field_button'] ) ? $args['field_button'] : false );
		$button_title       = ( isset( $args['button_title'] ) ? $args['button_title'] : 'Submit' );
		$field_rows         = isset( $args['field_rows'] ) ? $args['field_rows'] : 5;
		?>
		<textarea cols="50" rows="<?php echo esc_attr( $field_rows ); ?>" class="better-by-default-subfield-textarea" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" placeholder="<?php echo esc_attr( $field_placeholder ); ?>"><?php echo esc_textarea( $field_option_value ); ?></textarea>
		<?php if ( $field_button ) : ?>
			<input type="button" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text button" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $button_title ); ?>">
			<?php
		endif;
	}

	/**
	 * Render sortable menu field
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_sortable_menu( $args ) {
		?>
		<div class="subfield-description">
			<?php
				echo esc_html__( 'Drag and drop menu items to the desired position. Optionally change 3rd party plugin/theme\'s menu item titles or hide some items until toggled by clicking "Show All" at the bottom of the admin menu.', 'better-by-default' );
			?>
		</div>
		<ul id="custom-admin-menu" class="menu ui-sortable">
			<?php
			global $menu;
			$common_methods = new \BetterByDefault\Inc\Common_Methods();
			$option_name    = $args['option_name'];

			$options = array();
			if ( ! empty( $option_name ) ) {
				$options = get_option( $option_name, array() );
			}

			// Set menu items to be excluded from title renaming. These are from WordPress core.
			$renaming_not_allowed = array( 'menu-dashboard', 'menu-pages', 'menu-media', 'menu-comments', 'menu-appearance', 'menu-plugins', 'menu-users', 'menu-tools', 'menu-settings' );
			// Get custom menu item titles.
			if ( ! empty( $options ) && array_key_exists( 'custom_menu_titles', $options ) ) {
				$custom_menu_titles = $options['custom_menu_titles'];
				$custom_menu_titles = explode( ',', $custom_menu_titles );
			} else {
				$custom_menu_titles = array();
			}
			// Get menu items hidden by toggle.
			$menu_hidden_by_toggle = $common_methods->get_menu_hidden_by_toggle();
			$dashicons             = array( 'admin-site', 'admin-media', 'admin-page', 'admin-comments', 'admin-appearance', 'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home', 'admin-generic', 'admin-collapse', 'admin-post', 'admin-links', 'admin-multisite', 'analytics', 'backup', 'block', 'block-default', 'calendar', 'camera', 'category', 'clock', 'cloud', 'cloud-upload', 'dashboard', 'database', 'desktop', 'feedback', 'forms', 'format-gallery', 'format-image', 'format-quote', 'format-status', 'format-video', 'heart', 'info', 'laptop', 'location', 'lock', 'microphone', 'search', 'share', 'star-empty', 'star-filled' );

			// Check if there's an existing custom menu order data stored in options.
			if ( ! empty( $options ) && array_key_exists( 'custom_menu_order', $options ) ) {
				$custom_menu     = $options['custom_menu_order'];
				$custom_menu     = explode( ',', $custom_menu );
				$menu_key_in_use = array();
				// Render sortables with data in custom menu order.
				foreach ( $custom_menu as $custom_menu_item ) {
					foreach ( $menu as $menu_key => $menu_info ) {
						if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
							$menu_item_title = $menu_info[2];
							$menu_item_id    = $menu_info[2];
						} else {
							$menu_item_title = $menu_info[0];
							$menu_item_id    = $menu_info[5];
						}
						$menu_url_fragment     = '';
						$selected_dashicon     = isset( $options['customize_admin_menu_dashicons'][ $menu_item_id ] ) ? $options['customize_admin_menu_dashicons'][ $menu_item_id ] : '';
						$selected_admin_colors = isset( $options['customize_admin_menu_colors'][ $menu_item_id ] ) ? $options['customize_admin_menu_colors'][ $menu_item_id ] : '';
						if ( $custom_menu_item === $menu_item_id ) {

							// echo "<pre>";
							// print_r( $menu_item_title );
							// if('Show All' === $menu_item_title || 'Show Less' === $menu_item_title ) {
							// 	continue;
							// }
	

							$menu_item_id_transformed = $common_methods->transform_menu_item_id( $menu_item_id );
							$is_custom_menu           = 'no';
							$hide_menu_class          = ( false !== strpos( strtolower( $menu_item_id ), 'separator' ) ) ? ' hide-menu-li' : '';
							?>
							<li id="<?php echo esc_attr( $menu_item_id ); ?>" class="menu-item parent-menu-item menu-item-depth-0<?php echo esc_attr( $hide_menu_class ); ?>" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">
								<div class="menu-item-bar">
									<div class="menu-item-handle">
										<span class="dashicons dashicons-menu"></span>
										<div class="item-title">
											<div class="title-wrapper">
												<span class="menu-item-title">
												<?php
												if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
													$separator_name_ori = $menu_info[2];
													$separator_name     = str_replace( 'separator', 'Separator-', $separator_name_ori );
													$separator_name     = str_replace( '--last', '-Last', $separator_name );
													$separator_name     = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
													echo '~~ ' . esc_html( $separator_name ) . ' ~~';
												} elseif ( in_array( $menu_item_id, $renaming_not_allowed, true ) ) {
														$menu_item_title = $menu_info[0];
														echo wp_kses_post( $common_methods->strip_html_tags_and_content( $menu_item_title ) );
												} else {
													// Get defaul/custom menu item title.
													foreach ( $custom_menu_titles as $custom_menu_title ) {
														// At this point, $custom_menu_title value looks like toplevel_page_snippets__Code Snippets.
														$custom_menu_title = explode( '__', $custom_menu_title );
														if ( $custom_menu_title[0] === $menu_item_id ) {
															$menu_item_title = $common_methods->strip_html_tags_and_content( $custom_menu_title[1] );
															// e.g. Code Snippets.
															break;
															// stop foreach loop so $menu_item_title is not overwritten in the next iteration.
														} else {
															$menu_item_title = $common_methods->strip_html_tags_and_content( $menu_info[0] );
														}
													}
													?>
														<input type="text" value="<?php echo wp_kses_post( $menu_item_title ); ?>" class="menu-item-custom-title" data-menu-item-id="<?php echo esc_attr( $menu_item_id ); ?>">
														<?php
												}
												?>
												</span><!-- end of .menu-item-title -->
											</div><!-- end of .title-wrapper -->
											<div class="options-for-hiding">
												<?php
												$hide_text      = __( 'Hide until toggled', 'better-by-default' );
												$checkbox_class = 'parent-menu-hide-checkbox';
												?>
												<label class="menu-item-checkbox-label">
													<?php
													if ( in_array( $custom_menu_item, $menu_hidden_by_toggle, true ) ) {
														?>
														<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $common_methods->strip_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>" checked>
														<span><?php echo esc_html( $hide_text ); ?></span>
														<?php
													} else {
														if('Show All' !== $menu_item_title && 'Show Less' !== $menu_item_title ) {
															?>
																<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $common_methods->strip_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>">
																<span><?php echo esc_html( $hide_text ); ?></span>
															<?php
														}
													}
													?>
												</label>
											</div><!-- end of .options-for-hiding -->
											<div class="item-inner-block">
												<div class="dashboard-item-toggle">
													<span class="select-menu-icon toggle-style" data-name="select-icon">Select Menu Icon</span>
													<span class="select-menu-color toggle-style" data-name="select-color">Select Menu Item Color</span>
												</div>
												<div class="menu-styling-wrapper">
													<div class="menu-styling menu-item-color-wrapper" style="display: none;">
														<label>Choose Menu Item Color: </label>
														<input type="text" class="color-picker" id="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_admin_colors ); ?>" />
													</div>
													<div class="menu-styling dashicons-list-wrapper" style="display: none;">
														<div id="dashicon-picker">
															<?php
															foreach ( $dashicons as $dashicon ) :
																	$active_class = $selected_dashicon === $dashicon ? ' active' : '';
																?>
																<span class="dashicons dashicons-<?php echo esc_attr( $dashicon ); ?><?php echo esc_attr( $active_class ); ?>" data-value="<?php echo esc_attr( $dashicon ); ?>" ></span>
															<?php endforeach; ?>
														</div>
														<input type="hidden" id="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_dashicon ); ?>" />
													</div>
												</div>
											</div> <!-- end of Menu styling -->
										</div><!-- end of .item-title -->
									</div><!-- end of .menu-item-handle -->
								</div><!-- end of .menu-item-bar -->
								<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
							</li>
							<?php
							$menu_key_in_use[] = $menu_key;
						}
					}
				}
				// Render the rest of the current menu towards the end of the sortables.
				foreach ( $menu as $menu_key => $menu_info ) {
					if ( ! in_array( $menu_key, $menu_key_in_use, true ) ) {
						if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
							$menu_item_id = $menu_info[2];
						} else {
							$menu_item_id = $menu_info[5];
						}
						$menu_item_title   = $menu_info[0];
						$menu_url_fragment = '';
						// Strip tags.
						$menu_item_title = $common_methods->strip_html_tags_and_content( $menu_item_title );
						// Exclude Show All/Less toggles.
						if ( false === strpos( $menu_item_id, 'toplevel_page_better-by-default_' ) ) {
							$menu_item_id_transformed = $common_methods->transform_menu_item_id( $menu_item_id );
							$is_custom_menu           = 'no';
							$hide_menu_class          = ( false !== strpos( strtolower( $menu_item_id ), 'separator' ) || false !== strpos( strtolower( $menu_item_id ), 'toplevel_page_better_by_default_show_hidden_menu' ) || false !== strpos( strtolower( $menu_item_id ), 'toplevel_page_better_by_default_hide_hidden_menu' ) || false !== strpos( strtolower( $menu_item_id ), 'menu-comments' ) ) ? ' hide-menu-li' : '';
							?>
							<li id="<?php echo esc_attr( $menu_item_id ); ?> " class="menu-item parent-menu-item menu-item-depth-0<?php echo esc_attr( $hide_menu_class ); ?>" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">
								<div class="menu-item-bar">
									<div class="menu-item-handle">
										<span class="dashicons dashicons-menu"></span>
										<div class="item-title">
											<div class="title-wrapper">
												<span class="menu-item-title">
													<?php
													if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
														$separator_name_ori = $menu_info[2];
														$separator_name     = str_replace( 'separator', 'Separator-', $separator_name_ori );
														$separator_name     = str_replace( '--last', '-Last', $separator_name );
														$separator_name     = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
														echo '~~ ' . esc_html( $separator_name ) . ' ~~';
													} else {
														?>
																				<input type="text" value="
																				<?php
																				echo wp_kses_post( $menu_item_title );
																				?>
														" class="menu-item-custom-title" data-menu-item-id="
														<?php
														echo esc_attr( $menu_item_id );
														?>
														">
																			<?php
													}
													?>
												</span>
											</div>
											<div class="options-for-hiding">
												<?php
												$hide_text      = __( 'Hide until toggled', 'better-by-default' );
												$checkbox_class = 'parent-menu-hide-checkbox';
												?>
												<label class="menu-item-checkbox-label">
													<input type="checkbox" id="hide-status-for-
													<?php
														echo esc_attr( $menu_item_id_transformed );
													?>
														" class="
														<?php
														echo esc_attr( $checkbox_class );
														?>
														" data-menu-item-title="
														<?php
														echo esc_attr( $common_methods->strip_html_tags_and_content( $menu_item_title ) );
														?>
														" data-menu-item-id="
														<?php
														echo esc_attr( $menu_item_id_transformed );
														?>
														" data-menu-item-id-ori="
														<?php
														echo esc_attr( $menu_item_id );
														?>
														" data-menu-url-fragment="
														<?php
														echo esc_attr( $menu_url_fragment );
														?>
														">
													<span><?php echo esc_html( $hide_text ); ?></span>
												</label>
											</div><!-- end of .options-for-hiding -->
											<div class="item-inner-block">
												<div class="dashboard-item-toggle">
													<span class="select-menu-icon toggle-style" data-name="select-icon">Select Menu Icon</span>
													<span class="select-menu-color toggle-style" data-name="select-color">Select Menu Item Color</span>
												</div>
												<div class="menu-styling-wrapper">
													<div class="menu-styling menu-item-color-wrapper" style="display: none;">
														<label>Choose Menu Item Color: </label>
														<input type="text" class="color-picker" id="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_admin_colors ); ?>" />
													</div>
													<div class="menu-styling dashicons-list-wrapper" style="display: none;">
														<div id="dashicon-picker">
															<?php
															foreach ( $dashicons as $dashicon ) :
																	$active_class = $selected_dashicon === $dashicon ? ' active' : '';
																?>
																<span class="dashicons dashicons-<?php echo esc_attr( $dashicon ); ?><?php echo esc_attr( $active_class ); ?>" data-value="<?php echo esc_attr( $dashicon ); ?>" ></span>
															<?php endforeach; ?>
														</div>
														<input type="hidden" id="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_dashicon ); ?>" />
													</div>
												</div>
											</div> <!-- end of Menu styling -->
										</div><!-- end of .item-title -->
									</div><!-- end of .menu-item-handle -->
								</div><!-- end of .menu-item-bar -->
								<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
							</li><!-- end of .menu-item -->
							<?php
						}
					}
				}
			} else {
				// No custom menu order has been saved yet.
				// Render sortables with existing items in the admin menu.
				foreach ( $menu as $menu_key => $menu_info ) {
					if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
						$menu_item_id = $menu_info[2];
					} else {
						$menu_item_id = $menu_info[5];
					}
					$menu_url_fragment        = '';
					$menu_item_title          = $menu_info[0];
					$menu_item_id_transformed = $common_methods->transform_menu_item_id( $menu_item_id );
					// Strip tags.
					$menu_item_title       = $common_methods->strip_html_tags_and_content( $menu_item_title );
					$is_custom_menu        = 'no';
					$selected_dashicon     = isset( $options['customize_admin_menu_dashicons'][ $menu_item_id ] ) ? $options['customize_admin_menu_dashicons'][ $menu_item_id ] : '';
					$selected_admin_colors = isset( $options['customize_admin_menu_colors'][ $menu_item_id ] ) ? $options['customize_admin_menu_colors'][ $menu_item_id ] : '';
					$hide_menu_class       = ( false !== strpos( strtolower( $menu_item_id ), 'separator' ) ) ? ' hide-menu-li' : '';
					?>
					<li id="<?php echo esc_attr( $menu_item_id ); ?>" class="menu-item parent-menu-item menu-item-depth-0<?php echo esc_attr( $hide_menu_class ); ?>" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">  
						<div class="menu-item-bar">
							<div class="menu-item-handle">
								<span class="dashicons dashicons-menu"></span>
								<div class="item-title">
									<div class="title-wrapper">
										<span class="menu-item-title">
										<?php
										if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
											$separator_name_ori = $menu_info[2];
											$separator_name     = str_replace( 'separator', 'Separator-', $separator_name_ori );
											$separator_name     = str_replace( '--last', '-Last', $separator_name );
											$separator_name     = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
											echo '~~ ' . esc_html( $separator_name ) . ' ~~';
										} elseif ( in_array( $menu_item_id, $renaming_not_allowed, true ) ) {
												echo wp_kses_post( $menu_item_title );
										} else {
											?>
												<input type="text" value="<?php echo wp_kses_post( $menu_item_title ); ?>" class="menu-item-custom-title" data-menu-item-id="<?php echo esc_attr( $menu_item_id ); ?>">
												<?php
										}
										?>
										</span>
									</div><!-- end of .title-wrapper -->
									<div class="options-for-hiding">
										<?php
										$hide_text      = __( 'Hide until toggled', 'better-by-default' );
										$checkbox_class = 'parent-menu-hide-checkbox';
										?>
										<label class="menu-item-checkbox-label">
											<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $common_methods->strip_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>">
											<span><?php echo esc_html( $hide_text ); ?></span>
										</label>
									</div><!-- end of .options-for-hiding -->
									<div class="item-inner-block">
												<div class="dashboard-item-toggle">
													<span class="select-menu-icon toggle-style" data-name="select-icon">Select Menu Icon</span>
													<span class="select-menu-color toggle-style" data-name="select-color">Select Menu Item Color</span>
												</div>
												<div class="menu-styling-wrapper">
													<div class="menu-styling menu-item-color-wrapper" style="display: none;">
														<label>Choose Menu Item Color: </label>
														<input type="text" class="color-picker" id="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_colors][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_admin_colors ); ?>" />
													</div>
													<div class="menu-styling dashicons-list-wrapper" style="display: none;">
														<div id="dashicon-picker">
															<?php
															foreach ( $dashicons as $dashicon ) :
																	$active_class = $selected_dashicon === $dashicon ? ' active' : '';
																?>
																<span class="dashicons dashicons-<?php echo esc_attr( $dashicon ); ?><?php echo esc_attr( $active_class ); ?>" data-value="<?php echo esc_attr( $dashicon ); ?>" ></span>
															<?php endforeach; ?>
														</div>
														<input type="hidden" id="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" name="better_by_default_personalize_option[customize_admin_menu_dashicons][<?php echo esc_attr( $menu_item_id ); ?>]" value="<?php echo esc_attr( $selected_dashicon ); ?>" />
													</div>
												</div>
											</div> <!-- end of Menu styling -->
								</div><!-- end of .item-title -->
							</div><!-- end of .menu-item-handle -->
						</div><!-- end of .menu-item-bar -->
						<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
					</li>
					<?php
				}
			}
			?>
		</ul>
		<?php
		// Hidden input field to store custom menu order (from options as is, or sortupdate) upon clicking Save Changes.
		$field_name         = $args['field_name'];
		$field_option_value = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '' );
		?>
		<input type="hidden" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
		<?php
		$this->output_admin_menu_organizer_hidden_field( 'custom_menu_titles' );
		$this->output_admin_menu_organizer_hidden_field( 'custom_menu_hidden' );
		$this->output_admin_menu_organizer_hidden_field( 'custom_menu_icons_hidden' );
	}

	/**
	 * Output hidden field
	 *
	 * @param  string $field_id Field ID.
	 * @since 1.0.0
	 */
	public function output_admin_menu_organizer_hidden_field( $field_id ) {
		$options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		$field_name         = BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS . '[' . $field_id . ']';
		$field_option_value = ( isset( $options[ $field_id ] ) ? $options[ $field_id ] : '' );
		?>
		<input type="hidden" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_option_value ); ?>">
		<?php
	}

	/**
	 * Render datatable field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 2.3.0
	 */
	public function render_datatable_callback( $args ) {
		global $wpdb;

		$field_id   = $args['field_id'];
		$table_name = $args['table_name'];
		$table_name = $wpdb->prefix . $table_name;
		$limit      = 1000;
		$sql = $wpdb->prepare( "SELECT * FROM {$table_name} ORDER BY unixtime DESC LIMIT %d", array($limit) ); //phpcs:ignore
		$entries = $wpdb->get_results( $sql, ARRAY_A );	//phpcs:ignore
		?>
		<?php
		if ( 'login_attempts_log_table' === $field_id ) {
			?>
			<table id="login-attempts-log" class="wp-list-table widefat striped datatable">
				<thead>
					<tr class="datatable-tr">
						<th class="datatable-th"><?php esc_html_e( 'IP Address', 'better-by-default' ); ?></th>
						<th class="datatable-th"><?php esc_html_e( 'Last Username', 'better-by-default' ); ?></th>
						<th class="datatable-th"><?php esc_html_e( 'Attempts', 'better-by-default' ); ?></th>
						<th class="datatable-th"><?php esc_html_e( 'Lockouts', 'better-by-default' ); ?></th>
						<th class="datatable-th"><?php esc_html_e( 'Last Attempt On', 'better-by-default' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( ! empty( $entries ) ) {
						foreach ( $entries as $entry ) {
							$unixtime = $entry['unixtime'];
							if ( function_exists( 'wp_date' ) ) {
								$date = wp_date( 'F j, Y', $unixtime );
								$time = wp_date( 'H:i:s', $unixtime );
							} else {
								$date = date_i18n( 'F j, Y', $unixtime );
								$time = date_i18n( 'H:i:s', $unixtime );
							}
							?>
							<tr class="datatable-tr">
								<td class="datatable-td"><?php echo esc_html( $entry['ip_address'] ); ?></td>
								<td class="datatable-td"><?php echo esc_html( $entry['username'] ); ?></td>
								<td class="datatable-td"><?php echo esc_html( $entry['fail_count'] ); ?></td>
								<td class="datatable-td"><?php echo esc_html( $entry['lockout_count'] ); ?></td>
								<td class="datatable-td"><?php echo esc_html( $date ); ?><?php echo $time ? esc_html( ' at ' . $time ) : ''; ?></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}

	/**
	 * Render radio buttons field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.3.0
	 */
	public function render_radio_buttons_subfield( $args ) {
		$option_name = $args['option_name'];

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name = $args['field_name'];

		$field_description  = $args['field_description'];
		$field_option_value = ( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $field_description );
		$field_id           = $args['field_id'];

		$field_radios = $args['field_radios'];
		if ( ! empty( $args['default_value'] ) ) {
			$default_value = $args['default_value'];
		} else {
			$default_value = false;
		}
		$field_description  = ( isset( $args['field_description'] ) ? $args['field_description'] : '' );
		$field_option_value = ( isset( $options[ $field_id ] ) ? $options[ $field_id ] : $default_value );
		?>
		<div class="better-by-default-subfield-radio-button-wrapper">
			<?php if ( ! empty( $field_radios ) ) : ?>
				<?php foreach ( $field_radios as $radio_label => $radio_value ) : ?>
					<div class="radio-btn_full">
						<input type="radio" id="<?php echo esc_attr( $field_id . '_' . $radio_value ); ?>" class="better-by-default-subfield-radio-button" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $radio_value ); ?>" <?php echo checked( $radio_value, $field_option_value, false ); ?>>
						<label for="<?php echo esc_attr( $field_id . '_' . $radio_value ); ?>" class="better-by-default-subfield-radio-button-label"><?php echo wp_kses_post( $radio_label ); ?></label>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $field_description ) ) : ?>
			<div class="better-by-default-subfield-description"><?php echo wp_kses_post( $field_description ); ?></div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Generate wysiwyg.
	 *
	 * @param  array $args Field argument.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_wpeditor_subfield( $args ) {
		$option_name = isset( $args['option_name'] ) ? sanitize_text_field( $args['option_name'] ) : '';

		$options = array();
		if ( ! empty( $option_name ) ) {
			$options = get_option( $option_name, array() );
		}

		$field_name         = isset( $args['field_name'] ) ? sanitize_text_field( $args['field_name'] ) : '';
		$field_intro        = isset( $args['field_intro'] ) ? wp_kses_post( $args['field_intro'] ) : '';
		$field_description  = isset( $args['field_description'] ) ? wp_kses_post( $args['field_description'] ) : '';
		$field_option_value = isset( $options[ $args['field_id'] ] ) ? wp_kses_post( $options[ $args['field_id'] ] ) : $field_description;
		$editor_settings    = isset( $args['editor_settings'] ) ? (array) $args['editor_settings'] : array();

		$editor_id = str_replace( array( '[', ']' ), array( '--', '' ), $field_name );
		?>
		<div class="better-by-default-subfield-wpeditor-wrapper">
			<?php if ( ! empty( $field_intro ) ) : ?>
				<div class="better-by-default-subfield-wpeditor-intro"><?php echo esc_html( $field_intro ); ?></div>
			<?php endif; ?>
			<?php
			// Render the WordPress editor field.
			wp_editor( $field_option_value, $editor_id, $editor_settings ); //phpcs:ignore
			?>

		</div>
		<?php
	}

	/**
	 * Render button field as sub-field of a toggle/switcher checkbox
	 *
	 * @param  array $args Field argument.
	 * @since 1.0.0
	 */
	public function render_button_subfield( $args ) {
		$field_name        = ( isset( $args['field_name'] ) ? $args['field_name'] : '' );
		$button_title      = ( isset( $args['button_title'] ) ? $args['button_title'] : 'Submit' );
		$field_description = ( isset( $args['field_description'] ) ? $args['field_description'] : '' );

		if ( ! empty( $field_description ) ) {
			?>
			<label for="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-checkbox-label"><?php echo esc_html( $field_description ); ?></label> 
									<?php
		}
		?>
		<input type="button" id="<?php echo esc_attr( $field_name ); ?>" class="better-by-default-subfield-text button" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $button_title ); ?>">
		<?php
	}
}

