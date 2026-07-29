// JS for SEO Editor: fetch, image upload, live preview, save and auto-save
document.addEventListener('DOMContentLoaded', function() {
  const instanceId = document.querySelector('input[name="instance_id"]').value;
  const form = document.getElementById('seoForm');
  const saveBtn = document.getElementById('saveBtn');
  const saveDraftBtn = document.getElementById('saveDraftBtn');
  const alertBox = document.getElementById('alertBox');
  const spinner = document.getElementById('saveSpinner');

  const previewTitle = document.getElementById('previewTitle');
  const previewDesc = document.getElementById('previewDesc');
  const previewKeywords = document.getElementById('previewKeywords');
  const previewLogo = document.getElementById('previewLogo');
  const previewOg = document.getElementById('previewOg');

  const logoFile = document.getElementById('logoFile');
  const ogFile = document.getElementById('ogFile');
  const uploadLogoBtn = document.getElementById('uploadLogoBtn');
  const uploadOgBtn = document.getElementById('uploadOgBtn');

  let autoSaveTimer = null;

  function showAlert(type, msg) {
    alertBox.innerHTML = '<div class="alert alert-' + type + '">' + msg + '</div>';
  }

  function updatePreview() {
    const meta_title = form.elements['meta_title'].value;
    const meta_description = form.elements['meta_description'].value;
    const meta_keywords = form.elements['meta_keywords'].value;
    previewTitle.textContent = meta_title || 'Sample Shop';
    previewDesc.textContent = meta_description || 'A sample shop landing page';
    previewKeywords.textContent = meta_keywords || '';
    // logo and og previews updated on upload
  }

  form.addEventListener('input', function() {
    updatePreview();
    // debounce auto-save (3s)
    if (autoSaveTimer) clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => saveSeo(true), 3000);
  });

  async function uploadFile(fileInput, prefix, previewElem) {
    if (!fileInput.files || !fileInput.files[0]) return null;
    const f = fileInput.files[0];
    const fd = new FormData();
    fd.append('file', f);
    fd.append('prefix', prefix);
    try {
      const res = await axios.post('/api/upload.php', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      if (res.data && res.data.success) {
        const url = res.data.url;
        previewElem.src = '/appimg/' + url;
        previewElem.style.display = '';
        return url;
      } else {
        showAlert('danger', 'Upload failed');
        return null;
      }
    } catch (err) {
      showAlert('danger', 'Upload error');
      return null;
    }
  }

  uploadLogoBtn.addEventListener('click', async function() {
    const url = await uploadFile(logoFile, 'logo', previewLogo);
    if (url) {
      // set a hidden input? We'll set the form value via dataset
      form.dataset.logo = url;
      showAlert('success', 'Logo uploaded');
    }
  });

  uploadOgBtn.addEventListener('click', async function() {
    const url = await uploadFile(ogFile, 'og', previewOg);
    if (url) {
      form.dataset.og = url;
      showAlert('success', 'OG uploaded');
    }
  });

  async function saveSeo(draft = false) {
    spinner.classList.remove('d-none');
    saveBtn.disabled = true; saveDraftBtn.disabled = true;
    const payload = {
      instance_id: parseInt(instanceId, 10),
      meta_title: form.elements['meta_title'].value,
      meta_description: form.elements['meta_description'].value,
      meta_keywords: form.elements['meta_keywords'].value,
      logo_image_url: form.dataset.logo || (previewLogo.getAttribute('src') ? previewLogo.getAttribute('src').replace('/appimg/','') : null),
      og_image_url: form.dataset.og || (previewOg.getAttribute('src') ? previewOg.getAttribute('src').replace('/appimg/','') : null)
    };
    if (draft) payload._draft = 1;
    try {
      const res = await axios.post('/api/seo.php', payload);
      if (res.data && res.data.success) {
        showAlert('success', 'SEO saved');
      } else {
        showAlert('danger', 'Save failed');
      }
    } catch (err) {
      showAlert('danger', 'Save error');
    } finally {
      spinner.classList.add('d-none');
      saveBtn.disabled = false; saveDraftBtn.disabled = false;
    }
  }

  saveBtn.addEventListener('click', function() { saveSeo(false); });
  saveDraftBtn.addEventListener('click', function() { saveSeo(true); });

  // initial update
  updatePreview();
});
