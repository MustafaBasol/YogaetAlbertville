# Migration Joomla → WordPress : checklist SEO

> À exécuter **au moment de la mise en ligne**, pas avant. Rien de ceci n'a été réalisé pour l'instant. Le site Joomla reste en ligne tant que le nouveau site n'est pas validé.

Note : pendant ce premier round, l'accès réseau au site actuel était bloqué depuis l'environnement de développement ; **l'inventaire des URL reste donc à faire** (étape 1).

## 1. Inventaire des anciennes URL

Sources à croiser (sans toucher à l'installation Joomla) :

- [ ] Exploration du site public (Screaming Frog, `wget --spider`, ou `sitemap.xml` s'il existe).
- [ ] Google Search Console : *Pages* et *Performances* (URL avec clics/impressions, 16 derniers mois).
- [ ] Backlinks connus (Search Console › Liens, annuaires locaux, mairie, office de tourisme, fédération).
- [ ] Documents PDF et images liés depuis l'extérieur.
- [ ] Formats Joomla typiques à repérer : `/index.php?option=com_content&view=article&id=…`, `/index.php/…`, `/component/…`, alias SEF (`/les-cours.html`), `?Itemid=…`, `?format=feed`.

Consigner le résultat dans le tableau ci-dessous.

## 2. Correspondance anciennes → nouvelles URL

| Ancienne URL (Joomla) | Trafic / liens | Nouvelle URL (WordPress) | Code |
| --- | --- | --- | --- |
| `/` | | `/` | — |
| *(accueil `index.php`)* | | `/` | 301 |
| *(page cours / planning)* | | `/les-cours/` | 301 |
| *(page présentation / yoga)* | | `/le-yoga/` | 301 |
| *(page association)* | | `/association/` | 301 |
| *(tarifs / inscriptions)* | | `/infos-pratiques/` | 301 |
| *(actualités / agenda)* | | `/evenements/` | 301 |
| *(article d'actualité X)* | | `/<slug-article>/` ou `/evenements/` | 301 |
| *(contact)* | | `/contact/` | 301 |
| *(mentions légales)* | | `/mentions-legales/` | 301 |
| *(PDF utiles)* | | `/wp-content/uploads/…` | 301 |
| *(contenu obsolète sans équivalent)* | | — | 410 ou 301 vers la page la plus proche |

Nouvelles URL du prototype : `/`, `/le-yoga/`, `/les-cours/`, `/cours/<cours>/`, `/association/`, `/evenements/`, `/<article>/`, `/category/evenements/`, `/infos-pratiques/`, `/contact/`, `/mentions-legales/`, `/politique-de-confidentialite/`.

## 3. Redirections 301

- [ ] Implémenter côté serveur (`.htaccess` Apache chez OVH) **avant** les règles WordPress, ou avec l'extension *Redirection* si l'association doit pouvoir les gérer.
- [ ] Gérer les URL à paramètres (`index.php?option=…&id=…`) avec `RewriteCond %{QUERY_STRING}`.
- [ ] Forcer HTTPS et une seule variante d'hôte (`www` ou non — conserver celle d'aujourd'hui : `www.yogaetviealbertville.fr`).
- [ ] Pas de chaînes de redirections (A → B → C) ; pas de redirection massive vers l'accueil.
- [ ] Tester chaque ligne du tableau (`curl -I`) : code 301 et destination correcte.

## 4. Balises canoniques

- [ ] Une seule URL canonique par page (WordPress la génère sur les contenus ; vérifier l'accueil, les archives et la pagination).
- [ ] Les canoniques pointent vers l'URL HTTPS finale, pas vers la préproduction.
- [ ] Si une extension SEO est ajoutée, le thème désactive automatiquement ses propres balises (pas de doublon).

## 5. Sitemap

- [ ] Sitemap natif WordPress : `/wp-sitemap.xml` (ou celui de l'extension SEO retenue).
- [ ] Exclure du sitemap ce qui n'a pas de valeur (pages de test, taxonomies vides, auteurs).
- [ ] Déclarer le sitemap dans `robots.txt` et dans Search Console.

## 6. robots.txt et indexation

- [ ] **Préproduction : non indexable** (*Réglages › Lecture › Demander aux moteurs de ne pas indexer* + protection par mot de passe).
- [ ] **Au lancement : décocher cette option** (erreur la plus fréquente !).
- [ ] `robots.txt` minimal, sans bloquer CSS/JS/images.

## 7. Search Console

- [ ] Vérifier la propriété (domaine, via DNS) si ce n'est pas déjà fait — récupérer l'accès existant auprès de l'association.
- [ ] Soumettre le nouveau sitemap.
- [ ] Inspecter et demander l'indexation des pages principales.
- [ ] Surveiller *Pages* (404, redirections, exclusions) pendant 4 à 8 semaines.

## 8. Contrôle des 404

- [ ] Parcourir toutes les anciennes URL de l'inventaire après bascule : aucune 404 inattendue.
- [ ] Journal des 404 (extension *Redirection* ou logs OVH) pendant les premières semaines ; ajouter les redirections manquantes.
- [ ] La page 404 du thème propose recherche et liens utiles (déjà en place).

## 9. Contenu et métadonnées

- [ ] Titre (`<title>`) et « chapô » (extrait = meta description) renseignés pour chaque page.
- [ ] Textes alternatifs de toutes les images porteuses de sens (voir le guide d'édition).
- [ ] Informations locales cohérentes partout (nom, adresse, téléphone) + fiche Google Business Profile éventuelle.
- [ ] Données structurées éventuelles (Organization / SportsActivityLocation / Event) via l'extension SEO — à décider.
- [ ] Image de partage (Open Graph) par défaut : logo définitif ou photo représentative.

## 10. Bascule

- [ ] Sauvegarde complète du Joomla (fichiers + base) **conservée hors ligne**, jamais dans ce dépôt.
- [ ] Baisser le TTL DNS quelques jours avant.
- [ ] Mise en ligne, tests (HTTPS, formulaire, redirections, sitemap, robots), puis suivi Search Console.
- [ ] Après stabilisation : désactivation puis suppression de l'ancienne installation Joomla (sécurité).
