<?php
/**
 * Block styles that need CSS beyond what theme.json can express.
 *
 * Simpler styles (Surtitre, Chapô, Carte, Arche, Arrondie) are declared as
 * JSON partials in styles/blocks/ and registered automatically.
 * The CSS for the styles below lives in the matching
 * assets/css/blocks/core-<block>.css file.
 *
 * @package YogaEtVie
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers CSS-based block styles.
 */
function yev_register_block_styles(): void {
	register_block_style(
		'core/separator',
		array(
			'name'  => 'mountains',
			'label' => __( 'Montagnes', 'yoga-et-vie' ),
		)
	);

	register_block_style(
		'core/list',
		array(
			'name'  => 'leaf',
			'label' => __( 'Puces douces', 'yoga-et-vie' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'mountain-edge',
			'label' => __( 'Bord montagnes', 'yoga-et-vie' ),
		)
	);

	register_block_style(
		'core/query',
		array(
			'name'  => 'schedule',
			'label' => __( 'Planning (liste)', 'yoga-et-vie' ),
		)
	);
}
add_action( 'init', 'yev_register_block_styles' );
