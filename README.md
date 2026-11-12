# ImoveCheck — autenticação e gerenciamento de imóveis

## Tecnologias
- HTML5, CSS3 e JavaScript no navegador.
- PHP 8+ como backend/API.
- MySQL ou MariaDB com PDO.
- Sessões PHP, `password_hash`/`password_verify`, tokens aleatórios armazenados em hash e proteção CSRF.

## Instalação limpa (banco novo)
1. Extraia a pasta `ImoveCheck-main` em `C:\xampp\htdocs\imovecheck`.
2. Inicie Apache e MySQL no XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe `database.sql`. Ele cria o banco `imovecheck` e as tabelas de usuários, imóveis, fotos e tokens.
5. Confira as credenciais MySQL em `api/db.php` (padrão XAMPP: `root`, senha vazia).
6. Abra `http://localhost/imovecheck/`. Você será direcionado ao login.
7. Clique em **Cadastre-se**, crie a primeira conta e entre.

## Atualização de um banco antigo
1. Faça um backup completo do banco `imovecheck` pelo phpMyAdmin (Exportar).
2. Não importe `database.sql` sobre a instalação antiga.
3. Execute `MIGRACAO_BANCO.sql` uma única vez no phpMyAdmin.
4. Crie sua conta normalmente.
5. Os imóveis antigos ficam com `usuario_id = NULL`, pois não é seguro atribuí-los automaticamente a uma pessoa. Para associá-los, descubra o ID da conta na tabela `usuarios` e execute, substituindo `ID_DA_CONTA`:
   `UPDATE imoveis SET usuario_id = ID_DA_CONTA WHERE usuario_id IS NULL;`

## Recuperação de senha por e-mail
A aplicação gera um token aleatório de uso único, válido por 1 hora, e envia o link usando a função `mail()` do PHP. Em instalação local, o XAMPP normalmente não vem configurado para enviar e-mails. Configure SMTP/um serviço de envio no ambiente ou adapte o envio antes de depender desse recurso. A API sempre mostra uma mensagem genérica para não revelar se um e-mail está cadastrado.

## Funcionalidades
- Login e logout.
- Cadastro de usuários e validação de e-mail único.
- Senhas armazenadas com hash seguro.
- Opção “Manter conectado” com token persistente de 30 dias.
- Recuperação e redefinição de senha com token de uso único e expiração.
- Proteção das páginas e da API de imóveis.
- Cada usuário lista, cadastra e exclui apenas os próprios imóveis.
- Proteção CSRF nas operações que alteram dados.
- Formulários responsivos e mostrar/ocultar senha.

## Observações importantes
- O projeto é adequado para desenvolvimento local e demonstração. Antes de publicar na internet, configure HTTPS, SMTP, backups, limites de tentativas de login, logs, política de privacidade e revisão de segurança.
- O link “Precisa de ajuda?” abre o aplicativo de e-mail local para `suporte@imovecheck.local`; substitua por um endereço real de suporte.
- A autenticação não substitui autorização administrativa nem implementa perfis/permissões avançadas.
- A senha deve ter pelo menos 8 caracteres. O limite superior de 72 bytes é aplicado para compatibilidade com bcrypt/PHP.
