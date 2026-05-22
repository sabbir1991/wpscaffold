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

	public function test_menu_can_be_instantiated(): void {
		Functions\stubs( [ 'add_action' ] );

		$menu = new Menu();
		$this->assertInstanceOf( Menu::class, $menu );
	}

	public function test_menu_registers_admin_menu_action(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_menu', \Mockery::type( 'array' ) );

		new Menu();
	}
}
