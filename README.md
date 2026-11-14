# Counter POS — Forms, validation, and avatars

IT0049 Technical Formative Assessment 7 extends the existing TFA5/TFA6 Laravel POS in place. Customer and user directories use Eloquent, with create/edit forms, server validation, old input, success notices, and processed user avatars. The four existing pages, navigation, roles, status badges, and styling remain. Checkout and login screens are outside this activity.

## Technologies and requirements

Laravel 13.42.0, PHP 8.3+ compatible with composer.lock (tested on PHP 8.5.10), Composer 2, Blade, HTML/CSS, SQLite, and PHPUnit 12. Install PHP PDO SQLite plus the extensions checked by `composer check-platform-reqs`. MySQL deployments additionally need PDO MySQL. Intervention Image 3.11.10 (approved by the user as instructor approved) prepares avatars using the GD driver. PHP GD is required in addition to Fileinfo and the standard Laravel extensions. No frontend build is needed. The lock file is authoritative for dependency versions.

## Install a new downloaded copy

Run these commands from the project root. Copy `.env` and generate the key only on first installation; preserve existing credentials and production keys.

```sh
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
php artisan storage:link --no-interaction
composer check-platform-reqs
php artisan serve
```

Visit http://127.0.0.1:8000. Stop the server with Ctrl+C. For the existing prepared copy, the TFA7 migration and storage link have already run; start it with `php artisan serve`. For another existing installation, preserve `.env` and APP_KEY, back up the database, run `composer install`, `php artisan migrate --no-interaction`, and `php artisan storage:link --no-interaction`. `composer run dev` also starts the server. Never run `migrate:fresh` against existing data.

## Database setup

The existing driver is SQLite and is preserved. `.env` and database files are ignored by Git. With `DB_CONNECTION=sqlite` and no active DB_DATABASE setting, Laravel uses `database/database.sqlite`. If an explicit DB_DATABASE is configured, use its absolute path and ensure it exists and PHP can write to it. No passwords were changed.

Keep `SESSION_DRIVER=file`, `CACHE_STORE=file`, and `QUEUE_CONNECTION=sync` for this single-instance lab. After changing environment values run `php artisan config:clear`. PHP must be able to write `storage/`, `bootstrap/cache/`, and the SQLite database directory.

For a host that requires MySQL, create an empty application database in the hosting control panel, grant its application user access, then configure DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD securely in the host environment. Use the host-provided values, then run the same migrations and seeders. This creates the schema and demonstration records; it does not transfer an existing SQLite dataset. MySQL execution has not been tested here.

## Schema and TFA2 compatibility

TFA2's supplied schema specifies full_name VARCHAR(100), customer email VARCHAR(100), nullable phone VARCHAR(20), and unique username VARCHAR(50). Those definitions are used. Intentional additions preserve the existing Laravel application and TFA5 display: updated_at timestamps, customer status, user role/status, and all default authentication columns. The users primary key remains Laravel's BIGINT rather than replacing it with TFA2's INT; existing nullable Laravel timestamps also remain. SQLite represents Laravel string and timestamp declarations using SQLite types and does not enforce VARCHAR lengths like MySQL.

| Customers column | Definition |
| --- | --- |
| id | Unsigned INT auto-increment primary key on MySQL; integer primary key on SQLite |
| full_name | VARCHAR(100), required |
| email | VARCHAR(100), required; no added unique constraint, matching TFA2 |
| phone | VARCHAR(20), nullable |
| created_at, updated_at | DATETIME, required, maintained by Eloquent |
| status | VARCHAR(20), required, default Active |

| Users column | Definition |
| --- | --- |
| id | Existing unsigned BIGINT auto-increment primary key; SQLite integer primary key |
| username | VARCHAR(50), required, unique |
| full_name | VARCHAR(100), required |
| name | Existing VARCHAR(255), required, retained for authentication compatibility |
| email | Existing VARCHAR(255), required, unique |
| email_verified_at | Existing nullable timestamp |
| password | Existing required VARCHAR(255), hashed by the User model |
| remember_token | Existing nullable VARCHAR(100) |
| created_at, updated_at | Existing nullable Laravel timestamps |
| avatar | Nullable VARCHAR(255), relative public-disk path only (TFA7) |
| role | VARCHAR(50), required, default Staff |
| status | VARCHAR(20), required, default Active |

