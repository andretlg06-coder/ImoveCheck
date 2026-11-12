<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json; charset=utf-8');
function respond(int $status,array $data):never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE); exit; }
$user=require_user($pdo);
$method=$_SERVER['REQUEST_METHOD'];
if ($method==='GET') {
 $stmt=$pdo->prepare('SELECT i.id,i.endereco,i.proprietario,i.inquilino,i.criado_em,(SELECT COUNT(*) FROM fotos f WHERE f.imovel_id=i.id) total_fotos FROM imoveis i WHERE i.usuario_id=? ORDER BY i.id DESC');
 $stmt->execute([$user['id']]); respond(200,['success'=>true,'imoveis'=>$stmt->fetchAll()]);
}
if ($method==='POST') {
 require_csrf();
 $endereco=trim((string)($_POST['endereco']??'')); $proprietario=trim((string)($_POST['proprietario']??'')); $inquilino=trim((string)($_POST['inquilino']??''));
 if($endereco==='')respond(422,['success'=>false,'message'=>'Informe o endereço do imóvel.']);
 if(mb_strlen($endereco)>255||mb_strlen($proprietario)>150||mb_strlen($inquilino)>150)respond(422,['success'=>false,'message'=>'Um ou mais campos ultrapassaram o limite permitido.']);
 $files=$_FILES['fotos']??null; $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$toSave=[];
 if($files&&is_array($files['name'])){if(count($files['name'])>20)respond(422,['success'=>false,'message'=>'Envie no máximo 20 fotos por imóvel.']);$finfo=new finfo(FILEINFO_MIME_TYPE);
 for($i=0;$i<count($files['name']);$i++){if($files['error'][$i]===UPLOAD_ERR_NO_FILE)continue;if($files['error'][$i]!==UPLOAD_ERR_OK)respond(422,['success'=>false,'message'=>'Erro durante o envio de uma foto.']);if($files['size'][$i]>5*1024*1024)respond(422,['success'=>false,'message'=>'Cada foto deve ter no máximo 5 MB.']);$mime=$finfo->file($files['tmp_name'][$i]);if(!isset($allowed[$mime])||@getimagesize($files['tmp_name'][$i])===false)respond(422,['success'=>false,'message'=>'Formato inválido. Use JPG, PNG ou WEBP.']);$toSave[]=['tmp'=>$files['tmp_name'][$i],'ext'=>$allowed[$mime]];}}
 $pdo->beginTransaction();$saved=[];
 try{$stmt=$pdo->prepare('INSERT INTO imoveis(usuario_id,endereco,proprietario,inquilino) VALUES(?,?,?,?)');$stmt->execute([$user['id'],$endereco,$proprietario?:null,$inquilino?:null]);$id=(int)$pdo->lastInsertId();$dir=dirname(__DIR__).'/uploads';if(!is_dir($dir)&&!mkdir($dir,0755,true))throw new RuntimeException();
 $photoStmt=$pdo->prepare('INSERT INTO fotos(imovel_id,caminho) VALUES(?,?)');foreach($toSave as $photo){$name=bin2hex(random_bytes(16)).'.'.$photo['ext'];$dest=$dir.'/'.$name;if(!move_uploaded_file($photo['tmp'],$dest))throw new RuntimeException();$saved[]=$dest;$photoStmt->execute([$id,'uploads/'.$name]);}$pdo->commit();respond(201,['success'=>true,'message'=>'Imóvel cadastrado com sucesso!','id'=>$id]);
 }catch(Throwable $e){$pdo->rollBack();foreach($saved as $p)if(is_file($p))unlink($p);respond(500,['success'=>false,'message'=>'Não foi possível concluir o cadastro. Verifique a pasta uploads.']);}
}
if($method==='DELETE'){require_csrf();$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);if(!$id||$id<1)respond(400,['success'=>false,'message'=>'Identificador inválido.']);$stmt=$pdo->prepare('SELECT f.caminho FROM fotos f JOIN imoveis i ON i.id=f.imovel_id WHERE i.id=? AND i.usuario_id=?');$stmt->execute([$id,$user['id']]);$paths=$stmt->fetchAll();$del=$pdo->prepare('DELETE FROM imoveis WHERE id=? AND usuario_id=?');$del->execute([$id,$user['id']]);if(!$del->rowCount())respond(404,['success'=>false,'message'=>'Imóvel não encontrado.']);foreach($paths as $p){$path=dirname(__DIR__).'/'.$p['caminho'];if(is_file($path))unlink($path);}respond(200,['success'=>true,'message'=>'Imóvel excluído com sucesso.']);}
header('Allow: GET, POST, DELETE');respond(405,['success'=>false,'message'=>'Método não permitido.']);
