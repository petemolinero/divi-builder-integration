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
