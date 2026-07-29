Added Slider Manager UI (admin/slider_manager.php), API (api/slider.php), model updates (src/models/Slider.php) and client JS.

- Use /admin/slider_manager.php?instance_id=N to manage slides for instance N.
- Upload images via drag-drop area or the modal upload button. Upload uses /api/upload.php and auto-creates a slide for each dropped image.
- API: GET lists slides; POST creates/updates; DELETE removes a slide (also deletes the image file if present); POST?action=reorder reorders slides.
- Seed defaults already include a default slide for instance 1 in migrations/seed_defaults.sql
