<?php
/**
 * Seeds the DEMO content of the Yoga et Vie prototype.
 *
 * Usage (from the WordPress root, theme + plugin active):
 *   wp eval-file path/to/tools/seed-demo-content.php
 *
 * Idempotent: running it again updates the same pages/posts instead of
 * duplicating them. Every demo course, post and image is flagged with the
 * post meta "_yev_demo" so tools/remove-demo-content.php can delete it.
 *
 * Nothing here is final client content: texts are placeholders written in
 * French, and no factual data (times, prices, names, addresses) is invented.
 *
 * @package YogaEtVie
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Ce script s'exécute uniquement avec WP-CLI.\n" );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

const YEV_SEED_IMAGES = __DIR__ . '/demo-content/images/';

// Block markup must be stored verbatim (WP-CLI runs without a user, which
// would otherwise run the content through the KSES HTML filter).
kses_remove_filters();

/**
 * Returns a theme pattern's content with nested pattern references expanded.
 *
 * @param string $slug Pattern slug.
 * @return string
 */
function yev_seed_pattern( string $slug ): string {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $pattern ) {
		WP_CLI::error( "Motif introuvable : $slug (le thème Yoga et Vie est-il actif ?)" );
	}

	return preg_replace_callback(
		'/<!-- wp:pattern \{"slug":"([^"]+)"\} \/-->/',
		static fn( array $m ) => yev_seed_pattern( $m[1] ),
		$pattern['content']
	);
}

/**
 * Creates or updates a post identified by slug + post type.
 *
 * @param array<string, mixed> $data Post data (post_name and post_type required).
 * @param array<string, mixed> $meta Meta values.
 * @return int Post ID.
 */
function yev_seed_post( array $data, array $meta = array() ): int {
	$existing = get_posts(
		array(
			'name'           => $data['post_name'],
			'post_type'      => $data['post_type'],
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	$data = array_merge( array( 'post_status' => 'publish' ), $data );
	if ( $existing ) {
		$data['ID'] = $existing[0];
	}

	$demo_post_id = $existing ? wp_update_post( wp_slash( $data ), true ) : wp_insert_post( wp_slash( $data ), true );
	if ( is_wp_error( $demo_post_id ) ) {
		WP_CLI::error( $demo_post_id->get_error_message() );
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $demo_post_id, $key, $value );
	}

	WP_CLI::log( sprintf( '%s %s « %s » (#%d)', $existing ? 'Mis à jour :' : 'Créé :', $data['post_type'], $data['post_title'], $demo_post_id ) );
	return (int) $demo_post_id;
}

/**
 * Imports an image from tools/demo-content/images once.
 *
 * @param string $file File name.
 * @param string $alt  Alternative text.
 * @return int Attachment ID.
 */
function yev_seed_image( string $file, string $alt ): int {
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_yev_demo_file', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $file,            // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}

	$tmp = wp_tempnam( $file );
	copy( YEV_SEED_IMAGES . $file, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$alt
	);
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_yev_demo', 1 );
	update_post_meta( $id, '_yev_demo_file', $file );
	return (int) $id;
}

// ---------------------------------------------------------------------------
// 1. Site settings.
// ---------------------------------------------------------------------------

update_option( 'blogname', 'Yoga et Vie Albertville' );
update_option( 'blogdescription', 'Association de yoga à Albertville' );
update_option( 'timezone_string', 'Europe/Paris' );
update_option( 'date_format', 'j F Y' );
update_option( 'time_format', 'G\hi' );
update_option( 'start_of_week', 1 );
update_option( 'default_comment_status', 'closed' );
update_option( 'default_ping_status', 'closed' );
update_option( 'permalink_structure', '/%postname%/' );

if ( in_array( 'fr_FR', get_available_languages(), true ) ) {
	update_option( 'WPLANG', 'fr_FR' );
} else {
	WP_CLI::warning( 'Pack de langue fr_FR absent : lancez « wp language core install fr_FR --activate » pour une administration en français.' );
}

// Remove WordPress' sample content.
foreach ( array(
	'hello-world'    => 'post',
	'sample-page'    => 'page',
	'privacy-policy' => 'page',
) as $slug => $sample_type ) {
	$sample = get_page_by_path( $slug, OBJECT, $sample_type );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}

// ---------------------------------------------------------------------------
// 2. Pages.
// ---------------------------------------------------------------------------

$demo_pages = array(
	'accueil'                      => array( 'Accueil', 'page-no-title', 'page-home', 'Yoga et Vie, association de yoga à Albertville (Savoie) : des cours accessibles et bienveillants, pour tous les âges et tous les niveaux.' ),
	'le-yoga'                      => array( 'Le yoga', '', 'page-yoga', 'Postures, respiration, relaxation : une pratique progressive pour prendre soin de soi, à son rythme.' ),
	'les-cours'                    => array( 'Les cours', '', 'page-courses', 'Le planning de la semaine et la présentation de chaque cours, pour choisir celui qui vous convient.' ),
	'association'                  => array( 'L’association', '', 'page-association', 'Une association locale animée par des bénévoles, où le yoga reste un plaisir partagé.' ),
	'evenements'                   => array( 'Événements & actualités', '', '', 'Stages, ateliers, rencontres et nouvelles de l’association.' ),
	'infos-pratiques'              => array( 'Infos pratiques', '', 'page-practical', 'Lieux, tarifs, inscription, matériel : tout ce qu’il faut savoir avant de venir.' ),
	'contact'                      => array( 'Contact', '', 'page-contact', 'Une question sur les cours ou sur l’association ? Écrivez-nous.' ),
	'mentions-legales'             => array( 'Mentions légales', 'page-legal', 'legal-notice', '' ),
	'politique-de-confidentialite' => array( 'Politique de confidentialité', 'page-legal', 'privacy-policy', '' ),
);

$page_ids   = array();
$menu_order = 0;
foreach ( $demo_pages as $slug => [ $item_title, $template, $pattern, $excerpt ] ) {
	$page_ids[ $slug ] = yev_seed_post(
		array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $item_title,
			'post_excerpt' => $excerpt,
			'post_content' => $pattern ? yev_seed_pattern( 'yoga-et-vie/' . $pattern ) : '',
			'menu_order'   => $menu_order++,
		),
		array( '_wp_page_template' => $template )
	);
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['accueil'] );
update_option( 'page_for_posts', $page_ids['evenements'] );
update_option( 'wp_page_for_privacy_policy', $page_ids['politique-de-confidentialite'] );

