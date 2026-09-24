/**
 * Post-processes the wget-mirrored static preview in place:
 *  - forces <meta name="robots" content="noindex, nofollow">;
 *  - strips WordPress-only head plumbing that means nothing in a static
 *    export (RSD/xmlrpc, wlwmanifest, REST API discovery, feeds, oEmbed,
 *    the "generator" tag);
 *  - rewrites any leftover absolute http://localhost:PORT string to a
 *    root-relative path, so the export works under any Vercel domain;
 *  - disables the contact form's real submission (no admin-post.php target
 *    exists statically) without faking a successful send.
 *
 * Usage: node postprocess-static-preview.mjs <dist-dir> <base-url>
 */
import fs from 'node:fs';
import path from 'node:path';

const [, , distDir, baseUrl] = process.argv;
if (!distDir || !baseUrl) {
	console.error('Usage: postprocess-static-preview.mjs <dist-dir> <base-url>');
	process.exit(1);
}

const walk = (dir) => {
	const out = [];
	for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
		const full = path.join(dir, entry.name);
		if (entry.isDirectory()) out.push(...walk(full));
		else out.push(full);
	}
	return out;
};

let files = walk(distDir);

// wget preserves WordPress's cache-busting query strings (?ver=...) in the
// local filename it saves to disk (e.g. "global.css?ver=0.1.0.css"), which
// a static file server cannot serve back (it splits the query string off
// the path before looking a file up). Rename every such file by dropping
// the query string, then rewrite every reference to it further down.
let renamed = 0;
for (const file of files) {
	const qIndex = file.indexOf('?');
	if (qIndex === -1) continue;
	const clean = file.slice(0, qIndex);
	if (!fs.existsSync(clean)) {
		fs.renameSync(file, clean);
		renamed++;
	}
}
files = walk(distDir);
const htmlFiles = files.filter((f) => f.endsWith('.html'));
const textFiles = files.filter((f) => /\.(html|css|js|xml|txt|json)$/.test(f));

// Match a local .css/.js reference still carrying its query string (in an
// href/src attribute, an @font-face url(), or a raw import-map JSON value)
// and drop the query string so it points at the renamed file above. wget's
// own link-converter percent-encodes "?" as "%3F" when it rewrites an href
// to point at a locally-saved file whose name contains one (a literal "?"
// is unsafe in a path segment), so both forms must be matched.
const QUERY_SUFFIX = /(\.(?:css|js))(?:\?|%3[Ff])[^"'()\s]*/g;

let formsDisabled = 0;
let noindexed = 0;

for (const file of htmlFiles) {
	let html = fs.readFileSync(file, 'utf8');

	// Force a strict, unambiguous robots directive (belt and braces on top
	// of robots.txt): replace WordPress's own tag if present, else inject one.
	if (/<meta\s+name=(['"])robots\1[^>]*>/i.test(html)) {
		html = html.replace(/<meta\s+name=(['"])robots\1[^>]*>/i, '<meta name="robots" content="noindex, nofollow, noarchive">');
	} else {
		html = html.replace(/<head>/i, '<head>\n\t<meta name="robots" content="noindex, nofollow, noarchive">');
	}

	// Strip head plumbing that is meaningless (or misleading) once static.
	html = html
		.replace(/<link rel=["']https:\/\/api\.w\.org\/["'][^>]*>\s*/gi, '')
		.replace(/<link rel=["']alternate["'][^>]*type=["']application\/json[^>]*>\s*/gi, '')
		.replace(/<link rel=["']alternate["'][^>]*oembed[^>]*>\s*/gi, '')
		.replace(/<link rel=["']alternate["'][^>]*rss\+xml[^>]*>\s*/gi, '')
		.replace(/<link rel=["']EditURI["'][^>]*>\s*/gi, '')
		.replace(/<link rel=["']wlwmanifest["'][^>]*>\s*/gi, '')
		.replace(/<meta name=["']generator["'][^>]*>\s*/gi, '');

	// wget's --convert-links points internal links straight at the saved
	// "index.html" file. That works, but "/les-cours/index.html" in the
	// address bar is uglier than the clean "/les-cours/" the real site
	// uses — and a bare "index.html" (no leading path) needs to become
	// "./" rather than an empty string. Strip the redundant filename.
	html = html
		.replace(/href=(["'])index\.html\1/g, 'href=$1./$1')
		.replace(/href=(["'])([^"'>]*\/)index\.html\1/g, 'href=$1$2$1');

	// Disable the contact form's real submission — visual form kept, but it
	// can no longer POST anywhere, and we do not fake a successful send.
	if (html.includes('class="yev-contact__form"')) {
		const before = html;
		html = html
			.replace(
				/<form class="yev-contact__form" action=["'][^"']*["'] method="post" novalidate>/,
				'<form class="yev-contact__form" action="#formulaire-contact" method="post" novalidate onsubmit="return false" aria-describedby="yev-static-preview-notice">'
			)
			.replace(
				/(<div class="yev-contact"[^>]*>)/,
				'$1\n\t\t<p class="yev-contact__demo" id="yev-static-preview-notice"><strong>Aperçu statique :</strong> ce formulaire ne peut pas être envoyé depuis cette page de démonstration (aucun serveur derrière cet aperçu).</p>'
			);
		if (html !== before) formsDisabled++;
	}

	fs.writeFileSync(file, html);
	noindexed++;
}

// Remove the now-broken origin from every text asset (HTML, CSS, JS) so the
// export is portable to any domain — root-relative paths keep working.
const origins = [baseUrl, baseUrl.replace(/^https?:/, 'http:'), baseUrl.replace(/^https?:/, 'https:')];
for (const file of textFiles) {
	let content = fs.readFileSync(file, 'utf8');
	let changed = false;
	for (const origin of new Set(origins)) {
		if (content.includes(origin)) {
			content = content.split(origin).join('');
			changed = true;
		}
	}
	const withoutQueryStrings = content.replace(QUERY_SUFFIX, '$1');
	if (withoutQueryStrings !== content) {
		content = withoutQueryStrings;
		changed = true;
	}
	if (changed) fs.writeFileSync(file, content);
}

console.log(`Post-traitement : ${noindexed} page(s) HTML, ${formsDisabled} formulaire(s) désactivé(s), ${renamed} fichier(s) renommé(s) (suppression des "?ver=…").`);

// Sanity check: fail loudly if the banned origin string survives anywhere.
const leftovers = [];
for (const file of textFiles) {
	const content = fs.readFileSync(file, 'utf8');
	if (content.includes(baseUrl) || content.includes('wp-admin/admin-post.php') || content.includes('wp-login.php')) {
		leftovers.push(file);
	}
}
if (leftovers.length) {
	console.error('Références restantes à nettoyer :\n' + leftovers.join('\n'));
	process.exit(1);
}
