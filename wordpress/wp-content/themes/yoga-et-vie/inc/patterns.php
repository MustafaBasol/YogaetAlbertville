<?php
/**
 * Pattern categories. Patterns themselves are auto-registered from the
 * patterns/ directory (file headers declare title, slug, categories…).
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the pattern categories shown in the editor inserter.
 */
function yev_register_pattern_categories(): void {
	$categories = array(
		'yoga-et-vie-pages'    => array(
			'label'       => __( 'Yoga et Vie — pages complètes', 'yoga-et-vie' ),
			'description' => __( 'Modèles de pages prêtes à remplir.', 'yoga-et-vie' ),
		),
		'yoga-et-vie-sections' => array(
			'label'       => __( 'Yoga et Vie — sections', 'yoga-et-vie' ),
			'description' => __( 'Sections à insérer dans une page : bandeau, cartes, planning, contact…', 'yoga-et-vie' ),
		),
		'yoga-et-vie-contenus' => array(
			'label'       => __( 'Yoga et Vie — cours & événements', 'yoga-et-vie' ),
			'description' => __( 'Mises en page pour une fiche cours ou un événement.', 'yoga-et-vie' ),
		),
	);

	foreach ( $categories as $slug => $args ) {
		register_block_pattern_category( $slug, $args );
	}
}
add_action( 'init', 'yev_register_pattern_categories' );

/**
 * Hides the remote pattern directory and core patterns from the inserter
 * so non-technical editors only see patterns that match this design.
 */
function yev_limit_patterns(): void {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'yev_limit_patterns', 20 );
add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Returns the URL of an image shipped with the theme (for patterns).
 *
 * @param string $file File name inside assets/images/.
 * @return string
 */
function yev_image_url( string $file ): string {
	return get_theme_file_uri( 'assets/images/' . $file );
}
