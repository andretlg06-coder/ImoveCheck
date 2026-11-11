const API_URL = 'api/imoveis.php';

const escapeHTML = (value = '') => String(value).replace(/[&<>"']/g, char => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
}[char]));

function showMessage(element, message, type = 'error') {
  if (!element) return;
  element.textContent = message;
  element.className = `feedback visible ${type}`;
}

async function readJson(response) {
  const data = await response.json().catch(() => ({}));
  if (!response.ok || data.success === false) throw new Error(data.message || 'Ocorreu um erro inesperado.');
  return data;
}

if (document.body.dataset.page === 'cadastro') {
  const form = document.querySelector('#property-form');
  const input = document.querySelector('#fotos');
  const preview = document.querySelector('#photo-preview');
  const feedback = document.querySelector('#form-feedback');
  const saveButton = document.querySelector('#save-button');
  let selectedFiles = [];

  function renderPhotos() {
    preview.innerHTML = '';
    selectedFiles.forEach((file, index) => {
      const url = URL.createObjectURL(file);
      const item = document.createElement('div');
      item.className = 'photo-item';
      item.innerHTML = `<img src="${url}" alt="${escapeHTML(file.name)}"><button type="button" class="remove-photo" aria-label="Remover foto ${index + 1}">×</button><span>${escapeHTML(file.name)}</span>`;
      item.querySelector('button').addEventListener('click', () => {
        selectedFiles.splice(index, 1);
        renderPhotos();
      });
      preview.appendChild(item);
    });
  }

  input.addEventListener('change', () => {
    const incoming = Array.from(input.files || []);
    const combined = [...selectedFiles, ...incoming];
    if (combined.length > 20) {
      showMessage(feedback, 'Você pode selecionar no máximo 20 fotos.');
      return;
    }
    const invalid = incoming.find(file => !['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024);
    if (invalid) {
      showMessage(feedback, `A foto "${invalid.name}" é inválida ou ultrapassa 5 MB.`);
      input.value = '';
      return;
    }
    selectedFiles = combined;
    input.value = '';
    feedback.className = 'feedback';
    renderPhotos();
  });

  form.addEventListener('submit', async event => {
    event.preventDefault();
    feedback.className = 'feedback';
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    const address = form.endereco.value.trim();
    if (!address) {
      document.querySelector('[data-error-for="endereco"]').textContent = 'O endereço é obrigatório.';
      form.endereco.focus();
      return;
    }

    const data = new FormData();
    data.append('endereco', address);
    data.append('proprietario', form.proprietario.value.trim());
    data.append('inquilino', form.inquilino.value.trim());
    selectedFiles.forEach(file => data.append('fotos[]', file));

    saveButton.disabled = true;
    saveButton.innerHTML = '<span class="spinner"></span> Salvando...';
    try {
      const response = await fetch(API_URL, { method: 'POST', body: data });
      const result = await readJson(response);
      window.location.href = `index.php?sucesso=${encodeURIComponent(result.message)}`;
    } catch (error) {
      showMessage(feedback, error.message);
      saveButton.disabled = false;
      saveButton.innerHTML = '<span>▣</span> Salvar';
    }
  });
}

if (document.body.dataset.page === 'lista') {
  const list = document.querySelector('#property-list');
  const feedback = document.querySelector('#feedback');

  async function loadProperties() {
    try {
      const result = await readJson(await fetch(API_URL));
      if (!result.imoveis.length) {
        list.innerHTML = '<div class="empty-state"><div class="empty-icon">⌂</div><h2>Nenhum imóvel cadastrado</h2><p>Comece adicionando o primeiro imóvel ao sistema.</p><a class="button primary" href="cadastrar.php">Cadastrar imóvel</a></div>';
        return;
      }
      list.innerHTML = result.imoveis.map(item => `
        <article class="property-card">
          <div class="property-symbol">⌂</div>
          <div class="property-info">
            <h2>${escapeHTML(item.endereco)}</h2>
            <p><strong>Proprietário:</strong> ${escapeHTML(item.proprietario || 'Não informado')}</p>
            <p><strong>Inquilino:</strong> ${escapeHTML(item.inquilino || 'Não informado')}</p>
            <span class="photo-count">${Number(item.total_fotos)} foto(s)</span>
          </div>
          <button class="button danger" type="button" data-delete="${Number(item.id)}">Excluir</button>
        </article>`).join('');

      list.querySelectorAll('[data-delete]').forEach(button => {
        button.addEventListener('click', async () => {
          if (!confirm('Tem certeza de que deseja excluir este imóvel e suas fotos?')) return;
          button.disabled = true;
          try {
            const response = await fetch(`${API_URL}?id=${button.dataset.delete}`, { method: 'DELETE' });
            const data = await readJson(response);
            showMessage(feedback, data.message, 'success');
            loadProperties();
          } catch (error) {
            showMessage(feedback, error.message);
            button.disabled = false;
          }
        });
      });
    } catch (error) {
      list.innerHTML = `<div class="empty-state error-state">${escapeHTML(error.message)}<p>Confira se o Apache e o MySQL estão ativos e se o banco foi importado.</p></div>`;
    }
  }

  const params = new URLSearchParams(window.location.search);
  if (params.has('sucesso')) showMessage(feedback, params.get('sucesso'), 'success');
  loadProperties();
}
