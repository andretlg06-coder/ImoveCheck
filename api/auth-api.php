<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

$action=$_GET['action'] ?? '';
$method=$_SERVER['REQUEST_METHOD'];

if ($action==='csrf' && $method==='GET') json_response(200,['success'=>true,'csrf'=>csrf_token()]);
if ($action==='me' && $method==='GET') {
    $u=current_user($pdo);
    json_response(200,['success'=>true,'authenticated'=>(bool)$u,'user'=>$u,'csrf'=>csrf_token()]);
}
if ($action==='register' && $method==='POST') {
    require_csrf();
    $body=json_decode(file_get_contents('php://input'),true) ?: [];
    $nome=trim((string)($body['nome']??'')); $email=strtolower(trim((string)($body['email']??'')));
    $senha=(string)($body['senha']??'');
    if (mb_strlen($nome)<2 || mb_strlen($nome)>100) json_response(422,['success'=>false,'message'=>'Informe um nome entre 2 e 100 caracteres.']);
    if (!filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($email)>190) json_response(422,['success'=>false,'message'=>'Informe um e-mail válido.']);
    if (strlen($senha)<8 || strlen($senha)>72) json_response(422,['success'=>false,'message'=>'A senha deve ter entre 8 e 72 caracteres.']);
    $stmt=$pdo->prepare('SELECT id FROM usuarios WHERE email=?'); $stmt->execute([$email]);
    if ($stmt->fetch()) json_response(409,['success'=>false,'message'=>'Este e-mail já possui cadastro.']);
    try {
        $stmt=$pdo->prepare('INSERT INTO usuarios (nome,email,senha_hash) VALUES (?,?,?)');
        $stmt->execute([$nome,$email,password_hash($senha,PASSWORD_DEFAULT)]);
    } catch (PDOException $e) { json_response(409,['success'=>false,'message'=>'Não foi possível cadastrar. Verifique se o e-mail já está em uso.']); }
    json_response(201,['success'=>true,'message'=>'Conta criada! Você já pode entrar.']);
}
if ($action==='login' && $method==='POST') {
    require_csrf();
    $body=json_decode(file_get_contents('php://input'),true) ?: [];
    $email=strtolower(trim((string)($body['email']??''))); $senha=(string)($body['senha']??'');
    if (!filter_var($email,FILTER_VALIDATE_EMAIL) || $senha==='') json_response(422,['success'=>false,'message'=>'Informe seu e-mail e sua senha.']);
    $stmt=$pdo->prepare('SELECT id,nome,email,senha_hash FROM usuarios WHERE email=? AND ativo=1');
    $stmt->execute([$email]); $u=$stmt->fetch();
    if (!$u || !password_verify($senha,$u['senha_hash'])) json_response(401,['success'=>false,'message'=>'E-mail ou senha incorretos.']);
    session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; $_SESSION['csrf']=bin2hex(random_bytes(32));
    if (!empty($body['remember'])) create_remember_token($pdo,(int)$u['id']);
    json_response(200,['success'=>true,'message'=>'Login realizado com sucesso.','user'=>['id'=>$u['id'],'nome'=>$u['nome'],'email'=>$u['email']]]);
}
if ($action==='logout' && $method==='POST') {
    require_csrf();
    $raw=$_COOKIE['imovecheck_remember']??'';
    if (preg_match('/^[a-f0-9]{64}$/',$raw)) $pdo->prepare('DELETE FROM tokens_acesso WHERE token_hash=?')->execute([hash('sha256',$raw)]);
    setcookie('imovecheck_remember','',['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']),'httponly'=>true,'samesite'=>'Lax']);
    $_SESSION=[]; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>'Lax']); }
    session_destroy(); json_response(200,['success'=>true,'message'=>'Você saiu da sua conta.']);
}
if ($action==='forgot' && $method==='POST') {
    require_csrf();
    $body=json_decode(file_get_contents('php://input'),true) ?: [];
    $email=strtolower(trim((string)($body['email']??'')));
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) json_response(422,['success'=>false,'message'=>'Informe um e-mail válido.']);
    $stmt=$pdo->prepare('SELECT id,nome FROM usuarios WHERE email=? AND ativo=1'); $stmt->execute([$email]); $u=$stmt->fetch();
    if ($u) {
        $raw=bin2hex(random_bytes(32)); $hash=hash('sha256',$raw);
        $pdo->prepare('DELETE FROM tokens_recuperacao WHERE usuario_id=?')->execute([$u['id']]);
        $pdo->prepare('INSERT INTO tokens_recuperacao (usuario_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))')->execute([$u['id'],$hash]);
        $url=(isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])),'/').'/redefinir-senha.php?token='.$raw;
        $subject='Redefinição de senha - ImoveCheck';
        $message="Olá, {$u['nome']}!\n\nPara redefinir sua senha, acesse o link abaixo em até 1 hora:\n{$url}\n\nSe não solicitou, ignore este e-mail.";
        $headers="Content-Type: text/plain; charset=UTF-8\r\n";
        @mail($email,$subject,$message,$headers);
    }
    json_response(200,['success'=>true,'message'=>'Se o e-mail estiver cadastrado, enviaremos instruções de recuperação. Verifique também a caixa de spam.']);
}
if ($action==='reset' && $method==='POST') {
    require_csrf();
    $body=json_decode(file_get_contents('php://input'),true) ?: [];
    $token=(string)($body['token']??''); $senha=(string)($body['senha']??'');
    if (!preg_match('/^[a-f0-9]{64}$/',$token)) json_response(422,['success'=>false,'message'=>'Link de recuperação inválido ou expirado.']);
    if (strlen($senha)<8 || strlen($senha)>72) json_response(422,['success'=>false,'message'=>'A nova senha deve ter entre 8 e 72 caracteres.']);
    $stmt=$pdo->prepare('SELECT id,usuario_id FROM tokens_recuperacao WHERE token_hash=? AND expires_at>NOW()'); $stmt->execute([hash('sha256',$token)]); $r=$stmt->fetch();
    if (!$r) json_response(400,['success'=>false,'message'=>'Link de recuperação inválido ou expirado.']);
    $pdo->beginTransaction();
    $pdo->prepare('UPDATE usuarios SET senha_hash=? WHERE id=?')->execute([password_hash($senha,PASSWORD_DEFAULT),$r['usuario_id']]);
    $pdo->prepare('DELETE FROM tokens_recuperacao WHERE usuario_id=?')->execute([$r['usuario_id']]);
    $pdo->prepare('DELETE FROM tokens_acesso WHERE usuario_id=?')->execute([$r['usuario_id']]);
    $pdo->commit();
    json_response(200,['success'=>true,'message'=>'Senha alterada. Faça login com sua nova senha.']);
}
json_response(405,['success'=>false,'message'=>'Operação não permitida.']);
