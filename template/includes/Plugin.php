<?php
/**
 * Main plugin class.
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton;

/* @skeleton-admin */
use wpRigel\PluginSkeleton\Admin\Menu;
/* @skeleton-admin-end */
/* @skeleton-block */
use wpRigel\PluginSkeleton\Blocks;
/* @skeleton-block-end */

/**
 * Class Plugin.
 */
class Plugin {

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->load_textdomain();
		$this->load();
	}

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain( 'plugin-skeleton', false, plugin_basename( PLUGIN_SKELETON_PATH ) . '/languages' );
	}

	/**
	 * Instantiate service classes.
	 *
	 * @return void
	 */
	private function load(): void {
		Assets::get_instance();

		/* @skeleton-admin */
		if ( is_admin() ) {
			Menu::get_instance();
		}
		/* @skeleton-admin-end */

		/* @skeleton-block */
		Blocks::get_instance();
		/* @skeleton-block-end */
	}
}
