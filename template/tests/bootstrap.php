<?php
/**
 * PHPUnit bootstrap for unit tests.
 *
 * @package wpRigel\PluginSkeleton\Tests
 */

require dirname( __DIR__ ) . '/vendor/autoload.php';

// Minimal WP environment for unit tests.
defined( 'ABSPATH' ) || define( 'ABSPATH', '/tmp/' );
defined( 'WPINC' ) || define( 'WPINC', 'wp-includes' );

// Plugin constants used by classes under test.
define( 'PLUGIN_SKELETON_VERSION', '1.0.0' );
define( 'PLUGIN_SKELETON_PATH', dirname( __DIR__ ) );
define( 'PLUGIN_SKELETON_URL', 'http://example.com/wp-content/plugins/plugin-skeleton' );
define( 'PLUGIN_SKELETON_ASSET_PATH', PLUGIN_SKELETON_PATH . '/assets' );
define( 'PLUGIN_SKELETON_ASSET_BUILD_PATH', PLUGIN_SKELETON_PATH . '/assets/build' );
define( 'PLUGIN_SKELETON_ASSET_URL', PLUGIN_SKELETON_URL . '/assets' );

// WP_Error stub — avoids requiring a full WP bootstrap.
if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error { // phpcs:ignore
		public string $code;
		public string $message;
		public mixed $data;

		public function __construct( string $code = '', string $message = '', mixed $data = '' ) {
			$this->code    = $code;
			$this->message = $message;
			$this->data    = $data;
		}

		public function get_error_code(): string { return $this->code; }
		public function get_error_message(): string { return $this->message; }
		public function get_error_data(): mixed { return $this->data; }
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ): bool {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( (string) $value );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}
