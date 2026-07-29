// JS for Slider Manager: drag/drop upload, CRUD, reorder
document.addEventListener('DOMContentLoaded', function() {
  const dropArea = document.getElementById('dropArea');
  const fileInput = document.getElementById('fileInput');
  const uploadPreview = document.getElementById('uploadPreview');
  const addSlideBtn = document.getElementById('addSlideBtn');
  const saveOrderBtn = document.getElementById('saveOrderBtn');
  const slidesTbody = document.getElementById('slidesTbody');
  const slideModalEl = document.getElementById('slideModal');
  const slideModal = new bootstrap.Modal(slideModalEl);
  const slideForm = document.getElementById('slideForm');
  const uploadSlideBtn = document.getElementById('uploadSlideBtn');
  const slideFile = document.getElementById('slideFile');
  const slidePreview = document.getElementById('slidePreview');
  const saveSlideBtn = document.getElementById('saveSlideBtn');
  const modalAlert = document.getElementById('modalAlert');

  const instanceId = slideForm.querySelector('input[name="instance_id"]').value;

  // Sortable
  const sortable = Sortable.create(slidesTbody, { handle: '.drag-handle', animation: 150 });

  function serializeOrder() { return Array.from(slidesTbody.querySelectorAll('tr')).map(tr => tr.dataset.id); }

  saveOrderBtn.addEventListener('click', async function() {
    const order = serializeOrder();
    try {
      const res = await axios.post('/api/slider.php?action=reorder', { instance_id: parseInt(instanceId,10), order });
      if (res.data && res.data.success) alert('Order saved'); else alert('Failed to save order');
    } catch (err) { alert('Error saving order'); }
  });

  // Drag & drop
  ['dragenter','dragover'].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.classList.add('bg-light'); }));
  ['dragleave','drop'].forEach(e => dropArea.addEventListener(e, ev => { ev.preventDefault(); dropArea.classList.remove('bg-light'); }));

  dropArea.addEventListener('drop', function(e) {
    const files = Array.from(e.dataTransfer.files || []);
    handleFiles(files);
  });

  dropArea.addEventListener('click', function() { fileInput.click(); });
  fileInput.addEventListener('change', function() { handleFiles(Array.from(fileInput.files || [])); });

  function handleFiles(files) {
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = function(ev) {
        const img = document.createElement('img'); img.src = ev.target.result; img.className = 'thumb';
        const wrap = document.createElement('div'); wrap.appendChild(img);
        uploadPreview.appendChild(wrap);
      };
      reader.readAsDataURL(file);
      // auto-upload
      uploadFile(file, 'slider').then(url => {
        // auto-create slide with uploaded image
        createSlide({ instance_id: parseInt(instanceId,10), img_url: url, short_text: '', medium_text: '', link_url: '', is_active: 1 });
      });
    });
  }

  async function uploadFile(file, prefix) {
    const fd = new FormData(); fd.append('file', file); fd.append('prefix', prefix);
    try {
      const res = await axios.post('/api/upload.php', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      if (res.data && res.data.success) return res.data.url; else throw new Error('Upload failed');
    } catch (err) { alert('Upload error'); return null; }
  }

  async function createSlide(payload) {
    try {
      const res = await axios.post('/api/slider.php', payload);
      if (res.data && res.data.success) location.reload(); else alert('Failed to create slide');
    } catch (err) { alert('Error creating slide'); }
  }

  addSlideBtn.addEventListener('click', function() { slideForm.reset(); slideForm.elements['slider_id'].value = ''; slidePreview.innerHTML = '<span class="text-muted small">No image</span>'; modalAlert.innerHTML=''; slideModal.show(); });

  // Edit / Delete handlers
  slidesTbody.addEventListener('click', function(e) {
    if (e.target.closest('.editBtn')) {
      const tr = e.target.closest('tr'); populateModalFromRow(tr); slideModal.show();
    } else if (e.target.closest('.deleteBtn')) {
      const tr = e.target.closest('tr'); if (confirm('Delete this slide?')) deleteSlide(tr.dataset.id);
    }
  });

  function populateModalFromRow(tr) {
    modalAlert.innerHTML='';
    slideForm.elements['slider_id'].value = tr.dataset.id;
    slideForm.elements['short_text'].value = tr.querySelector('.short_text').textContent.trim();
    slideForm.elements['medium_text'].value = tr.querySelector('.medium_text').textContent.trim();
    slideForm.elements['link_url'].value = tr.querySelector('.link_url').textContent.trim();
    const imgSrc = tr.querySelector('img.thumb').getAttribute('src');
    slidePreview.innerHTML = '<img src="'+imgSrc+'" style="max-width:100%; max-height:100%;">';
    slideForm.elements['is_active'].checked = tr.querySelector('.is_active').textContent.trim().toLowerCase() === 'yes';
  }

  uploadSlideBtn.addEventListener('click', async function() {
    if (!slideFile.files || !slideFile.files[0]) { modalAlert.innerHTML = '<div class="alert alert-warning">No file selected</div>'; return; }
    const url = await uploadFile(slideFile.files[0], 'slider');
    if (url) { slidePreview.innerHTML = '<img src="/appimg/'+url+'" style="max-width:100%; max-height:100%;">'; slideForm.dataset.img = url; }
  });

  saveSlideBtn.addEventListener('click', async function() {
    modalAlert.innerHTML = '';
    const payload = {};
    const fd = new FormData(slideForm); fd.forEach((v,k)=>payload[k]=v);
    payload.is_active = slideForm.elements['is_active'].checked ? 1 : 0;
    payload.instance_id = parseInt(instanceId,10);
    // if an uploaded image exists in dataset use it
    if (slideForm.dataset.img) payload.img_url = slideForm.dataset.img;
    try {
      const res = await axios.post('/api/slider.php', payload);
      if (res.data && res.data.success) location.reload(); else modalAlert.innerHTML = '<div class="alert alert-danger">Save failed</div>';
    } catch (err) { modalAlert.innerHTML = '<div class="alert alert-danger">Error saving</div>'; }
  });

  async function deleteSlide(id) {
    try {
      const res = await axios.delete('/api/slider.php', { data: { slider_id: parseInt(id,10), instance_id: parseInt(instanceId,10) } });
      if (res.data && res.data.success) location.reload(); else alert('Failed to delete');
    } catch (err) { alert('Error deleting'); }
  }

});
