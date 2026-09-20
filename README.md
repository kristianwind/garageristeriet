# GarageRisteriet — WordPress-blocktheme

Warmt nordisk / japandi FSE-tema til GarageRisteriet, specialkafferisteri i
Hornslet. Onepager-forside, WooCommerce-klar, selvhostede fonte, ingen
eksterne CDN-kald (GDPR).

- **Temamappe:** `garageristeriet` — mappenavnet ER temaets identitet og maa
  aldrig omdoebes. En omdoebning er en ny installation.
- **Repo-rod = temarod.** `style.css` ligger i roden.

## Fonte — laes foer foerste release

`assets/fonts/` indeholder kun Source Serif 4 og en README. Temaet forventer
desuden Source Sans 3 (to filer) og Anton, plus OFL-licenserne. Se
`assets/fonts/README.md` for filnavne og kilder. Uden dem falder temaet
tilbage paa systemfonte — det virker, men ser forkert ud.

Myriad Pro og Impact er brandets skrifter i trykt materiale, men er
proprietaere desktop-licenser og maa ikke ligge i dette repo.

## Opdatering

Temaet opdaterer sig selv via WordPress' `Update URI` (WP 6.1+):

    Update URI: https://github.com/kristianwind/garageristeriet-theme

`inc/updater.php` haenger paa filteret `update_themes_github.com`, laeser
`tag_name` fra `releases/latest` og bygger selv download-adressen:

    https://github.com/kristianwind/garageristeriet-theme/releases/download/<tag>/garageristeriet.zip

Tre ting der ikke maa aendres uden at kaeden knaekker:

1. Guarden `'garageristeriet' !== $theme_stylesheet` — filteret deles med alle
   andre temaer der opdaterer fra github.com.
2. Ved feed-fejl returneres den **installerede** version med tom `package`.
   Returneres `false`, forsvinder temaet fra opdaterings-transienten, og
   "Aktivér auto-opdateringer" forsvinder fra skaermen.
3. Zippens oeverste mappe skal hedde `garageristeriet`. Ellers installerer
   WordPress en kopi ved siden af den gamle, og den gamle bliver aktiv.
   Workflowet asserterer det.

## Release

    # ret Version: i style.css og GR_VERSION i functions.php
    git tag v1.2.0 && git push --tags

`.github/workflows/release.yml`: `test` (ugated) tjekker at versionen matcher
tagget, koerer `php -l` og bygger zippen. `publish` er gated med
`github.server_url == 'https://github.com'`, saa tags ikke ogsaa laver en halv
release paa Gitea-spejlet.

## Verifikation

`ci/update-smoke.sh` beviser kaeden ved at lade WordPress selv hente:
installér 0.9.0, se at opdateringen tilbydes, opdatér, og assertér at temaet
stadig er aktivt og at der er **praecis én** mappe der starter med temanavnet.
