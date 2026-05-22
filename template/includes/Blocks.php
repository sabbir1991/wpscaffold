<?php
/**
 * Block registration.
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton;

use wpRigel\PluginSkeleton\Traits\Singleton;

/**
 * Class Blocks.
 */
class Blocks {

	use Singleton;

	/**
	 * Constructor — registers hooks.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register all plugin blocks from the build directory.
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		$build_dir = PLUGIN_SKELETON_PATH . '/build';

		if ( ! is_dir( $build_dir ) ) {
			return;
		}

		foreach ( glob( $build_dir . '/*/block.json' ) as $block_json ) {
			register_block_type( dirname( $block_json ) );
		}
	}
}
