<?php
/*
Plugin Name:       Divi Builder Integration
Plugin URI:        http://laternastudio.com/blog/divi-page-builder-for-custom-post-types
Description:       Use the Divi page builder on posts and CPTs
Version:           1.0.2
Author:            Pete Molinero
Author URI:        http://www.laternastudio.com
License:           GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-divi-builder-integration-admin.php' );
	add_action( 'plugins_loaded', array( 'Divi_Builder_Integration_Admin', 'get_instance' ) );

}
