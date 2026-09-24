<?php
/**
 * Title: L'association (image et texte)
 * Slug: yoga-et-vie/association
 * Categories: yoga-et-vie-sections, about
 * Keywords: association, communauté, adhérer, bénévoles
 * Description: Illustration à gauche, présentation de l'association et bouton à droite.
 * Viewport Width: 1400
 *
 * CONTENU DE DÉMONSTRATION — à valider par l'association. Illustration provisoire
 * à remplacer par une photo de groupe (avec l'accord des personnes photographiées).
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded-soft"} -->
<figure class="wp-block-image size-full is-style-rounded-soft"><img src="<?php echo esc_url( yev_image_url( 'illustration-cercle.svg' ) ); ?>" alt="" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">L’association</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Une association à taille humaine</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Yoga et Vie fonctionne grâce à l’engagement de ses bénévoles et à la confiance de ses adhérents. Adhérer, c’est rejoindre un groupe où l’on se connaît, où l’on prend des nouvelles les uns des autres, et où le yoga reste avant tout un plaisir partagé.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-leaf"} -->
<ul class="wp-block-list is-style-leaf"><!-- wp:list-item -->
<li>Des cours en petits groupes</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Une vie associative ouverte à tous</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Des moments conviviaux tout au long de la saison</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/association/">Découvrir l’association</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
