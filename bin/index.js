#!/usr/bin/env node

'use strict';

const { generate } = require( '../lib/generator' );

const args    = process.argv.slice( 2 );
const command = args[ 0 ];
const name    = args.slice( 1 ).join( ' ' ).trim();

if ( command === 'create' ) {
	generate( name ).catch( ( err ) => {
		console.error( '\n  Error:', err.message );
		process.exit( 1 );
	} );
} else {
	console.error( '\n  Usage: wpscaffold create "Plugin Name"\n' );
	process.exit( 1 );
}
