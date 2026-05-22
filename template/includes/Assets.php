<?php
/**
 * Assets class — script and style registration.
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton;

use wpRigel\PluginSkeleton\Traits\Singleton;

/**
 * Class Assets.
 */
class Assets {

	use Singleton;

	/* @skeleton-admin */
	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	private function setup_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	/**
	 * Register admin scripts and styles.
	 *
	 * @return void
	 */
	public function admin_scripts(): void {
		$this->register_script( 'plugin-skeleton-admin', 'build/admin.js', [], PLUGIN_SKELETON_VERSION, true );
		$this->register_style( 'plugin-skeleton-admin', 'build/admin.css', [], PLUGIN_SKELETON_VERSION );
		wp_enqueue_script( 'plugin-skeleton-admin' );
		wp_enqueue_style( 'plugin-skeleton-admin' );
	}
	/* @skeleton-admin-end */
	/* @skeleton-block-only */
	/**
	 * Constructor — no hooks needed for block-only plugins.
	 */
	protected function __construct() {
	}
	/* @skeleton-block-only-end */

	/**
	 * Load asset metadata from generated .asset.php file.
	 *
	 * @param string $file Relative path under assets/.
	 * @param array  $deps Additional dependency handles.
	 * @param mixed  $ver  Fallback version.
	 * @return array{dependencies: string[], version: string|false}
	 */
	public function get_asset_meta( string $file, array $deps = [], $ver = false ): array {
		$asset_meta_file = sprintf(
			'%s/%s.asset.php',
			untrailingslashit( PLUGIN_SKELETON_ASSET_PATH ),
			preg_replace( '/\.[^.]+$/', '', $file )
		);

		$asset_meta = is_readable( $asset_meta_file )
			? require $asset_meta_file
			: [
				'dependencies' => [],
				'version'      => $this->get_file_version( $file, $ver ),
			];

		$asset_meta['dependencies'] = array_merge( $deps, $asset_meta['dependencies'] );

		return $asset_meta;
	}

	/**
	 * Get a file's version (filemtime or provided fallback).
	 *
	 * @param string     $file Relative path under assets/build/.
	 * @param mixed      $ver  Explicit version override.
	 * @return string|false
	 */
	public function get_file_version( string $file, $ver = false ) {
		if ( ! empty( $ver ) ) {
			return $ver;
		}

		$file_path = sprintf( '%s/%s', PLUGIN_SKELETON_ASSET_PATH, $file );

		return file_exists( $file_path ) ? (string) filemtime( $file_path ) : false;
	}

	/**
	 * Register a script.
	 *
	 * @param string      $handle    Script handle.
	 * @param string      $file      Path relative to assets/ (or full URL).
	 * @param string[]    $deps      Additional dependencies.
	 * @param mixed       $ver       Version override.
	 * @param bool        $in_footer Enqueue in footer.
	 * @return bool
	 */
	public function register_script( string $handle, string $file, array $deps = [], $ver = false, bool $in_footer = true ): bool {
		$src        = str_starts_with( $file, 'https://' ) ? $file : sprintf( '%s/%s', PLUGIN_SKELETON_ASSET_URL, $file );
		$asset_meta = $this->get_asset_meta( $file, $deps, $ver );

		return wp_register_script( $handle, $src, $asset_meta['dependencies'], $asset_meta['version'], $in_footer );
	}

	/**
	 * Register a stylesheet.
	 *
	 * @param string      $handle Script handle.
	 * @param string      $file   Path relative to assets/ (or full URL).
	 * @param string[]    $deps   Additional dependencies.
	 * @param mixed       $ver    Version override.
	 * @param string      $media  Media type.
	 * @return bool
	 */
	public function register_style( string $handle, string $file, array $deps = [], $ver = false, string $media = 'all' ): bool {
		if ( str_starts_with( $file, 'https://' ) ) {
			$src = $file;
			$ver = PLUGIN_SKELETON_VERSION;
		} else {
			$src = sprintf( '%s/%s', PLUGIN_SKELETON_ASSET_URL, $file );
			$ver = $this->get_file_version( $file, $ver );
		}

		return wp_register_style( $handle, $src, $deps, $ver, $media );
	}
}
