#!/usr/bin/env bash
set -euo pipefail

if ! command -v wp >/dev/null 2>&1; then
  echo "WP-CLI не найден" >&2
  exit 1
fi

wp theme activate wolfagroles
wp plugin activate wolfagroles-core
wp eval 'wg_seed_content();'
wp option update blogname 'ООО «Вольфагролес»'
wp option update blogdescription 'Производственные задачи по чертежам'
wp rewrite structure '/%postname%/' --hard
wp rewrite flush --hard
echo "Начальный контент создан. Проверьте черновики и Настройки → Вольфагролес."
