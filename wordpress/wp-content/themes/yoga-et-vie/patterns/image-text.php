<?php
/**
 * Title: Image et texte
 * Slug: yoga-et-vie/image-text
 * Categories: yoga-et-vie-sections, text
 * Keywords: image, texte, photo, présentation, colonnes
 * Description: Un texte avec titre à gauche et une image à droite (inversable avec les flèches du bloc Colonnes).
 * Viewport Width: 1400
 *
 * CONTENU DE DÉMONSTRATION — à valider par l'association.
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Découvrir</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Qu’est-ce que le yoga ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Né en Inde il y a plusieurs millénaires, le yoga associe des postures, un travail sur la respiration et des temps de relaxation ou de méditation. Plus qu’une gymnastique, c’est une manière d’être attentif à soi : à son corps, à son souffle, à ce que l’on ressent.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Il existe de nombreuses approches. Dans nos cours, nous privilégions une pratique progressive et adaptée, où chacun respecte ses possibilités du moment.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded-soft"} -->
<figure class="wp-block-image size-full is-style-rounded-soft"><img src="<?php echo esc_url( yev_image_url( 'illustration-souffle.svg' ) ); ?>" alt="" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
