<?php
/**
 * Minimal SEO / social sharing fallback.
 *
 * WordPress core already outputs <title>, rel="canonical" on singular
 * content, robots meta and /wp-sitemap.xml. This file adds a meta
 * description and basic Open Graph tags. It switches itself off as soon
 * as a dedicated SEO plugin is active, so it never duplicates tags.
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a known SEO plugin is handling meta tags.
 *
 * @return bool
 */
function yev_seo_plugin_active(): bool {
	$active = defined( 'WPSEO_VERSION' )          // Yoast SEO.
		|| defined( 'RANK_MATH_VERSION' )          // Rank Math.
		|| defined( 'SEOPRESS_VERSION' )           // SEOPress.
		|| defined( 'AIOSEO_VERSION' )             // All in One SEO.
		|| class_exists( 'The_SEO_Framework\Load' ); // The SEO Framework.

	/**
	 * Filters whether the theme should skip its own meta tags.
	 *
	 * @param bool $active Whether an SEO plugin was detected.
	 */
	return (bool) apply_filters( 'yev_seo_plugin_active', $active );
}

/**
 * Builds the description for the current view.
 *
 * @return string
 */
function yev_seo_description(): string {
	$description = '';

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$description = has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$description = get_the_excerpt( (int) get_option( 'page_for_posts' ) );
	}

	if ( '' === trim( wp_strip_all_tags( (string) $description ) ) ) {
		$description = get_bloginfo( 'description' );
	}

	return trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $description ) ) );
}

/**
 * Outputs meta description and Open Graph tags.
 */
function yev_seo_meta_tags(): void {
	if ( yev_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}

	$description = yev_seo_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image       = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	} elseif ( has_custom_logo() ) {
		$image = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
	}

	$tags = array(
		'og:locale'    => 'fr_FR',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:type'      => is_singular( 'post' ) ? 'article' : 'website',
		'og:title'     => $title,
		'og:url'       => $url,
	);

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		$tags['og:description'] = $description;
	}

	if ( $image ) {
		$tags['og:image'] = $image;
	}

	foreach ( $tags as $property => $content ) {
		printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
	}

	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
}
add_action( 'wp_head', 'yev_seo_meta_tags', 5 );
