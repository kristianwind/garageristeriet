repo: kristianwind/garageristeriet-theme
branch: main

Repo-rod = temarod. Temamappen ved installation hedder `garageristeriet` og
maa aldrig omdoebes — mappenavnet ER temaets identitet.

Skriveadgang: Claude Code paa serveren, arbejdsmappe
`/root/rc/garageristeriet`. Den committer, pusher og saetter tags.
Mappenavn og repo-navn maa gerne afvige — omdoeb ikke arbejdsmappen.

## Regler naar der skrives til repoet

1. **Ingen `.otf` eller `.ttf`.** Repoet er offentligt og GPL-2.0. Kun `.woff2`
   under en licens der tillader videredistribution, og licensteksten skal ligge
   ved siden af. Myriad Pro og Impact er brandets skrifter i tryk, men er
   proprietaere desktop-licenser og maa ikke ligge her. Web-erstatningerne er
   Source Sans 3 og Anton.
2. **Zips er fulde udskiftninger, ikke deltaer.** Et `unzip -o` efterlader
   filer fra tidligere udgaver som ingen laengere enqueuer. Naar noget skal
   *slettes*, skal det staa eksplicit — ikke kun hvad der skal aendres.
3. **`permissions: contents: write` skal blive paa `publish`-jobbet.** Repoets
   default er read, og at oprette en release er en skrivning. Uden den fejler
   `action-gh-release` med 403. Kun paa `publish` — `test` skal ikke kunne
   skrive.

## Opdateringskaede

`Update URI` i `style.css` → filteret `update_themes_github.com` i
`inc/updater.php` → `releases/latest` → download-URL bygget af `tag_name`.

Tre ting der ikke maa aendres uden at kaeden knaekker:

- Guarden `'garageristeriet' !== $theme_stylesheet` — filteret deles med alle
  andre temaer der opdaterer fra github.com.
- Ved feed-fejl returneres den **installerede** version med tom `package`.
  Returneres `false`, ryger temaet ud af opdaterings-transienten, og
  "Aktivér auto-opdateringer" forsvinder fra skaermen.
- Zippens oeverste mappe skal hedde `garageristeriet`. Workflowet asserterer
  det.

Release: ret `Version:` i `style.css` og `GR_VERSION` i `functions.php`, tag
`vX.Y.Z`. `test` verificerer at de matcher.

## Last sync

date: 2026-09-20T10:21:00Z
version: 1.2.1

### Updated in this project
- Forsiden porteret fra Onepager-designet: hero, mørkt USP-bånd, abonnement, butik
- Variantvælger som chips i produktgriddet (fieldset → div, Safari-fix)
- Selvopdatering via GitHub Releases (`inc/updater.php`, `Update URI`)
- Proprietære fonte byttet til Source Sans 3 + Anton, med OFL-licenser

## Screen map

| Skærm i projektet | Filer i repoet |
|---|---|
| Onepager.dc.html (forside) | `templates/front-page.html`, `patterns/*.php`, `style.css` |
| — header/footer | `parts/header.html`, `parts/footer.html` |
| — produktkort m. vægtvalg | `inc/loop-variations.php`, `inc/grind.php`, `style.css` |
| Webshop-labels.dc.html | (ingen — print, ligger kun i dette projekt) |
| Kaffekort.dc.html | (ingen — print) |
| Installation og fotobrief.dc.html | `README.md`, `assets/fonts/README.md` |
| Tokens / farver / type | `theme.json`, `style.css` (`:root`) |
| Opdateringskæde | `inc/updater.php`, `.github/workflows/release.yml`, `ci/update-smoke.sh` |
