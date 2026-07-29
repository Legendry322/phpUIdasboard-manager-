-- Seed defaults for instances: theme, seo, slider, contact links

INSERT INTO app_theme (instance_id, background_color, box_color, header_color, footer_color, site_color, hover_text_color, side_banner_color, font_family)
VALUES (1, '#ffffff', '#ffffff', '#1f2937', '#0f172a', '#0d6efd', '#ffffff', '#f3f4f6', 'Poppins')
ON DUPLICATE KEY UPDATE theme_id = theme_id;

INSERT INTO app_seo (instance_id, meta_title, meta_description, meta_keywords, logo_image_url, og_image_url)
VALUES (1, 'Default Shop', 'Default shop description', JSON_ARRAY('shop','default','uimanager'), 'default_logo.png', 'default_og.png')
ON DUPLICATE KEY UPDATE seo_id = seo_id;

INSERT INTO app_slider (instance_id, short_text, medium_text, img_url, link_url, display_order, is_active)
VALUES (1, 'Welcome', 'Welcome to our default shop', 'default_slider1.jpg', '#', 0, 1)
ON DUPLICATE KEY UPDATE slider_id = slider_id;

INSERT INTO app_contact_link (instance_id, name, address, type, address_value, is_active, display_order)
VALUES (1, 'Support', NULL, 'email', 'support@example.com', 1, 0),
       (1, 'Phone', NULL, 'phone', '+1234567890', 1, 1),
       (1, 'Twitter', NULL, 'social', 'https://twitter.com/example', 1, 2)
ON DUPLICATE KEY UPDATE contact_link_id = contact_link_id;
