Added Contact Links Manager (admin/contact_links.php + API) and seed defaults.

- Use /admin/contact_links.php?instance_id=N to manage contact links for instance N.
- API: /api/contact_links.php supports GET (list), POST (create/update), DELETE, and POST?action=reorder for ordering.
- Seed file added: migrations/seed_defaults.sql to insert default attributes (theme, seo, slider, contact links) for instance 1.
