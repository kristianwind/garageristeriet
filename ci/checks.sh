#!/usr/bin/env bash
# Statiske kontroller for temaet. Koeres af release.yml paa hver push og hver
# PR, og kan koeres i haanden foer man committer:
#
#     ci/checks.sh            # alt undtagen tag-kontrollen
#     ci/checks.sh v1.2.2     # ogsaa: matcher versionen tagget?
#
# Hver kontrol her findes fordi den fejl er sket. Ingen af dem er hypotetiske.
set -uo pipefail

FAIL=0
ok()   { printf '  OK    %s\n' "$1"; }
bad()  { printf '  FEJL  %s\n' "$1"; FAIL=1; }
head_() { printf '\n%s\n' "$1"; }

cd "$(dirname "$0")/.."
TAG="${1:-}"

# ---------------------------------------------------------------- fonte ----
# 2026-09-20: otte proprietaere fonte (Myriad Pro/Adobe, Impact/Monotype) var
# paa vej ind i dette offentlige GPL-repo, registreret i theme.json med
# file:-referencer. Det blev fanget af et menneske der kiggede i en zip.
# Denne kontrol er det menneske.
head_ "Fonte"

MODERNE=$(find . -path ./.git -prune -o -type f \( -name '*.otf' -o -name '*.ttf' \) -print 2>/dev/null)
if [ -n "$MODERNE" ]; then
  bad "desktop-fontformater i repoet — kun .woff2 hoerer hjemme her:"
  printf '        %s\n' $MODERNE
else
  ok "ingen .otf/.ttf"
fi

# Hver fontfil theme.json peger paa skal findes. En manglende fil giver ingen
# fejl nogen steder — teksten falder bare tilbage til en systemfont, og det
# opdager man ikke paa en skaerm man selv har designet.
if command -v python3 >/dev/null 2>&1; then
  MISSING=$(python3 - <<'PY'
import json, os
try:
    d = json.load(open('theme.json'))
except Exception as e:
    print(f"KUNNE-IKKE-LAESE: {e}"); raise SystemExit
for fam in d.get('settings', {}).get('typography', {}).get('fontFamilies', []):
    for face in fam.get('fontFace', []):
        for src in face.get('src', []):
            p = src.replace('file:./', '')
            if not os.path.exists(p):
                print(p)
PY
)
  if [ -n "$MISSING" ]; then
    bad "theme.json peger paa fontfiler der ikke findes:"
    printf '        %s\n' $MISSING
  else
    ok "alle fonte i theme.json findes paa disken"
  fi

  # SIL OFL kraever at licensteksten foelger filerne. Den for Source Serif 4
  # manglede i maaneder uden at nogen opdagede det.
  FAMS=$(python3 -c "
import json
d=json.load(open('theme.json'))
for f in d.get('settings',{}).get('typography',{}).get('fontFamilies',[]):
    if f.get('fontFace'): print(f['name'].split('(')[0].strip().lower().replace(' ','-'))
" 2>/dev/null)
  LIC_MISSING=""
  for fam in $FAMS; do
    ls assets/fonts/LICENSE-"$fam"*.txt >/dev/null 2>&1 || LIC_MISSING="$LIC_MISSING $fam"
  done
  if [ -n "$LIC_MISSING" ]; then
    bad "fontfamilier uden LICENSE-*.txt ved siden af:$LIC_MISSING"
  else
    ok "hver fontfamilie har sin licenstekst"
  fi
fi

# -------------------------------------------------------------- version ----
# style.css og GR_VERSION drev fra hinanden, og enqueue-versionen — altsaa
# cache-busteren paa stylesheetet — kommer fra den sidste.
head_ "Version"

CSS_V=$(sed -n 's/^Version:[[:space:]]*//p' style.css | head -1 | tr -d '\r')
PHP_V=$(sed -n "s/.*'GR_VERSION'[^']*'\([^']*\)'.*/\1/p" functions.php | head -1)

[ -n "$CSS_V" ] || bad "ingen Version: i style.css"
if [ "$CSS_V" = "$PHP_V" ]; then
  ok "style.css og GR_VERSION er enige ($CSS_V)"
else
  bad "style.css siger '$CSS_V', GR_VERSION siger '$PHP_V'"
fi

if [ -n "$TAG" ]; then
  if [ "${TAG#v}" = "$CSS_V" ]; then
    ok "tagget matcher versionen ($TAG)"
  else
    bad "tag '$TAG' matcher ikke Version: $CSS_V — bump foer du tagger"
  fi
fi

# ------------------------------------------------- opdateringskaeden ----
# To gange paa én dag stod dokumentationen og beskrev en opdateringskaede der
# ikke fandtes: foerst fonte der var byttet ud, saa et helt afsnit om Gitea
# efter flytningen til GitHub. Koden flytter sig, teksten staar stille.
head_ "Opdateringskaede"

URI=$(sed -n 's/^Update URI:[[:space:]]*//p' style.css | head -1 | tr -d '\r')
if [ -z "$URI" ]; then
  bad "ingen Update URI: i style.css — saa opdaterer temaet sig aldrig"
else
  ok "Update URI: $URI"
  HOST=$(printf '%s' "$URI" | sed -E 's|https?://([^/]+)/.*|\1|')
  if grep -q "update_themes_${HOST}" inc/updater.php; then
    ok "filteret hedder update_themes_${HOST}, som WordPress bygger det af vaertsnavnet"
  else
    bad "inc/updater.php haenger ikke paa update_themes_${HOST} — kaeden er brudt"
  fi
  if grep -q "$URI" readme.txt 2>/dev/null; then
    ok "readme.txt beskriver samme adresse"
  else
    bad "readme.txt naevner ikke $URI — dokumentationen er bagud"
  fi
fi

# Guarden: filteret deles med ALLE temaer der opdaterer fra samme vaert.
# Uden den tilbyder vi vores release til et fremmed tema. Samme fejl kostede
# et plugin en forkert opdatering paa en shop i drift.
if grep -q 'theme_stylesheet' inc/updater.php && grep -qE '(GR_UPDATE_SLUG|.garageristeriet.)[^;]*!==[^;]*theme_stylesheet' inc/updater.php; then
  ok "guarden paa \$theme_stylesheet er der"
else
  bad "ingen guard paa \$theme_stylesheet i inc/updater.php"
fi

# ------------------------------------------------------------------ php ----
head_ "PHP"
if command -v php >/dev/null 2>&1; then
  ERR=$(find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v 'No syntax errors' || true)
  [ -z "$ERR" ] && ok "alle php-filer er syntaktisk gyldige" || { bad "php-syntaksfejl:"; printf '        %s\n' "$ERR"; }
else
  bad "php mangler — syntakskontrollen blev IKKE koert"
fi

printf '\n'
[ "$FAIL" -eq 0 ] && { echo "Alle kontroller bestaaet."; exit 0; }
echo "Mindst én kontrol fejlede."; exit 1
