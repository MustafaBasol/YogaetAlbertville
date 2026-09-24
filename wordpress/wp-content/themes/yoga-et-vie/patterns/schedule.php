<?php
/**
 * Title: Planning de la semaine (automatique)
 * Slug: yoga-et-vie/schedule
 * Categories: yoga-et-vie-sections
 * Keywords: planning, horaires, emploi du temps, semaine, cours
 * Description: Liste des cours triée par jour et horaire, générée automatiquement à partir du menu « Cours & planning ».
 * Viewport Width: 1400
 *
 * Aucune donnée n'est saisie ici : le planning se met à jour tout seul quand
 * on modifie un cours dans « Cours & planning ».
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained","contentSize":"40rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Planning</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Les cours de la semaine</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"yev-note"} -->
<p class="yev-note">Planning 2026–2027 en cours de finalisation : les jours et horaires définitifs seront publiés prochainement.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":21,"query":{"perPage":30,"pages":0,"offset":0,"postType":"yev_cours","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide","className":"is-style-schedule","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"default"}} -->
<div class="wp-block-query alignwide is-style-schedule" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:post-template -->
<!-- wp:group {"className":"yev-schedule-row","layout":{"type":"default"}} -->
<div class="wp-block-group yev-schedule-row"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"when"}}}},"className":"yev-schedule-row__when"} -->
<p class="yev-schedule-row__when"></p>
<!-- /wp:paragraph -->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"level"}}}},"className":"yev-schedule-row__meta"} -->
<p class="yev-schedule-row__meta"></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"yoga-et-vie/field","args":{"key":"place"}}}},"className":"yev-schedule-row__meta"} -->
<p class="yev-schedule-row__meta"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Le planning de la saison sera publié ici très prochainement.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->

<!-- wp:buttons {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons alignwide" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/infos-pratiques/">Tarifs et inscriptions</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/contact/">Poser une question</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
