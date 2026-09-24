<?php
/**
 * Events are regular WordPress posts ("Articles") in the "Événements"
 * category, with a few optional fields (date, time, place, button).
 * News items are posts without these fields. No separate event system.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

// Slug of the category that marks a post as an event.
const YEV_EVENT_CATEGORY = 'evenements';

/**
 * Registers event meta on posts.
 */
function yev_register_event_meta(): void {
	yev_register_meta_fields( 'post', yev_event_fields() );
}
add_action( 'init', 'yev_register_event_meta' );

/**
 * Adds the "Événement" box to posts.
 */
function yev_add_event_meta_box(): void {
	add_meta_box(
		'yev-event-details',
		__( 'Événement (facultatif)', 'yoga-et-vie-core' ),
		static function ( WP_Post $post ) {
			echo '<p class="description">' . esc_html__( 'À remplir uniquement si cet article annonce un événement (stage, atelier, assemblée générale…).', 'yoga-et-vie-core' ) . '</p>';
			yev_render_fields( $post, yev_event_fields(), 'yev_event' );
		},
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'yev_add_event_meta_box' );

/**
 * Saves the event box.
 *
 * @param int $post_id Post ID.
 */
function yev_save_event( int $post_id ): void {
	yev_save_fields( $post_id, yev_event_fields(), 'yev_event' );
}
add_action( 'save_post_post', 'yev_save_event' );
