=== GarageRisteriet ===

Contributors: GarageRisteriet
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Warmt nordisk / japandi blocktheme (FSE) til GarageRisteriet - specialkafferisteri i Hornslet.

== Installation ==

1. WordPress-admin > Udseende > Temaer > Tilføj nyt > Upload tema.
2. Vælg garageristeriet.zip og aktivér.
3. Installer WooCommerce (nødvendig for produktgrid, kurv og kasse).
4. Indstillinger > Læsning: sæt "Din forside viser" til en statisk side (fx "Forside"). Temaets front-page.html bruges automatisk.
5. Udseende > Redigér (Site Editor): upload logoet under Site Logo. Filerne ligger i temaet under assets/logo/.
6. Opret siderne kurv, kasse, min-konto via WooCommerce-opsætningen. Header og footer linker til /kurv/, /kasse/, /min-konto/.

== Skabeloner ==

* front-page.html - onepageren: hero, USP-stribe, #kaffen (produktgrid), #rist, #om, #butik, #erhverv
* page.html - tekstside med titel (handelsbetingelser, privatlivspolitik, fragt, FAQ)
* page-fullwidth.html - side uden titel, fx kurv og kasse (vælg som skabelon på siden)
* single.html, archive.html, search.html, index.html, 404.html
* parts/header.html, parts/footer.html

Sektionerne findes også som patterns under kategorien "GarageRisteriet", så de kan sættes ind på enhver side.

== Fonte ==

Temaet hoster alle sine fonte selv i assets/fonts/ og indlæser dem via
theme.json. Ingen fonte hentes fra eksterne CDN'er - alt ligger lokalt af
hensyn til GDPR.

  Source Sans 3    brødtekst og UI   (variabel, normal + kursiv)
  Source Serif 4   overskrifter      (variabel, normal + kursiv)
  Anton            display og tal

Alle tre er under SIL Open Font License 1.1 og må distribueres med temaet.
Licensteksterne ligger ved siden af filerne som LICENSE-source-sans-3.txt,
LICENSE-source-serif-4.txt og LICENSE-anton.txt; OFL kræver at de følger med.

Brandets trykte skrifter er Myriad Pro og Impact, men de er proprietære
desktop-licenser og må ikke ligge i et offentligt repo eller serveres til
besøgende. Source Sans 3 er Adobes egen frie slægtning til Myriad, og Anton
dækker Impacts rolle. Vil I have den ægte Myriad live, kræver det et Adobe
Fonts-webprojekt - og det er et eksternt CDN-kald, altså netop det GDPR-valget
her undgår.

== Mærker og logoer ==

Footeren har navngivne pladsholdere til EU's øko-logo, Ø-mærket, DK-ØKO-100,
Dankort/Visa/Mastercard/MobilePay, GLS/PostNord og Trustpilot. Det er beskyttede
mærker, og de er derfor ikke tegnet. Udskift .gr-mark-elementerne i parts/footer.html
med de officielle filer (eller Trustpilots egen widget).

== Billeder ==

Alle billedfelter er pladsholdere (.gr-ph) med en fotobrief i teksten. Erstat hver
gruppe med en billed-blok, når fotografierne findes. Retning: varmt lavkontrast
dagslys, eg, linned, mat keramik, kraftpapir, mørke bønner. Ingen HDR, ingen kolde toner.

== Ændringslog ==

= 1.2.1 =
* Versionsbump.

= 1.2.0 =
* Onepager-forside porteret fra designet: hero, mørkt USP-bånd, abonnement, butik.
* Variantvælger som chips i produktgriddet.
* Selvopdatering via GitHub Releases.
* Frie fonte: Source Sans 3, Source Serif 4 og Anton under SIL OFL.

= 1.0.0 =
* Første udgivelse. Onepager-forside, WooCommerce-styling, tokens fra GarageRisteriet Design System.

== Opdatering ==

style.css har "Update URI: https://github.com/kristianwind/garageristeriet-theme".
inc/updater.php haenger paa filteret update_themes_github.com og svarer KUN naar
$theme_stylesheet er "garageristeriet" — filteret deles med alle andre temaer der
opdaterer fra samme host, og uden den guard tilbyder vi vores release til et
fremmed tema. Svarer feedet ikke, returneres den installerede version med tom
package, saa temaet bliver i opdaterings-transienten og "Aktivér
auto-opdateringer" ikke forsvinder fra skaermen.

Release: ret Version i style.css og GR_VERSION i functions.php til samme tal, og
tag med vX.Y.Z. .github/workflows/release.yml verificerer at de tre matcher,
bygger zip'en med oeverste mappe "garageristeriet" og laegger den paa releasen.

Temamappen hedder "garageristeriet" og maa aldrig omdoebes — mappenavnet ER
temaets identitet.
