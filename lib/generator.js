'use strict';

/**
 * Core generator for create-scaffold.
 *
 * Copies the bundled template/, applies type markers and text replacements,
 * then writes a ready-to-develop WordPress plugin directory.
 */

const readline = require( 'readline' );
const fs       = require( 'fs' );
const path     = require( 'path' );
const { execSync } = require( 'child_process' );

const TEMPLATE_DIR = path.join( __dirname, '..', 'template' );

// ── Helpers ──────────────────────────────────────────────────────────────────

function ask( rl, question, defaultVal = '' ) {
	return new Promise( ( resolve ) => {
		const prompt = defaultVal ? `  ${ question } [${ defaultVal }]: ` : `  ${ question }: `;
		rl.question( prompt, ( answer ) => resolve( answer.trim() || defaultVal ) );
	} );
}

function getGitConfig( key ) {
	try {
		return execSync( `git config --global ${ key }`, { encoding: 'utf8' } ).trim();
	} catch {
		return '';
	}
}

function ensureHttps( uri ) {
	if ( ! uri ) return uri;
	return /^https?:\/\//i.test( uri ) ? uri : 'https://' + uri;
}

function toSlug( str ) {
	return str
		.toLowerCase()
		.replace( /[^a-z0-9]+/g, '-' )
		.replace( /^-+|-+$/g, '' );
}

function toPascalCase( str ) {
	return str
		.split( /[-_\s]+/ )
		.map( ( w ) => w.charAt( 0 ).toUpperCase() + w.slice( 1 ) )
		.join( '' );
}

function toSnake( str ) {
	return str.replace( /-/g, '_' );
}

function toConstant( str ) {
	return toSnake( str ).toUpperCase();
}

// ── Marker processing ─────────────────────────────────────────────────────────

/**
 * Remove or strip a marker section.
 *
 * @param {string}  content       Raw file text.
 * @param {string}  marker        Marker name without @/comment syntax.
 * @param {boolean} removeContent true = delete enclosed content; false = keep content, remove markers only.
 */
function removeSection( content, marker, removeContent ) {
	const esc = marker.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );

	if ( removeContent ) {
		const re = new RegExp(
			`[ \\t]*/\\* @${ esc } \\*/[\\s\\S]*?/\\* @${ esc }-end \\*/[ \\t]*\\n?`,
			'g'
		);
		return content.replace( re, '' );
	}

	const reO = new RegExp( `[ \\t]*/\\* @${ esc } \\*/[ \\t]*\\n?`, 'g' );
	const reC = new RegExp( `[ \\t]*/\\* @${ esc }-end \\*/[ \\t]*\\n?`, 'g' );
	return content.replace( reO, '' ).replace( reC, '' );
}

function applyTypeMarkers( content, pluginType ) {
	if ( pluginType === 'admin' ) {
		content = removeSection( content, 'skeleton-block', true );
		content = removeSection( content, 'skeleton-block-only', true );
		content = removeSection( content, 'skeleton-admin', false );
	} else if ( pluginType === 'block' ) {
		content = removeSection( content, 'skeleton-admin', true );
		content = removeSection( content, 'skeleton-block-only', false );
		content = removeSection( content, 'skeleton-block', false );
	} else {
		content = removeSection( content, 'skeleton-block-only', true );
		content = removeSection( content, 'skeleton-admin', false );
		content = removeSection( content, 'skeleton-block', false );
	}
	return content;
}

// ── Replacements ──────────────────────────────────────────────────────────────

