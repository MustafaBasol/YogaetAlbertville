# Logo officiel

`yoga-et-vie-logo-original.png` est le **fichier source officiel** de l'association (fourni par le
client), conservé ici **sans retouche** : proportions, couleurs et typographie d'origine intactes.
Aucune recoloration, aucun redessin, aucun recadrage de son contenu.

- Dimensions d'origine : 1039 × 1058 px, PNG avec transparence.
- Ne pas modifier ce fichier. Si le client fournit une version supérieure (vectorielle, SVG/AI/EPS,
  ou une résolution plus grande), la remplacer ici en conservant ce nom de fichier ou en mettant à
  jour les scripts qui la référencent (`tools/*.php` sous `tools/demo-content/`).

## Dérivés web

Générés par un simple redimensionnement sur un fond carré transparent (le contenu original n'est
jamais recadré : un léger remplissage transparent est ajouté sur l'axe le plus court pour obtenir un
carré). Rangés dans `tools/demo-content/images/` car ce sont des fichiers *importés dans la médiathèque*
par le script de démonstration, pas des assets chargés directement par le thème :

- `yoga-et-vie-logo.png` (1058×1058 px) : logo du site (bloc Logo du site).
- `yoga-et-vie-icon.png` (512×512 px) : icône du site / favicon.

Pour régénérer ces dérivés après une mise à jour du fichier source, un script PHP minimal (GD) suffit :
charger l'original, le placer sur un carré transparent, redimensionner avec `imagecopyresampled` en
conservant le canal alpha (`imagesavealpha`).
