<?php
/**
 * Base test case for all unit tests.
 *
 * @package wpRigel\PluginSkeleton\Tests\Unit
 */

namespace wpRigel\PluginSkeleton\Tests\Unit;

use Brain\Monkey;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

abstract class AbstractTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Monkey\Functions\stubTranslationFunctions();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
