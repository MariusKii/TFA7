# Counter POS — From Arrays to Eloquent

IT0049 Technical Formative Assessment 6. This upgrades the existing TFA5 application in place: customer and team directories now retrieve persistent records through Eloquent. The existing four pages, navigation, CSS, roles, and status badges are preserved. Checkout, account editing, and login screens remain outside this activity.

## Technologies and requirements

Laravel 13.42.0, PHP 8.3+ compatible with composer.lock (tested on PHP 8.5.10), Composer 2, Blade, HTML/CSS, SQLite, and PHPUnit 12. Install PHP PDO SQLite plus the extensions checked by `composer check-platform-reqs`. MySQL deployments additionally need PDO MySQL. No new packages or frontend build are needed. The lock file is authoritative for dependency versions.

## Install a new downloaded copy

Run these commands from the project root. Copy `.env` and generate the key only on first installation; preserve existing credentials and production keys.

```sh
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
composer check-platform-reqs
php artisan serve
```

Visit http://127.0.0.1:8000. Stop the server with Ctrl+C. For the existing prepared copy, migrations and seeders have already run; start it with `php artisan serve`. `composer run dev` also starts the server. Never run `migrate:fresh` against existing data.

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
| role | VARCHAR(50), required, default Staff |
| status | VARCHAR(20), required, default Active |

The original users migration remains unchanged. A new migration adds the POS columns, assigns existing accounts unique usernames `legacy_<id>`, copies up to 100 characters of their names into full_name, then makes the new fields required. Original names, IDs, email addresses, passwords, and tokens remain intact. Full names longer than 100 characters are shortened only in the new POS column. Back up databases before deployments. Rolling back the new migration removes its added fields; use rollback only when that loss of POS field data is intended.

## Seeders and sample records

`DatabaseSeeder` calls `CustomerSeeder` and `UserSeeder`. Customers use Eloquent firstOrCreate keyed by email. Users are created through Eloquent only when neither their demonstration username nor email already exists. Reruns keep edits and password hashes, and do not add duplicate demonstration accounts. Existing conflicting user identifiers are skipped rather than overwritten; on a populated deployment, check that at least five users exist after seeding.

Customer samples are Isabel Santos, Miguel Reyes, Sofia Cruz, Gabriel Lim, and Amelia Garcia. Team samples are Andrea Mendoza (andrea.mendoza), Daniel Ramos (daniel.ramos), Chloe Tan (chloe.tan), Lucas Flores (lucas.flores), and Elena Torres (elena.torres). All emails use example.com. See IMPLEMENTATION_REPORT.md for all sample fields.

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

Routes are unchanged. CustomerController calls `Customer::all()` and UserController calls `User::all()`. Eloquent uses the conventional customers/users tables. Both models define mass-assignable `$fillable` attributes. Controllers pass collections to the existing Blade templates. Templates display full_name and the other account fields with escaped `{{ }}` output and `@forelse` empty states. The user directory adds a username column while keeping email, role, and status. Model factories support isolated tests. The shared layout and stylesheet retain the TFA5 design.

## Verification

```sh
php artisan test --compact
php artisan route:list --except-vendor --no-interaction
php artisan view:cache --no-interaction
composer check-platform-reqs
vendor/bin/pint --dirty --format agent
```

Executed locally: 11 tests passed with 69 assertions. Tests use isolated in-memory SQLite databases and cover Eloquent collections, edited database values, navigation, empty states, escaped HTML, nullable phone, unique usernames, repeatable seeding, and preservation of an existing account during migration. The existing SQLite database migrated successfully and contains five customers and five users after two seeder runs. All four live HTTP pages and public/css/app.css returned 200. MySQL and production hosting have not been tested.

## GitHub submission

The existing repository is [MariusKii/laravel-pos](https://github.com/MariusKii/laravel-pos). The TFA6 update is published in this repository. For future updates, review the diff before publishing:

```sh
git status --short
git diff
php artisan test --compact
git add app database resources/views tests README.md IMPLEMENTATION_REPORT.md SUBMISSION.md screenshots
git diff --cached
git commit -m "Upgrade POS directories to Eloquent for TFA6"
git push origin main
```

Submit the updated repository URL and a verified hosted application URL. Migration and seeder source files satisfy the activity's migration/seeder submission option; an SQL export is not required for that option. Keep `.env`, passwords, database files, vendor, logs, and generated caches out of Git. Complete the assignment cover sheet separately.

## Deployment

Use a PHP-capable host compatible with the locked dependencies. GitHub Pages cannot execute Laravel. No production deployment was performed.

1. Deploy the reviewed repository and run `composer install --no-dev --optimize-autoloader`.
2. Configure a private production environment with APP_ENV=production, APP_DEBUG=false, APP_URL set to the real HTTPS address, and a production APP_KEY generated once and preserved on redeployments.
3. Configure a persistent database. For SQLite, use an absolute path on a persistent writable volume, create the file once, and ensure PHP can write its directory. Ephemeral hosting filesystems cannot retain this database between deployments. For MySQL, provision the database/user and supply host-managed connection values.
4. Back up existing production data. Run `php artisan migrate --force --no-interaction` and, for the lab's demonstration records, `php artisan db:seed --force --no-interaction`.
5. Set the document root to public/. Configure requests to public/index.php and writable storage/ and bootstrap/cache/. File sessions/cache require persistent storage; this setup targets a single instance.
6. Run `php artisan optimize`, enable HTTPS, verify all four routes and CSS, confirm at least five records per directory, then record the real URL in SUBMISSION.md.

Use the host's web server in production. Artisan serve is for local development. Do not publish secrets or use destructive reset commands on an existing database.

Official references: [migrations](https://laravel.com/docs/13.x/migrations), [Eloquent](https://laravel.com/docs/13.x/eloquent), [seeding](https://laravel.com/docs/13.x/seeding).
