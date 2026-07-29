// JS for Contact Links Manager: CRUD, reorder using SortableJS
document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.getElementById('linksTbody');
  const addRowBtn = document.getElementById('addRowBtn');
  const saveOrderBtn = document.getElementById('saveOrderBtn');
  const editModalEl = document.getElementById('editModal');
  const editModal = new bootstrap.Modal(editModalEl);
  const linkForm = document.getElementById('linkForm');
  const saveLinkBtn = document.getElementById('saveLinkBtn');
  const modalAlert = document.getElementById('modalAlert');

  const instanceId = linkForm.querySelector('input[name="instance_id"]').value;

  // make tbody sortable
  const sortable = Sortable.create(tbody, {
    handle: '.drag-handle',
    animation: 150
  });

  function serializeOrder() {
    return Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
  }

  saveOrderBtn.addEventListener('click', async function() {
    const order = serializeOrder();
    try {
      const res = await axios.post('/api/contact_links.php?action=reorder', { instance_id: parseInt(instanceId,10), order });
      if (res.data && res.data.success) {
        alert('Order saved');
      } else {
        alert('Failed to save order');
      }
    } catch (err) {
      alert('Error saving order');
    }
  });

  // Add new
  addRowBtn.addEventListener('click', function() {
    linkForm.reset();
    linkForm.elements['contact_link_id'].value = '';
    modalAlert.innerHTML = '';
    editModal.show();
  });

  // Edit
  tbody.addEventListener('click', function(e) {
    if (e.target.closest('.editBtn')) {
      const tr = e.target.closest('tr');
      populateModalFromRow(tr);
      editModal.show();
    } else if (e.target.closest('.deleteBtn')) {
      const tr = e.target.closest('tr');
      if (confirm('Delete this link?')) {
        deleteRow(tr.dataset.id);
      }
    }
  });

  function populateModalFromRow(tr) {
    modalAlert.innerHTML = '';
    linkForm.elements['contact_link_id'].value = tr.dataset.id;
    linkForm.elements['name'].value = tr.querySelector('.name').textContent.trim();
    linkForm.elements['address'].value = tr.querySelector('.address').textContent.trim();
    linkForm.elements['type'].value = tr.querySelector('.type').textContent.trim();
    linkForm.elements['address_value'].value = tr.querySelector('.value').textContent.trim();
    linkForm.elements['is_active'].checked = tr.querySelector('.is_active').textContent.trim().toLowerCase() === 'yes';
  }

  saveLinkBtn.addEventListener('click', async function() {
    modalAlert.innerHTML = '';
    const payload = {};
    const formData = new FormData(linkForm);
    formData.forEach((v,k) => payload[k]=v);
    payload.is_active = linkForm.elements['is_active'].checked ? 1 : 0;
    try {
      const res = await axios.post('/api/contact_links.php', payload);
      if (res.data && res.data.success) {
        // reload page to reflect changes
        location.reload();
      } else {
        modalAlert.innerHTML = '<div class="alert alert-danger">Failed to save</div>';
      }
    } catch (err) {
      modalAlert.innerHTML = '<div class="alert alert-danger">Error saving</div>';
    }
  });

  async function deleteRow(id) {
    try {
      const res = await axios.delete('/api/contact_links.php', { data: { contact_link_id: parseInt(id,10), instance_id: parseInt(instanceId,10) } });
      if (res.data && res.data.success) {
        location.reload();
      } else {
        alert('Failed to delete');
      }
    } catch (err) {
      alert('Error deleting');
    }
  }

});
