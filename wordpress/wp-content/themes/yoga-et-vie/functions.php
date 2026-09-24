<?php
/**
 * Yoga et Vie — theme bootstrap.
 *
 * The theme is a block theme: layout lives in templates/, parts/ and
 * patterns/, design tokens in theme.json. PHP is kept to the minimum
 * needed to enqueue per-block CSS, register block styles and pattern
 * categories, and output a small SEO fallback.
 *
 * Structured content (courses, event fields, contact form) lives in the
 * companion plugin "Yoga et Vie — fonctionnalités" (yoga-et-vie-core) so
 * that content survives a future theme change.
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

define( 'YEV_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/assets.php' );
require_once get_theme_file_path( 'inc/block-styles.php' );
require_once get_theme_file_path( 'inc/patterns.php' );
require_once get_theme_file_path( 'inc/seo.php' );
