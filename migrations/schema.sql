-- Migrations: corrected schema with PRIMARY KEY and AUTO_INCREMENT

CREATE TABLE `app_contact_link` (
  `contact_link_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `type` enum('email','web','phone','social') NOT NULL,
  `address_value` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`contact_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `app_seo` (
  `seo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) UNSIGNED NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `logo_image_url` varchar(255) DEFAULT NULL,
  `og_image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `app_slider` (
  `slider_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) UNSIGNED NOT NULL,
  `short_text` varchar(100) DEFAULT NULL,
  `medium_text` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `app_theme` (
  `theme_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) UNSIGNED NOT NULL,
  `background_color` varchar(20) DEFAULT '#ffffff',
  `box_color` varchar(20) DEFAULT '#f0f0f0',
  `header_color` varchar(20) DEFAULT '#333333',
  `footer_color` varchar(20) DEFAULT '#222222',
  `site_color` varchar(20) DEFAULT '#007bff',
  `hover_text_color` varchar(20) DEFAULT '#ffffff',
  `side_banner_color` varchar(20) DEFAULT '#e0e0e0',
  `font_family` varchar(100) DEFAULT 'Arial',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `AppInfo` (
  `AppId` INT AUTO_INCREMENT PRIMARY KEY,
  `AppName` VARCHAR(100) NOT NULL,
  `AppTitle` VARCHAR(200) NOT NULL,
  `LogoUrl` VARCHAR(500) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
