<?php
/**
 * Singleton trait.
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton\Traits;

trait Singleton {

	/**
	 * Protected constructor — override in child class to hook actions/filters.
	 */
	protected function __construct() {
	}

	/**
	 * Prevent cloning.
	 */
	final protected function __clone() {
	}

	/**
	 * Return the singleton instance.
	 *
	 * @param mixed ...$args Constructor arguments.
	 * @return static
	 */
	final public static function get_instance( ...$args ) {
		static $instances = [];

		$called_class = get_called_class();
		$key          = $called_class . maybe_serialize( $args );

		if ( ! isset( $instances[ $key ] ) ) {
			$instances[ $key ] = new $called_class( ...$args );

			do_action( sprintf( 'plugin_skeleton_singleton_init_%s', $called_class ) ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}

		return $instances[ $key ];
	}
}
