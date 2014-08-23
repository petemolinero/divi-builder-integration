<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Divi Page Builder Integration
 * @author    Pete Molinero <pete@laternastudio.com>
 * @license   GPL-2.0+
 * @link      http://www.laternastudio.com
 * @copyright 2014 Laterna Studio
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('dbi_settings');

// Remove the files
$upload_dir = wp_upload_dir();
$directory = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . "divi-builder-integration" . DIRECTORY_SEPARATOR;
if (is_dir($directory)) {
	foreach(glob($directory.'*.*') as $v){
		unlink($v);
	}
	rmdir($directory);
}

