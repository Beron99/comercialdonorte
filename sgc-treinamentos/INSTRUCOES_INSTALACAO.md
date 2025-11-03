# 🚀 Instruções de Instalação - SGC

## ✅ O QUE FOI CRIADO ATÉ AGORA

### 📦 Fase 1: Estrutura Base (COMPLETA)

✅ **Estrutura de Diretórios**
- Todos os diretórios do projeto criados
- Organização MVC completa

✅ **Configurações**
- `composer.json` - Dependências PHP
- `app/config/config.php` - Configurações gerais
- `app/config/database.php` - Conexão com Hostinger
- `.gitignore` - Arquivos ignorados pelo Git

✅ **Classes Core**
- `Database.php` - Singleton PDO com tratamento de erros
- `Auth.php` - Sistema completo de autenticação

✅ **Banco de Dados**
- `database/schema.sql` - Script SQL completo
  - 9 tabelas criadas
  - 3 views para relatórios
  - Índices de performance
  - Configurações padrão
  - Usuário admin padrão

✅ **Interface do Sistema**
- `public/index.php` - Página de login moderna
- `public/dashboard.php` - Dashboard principal
- `public/logout.php` - Logout seguro
- `public/install.php` - Instalador automático
- `public/test_connection.php` - Teste de conexão

✅ **Layouts**
- `header.php` - Cabeçalho responsivo
- `footer.php` - Rodapé com informações
- `sidebar.php` - Menu lateral colapsável
- `navbar.php` - Barra superior com notificações

✅ **Documentação**
- `README.md` - Documentação completa
- Este arquivo de instruções

---

## 🎯 PRÓXIMOS PASSOS PARA VOCÊ

### 1. Upload para Hostinger

```bash
# Comprimir projeto
zip -r sgc-treinamentos.zip sgc-treinamentos/

# Fazer upload via FTP ou File Manager do Hostinger
# Extrair no diretório public_html ou subpasta desejada
```

### 2. Instalar Dependências PHP

Acesse via SSH do Hostinger:

```bash
cd caminho/para/sgc-treinamentos
composer install
```

### 3. Ajustar Permissões

```bash
chmod -R 755 sgc-treinamentos/
chmod -R 777 sgc-treinamentos/logs/
chmod -R 777 sgc-treinamentos/temp/
chmod -R 777 sgc-treinamentos/public/uploads/
```

### 4. Configurar URL

Edite `app/config/config.php` linha 24:

```php
// Altere de:
define('BASE_URL', 'http://localhost/sgc-treinamentos/public/');

// Para (exemplo):
define('BASE_URL', 'https://seudominio.com.br/sgc/');
```

### 5. Executar Instalação

Acesse no navegador:

```
https://seudominio.com.br/sgc/install.php
```

Clique em **"Iniciar Instalação"**

O sistema irá:
- ✅ Testar conexão com banco
- ✅ Criar todas as 9 tabelas
- ✅ Criar 3 views
- ✅ Inserir configurações padrão
- ✅ Criar usuário administrador

### 6. Fazer Login

```
URL: https://seudominio.com.br/sgc/
Email: admin@sgc.com
Senha: admin123
```

⚠️ **IMPORTANTE:** Altere a senha após primeiro login!

---

## 🔧 CONFIGURAÇÕES ADICIONAIS

### Ativar Modo Produção

Edite `app/config/config.php` linha 21:

```php
define('APP_ENV', 'production'); // Mude de 'development' para 'production'
```

### Configurar SMTP (E-mail)

No sistema, vá em:
1. **Configurações > E-mail**
2. Preencha:
   - Host SMTP
   - Porta (587 ou 465)
   - Usuário
   - Senha

### Configurar WordPress (Opcional)

No sistema, vá em:
1. **Integração > Configurar**
2. Preencha:
   - URL da API: `https://seuwordpress.com.br/wp-json`
   - Usuário WordPress
   - Senha de Aplicação

---

## 📊 ESTRUTURA DO BANCO DE DADOS

### Tabelas Criadas:

1. **colaboradores** - Dados dos funcionários
2. **treinamentos** - Cursos e capacitações
3. **agenda_treinamentos** - Datas e horários
4. **treinamento_participantes** - Vinculação colaborador-treinamento
5. **frequencia_treinamento** - Controle de presença
6. **notificacoes** - E-mails e check-ins
7. **wp_sync_log** - Log de sincronização WordPress
8. **configuracoes** - Configurações do sistema
9. **usuarios_sistema** - Usuários administradores