function buildReplacements( { pluginName, slug, description, author, authorUri, pluginUri, nsVendor, nsPackage, constPrefix } ) {
	const fnPrefix      = toSnake( slug );
	const composerVendor = nsVendor.toLowerCase();
	const composerName  = `${ composerVendor }/${ slug }`;
	const fullNs        = `${ nsVendor }\\${ nsPackage }`;

	return [
		[ 'wpRigel\\\\PluginSkeleton', `${ nsVendor }\\\\${ nsPackage }` ],
		[ 'wpRigel\\PluginSkeleton',   fullNs ],
		[ 'wprigel/plugin-skeleton',   composerName ],
		[ `wp-block-wprigel-plugin-skeleton`, `wp-block-${ composerVendor }-${ fnPrefix }` ],
		[ 'https://wprigel.com/plugin-skeleton', pluginUri ],
		[ 'https://yoursite.com',      authorUri ],
		[ 'Plugin Skeleton',           pluginName ],
		[ 'plugin-skeleton',           slug ],
		[ 'PLUGIN_SKELETON',           constPrefix ],
		[ 'plugin_skeleton',           fnPrefix ],
		[ 'PluginSkeleton',            nsPackage ],
		[ 'Your Name',                 author ],
		[ 'A WordPress plugin skeleton for rapid development.', description ],
	];
}

function applyReplacements( content, replacements ) {
	let result = content;
	for ( const [ from, to ] of replacements ) {
		result = result.split( from ).join( to );
	}
	return result;
}

// ── Template copying ──────────────────────────────────────────────────────────

const TEXT_EXTS = new Set( [
	'.php', '.js', '.ts', '.json', '.xml', '.yml', '.yaml',
	'.css', '.scss', '.txt', '.md', '.pot', '.sh', '.dist',
] );

const TEXT_DOTFILES = new Set( [
	'_editorconfig', '_gitignore', '_nvmrc', '_stylelintignore',
	'.editorconfig', '.gitignore', '.nvmrc', '.stylelintignore',
	'.gitkeep',
] );

function isTextFile( filePath ) {
	const ext  = path.extname( filePath ).toLowerCase();
	const base = path.basename( filePath );
	return TEXT_EXTS.has( ext ) || TEXT_DOTFILES.has( base );
}

/**
 * Map template filename → output filename.
 * Files/dirs starting with _ become dotfiles/dirs starting with .
 */
function mapName( name ) {
	return name.startsWith( '_' ) ? '.' + name.slice( 1 ) : name;
}

/**
 * Recursively copy template → target, applying transformations.
 */
function copyTemplate( srcDir, destDir, replacements, pluginType, slug ) {
	fs.mkdirSync( destDir, { recursive: true } );

	const entries = fs.readdirSync( srcDir, { withFileTypes: true } );

	for ( const entry of entries ) {
		const srcPath  = path.join( srcDir, entry.name );
		const destName = mapName( entry.name );
		const destPath = path.join( destDir, destName );

		if ( entry.isDirectory() ) {
			copyTemplate( srcPath, destPath, replacements, pluginType, slug );
			continue;
		}

		if ( ! entry.isFile() ) continue;

		if ( isTextFile( srcPath ) ) {
			let content = fs.readFileSync( srcPath, 'utf8' );
			content = applyTypeMarkers( content, pluginType );
			content = applyReplacements( content, replacements );
			fs.writeFileSync( destPath, content, 'utf8' );
		} else {
			fs.copyFileSync( srcPath, destPath );
		}
	}
}

// ── package.json builder ──────────────────────────────────────────────────────

