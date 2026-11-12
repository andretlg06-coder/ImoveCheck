<?php
require_once __DIR__ . '/api/auth.php';
if (current_user($pdo)) { header('Location: index.php'); exit; }
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar | ImoveCheck</title><link rel="stylesheet" href="assets/css/auth.css"><script src="assets/js/auth.js" defer></script></head>
<body data-auth-page="login">
<header class="topbar"><div class="topbar-inner"><a class="brand" href="login.php"><span class="brand-icon">▤</span><strong>ImoveCheck</strong></a><span class="brand-divider"></span><span class="brand-subtitle">Sistema de Inspeção de Imóveis</span><a class="help" href="mailto:suporte@imovecheck.local">Precisa de ajuda? <b>ⓘ</b></a></div></header>
<main class="auth-main"><section class="auth-card"><h1>Bem-vindo de volta</h1><p class="subtitle">Acesse sua conta para gerenciar suas vistorias.</p>
<form id="login-form" novalidate><label for="email">E-mail</label><div class="input-wrap"><span>✉</span><input id="email" name="email" type="email" autocomplete="username" placeholder="seu@email.com" required maxlength="190"></div>
<div class="label-row"><label for="senha">Senha</label><a href="recuperar-senha.php">Esqueceu a senha?</a></div><div class="input-wrap"><span>♙</span><input id="senha" name="senha" type="password" autocomplete="current-password" placeholder="Digite sua senha" required><button class="toggle-password" type="button" aria-label="Mostrar senha" data-toggle="senha">◎</button></div>
<label class="remember"><input type="checkbox" name="remember"> <span>Manter conectado</span></label><div class="notice" id="feedback" role="alert"></div><button class="submit" type="submit"><span>Entrar</span><b>→</b></button></form>
<div class="auth-bottom">Ainda não tem uma conta? <a href="cadastro-usuario.php">Cadastre-se</a></div></section><p class="secure">♢ Seus dados protegidos, do início ao fim.</p></main>
<footer class="footer"><span>© 2026 ImoveCheck. Todos os direitos reservados.</span></footer></body></html>
