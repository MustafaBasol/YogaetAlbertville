<?php
/**
 * Optional "contenus provisoires" banner shown while the client is
 * reviewing the site (Réglages > Yoga et Vie).
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the banner right after <body>.
 */
function yev_demo_notice(): void {
	if ( ! yev_setting( 'demo_notice' ) ) {
		return;
	}

	printf(
		'<aside class="yev-demo-notice" aria-label="%s"><p>%s</p></aside>',
		esc_attr__( 'Avertissement', 'yoga-et-vie-core' ),
		esc_html__( 'Site en préparation — les textes, horaires et images sont provisoires et en attente de validation par l’association.', 'yoga-et-vie-core' )
	);
}
add_action( 'wp_body_open', 'yev_demo_notice', 5 );

/**
 * Banner styles (tiny, inlined only when the banner is on).
 */
function yev_demo_notice_styles(): void {
	if ( ! yev_setting( 'demo_notice' ) ) {
		return;
	}

	wp_register_style( 'yev-demo-notice', false, array(), YEV_CORE_VERSION );
	wp_enqueue_style( 'yev-demo-notice' );
	wp_add_inline_style(
		'yev-demo-notice',
		'.yev-demo-notice{background:#33432F;color:#FAF6EF;font-size:.875rem;line-height:1.4;text-align:center;padding:.5rem 1rem}.yev-demo-notice p{margin:0;max-width:60rem;margin-inline:auto}'
	);
}
add_action( 'wp_enqueue_scripts', 'yev_demo_notice_styles' );
