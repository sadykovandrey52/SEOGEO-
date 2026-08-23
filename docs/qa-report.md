# QA и приёмка

Дата локальной сборки: 24.08.2026.

| Проверка | Метод | Статус до CI/staging | Доказательство/действие |
|---|---|---|---|
| Обязательные файлы темы и плагина | `tools/build.sh` | PASS | Сборка останавливается при отсутствии файла |
| Целостность ZIP | `unzip -tq` | PASS после сборки | `dist/*.zip` |
| Контрольные суммы | SHA-256 | PASS после сборки | `dist/SHA256SUMS.txt` |
| PHP syntax | `php -l` в GitHub Actions | PENDING до первого workflow | `.github/workflows/validate.yml` |
| Старый публичный телефон / lorem | `rg` в build и CI | PASS | Сборка останавливается при совпадении |
| Неподтверждённая сущность не публикуется | `wp_insert_post_data` guard | IMPLEMENTED | Проверить сменой статуса на staging |
| Canonical/Open Graph | просмотр HTML | IMPLEMENTED | Проверить по одному URL каждого типа |
| JSON-LD | серверная генерация | IMPLEMENTED | Проверить Schema.org Validator на staging |
| XML sitemap / robots | WordPress core + filter | IMPLEMENTED | Проверить `/wp-sitemap.xml`, `/robots.txt` |
| Форма без канала связи | серверная валидация | IMPLEMENTED | Ожидается ошибка |
| Разрешённые/запрещённые файлы | extension + MIME + size + count | IMPLEMENTED | Протокол staging обязателен |
| Honeypot / nonce / rate limit | серверная проверка | IMPLEMENTED | 3 заявки / 10 минут с одного IP |
| SMTP и письмо менеджеру | `wp_mail` | BLOCKED до SMTP | Ввести фактические SMTP-настройки |
| Письмо пользователю | нейтральное подтверждение | IMPLEMENTED | Проверить по тестовому e-mail |
| Аналитика | `dataLayer` после server success | IMPLEMENTED | Проверить один event на lead ID |
| 320/375/768/1024/1440 | браузерный smoke-test | PENDING staging | Проверить отсутствие overflow |
| Keyboard/focus/reduced motion | ручная проверка | PENDING staging | Меню, форма, FAQ, sticky CTA |
| Lighthouse mobile | production-like URL | PENDING staging | Цели: P≥85, A11y/BP/SEO≥95 |
| LCP/INP/CLS | Lighthouse/CrUX | PENDING production-like | LCP≤2,5с; INP≤200мс; CLS≤0,1 |
| Security headers | `curl -I` | PENDING web server | Часть заголовков добавляет плагин |
| Backup/restore | тест хостинга | BLOCKED до хостинга | Нужна единая точка БД + files |

## Smoke-test формы

1. Отправить валидную заявку только с телефоном.
2. Отправить валидную заявку только с e-mail и убедиться в двух письмах.
3. Приложить по одному PDF, DXF/DWG, STEP, XLSX, DOCX и ZIP.
4. Проверить блокировку `.php`, `.exe`, неверного MIME, превышения размера и количества.
5. Создать ошибку SMTP и убедиться, что временные файлы удалены, а содержимое чертежа не попало в журнал.
6. Повторить reload успешного URL и убедиться, что `lead_form_success` не дублируется в одной сессии.
