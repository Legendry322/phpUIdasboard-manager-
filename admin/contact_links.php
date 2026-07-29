<?php
require_once __DIR__ . '/../src/Database.php';
$pdo = Database::getConnection();
$instance_id = isset($_GET['instance_id']) ? (int)$_GET['instance_id'] : 1;

require_once __DIR__ . '/../src/models/ContactLink.php';
$links = ContactLink::getAll($pdo, $instance_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Links - Instance <?php echo $instance_id; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Contact Links — Instance <?php echo $instance_id; ?></h4>
    <a href="/admin/index.php?instance_id=<?php echo $instance_id; ?>" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="mb-3">
    <button id="addRowBtn" class="btn btn-sm btn-primary">Add Link</button>
    <button id="saveOrderBtn" class="btn btn-sm btn-outline-primary ms-2">Save Order</button>
  </div>

  <div class="card p-3">
    <div id="linksList">
      <table class="table table-sm table-borderless align-middle" id="linksTable">
        <thead>
          <tr>
            <th style="width:36px;"></th>
            <th>Name</th>
            <th>Address</th>
            <th>Type</th>
            <th>Value</th>
            <th style="width:120px;">Active</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody id="linksTbody">
          <?php foreach ($links as $l): ?>
            <tr data-id="<?php echo (int)$l['contact_link_id']; ?>">
              <td class="drag-handle"><i class="fa fa-grip-vertical"></i></td>
              <td class="name"><?php echo htmlspecialchars($l['name']); ?></td>
              <td class="address"><?php echo htmlspecialchars($l['address'] ?? ''); ?></td>
              <td class="type"><?php echo htmlspecialchars($l['type']); ?></td>
              <td class="value"><?php echo htmlspecialchars($l['address_value']); ?></td>
              <td class="is_active"><?php echo $l['is_active'] ? 'Yes' : 'No'; ?></td>
              <td>
                <button class="btn btn-sm btn-outline-secondary editBtn">Edit</button>
                <button class="btn btn-sm btn-outline-danger deleteBtn">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal" tabindex="-1" id="editModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Contact Link</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="linkForm">
            <input type="hidden" name="contact_link_id" />
            <input type="hidden" name="instance_id" value="<?php echo $instance_id; ?>" />
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input class="form-control" name="name" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Address (optional)</label>
              <input class="form-control" name="address" />
            </div>
            <div class="mb-3">
              <label class="form-label">Type</label>
              <select class="form-select" name="type">
                <option value="email">email</option>
                <option value="web">web</option>
                <option value="phone">phone</option>
                <option value="social">social</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Value</label>
              <input class="form-control" name="address_value" required />
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" checked />
              <label class="form-check-label" for="isActiveCheck">Active</label>
            </div>
          </form>
          <div id="modalAlert"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="saveLinkBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="/assets/js/contact-links.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
