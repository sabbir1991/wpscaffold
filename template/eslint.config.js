const wpScriptsConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );

module.exports = [
	{
		ignores: [ 'assets/**', 'build/**', 'vendor/**', 'node_modules/**' ],
	},
	...wpScriptsConfig,
];
