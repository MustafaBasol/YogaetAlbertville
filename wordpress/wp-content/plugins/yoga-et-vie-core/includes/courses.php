<?php
/**
 * "Cours" post type: one entry = one weekly class slot.
 *
 * Editors fill title + description in the block editor and the practical
 * fields (day, times, level, teacher, place) in the "Informations
 * pratiques" box of the sidebar. The planning is then generated
 * automatically, sorted by day and start time.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

const YEV_COURSE_POST_TYPE = 'yev_cours';
const YEV_COURSE_SORT_KEY  = '_yev_sort';

/**
 * Registers the post type and its meta.
 */
function yev_register_course_post_type(): void {
	register_post_type(
		YEV_COURSE_POST_TYPE,
		array(
			'labels'        => array(
				'name'               => __( 'Cours', 'yoga-et-vie-core' ),
				'singular_name'      => __( 'Cours', 'yoga-et-vie-core' ),
				'menu_name'          => __( 'Cours & planning', 'yoga-et-vie-core' ),
				'add_new'            => __( 'Ajouter un cours', 'yoga-et-vie-core' ),
				'add_new_item'       => __( 'Ajouter un cours', 'yoga-et-vie-core' ),
				'edit_item'          => __( 'Modifier le cours', 'yoga-et-vie-core' ),
				'new_item'           => __( 'Nouveau cours', 'yoga-et-vie-core' ),
				'view_item'          => __( 'Voir le cours', 'yoga-et-vie-core' ),
				'all_items'          => __( 'Tous les cours', 'yoga-et-vie-core' ),
				'search_items'       => __( 'Rechercher un cours', 'yoga-et-vie-core' ),
				'not_found'          => __( 'Aucun cours pour le moment.', 'yoga-et-vie-core' ),
				'not_found_in_trash' => __( 'Aucun cours dans la corbeille.', 'yoga-et-vie-core' ),
			),
			'description'   => __( 'Un créneau hebdomadaire de cours. Si un même cours a lieu deux fois par semaine, créez deux fiches.', 'yoga-et-vie-core' ),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-calendar-alt',
			'rewrite'       => array(
				'slug'       => 'cours',
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
			'template'      => array(
				array(
					'core/paragraph',
					array( 'placeholder' => __( 'Présentez le cours en quelques phrases : son esprit, à qui il s’adresse, ce qu’on y pratique…', 'yoga-et-vie-core' ) ),
				),
				array(
					'core/heading',
					array(
						'level'   => 2,
						'content' => __( 'Déroulement d’une séance', 'yoga-et-vie-core' ),
					),
				),
				array(
					'core/paragraph',
					array( 'placeholder' => __( 'Accueil, échauffement, postures, respiration, relaxation…', 'yoga-et-vie-core' ) ),
				),
			),
		)
	);

	yev_register_meta_fields( YEV_COURSE_POST_TYPE, yev_course_fields() );
}
add_action( 'init', 'yev_register_course_post_type' );

/**
 * Adds the "Informations pratiques" box.
 */
function yev_add_course_meta_box(): void {
	add_meta_box(
		'yev-course-details',
		__( 'Informations pratiques', 'yoga-et-vie-core' ),
		static fn( WP_Post $post ) => yev_render_fields( $post, yev_course_fields(), 'yev_course' ),
		YEV_COURSE_POST_TYPE,
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'yev_add_course_meta_box' );

/**
 * Saves the box and refreshes the sort key.
 *
 * @param int $post_id Post ID.
 */
function yev_save_course( int $post_id ): void {
	yev_save_fields( $post_id, yev_course_fields(), 'yev_course' );
	yev_update_course_sort_key( $post_id );
}
add_action( 'save_post_' . YEV_COURSE_POST_TYPE, 'yev_save_course' );

/**
 * Stores a numeric key (day index × 10000 + minutes) used to sort the
 * planning. Courses without a day go last.
 *
 * @param int $post_id Post ID.
 */
function yev_update_course_sort_key( int $post_id ): void {
	$days  = array_keys( yev_days() );
	$index = array_search( get_post_meta( $post_id, 'yev_day', true ), $days, true );
	$index = false === $index ? 9 : $index + 1;

	$start   = (string) get_post_meta( $post_id, 'yev_start', true );
	$minutes = preg_match( '/^(\d{2}):(\d{2})$/', $start, $m ) ? ( (int) $m[1] * 60 + (int) $m[2] ) : 9999;

	update_post_meta( $post_id, YEV_COURSE_SORT_KEY, (string) ( $index * 10000 + $minutes ) );
}

/**
 * Keeps the sort key in sync when meta is changed outside the meta box
 * (REST API, WP-CLI, import).
 *
 * @param int    $meta_id   Meta ID.
 * @param int    $object_id Post ID.
 * @param string $meta_key  Meta key.
 */
function yev_course_meta_changed( $meta_id, $object_id, $meta_key ): void {
	if ( in_array( $meta_key, array( 'yev_day', 'yev_start' ), true ) && YEV_COURSE_POST_TYPE === get_post_type( $object_id ) ) {
		yev_update_course_sort_key( (int) $object_id );
	}
}
add_action( 'added_post_meta', 'yev_course_meta_changed', 10, 3 );
add_action( 'updated_post_meta', 'yev_course_meta_changed', 10, 3 );

/**
 * Sorts every Query Loop block that lists courses by day, then time.
 *
 * @param array<string, mixed> $query Query vars.
 * @return array<string, mixed>
 */
function yev_sort_course_queries( array $query ): array {
	$post_type = $query['post_type'] ?? '';

	if ( YEV_COURSE_POST_TYPE === $post_type || array( YEV_COURSE_POST_TYPE ) === $post_type ) {
		$query['meta_key'] = YEV_COURSE_SORT_KEY; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Tiny table.
		$query['orderby']  = array(
			'meta_value_num' => 'ASC',
			'title'          => 'ASC',
		);
	}

	return $query;
}
add_filter( 'query_loop_block_query_vars', 'yev_sort_course_queries' );
// Same ordering for the editor preview, which queries the REST API.
add_filter( 'rest_' . YEV_COURSE_POST_TYPE . '_query', 'yev_sort_course_queries' );

/**
 * Admin list: adds Jour / Horaire / Lieu columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function yev_course_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['yev_when']  = __( 'Jour & horaire', 'yoga-et-vie-core' );
			$new['yev_place'] = __( 'Lieu', 'yoga-et-vie-core' );
		}
	}
	unset( $new['date'] );
	return $new;
}
add_filter( 'manage_' . YEV_COURSE_POST_TYPE . '_posts_columns', 'yev_course_columns' );

/**
 * Admin list column content.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function yev_course_column_content( string $column, int $post_id ): void {
	if ( 'yev_when' === $column ) {
		echo esc_html( yev_course_value( $post_id, 'when', true ) );
	} elseif ( 'yev_place' === $column ) {
		echo esc_html( yev_course_value( $post_id, 'place', true ) );
	}
}
add_action( 'manage_' . YEV_COURSE_POST_TYPE . '_posts_custom_column', 'yev_course_column_content', 10, 2 );

/**
 * Admin list sorted like the planning.
 *
 * @param WP_Query $query Query.
 */
function yev_course_admin_order( WP_Query $query ): void {
	if ( is_admin() && $query->is_main_query() && YEV_COURSE_POST_TYPE === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', YEV_COURSE_SORT_KEY ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Tiny table.
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'yev_course_admin_order' );
