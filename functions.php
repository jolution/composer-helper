<?php

/**
 * @link              https://jolution.de
 * @since             1.0.0
 * @package           Composer_Helper
 *
 * @wordpress-plugin
 * Plugin Name:       Composer Helper
 * Plugin URI:        https://github.com/jolution/composer-helper
 * Description:       Helper Plugin for generate Requirements for Composer based on installed Plugins.
 * Version:           1.0.0
 * Author:            Julian Kasimir
 * Author URI:        https://jolution.de
 * License:           GPL-3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       composer-helper
 * Domain Path:       /languages
 */

// Namespace
namespace Jolution\ComposerHelperPlugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function add_settings_page() {
	add_options_page(
		'Composer Helper',
		'Composer Helper',
		'manage_options',
		'composer-helper', // page slug (options-general.php?page=wp-configure-cors-origin)
		__NAMESPACE__ . '\render_settings_page'
	);
}

add_action( 'admin_menu', __NAMESPACE__ . '\add_settings_page' );

function render_settings_page() {

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$active_plugins        = get_option( 'active_plugins' );
	$all_plugins           = get_plugins();
	$premium_plugins       = [ 'borlabs-cookie', 'revslider', 'divi-blog-extras' ];
	$premium_plugins_found = [];

	// @TODO: add options to change repository to github/Composer
	$counter = 0;
	$code    = "\n" . '"require": {';
	$code    .= "\n";
	foreach ( $all_plugins as $key => $value ) {
		if ( in_array( $value["TextDomain"], $premium_plugins ) ) {
			$premium_plugins_found[] = $value["TextDomain"];
			continue;
		}
		$code      .= "\t";
//		$is_active = ! in_array( $key, $active_plugins );
		$code .= sprintf( '"wpackagist-plugin/%s": "%s"', $value["TextDomain"], $value["Version"] );
		if ( $counter !== (count( $all_plugins ) - count( $premium_plugins_found )) - 2 ) {
			$code .= ",";
		}
		$code .= "\n";
		$counter ++;
	}
	$code .= '},';
	?>
    <h2>Composer Helper</h2>
    <pre style="white-space: pre-wrap">
		<?php echo $code; ?>
    </pre>
    <h3>Skipped Premium Plugins</h3>
    <ul>
		<?php
		foreach ( $premium_plugins_found as $plugin ) {
			echo "<li>" . $plugin . "</li>";
		}
		?>
    </ul>
	<?php
}
