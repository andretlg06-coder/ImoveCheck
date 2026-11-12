<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $isHttps,
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}

function json_response(int $status, array $payload): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function require_csrf(): void {
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$sent || !hash_equals(csrf_token(), $sent)) {
        json_response(419, ['success'=>false, 'message'=>'Sua sessão expirou. Atualize a página e tente novamente.']);
    }
}
function current_user(PDO $pdo): ?array {
    if (!empty($_SESSION['user_id'])) {
        $stmt=$pdo->prepare('SELECT id,nome,email FROM usuarios WHERE id=? AND ativo=1');
        $stmt->execute([(int)$_SESSION['user_id']]);
        $u=$stmt->fetch();
        if ($u) return $u;
        unset($_SESSION['user_id']);
    }
    $token=$_COOKIE['imovecheck_remember'] ?? '';
    if ($token && preg_match('/^[a-f0-9]{64}$/', $token)) {
        $hash=hash('sha256',$token);
        $stmt=$pdo->prepare('SELECT t.id,t.usuario_id,u.nome,u.email FROM tokens_acesso t JOIN usuarios u ON u.id=t.usuario_id WHERE t.token_hash=? AND t.expires_at>NOW() AND u.ativo=1');
        $stmt->execute([$hash]); $row=$stmt->fetch();
        if ($row) {
            session_regenerate_id(true);
            $_SESSION['user_id']=(int)$row['usuario_id'];
            $pdo->prepare('UPDATE tokens_acesso SET last_used_at=NOW() WHERE id=?')->execute([$row['id']]);
            return ['id'=>$row['usuario_id'],'nome'=>$row['nome'],'email'=>$row['email']];
        }
        setcookie('imovecheck_remember','',['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']),'httponly'=>true,'samesite'=>'Lax']);
    }
    return null;
}
function require_user(PDO $pdo): array {
    $user=current_user($pdo);
    if (!$user) json_response(401,['success'=>false,'message'=>'Faça login para continuar.']);
    return $user;
}
function create_remember_token(PDO $pdo, int $userId): void {
    $raw=bin2hex(random_bytes(32));
    $pdo->prepare('INSERT INTO tokens_acesso (usuario_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 30 DAY))')
        ->execute([$userId,hash('sha256',$raw)]);
    setcookie('imovecheck_remember',$raw,[
        'expires'=>time()+60*60*24*30,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off',
        'httponly'=>true,'samesite'=>'Lax'
    ]);
}
