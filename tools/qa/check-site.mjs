/**
 * Front-end QA for a running local site (see tools/local-preview.sh).
 *
 *   npm install --no-save playwright axe-core
 *   node tools/qa/check-site.mjs [baseUrl]
 *
 * For each page and viewport (375, 768, 1024, 1440 px) it:
 *  - saves a full-page screenshot in tools/qa/screenshots/ (git-ignored);
 *  - fails on horizontal scrolling;
 *  - runs axe-core (WCAG 2.0/2.1/2.2 A + AA rules) once per page at 1440 px;
 *  - checks there is exactly one <h1> and no skipped heading level.
 *
 * Environment variables: PLAYWRIGHT_MODULE / AXE_PATH to point at copies
 * installed elsewhere.
 */
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';

const require = createRequire( import.meta.url );
const { chromium } = await import( process.env.PLAYWRIGHT_MODULE || 'playwright' );
const axePath = process.env.AXE_PATH || require.resolve( 'axe-core/axe.min.js' );
const axeSource = fs.readFileSync( axePath, 'utf8' );

const base = ( process.argv[ 2 ] || 'http://localhost:8888' ).replace( /\/$/, '' );
const outDir = path.join( path.dirname( new URL( import.meta.url ).pathname ), 'screenshots' );
fs.mkdirSync( outDir, { recursive: true } );

const pages = {
	accueil: '/',
	'le-yoga': '/le-yoga/',
	'les-cours': '/les-cours/',
	cours: '/cours/yoga-doux/',
	association: '/association/',
	evenements: '/evenements/',
	evenement: '/stage-respirer-sancrer/',
	'infos-pratiques': '/infos-pratiques/',
	contact: '/contact/',
	'mentions-legales': '/mentions-legales/',
	'404': '/page-inexistante/',
};
const widths = [ 375, 768, 1024, 1440 ];

const browser = await chromium.launch();
const problems = [];

for ( const [ name, url ] of Object.entries( pages ) ) {
	for ( const width of widths ) {
		const page = await browser.newPage( { viewport: { width, height: 900 } } );
		await page.goto( base + url, { waitUntil: 'networkidle' } );

		const overflow = await page.evaluate( () => document.documentElement.scrollWidth - window.innerWidth );
		if ( overflow > 0 ) {
			problems.push( `${ name } @${ width }px: horizontal overflow of ${ overflow }px` );
		}
		// Load lazy images before the full-page capture.
		await page.evaluate( async () => {
			for ( let y = 0; y < document.body.scrollHeight; y += 600 ) {
				window.scrollTo( 0, y );
				await new Promise( ( r ) => setTimeout( r, 60 ) );
			}
			window.scrollTo( 0, 0 );
		} );
		await page.waitForLoadState( 'networkidle' );
		await page.screenshot( { path: path.join( outDir, `${ name }-${ width }.png` ), fullPage: true } );

		if ( width === 1440 ) {
			const headings = await page.$$eval( 'h1,h2,h3,h4,h5,h6', ( els ) => els.map( ( el ) => Number( el.tagName[ 1 ] ) ) );
			const h1Count = headings.filter( ( level ) => level === 1 ).length;
			if ( h1Count !== 1 ) {
				problems.push( `${ name }: ${ h1Count } <h1> elements` );
			}
			headings.forEach( ( level, i ) => {
				if ( i > 0 && level > headings[ i - 1 ] + 1 ) {
					problems.push( `${ name }: heading level skipped (h${ headings[ i - 1 ] } → h${ level })` );
				}
			} );

			await page.addScriptTag( { content: axeSource } );
			const result = await page.evaluate( () =>
				window.axe.run( document, { runOnly: { type: 'tag', values: [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice' ] } } )
			);
			for ( const v of result.violations ) {
				problems.push( `${ name }: [axe ${ v.impact }] ${ v.id } — ${ v.help } (${ v.nodes.length } élément(s)) ${ v.nodes.slice( 0, 2 ).map( ( n ) => n.target.join( ' ' ) ).join( ' | ' ) }` );
			}
		}
		await page.close();
	}
	console.log( `✓ ${ name }` );
}

await browser.close();

if ( problems.length ) {
	console.log( `\n${ problems.length } problème(s) :\n- ` + problems.join( '\n- ' ) );
	process.exitCode = 1;
} else {
	console.log( '\nAucun problème détecté.' );
}
console.log( `Captures : ${ outDir }` );
