<?php
/**
 * Title: Fiches des cours (automatique)
 * Slug: yoga-et-vie/courses-grid
 * Categories: yoga-et-vie-sections
 * Keywords: cours, cartes, fiches, liste
 * Description: Une carte par cours (titre, jour, horaire, résumé, niveau, lieu), générée à partir du menu « Cours & planning ».
 * Viewport Width: 1400
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"sand","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained","contentSize":"40rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">En détail</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Choisir son cours</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Chaque cours a sa propre fiche : son esprit, le public auquel il s’adresse et les informations pratiques.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":22,"query":{"perPage":30,"pages":0,"offset":0,"postType":"yev_cours","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-query alignwide" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","minimumColumnWidth":"17rem"}} -->
<!-- wp:group {"className":"is-style-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group is-style-card"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"when"}}}},"className":"yev-badge"} -->
<p class="yev-badge"></p>
<!-- /wp:paragraph -->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"textColor":"taupe","fontSize":"small"} /-->

<!-- wp:group {"className":"yev-push-bottom","style":{"spacing":{"blockGap":"0.2rem"}},"fontSize":"small","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group yev-push-bottom has-small-font-size"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"level"}}}}} -->
<p></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"place"}}}}} -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Les fiches des cours seront publiées ici très prochainement.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
