<?php
/**
 * Tests for the Admin\Menu class.
 *
 * @package wpRigel\PluginSkeleton\Tests\Unit
 */

namespace wpRigel\PluginSkeleton\Tests\Unit;

use Brain\Monkey\Functions;
use wpRigel\PluginSkeleton\Admin\Menu;

class MenuTest extends AbstractTestCase {

	private function make_menu(): Menu {
		return new class extends Menu {
			public function __construct() {
				parent::__construct();
			}
		};
	}

	public function test_menu_can_be_instantiated(): void {
		Functions\stubs( [ 'add_action' ] );

		$menu = $this->make_menu();
		$this->assertInstanceOf( Menu::class, $menu );
	}

	public function test_setup_hooks_registers_admin_menu_action(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_menu', \Mockery::type( 'array' ) );

		$this->make_menu();
	}
}
