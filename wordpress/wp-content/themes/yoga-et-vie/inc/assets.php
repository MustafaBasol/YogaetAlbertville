<?php
/**
 * Styles. The theme ships no front-end JavaScript of its own: the
 * responsive menu is the core Navigation block.
 *
 * CSS architecture:
 * - theme.json            → design tokens and most block styling;
 * - assets/css/global.css → base rules (focus, skip link, header, footer,
 *                            reduced motion, section helpers);
 * - assets/css/blocks/core-<block>.css → loaded by WordPress only on pages
 *                            that actually contain that block.
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the global stylesheet.
 */
function yev_enqueue_global_styles(): void {
	wp_enqueue_style(
		'yoga-et-vie-global',
		get_theme_file_uri( 'assets/css/global.css' ),
		array(),
		YEV_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'yev_enqueue_global_styles' );

/**
 * Registers one stylesheet per core block found in assets/css/blocks/.
 *
 * File "core-navigation.css" is attached to block "core/navigation", and
 * so on. WordPress inlines or enqueues it only when the block is rendered.
 */
function yev_enqueue_block_styles(): void {
	$files = glob( get_theme_file_path( 'assets/css/blocks/core-*.css' ) );

	if ( ! $files ) {
		return;
	}

	foreach ( $files as $file ) {
		$slug       = basename( $file, '.css' );
		$block_name = 'core/' . substr( $slug, strlen( 'core-' ) );

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => 'yoga-et-vie-' . $slug,
				'src'    => get_theme_file_uri( 'assets/css/blocks/' . $slug . '.css' ),
				'path'   => $file,
				'ver'    => YEV_THEME_VERSION,
			)
		);
	}
}
add_action( 'init', 'yev_enqueue_block_styles' );

/**
 * Preloads the two most used font files to avoid a late font swap.
 */
function yev_preload_fonts(): void {
	$fonts = array(
		'assets/fonts/figtree-latin-wght-normal.woff2',
		'assets/fonts/lora-latin-wght-normal.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $font ) )
		);
	}
}
add_action( 'wp_head', 'yev_preload_fonts', 1 );
