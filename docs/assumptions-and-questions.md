# Hypothèses de travail et décisions en attente (round 1)

Hypothèses prises pour avancer sans bloquer la conception. Chacune est facile à revoir.

| Sujet | Hypothèse retenue | Impact si elle change |
| --- | --- | --- |
| Logo | Logo **provisoire** (pictogramme montagnes + soleil) ; le logo actuel n'a pas pu être récupéré (site inaccessible depuis l'environnement de développement). | Remplacer le logo dans *Apparence › Éditeur* (bloc Logo du site) ou via le script ; aucune modification de code. |
| Langue | Site **uniquement en français**. | Multilingue = extension dédiée (Polylang…) et traduction des contenus. |
| Inscription / paiement | Pas d'inscription ni de paiement en ligne : contact + infos pratiques. | Ajout d'un service (HelloAsso, formulaire…) : nouvelle section et mise à jour RGPD. |
| Cours | Un cours = un créneau hebdomadaire ; 5 cours d'exemple, jours/horaires/lieux « à confirmer ». | Aucun : il suffit de remplir les fiches. |
| Événements | Articles WordPress + catégorie « Événements » ; inscription éventuelle via un bouton-lien. | Billetterie ou inscriptions gérées sur le site = autre outil. |
| Enseignant·e·s | Section de cartes (photo, nom, présentation) modifiables à la main. | Si beaucoup d'enseignant·e·s ou de mises à jour : type de contenu dédié. |
| Formulaire | Formulaire intégré (extension maison), **mode démonstration**, envoi par e-mail via `wp_mail()` à activer ; pas de stockage en base. | Choix final de la solution d'envoi (SMTP OVH ou autre) avant la mise en ligne. |
| Carte | Pas de carte intégrée ; lien OpenStreetMap. | Une carte intégrée ajouterait des requêtes tierces (à mentionner dans la politique de confidentialité). |
| Mesure d'audience | Aucune. | Si souhaitée : solution sans cookie (ex. statistiques côté serveur) pour éviter une bannière de consentement. |
| Hébergement | OVH plus tard, sur un environnement **neuf** (pas de réutilisation du Joomla). | — |

## Questions à poser à l'association

1. Faut-il conserver le logo actuel, le moderniser ou en créer un nouveau ?
2. Une inscription et/ou un paiement en ligne sont-ils souhaités (maintenant ou plus tard) ?
3. Les événements (stages, ateliers) nécessitent-ils une inscription préalable ? Par quel moyen ?
4. Quelle adresse e-mail doit recevoir les messages du formulaire ?
5. Souhaitent-ils présenter les enseignant·e·s avec photo et biographie ?
6. Quelles pages ou documents de l'ancien site sont encore utilisés (liens partagés, PDF) ?
