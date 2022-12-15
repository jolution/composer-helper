<?php

/**
 * @link              https://jolution.de
 * @since             1.0.0
 * @package           Composer_Wp_Helper
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Composer Helper
 * Plugin URI:        https://github.com/jolution/composer-wp-helper
 * Description:       Test
 * Version:           1.0.0
 * Author:            Julian
 * Author URI:        https://jolution.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       composer-wp-helper
 * Domain Path:       /languages
 */

// Namespace
namespace Jolution\ComposerWpHelperPlugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function add_settings_page() {
	add_options_page(
		'WP Composer Helper',
		'Composer Helper',
		'manage_options',
		'composer-wp-helper', // page slug (options-general.php?page=wp-configure-cors-origin)
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
	$premium_plugins       = [ 'borlabs-cookie', 'zrm-posfinder' ];
	$premium_plugins_found = [];
//	$activated_plugins = array();

	/*foreach ( $active_plugins as $plugin ) {
		if ( isset( $all_plugins[ $plugin ] ) ) {
			$activated_plugins[] = $all_plugins[ $plugin ];
        }
	}*/

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
    <h2>WordPress Composer Helper</h2>
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
