<?php
/**
 * Tests for the Assets class.
 *
 * @package wpRigel\PluginSkeleton\Tests\Unit
 */

namespace wpRigel\PluginSkeleton\Tests\Unit;

use Brain\Monkey\Functions;
use wpRigel\PluginSkeleton\Assets;

class AssetsTest extends AbstractTestCase {

	private function make_assets(): Assets {
		return new class extends Assets {
			public function __construct() {
				parent::__construct();
			}
		};
	}

	public function test_assets_can_be_instantiated(): void {
		Functions\stubs( [ 'add_action' ] );

		$assets = $this->make_assets();
		$this->assertInstanceOf( Assets::class, $assets );
	}

	public function test_setup_hooks_registers_admin_enqueue_scripts(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_enqueue_scripts', \Mockery::type( 'array' ) );

		$this->make_assets();
	}

	public function test_get_file_version_returns_false_for_missing_file(): void {
		Functions\stubs( [ 'add_action' ] );

		$assets = $this->make_assets();
		$result = $assets->get_file_version( 'nonexistent.js' );

		$this->assertFalse( $result );
	}

	public function test_get_file_version_returns_provided_version(): void {
		Functions\stubs( [ 'add_action' ] );

		$assets = $this->make_assets();
		$result = $assets->get_file_version( 'nonexistent.js', '2.0.0' );

		$this->assertSame( '2.0.0', $result );
	}
}
