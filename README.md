Added combined instance settings endpoint: api/instance_settings.php

- GET /api/instance_settings.php?instance_id=N
  Returns JSON with theme, seo, sliders (active), and contact_links (active) for the given instance_id.

Example:
  GET /api/instance_settings.php?instance_id=1
  Response:
  {
    "success": true,
    "data": {
      "theme": { ... },
      "seo": { ... },
      "sliders": [ ... ],
      "contact_links": [ ... ]
    }
  }
