# Manual deployment

Use this procedure only when the normal GitHub Actions deployment is unavailable. Run local commands from the repository root and server commands from the Laravel release directory that contains `artisan`.

## Server locations

| Environment | Laravel root | Public web root |
| --- | --- | --- |
| Staging | `/home/logisticlink/deployments/releases/admin-staging` | `/home/logisticlink/public_html/admin-staging` |
| Production | `/home/logisticlink/deployments/releases/admin-production` | `/home/logisticlink/public_html/admin-production` |

PHP on the server is `/usr/local/bin/php`. SSH uses `logisticlink@88.198.98.9` on port `4222`.

## 1. Prepare and verify locally

Identify the exact deployed baseline commit, then review every file changed since it. Do not assume the last task contains all pending changes.

```bash
git status --short
git diff --stat <deployed-commit>..HEAD
git diff --name-only <deployed-commit>..HEAD
```

Include intentional untracked files shown by `git status`. Before uploading, run syntax checks for every changed PHP file and the relevant focused tests.

```bash
php -l path/to/ChangedFile.php
php artisan route:list
```

Do not upload `.env`, runtime logs, caches, `vendor`, or unrelated local files. Do not invoke the legacy test/message routes in `routes/web.php`.

## 2. Preview the upload

Pass each intended file explicitly and preserve its repository-relative path with `-R`:

```bash
rsync -avRn \
  -e "ssh -p 4222" \
  ./path/to/FirstChangedFile.php \
  ./path/to/SecondChangedFile.php \
  logisticlink@88.198.98.9:/home/logisticlink/deployments/releases/admin-staging/
```

Important: `-n` means **dry run**. It prints what would be transferred but uploads nothing. Review this output before the real upload.

## 3. Upload with recoverable backups

Remove only `-n` for the real transfer. `--backup-dir` preserves overwritten server files so the upload can be rolled back.

```bash
DEPLOY_TAG=$(date +%Y%m%d-%H%M%S)

rsync -avR \
  --backup \
  --backup-dir="/home/logisticlink/deployments/backups/admin-staging/$DEPLOY_TAG" \
  -e "ssh -p 4222" \
  ./path/to/FirstChangedFile.php \
  ./path/to/SecondChangedFile.php \
  logisticlink@88.198.98.9:/home/logisticlink/deployments/releases/admin-staging/
```

Upload to staging first. After staging verification, repeat with `admin-production` in both the destination and backup paths. Never use `--delete` for a selective manual deployment.

## 4. Finish on the server

```bash
ssh -p 4222 logisticlink@88.198.98.9
cd /home/logisticlink/deployments/releases/admin-staging

/usr/local/bin/php artisan optimize:clear
/usr/local/bin/php artisan config:cache
```

Do not run `route:cache` unless it has been verified for the deployed revision; this legacy application contains closure routes that may prevent route serialization.

Only when the change includes new Composer dependencies:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
```

Only when the change includes a new migration, take a database backup first and then run:

```bash
/usr/local/bin/php artisan migrate --force
```

Queue workers are started every minute by the existing per-environment cron entries. They use separate lock files to prevent overlap. After configuration changes, each cron invocation starts a fresh worker; do not run old queued business jobs blindly.

Set `DB_QUEUE` to an environment-specific queue name when more than one deployment shares the same queue database. The worker for that environment must listen to the same name. Staging uses `admin-staging`; production remains on `default` until it is deliberately isolated with its own worker.

Shipment Excel imports use `default` unless `SHIPPMENT_IMPORT_QUEUE` is configured for that environment. When isolating an environment, set it to `shippment-imports` and add a worker with its own lock file. Do not change production until its matching worker is installed:

```bash
* * * * * /usr/bin/flock -n /home/logisticlink/tmp/admin-staging-import-queue.lock /usr/local/bin/php /home/logisticlink/deployments/releases/admin-staging/artisan queue:work database --queue=shippment-imports --stop-when-empty --sleep=1 --tries=3 --timeout=90 >> /home/logisticlink/logs/admin-staging-import-queue.log 2>&1
```

## 5. Verify before production

- Open the changed page in both Arabic and English.
- Test with the role affected by the change, not only an administrator.
- Check `storage/logs/laravel.log`, the queue log, pending jobs, and failed jobs.
- Confirm uploaded files on the server with `stat` or a checksum when needed.
- Record the commit deployed and the backup directory created by `rsync`.

Useful checks:

```bash
/usr/local/bin/php artisan queue:failed
/usr/local/bin/php artisan tinker --execute='dump(Illuminate\Support\Facades\DB::table("jobs")->count());'
tail -100 storage/logs/laravel.log
```

## Rollback

Restore only the files backed up for that deployment from its directory under `/home/logisticlink/deployments/backups/`. Clear and rebuild Laravel caches afterward. Database migrations require a separately reviewed rollback; do not run a broad `migrate:rollback` on production without confirming exactly which migration batch it will reverse.