// ---------------------------------------------------------------------------
// 3. Courses (one entry = one weekly slot). Day/time left empty on purpose:
// the site shows "à confirmer" until the association provides them.
// ---------------------------------------------------------------------------

$course_body = static function ( string $intro, string $session, string $audience ): string {
	return "<!-- wp:paragraph -->\n<p>$intro</p>\n<!-- /wp:paragraph -->\n\n"
		. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Déroulement d’une séance</h2>\n<!-- /wp:heading -->\n\n"
		. "<!-- wp:paragraph -->\n<p>$session</p>\n<!-- /wp:paragraph -->\n\n"
		. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Pour qui ?</h2>\n<!-- /wp:heading -->\n\n"
		. "<!-- wp:paragraph -->\n<p>$audience</p>\n<!-- /wp:paragraph -->";
};

$courses = array(
	'yoga-pour-tous'          => array(
		'Yoga pour tous',
		'Une séance complète et progressive, pour découvrir le yoga ou poursuivre sa pratique.',
		'Tous niveaux',
		$course_body(
			'Le cours « Yoga pour tous » propose une pratique complète, guidée pas à pas. Chaque posture est expliquée et accompagnée de variantes, pour que chacun puisse avancer à son rythme.',
			'Un temps d’arrivée et de respiration, une mise en mouvement progressive, un enchaînement de postures, puis une relaxation guidée pour terminer.',
			'Débutants comme pratiquants plus réguliers : le cours s’adapte au groupe.'
		),
	),
	'yoga-doux'               => array(
		'Yoga doux',
		'Un rythme plus lent et des postures accessibles, pour pratiquer en douceur.',
		'Pratique douce, accessible à tous',
		$course_body(
			'Le yoga doux privilégie le confort et l’écoute : des mouvements lents, des postures accessibles, souvent avec des supports (coussin, sangle, chaise).',
			'Beaucoup d’attention portée à la respiration, des postures tenues sans effort excessif et de longs temps de détente.',
			'Personnes qui reprennent une activité, qui souhaitent une pratique calme, ou qui préfèrent un rythme plus lent.'
		),
	),
	'yoga-debutants'          => array(
		'Yoga débutants',
		'Les bases du yoga, expliquées simplement, pour commencer en confiance.',
		'Débutants',
		$course_body(
			'Vous n’avez jamais pratiqué ? Ce cours pose les fondations : les postures essentielles, la respiration et quelques repères pour pratiquer en sécurité.',
			'Des explications claires, des postures de base reprises régulièrement et un temps de relaxation en fin de séance.',
			'Toute personne qui découvre le yoga, sans prérequis de souplesse ni de condition physique.'
		),
	),
	'respiration-relaxation'  => array(
		'Respiration & relaxation',
		'Explorer le souffle et apprendre à se détendre profondément.',
		'Tous niveaux',
		$course_body(
			'Ce cours met l’accent sur le souffle (pranayama) et sur la relaxation. Peu de postures, beaucoup d’attention à ce que l’on ressent.',
			'Exercices respiratoires guidés, quelques mouvements simples pour relâcher le corps, puis une relaxation longue.',
			'Toute personne souhaitant apprendre à se poser et à mieux gérer les tensions du quotidien.'
		),
	),
	'yoga-pratique-reguliere' => array(
		'Yoga — pratique régulière',
		'Pour approfondir les postures et gagner en autonomie dans sa pratique.',
		'Pratiquants réguliers',
		$course_body(
			'Un cours pour celles et ceux qui pratiquent déjà : postures plus tenues, enchaînements plus riches, et davantage de place laissée à l’autonomie.',
			'Préparation, séquence approfondie autour d’un thème, respiration et relaxation.',
			'Personnes ayant déjà une pratique régulière du yoga.'
		),
	),
);

