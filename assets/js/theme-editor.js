// JS for Theme Editor: live preview and save via API
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('themeForm');
  const saveBtn = document.getElementById('saveBtn');
  const saveDraftBtn = document.getElementById('saveDraftBtn');
  const alertBox = document.getElementById('alertBox');
  const spinner = document.getElementById('saveSpinner');

  function updatePreview() {
    const fd = new FormData(form);
    const bg = fd.get('background_color');
    const box = fd.get('box_color');
    const header = fd.get('header_color');
    const footer = fd.get('footer_color');
    const site = fd.get('site_color');
    const hover = fd.get('hover_text_color');
    const side = fd.get('side_banner_color');
    const font = fd.get('font_family');

    const previewArea = document.getElementById('previewArea');
    previewArea.style.background = bg;
    document.getElementById('previewCard').style.background = box;
    document.getElementById('previewHeader').style.background = header;
    document.getElementById('previewFooter').style.background = footer;
    document.getElementById('previewSide').style.background = side;
    document.getElementById('previewHeader').style.color = hover;
    document.getElementById('previewFooter').style.color = hover;
    document.getElementById('previewCard').style.fontFamily = font;
    document.getElementById('previewArea').style.fontFamily = font;
    const buttons = previewArea.querySelectorAll('button');
    buttons.forEach(b => { b.style.background = site; b.style.borderColor = site; b.style.color = hover; });
  }

  form.addEventListener('input', function(e) {
    updatePreview();
  });

  async function saveTheme(draft = false) {
    alertBox.innerHTML = '';
    spinner.classList.remove('d-none');
    saveBtn.disabled = true; saveDraftBtn.disabled = true;

    const data = {};
    new FormData(form).forEach((v,k) => data[k]=v);
    if (draft) data._draft = 1;

    try {
      const res = await axios.post('/api/theme.php', data);
      if (res.data && res.data.success) {
        alertBox.innerHTML = '<div class="alert alert-success">Saved</div>';
      } else {
        alertBox.innerHTML = '<div class="alert alert-danger">Failed to save</div>';
      }
    } catch (err) {
      alertBox.innerHTML = '<div class="alert alert-danger">Save error</div>';
    } finally {
      spinner.classList.add('d-none');
      saveBtn.disabled = false; saveDraftBtn.disabled = false;
    }
  }

  saveBtn.addEventListener('click', function() { saveTheme(false); });
  saveDraftBtn.addEventListener('click', function() { saveTheme(true); });

  // Initial preview
  updatePreview();
});
