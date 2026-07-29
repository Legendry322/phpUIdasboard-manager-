<?php
// Sample landing page that reflects settings from DB (theme colors, slider images, seo logo, contact links)
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/models/Theme.php';
require_once __DIR__ . '/src/models/Slider.php';
require_once __DIR__ . '/src/models/Seo.php';
require_once __DIR__ . '/src/models/ContactLink.php';

$pdo = Database::getConnection();
$theme = Theme::getCurrent($pdo);
$sliders = Slider::getActive($pdo);
$seo = Seo::get($pdo);
$contacts = ContactLink::getActive($pdo);

// Simple font map (if it's a Google font name, include link)
$googleFonts = ['Roboto','Open Sans','Lato','Poppins','Montserrat','Inter'];
$fontFamily = $theme['font_family'] ?? 'Arial';
$fontLink = '';
if (in_array($fontFamily, $googleFonts)) {
    $fontLink = 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', $fontFamily) . ':wght@300;400;600;700&display=swap';
}

// Helper: ensure image URL is safe and relative to /appimg/
function imageUrl($url) {
    if (!$url) return '';
    // if it's already an absolute URL, return as-is
    if (preg_match('#^https?://#i', $url)) return $url;
    // otherwise assume it's stored in /appimg/
    return '/appimg/' . ltrim($url, '/');
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($seo['meta_title'] ?? 'Sample Shop'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($seo['meta_description'] ?? 'A sample shop landing page'); ?>">
  <?php if (!empty($seo['meta_keywords']) && is_array($seo['meta_keywords'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars(implode(', ', $seo['meta_keywords'])); ?>">
  <?php endif; ?>
  <?php if ($fontLink): ?>
    <link rel="stylesheet" href="<?php echo $fontLink; ?>">
  <?php endif; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --bg-color: <?php echo htmlspecialchars($theme['background_color'] ?? '#ffffff'); ?>;
      --box-color: <?php echo htmlspecialchars($theme['box_color'] ?? '#f0f0f0'); ?>;
      --header-color: <?php echo htmlspecialchars($theme['header_color'] ?? '#333333'); ?>;
      --footer-color: <?php echo htmlspecialchars($theme['footer_color'] ?? '#222222'); ?>;
      --site-color: <?php echo htmlspecialchars($theme['site_color'] ?? '#007bff'); ?>;
      --hover-text: <?php echo htmlspecialchars($theme['hover_text_color'] ?? '#ffffff'); ?>;
      --side-banner: <?php echo htmlspecialchars($theme['side_banner_color'] ?? '#e0e0e0'); ?>;
      font-family: '<?php echo htmlspecialchars($fontFamily); ?>', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }
    body { background: var(--bg-color); }
    .site-header { background: var(--header_color); color: var(--hover-text); }
    .site-footer { background: var(--footer-color); color: var(--hover-text); padding: 1rem 0; }
    .site-card { background: var(--box-color); border-radius: 8px; padding: 1rem; }
    .btn-primary { background: var(--site-color); border-color: var(--site-color); }
    .side-banner { background: var(--side-banner); padding: 1rem; border-radius: 8px; }
  </style>
</head>
<body>

<header class="site-header py-3">
  <div class="container d-flex align-items-center">
    <div class="me-3">
      <?php if (!empty($seo['logo_image_url'])): ?>
        <img src="<?php echo htmlspecialchars(imageUrl($seo['logo_image_url'])); ?>" alt="Logo" style="height:48px; object-fit:contain;">
      <?php else: ?>
        <h4 class="mb-0 text-white"><?php echo htmlspecialchars($seo['meta_title'] ?? 'Sample Shop'); ?></h4>
      <?php endif; ?>
    </div>
    <nav class="ms-auto">
      <?php foreach ($contacts as $c): ?>
        <?php $type = $c['type']; $val = $c['address_value']; ?>
        <a class="text-white ms-3" href="<?php echo $type === 'email' ? 'mailto:'.htmlspecialchars($val) : htmlspecialchars($val); ?>">
          <?php if ($type === 'phone'): ?><i class="fa-solid fa-phone"></i>
          <?php elseif ($type === 'email'): ?><i class="fa-solid fa-envelope"></i>
          <?php elseif ($type === 'social'): ?><i class="fa-solid fa-hashtag"></i>
          <?php else: ?><i class="fa-solid fa-link"></i>
          <?php endif; ?>
          <span class="ms-1 d-none d-md-inline"><?php echo htmlspecialchars($c['name']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>

<main class="container my-4">
  <div class="row">
    <div class="col-lg-8">
      <!-- Slider -->
      <?php if (!empty($sliders)): ?>
      <div id="mainCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php foreach ($sliders as $i => $s): ?>
            <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
              <img src="<?php echo htmlspecialchars(imageUrl($s['img_url'])); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($s['short_text']); ?>">
              <div class="carousel-caption d-none d-md-block">
                <h5><?php echo htmlspecialchars($s['short_text']); ?></h5>
                <p><?php echo htmlspecialchars($s['medium_text']); ?></p>
                <?php if (!empty($s['link_url'])): ?>
                  <a href="<?php echo htmlspecialchars($s['link_url']); ?>" class="btn btn-primary">Shop now</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <?php endif; ?>

      <!-- Sample products grid -->
      <div class="row g-3">
        <?php for ($i=1;$i<=6;$i++): ?>
        <div class="col-6 col-md-4">
          <div class="site-card">
            <div style="height:140px; background:linear-gradient(180deg, rgba(0,0,0,0.03), rgba(0,0,0,0.015)); display:flex; align-items:center; justify-content:center; border-radius:6px;">
              <img src="https://picsum.photos/seed/product<?php echo $i;?>/300/200" alt="product" style="max-width:100%; max-height:100%; object-fit:cover;">
            </div>
            <h6 class="mt-2">Product <?php echo $i; ?></h6>
            <p class="text-muted small">Short description here</p>
            <div class="d-flex justify-content-between align-items-center">
              <strong>$<?php echo rand(10,99); ?>.00</strong>
              <button class="btn btn-sm btn-primary">Add to cart</button>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>

    </div>
    <div class="col-lg-4">
      <div class="side-banner mb-4">
        <h5>About Shop</h5>
        <p class="small text-muted">This is a sample landing area. Theme colors, fonts and slider images are pulled from the database to demonstrate reflectivity.</p>
        <a href="#" class="btn btn-primary">Explore</a>
      </div>

      <div class="site-card">
        <h6>Contact</h6>
        <ul class="list-unstyled mb-0">
          <?php foreach ($contacts as $c): ?>
            <li>
              <strong><?php echo htmlspecialchars($c['name']); ?>:</strong>
              <span class="small text-muted"> <?php echo htmlspecialchars($c['address_value']); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</main>

<footer class="site-footer mt-4">
  <div class="container text-center">
    <small>&copy; <?php echo date('Y'); ?> Sample Shop</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