foreach ( $courses as $slug => [ $item_title, $excerpt, $level, $content ] ) {
	yev_seed_post(
		array(
			'post_type'    => 'yev_cours',
			'post_name'    => $slug,
			'post_title'   => $item_title,
			'post_excerpt' => $excerpt,
			'post_content' => $content,
		),
		array(
			'yev_level' => $level,
			'_yev_demo' => 1,
		)
	);
}

// ---------------------------------------------------------------------------
// 4. Events & news (regular posts).
// ---------------------------------------------------------------------------

$cat_events = wp_insert_term( 'Événements', 'category', array( 'slug' => 'evenements' ) );
$cat_news   = wp_insert_term( 'Actualités', 'category', array( 'slug' => 'actualites' ) );
$cat_events = is_wp_error( $cat_events ) ? (int) $cat_events->get_error_data( 'term_exists' ) : (int) $cat_events['term_id'];
$cat_news   = is_wp_error( $cat_news ) ? (int) $cat_news->get_error_data( 'term_exists' ) : (int) $cat_news['term_id'];

$event_body = static function ( string $intro, array $programme, string $practical ): string {
	$items = implode(
		"\n\n",
		array_map( static fn( $item ) => "<!-- wp:list-item -->\n<li>$item</li>\n<!-- /wp:list-item -->", $programme )
	);
	return "<!-- wp:paragraph {\"className\":\"is-style-lead\"} -->\n<p class=\"is-style-lead\">$intro</p>\n<!-- /wp:paragraph -->\n\n"
		. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Au programme</h2>\n<!-- /wp:heading -->\n\n"
		. "<!-- wp:list {\"className\":\"is-style-leaf\"} -->\n<ul class=\"wp-block-list is-style-leaf\">$items</ul>\n<!-- /wp:list -->\n\n"
		. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Informations pratiques</h2>\n<!-- /wp:heading -->\n\n"
		. "<!-- wp:paragraph -->\n<p>$practical</p>\n<!-- /wp:paragraph -->";
};

