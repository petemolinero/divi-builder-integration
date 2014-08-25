<?php
/**
 *
 * @package   Divi Page Builder Integration
 * @author    Pete Molinero <pete@laternastudio.com>
 * @license   GPL-2.0+
 * @link      http://www.laternastudio.com
 * @copyright 2014 Laterna Studio
 *
 * @wordpress-plugin
 * Plugin Name:       Divi Page Builder Integration
 * Plugin URI:        n/a
 * Description:       Allow the Divi Page Builder to be used on more than just the 'page' and 'project' content types.
 * Version:           1.0.1
 * Author:            Pete Molinero
 * Author URI:        http://www.laternastudio.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * ----------------------------------------------------------------------------//
 * This plugin exists to extend the functionality of the of the awesome
 * "Divi" theme created by Elegant Themes. If you want some great themes at a
 * great price, check out the links below.
 *
 * Author:      Elegant Themes
 * Author URI:  http://www.elegantthemes.com/
 * Theme Name:  Divi
 * Theme URI:   http://www.elegantthemes.com/gallery/divi/
 *
 * ----------------------------------------------------------------------------//
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

class Divi_Builder_Integration_Admin {

	public $options;

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.1';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'divi-builder-integration';



	private function __construct() {

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Retrieve the options to the settings page.
		add_action( 'admin_init', array( $this, 'get_settings_and_fields') );

		// Add the options to the settings page.
		add_action( 'admin_init', array( $this, 'register_settings_and_fields') );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 */
		
		// Only enable the builder if Divi is your current theme
		$theme = wp_get_theme( $stylesheet, $theme_root );

		if($theme->get('Name') == 'Divi'                // Divi is the current theme
			|| $theme->get('Template') == 'Divi') {     // Or the current theme is a child of Divi

			// Render all of the related HTML for the page builder onto the admin
			// page for the post type. This includes the "Use Builder" button.
			add_action( 'edit_form_after_title', array( $this, 'dbi_edit_form_after_title_custom' ) );

			// Finish adding the html to the admin page for each enabled content type.
			add_action( 'edit_form_after_editor', array( $this, 'dbi_edit_form_after_editor_custom' ) );

			// Make sure that the needed JS and CSS is loaded for each enabled content type.
			// Interestingly enough, Elegant Themes provides a hook for this.
			add_filter( 'et_pb_builder_post_types', array( $this, 'dbi_et_pb_builder_post_types_custom' ) );

			// Add the actual meta box to the admin page. Primarily this has to do
			// with saving whether or not the page builder is turned on for the page.
			add_action( 'add_meta_boxes', array( $this, 'dbi_et_add_post_meta_box_custom') , 11);
		}

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

	/*
	 * Add a settings page for this plugin to the Settings menu.
	 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Divi Builder Integration', $this->plugin_slug ),
			__( 'Divi Builder', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		include_once( 'views/admin.php' );
	}

	/**
	 * Get the settings and fields stored in the database.
	 *
	 * @since    1.0.0
	 */
	public function get_settings_and_fields() {
		$this->options = get_option('dbi_settings');
	}

	/**
	 * Register the settings and fields for the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function register_settings_and_fields() {


		/* If the options do not exist then create them*/
		if ( false == get_option( 'dbi_settings' ) ) {
		    add_option( 'dbi_settings', array());
		}

		register_setting('dbi_settings', 'dbi_settings');

		add_settings_section(
	        'dbi_main_section',                          // ID used to identify this section and with which to register options
	        'Enabled Post Types',                        // Title to be displayed on the administration page
	        array($this, 'dbi_main_settings_callback'),  // Callback used to render the description of the section
	        $this->plugin_slug                           // Page on which to add this section of options
	    );

		$post_types = get_post_types();

		foreach ($post_types as $post_type) {

		    // Checkbox for each post type
		    add_settings_field($post_type, $post_type, array( $this, 'dbi_post_setting' ), $this->plugin_slug, 'dbi_main_section', $post_type);
		}

	}

	public function dbi_main_settings_callback() {
	    echo '<p>Check each post type that you would like the Divi Page Builder to appear on.</p>';
	    echo '<p style="font-style: italic;">Note that the <strong>page</strong> and <strong>project</strong> types are always enabled since they are hard-coded into the Divi Page Builder.</p>';
	}

	/**
	 * Input field creation methods
	 *
	 * @since    1.0.0
	 */
	public function dbi_post_setting($post_type) {
		if ($post_type == 'page' || $post_type == 'project') {
			echo "<input name='dbi_settings[".$post_type."]' type='checkbox' value='1' checked='checked' disabled='disabled' />";
		}
		else {
			echo "<input name='dbi_settings[".$post_type."]' type='checkbox' value='1' ".checked('1', $this->options[$post_type], false)."' />";
		}
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

	public function dbi_edit_form_after_title_custom ($post) {

		// Just return if we're dealing with pages or projects; it's already
		// been added to those content types.
		if ( in_array( $post->post_type, array( 'page', 'project' ) ) ) return;

		// Return if this post type isn't turned on
		if ( !is_array($this->options) || !array_key_exists( $post->post_type, $this->options ) ) return;

		// Make sure that the neccessary post meta data exists.
		add_post_meta($post->ID, '_et_pb_use_builder', 'off', true);

		// I don't think the code below is neccessary? Not really sure what it does.
		// Set page template to default or save_post action will not run
		// if ( 'page' === $post->post_type )
		// 	update_post_meta( $post->ID, '_wp_page_template', 'default' );

		$is_builder_used = 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) ? true : false;

		printf( '<a href="#" id="et_pb_toggle_builder" data-builder="%2$s" data-editor="%3$s" class="button button-primary button-large%5$s">%1$s</a><div id="et_pb_main_editor_wrap"%4$s>',
			( $is_builder_used ? __( 'Use Default Editor', 'Divi' ) : __( 'Use Page Builder', 'Divi' ) ),
			__( 'Use Page Builder', 'Divi' ),
			__( 'Use Default Editor', 'Divi' ),
			( $is_builder_used ? ' class="et_pb_hidden"' : '' ),
			( $is_builder_used ? ' et_pb_builder_is_used' : '' )
		);
	}

	// Finish adding the html to the admin page for each enabled content type.
	public function dbi_edit_form_after_editor_custom ($post) {

		// Just return if we're dealing with pages or projects; it's already
		// been added to those content types.
		if ( in_array( $post->post_type, array( 'page', 'project' ) ) ) return;

		// Return if this post type isn't turned on
		if ( !is_array($this->options) || !array_key_exists( $post->post_type, $this->options ) ) return;
		
		echo '</div> <!-- #et_pb_main_editor_wrap -->';
	}

	// Make sure that the needed JS and CSS is loaded for each enabled content type.
	// Interestingly enough, Elegant Themes provides a hook for this.
	public function dbi_et_pb_builder_post_types_custom ($content) {

		if(is_array($this->options)) {
			foreach ($this->options as $key => $post_type) {
				if(!in_array($key, $content)) {
					$content[] = $key;
				}
			}			
		}

		return $content;
	}

	// Add the actual meta box to the admin page. Primarily this has to do
	// with saving whether or not the page builder is turned on for the page.
	public function dbi_et_add_post_meta_box_custom() {
		$post_types = get_post_types();
		remove_meta_box( 'et_settings_meta_box', 'post', 'side' );

		foreach ($post_types as $type) {
			if ( !in_array( $type, array( 'page', 'project' ) ) ) {
				add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Divi' ), array( $this, 'dbi_et_single_settings_meta_box_custom' ), $type, 'side', 'high' );
			}
		}
	}

	// This is called by et_add_post_meta_box_custom(), and is basically just the
	// actual output needed to set up the meta box on the page.
	public function dbi_et_single_settings_meta_box_custom( $post ) {
		$post_id = get_the_ID();

		// Turn the page builder on, if it isn't already set.
		add_post_meta($post_id, '_et_pb_use_builder', 'off', true);

		wp_nonce_field( 'functions.php', 'et_settings_nonce' );

		$page_layout = get_post_meta( $post_id, '_et_pb_page_layout', true );

		$page_layouts = array(
			'et_right_sidebar'   => __( 'Right Sidebar', 'Divi' ),
	   		'et_left_sidebar'    => __( 'Left Sidebar', 'Divi' ),
	   		'et_full_width_page' => __( 'Full Width', 'Divi' ),
		);
		?>
		<p class="et_pb_page_settings et_pb_page_layout_settings">
			<label for="et_pb_page_layout" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Page Layout', 'Divi' ); ?>: </label>

			<select id="et_pb_page_layout" name="et_pb_page_layout">
			<?php
			foreach ( $page_layouts as $layout_value => $layout_name ) {
				printf( '<option value="%2$s"%3$s>%1$s</option>',
					esc_html( $layout_name ),
					esc_attr( $layout_value ),
					selected( $layout_value, $page_layout )
				);
			} ?>
			</select>
		</p>

	<?php if ( !in_array( $post->post_type, array( 'page', 'project' ) ) ) : ?>

		<p class="et_pb_page_settings" style="display: none;">
			<input type="hidden" id="et_pb_use_builder" name="et_pb_use_builder" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_pb_use_builder', true ) ); ?>" />
			<textarea id="et_pb_old_content" name="et_pb_old_content"><?php echo esc_attr( get_post_meta( $post_id, '_et_pb_old_content', true ) ); ?></textarea>
		</p>
	<?php endif; ?>
	<?php
	}

}
