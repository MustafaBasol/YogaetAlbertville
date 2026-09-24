<?php
/**
 * Static validation of the block theme — no WordPress needed (used in CI).
 *
 * Usage : php tools/validate-theme.php
 *
 * Checks:
 *  - theme.json and style variation partials are valid JSON (version 3);
 *  - every pattern has Title and Slug headers, a unique "yoga-et-vie/" slug
 *    and a known category;
 *  - block comment delimiters are balanced in templates, parts and patterns;
 *  - referenced patterns, template parts and custom templates exist;
 *  - no image is hot-linked from another website.
 *
 * @package YogaEtVie
 */

$root   = dirname( __DIR__ ) . '/wordpress/wp-content/themes/yoga-et-vie';
$errors = array();
$fail   = static function ( string $message ) use ( &$errors ) {
	$errors[] = $message;
};

// 1. JSON files.
$theme_json = json_decode( (string) file_get_contents( "$root/theme.json" ), true );
if ( ! is_array( $theme_json ) || 3 !== ( $theme_json['version'] ?? null ) ) {
	$fail( 'theme.json : JSON invalide ou version différente de 3.' );
}
foreach ( (array) glob( "$root/styles/**/*.json" ) as $file ) {
	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) || empty( $data['title'] ) || empty( $data['blockTypes'] ) ) {
		$fail( basename( $file ) . ' : variation de style invalide (title / blockTypes).' );
	}
}

// 2. Patterns.
$categories = array( 'yoga-et-vie-pages', 'yoga-et-vie-sections', 'yoga-et-vie-contenus', 'featured', 'banner', 'about', 'text', 'team', 'call-to-action' );
$slugs      = array();
foreach ( glob( "$root/patterns/*.php" ) as $file ) {
	$source = (string) file_get_contents( $file );
	$name   = 'patterns/' . basename( $file );

	preg_match( '/^\s*\*\s*Title:\s*(.+)$/m', $source, $title );
	preg_match( '/^\s*\*\s*Slug:\s*(.+)$/m', $source, $slug );
	preg_match( '/^\s*\*\s*Categories:\s*(.+)$/m', $source, $cats );

	if ( empty( $title[1] ) || empty( $slug[1] ) ) {
		$fail( "$name : en-tête Title ou Slug manquant." );
		continue;
	}
	$slug = trim( $slug[1] );
	if ( ! str_starts_with( $slug, 'yoga-et-vie/' ) ) {
		$fail( "$name : le slug doit commencer par yoga-et-vie/." );
	}
	if ( isset( $slugs[ $slug ] ) ) {
		$fail( "$name : slug en double ($slug)." );
	}
	$slugs[ $slug ] = $file;

	foreach ( array_map( 'trim', explode( ',', $cats[1] ?? '' ) ) as $cat ) {
		if ( $cat && ! in_array( $cat, $categories, true ) ) {
			$fail( "$name : catégorie inconnue « $cat »." );
		}
	}
}

// 3. Block markup in templates, parts and patterns.
$markup_files = array_merge( glob( "$root/templates/*.html" ), glob( "$root/parts/*.html" ), glob( "$root/patterns/*.php" ) );
foreach ( $markup_files as $file ) {
	$name   = str_replace( "$root/", '', $file );
	$markup = (string) file_get_contents( $file );

	preg_match_all( '/<!--\s+(\/)?wp:([a-z0-9\/-]+)(\s+(\{.*?\}))?\s+(\/)?-->/s', $markup, $matches, PREG_SET_ORDER );
	$stack = array();
	foreach ( $matches as $m ) {
		[ , $closing, $block ] = $m;
		$attrs                 = $m[4] ?? '';
		$self                  = ! empty( $m[5] );

		if ( $attrs && null === json_decode( $attrs ) ) {
			$fail( "$name : attributs JSON invalides pour wp:$block." );
		}
		if ( $closing && ! $self ) {
			$open = array_pop( $stack );
			if ( $open !== $block ) {
				$fail( "$name : wp:$block fermé alors que wp:" . ( $open ?? '(rien)' ) . ' était ouvert.' );
			}
		} elseif ( ! $self ) {
			$stack[] = $block;
		}

		if ( 'pattern' === $block && preg_match( '/"slug":"([^"]+)"/', $attrs, $ref ) && ! isset( $slugs[ $ref[1] ] ) ) {
			$fail( "$name : motif référencé introuvable ({$ref[1]})." );
		}
		if ( 'template-part' === $block && preg_match( '/"slug":"([^"]+)"/', $attrs, $ref ) && ! is_file( "$root/parts/{$ref[1]}.html" ) ) {
			$fail( "$name : partie de modèle introuvable ({$ref[1]})." );
		}
	}
	if ( $stack ) {
		$fail( "$name : bloc(s) non fermé(s) : " . implode( ', ', $stack ) );
	}

	if ( preg_match_all( '/<img[^>]+src="(https?:\/\/[^"]+)"/', $markup, $imgs ) ) {
		foreach ( $imgs[1] as $src ) {
			$fail( "$name : image externe interdite ($src)." );
		}
	}
}

// 4. Custom templates and template parts declared in theme.json.
foreach ( $theme_json['customTemplates'] ?? array() as $template ) {
	if ( ! is_file( "$root/templates/{$template['name']}.html" ) ) {
		$fail( "theme.json : modèle personnalisé introuvable ({$template['name']})." );
	}
}
foreach ( $theme_json['templateParts'] ?? array() as $part ) {
	if ( ! is_file( "$root/parts/{$part['name']}.html" ) ) {
		$fail( "theme.json : partie de modèle introuvable ({$part['name']})." );
	}
}

if ( $errors ) {
	fwrite( STDERR, implode( "\n", $errors ) . "\n" );
	exit( 1 );
}

printf( "Thème valide : %d motifs, %d fichiers de balisage vérifiés.\n", count( $slugs ), count( $markup_files ) );
