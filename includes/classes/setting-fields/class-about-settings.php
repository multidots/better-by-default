<?php
/**
 * The about-settings-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the about-settings-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Setting_Fields;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * About_Settings class file.
 */
class About_Settings {

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
	public function better_by_default_about_page_init() {

		add_settings_field(
			'about_page', // id.
			'', // title.
			array( $this, 'render_html' ), // callback.
			'better-by-default-setting-page', // page.
			'better_by_default_setting_section', // section.
		);
	}

	/**
	 * Render About page html
	 */
	public function render_html() {
		?>
		<tr class="better-by-default-toggle better-by-default-about-page">
		<td>
		<div class="about-md">
		<figure>
		<img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/GIF-logo-with-text-without-background.gif' ); //phpcs:ignore ?>" alt="md-logo">
		<figcaption>
			<p class="has-text-align-center">Serving People. Solving Problems.</p>
		</figcaption>
		</figure>
		<div class="md-info">
		<p>Multidots is&nbsp;an&nbsp; <a href="https://www.inc.com/profile/multidots" target="_blank" rel="noreferrer noopener">Inc. 5000</a>&nbsp;company&nbsp;and one of the top WordPress development companies in the world. We are also one of the preferred enterprise WordPress implementation partners&nbsp; <strong>(WordPress VIP Gold Partner)</strong>&nbsp;selected by Automattic – the company behind&nbsp;WordPress.com, WooCommerce, and Tumblr. As a globally distributed team, we're able to serve publishers in North America, Asia, and Europe. We also offer WordPress Plugins and Products to small-medium businesses through our other brands —&nbsp; <a href="https://www.multicollab.com/" target="_blank" rel="noreferrer noopener">Multicollab</a>&nbsp;and&nbsp; <a href="https://www.thedotstore.com/" target="_blank" rel="noreferrer noopener">Dotstore</a>. </p>
		<a href="https://www.multidots.com/contact-us/" class="contactus-non-usa contactus-btn button-primary">Contact Us</a>
		</div>
		<div class="md-revamp-explore-brands mdinc-section-bottom-margin">
		<div class="group__inner-container">
			<h2 class="block-heading has-text-align-center" id="h-explore-our-brands">Explore Our Brands</h2>
			<div class="mdinc-our-brands-section-columns">
			<div class="mdinc-our-brands-section-column mdinc-our-brands-section-column-md">
			<div class="wp-block-image">
			<figure class="aligncenter size-full">
				<a href="https://www.multicollab.com/" target="_blank" rel="noreferrer noopener">
				<img decoding="async" src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/Explore-Multicollab-logo.svg' ); //phpcs:ignore ?>" alt="Multicollab-logo" class="wp-image-md-logos" loading="lazy">
				</a>
			</figure>
			</div>
			<p class="has-text-align-center">Google Docs-Style Collaboration Plugin for WordPress</p>
			</div>
			<div class="mdinc-our-brands-section-column mdinc-our-brands-section-column-ds">
			<div class="wp-block-image">
			<figure class="aligncenter size-full">
				<a href="https://www.thedotstore.com/" target="_blank" rel="noreferrer noopener">
				<img decoding="async" src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/Explore-dotstore-logo-logo.svg' ); //phpcs:ignore ?>" alt="dotstore-logo" class="wp-image-md-logos" loading="lazy">
				</a>
			</figure>
			</div>
			<p class="has-text-align-center">Premium Plugins for Your WooCommerce Website</p>
			</div>
			<div class="mdinc-our-brands-section-column mdinc-our-brands-section-column-ps">
			<div class="wp-block-image">
			<figure class="aligncenter size-full">
				<a href="https://www.peacefulgrowth.com/" target="_blank" rel="noreferrer noopener">
				<img decoding="async" src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/Explore-peaceful-logo-logo.svg' ); //phpcs:ignore ?>" alt="peaceful-logo" class="wp-image-md-logos" loading="lazy">
				</a>
			</figure>
			</div>
			<p class="has-text-align-center">A Podcast to Learn Peaceful Growth Strategies</p>
			</div>
			</div>
		</div>
		</div>
		</div>
		</td>
		</tr>
		<?php
	}
}
