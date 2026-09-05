import fs from 'node:fs/promises';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const output = process.argv[2] ? path.resolve(process.argv[2]) : path.join(root, '.wayback-cache');
const functionsPhp = await fs.readFile(path.join(root, 'wp-content/themes/suburban-relocation/functions.php'), 'utf8');
const start = functionsPhp.indexOf('function srs_legacy_url_map');
const end = functionsPhp.indexOf('/** Preserve the exact public URLs', start);
const block = functionsPhp.slice(start, end);
const matches = [...block.matchAll(/'([^']+[.]html)'\s*=>\s*array\(\s*'(srs_service|srs_location)'\s*,\s*'([^']+)'/g)];
const unique = new Map();
for (const match of matches) {
  if (!unique.has(match[3])) unique.set(match[3], { slug: match[3], type: match[2], legacyUrl: match[1] });
}

await fs.mkdir(output, { recursive: true });
const pages = [...unique.values()];
const results = [];
let cursor = 0;

async function fetchPage(page) {
  const source = `https://web.archive.org/web/20231004090206id_/https://suburbanrelocationmovers.com/${page.legacyUrl}`;
  let lastError = '';
  for (let attempt = 1; attempt <= 4; attempt++) {
    try {
      const response = await fetch(source, { redirect: 'follow', headers: { 'user-agent': 'SuburbanRelocationMigration/1.0' } });
      const html = await response.text();
      if (response.ok && html.length > 1000 && /<html/i.test(html)) {
        const file = path.join(output, `${page.slug}.html`);
        await fs.writeFile(file, html);
        return { ...page, ok: true, status: response.status, source: response.url, file };
      }
      lastError = `HTTP ${response.status}, ${html.length} bytes`;
    } catch (error) {
      lastError = error.message;
    }
    await new Promise(resolve => setTimeout(resolve, attempt * 1200));
  }
  return { ...page, ok: false, error: lastError, source };
}

async function worker() {
  while (cursor < pages.length) {
    const index = cursor++;
    const result = await fetchPage(pages[index]);
    results[index] = result;
    process.stdout.write(`${result.ok ? 'OK' : 'FAIL'} ${index + 1}/${pages.length} ${result.slug}${result.ok ? '' : `: ${result.error}`}\n`);
  }
}

await Promise.all([worker(), worker(), worker()]);
await fs.writeFile(path.join(output, 'manifest.json'), JSON.stringify(results, null, 2));
const failed = results.filter(item => !item.ok);
process.stdout.write(`COMPLETE=${results.length - failed.length} FAILED=${failed.length}\n`);
if (failed.length) process.exitCode = 1;
