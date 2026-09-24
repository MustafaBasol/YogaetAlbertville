<?php
/**
 * Réglages > Yoga et Vie : a single, simple settings screen.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default option values.
 *
 * @return array<string, mixed>
 */
function yev_settings_defaults(): array {
	return array(
		'demo_notice'     => false,
		'contact_enabled' => false,
		'contact_email'   => '',
	);
}

/**
 * Reads one setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function yev_setting( string $key ) {
	$options = wp_parse_args( (array) get_option( 'yev_settings', array() ), yev_settings_defaults() );
	return $options[ $key ] ?? null;
}

/**
 * Registers the option and its fields.
 */
function yev_register_settings(): void {
	register_setting(
		'yev_settings',
		'yev_settings',
		array(
			'type'              => 'object',
			'default'           => yev_settings_defaults(),
			'sanitize_callback' => static function ( $input ) {
				$input = (array) $input;
				return array(
					'demo_notice'     => ! empty( $input['demo_notice'] ),
					'contact_enabled' => ! empty( $input['contact_enabled'] ),
					'contact_email'   => sanitize_email( $input['contact_email'] ?? '' ),
				);
			},
		)
	);

	add_settings_section( 'yev_general', __( 'Site', 'yoga-et-vie-core' ), '__return_false', 'yev-settings' );
	add_settings_section(
		'yev_contact',
		__( 'Formulaire de contact', 'yoga-et-vie-core' ),
		static function () {
			echo '<p>' . esc_html__( 'Tant que l’envoi n’est pas activé, le formulaire reste en mode démonstration : aucun message n’est envoyé ni enregistré. Avant d’activer l’envoi, vérifiez que le site sait envoyer des e-mails (extension SMTP configurée).', 'yoga-et-vie-core' ) . '</p>';
		},
		'yev-settings'
	);

	add_settings_field(
		'yev_demo_notice',
		__( 'Bandeau « contenus provisoires »', 'yoga-et-vie-core' ),
		static function () {
			printf(
				'<label><input type="checkbox" name="yev_settings[demo_notice]" value="1"%s> %s</label>',
				checked( (bool) yev_setting( 'demo_notice' ), true, false ),
				esc_html__( 'Afficher en haut du site un bandeau indiquant que les textes sont provisoires.', 'yoga-et-vie-core' )
			);
		},
		'yev-settings',
		'yev_general'
	);

	add_settings_field(
		'yev_contact_enabled',
		__( 'Envoi des messages', 'yoga-et-vie-core' ),
		static function () {
			printf(
				'<label><input type="checkbox" name="yev_settings[contact_enabled]" value="1"%s> %s</label>',
				checked( (bool) yev_setting( 'contact_enabled' ), true, false ),
				esc_html__( 'Activer l’envoi réel des messages par e-mail.', 'yoga-et-vie-core' )
			);
		},
		'yev-settings',
		'yev_contact'
	);

	add_settings_field(
		'yev_contact_email',
		__( 'Adresse de réception', 'yoga-et-vie-core' ),
		static function () {
			printf(
				'<input type="email" class="regular-text" name="yev_settings[contact_email]" value="%s" placeholder="%s"><p class="description">%s</p>',
				esc_attr( (string) yev_setting( 'contact_email' ) ),
				esc_attr( (string) get_option( 'admin_email' ) ),
				esc_html__( 'Laissez vide pour utiliser l’adresse e-mail d’administration du site.', 'yoga-et-vie-core' )
			);
		},
		'yev-settings',
		'yev_contact'
	);
}
add_action( 'admin_init', 'yev_register_settings' );

/**
 * Adds Réglages > Yoga et Vie.
 */
function yev_add_settings_page(): void {
	add_options_page(
		__( 'Réglages Yoga et Vie', 'yoga-et-vie-core' ),
		__( 'Yoga et Vie', 'yoga-et-vie-core' ),
		'manage_options',
		'yev-settings',
		static function () {
			echo '<div class="wrap"><h1>' . esc_html__( 'Réglages Yoga et Vie', 'yoga-et-vie-core' ) . '</h1><form action="options.php" method="post">';
			settings_fields( 'yev_settings' );
			do_settings_sections( 'yev-settings' );
			submit_button();
			echo '</form></div>';
		}
	);
}
add_action( 'admin_menu', 'yev_add_settings_page' );
