<?php
// SEO editor UI
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();
$instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;

require_once __DIR__ . '/../src/models/Seo.php';
$seo = Seo::get($pdo, $instance_id);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SEO Editor - Instance <?php echo $instance_id; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>SEO Editor — Instance <?php echo $instance_id; ?></h4>
    <a href="/admin/index.php?instance_id=<?php echo $instance_id; ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <form id="seoForm">
        <input type="hidden" name="instance_id" value="<?php echo $instance_id; ?>">

        <div class="mb-3">
          <label class="form-label">Meta Title</label>
          <input class="form-control" type="text" name="meta_title" maxlength="255" value="<?php echo htmlspecialchars($seo['meta_title'] ?? ''); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Meta Description</label>
          <textarea class="form-control" name="meta_description" rows="4"><?php echo htmlspecialchars($seo['meta_description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Meta Keywords (comma-separated)</label>
          <input class="form-control" type="text" name="meta_keywords" id="metaKeywords" value="<?php echo htmlspecialchars(is_array($seo['meta_keywords']) ? implode(', ', $seo['meta_keywords']) : ($seo['meta_keywords'] ?? '')); ?>">
          <div class="form-text">Enter keywords separated by commas. They will be saved as a JSON array.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Logo Image</label>
          <div class="d-flex gap-2 align-items-center">
            <input type="file" id="logoFile" accept="image/*" class="form-control form-control-sm" />
            <button type="button" id="uploadLogoBtn" class="btn btn-outline-secondary btn-sm">Upload</button>
            <div id="logoPreview" style="width:72px; height:48px; overflow:hidden; border-radius:6px; background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
              <?php if (!empty($seo['logo_image_url'])): ?>
                <img src="/appimg/<?php echo htmlspecialchars($seo['logo_image_url']); ?>" alt="logo" style="max-width:100%; max-height:100%;">
              <?php else: ?>
                <span class="text-muted small">No logo</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">OG Image</label>
          <div class="d-flex gap-2 align-items-center">
            <input type="file" id="ogFile" accept="image/*" class="form-control form-control-sm" />
            <button type="button" id="uploadOgBtn" class="btn btn-outline-secondary btn-sm">Upload</button>
            <div id="ogPreview" style="width:120px; height:80px; overflow:hidden; border-radius:6px; background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
              <?php if (!empty($seo['og_image_url'])): ?>
                <img src="/appimg/<?php echo htmlspecialchars($seo['og_image_url']); ?>" alt="og" style="max-width:100%; max-height:100%;">
              <?php else: ?>
                <span class="text-muted small">No OG</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button id="saveBtn" type="button" class="btn btn-primary">Save</button>
          <button id="saveDraftBtn" type="button" class="btn btn-outline-secondary">Save Draft</button>
          <div id="saveSpinner" class="spinner-border text-primary d-none" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div id="alertBox" class="mt-3"></div>
      </form>
    </div>

    <div class="col-lg-5">
      <h6>Live Preview</h6>
      <div class="p-3 site-card" id="seoPreview">
        <div class="d-flex align-items-center mb-3">
          <div style="width:60px; height:40px; margin-right:12px;">
            <img id="previewLogo" src="<?php echo !empty($seo['logo_image_url']) ? '/appimg/'.htmlspecialchars($seo['logo_image_url']) : ''; ?>" style="max-width:100%; max-height:100%; object-fit:contain;" alt="logo">
          </div>
          <div>
            <h5 id="previewTitle"><?php echo htmlspecialchars($seo['meta_title'] ?? 'Sample Shop'); ?></h5>
            <p id="previewDesc" class="small text-muted mb-0"><?php echo htmlspecialchars($seo['meta_description'] ?? 'A sample shop landing page'); ?></p>
          </div>
        </div>
        <div class="mt-3">
          <img id="previewOg" src="<?php echo !empty($seo['og_image_url']) ? '/appimg/'.htmlspecialchars($seo['og_image_url']) : ''; ?>" alt="og" style="width:100%; height:auto; max-height:180px; object-fit:cover; display:<?php echo !empty($seo['og_image_url']) ? 'block' : 'none'; ?>;">
        </div>
        <div class="mt-2">
          <div class="small text-muted">Keywords: <span id="previewKeywords"><?php echo htmlspecialchars(is_array($seo['meta_keywords']) ? implode(', ', $seo['meta_keywords']) : ($seo['meta_keywords'] ?? '')); ?></span></div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/assets/js/seo-editor.js"></script>
</body>
</html>
