<?php
/**
 * Title: Coordonnées et formulaire de contact
 * Slug: yoga-et-vie/contact-details
 * Categories: yoga-et-vie-sections
 * Keywords: contact, formulaire, coordonnées, email, téléphone, adresse
 * Description: Formulaire de contact accessible à gauche, coordonnées de l'association à droite.
 * Viewport Width: 1400
 *
 * Les coordonnées sont des emplacements « à confirmer » : aucune donnée réelle
 * n'est inventée. Le formulaire est fourni par l'extension « Yoga et Vie —
 * fonctionnalités » (code court [yev_contact_form]).
 *
 * @package YogaEtVie
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:heading -->
<h2 class="wp-block-heading">Écrivez-nous</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Une question sur les cours, l’inscription ou un événement ? Laissez-nous un message, un membre de l’association vous répondra.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[yev_contact_form]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:group {"className":"is-style-card","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-card"><!-- wp:heading {"level":2,"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size">Coordonnées</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"yev-detail","layout":{"type":"default"}} -->
<div class="wp-block-group yev-detail"><!-- wp:paragraph {"className":"yev-detail__label"} -->
<p class="yev-detail__label">Association</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Yoga et Vie<br>Adresse postale à confirmer<br>73200 Albertville</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"yev-detail","layout":{"type":"default"}} -->
<div class="wp-block-group yev-detail"><!-- wp:paragraph {"className":"yev-detail__label"} -->
<p class="yev-detail__label">E-mail</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>À confirmer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"yev-detail","layout":{"type":"default"}} -->
<div class="wp-block-group yev-detail"><!-- wp:paragraph {"className":"yev-detail__label"} -->
<p class="yev-detail__label">Téléphone</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>À confirmer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"yev-detail","layout":{"type":"default"}} -->
<div class="wp-block-group yev-detail"><!-- wp:paragraph {"className":"yev-detail__label"} -->
<p class="yev-detail__label">Permanences / accueil</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>À confirmer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"yev-note","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<p class="yev-note" style="margin-top:var(--wp--preset--spacing--30)">Nous répondons généralement sous quelques jours. Merci de votre patience : l’association est animée par des bénévoles.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
