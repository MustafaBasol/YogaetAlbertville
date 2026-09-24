<?php
/**
 * Theme supports and small editorial adjustments.
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers theme supports.
 */
function yev_theme_setup(): void {
	// Block themes get title-tag, feeds, html5 etc. automatically; these
	// are the extras we rely on.
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 160,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Styles shared between front end and editor so the editor is WYSIWYG.
	add_editor_style( 'assets/css/global.css' );

	// A page "chapô" (excerpt) is used as the page introduction and as the
	// meta description (see inc/seo.php).
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'yev_theme_setup' );

/**
 * Keeps the default "Read more" ellipsis short and French.
 *
 * @return string
 */
function yev_excerpt_more(): string {
	return '…';
}
add_filter( 'excerpt_more', 'yev_excerpt_more' );

/**
 * Shorter excerpts for cards.
 *
 * @return int
 */
function yev_excerpt_length(): int {
	return 28;
}
add_filter( 'excerpt_length', 'yev_excerpt_length' );

/**
 * Removes emoji detection script/styles (extra requests, no benefit here).
 */
function yev_disable_emojis(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'yev_disable_emojis' );
