<?php
// Simple admin dashboard shell with links to editors
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();

// Instance selector: list distinct instance_ids from app_theme (fallback to 1)
$instances = [];
try {
    $stmt = $pdo->query('SELECT DISTINCT instance_id FROM app_theme UNION SELECT DISTINCT instance_id FROM app_slider UNION SELECT DISTINCT instance_id FROM app_seo UNION SELECT DISTINCT instance_id FROM app_contact_link');
    $instances = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    // ignore - show default
}
if (empty($instances)) {
    $instances = [1];
}
$selectedInstance = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : $instances[0];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="/assets/css/admin.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand bg-light border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">UI Manager - Admin</a>
    <div class="ms-auto d-flex align-items-center">
      <form method="get" class="me-3">
        <label for="instance_id" class="form-label me-2 mb-0">Instance</label>
        <select id="instance_id" name="instance_id" onchange="this.form.submit()" class="form-select form-select-sm">
          <?php foreach ($instances as $ins): ?>
            <option value="<?php echo (int)$ins; ?>" <?php echo ((int)$ins === (int)$selectedInstance) ? 'selected' : ''; ?>>Instance <?php echo (int)$ins; ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a href="/index.php?instance_id=<?php echo (int)$selectedInstance; ?>" class="btn btn-outline-secondary btn-sm ms-2">View site</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="row">
    <div class="col-md-3">
      <div class="list-group">
        <a href="/admin/theme_editor.php?instance_id=<?php echo (int)$selectedInstance; ?>" class="list-group-item list-group-item-action">🎨 App Theme</a>
        <a href="#" class="list-group-item list-group-item-action disabled">🖼️ App Slider (coming)</a>
        <a href="#" class="list-group-item list-group-item-action disabled">🔍 App SEO (coming)</a>
        <a href="#" class="list-group-item list-group-item-action disabled">📞 App Contact Links (coming)</a>
        <a href="#" class="list-group-item list-group-item-action disabled">⚙️ Structure</a>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card p-3">
        <h5>Welcome</h5>
        <p>Select a module from the left to edit settings for Instance <?php echo (int)$selectedInstance; ?>.</p>
        <p class="text-muted small">Changes will only affect the selected instance_id.</p>
      </div>
    </div>
  </div>
</div>

</body>
</html>
