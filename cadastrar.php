<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Novo Imóvel | ImoveCheck</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/app.js" defer></script>
</head>
<body data-page="cadastro">
  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="index.php"><span class="brand-icon">▤</span><strong>ImoveCheck</strong></a>
      <span class="brand-subtitle">Sistema de Inspeção de Imóveis</span>
      <a class="home-link" href="index.php">⌂ <span>Início</span></a>
    </div>
  </header>

  <main class="container form-container">
    <div class="back-heading">
      <a class="back-link" href="index.php" aria-label="Voltar para início">←</a>
      <h1>Cadastrar Novo Imóvel</h1>
    </div>

    <form id="property-form" class="form-card" enctype="multipart/form-data" novalidate>
      <div class="field">
        <label for="endereco">Endereço <span class="required">*</span></label>
        <input id="endereco" name="endereco" type="text" placeholder="Rua, número, bairro, cidade" maxlength="255" required>
        <small class="field-error" data-error-for="endereco"></small>
      </div>

      <div class="form-grid">
        <div class="field">
          <label for="proprietario">Proprietário</label>
          <input id="proprietario" name="proprietario" type="text" placeholder="Nome do proprietário" maxlength="150">
        </div>
        <div class="field">
          <label for="inquilino">Inquilino</label>
          <input id="inquilino" name="inquilino" type="text" placeholder="Nome do inquilino" maxlength="150">
        </div>
      </div>

      <div class="field photos-field">
        <label for="fotos">Fotos Gerais do Imóvel</label>
        <label class="upload-button" for="fotos"><span aria-hidden="true">⇧</span> Adicionar Fotos</label>
        <input class="visually-hidden" id="fotos" name="fotos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
        <p class="hint">JPG, PNG ou WEBP. Até 5 MB por foto.</p>
        <div id="photo-preview" class="photo-preview"></div>
        <small class="field-error" data-error-for="fotos"></small>
      </div>

      <div id="form-feedback" class="feedback" role="alert" aria-live="polite"></div>
      <div class="form-actions">
        <button class="button primary" id="save-button" type="submit"><span>▣</span> Salvar</button>
        <a class="button secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </main>
</body>
</html>
