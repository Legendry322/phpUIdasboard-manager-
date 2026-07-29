# PHP UI Dashboard Manager

This branch adds an admin dashboard scaffold and a sample landing page that reflects settings from the database (theme colors, sliders, SEO, contact links).

Quick start

1. Copy config.example.php to config.php and fill DB credentials (config.php is gitignored).
2. Import the database schema: `mysql -u root -p uimanger < migrations/schema.sql`
3. Place uploaded images (or sample slider images) into the `appimg/` folder. Ensure web server can serve files from that folder.
4. Visit `index.php` to see the sample landing page which reads settings from DB.

Files added
- migrations/schema.sql - corrected table definitions with primary keys and auto-increment
- src/Database.php - PDO wrapper (reads config.php or falls back to dbconnect.php if present)
- src/models/* - minimal models for Theme, Slider, Seo, ContactLink
- index.php - sample shop landing page that reflects DB settings
- config.example.php - copy to config.php
- .gitignore
- appimg/.gitkeep

Next steps
- Implement admin UI pages and API endpoints (theme editor, slider manager, SEO editor, contact links manager).
- Add upload handler and secure renaming for images.
