# Sistema de Estoque

## Pré-requisitos

* PHP 7.4 ou superior
* MySQL 5.7 ou superior
* Composer
* Servidor web (Apache/Nginx)

### 1. Clone o repositório
```bash
git clone [URL_DO_REPOSITORIO]
cd [NOME_DO_DIRETORIO]
```

### 2. Instale as dependências via Composer
```bash
composer install
```

### 3. Configure o banco de dados

1. Crie um banco de dados MySQL chamado `facilestoque`
2. Importe o arquivo SQL fornecido para criar as tabelas necessárias
3. Configure as credenciais do banco de dados no arquivo `src/database/conection.php`:

### 4. Configure as permissões de diretório

Dê permissões de escrita para o diretório de imagens:

```bash
chmod -R 755 public/assets/image/produtos
```

### 5. Usuário inicial
Acesse o dashboard com as seguintes credenciais:
Email: admin@email.com
Senha: 123


## Funcionalidades principais

- Gestão de produtos
- Controle de estoque
- Ponto de venda
- Geração de relatórios em PDF
- Gestão de categorias
- Controle de validade de produtos