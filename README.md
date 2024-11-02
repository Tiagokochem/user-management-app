# 📋 User Management Application

Este é um sistema de gerenciamento de usuários simples, desenvolvido em PHP e MySQL, com um ambiente configurado via Docker. A aplicação permite realizar operações de CRUD (Create, Read, Update, Delete) para usuários, com funcionalidades de validação e upload de foto.

## 🚀 Tecnologias Utilizadas

- **Backend**: PHP 8 com Apache
- **Banco de Dados**: MySQL
- **Ambiente**: Docker
- **Frontend**: HTML, CSS básico e JavaScript para interatividade

---

## 📁 Estrutura do Projeto


user-management-app/
├── src/
│   ├── index.php
│   ├── add_user.php
│   ├── edit_user.php
│   ├── delete_user.php
│   └── search_user.php
├── uploads/
├── docker-compose.yml
└── Dockerfile


---

## 🛠 Funcionalidades

1. **Cadastro de Usuário**:
   - Nome, Email e Data de Nascimento.
   - Upload de foto com tamanho máximo de 200 KB e dimensões de até 700x1080 pixels (formato `.jpg` ou `.jpeg`).
   
2. **Listagem de Usuários**:
   - Exibição em uma tabela com foto em miniatura, ações de edição e exclusão.
   - Busca por nome ou email.
   
3. **Edição de Usuário**:
   - Formulário pré-preenchido para atualizar as informações.
   - Atualização da foto, se necessário.
   
4. **Exclusão de Usuário**:
   - Confirmação antes de excluir o usuário.
   
5. **Mensagens de Sucesso e Erro**:
   - Exibição de mensagens de validação e sucesso para feedback do usuário.

---

## 📋 Pré-requisitos

- **Docker**: Certifique-se de que o Docker está instalado e em execução no seu sistema.
- **Docker Compose**: Necessário para orquestrar os containers.

---

## 🚀 Instruções de Configuração

### Passo 1: Clonar o Repositório

```bash
git clone https://github.com/Tiagokochem/user-management-app
cd user-management-app/src


Passo 2: Configurar o Ambiente Docker Navegue até o diretório src do projeto:

cd "C:\user-management-app\src"

Inicie os containers Docker:

docker compose up -d
A aplicação estará disponível em http://localhost:8080.

Observação: Pode aparecer um aviso sobre o atributo version no docker-compose.yml. Esse aviso pode ser ignorado, pois o Docker ainda executará o arquivo corretamente.

Passo 3: Configuração do Banco de Dados O banco de dados é configurado automaticamente ao iniciar os containers. O arquivo init.sql define o esquema da tabela users necessária para a aplicação.

📊 Estrutura da Tabela users A tabela users possui os seguintes campos:

id (INT) - Identificador único do usuário (chave primária).
nome (VARCHAR) - Nome do usuário.
email (VARCHAR) - Email do usuário (deve ser único).
data_nascimento (DATE) - Data de nascimento do usuário.
foto (VARCHAR) - Caminho da foto do usuário.
🔒 Validações e Regras de Negócio Nome: Deve ter no mínimo 3 caracteres. Email: Deve estar em formato válido e ser único. Data de Nascimento: O usuário precisa ser maior de 18 anos. Foto: Tamanho máximo de 200 KB, com dimensões de até 700x1080 pixels e formato .jpg ou .jpeg.

▶️ Como Usar Acessar a Aplicação: Abra http://localhost:8080 no navegador. Adicionar Usuário: Clique em "Adicionar Novo Usuário" e preencha o formulário. Editar Usuário: Na lista de usuários, clique em "Editar" ao lado do usuário que deseja modificar. Excluir Usuário: Na lista de usuários, clique em "Excluir" e confirme para remover o usuário.

🛑 Encerrando o Ambiente Para parar os containers Docker:

docker compose down
📌 Notas Persistência de Dados: O volume do banco de dados é configurado para persistir dados entre reinicializações do container. Uploads de Fotos: As fotos dos usuários são salvas na pasta uploads dentro da pasta src.

⚠️ Problemas Comuns Se os containers não iniciarem corretamente, certifique-se de que o Docker está configurado para executar containers Linux. Verifique se não há conflitos de porta, especialmente com a porta 8080.

## 📸 Imagem do Sistema

![Alt text](https://github-production-user-asset-6210df.s3.amazonaws.com/57450432/382481306-7c8bd45a-71aa-4bb4-b49a-53cdd793da81.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAVCODYLSA53PQK4ZA%2F20241102%2Fus-east-1%2Fs3%2Faws4_request&X-Amz-Date=20241102T150608Z&X-Amz-Expires=300&X-Amz-Signature=01c41867fc4e125b469ad65e2534179da064fd89ebad9871824e68f20ade4ef2&X-Amz-SignedHeaders=host)

