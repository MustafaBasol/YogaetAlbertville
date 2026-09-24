<?php
/**
 * Title: Bandeau d'accueil
 * Slug: yoga-et-vie/hero
 * Categories: yoga-et-vie-sections, banner
 * Keywords: accueil, hero, bandeau, titre, introduction
 * Description: Grand titre, courte introduction, deux boutons et une illustration en arche.
 * Viewport Width: 1400
 *
 * CONTENU DE DÉMONSTRATION — textes à valider par l'association.
 * L'illustration est provisoire : remplacez-la par une photo (format portrait).
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","className":"yev-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}},"backgroundColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull yev-hero has-cream-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"56%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:56%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Association de yoga à Albertville</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"huge"} -->
<h1 class="wp-block-heading has-huge-font-size">Le yoga, <em>simplement</em>.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead">Des cours accessibles et bienveillants pour prendre soin de son corps et de son souffle, à son rythme — quels que soient son âge et son expérience.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/les-cours/">Découvrir les cours</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/contact/">Nous contacter</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:paragraph {"className":"yev-note"} -->
<p class="yev-note">Saison 2026–2027 · Horaires et inscriptions à confirmer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"44%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:44%"><!-- wp:image {"aspectRatio":"4/5","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-arch"} -->
<figure class="wp-block-image size-full is-style-arch"><img src="<?php echo esc_url( yev_image_url( 'hero-montagnes.svg' ) ); ?>" alt="" style="aspect-ratio:4/5;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