function buildPackageJson( { slug, pluginName, description, author, pluginType } ) {
	const makepot = `wp i18n make-pot . languages/${ slug }.pot --exclude=vendor,node_modules,build,assets/build --ignore-domain`;

	const base = {
		name: slug,
		version: '1.0.0',
		description,
		author,
		license: 'GPL-2.0-or-later',
	};

	let scripts = {
		format:           'wp-scripts format',
		'lint:css':       'wp-scripts lint-style src/',
		'lint:js':        'wp-scripts lint-js src/',
		'fix:js':         'wp-scripts lint-js --fix src/',
		'fix:css':        'wp-scripts lint-style --fix src/',
		'lint:php':       'composer run cs',
		'fix:php':        'composer run cs:fix',
		'packages-update':'wp-scripts packages-update',
		makepot,
		'test:php':       'composer run test',
		prepare:          'lefthook install',
	};

	let files = [ 'includes', 'languages', `${ slug }.php`, 'readme.txt', 'package.json', 'composer.json' ];

	let devDependencies = {
		'@wordpress/scripts': '^32.1.0',
		'lefthook':           '^2.1.6',
	};

	if ( pluginType === 'admin' ) {
		scripts = {
			build:          `wp-scripts build src/global/js/admin.js --output-path=assets/build && npm run makepot`,
			start:          `wp-scripts start src/global/js/admin.js --output-path=assets/build`,
			'build:custom': `wp-scripts build src/global/js/admin.js --output-path=assets/build`,
			zip:            `npm run build && rm -rf ${ slug }.zip && wp-scripts plugin-zip`,
			...scripts,
		};
		files = [ 'assets/build', ...files ];
	} else if ( pluginType === 'block' ) {
		scripts = {
			build: `wp-scripts build && npm run makepot`,
			start: `wp-scripts start`,
			zip:   `npm run build && rm -rf ${ slug }.zip && wp-scripts plugin-zip`,
			...scripts,
		};
		files = [ 'build', ...files ];
		devDependencies = {
			'@wordpress/block-editor': '^12.5.0',
			'@wordpress/blocks':      '^15.18.0',
			'@wordpress/element':     '^5.14.0',
			'@wordpress/i18n':        '^6.18.0',
			...devDependencies,
		};
	} else {
		scripts = {
			build:          `wp-scripts build && wp-scripts build src/global/js/admin.js --output-path=assets/build && npm run makepot`,
			start:          `wp-scripts start`,
			'start:custom': `wp-scripts start src/global/js/admin.js --output-path=assets/build`,
			'build:custom': `wp-scripts build src/global/js/admin.js --output-path=assets/build`,
			zip:            `npm run build && rm -rf ${ slug }.zip && wp-scripts plugin-zip`,
			...scripts,
		};
		files = [ 'assets/build', 'build', ...files ];
		devDependencies = {
			'@wordpress/block-editor': '^12.5.0',
			'@wordpress/blocks':      '^15.18.0',
			'@wordpress/element':     '^5.14.0',
			'@wordpress/i18n':        '^6.18.0',
			...devDependencies,
		};
	}

	return { ...base, scripts, files, devDependencies };
}

// ── Type-based cleanup ────────────────────────────────────────────────────────

function cleanupByType( targetDir, pluginType ) {
	function rm( rel ) {
		const full = path.join( targetDir, rel );
		if ( fs.existsSync( full ) ) {
			fs.rmSync( full, { recursive: true, force: true } );
		}
	}

	if ( pluginType === 'admin' ) {
		rm( 'src/block' );
		rm( 'includes/Blocks.php' );
	} else if ( pluginType === 'block' ) {
		rm( 'src/global' );
		rm( 'includes/Admin/Menu.php' );
		rm( 'tests/Unit/MenuTest.php' );

		const adminDir = path.join( targetDir, 'includes', 'Admin' );
		if ( fs.existsSync( adminDir ) && fs.readdirSync( adminDir ).length === 0 ) {
			fs.rmdirSync( adminDir );
		}
	}
}

// ── Main ──────────────────────────────────────────────────────────────────────

