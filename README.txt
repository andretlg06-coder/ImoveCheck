IMOVECHECK - Cadastro de imóveis
================================

REQUISITOS
- XAMPP com Apache, PHP 8+ e MySQL/MariaDB.
- Extensões PHP PDO MySQL, fileinfo e mbstring habilitadas.

INSTALAÇÃO
1. Extraia a pasta "imovecheck" dentro de C:\xampp\htdocs\
2. Abra o XAMPP Control Panel e inicie Apache e MySQL.
3. Acesse http://localhost/phpmyadmin
4. Importe o arquivo database.sql (aba Importar).
5. Confira usuário e senha do MySQL em api/db.php. O padrão do XAMPP costuma ser root sem senha.
6. Abra http://localhost/imovecheck/

FUNCIONALIDADES
- Cadastro com endereço obrigatório.
- Proprietário e inquilino opcionais.
- Seleção de até 20 imagens JPG, PNG ou WEBP, com limite de 5 MB por imagem.
- Pré-visualização e remoção das fotos antes de salvar.
- Persistência em MySQL e armazenamento físico das fotos na pasta uploads.
- Listagem e exclusão de imóveis (as fotos também são excluídas).
- Layout responsivo para celular e computador.

OBSERVAÇÕES
- O diretório uploads precisa permitir gravação pelo Apache.
- Para produção, implemente autenticação, autorização, proteção CSRF, limites de requisição e política de backup.
- Este projeto implementa o fluxo de cadastro e gerenciamento básico; não inclui ainda inspeções, cômodos, checklists ou relatórios.
