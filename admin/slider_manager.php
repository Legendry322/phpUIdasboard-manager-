<?php
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();
$instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;

require_once __DIR__ . '/../src/models/Slider.php';
$sliders = Slider::getAll($pdo, $instance_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Slider Manager - Instance <?php echo $instance_id; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="/assets/css/admin.css" rel="stylesheet">
  <style>
    .thumb { width:120px; height:70px; object-fit:cover; border-radius:6px; }
    .drop-area { border:2px dashed #e9ecef; padding:12px; border-radius:8px; text-align:center; }
  </style>
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Slider Manager — Instance <?php echo $instance_id; ?></h4>
    <a href="/admin/index.php?instance_id=<?php echo $instance_id; ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="mb-3 d-flex gap-2">
    <button id="addSlideBtn" class="btn btn-primary btn-sm">Add Slide</button>
    <button id="saveOrderBtn" class="btn btn-outline-primary btn-sm">Save Order</button>
  </div>

  <div class="card p-3 mb-3">
    <h6>Upload Area</h6>
    <div class="drop-area" id="dropArea">
      Drag & drop images here or click to select (jpg/png/webp, &lt;2MB)
      <input type="file" id="fileInput" accept="image/*" multiple style="display:none;" />
    </div>
    <div id="uploadPreview" class="mt-3 d-flex gap-2 flex-wrap"></div>
  </div>

  <div class="card p-3">
    <table class="table table-sm table-borderless align-middle" id="slidesTable">
      <thead>
        <tr>
          <th style="width:36px;"></th>
          <th>Image</th>
          <th>Short Text</th>
          <th>Medium Text</th>
          <th>Link</th>
          <th style="width:80px;">Active</th>
          <th style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody id="slidesTbody">
        <?php foreach ($sliders as $s): ?>
          <tr data-id="<?php echo (int)$s['slider_id']; ?>">
            <td class="drag-handle"><i class="fa fa-grip-vertical"></i></td>
            <td><img src="/appimg/<?php echo htmlspecialchars($s['img_url']); ?>" class="thumb"></td>
            <td class="short_text"><?php echo htmlspecialchars($s['short_text']); ?></td>
            <td class="medium_text"><?php echo htmlspecialchars($s['medium_text']); ?></td>
            <td class="link_url"><?php echo htmlspecialchars($s['link_url'] ?? ''); ?></td>
            <td class="is_active"><?php echo $s['is_active'] ? 'Yes' : 'No'; ?></td>
            <td>
              <button class="btn btn-sm btn-outline-secondary editBtn">Edit</button>
              <button class="btn btn-sm btn-outline-danger deleteBtn">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal -->
  <div class="modal" tabindex="-1" id="slideModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Slide</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="slideForm">
            <input type="hidden" name="slider_id" />
            <input type="hidden" name="instance_id" value="<?php echo $instance_id; ?>" />
            <div class="mb-3">
              <label class="form-label">Short Text</label>
              <input class="form-control" name="short_text" maxlength="100" />
            </div>
            <div class="mb-3">
              <label class="form-label">Medium Text</label>
              <input class="form-control" name="medium_text" maxlength="255" />
            </div>
            <div class="mb-3">
              <label class="form-label">Link URL</label>
              <input class="form-control" name="link_url" />
            </div>
            <div class="mb-3">
              <label class="form-label">Image</label>
              <div class="d-flex gap-2 align-items-center">
                <input type="file" id="slideFile" accept="image/*" class="form-control form-control-sm" />
                <button type="button" id="uploadSlideBtn" class="btn btn-outline-secondary btn-sm">Upload</button>
                <div id="slidePreview" style="width:120px; height:70px; overflow:hidden; border-radius:6px; background:#f8f9fa; display:flex; align-items:center; justify-content:center;">
                  <span class="text-muted small">No image</span>
                </div>
              </div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSlide" checked />
              <label class="form-check-label" for="isActiveSlide">Active</label>
            </div>
          </form>
          <div id="modalAlert"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="saveSlideBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="/assets/js/slider-manager.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
