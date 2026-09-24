# Direction artistique — Yoga et Vie Albertville

## Concept

**« Le yoga, simplement. »** Un site calme, chaleureux et humain, à l'image d'une association locale : de l'espace, une typographie lisible, des couleurs naturelles et un clin d'œil discret aux montagnes de Savoie. Le site parle d'abord de **yoga** et de **l'association**, pas de tourisme ni de bien‑être de luxe.

À éviter volontairement : l'esthétique « spa haut de gamme », les codes de start‑up (dégradés, néons, animations), le mysticisme appuyé (mandalas, lotus dorés, chakras), le gabarit d'entreprise.

## Public

Adultes de tous âges d'Albertville et des environs, souvent débutants ou en reprise, parfois seniors ; adhérents actuels cherchant un horaire ; bénévoles de l'association qui mettent le site à jour. D'où : **texte grand et contrasté**, informations pratiques faciles à trouver, zéro jargon, rien de clinquant.

## Couleurs

| Rôle | Nom (éditeur) | Valeur | Usage |
| --- | --- | --- | --- |
| Fond principal | Crème | `#FAF6EF` | Fond du site |
| Fonds de section | Sable / Beige / Blanc | `#F2EBDF` / `#E7DCCA` / `#FFFFFF` | Alternance des sections, cartes |
| Vert doux | Sauge pâle | `#E1E7DA` | Fonds apaisés, pastilles |
| Vert décor | Sauge | `#7D9275` | Illustrations, ornements (jamais pour du texte) |
| Vert principal | Sauge profonde | `#4A5E45` | Boutons, liens |
| Vert sombre | Forêt | `#33432F` | Pied de page, survol des boutons |
| Accent | Argile / Argile profonde | `#A65A36` / `#8E4A2A` | Soleil, surtitres, soulignés, focus clavier |
| Texte | Encre / Taupe | `#2C2B27` / `#5C574E` | Texte principal / secondaire |

Le vert est dosé : il sert aux actions (boutons, liens) et à quelques fonds, le reste du site vit dans les crèmes et sables. L'argile apporte la chaleur, par petites touches.

**Contrastes vérifiés (WCAG AA)** : texte encre sur crème 13,2:1 ; taupe sur crème 6,7:1 ; sauge profonde sur crème 6,6:1 ; crème sur forêt 9,8:1 ; argile profonde sur crème 6,2:1. La sauge (3,1:1) et l'argile claire sont réservées au décor ou aux grands textes.

La palette est **verrouillée** dans l'éditeur (pas de couleur libre) pour garder un site cohérent quelles que soient les personnes qui le modifient.

## Typographie

- **Lora** (serif, titres) : élégante sans être précieuse, excellente lisibilité à l'écran, italique expressive utilisée avec parcimonie (« *simplement* »).
- **Figtree** (sans‑serif, texte) : chaleureuse, très lisible, bien dessinée pour le français (accents, ligatures, « œ »).
- Deux fichiers variables par famille, **hébergés sur le site** (aucun appel à Google Fonts), sous‑ensemble latin, `font-display: swap`, préchargement des deux fichiers principaux.
- Corps de texte 17–18 px, interligne 1,65 ; tailles fluides (`clamp`) de 375 à 1440 px ; titres équilibrés (`text-wrap: balance`).

## Formes et ornements

- **Arche** pour l'image d'accueil : forme douce qui évoque à la fois une fenêtre, un soleil levant et une crête.
- **Ligne de crêtes** : petit ornement (séparateur « Montagnes ») et silhouette de montagnes au-dessus du pied de page — seule référence alpine appuyée.
- Cartes blanches aux coins arrondis (14 px), ombre très légère ; boutons en pilule.
- Illustrations vectorielles originales (montagnes, soleil, souffle, cercle de pratiquants) en attendant de **vraies photos** de l'association, qui donneront l'authenticité finale.

## Structure des pages

- **Accueil** : bandeau (titre, introduction, 2 boutons, arche) → présentation → types de cours → bienfaits (formulation prudente) → planning automatique → l'association → événements → appel à contacter.
- **Le yoga** : qu'est-ce que le yoga, bienfaits, citation, FAQ.
- **Les cours** : planning de la semaine (liste lisible sur mobile) → fiches de cours → contact. Chaque cours a sa page avec un encadré « Informations pratiques ».
- **L'association** : histoire, valeurs, enseignant·e·s, adhésion.
- **Événements & actualités** : grille de cartes (image, catégorie, date d'événement, extrait).
- **Infos pratiques** : lieux, tarifs, inscription, matériel, FAQ.
- **Contact** : formulaire, coordonnées, lieux (lien OpenStreetMap, pas de carte intégrée).

Navigation principale : Le yoga · Les cours · L'association · Événements · Infos pratiques · **Contact** (mis en valeur). « Accueil » est accessible par le logo et figure dans le menu mobile.

## Comportement responsive

- Conception **mobile d'abord**, vérifiée à 375, 768, 1024 et 1440 px sans défilement horizontal.
- Menu compact (bouton « menu », 44 × 44 px) jusqu'à 1120 px, car le menu compte 7 entrées ; menu horizontal au-delà.
- Grilles auto‑adaptatives (`minmax`) : les cartes passent de 4 → 2 → 1 colonne sans règles spécifiques.
- Planning : une ligne par cours sur grand écran (jour/heure · cours · niveau/lieu), empilé sur mobile.
- Images avec ratio fixé (`aspect-ratio`) : pas de décalage de mise en page au chargement.

## Accessibilité (objectif WCAG 2.2 AA)

Lien d'évitement, repères (en-tête, navigation nommée, principal, pied de page), un seul `h1` par page et hiérarchie de titres continue, focus clavier très visible (contour argile 3 px), cibles tactiles ≥ 44 px, formulaire avec étiquettes, erreurs explicites reliées aux champs, FAQ en `<details>` natifs, animations désactivées si « réduire les animations » est activé, pas de carrousel.
