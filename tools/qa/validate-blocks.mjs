/**
 * Validates block markup with the real block editor.
 *
 * Opens the WordPress editor of a running site, then parses every theme
 * pattern, template, template part and every page / course / post with
 * wp.blocks.parse(). Any block the editor would flag as "invalid content"
 * (markup not matching what the block would save) is reported.
 *
 *   npm install --no-save playwright
 *   node tools/qa/validate-blocks.mjs [baseUrl] [user] [password]
 */
const { chromium } = await import( process.env.PLAYWRIGHT_MODULE || 'playwright' );

const base = ( process.argv[ 2 ] || 'http://localhost:8888' ).replace( /\/$/, '' );
const user = process.argv[ 3 ] || 'admin';
const pass = process.argv[ 4 ] || 'admin';

const browser = await chromium.launch();
const page = await browser.newPage();

await page.goto( `${ base }/wp-login.php` );
await page.fill( '#user_login', user );
await page.fill( '#user_pass', pass );
await Promise.all( [ page.waitForNavigation(), page.click( '#wp-submit' ) ] );

await page.goto( `${ base }/wp-admin/post-new.php?post_type=page` );
await page.waitForFunction( () => window.wp?.blocks?.getBlockTypes?.().length > 50, null, { timeout: 60000 } );

const report = await page.evaluate( async () => {
	const { apiFetch, blocks } = window.wp;
	const sources = [];

	const patterns = await apiFetch( { path: '/wp/v2/block-patterns/patterns' } );
	patterns.filter( ( p ) => p.name.startsWith( 'yoga-et-vie/' ) ).forEach( ( p ) => sources.push( [ `motif ${ p.name }`, p.content ] ) );

	for ( const type of [ 'templates', 'template-parts' ] ) {
		const items = await apiFetch( { path: `/wp/v2/${ type }?context=edit&per_page=100` } );
		items.filter( ( t ) => t.theme === 'yoga-et-vie' ).forEach( ( t ) => sources.push( [ `${ type } ${ t.slug }`, t.content.raw ] ) );
	}

	for ( const type of [ 'pages', 'posts', 'yev_cours' ] ) {
		const items = await apiFetch( { path: `/wp/v2/${ type }?context=edit&per_page=100&status=any` } ).catch( () => [] );
		items.forEach( ( item ) => sources.push( [ `${ type } ${ item.slug }`, item.content.raw ] ) );
	}

	const problems = [];
	let count = 0;
	const walk = ( list, label ) => {
		for ( const block of list ) {
			count++;
			if ( block.name === 'core/missing' ) {
				problems.push( `${ label}: bloc inconnu ${ block.attributes.originalName }` );
			} else if ( block.isValid === false ) {
				const issue = ( block.validationIssues || [] ).map( ( i ) => i.args?.slice( 0, 3 ).join( ' ' ) ).join( ' / ' );
				problems.push( `${ label }: ${ block.name } invalide — ${ issue.slice( 0, 400 ) }` );
			}
			walk( block.innerBlocks || [], label );
		}
	};
	for ( const [ label, content ] of sources ) {
		walk( blocks.parse( content ), label );
	}
	return { problems, count, sources: sources.length };
} );

await browser.close();

console.log( `${ report.sources } contenus, ${ report.count } blocs analysés.` );
if ( report.problems.length ) {
	console.log( report.problems.join( '\n' ) );
	process.exitCode = 1;
} else {
	console.log( 'Aucun bloc invalide.' );
}