The original users migration remains unchanged. A new migration adds the POS columns, assigns existing accounts unique usernames `legacy_<id>`, copies up to 100 characters of their names into full_name, then makes the new fields required. Original names, IDs, email addresses, passwords, and tokens remain intact. Full names longer than 100 characters are shortened only in the new POS column. Back up databases before deployments. Rolling back the new migration removes its added fields; use rollback only when that loss of POS field data is intended.

## Seeders and sample records

`DatabaseSeeder` calls `CustomerSeeder` and `UserSeeder`. Customers use Eloquent firstOrCreate keyed by email. Users are created through Eloquent only when neither their demonstration username nor email already exists. Reruns keep edits and password hashes, and do not add duplicate demonstration accounts. Existing conflicting user identifiers are skipped rather than overwritten; on a populated deployment, check that at least five users exist after seeding.

Customer samples are Isabel Santos, Miguel Reyes, Sofia Cruz, Gabriel Lim, and Amelia Garcia. Team samples are Andrea Mendoza (andrea.mendoza), Daniel Ramos (daniel.ramos), Chloe Tan (chloe.tan), Lucas Flores (lucas.flores), and Elena Torres (elena.torres). All emails use example.com. The existing IMPLEMENTATION_REPORT.md records the earlier TFA6 upgrade; see TFA7-REPORT.txt for this update.

Demo users receive random passwords hashed by Laravel; no reusable demo password is stored or documented. These pages do not provide a login form. The Authenticatable base class, notifications, hidden credentials, casts, and default authentication tables remain available.

```sh
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
php artisan migrate:status --no-interaction
php artisan db:show --counts --no-interaction
```

## Pages and Eloquent flow

| URL | Named route | Controller |
| --- | --- | --- |
| / | home | PageController@index |
| /about | about | PageController@about |
| /customers | customers.index | CustomerController@index |
| /users | users.index | UserController@index |

TFA7 adds resource actions to the existing controllers. The index actions still call `Customer::all()` and `User::all()`. Existing models, schema, factories, and seeders are reused. No relationships are defined between customers and users; the directories are independent.

| Method | URL | Named route |
| --- | --- | --- |
| GET | /customers/create | customers.create |
| POST | /customers | customers.store |
| GET | /customers/{customer}/edit | customers.edit |
| PUT/PATCH | /customers/{customer} | customers.update |
| GET | /users/create | users.create |
| POST | /users | users.store |
| GET | /users/{user}/edit | users.edit |
| PUT/PATCH | /users/{user} | users.update |

## TFA7 forms and avatar workflow

Use Add customer or Add user on the relevant directory. Both forms require full_name. Customer email must be valid; usernames must be unique. Length limits match existing columns. Creating users also requires a unique email and a confirmed password of at least eight characters, because the existing authentication schema requires them. The User model hashes passwords; name is synchronized with full_name. New users receive the existing Staff/Active defaults. Editing users changes only username, full_name/name, and an optional avatar; email, password, role, and status remain intact. Customer edits preserve phone and status. Controllers whitelist validated input. Avatar paths are assigned explicitly, never from a submitted string.

Forms include CSRF tokens, edit method fields, field-level errors, and `old()` fallback values. Passwords and file inputs are never repopulated. Route model binding returns 404 for missing records. Username uniqueness excludes the bound user on updates.

On Edit user, select JPG/JPEG, PNG, WebP, or GIF, at most 2048 KB and 4096 × 4096 pixels. Intervention Image reads the validated upload, crops/resizes it to 256 × 256, and re-encodes PNG bytes. `Storage::disk('public')->put()` saves those bytes under a generated UUID in avatars/. Only that relative path is stored in the nullable users.avatar column. There is no manual move() or binary database storage. A missing upload keeps the current avatar. Failed processing or writing returns a readable avatar error without saving text changes. A database save failure removes the newly written file. Previous avatar files are retained when replaced to avoid deleting existing uploads; cleanup can be added separately if wanted.

The directory and edit preview use public-disk URLs, consistent sizes, and a local SVG placeholder for null or missing files. Keep storage/app/public on persistent hosting storage, run `storage:link`, and set APP_URL to the real site address. For uploads near the 2 MB limit, configure PHP upload_max_filesize to at least 2M and post_max_size above 2M to allow multipart overhead; align the web server request limit.

## Manual verification