async function generate( initialName = '' ) {
	const rl = readline.createInterface( { input: process.stdin, output: process.stdout } );

	const gitAuthor = getGitConfig( 'user.name' );
	const gitEmail  = getGitConfig( 'user.email' );

	console.log( '\n  ╔══════════════════════════════╗' );
	console.log( '  ║  Create WordPress Scaffold   ║' );
	console.log( '  ╚══════════════════════════════╝\n' );
	console.log( '  Press Enter to accept default values in brackets.\n' );

	try {
		const pluginName  = await ask( rl, 'Plugin Name', initialName || 'My Plugin' );
		const defaultSlug = toSlug( pluginName );
		const slug        = await ask( rl, 'Slug / text-domain', defaultSlug );
		const description = await ask( rl, 'Description', `A WordPress plugin.` );
		const author      = await ask( rl, 'Author Name', gitAuthor || 'your-name' );
		const authorEmail = await ask( rl, 'Author Email', gitEmail || 'your-email' );
		const authorUri   = ensureHttps( await ask( rl, 'Author URI', 'https://yoursite.com' ) );
		const pluginUri   = ensureHttps( await ask( rl, 'Plugin URI', `${ authorUri }/${ slug }` ) );
		const nsVendor    = await ask( rl, 'Namespace Vendor', 'CompanyName' );
		const nsPackage   = await ask( rl, 'Namespace Package', toPascalCase( slug ) );
		const constPrefix = await ask( rl, 'Constant Prefix', toConstant( slug ) );

		const typeRaw   = await ask( rl, 'Plugin Type  [admin / block / both]', 'both' );
		const pluginType = [ 'admin', 'block', 'both' ].includes( typeRaw.toLowerCase() )
			? typeRaw.toLowerCase()
			: 'both';

		const fnPrefix      = toSnake( slug );
		const composerVendor = nsVendor.toLowerCase();
		const composerName  = `${ composerVendor }/${ slug }`;
		const fullNs        = `${ nsVendor }\\${ nsPackage }`;

		console.log( '\n  ─── Summary ───────────────────────────────────' );
		console.log( `  Plugin Name   : ${ pluginName }` );
		console.log( `  Slug          : ${ slug }` );
		console.log( `  Namespace     : ${ fullNs }` );
		console.log( `  Composer      : ${ composerName }` );
		console.log( `  Constant      : ${ constPrefix }` );
		console.log( `  Function      : ${ fnPrefix }` );
		console.log( `  Author        : ${ author }${ authorEmail ? ` <${ authorEmail }>` : '' }` );
		console.log( `  Plugin URI    : ${ pluginUri }` );
		console.log( `  Type          : ${ pluginType }` );
		console.log( '  ────────────────────────────────────────────────\n' );

		const targetDir = path.join( process.cwd(), slug );

		if ( fs.existsSync( targetDir ) ) {
			console.error( `  Error: Directory '${ slug }' already exists in the current folder.` );
			rl.close();
			return;
		}

		const confirm = await ask( rl, `Create plugin in ./${ slug }? (yes/no)`, 'yes' );
		rl.close();

		if ( ! [ 'yes', 'y' ].includes( confirm.toLowerCase() ) ) {
			console.log( '\n  Aborted.\n' );
			return;
		}

		// Build context for replacements
		const context = { pluginName, slug, description, author, authorUri, pluginUri, nsVendor, nsPackage, constPrefix };
		const replacements = buildReplacements( context );

		console.log( `\n  Creating ${ slug }/ ...\n` );

		// Copy template
		copyTemplate( TEMPLATE_DIR, targetDir, replacements, pluginType, slug );

		// Rename main PHP file: plugin-skeleton.php → {slug}.php
		const oldPhp = path.join( targetDir, 'plugin-skeleton.php' );
		const newPhp = path.join( targetDir, `${ slug }.php` );
		if ( fs.existsSync( oldPhp ) ) {
			fs.renameSync( oldPhp, newPhp );
		}

		// Type-based cleanup
		cleanupByType( targetDir, pluginType );

		// Write package.json
		const pkg = buildPackageJson( { slug, pluginName, description, author: authorEmail ? `${ author } <${ authorEmail }>` : author, pluginType } );
		fs.writeFileSync( path.join( targetDir, 'package.json' ), JSON.stringify( pkg, null, '\t' ) + '\n', 'utf8' );

		// Done
		console.log( `  ✅  Created: ${ path.resolve( targetDir ) }\n` );
		console.log( '  Next steps:\n' );
		console.log( `    cd ${ slug }` );
		console.log( '    composer install' );
		console.log( '    npm install' );
		console.log( '    npm run build\n' );
	} catch ( err ) {
		rl.close();
		throw err;
	}
}

module.exports = { generate };
