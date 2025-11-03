# 🎓 SGC - Sistema de Gestão de Capacitações

Sistema web completo em PHP para gerenciamento de treinamentos corporativos, controle de participantes, cálculo de indicadores de RH e integração com WordPress.

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Credenciais Padrão](#credenciais-padrão)
- [Suporte](#suporte)

---

## 📖 Sobre o Projeto

O **SGC (Sistema de Gestão de Capacitações)** é uma solução completa para gerenciar treinamentos corporativos, incluindo:

- Cadastro de colaboradores e treinamentos
- Matriz de capacitações com 12 campos específicos
- Sistema de notificações por e-mail
- Check-in via token único
- Controle de frequência detalhado
- Relatórios gerenciais com 6 indicadores de RH
- Integração com WordPress para importação de usuários

---

## ✨ Funcionalidades

### 🧑‍💼 Gestão de Colaboradores
- ✅ CRUD completo de colaboradores
- ✅ Importação via Excel/CSV
- ✅ Filtros avançados e busca
- ✅ Histórico de treinamentos
- ✅ Exportação de relatórios

### 📚 Matriz de Capacitações
- ✅ Cadastro com 12 campos obrigatórios
- ✅ Wizard multi-etapas
- ✅ Agendamento de múltiplas datas
- ✅ Controle de custos
- ✅ Status (Programado, Executado, Pendente, Cancelado)

### 🔔 Notificações e Check-in
- ✅ Envio automático de convites por e-mail
- ✅ Sistema de tokens únicos
- ✅ Check-in online
- ✅ Templates HTML responsivos

### 📊 Relatórios e Indicadores
- ✅ HTC (Horas de Treinamento por Colaborador)
- ✅ HTC por Nível Hierárquico
- ✅ CTC (Custo de Treinamento por Colaborador)
- ✅ % de Investimento sobre Folha Salarial
- ✅ % de Treinamentos Realizados vs Planejados
- ✅ % de Colaboradores Capacitados

### 🔗 Integração WordPress
- ✅ Sincronização automática de usuários
- ✅ Importação via REST API
- ✅ Log detalhado de sincronizações

---

## 🔧 Requisitos

### Servidor
- **PHP**: 8.1 ou superior
- **MySQL**: 8.0 ou superior
- **Apache**: 2.4+ com mod_rewrite **OU** Nginx 1.18+
- **Composer**: Para gerenciar dependências

### Extensões PHP Necessárias
- PDO
- pdo_mysql
- mbstring
- openssl
- curl
- gd (para manipulação de imagens)
- zip

---

## 🚀 Instalação

### 1. Clonar o Repositório

```bash
git clone https://github.com/seu-usuario/sgc-treinamentos.git
cd sgc-treinamentos
```

### 2. Instalar Dependências

```bash
composer install
```

### 3. Configurar Banco de Dados

Edite o arquivo `app/config/database.php` com suas credenciais:

```php
define('DB_HOST', 'seu-host');
define('DB_NAME', 'seu-database');
define('DB_USER', 'seu-usuario');
define('DB_PASS', 'sua-senha');
```

### 4. Executar Instalação

Acesse no navegador:

```
http://seu-dominio/sgc-treinamentos/public/install.php
```

Clique em **"Iniciar Instalação"** para criar as tabelas automaticamente.

### 5. Acessar o Sistema

```
http://seu-dominio/sgc-treinamentos/public/
```

---

## ⚙️ Configuração

### URL Base

Edite `app/config/config.php` e ajuste a URL conforme seu ambiente:

```php
define('BASE_URL', 'http://seu-dominio/sgc-treinamentos/public/');
```

### SMTP (E-mail)

Configure as credenciais SMTP no painel de configurações do sistema ou diretamente na tabela `configuracoes`:

- `smtp_host`: Servidor SMTP
- `smtp_port`: Porta (587 ou 465)
- `smtp_user`: Usuário SMTP
- `smtp_password`: Senha SMTP
- `email_remetente`: E-mail remetente

### WordPress API

Para integração com WordPress, configure na interface do sistema:

1. Acesse **Integração > Configurar**
2. Preencha:
   - URL da API WordPress
   - Usuário
   - Senha de Aplicação

---

## 📝 Uso

### Login

**Credenciais padrão:**
- **E-mail:** admin@sgc.com
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere a senha padrão após o primeiro acesso!

### Fluxo Básico

1. **Cadastrar Colaboradores**
   - Menu: `Colaboradores > Cadastrar`
   - Ou importar via planilha

2. **Criar Treinamento**
   - Menu: `Treinamentos > Cadastrar`
   - Preencher os 12 campos obrigatórios
   - Agendar datas

3. **Vincular Participantes**
   - Menu: `Participantes > Vincular`
   - Selecionar colaboradores
   - Sistema envia convites automáticos

4. **Registrar Frequência**
   - Menu: `Frequência > Registrar`
   - Marcar presença/ausência por dia

5. **Visualizar Relatórios**
   - Menu: `Relatórios > Dashboard`
   - Ver indicadores calculados automaticamente

---

## 📁 Estrutura do Projeto

```
sgc-treinamentos/
├── public/                     # Arquivos públicos
│   ├── index.php               # Página de login
│   ├── dashboard.php           # Dashboard principal
│   ├── install.php             # Script de instalação
│   ├── test_connection.php     # Teste de conexão
│   └── assets/                 # CSS, JS, imagens
│
├── app/                        # Aplicação PHP
│   ├── config/                 # Arquivos de configuração
│   │   ├── config.php
│   │   └── database.php
│   │
│   ├── classes/                # Classes principais
│   │   ├── Database.php        # Singleton PDO
│   │   ├── Auth.php            # Autenticação
│   │   ├── WordPressSync.php   # Integração WP
│   │   ├── NotificationManager.php
│   │   └── IndicadoresRH.php
│   │
│   ├── models/                 # Models (MVC)
│   ├── controllers/            # Controllers (MVC)
│   ├── views/                  # Views (MVC)
│   │   └── layouts/            # Layouts (header, footer, sidebar)
│   └── helpers/                # Funções auxiliares
│
├── database/                   # Scripts SQL
│   └── schema.sql              # Estrutura completa do banco
│
├── logs/                       # Logs do sistema
├── temp/                       # Arquivos temporários
├── docs/                       # Documentação
│
├── composer.json               # Dependências PHP
├── .gitignore
└── README.md
```

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 8.1+** - Linguagem principal
- **MySQL 8.0+** - Banco de dados
- **PDO** - Camada de abstração de banco
- **Composer** - Gerenciador de dependências

### Frontend
- **HTML5** - Estrutura
- **CSS3** - Estilização
- **JavaScript** - Interatividade

### Bibliotecas PHP
- **PHPMailer** - Envio de e-mails
- **PhpSpreadsheet** - Geração de Excel
- **TCPDF** - Geração de PDF
- **Guzzle** - Cliente HTTP para API

### Padrões e Arquitetura
- **MVC** - Model-View-Controller
- **Singleton** - Para conexão com banco
- **PSR-4** - Autoloading
- **Prepared Statements** - Segurança SQL

---

## 🔑 Credenciais Padrão

### Usuário Administrador
- **E-mail:** admin@sgc.com
- **Senha:** admin123

### Banco de Dados (Hostinger)
- **Host:** u411458227_comercial
- **Database:** u411458227_comercial
- **Username:** u411458227_comercial25
- **Password:** #Ide@2k25

---

## 📊 Indicadores Calculados

### 1. HTC - Horas de Treinamento por Colaborador
```
HTC = Total de Horas / Número de Colaboradores Treinados
```

### 2. HTC por Nível Hierárquico
```
HTC_nível = Total de Horas do Nível / Número de Colaboradores do Nível
```

### 3. CTC - Custo de Treinamento por Colaborador
```
CTC = Custo Total / Número de Colaboradores Treinados
```

### 4. % de Investimento sobre Folha
```
% = (Custo Total de Treinamentos / Folha Salarial Total) × 100
```

### 5. % de Treinamentos Realizados vs Planejados
```
% = (Horas Realizadas / Horas Planejadas) × 100
```

### 6. % de Colaboradores Capacitados
```
% = (Colaboradores Treinados / Colaboradores Totais) × 100
```

---

## 🐛 Troubleshooting

### Erro de Conexão com Banco
1. Verifique as credenciais em `app/config/database.php`
2. Certifique-se de que o MySQL está rodando
3. Teste a conexão em `public/test_connection.php`

### Erro 500 (Internal Server Error)
1. Ative `display_errors` em `app/config/config.php`
2. Verifique logs em `logs/error.log`
3. Certifique-se de que todas as extensões PHP estão instaladas

### Composer não encontrado
```bash
# Instalar Composer globalmente
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

---

## 🚀 Próximos Passos

Após instalação básica, você pode:

1. ✅ **Importar Colaboradores** via planilha Excel
2. ✅ **Configurar SMTP** para envio de e-mails
3. ✅ **Integrar com WordPress** para sincronização de usuários
4. ✅ **Criar Treinamentos** e vincular participantes
5. ✅ **Gerar Relatórios** com indicadores de RH

---

## 📞 Suporte

Para dúvidas ou problemas:

1. Consulte a documentação completa em `/docs`
2. Verifique os logs em `/logs`
3. Entre em contato com o suporte técnico

---

## 📄 Licença

Proprietary - © 2025 Comercial do Norte

---

## ✍️ Autor

**Comercial do Norte**
- Website: [comercialdonorte.com](https://comercialdonorte.com)
- Email: contato@comercialdonorte.com

---

## 🎯 Versão

**1.0.0** - Data: 03/11/2025

---

**Desenvolvido com ❤️ para otimizar a gestão de capacitações corporativas**