1. Open /customers, choose Add customer, and save valid contact information. Confirm the row and success notice.
2. Submit blank names and invalid email values. Browser checks catch common input errors; server validation also applies. Confirm field messages and old values after a server rejection.
3. Create a user with username, full name, email, and matching passwords. Try an existing username and confirm rejection.
4. Edit a customer and a user. Keep a user's username unchanged once, then change it to another available value. Confirm row counts do not grow.
5. Upload a valid image on Edit user. Check its square preview and directory avatar. Save again without selecting a file and confirm it remains.
6. Try a PDF and an image over 2048 KB. Confirm errors, old text values, and unchanged saved data. Select files again after validation errors.
7. Check placeholders for users without avatars, all four navigation destinations, and a nonexistent edit ID returning 404.

## Verification

```sh
php artisan test --compact
php artisan route:list --except-vendor --no-interaction
php artisan view:cache --no-interaction
composer check-platform-reqs
vendor/bin/pint --dirty --format agent
```

Executed on November 14, 2026: **23 tests passed, 179 assertions** on PHP 8.5.10 / Laravel 13.42.0 / SQLite. The suite includes the 11 prior tests plus 12 account-form tests. Coverage includes creation, required/invalid fields, duplicate usernames/emails, safe updates, unchanged usernames, preserved credentials and avatars, old input/errors, thumbnails, public storage paths/URLs, 2048 KB accepted versus 2049 KB rejected, non-image/SVG/dimension rejection, storage-write failures, placeholders, missing records, and original navigation/Eloquent/seeding/migration behavior. Tests use in-memory SQLite and fake public storage.

The actual SQLite database was backed up before applying the avatar migration. All five existing customers and five users were compared column by column against the backup after migration and remained unchanged. The public storage link was created. Blade compilation, PHP platform checks, Pint, and Git whitespace checks passed. A local browser check confirmed customer form styling, visible required-name errors, and retained email input. MySQL and production hosting remain untested.

## GitHub submission

The existing repository is [MariusKii/TFA7](https://github.com/MariusKii/TFA7). TFA7 extends the same repository. For future updates, review the diff before publishing:

```sh
git status --short
git diff
php artisan test --compact
git add app database/migrations resources/views routes/web.php public/css/app.css public/images composer.json composer.lock tests README.md SUBMISSION.md TFA7-REPORT.txt
git diff --cached
git commit -m "Add validated account forms and avatars for TFA7"
git push origin main
```

Submit the updated repository URL and a verified hosted application URL. Migration and seeder source files satisfy the activity's migration/seeder submission option; an SQL export is not required for that option. Keep `.env`, passwords, database files, vendor, logs, and generated caches out of Git. Complete the assignment cover sheet separately.

## Deployment

Use a PHP-capable host compatible with the locked dependencies. GitHub Pages cannot execute Laravel. No production deployment was performed.

1. Deploy the reviewed repository and run `composer install --no-dev --optimize-autoloader`.
2. Configure a private production environment with APP_ENV=production, APP_DEBUG=false, APP_URL set to the real HTTPS address, and a production APP_KEY generated once and preserved on redeployments.
3. Configure a persistent database. For SQLite, use an absolute path on a persistent writable volume, create the file once, and ensure PHP can write its directory. Ephemeral hosting filesystems cannot retain this database between deployments. For MySQL, provision the database/user and supply host-managed connection values.
4. Back up existing production data. Run `php artisan migrate --force --no-interaction` and, for the lab's demonstration records, `php artisan db:seed --force --no-interaction`.
5. Run `php artisan storage:link --no-interaction`. Persist storage/app/public and ensure it is writable. Install PHP GD and check upload size limits. Set the document root to public/. Configure requests to public/index.php and writable storage/ and bootstrap/cache/. File sessions/cache require persistent storage; this setup targets a single instance.
6. Run `php artisan optimize`, enable HTTPS, verify all pages, create/edit workflows, uploads and CSS, confirm at least five records per directory, then record the real URL in SUBMISSION.md.

Use the host's web server in production. Artisan serve is for local development. Do not publish secrets or use destructive reset commands on an existing database.

Official references: [validation](https://laravel.com/docs/13.x/validation), [file storage](https://laravel.com/docs/13.x/filesystem), [Intervention Image v3](https://image.intervention.io/v3/modifying-images/resizing), [migrations](https://laravel.com/docs/13.x/migrations), [Eloquent](https://laravel.com/docs/13.x/eloquent), [seeding](https://laravel.com/docs/13.x/seeding).
