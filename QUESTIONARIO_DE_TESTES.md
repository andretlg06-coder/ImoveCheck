# Questionário de testes — ImoveCheck

**Como usar:** execute cada teste no XAMPP com Apache e MySQL ativos. Marque `[x]` somente depois de observar o resultado esperado. Registre falhas, navegador, data e mensagem exibida.

## 1. Preparação e banco de dados
- [ ] O Apache inicia sem erro?
- [ ] O MySQL inicia sem erro?
- [ ] O banco `imovecheck` existe no phpMyAdmin?
- [ ] As tabelas `usuarios`, `imoveis`, `fotos`, `tokens_acesso` e `tokens_recuperacao` foram criadas?
- [ ] A aplicação abre em `http://localhost/imovecheck/`?
- [ ] `api/db.php` contém host, banco, usuário e senha corretos?
- [ ] A extensão PDO MySQL está habilitada no PHP?
- [ ] As extensões `fileinfo` e `mbstring` estão habilitadas?

## 2. Acesso e proteção de páginas
- [ ] Ao abrir o sistema sem login, sou direcionado para `login.php`?
- [ ] Se tento abrir `index.php` diretamente sem login, sou redirecionado?
- [ ] Se tento abrir `cadastrar.php` diretamente sem login, sou redirecionado?
- [ ] Se tento consultar `api/imoveis.php` sem login, a API responde HTTP 401?
- [ ] Após entrar, consigo acessar a listagem de imóveis?
- [ ] Ao sair, não consigo voltar às páginas protegidas usando o botão Voltar do navegador sem autenticar novamente?

## 3. Cadastro de usuário
- [ ] Consigo abrir a tela “Cadastre-se”?
- [ ] Consigo criar uma conta com nome, e-mail e senha válidos?
- [ ] O sistema rejeita nome vazio ou com menos de 2 caracteres?
- [ ] O sistema rejeita e-mail inválido?
- [ ] O sistema rejeita senha com menos de 8 caracteres?
- [ ] O sistema rejeita senha com mais de 72 bytes?
- [ ] O sistema avisa quando as senhas de confirmação são diferentes?
- [ ] O sistema impede cadastrar novamente um e-mail já utilizado?
- [ ] Após cadastro válido, aparece uma confirmação e sou encaminhado ao login?
- [ ] Na tabela `usuarios`, a senha está armazenada como hash e não em texto puro?
- [ ] A senha diferencia letras maiúsculas e minúsculas?

## 4. Login
- [ ] Consigo entrar com e-mail e senha corretos?
- [ ] Sou impedido de entrar com senha incorreta?
- [ ] Sou impedido de entrar com e-mail não cadastrado?
- [ ] O sistema apresenta mensagem genérica para credenciais incorretas (sem revelar qual campo falhou)?
- [ ] O campo e-mail aceita um endereço válido?
- [ ] O campo senha oculta os caracteres por padrão?
- [ ] O botão de olho mostra e oculta a senha?
- [ ] O botão Entrar fica desabilitado durante o envio para evitar cliques repetidos?
- [ ] Depois do login, sou levado à tela de imóveis?
- [ ] O login continua funcionando depois de atualizar a página?

## 5. Manter conectado e sessão
- [ ] Sem marcar “Manter conectado”, fechar e reabrir o navegador encerra a sessão conforme o comportamento do navegador?
- [ ] Marcando “Manter conectado”, continuo autenticado após fechar e reabrir o navegador?
- [ ] O cookie persistente é `HttpOnly`?
- [ ] O cookie persistente tem `SameSite=Lax`?
- [ ] Em hospedagem HTTPS, o cookie persistente recebe `Secure`?
- [ ] Ao sair, o cookie persistente é removido?
- [ ] Ao sair, o token correspondente é removido da tabela `tokens_acesso`?
- [ ] Um token expirado deixa de autenticar?
- [ ] Depois de redefinir a senha, os tokens persistentes antigos deixam de funcionar?

