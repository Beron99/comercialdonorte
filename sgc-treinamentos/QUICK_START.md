# ⚡ Quick Start - Deploy Rápido

## 🎯 Guia de 5 Minutos

### Passo 1: Upload para Hostinger (2 minutos)

```bash
# No seu computador:
zip -r sgc.zip sgc-treinamentos/

# Upload via File Manager do Hostinger
# Ou via FTP para public_html/
```

### Passo 2: Descompactar (30 segundos)

No File Manager do Hostinger:
1. Clique com botão direito no arquivo `sgc.zip`
2. Selecione "Extract"
3. Confirme

### Passo 3: Ajustar URL (1 minuto)

Edite: `sgc-treinamentos/app/config/config.php`

Linha 24:
```php
// Mude de:
define('BASE_URL', 'http://localhost/sgc-treinamentos/public/');

// Para:
define('BASE_URL', 'https://seudominio.com.br/sgc-treinamentos/public/');
```

### Passo 4: Instalar (1 minuto)

Acesse no navegador:
```
https://seudominio.com.br/sgc-treinamentos/public/install.php
```

Clique em **"Iniciar Instalação"**

Aguarde a criação das tabelas ✅

### Passo 5: Login (30 segundos)

```
URL: https://seudominio.com.br/sgc-treinamentos/public/
Email: admin@sgc.com
Senha: admin123
```

---

## 🎉 Pronto! Sistema Instalado!

Você agora tem acesso a:
- ✅ Dashboard funcional
- ✅ Sistema de login
- ✅ Menu completo
- ✅ Banco de dados configurado

---

## ⚙️ Configurações Opcionais

### Configurar SMTP (E-mail)
1. Vá em **Configurações > E-mail**
2. Preencha dados do servidor SMTP

### Configurar WordPress
1. Vá em **Integração > Configurar**
2. Preencha URL da API WordPress

### Alterar Senha Admin
1. Vá em **Meu Perfil > Alterar Senha**
2. Digite nova senha

---

## 🔧 Instalar via SSH (Alternativo)

Se você tem acesso SSH:

```bash
# Conectar via SSH
ssh usuario@seudominio.com.br

# Navegar para pasta
cd public_html/sgc-treinamentos

# Instalar dependências
composer install

# Ajustar permissões
chmod -R 755 .
chmod -R 777 logs/ temp/ public/uploads/
```

---

## 📊 Credenciais do Sistema

### Banco de Dados
```
Host:     u411458227_comercial
Database: u411458227_comercial
Username: u411458227_comercial25
Password: #Ide@2k25
```

### Usuário Admin
```
Email: admin@sgc.com
Senha: admin123
```

⚠️ **Altere a senha após primeiro acesso!**

---

## ✅ Checklist Pós-Instalação

- [ ] Sistema instalado e funcionando
- [ ] Login realizado com sucesso
- [ ] Senha do admin alterada
- [ ] URL configurada corretamente
- [ ] SMTP configurado (opcional)
- [ ] WordPress configurado (opcional)

---

## 🐛 Problemas Comuns

### Erro de Conexão
```
Solução: Verifique app/config/database.php
```

### Erro 500
```
Solução:
1. Ative display_errors em config.php
2. Verifique logs/error.log
```

### Página em Branco
```
Solução:
1. Verifique permissões das pastas
2. Execute: chmod -R 755 sgc-treinamentos/
```

---

## 📞 Links Úteis

- **Login:** `/public/index.php`
- **Instalação:** `/public/install.php`
- **Teste de Conexão:** `/public/test_connection.php`
- **Dashboard:** `/public/dashboard.php`

---

## 🚀 Próximos Passos

Após instalação, você pode:

1. **Cadastrar Colaboradores**
   - Menu > Colaboradores > Cadastrar

2. **Criar Treinamentos**
   - Menu > Treinamentos > Cadastrar

3. **Ver Relatórios**
   - Menu > Relatórios > Dashboard

4. **Configurar Integrações**
   - Menu > Integração > Configurar

---

**Tempo Total de Instalação: ~5 minutos** ⏱️

**Dificuldade: Fácil** ✅

**Pré-requisitos: Apenas acesso ao Hostinger** 🌐

---

**Desenvolvido por Comercial do Norte** 💼
