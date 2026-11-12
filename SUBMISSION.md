# Assignment submission

Repository: [MariusKii/laravel-pos](https://github.com/MariusKii/laravel-pos)

## What to submit

1. Copy the repository link above into your assignment submission.
2. Complete your assignment cover sheet with your student/group name, section, and professor.
3. The four screenshots are in `screenshots/` and displayed in README.
4. The assignment also requests a hosted working URL. Deploy to a PHP-capable host using README's deployment instructions, then replace the hosted URL placeholder with the verified HTTPS address.

GitHub stores the source code; it does not run this Laravel application. GitHub Pages cannot execute PHP. Hosting is not yet complete.

## Running a downloaded copy

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Open http://127.0.0.1:8000. No database setup or npm build is required.

## Updating the GitHub submission

From the local laravel-pos folder, after making and reviewing changes:

```sh
php artisan test --compact
git status --short
git add .
git diff --cached --stat
git diff --cached
git commit -m "Update assignment submission"
git push origin main
```

Do not force-add `.env`, `vendor/`, `node_modules/`, generated logs/caches, or SQLite files. Keep `.env.example`, `composer.lock`, migrations, seeders, controllers, views, CSS, tests, and screenshots in the repository.
