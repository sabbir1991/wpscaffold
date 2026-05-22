/**
 * Block registration for Plugin Skeleton.
 *
 * @package plugin-skeleton
 */

import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit: Edit,
} );
