<?php
/**
 * Accessible contact form: shortcode [yev_contact_form].
 *
 * - No third-party service, no cookie, no tracking.
 * - Spam protection: honeypot field + minimum fill time (signed).
 * - Messages are NOT stored in the database; they are e-mailed with
 *   wp_mail() once "Envoi des messages" is enabled in Réglages > Yoga et
 *   Vie. Until then the form runs in demonstration mode.
 * - Production prerequisite: a working mail setup (SMTP plugin using the
 *   association's mailbox). Credentials never belong in this repository.
 *
 * @package YogaEtVieCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Subjects offered in the form.
 *
 * @return array<string, string>
 */
function yev_contact_subjects(): array {
	return array(
		'cours'       => __( 'Renseignements sur les cours', 'yoga-et-vie-core' ),
		'inscription' => __( 'Inscription / adhésion', 'yoga-et-vie-core' ),
		'evenement'   => __( 'Stage ou événement', 'yoga-et-vie-core' ),
		'autre'       => __( 'Autre demande', 'yoga-et-vie-core' ),
	);
}

/**
 * Renders the form.
 *
 * @return string
 */
function yev_contact_form_shortcode(): string {
	wp_enqueue_style( 'yev-contact-form', YEV_CORE_URL . 'assets/contact-form.css', array(), YEV_CORE_VERSION );

	// Read-only display state coming from our own redirect.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$status = isset( $_GET['yev_contact'] ) ? sanitize_key( wp_unslash( $_GET['yev_contact'] ) ) : '';
	$token  = isset( $_GET['yev_token'] ) ? sanitize_key( wp_unslash( $_GET['yev_token'] ) ) : '';
	// phpcs:enable

	$saved  = $token ? get_transient( 'yev_contact_' . $token ) : false;
	$values = is_array( $saved ) ? $saved['values'] : array();
	$errors = is_array( $saved ) ? $saved['errors'] : array();
	$value  = static fn( string $key ) => (string) ( $values[ $key ] ?? '' );

	$messages = array(
		'sent'    => array( 'success', __( 'Merci ! Votre message a bien été envoyé. Nous vous répondrons dès que possible.', 'yoga-et-vie-core' ) ),
		'demo'    => array( 'info', __( 'Mode démonstration : le formulaire fonctionne, mais l’envoi des messages n’est pas encore activé. Votre message n’a pas été envoyé ni enregistré.', 'yoga-et-vie-core' ) ),
		'invalid' => array( 'error', __( 'Le formulaire contient des erreurs. Merci de corriger les champs signalés ci-dessous.', 'yoga-et-vie-core' ) ),
		'error'   => array( 'error', __( 'Désolé, le message n’a pas pu être envoyé. Merci de réessayer plus tard ou de nous contacter par un autre moyen.', 'yoga-et-vie-core' ) ),
	);

	$now        = time();
	$time_token = $now . '|' . wp_hash( 'yev_contact_' . $now );

	ob_start();
	?>
	<div class="yev-contact" id="formulaire-contact">
		<?php if ( isset( $messages[ $status ] ) ) : ?>
			<div class="yev-contact__notice is-<?php echo esc_attr( $messages[ $status ][0] ); ?>" role="<?php echo 'error' === $messages[ $status ][0] ? 'alert' : 'status'; ?>" tabindex="-1">
				<p><?php echo esc_html( $messages[ $status ][1] ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( ! yev_setting( 'contact_enabled' ) && 'demo' !== $status ) : ?>
			<p class="yev-contact__demo"><?php esc_html_e( 'Formulaire en mode démonstration : les messages ne sont pas encore envoyés.', 'yoga-et-vie-core' ); ?></p>
		<?php endif; ?>

		<form class="yev-contact__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
			<input type="hidden" name="action" value="yev_contact">
			<input type="hidden" name="yev_time" value="<?php echo esc_attr( $time_token ); ?>">
			<input type="hidden" name="yev_return" value="<?php echo esc_url( get_permalink() ); ?>">

			<p class="yev-contact__required-note"><?php esc_html_e( 'Les champs marqués d’un astérisque (*) sont obligatoires.', 'yoga-et-vie-core' ); ?></p>

			<div class="yev-contact__row">
				<?php
				yev_contact_input( 'yev_name', __( 'Nom et prénom', 'yoga-et-vie-core' ), 'text', $value( 'yev_name' ), $errors, true, 'name' );
				yev_contact_input( 'yev_email', __( 'Adresse e-mail', 'yoga-et-vie-core' ), 'email', $value( 'yev_email' ), $errors, true, 'email' );
				?>
			</div>
			<div class="yev-contact__row">
				<?php yev_contact_input( 'yev_phone', __( 'Téléphone (facultatif)', 'yoga-et-vie-core' ), 'tel', $value( 'yev_phone' ), $errors, false, 'tel' ); ?>
				<div class="yev-contact__field">
					<label for="yev_subject"><?php esc_html_e( 'Objet', 'yoga-et-vie-core' ); ?></label>
					<select id="yev_subject" name="yev_subject">
						<?php foreach ( yev_contact_subjects() as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value( 'yev_subject' ), $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="yev-contact__field<?php echo isset( $errors['yev_message'] ) ? ' has-error' : ''; ?>">
				<label for="yev_message"><?php esc_html_e( 'Votre message', 'yoga-et-vie-core' ); ?> <span class="yev-contact__star" aria-hidden="true">*</span></label>
				<textarea id="yev_message" name="yev_message" rows="6" required aria-required="true"<?php yev_contact_error_attrs( 'yev_message', $errors ); ?>><?php echo esc_textarea( $value( 'yev_message' ) ); ?></textarea>
				<?php yev_contact_error( 'yev_message', $errors ); ?>
			</div>

			<div class="yev-contact__hp" aria-hidden="true">
				<label for="yev_website"><?php esc_html_e( 'Ne pas remplir ce champ', 'yoga-et-vie-core' ); ?></label>
				<input type="text" id="yev_website" name="yev_website" tabindex="-1" autocomplete="off">
			</div>

			<div class="yev-contact__field yev-contact__consent<?php echo isset( $errors['yev_consent'] ) ? ' has-error' : ''; ?>">
				<input type="checkbox" id="yev_consent" name="yev_consent" value="1" required aria-required="true"<?php yev_contact_error_attrs( 'yev_consent', $errors ); ?><?php checked( '1', $value( 'yev_consent' ) ); ?>>
				<label for="yev_consent">
					<?php
					$privacy_url = get_privacy_policy_url();
					echo esc_html__( 'J’accepte que les informations saisies soient utilisées uniquement pour répondre à ma demande.', 'yoga-et-vie-core' );
					if ( $privacy_url ) {
						printf( ' <a href="%s">%s</a>', esc_url( $privacy_url ), esc_html__( 'Politique de confidentialité', 'yoga-et-vie-core' ) );
					}
					?>
					<span class="yev-contact__star" aria-hidden="true">*</span>
				</label>
				<?php yev_contact_error( 'yev_consent', $errors ); ?>
			</div>

			<p class="yev-contact__submit">
				<button type="submit" class="wp-element-button"><?php esc_html_e( 'Envoyer le message', 'yoga-et-vie-core' ); ?></button>
			</p>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'yev_contact_form', 'yev_contact_form_shortcode' );

/**
 * Prints a labelled input.
 *
 * @param string               $name         Field name / id.
 * @param string               $label        Label.
 * @param string               $type         Input type.
 * @param string               $value        Current value.
 * @param array<string,string> $errors       Errors.
 * @param bool                 $required     Required.
 * @param string               $autocomplete Autocomplete token.
 */
function yev_contact_input( string $name, string $label, string $type, string $value, array $errors, bool $required, string $autocomplete ): void {
	printf( '<div class="yev-contact__field%s">', isset( $errors[ $name ] ) ? ' has-error' : '' );
	printf(
		'<label for="%1$s">%2$s%3$s</label>',
		esc_attr( $name ),
		esc_html( $label ),
		$required ? ' <span class="yev-contact__star" aria-hidden="true">*</span>' : ''
	);
	printf(
		'<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" autocomplete="%4$s"%5$s',
		esc_attr( $type ),
		esc_attr( $name ),
		esc_attr( $value ),
		esc_attr( $autocomplete ),
		$required ? ' required aria-required="true"' : ''
	);
	yev_contact_error_attrs( $name, $errors );
	echo '>';
	yev_contact_error( $name, $errors );
	echo '</div>';
}

/**
 * Prints aria-invalid / aria-describedby when a field has an error.
 *
 * @param string               $name   Field name.
 * @param array<string,string> $errors Errors.
 */
function yev_contact_error_attrs( string $name, array $errors ): void {
	if ( isset( $errors[ $name ] ) ) {
		printf( ' aria-invalid="true" aria-describedby="%s-error"', esc_attr( $name ) );
	}
}

/**
 * Prints the error message of a field.
 *
 * @param string               $name   Field name.
 * @param array<string,string> $errors Errors.
 */
function yev_contact_error( string $name, array $errors ): void {
	if ( isset( $errors[ $name ] ) ) {
		printf( '<p class="yev-contact__error" id="%s-error">%s</p>', esc_attr( $name ), esc_html( $errors[ $name ] ) );
	}
}

/**
 * Handles a submission (logged-in or not).
 */
function yev_contact_handle(): void {
	// Public contact form: protected by honeypot + signed timing instead of
	// a nonce, which would break behind a page cache.
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$return = isset( $_POST['yev_return'] ) ? esc_url_raw( wp_unslash( $_POST['yev_return'] ) ) : home_url( '/' );
	$return = wp_validate_redirect( $return, home_url( '/' ) );

	$redirect = static function ( string $status, string $token = '' ) use ( $return ) {
		$args = array( 'yev_contact' => $status );
		if ( $token ) {
			$args['yev_token'] = $token;
		}
		wp_safe_redirect( add_query_arg( $args, $return ) . '#formulaire-contact' );
		exit;
	};

	// Honeypot filled or submitted too fast (< 3 s): silently pretend success.
	$time_token      = isset( $_POST['yev_time'] ) ? sanitize_text_field( wp_unslash( $_POST['yev_time'] ) ) : '';
	[ $time, $hash ] = array_pad( explode( '|', $time_token, 2 ), 2, '' );
	$valid_time      = hash_equals( wp_hash( 'yev_contact_' . $time ), $hash ) && ( time() - (int) $time ) >= 3;

	if ( ! empty( $_POST['yev_website'] ) || ! $valid_time ) {
		$redirect( yev_setting( 'contact_enabled' ) ? 'sent' : 'demo' );
	}

	$values = array(
		'yev_name'    => isset( $_POST['yev_name'] ) ? sanitize_text_field( wp_unslash( $_POST['yev_name'] ) ) : '',
		'yev_email'   => isset( $_POST['yev_email'] ) ? sanitize_text_field( wp_unslash( $_POST['yev_email'] ) ) : '', // Validated with is_email() below; kept as typed so it can be corrected.
		'yev_phone'   => isset( $_POST['yev_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['yev_phone'] ) ) : '',
		'yev_subject' => isset( $_POST['yev_subject'] ) ? sanitize_key( wp_unslash( $_POST['yev_subject'] ) ) : '',
		'yev_message' => isset( $_POST['yev_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['yev_message'] ) ) : '',
		'yev_consent' => empty( $_POST['yev_consent'] ) ? '' : '1',
	);
	// phpcs:enable

	$errors = array();
	if ( '' === $values['yev_name'] ) {
		$errors['yev_name'] = __( 'Merci d’indiquer votre nom.', 'yoga-et-vie-core' );
	}
	if ( ! is_email( $values['yev_email'] ) ) {
		$errors['yev_email'] = __( 'Merci d’indiquer une adresse e-mail valide, par exemple nom@exemple.fr.', 'yoga-et-vie-core' );
	}
	if ( mb_strlen( $values['yev_message'] ) < 10 ) {
		$errors['yev_message'] = __( 'Merci d’écrire votre message (10 caractères minimum).', 'yoga-et-vie-core' );
	}
	if ( '1' !== $values['yev_consent'] ) {
		$errors['yev_consent'] = __( 'Merci de cocher cette case pour que nous puissions vous répondre.', 'yoga-et-vie-core' );
	}
	if ( ! array_key_exists( $values['yev_subject'], yev_contact_subjects() ) ) {
		$values['yev_subject'] = 'autre';
	}

	if ( $errors ) {
		$token = strtolower( wp_generate_password( 20, false ) );
		set_transient( 'yev_contact_' . $token, compact( 'values', 'errors' ), 10 * MINUTE_IN_SECONDS );
		$redirect( 'invalid', $token );
	}

	if ( ! yev_setting( 'contact_enabled' ) ) {
		$redirect( 'demo' );
	}

	$values['yev_email'] = sanitize_email( $values['yev_email'] );

	$to      = yev_setting( 'contact_email' ) ? yev_setting( 'contact_email' ) : get_option( 'admin_email' );
	$subject = sprintf( '[%s] %s — %s', get_bloginfo( 'name' ), yev_contact_subjects()[ $values['yev_subject'] ], $values['yev_name'] );
	$body    = implode(
		"\n",
		array(
			__( 'Nom :', 'yoga-et-vie-core' ) . ' ' . $values['yev_name'],
			__( 'E-mail :', 'yoga-et-vie-core' ) . ' ' . $values['yev_email'],
			__( 'Téléphone :', 'yoga-et-vie-core' ) . ' ' . ( $values['yev_phone'] ? $values['yev_phone'] : '—' ),
			__( 'Objet :', 'yoga-et-vie-core' ) . ' ' . yev_contact_subjects()[ $values['yev_subject'] ],
			'',
			$values['yev_message'],
			'',
			'—',
			__( 'Message envoyé depuis le formulaire de contact du site.', 'yoga-et-vie-core' ),
		)
	);
	$headers = array( 'Reply-To: ' . $values['yev_name'] . ' <' . $values['yev_email'] . '>' );

	$redirect( wp_mail( $to, $subject, $body, $headers ) ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_yev_contact', 'yev_contact_handle' );
add_action( 'admin_post_yev_contact', 'yev_contact_handle' );
