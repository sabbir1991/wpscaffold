/**
 * Block editor component for Plugin Skeleton.
 *
 * @package plugin-skeleton
 */

import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Edit component — rendered in the block editor.
 *
 * @return {JSX.Element} Block edit view.
 */
export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<p>{ __( 'Plugin Skeleton block — edit view.', 'plugin-skeleton' ) }</p>
		</div>
	);
}
