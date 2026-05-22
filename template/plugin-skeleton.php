<?php
/**
 * Plugin Name: Plugin Skeleton
 * Plugin URI: https://wprigel.com/plugin-skeleton
 * Description: A WordPress plugin skeleton for rapid development.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL2
 * Text Domain: plugin-skeleton
 * Domain Path: /languages
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PLUGIN_SKELETON_VERSION', '1.0.0' );
define( 'PLUGIN_SKELETON_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PLUGIN_SKELETON_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'PLUGIN_SKELETON_ASSET_PATH', untrailingslashit( PLUGIN_SKELETON_PATH . '/assets' ) );
define( 'PLUGIN_SKELETON_ASSET_BUILD_PATH', untrailingslashit( PLUGIN_SKELETON_PATH . '/assets/build' ) );
define( 'PLUGIN_SKELETON_ASSET_URL', untrailingslashit( PLUGIN_SKELETON_URL . '/assets' ) );

$autoload_file = __DIR__ . '/vendor/autoload.php';

if ( ! file_exists( $autoload_file ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>' . esc_html__( 'Plugin Skeleton: Run composer install to load dependencies.', 'plugin-skeleton' ) . '</p></div>';
		}
	);
	return;
}

require_once $autoload_file;

/**
 * Get the main Plugin instance.
 *
 * @return Plugin
 */
function plugin_skeleton(): Plugin {
	static $plugin;

	if ( null === $plugin ) {
		$plugin = new Plugin();
	}

	return $plugin;
}

add_action(
	'plugins_loaded',
	function () {
		plugin_skeleton()->run();
	}
);
