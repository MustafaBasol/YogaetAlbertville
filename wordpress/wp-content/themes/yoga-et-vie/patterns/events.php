<?php
/**
 * Title: Derniers événements et actualités (automatique)
 * Slug: yoga-et-vie/events
 * Categories: yoga-et-vie-sections
 * Keywords: événements, actualités, stages, ateliers, articles
 * Description: Les trois derniers articles (événements et actualités), mis à jour automatiquement.
 * Viewport Width: 1400
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"sand","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">À venir</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Événements &amp; actualités</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p><a href="/evenements/">Tous les événements →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":23,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-query alignwide" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","minimumColumnWidth":"18rem"}} -->
<!-- wp:group {"className":"is-style-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group is-style-card"><!-- wp:post-featured-image {"aspectRatio":"3/2","sizeSlug":"medium_large"} /-->

<!-- wp:post-terms {"term":"category","className":"yev-terms"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"event_when"}}}},"className":"yev-badge yev-requires-event"} -->
<p class="yev-badge yev-requires-event"></p>
<!-- /wp:paragraph -->

<!-- wp:post-excerpt {"textColor":"taupe","fontSize":"small"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucun événement annoncé pour le moment. Revenez bientôt !</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
