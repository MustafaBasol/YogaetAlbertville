<?php
/**
 * Deletes the demo courses, posts and images created by
 * seed-demo-content.php (everything flagged with the "_yev_demo" meta)
 * and switches the "contenus provisoires" banner off.
 *
 * Pages are kept: they are the site structure and are meant to be edited.
 *
 * Usage: wp eval-file path/to/tools/remove-demo-content.php
 *
 * @package YogaEtVie
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Ce script s'exécute uniquement avec WP-CLI.\n" );
}

$demo_ids = get_posts(
	array(
		'post_type'      => array( 'post', 'yev_cours', 'attachment' ),
		'post_status'    => 'any',
		'meta_key'       => '_yev_demo', // phpcs:ignore WordPress.DB.SlowDBQuery
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$logo_id = (int) get_theme_mod( 'custom_logo' );
$icon_id = (int) get_option( 'site_icon' );

foreach ( $demo_ids as $demo_id ) {
	if ( $demo_id === $logo_id ) {
		remove_theme_mod( 'custom_logo' );
	}
	if ( $demo_id === $icon_id ) {
		delete_option( 'site_icon' );
	}
	$item_title = get_the_title( $demo_id );
	'attachment' === get_post_type( $demo_id ) ? wp_delete_attachment( $demo_id, true ) : wp_delete_post( $demo_id, true );
	WP_CLI::log( "Supprimé : $item_title (#$demo_id)" );
}

$settings                = (array) get_option( 'yev_settings', array() );
$settings['demo_notice'] = false;
update_option( 'yev_settings', $settings );

WP_CLI::success( count( $demo_ids ) . ' élément(s) de démonstration supprimé(s).' );
