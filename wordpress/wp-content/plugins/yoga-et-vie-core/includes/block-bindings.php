<?php
/**
 * Block Bindings source "yoga-et-vie/field".
 *
 * Lets templates and patterns display course / event fields with plain
 * core blocks (Paragraph, Heading, Button), e.g.:
 *
 *   <!-- wp:paragraph {"metadata":{"bindings":{"content":{
 *       "source":"yoga-et-vie/field","args":{"key":"when"}}}}} -->
 *
 * Missing values fall back to a readable French placeholder such as
 * "Horaire à confirmer", so we never display invented data.
 *
 * Conditional display: any block whose CSS class list contains
 * "yev-requires-<key>" (e.g. "yev-requires-event") is removed from the
 * page when that field is empty for the current post.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns a formatted value for a course or event field.
 *
 * @param int    $post_id       Post ID.
 * @param string $key           Field key (see switch below).
 * @param bool   $with_fallback Whether to return the placeholder text when empty.
 * @return string
 */
function yev_course_value( int $post_id, string $key, bool $with_fallback = true ): string {
	$meta = static fn( string $name ) => (string) get_post_meta( $post_id, $name, true );
	$days = yev_days();

	$value    = '';
	$fallback = '';

	switch ( $key ) {
		case 'day':
			$value    = $days[ $meta( 'yev_day' ) ] ?? '';
			$fallback = __( 'Jour à confirmer', 'yoga-et-vie-core' );
			break;

		case 'time':
			$start    = yev_format_time( $meta( 'yev_start' ) );
			$end      = yev_format_time( $meta( 'yev_end' ) );
			$value    = $start && $end ? $start . ' – ' . $end : $start;
			$fallback = __( 'Horaire à confirmer', 'yoga-et-vie-core' );
			break;

		case 'when':
			$day      = $days[ $meta( 'yev_day' ) ] ?? '';
			$time     = yev_course_value( $post_id, 'time', false );
			$value    = trim( $day . ( $day && $time ? ' · ' : '' ) . $time );
			$fallback = $day ? $day . ' · ' . __( 'horaire à confirmer', 'yoga-et-vie-core' ) : __( 'Jour et horaire à confirmer', 'yoga-et-vie-core' );
			break;

		case 'level':
			$value    = $meta( 'yev_level' );
			$fallback = __( 'Public à préciser', 'yoga-et-vie-core' );
			break;

		case 'teacher':
			$value    = $meta( 'yev_teacher' );
			$fallback = __( 'Enseignant·e à confirmer', 'yoga-et-vie-core' );
			break;

		case 'place':
			$value    = $meta( 'yev_place' );
			$fallback = __( 'Lieu à confirmer', 'yoga-et-vie-core' );
			break;

		case 'info':
			$value = $meta( 'yev_info' );
			break;

		case 'event_date':
			$date     = $meta( 'yev_event_date' );
			$value    = $date ? wp_date( 'l j F Y', strtotime( $date . ' 12:00:00' ) ) : '';
			$value    = $value ? ucfirst( $value ) : '';
			$fallback = __( 'Date à confirmer', 'yoga-et-vie-core' );
			break;

		case 'event_when':
			$date     = yev_course_value( $post_id, 'event_date', false );
			$time     = $meta( 'yev_event_time' );
			$value    = trim( $date . ( $date && $time ? ' · ' : '' ) . $time );
			$fallback = __( 'Date à confirmer', 'yoga-et-vie-core' );
			break;

		case 'event_time':
			$value = $meta( 'yev_event_time' );
			break;

		case 'event_place':
			$value    = $meta( 'yev_event_place' );
			$fallback = __( 'Lieu à confirmer', 'yoga-et-vie-core' );
			break;

		case 'event_cta_label':
			$value    = $meta( 'yev_event_cta_label' );
			$fallback = __( 'En savoir plus', 'yoga-et-vie-core' );
			break;

		case 'event_cta_url':
			$value = $meta( 'yev_event_cta_url' );
			break;

		case 'event':
			// "Is this post an event?" — any event field filled, or filed in
			// the "Événements" category.
			foreach ( array_keys( yev_event_fields() ) as $field ) {
				if ( '' !== $meta( $field ) ) {
					$value = '1';
				}
			}
			if ( has_category( YEV_EVENT_CATEGORY, $post_id ) ) {
				$value = '1';
			}
			break;

		case 'excerpt':
			// Only a hand-written excerpt ("chapô"), never an automatic one.
			$value = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
			break;
	}

	return ( '' === $value && $with_fallback ) ? $fallback : $value;
}

/**
 * Registers the binding source.
 */
function yev_register_block_bindings(): void {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return;
	}

	register_block_bindings_source(
		'yoga-et-vie/field',
		array(
			'label'              => __( 'Fiche Yoga et Vie', 'yoga-et-vie-core' ),
			'uses_context'       => array( 'postId', 'postType' ),
			'get_value_callback' => 'yev_block_binding_value',
		)
	);
}
add_action( 'init', 'yev_register_block_bindings' );

/**
 * Binding callback.
 *
 * @param array<string, mixed> $source_args Binding args ("key", optional "fallback").
 * @param WP_Block             $block       Block instance.
 * @return string|null
 */
function yev_block_binding_value( array $source_args, $block ): ?string {
	$key = isset( $source_args['key'] ) ? sanitize_key( $source_args['key'] ) : '';
	if ( ! $key ) {
		return null;
	}

	$post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
	if ( ! $post_id ) {
		return null;
	}

	$value = yev_course_value( $post_id, $key, ! isset( $source_args['fallback'] ) );
	if ( '' === $value && isset( $source_args['fallback'] ) ) {
		$value = (string) $source_args['fallback'];
	}

	// Rich text is passed through wp_kses_post() by core; HTML attributes
	// (the button URL) are escaped by the HTML API, hence esc_url_raw().
	return 'event_cta_url' === $key ? esc_url_raw( $value ) : esc_html( $value );
}

/**
 * Removes blocks marked "yev-requires-<key>" when the field is empty.
 *
 * @param string               $content Rendered block.
 * @param array<string, mixed> $block   Parsed block.
 * @return string
 */
function yev_conditional_blocks( string $content, array $block ): string {
	$class = $block['attrs']['className'] ?? '';

	if ( ! is_string( $class ) || ! str_contains( $class, 'yev-requires-' ) ) {
		return $content;
	}

	$post_id = (int) get_the_ID();
	if ( ! $post_id || ! preg_match_all( '/\byev-requires-([a-z_]+)/', $class, $matches ) ) {
		return $content;
	}

	foreach ( $matches[1] as $key ) {
		if ( '' === yev_course_value( $post_id, $key, false ) ) {
			return '';
		}
	}

	return $content;
}
add_filter( 'render_block', 'yev_conditional_blocks', 10, 2 );
