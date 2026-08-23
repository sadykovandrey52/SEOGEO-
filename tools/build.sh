#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

required=(
  "wp-content/themes/wolfagroles/style.css"
  "wp-content/themes/wolfagroles/theme.json"
  "wp-content/themes/wolfagroles/functions.php"
  "wp-content/themes/wolfagroles/front-page.php"
  "wp-content/plugins/wolfagroles-core/wolfagroles-core.php"
  "wp-content/plugins/wolfagroles-core/includes/forms.php"
  "wp-content/plugins/wolfagroles-core/includes/seo-schema.php"
)

for path in "${required[@]}"; do
  test -s "$path" || { echo "Отсутствует обязательный файл: $path" >&2; exit 1; }
done

if rg -n -i "lorem ipsum|\+7[[:space:]]*495[[:space:]]*011[[:space:]-]*08[[:space:]-]*65" wp-content README.md docs; then
  echo "Обнаружен запрещённый placeholder или устаревший контакт" >&2
  exit 1
fi

mkdir -p dist
rm -f dist/wolfagroles-theme.zip dist/wolfagroles-core.zip dist/SHA256SUMS.txt

(
  cd wp-content/themes
  zip -qr ../../dist/wolfagroles-theme.zip wolfagroles
)
(
  cd wp-content/plugins
  zip -qr ../../dist/wolfagroles-core.zip wolfagroles-core
)

(
  cd dist
  sha256sum wolfagroles-theme.zip wolfagroles-core.zip > SHA256SUMS.txt
)

unzip -tq dist/wolfagroles-theme.zip
unzip -tq dist/wolfagroles-core.zip
echo "Сборка готова: dist/wolfagroles-theme.zip, dist/wolfagroles-core.zip"
