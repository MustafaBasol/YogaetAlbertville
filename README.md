# Yoga et Vie Albertville — nouveau site WordPress

Remplacement du site actuel <https://www.yogaetviealbertville.fr/> (Joomla 2.5, hébergé chez OVHcloud) par un site **WordPress auto‑hébergé** moderne, sobre et facile à mettre à jour par l'association.

> **État : prototype / maquette fonctionnelle (round 1).** Les textes, images et informations pratiques sont **provisoires**. Rien n'est déployé ; le site Joomla en production, l'hébergement OVH et le DNS ne sont pas touchés.

---

## Sommaire

1. [Architecture](#architecture)
2. [Logiciels requis](#logiciels-requis)
3. [Démarrage rapide](#démarrage-rapide)
4. [Flux de développement et contrôles qualité](#flux-de-développement-et-contrôles-qualité)
5. [Structure du thème](#structure-du-thème)
6. [Expérience d'édition (administrateurs de l'association)](#expérience-dédition)
7. [Contenu de démonstration](#contenu-de-démonstration)
8. [Ce qu'il reste à obtenir du client](#ce-quil-reste-à-obtenir-du-client)
9. [Mise en production chez OVH (plus tard)](#mise-en-production-chez-ovh-plus-tard)
10. [Sécurité](#sécurité)

---

## Architecture

| Élément | Choix |
| --- | --- |
| CMS | WordPress auto‑hébergé (testé avec 7.1.2), PHP 8.2+ |
| Thème | `yoga-et-vie` — **block theme** (édition complète du site) : `theme.json`, modèles, parties, compositions (patterns). Aucun constructeur de pages, aucun thème ni extension payants. |
| Extension | `yoga-et-vie-core` — contenus structurés indépendants du thème : type de contenu **Cours**, champs « Événement » sur les articles, source *Block Bindings*, formulaire de contact, réglages. |
| JavaScript | Aucun JS propre au thème. Le menu mobile est le bloc Navigation natif. |
| Polices | Lora (titres) et Figtree (texte), licence SIL OFL, **hébergées localement** (≈ 100 Ko au total, sous‑ensemble latin incluant les caractères français). |
| Traceurs / cookies | Aucun. Pas de Google Fonts, Analytics, Maps, pixel… |

Le dépôt ne contient **que notre code** (thème, extension, outillage, documentation). WordPress lui‑même, la base de données et les médias ne sont pas versionnés.

```
.
├── README.md
├── docs/                         Direction artistique, checklists client & SEO, guide d'édition
├── tools/
│   ├── local-preview.sh          Aperçu local sans Docker (WordPress + SQLite + serveur PHP)
│   ├── seed-demo-content.php     Création du contenu de démonstration (WP-CLI)
│   ├── remove-demo-content.php   Suppression du contenu de démonstration (WP-CLI)
│   ├── validate-theme.php        Validation statique du thème (CI)
│   ├── router.php                Routeur du serveur PHP intégré (aperçu local)
│   ├── demo-content/images/      Illustrations générées pour la démo (libres de droits)
│   └── qa/                       Captures multi-écrans, axe-core, validation des blocs dans l'éditeur
├── wordpress/wp-content/
│   ├── themes/yoga-et-vie/       Le thème
│   └── plugins/yoga-et-vie-core/ L'extension « Yoga et Vie — fonctionnalités »
├── .wp-env.json                  Environnement Docker officiel (@wordpress/env)
├── composer.json / phpcs.xml.dist  PHPCS + WordPress Coding Standards + PHPCompatibility
├── package.json / .stylelintrc.json  Stylelint
└── .github/workflows/ci.yml      CI légère
```

### Pourquoi une extension en plus du thème ?

Les cours et les champs d'événement sont des **contenus** : s'ils vivaient dans le thème, un futur changement de thème les ferait disparaître. L'extension est volontairement petite (un type de contenu, quelques champs, un formulaire).

### Modèle de contenu

- **Pages** (Accueil, Le yoga, Les cours, L'association, Infos pratiques, Contact, mentions légales, confidentialité) : composées de sections (patterns) modifiables directement dans l'éditeur.
- **Cours** (menu « Cours & planning ») : une fiche = un créneau hebdomadaire. Champs : jour, début, fin, niveau/public, enseignant·e, lieu, info pratique. Le **planning** et les **cartes de cours** sont générés automatiquement (bloc Boucle de requête) et triés par jour puis heure. Un champ vide s'affiche « à confirmer ».
- **Événements & actualités** : des **articles** WordPress classiques. Catégorie « Événements » + encadré facultatif (date, horaire, lieu, bouton d'inscription). Pas de système d'événements complexe.
- **Contact** : formulaire accessible (code court `[yev_contact_form]`), en **mode démonstration** tant que l'envoi n'est pas activé dans *Réglages › Yoga et Vie*.

Les champs structurés s'affichent grâce aux **Block Bindings** (API native de WordPress) : de simples blocs Paragraphe/Bouton liés à la source `yoga-et-vie/field`, sans bloc personnalisé ni build JavaScript.

## Logiciels requis

- **Aperçu local sans Docker** : PHP 8.2+ (extensions `pdo_sqlite`, `gd`), Composer 2.
- **Environnement Docker (optionnel)** : Docker + Node.js 20+ (`@wordpress/env`).
- **Contrôles qualité** : PHP 8.2+, Composer, Node.js 20+.

## Démarrage rapide

### Option A — sans Docker (le plus simple)

```bash
./tools/local-preview.sh          # 1er lancement : télécharge WordPress, installe, crée la démo
# → http://localhost:8888  (admin : http://localhost:8888/wp-admin — admin / admin)
./tools/local-preview.sh --reset  # repartir d'un site vierge
PORT=8890 ./tools/local-preview.sh
```

Le script crée `.local-wp/` (ignoré par git), télécharge WordPress, WP-CLI et un connecteur SQLite via Composer, **lie par lien symbolique** le thème et l'extension du dépôt (toute modification est visible immédiatement), installe le site et lance le contenu de démonstration.
⚠️ SQLite et le serveur PHP intégré servent uniquement au développement.

Pour une administration en français (nécessite un accès à wordpress.org) :

```bash
.local-wp/tools/vendor/bin/wp --path=.local-wp/public language core install fr_FR --activate
```

### Option B — Docker avec `@wordpress/env`

```bash
npm run env:start     # http://localhost:8888 (admin / password)
npm run env:seed      # contenu de démonstration
npm run env:stop
```

## Flux de développement et contrôles qualité

```bash
composer install && npm install

composer lint:php        # syntaxe PHP
composer lint:phpcs      # WordPress Coding Standards + compatibilité PHP 8.2
composer validate:theme  # JSON, en-têtes des patterns, balisage des blocs, références
npm run lint:css         # Stylelint

# Avec un site local lancé (option A ou B) :
npm install --no-save playwright axe-core
npm run qa:site          # captures 375/768/1024/1440 px, débordement horizontal, titres, axe-core WCAG 2.2 AA
npm run qa:blocks        # ouvre l'éditeur WordPress et vérifie qu'aucun bloc n'est « invalide »
```

La CI GitHub Actions (`.github/workflows/ci.yml`) exécute les contrôles PHP, le validateur de thème, Stylelint et un garde-fou contre les fichiers sensibles.

Conventions : préfixe `yev_` (fonctions, options, champs), text domains `yoga-et-vie` / `yoga-et-vie-core`, indentation par tabulations (WordPress).

## Structure du thème

```
themes/yoga-et-vie/
├── style.css             En-tête du thème uniquement
├── theme.json            Jetons de design : couleurs, typographie fluide, espacements, largeurs, rayons
├── functions.php         Charge les fichiers de inc/
├── inc/
│   ├── setup.php         Supports du thème, extraits de pages, emojis désactivés
│   ├── assets.php        CSS global + une feuille par bloc (chargée seulement si le bloc est présent), préchargement des polices
│   ├── block-styles.php  Styles de blocs nécessitant du CSS (Montagnes, Puces douces, Bord montagnes, Planning)
│   ├── patterns.php      Catégories de patterns, masque les patterns génériques de WordPress
│   └── seo.php           Meta description + Open Graph (désactivé automatiquement si une extension SEO est active)
├── styles/blocks/*.json  Styles de blocs déclaratifs : Surtitre, Chapô, Carte, Arche, Arrondie
├── templates/            index, home (événements), archive, search, 404, page, page-no-title, page-legal, single, single-yev_cours
├── parts/                header, footer
├── patterns/             27 compositions (sections + pages complètes + contenus types)
└── assets/
    ├── css/global.css, css/blocks/core-*.css
    ├── fonts/            woff2 + licences OFL
    └── images/           Illustrations SVG originales (provisoires) + logo provisoire
```

## Expérience d'édition

Voir **[docs/guide-edition.md](docs/guide-edition.md)** (en français, à transmettre à l'association). En résumé :

- chaque page se modifie **comme un document** dans l'éditeur de blocs : on clique sur un texte, on le remplace ;
- pour ajouter une section : bouton **+** › *Compositions* › catégories « Yoga et Vie » ;
- une nouvelle page propose d'emblée les modèles de pages complètes ;
- les cours se gèrent dans **Cours & planning** : le planning se met à jour tout seul ;
- un événement = un **article** dans la catégorie « Événements », avec l'encadré « Événement » à droite ;
- palette de couleurs et tailles de texte limitées aux valeurs de la charte pour garder un site cohérent ;
- aucun HTML à écrire.

## Contenu de démonstration

Tout ce qui est visible est **temporaire** :

- textes de pages rédigés en français crédible mais **non validés** ;
- 5 cours d'exemple (jours, horaires, lieux, enseignant·e·s **laissés vides → « à confirmer »**) ;
- 4 articles d'exemple (dates « à confirmer ») ;
- illustrations SVG originales et logo provisoire (pictogramme montagnes + soleil) ;
- coordonnées, tarifs, adresses, mentions légales : **emplacements « à confirmer / [à compléter] »** — aucune donnée factuelle inventée ;
- bandeau « Site en préparation » en haut du site (désactivable dans *Réglages › Yoga et Vie*).

Dans le code, les patterns concernés portent la mention `CONTENU DE DÉMONSTRATION` dans leur en-tête ; les contenus créés par le script portent la méta `_yev_demo` et se suppriment avec `tools/remove-demo-content.php`.

## Ce qu'il reste à obtenir du client

Voir **[docs/client-content-checklist.md](docs/client-content-checklist.md)** (en français, prêt à envoyer). Principales attentes : logo, textes, liste des cours et horaires, enseignant·e·s, tarifs et inscription, lieux, coordonnées, informations légales (RNA, siège, directeur·rice de publication), photos avec droits, liste des pages importantes de l'ancien site.

## Mise en production chez OVH (plus tard)

Rien n'est à faire maintenant. Démarche envisagée :

1. **Nouvel environnement propre** (hébergement web OVH récent ou nouvel espace), PHP 8.2+/8.3, MySQL/MariaDB, HTTPS — **sans réutiliser** l'installation Joomla ni ses fichiers.
2. Installer WordPress (dernière version stable, en français), déposer `themes/yoga-et-vie` et `plugins/yoga-et-vie-core` (SFTP ou déploiement Git), activer.
3. Saisir les contenus définitifs (ou importer une préproduction validée), supprimer la démo.
4. Préproduction protégée (mot de passe / `noindex`) pour la validation par l'association.
5. Extensions minimales conseillées : SMTP (envoi fiable du formulaire, identifiants stockés côté serveur, jamais dans ce dépôt), sauvegardes, éventuellement une extension SEO légère et un cache.
6. Mise en ligne selon **[docs/migration-seo-checklist.md](docs/migration-seo-checklist.md)** : redirections 301, sitemap, Search Console, puis bascule DNS.
7. Après validation : archivage hors ligne puis suppression de l'ancien Joomla.

## Sécurité

- Construction **propre** : aucun fichier, extension, configuration ou identifiant Joomla n'est réutilisé.
- `.gitignore` bloque configurations (`wp-config.php`, `configuration.php`, `.env`), dumps, sauvegardes, archives et clés ; la CI refuse ces fichiers.
- Formulaire : champs assainis, honeypot + délai minimal signé, aucun stockage en base, adresse de redirection validée.
- Champs de l'administration : nonce, capacités vérifiées, assainissement par type.
- Aucune dépendance tierce chargée côté visiteur.
