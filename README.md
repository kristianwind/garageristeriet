# GarageRisteriet – WordPress blok-tema

Nordisk, minimalistisk Full Site Editing-tema til GarageRisteriet – dansk specialkaffe-risteri med WooCommerce-shop. 100 % native blokeditor: ingen page builders, ingen jQuery, intet build-step.

## Krav

- WordPress 6.5+
- WooCommerce (nyeste version, blok-baseret kurv/checkout)
- PHP 7.4+

## Installation

1. Download `garageristeriet.zip` (fra seneste [GitHub Release](https://github.com/kristianwind/garageristeriet-theme/releases) eller byg den selv, se nedenfor).
2. Gå til **Udseende → Temaer → Tilføj nyt → Upload tema** og vælg zip-filen.
3. Aktivér temaet.
4. Sæt forsiden op: **Indstillinger → Læsning → En statisk side** og vælg din forside. Forsiden bruger automatisk `front-page`-templaten med alle onepager-sektioner.
5. WooCommerce-siderne (Shop, Kurv, Kasse, Min konto) oprettes af WooCommerce selv og bruger temaets blok-templates.

## Onepager-struktur

Forsiden er bygget af block patterns i kategorien **GarageRisteriet**:

| Sektion | Pattern | Anker |
|---|---|---|
| Hero | `garageristeriet/hero` | – |
| Kaffe / shop | `garageristeriet/shop` | `#kaffe` |
| Om os | `garageristeriet/om-os` | `#om-os` |
| Ristningsprocessen | `garageristeriet/ristning` | `#ristning` |
| Testimonial | `garageristeriet/testimonial` | – |
| Kontakt / nyhedsbrev | `garageristeriet/kontakt` | `#kontakt` |

Header-navigationen linker til ankrene med smooth scroll, og `scroll-margin-top` kompenserer for den sticky header.

## Redigér sektioner i Site Editor

1. Gå til **Udseende → Editor → Skabeloner → Forside**.
2. Hver sektion ligger som et pattern. Klik ind i en sektion og redigér tekst, billeder og produkter direkte.
3. Vil du omarrangere eller tilføje sektioner: åbn blok-indsætteren, fanen **Mønstre → GarageRisteriet**, og træk sektionerne ind i den rækkefølge du vil.
4. Header og footer redigeres under **Udseende → Editor → Mønstre → Skabelondele**.
5. Farver, typografi og spacing styres centralt via **Udseende → Editor → Stilarter** (alt er defineret i `theme.json`).

**Tips:**

- Hero-billedet udskiftes ved at vælge cover-blokken og klikke **Erstat**. Behold klassen `gr-hero`, så billedet fortsat trækkes op under den transparente header.
- Nyhedsbrevs-placeholderen i kontaktsektionen erstattes med din formular-blok fra fx Mailchimp, MailPoet eller Brevo.
- Produktgrid'et i `#kaffe` er et WooCommerce **Product Collection**-block – antal kolonner og produkter ændres i blokkens sidebar.

## Teknik

- **theme.json (v3)** er sandheden: farvepalette, lokalt hostede fonte (Fraunces + Inter, OFL-licens, ingen Google Fonts CDN), fluid typografi med `clamp()`, spacing-skala og blok-styles.
- **Templates**: `front-page`, `index`, `page`, `404` samt blok-baserede WooCommerce-templates `single-product` og `archive-product`. Kurv/checkout leveres som blokke af WooCommerce og er stylet via temaet.
- **Sticky header**: transparent over hero, solid baggrund ved scroll – håndteret af ~40 linjer vanilla JS i `assets/js/garageristeriet.js`.
- Alle funktioner er prefixet `garageristeriet_`, al output escapes.

## Release-flow (automatiske opdateringer fra GitHub)

Temaet opdaterer sig selv via GitHub Releases. WordPress læser `Update URI`-headeren i `style.css` og spørger `inc/updater.php`, som tjekker GitHub API'et (cachet 12 timer i en transient).

Sådan udgiver du en ny version:

```bash
# 1. Bump versionen i style.css (fx 1.0.0 → 1.1.0)
# 2. Commit
git add style.css
git commit -m "Bump version til 1.1.0"

# 3. Tag og push – tagget SKAL matche versionen med v-prefix
git tag v1.1.0
git push origin main --tags
```

Herefter:

4. GitHub Actions (`.github/workflows/release.yml`) bygger en ren `garageristeriet.zip` (uden `.git`/`.github`, med korrekt mappenavn `garageristeriet/`) og uploader den som release-asset. Workflowet fejler bevidst, hvis versionen i `style.css` ikke matcher tagget.
5. Inden for ~12 timer (eller straks via **Kontrolpanel → Opdateringer → Søg igen**) ser WordPress den nye version og tilbyder opdatering som ethvert andet tema.

Falder updateren tilbage til GitHubs zipball (hvis asset'et mangler), omdøber `upgrader_source_selection`-hooket automatisk den udpakkede mappe til `garageristeriet/`, så opdateringen ikke lander i en forkert mappe.

## Byg zip lokalt

```bash
zip -rq garageristeriet.zip garageristeriet \
  -x 'garageristeriet/.git/*' 'garageristeriet/.github/*' '*.DS_Store'
```

## Licens

Tema: GPL-2.0-or-later. Fonte: Fraunces og Inter, begge SIL Open Font License (se `assets/fonts/`).