### Views Criadas:

1. **vw_treinamentos_status** - Resumo por status
2. **vw_participacoes_colaborador** - Participações por pessoa
3. **vw_indicadores_mensais** - Indicadores mensais

---

## 🎨 RECURSOS IMPLEMENTADOS

### Sistema de Autenticação
- ✅ Login seguro com hash de senha
- ✅ Controle de sessão
- ✅ Timeout de inatividade (30 minutos)
- ✅ 4 níveis de acesso (admin, gestor, instrutor, visualizador)
- ✅ Proteção CSRF

### Interface Moderna
- ✅ Design responsivo
- ✅ Sidebar colapsável
- ✅ Notificações em tempo real
- ✅ Alertas automáticos
- ✅ Menu dropdown
- ✅ Busca integrada

### Dashboard
- ✅ 4 cards de estatísticas
- ✅ Ações rápidas
- ✅ Informações do sistema
- ✅ Bem-vindo personalizado

---

## 🔜 PRÓXIMAS IMPLEMENTAÇÕES

### Fase 2: Módulo de Colaboradores
- Model Colaborador
- CRUD completo
- Importação Excel/CSV
- Filtros avançados

### Fase 3: Integração WordPress
- Classe WordPressSync (já documentada)
- Interface de configuração
- Sincronização automática

### Fase 4: Matriz de Capacitações
- CRUD de treinamentos
- Wizard multi-etapas
- 12 campos obrigatórios
- Agendamento

### Fase 5: Notificações
- NotificationManager (já documentada)
- Templates de e-mail
- Sistema de tokens
- Check-in online

### Fase 6: Relatórios
- IndicadoresRH (já documentada)
- 6 indicadores calculados
- Gráficos Chart.js
- Exportação Excel/PDF

---

## 🐛 SOLUÇÃO DE PROBLEMAS

### Erro: "Não foi possível conectar ao banco de dados"

**Solução:**
1. Verifique credenciais em `app/config/database.php`
2. Teste conexão em `test_connection.php`
3. Confirme que o IP está liberado no Hostinger

### Erro: "Class 'Database' not found"

**Solução:**
```bash
composer dump-autoload
```

### Erro 500 (Internal Server Error)

**Solução:**
1. Ative logs: Mude `APP_ENV` para `development`
2. Verifique `logs/error.log`
3. Confirme extensões PHP instaladas

### Erro: "Permission denied"

**Solução:**
```bash
chmod -R 755 sgc-treinamentos/
chmod -R 777 sgc-treinamentos/logs/
```

---

## 📞 COMANDOS ÚTEIS

### Backup do Banco
```bash
mysqldump -u u411458227_comercial25 -p u411458227_comercial > backup.sql
```

### Restaurar Banco
```bash
mysql -u u411458227_comercial25 -p u411458227_comercial < backup.sql
```

### Atualizar Composer
```bash
composer update
```

### Ver Logs em Tempo Real
```bash
tail -f logs/error.log
```

---

## ✅ CHECKLIST DE INSTALAÇÃO

- [ ] Upload dos arquivos para Hostinger
- [ ] Executar `composer install`
- [ ] Ajustar permissões de pastas
- [ ] Configurar URL em `config.php`
- [ ] Executar `install.php`
- [ ] Fazer primeiro login
- [ ] Alterar senha padrão
- [ ] Configurar modo produção
- [ ] Configurar SMTP (opcional)
- [ ] Configurar WordPress (opcional)
- [ ] Testar todas as funcionalidades

---

## 🎉 SISTEMA ESTÁ PRONTO PARA USO!

Com estas configurações, você terá:

✅ Sistema de login funcional
✅ Dashboard operacional
✅ Banco de dados configurado
✅ Estrutura completa para desenvolvimento
✅ Autenticação segura
✅ Interface responsiva

**Os próximos módulos (Colaboradores, Treinamentos, etc.) serão implementados nas próximas fases conforme cronograma do PLANO_DESENVOLVIMENTO_SGC.md**

---

## 📚 DOCUMENTAÇÃO ADICIONAL

- **README.md** - Documentação completa do projeto
- **PLANO_DESENVOLVIMENTO_SGC.md** - Plano técnico detalhado
- **database/schema.sql** - Estrutura completa do banco

---

**Data de Criação:** 03/11/2025
**Versão:** 1.0.0
**Status:** Fase 1 Completa ✅

---

**Desenvolvido com ❤️ por Comercial do Norte**
