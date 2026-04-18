This is a project to clone Netflix.

Current structure:

- `*.php` in the project root: public web entry points served by Apache.
- `includes/`: shared config, layout partials, and runtime helpers.
- `includes/classes/`: PHP domain and service classes.
- `ajax/`: AJAX endpoints.
- `assets/`: CSS, JS, and images.
- `entities/`: app media assets.
- `Trailer/`: poster CSV input and imported trailer artifacts.
- `scripts/poster/`: maintenance scripts for poster/trailer import and report generation.
- `database/`: SQL dump and schema artifacts.
- `tmp/`: temporary debug and scratch files.
- `ai_service/`: Python-side helper service.

Poster tooling commands:

- `C:\xampp\php\php.exe scripts\poster\sync_poster_movies.php`
- `C:\xampp\php\php.exe scripts\poster\generate_poster_category_sql.php`
- `C:\xampp\php\php.exe scripts\poster\generate_poster_media_type_sql.php`
- `C:\xampp\php\php.exe scripts\poster\export_imported_movies_sql.php`
