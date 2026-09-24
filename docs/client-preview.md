# Aperçu client statique — lire avant de partager un lien

## ⚠️ Ce n'est PAS l'architecture du site

Le vrai site est et reste un **WordPress auto-hébergé**. Le code source qui compte est :

```
wordpress/wp-content/themes/yoga-et-vie
wordpress/wp-content/plugins/yoga-et-vie-core
```

Le dossier **`preview-dist/`** décrit dans ce document est un **instantané HTML statique jetable**,
généré à partir de ce WordPress de démonstration, uniquement pour permettre à un client de consulter
une maquette en ligne le temps d'une relecture. Il ne contient ni PHP, ni base de données, ni panneau
d'administration fonctionnel. **Personne ne doit le confondre avec l'architecture de production.**

Il peut être régénéré à tout moment et n'est jamais la source de vérité : toute modification de
contenu ou de design se fait dans le thème/l'extension WordPress ci-dessus, jamais dans
`preview-dist/`.

## À quoi sert cet aperçu

Publier temporairement le site de démonstration sur une URL `https://….vercel.app` pour que le client
puisse le consulter sur ordinateur et mobile, sans avoir besoin d'un accès WordPress ni d'un
hébergement définitif.

## Ce qu'il contient / ne contient pas

| | |
| --- | --- |
| ✅ Pages incluses | Accueil, Le yoga, Les cours (+ chaque fiche cours), L'association, Événements & actualités (+ chaque article), Infos pratiques, Contact, Mentions légales, Politique de confidentialité, page 404 |
| ✅ Fonctionne | Navigation interne, menu mobile (ouverture/fermeture), polices et images, mise en page responsive |
| ❌ Ne fonctionne pas | `wp-admin` (aucun panneau n'est exporté ni accessible), l'envoi réel du formulaire de contact (désactivé volontairement, voir ci-dessous), la recherche (retire une page de résultats dynamique), la création de contenu |
| 🔒 Confidentialité | `robots.txt` interdit toute exploration, chaque page porte `<meta name="robots" content="noindex, nofollow">`, en-tête `X-Robots-Tag` ajouté par `vercel.json` |

### Formulaire de contact

Le formulaire reste visible (pour juger du design), mais **il ne peut pas être envoyé** : son
attribut `action` a été neutralisé et un bandeau explicite l'indique
(« Aperçu statique : ce formulaire ne peut pas être envoyé depuis cette page de démonstration »).
Aucun message de succès n'est simulé.

## Comment régénérer l'aperçu

Prérequis : un aperçu local fonctionnel (voir le README — PHP 8.2+, Composer, `wget`).

```bash
npm run preview:export
# ou directement :
./tools/export-static-preview.sh
```

Le script :

1. démarre l'aperçu local WordPress s'il ne tourne pas déjà (`tools/local-preview.sh`) ;
2. dresse la liste des pages, articles et cours publiés (via WP-CLI) ;
3. aspire ces pages et leurs ressources (CSS, polices, images, script du menu mobile) avec `wget`
   (une seule invocation, liens convertis en relatifs) ;
4. nettoie le résultat (`tools/qa/postprocess-static-preview.mjs`) : force le `noindex`, supprime les
   liens WordPress qui n'ont plus de sens en statique (flux RSS, découverte REST, RSD/xmlrpc,
   balise « generator »), corrige les noms de fichiers CSS/JS que `wget` enregistre avec leur
   paramètre `?ver=…` (illisible pour un serveur de fichiers statique), neutralise le formulaire de
   contact ;
5. écrit `robots.txt` (`Disallow: /`) et un `vercel.json` minimal.

Résultat : `preview-dist/` (à la racine du dépôt), ~2,5 Mo, une vingtaine de pages.

Pour vérifier localement avant de déployer :

```bash
cd preview-dist && python3 -m http.server 4000
# puis ouvrir http://localhost:4000/
```

## Pourquoi `preview-dist/` est commité (exception documentée)

La règle générale de ce dépôt est de ne pas committer de contenu généré. Ici, une exception
délibérée : **Vercel ne peut pas exécuter notre chaîne de rendu** (PHP, WordPress, SQLite, serveur
local) pendant sa propre étape de build — il n'y a pas d'environnement PHP par défaut côté Vercel, et
faire tourner un serveur WordPress complet pendant un build serverless n'est pas fiable. L'export doit
donc être **produit en local (ou en CI) puis livré tel quel**. Le dossier reste néanmoins clairement
documenté comme jetable et régénérable en une commande : à chaque nouvelle relecture client, on relance
`npm run preview:export`, on vérifie le résultat, et on commite la nouvelle version.

## Déploiement Vercel

Voir les étapes exactes dans le rapport de session / README. En résumé : projet Vercel pointant sur
ce dépôt, **Framework Preset : Other**, **Build Command : (aucune)**, **Output Directory :
`preview-dist`**, **Root Directory : `.`** (racine du dépôt).

Vercel Deployment Protection (mot de passe sur le déploiement) n'est disponible que sur les offres
payantes de Vercel — non activée ici faute d'accès ; le `noindex` + `robots.txt` restent la seule
protection tant qu'elle n'est pas mise en place. Le lien ne doit donc être partagé qu'avec le client,
pas publié publiquement.