## 6. Recuperação de senha
- [ ] Consigo abrir “Esqueceu a senha?”?
- [ ] O formulário rejeita e-mail inválido?
- [ ] O sistema apresenta a mesma mensagem para e-mail cadastrado e não cadastrado?
- [ ] Com o envio de e-mail configurado, a mensagem chega à caixa de entrada?
- [ ] O link recebido abre a tela de nova senha?
- [ ] O link expira após uma hora?
- [ ] Um link já utilizado não pode ser reutilizado?
- [ ] Um link alterado ou inválido é rejeitado?
- [ ] A nova senha precisa ter pelo menos 8 caracteres?
- [ ] A confirmação precisa ser igual à nova senha?
- [ ] Após redefinir, consigo entrar com a senha nova?
- [ ] A senha antiga deixa de funcionar?
- [ ] Tokens de recuperação anteriores são invalidados?

**Atenção:** o envio real de e-mail depende da configuração SMTP/`mail()` do ambiente. Em XAMPP local, esse recurso geralmente exige configuração adicional.

## 7. Gerenciamento de imóveis
- [ ] Consigo cadastrar um imóvel estando autenticado?
- [ ] O endereço é obrigatório?
- [ ] Proprietário e inquilino podem ficar vazios?
- [ ] Os limites de caracteres são respeitados?
- [ ] Consigo visualizar somente os imóveis da minha conta?
- [ ] Consigo excluir um imóvel da minha conta?
- [ ] A confirmação aparece antes de excluir?
- [ ] As fotos relacionadas são removidas ao excluir o imóvel?
- [ ] A API impede excluir um imóvel pertencente a outro usuário?
- [ ] A API impede cadastrar imóvel sem sessão válida?
- [ ] O sistema continua aceitando JPG, PNG e WEBP?
- [ ] O sistema rejeita arquivos que não sejam imagens válidas?
- [ ] O sistema rejeita imagem acima de 5 MB?
- [ ] O sistema limita o envio a 20 fotos por imóvel?
- [ ] A pré-visualização das fotos funciona?
- [ ] Consigo remover uma foto selecionada antes de salvar?

## 8. Isolamento entre contas
1. Crie duas contas: Usuário A e Usuário B.
2. Entre como A e cadastre um imóvel de teste.
3. Saia e entre como B.

- [ ] O usuário B não vê o imóvel cadastrado por A?
- [ ] O usuário B não consegue excluir o imóvel de A alterando o ID da requisição?
- [ ] O usuário B não consegue acessar os dados de A pela API?
- [ ] Ao entrar novamente como A, o imóvel continua aparecendo?

## 9. Segurança e erros
- [ ] Tentar enviar requisição POST sem token CSRF é rejeitado?
- [ ] Tentar excluir com token CSRF ausente ou inválido é rejeitado?
- [ ] Inserir aspas ou SQL no e-mail não permite manipular consultas?
- [ ] Inserir HTML/script nos campos não executa código na listagem?
- [ ] Erros de banco não exibem senha ou detalhes internos ao usuário?
- [ ] O sistema trata campos vazios e entradas muito longas sem quebrar?
- [ ] Atualizar a página durante uma operação não duplica cadastros?
- [ ] O sistema funciona após reiniciar Apache e MySQL?

## 10. Interface e responsividade
- [ ] A tela de login se parece com a referência enviada?
- [ ] O cabeçalho mantém logo, nome e subtítulo alinhados?
- [ ] O cartão de login fica centralizado na tela?
- [ ] Os campos têm espaçamento e foco visíveis?
- [ ] O botão Entrar ocupa a largura do cartão?
- [ ] Os links de cadastro e recuperação funcionam?
- [ ] A interface funciona em resolução desktop?
- [ ] A interface funciona em celular estreito?
- [ ] Não aparece rolagem horizontal indevida?
- [ ] Os textos e botões continuam legíveis com zoom de 200%?
- [ ] É possível navegar pelos campos usando apenas o teclado?

## 11. Resultado final
**Navegador e versão:** __________________________________

**Data dos testes:** ______________________________________

**Quantidade de testes aprovados:** _______________________

**Quantidade de testes reprovados:** ______________________

**Problemas encontrados e passos para reproduzir:**

__________________________________________________________

__________________________________________________________

**Correções realizadas:**

__________________________________________________________

__________________________________________________________

**Aprovado para apresentação?** ( ) Sim  ( ) Não

**Observação:** os testes deste documento são manuais. Para produção, complemente com testes automatizados, limitação de tentativas de login, revisão de configuração do servidor e testes de segurança independentes.
