<?php
/**
 * Field definitions and formatting helpers shared by courses and events.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Days of the week, in display order. Keys are stored in post meta.
 *
 * @return array<string, string>
 */
function yev_days(): array {
	return array(
		'lundi'    => __( 'Lundi', 'yoga-et-vie-core' ),
		'mardi'    => __( 'Mardi', 'yoga-et-vie-core' ),
		'mercredi' => __( 'Mercredi', 'yoga-et-vie-core' ),
		'jeudi'    => __( 'Jeudi', 'yoga-et-vie-core' ),
		'vendredi' => __( 'Vendredi', 'yoga-et-vie-core' ),
		'samedi'   => __( 'Samedi', 'yoga-et-vie-core' ),
		'dimanche' => __( 'Dimanche', 'yoga-et-vie-core' ),
	);
}

/**
 * Course fields: meta key => settings used by the meta box and REST.
 *
 * @return array<string, array<string, string>>
 */
function yev_course_fields(): array {
	return array(
		'yev_day'     => array(
			'label' => __( 'Jour', 'yoga-et-vie-core' ),
			'type'  => 'day',
		),
		'yev_start'   => array(
			'label' => __( 'Début', 'yoga-et-vie-core' ),
			'type'  => 'time',
		),
		'yev_end'     => array(
			'label' => __( 'Fin', 'yoga-et-vie-core' ),
			'type'  => 'time',
		),
		'yev_level'   => array(
			'label'       => __( 'Niveau / public', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'ex. Tous niveaux, débutants…', 'yoga-et-vie-core' ),
		),
		'yev_teacher' => array(
			'label'       => __( 'Enseignant·e', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'Prénom Nom', 'yoga-et-vie-core' ),
		),
		'yev_place'   => array(
			'label'       => __( 'Lieu', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'Salle, adresse…', 'yoga-et-vie-core' ),
		),
		'yev_info'    => array(
			'label'       => __( 'Info pratique (facultatif)', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'ex. Tapis fournis', 'yoga-et-vie-core' ),
		),
	);
}

/**
 * Event fields for regular posts ("Articles").
 *
 * @return array<string, array<string, string>>
 */
function yev_event_fields(): array {
	return array(
		'yev_event_date'      => array(
			'label' => __( 'Date de l’événement', 'yoga-et-vie-core' ),
			'type'  => 'date',
		),
		'yev_event_time'      => array(
			'label'       => __( 'Horaire', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'ex. 14h – 17h', 'yoga-et-vie-core' ),
		),
		'yev_event_place'     => array(
			'label'       => __( 'Lieu', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'Salle, adresse…', 'yoga-et-vie-core' ),
		),
		'yev_event_cta_label' => array(
			'label'       => __( 'Texte du bouton', 'yoga-et-vie-core' ),
			'type'        => 'text',
			'placeholder' => __( 'ex. S’inscrire', 'yoga-et-vie-core' ),
		),
		'yev_event_cta_url'   => array(
			'label'       => __( 'Lien du bouton', 'yoga-et-vie-core' ),
			'type'        => 'url',
			'placeholder' => 'https://',
		),
	);
}

/**
 * Sanitizes a field value according to its type.
 *
 * @param mixed  $value Raw value.
 * @param string $type  Field type.
 * @return string
 */
function yev_sanitize_field( $value, string $type ): string {
	$value = is_scalar( $value ) ? trim( (string) $value ) : '';

	switch ( $type ) {
		case 'day':
			return array_key_exists( $value, yev_days() ) ? $value : '';
		case 'time':
			return preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $value ) ? $value : '';
		case 'date':
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
		case 'url':
			return esc_url_raw( $value );
		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Formats "18:30" as "18h30" (French convention).
 *
 * @param string $time HH:MM.
 * @return string
 */
function yev_format_time( string $time ): string {
	if ( ! preg_match( '/^(\d{2}):(\d{2})$/', $time, $m ) ) {
		return '';
	}
	$hours = (string) (int) $m[1];
	return '00' === $m[2] ? $hours . 'h' : $hours . 'h' . $m[2];
}

/**
 * Registers meta keys (REST-visible so the block editor can read them).
 *
 * @param string                               $post_type Post type.
 * @param array<string, array<string, string>> $fields    Fields.
 */
function yev_register_meta_fields( string $post_type, array $fields ): void {
	foreach ( $fields as $key => $field ) {
		register_post_meta(
			$post_type,
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => static fn( $value ) => yev_sanitize_field( $value, $field['type'] ),
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}

/**
 * Renders the fields of a meta box.
 *
 * @param WP_Post                              $post   Current post.
 * @param array<string, array<string, string>> $fields Fields.
 * @param string                               $nonce  Nonce action.
 */
function yev_render_fields( WP_Post $post, array $fields, string $nonce ): void {
	wp_nonce_field( $nonce, $nonce . '_nonce' );

	echo '<div class="yev-fields">';
	foreach ( $fields as $key => $field ) {
		$value = (string) get_post_meta( $post->ID, $key, true );
		$id    = 'yev-field-' . $key;

		echo '<p class="yev-field">';
		printf( '<label for="%s"><strong>%s</strong></label><br>', esc_attr( $id ), esc_html( $field['label'] ) );

		if ( 'day' === $field['type'] ) {
			printf( '<select id="%1$s" name="%2$s" class="widefat">', esc_attr( $id ), esc_attr( $key ) );
			printf( '<option value="">%s</option>', esc_html__( '— À confirmer —', 'yoga-et-vie-core' ) );
			foreach ( yev_days() as $day_key => $day_label ) {
				printf( '<option value="%s"%s>%s</option>', esc_attr( $day_key ), selected( $value, $day_key, false ), esc_html( $day_label ) );
			}
			echo '</select>';
		} else {
			$input_type = in_array( $field['type'], array( 'time', 'date', 'url' ), true ) ? $field['type'] : 'text';
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" class="widefat">',
				esc_attr( $input_type ),
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( $value ),
				esc_attr( $field['placeholder'] ?? '' )
			);
		}
		echo '</p>';
	}
	echo '<p class="description">' . esc_html__( 'Laissez un champ vide s’il n’est pas encore connu : le site affichera « à confirmer ».', 'yoga-et-vie-core' ) . '</p>';
	echo '</div>';
}

/**
 * Saves meta box fields.
 *
 * @param int                                  $post_id Post ID.
 * @param array<string, array<string, string>> $fields  Fields.
 * @param string                               $nonce   Nonce action.
 */
function yev_save_fields( int $post_id, array $fields, string $nonce ): void {
	$nonce_value = isset( $_POST[ $nonce . '_nonce' ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce . '_nonce' ] ) ) : '';

	if ( ! $nonce_value || ! wp_verify_nonce( $nonce_value, $nonce ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( $fields as $key => $field ) {
		// Each value is sanitized by yev_sanitize_field() below.
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta( $post_id, $key, yev_sanitize_field( $raw, $field['type'] ) );
	}
}

/**
 * Keeps the sidebar fields inside the box on narrow sidebars.
 */
function yev_admin_field_styles(): void {
	wp_add_inline_style( 'wp-admin', '.yev-fields select,.yev-fields input{max-width:100%;box-sizing:border-box}.yev-field{margin:0 0 .75rem}' );
}
add_action( 'admin_enqueue_scripts', 'yev_admin_field_styles' );