$demo_posts = array(
	'bienvenue-nouveau-site'         => array(
		'Bienvenue sur le nouveau site de Yoga et Vie',
		$cat_news,
		'actualite-site.webp',
		'Illustration : montagnes et soleil levant',
		'Un site plus clair et plus simple pour retrouver les cours, le planning et la vie de l’association.',
		"<!-- wp:paragraph -->\n<p>Notre site fait peau neuve ! Vous y retrouverez plus facilement le planning des cours, les informations pratiques et les prochains rendez-vous de l’association.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Le contenu est en cours de finalisation : certaines informations (horaires, tarifs, lieux) seront complétées prochainement.</p>\n<!-- /wp:paragraph -->",
		'-3 days',
	),
	'stage-respirer-sancrer'         => array(
		'Stage : respirer, s’ancrer',
		$cat_events,
		'evenement-stage.webp',
		'Illustration : sentier menant vers les montagnes',
		'Un stage pour prendre le temps de pratiquer plus longuement autour du souffle et de l’ancrage.',
		$event_body( 'Une demi-journée pour approfondir la pratique autour de la respiration et de l’ancrage, dans une ambiance calme et conviviale.', array( 'Accueil et temps d’échange', 'Pratique posturale autour de l’ancrage', 'Exercices de respiration guidés', 'Relaxation et clôture' ), 'Date, lieu, tarif et nombre de places : à confirmer par l’association.' ),
		'-2 days',
	),
	'atelier-decouverte-respiration' => array(
		'Atelier découverte de la respiration',
		$cat_events,
		'evenement-atelier.webp',
		'Illustration : ondes de respiration et soleil',
		'Un atelier accessible à tous pour découvrir quelques techniques simples de respiration.',
		$event_body( 'Un atelier ouvert à tous, adhérents ou non, pour découvrir des exercices de respiration simples à refaire chez soi.', array( 'Observer sa respiration', 'Quelques techniques de base', 'Temps de relaxation' ), 'Date, lieu et modalités d’inscription : à confirmer.' ),
		'-1 day',
	),
	'assemblee-generale'             => array(
		'Assemblée générale de l’association',
		$cat_events,
		'evenement-ag.webp',
		'Illustration : un cercle de personnes devant les montagnes',
		'Le rendez-vous annuel des adhérents : bilan de la saison, projets et moments de convivialité.',
		$event_body( 'Chaque année, l’assemblée générale réunit les adhérents pour faire le bilan de la saison et préparer la suivante.', array( 'Rapport moral et financier', 'Projets pour la saison', 'Élection du bureau', 'Moment convivial' ), 'Date et lieu : à confirmer. La convocation sera envoyée aux adhérents.' ),
		'now',
	),
);

foreach ( $demo_posts as $slug => [ $item_title, $cat, $image, $alt, $excerpt, $content, $when ] ) {
	$demo_post_id = yev_seed_post(
		array(
			'post_type'     => 'post',
			'post_name'     => $slug,
			'post_title'    => $item_title,
			'post_excerpt'  => $excerpt,
			'post_content'  => $content,
			'post_category' => array( $cat ),
			'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( $when ) ),
		),
		array( '_yev_demo' => 1 )
	);
	set_post_thumbnail( $demo_post_id, yev_seed_image( $image, $alt ) );
}

// Default category for new posts: "Actualités" instead of "Uncategorized".
update_option( 'default_category', $cat_news );
$uncategorized = get_term_by( 'slug', 'uncategorized', 'category' );
if ( $uncategorized && (int) $uncategorized->term_id !== $cat_news ) {
	wp_delete_term( $uncategorized->term_id, 'category' );
}

// ---------------------------------------------------------------------------
// 5. Provisional logo & site icon, demo banner, rewrite rules.
// ---------------------------------------------------------------------------

set_theme_mod( 'custom_logo', yev_seed_image( 'logo-provisoire.png', 'Yoga et Vie' ) );
update_option( 'site_icon', yev_seed_image( 'icone-provisoire.png', '' ) );

$settings                = (array) get_option( 'yev_settings', array() );
$settings['demo_notice'] = true;
update_option( 'yev_settings', $settings );

flush_rewrite_rules();

WP_CLI::success( 'Contenu de démonstration installé. Pensez à le remplacer par les contenus validés par l’association.' );
