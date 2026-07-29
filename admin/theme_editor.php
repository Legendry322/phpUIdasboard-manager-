<?php
// Theme editor UI
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();
$instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;

// Available fonts (Google fonts + system)
$fonts = ['Arial','Roboto','Open Sans','Lato','Poppins','Montserrat','Inter'];

// Load existing theme for preview
require_once __DIR__ . '/../src/models/Theme.php';
$theme = Theme::getCurrent($pdo, $instance_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Theme Editor - Instance <?php echo $instance_id; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="/assets/css/admin.css" rel="stylesheet">
  <?php if (in_array($theme['font_family'] ?? '', $fonts)): ?>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=<?php echo str_replace(' ', '+', $theme['font_family']); ?>:wght@300;400;600;700&display=swap">
  <?php endif; ?>
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>App Theme Editor — Instance <?php echo $instance_id; ?></h4>
    <a href="/admin/index.php?instance_id=<?php echo $instance_id; ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <form id="themeForm">
        <input type="hidden" name="instance_id" value="<?php echo $instance_id; ?>">

        <div class="mb-3">
          <label class="form-label">Background Color</label>
          <input class="form-control form-control-color" type="color" name="background_color" value="<?php echo htmlspecialchars($theme['background_color'] ?? '#ffffff'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Box Color</label>
          <input class="form-control form-control-color" type="color" name="box_color" value="<?php echo htmlspecialchars($theme['box_color'] ?? '#f0f0f0'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Header Color</label>
          <input class="form-control form-control-color" type="color" name="header_color" value="<?php echo htmlspecialchars($theme['header_color'] ?? '#333333'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Footer Color</label>
          <input class="form-control form-control-color" type="color" name="footer_color" value="<?php echo htmlspecialchars($theme['footer_color'] ?? '#222222'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Site Color</label>
          <input class="form-control form-control-color" type="color" name="site_color" value="<?php echo htmlspecialchars($theme['site_color'] ?? '#007bff'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Hover Text Color</label>
          <input class="form-control form-control-color" type="color" name="hover_text_color" value="<?php echo htmlspecialchars($theme['hover_text_color'] ?? '#ffffff'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Side Banner Color</label>
          <input class="form-control form-control-color" type="color" name="side_banner_color" value="<?php echo htmlspecialchars($theme['side_banner_color'] ?? '#e0e0e0'); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Font Family</label>
          <select class="form-select" name="font_family" id="fontSelect">
            <?php foreach ($fonts as $f): ?>
              <option value="<?php echo htmlspecialchars($f); ?>" <?php echo (($theme['font_family'] ?? '') === $f) ? 'selected' : ''; ?>><?php echo htmlspecialchars($f); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-flex gap-2">
          <button id="saveBtn" type="button" class="btn btn-primary">Save</button>
          <button id="saveDraftBtn" type="button" class="btn btn-outline-secondary">Save Draft</button>
          <div id="saveSpinner" class="spinner-border text-primary d-none" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div id="alertBox" class="mt-3"></div>
      </form>
    </div>

    <div class="col-lg-6">
      <h6>Live Preview</h6>
      <div class="p-3" id="previewArea" style="border-radius:8px;">
        <div id="previewHeader" class="p-3 mb-3 text-white" style="border-radius:6px;">Header</div>
        <div class="d-flex gap-3">
          <div id="previewCard" class="p-3 site-card" style="flex:1;">
            <h5>Card Title</h5>
            <p class="small text-muted">This is a preview of a content card.</p>
            <button class="btn btn-primary">Action</button>
          </div>
          <div id="previewSide" class="p-3 side-banner" style="width:200px;">
            Side Banner
          </div>
        </div>
        <footer id="previewFooter" class="mt-3 p-3 text-white" style="border-radius:6px;">Footer</footer>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/assets/js/theme-editor.js"></script>
</body>
</html>
