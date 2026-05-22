<?php
/**
 * Admin menu registration.
 *
 * @package wpRigel\PluginSkeleton
 */

declare(strict_types=1);

namespace wpRigel\PluginSkeleton\Admin;

use wpRigel\PluginSkeleton\Traits\Singleton;

/**
 * Class Menu.
 */
class Menu {

	use Singleton;

	/**
	 * Constructor — registers hooks.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Wire up WordPress hooks.
	 *
	 * @return void
	 */
	public function setup_hooks(): void {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
	}

	/**
	 * Register the top-level admin menu page.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'Plugin Skeleton', 'plugin-skeleton' ),
			__( 'Plugin Skeleton', 'plugin-skeleton' ),
			'manage_options',
			'plugin-skeleton',
			[ $this, 'render_page' ],
			'dashicons-admin-generic',
			'99'
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Plugin Skeleton', 'plugin-skeleton' ); ?></h1>
			<p><?php esc_html_e( 'Plugin Skeleton is working! Start building your plugin here.', 'plugin-skeleton' ); ?></p>
		</div>
		<?php
	}
}
