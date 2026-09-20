#!/usr/bin/env bash
# Beviser opdateringskaeden ved at lade WordPress goere arbejdet.
# Koeres mod et engangs-WordPress med wp-cli installeret.
set -euo pipefail

THEME=garageristeriet
ZIP="${1:?brug: ci/update-smoke.sh /sti/til/garageristeriet.zip (version 0.9.0)}"

wp theme install "$ZIP" --force --activate
wp theme get "$THEME" --field=version

echo "— tilbydes der en opdatering?"
wp theme update --all --dry-run | tee /tmp/gr-dryrun.txt
grep -q "$THEME" /tmp/gr-dryrun.txt || { echo "FEJL: ingen opdatering tilbudt"; exit 1; }

wp theme update "$THEME"

echo "— efter opdatering"
NEW=$(wp theme get "$THEME" --field=version)
ACTIVE=$(wp theme list --status=active --field=name)
DIRS=$(ls -1d "$(wp theme path --dir)"/${THEME}* | wc -l)

[ "$ACTIVE" = "$THEME" ] || { echo "FEJL: temaet er ikke aktivt laengere"; exit 1; }
[ "$DIRS" -eq 1 ] || { echo "FEJL: $DIRS mapper starter med $THEME — zip'ens oeverste mappe er forkert"; exit 1; }

echo "OK — version $NEW, stadig aktivt, praecis én temamappe"
