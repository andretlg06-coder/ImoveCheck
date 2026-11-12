<?php
require_once __DIR__ . '/api/auth.php';
if (!current_user($pdo)) { header('Location: login.php'); exit; }
$pageTitle = "ImoveCheck | Imóveis cadastrados";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/app.js" defer></script>
</head>
<body data-page="lista">
  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="index.php"><span class="brand-icon">▤</span><strong>ImoveCheck</strong></a>
      <span class="brand-subtitle">Sistema de Inspeção de Imóveis</span>
      <a class="home-link" href="#" id="logout-link">Sair ↗</a>
    </div>
  </header>

  <main class="container">
    <div class="page-heading">
      <div>
        <h1>Imóveis cadastrados</h1>
        <p class="muted">Gerencie os imóveis registrados no sistema.</p>
      </div>
      <a class="button primary" href="cadastrar.php">＋ Cadastrar imóvel</a>
    </div>

    <div id="feedback" class="feedback" role="status" aria-live="polite"></div>
    <section id="property-list" class="property-list" aria-live="polite">
      <div class="empty-state">Carregando imóveis...</div>
    </section>
  </main>
</body>
</html>
