# 📋 Log de Desenvolvimento - SGC (Sistema de Gestão de Capacitações)

**Projeto:** Sistema de Gestão de Capacitações
**URL Produção:** https://comercial.ideinstituto.com.br/
**Ambiente:** PHP 8.x + MySQL
**Arquitetura:** MVC (Model-View-Controller)

---

## 🎯 Visão Geral do Sistema

### Módulos Planejados
1. ✅ **Colaboradores** - Gestão de colaboradores/funcionários
2. 🔄 **Treinamentos** - Gestão de treinamentos e capacitações
3. ⏳ **Participantes** - Vinculação de participantes aos treinamentos
4. ⏳ **Frequência** - Registro de presença/check-in
5. ⏳ **Relatórios** - Dashboards e relatórios analíticos
6. ⏳ **Integração WordPress** - Sincronização com site WordPress
7. ⏳ **Configurações** - Configurações do sistema
8. ⏳ **Perfil do Usuário** - Gestão de perfil

---

## 📁 Estrutura de Diretórios

```
comercial do norte/
├── app/
│   ├── classes/          # Classes auxiliares (Database, Auth)
│   ├── config/           # Configurações (config.php, database.php)
│   ├── controllers/      # Controllers MVC
│   ├── models/           # Models MVC
│   └── views/
│       └── layouts/      # Header, Footer, Sidebar
├── public/               # Pasta pública (document root)
│   ├── assets/          # CSS, JS, imagens
│   ├── uploads/         # Arquivos enviados
│   ├── colaboradores/   # Views do módulo Colaboradores
│   ├── treinamentos/    # Views do módulo Treinamentos
│   └── index.php        # Login
└── DEVELOPMENT_LOG.md   # Este arquivo
```

---

## 🔐 Sistema de Autenticação

**Classe:** `app/classes/Auth.php`

### Níveis de Acesso
- `admin` - Acesso total ao sistema
- `gestor` - Gestão de treinamentos e relatórios
- `instrutor` - Registro de frequência e visualização
- `visualizador` - Apenas visualização

### Sessão
- Timeout: 30 minutos
- CSRF Token: Implementado em todos os formulários
- Função `csrf_token()` - Gera token
- Função `csrf_validate($token)` - Valida token

---

## 💾 Banco de Dados

**Configuração:** `app/config/config.php`

### Tabelas Principais
1. `usuarios` - Usuários do sistema
2. `colaboradores` - Colaboradores/funcionários
3. `treinamentos` - Treinamentos cadastrados
4. `treinamento_participantes` - Vínculo participantes x treinamentos
5. `agenda_treinamentos` - Agenda/cronograma dos treinamentos

### Campos Padrão
Todas as tabelas possuem:
- `id` - Primary Key AUTO_INCREMENT
- `created_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at` - TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
- `origem` - VARCHAR(20) DEFAULT 'local' (local ou wordpress)

---

## ✅ MÓDULO: COLABORADORES (100% Concluído)

### Status: ✅ Completo

### Arquivos Criados
- **Model:** `app/models/Colaborador.php`
- **Controller:** `app/controllers/ColaboradorController.php`
- **Views:**
  - `public/colaboradores/listar.php` - Listagem com filtros
  - `public/colaboradores/cadastrar.php` - Formulário de cadastro
  - `public/colaboradores/editar.php` - Formulário de edição
  - `public/colaboradores/visualizar.php` - Detalhes do colaborador
  - `public/colaboradores/actions.php` - Ações (inativar, exportar)

### Funcionalidades
- ✅ CRUD completo (Create, Read, Update, Delete/Inativar)
- ✅ Filtros: busca por nome/email, nível hierárquico, status (ativo/inativo)
- ✅ Paginação (20 itens por página)
- ✅ Validação de CPF
- ✅ Validação de e-mail
- ✅ Exportação para CSV
- ✅ Histórico de treinamentos do colaborador
- ✅ Estatísticas (total de treinamentos, horas, etc.)
- ✅ Sistema de badges para status

### Campos do Colaborador
- `nome` (obrigatório)
- `email` (obrigatório, único)
- `cpf` (validado)
- `nivel_hierarquico` (obrigatório) - Operacional, Tático, Estratégico
- `cargo`
- `departamento`
- `salario`
- `data_admissao`
- `telefone`
- `observacoes`
- `ativo` (1 = ativo, 0 = inativo)

### Correções Realizadas
- **2025-01-XX:** Corrigido erro de sintaxe na linha 38 do ColaboradorController.php
  - Problema: `public function processar Cadastro()` (espaço indevido)
  - Solução: `public function processarCadastro()`

---

## ✅ MÓDULO: TREINAMENTOS (100% Concluído)

### Status: ✅ Completo

### Arquivos Criados
- ✅ **Model:** `app/models/Treinamento.php`
- ✅ **Controller:** `app/controllers/TreinamentoController.php`
- ✅ **Views:**
  - `public/treinamentos/listar.php` - Listagem com filtros e paginação
  - `public/treinamentos/cadastrar.php` - Formulário de cadastro
  - `public/treinamentos/editar.php` - Formulário de edição
  - `public/treinamentos/visualizar.php` - Detalhes do treinamento
  - `public/treinamentos/actions.php` - Ações (cancelar, executar, exportar)

### Funcionalidades Implementadas
- ✅ CRUD completo (Create, Read, Update, Cancelar)
- ✅ Listagem com filtros (busca, tipo, status, ano)
- ✅ Paginação (20 itens por página)
- ✅ Exportação para CSV
- ✅ Badges para tipo e status
- ✅ Contagem de participantes
- ✅ Sistema de ações (cancelar, marcar como executado)
- ✅ Validações de dados (datas, custo, carga horária)
- ✅ Página de visualização detalhada com:
  - Estatísticas de participação
  - Lista de participantes
  - Agenda do treinamento
  - Informações financeiras
  - Cálculo de duração e custo por participante
- ✅ Controle de acesso por nível de usuário
- ✅ Campos condicionais (fornecedor apenas para externos)
- ✅ Formatação automática de valores monetários
- ✅ Model com métodos completos:
  - `listar($params)` - Lista com filtros
  - `buscarPorId($id)` - Busca por ID
  - `criar($dados)` - Cria novo treinamento
  - `atualizar($id, $dados)` - Atualiza treinamento
  - `cancelar($id)` - Cancela treinamento
  - `marcarExecutado($id)` - Marca como executado
  - `buscarParticipantes($treinamentoId)` - Lista participantes
  - `buscarAgenda($treinamentoId)` - Lista agenda
  - `getEstatisticas($treinamentoId)` - Estatísticas
  - `getAnosDisponiveis()` - Anos para filtro
  - `getProximos($limite)` - Próximos treinamentos
  - `getEmAndamento()` - Treinamentos em andamento

### Campos do Treinamento
- `nome` (obrigatório)
- `tipo` (obrigatório) - Interno ou Externo
- `fornecedor` (para treinamentos externos)
- `instrutor`
- `carga_horaria`
- `carga_horaria_complementar`
- `data_inicio`
- `data_fim`
- `custo_total`
- `observacoes`
- `status` - Programado, Em Andamento, Executado, Cancelado

### Status do Treinamento
1. **Programado** - Badge azul (#d1ecf1)
2. **Em Andamento** - Badge amarelo (#fff3cd)
3. **Executado** - Badge verde (#d4edda)
4. **Cancelado** - Badge vermelho (#f8d7da)

---

## ✅ MÓDULO: PARTICIPANTES (100% Concluído)

### Status: ✅ Completo

### Arquivos Criados
- **Model:** `app/models/Participante.php`
- **Controller:** `app/controllers/ParticipanteController.php`
- **Views:**
  - `public/participantes/index.php` - Redireciona para seleção de treinamento
  - `public/participantes/vincular.php` - Vincular colaboradores ao treinamento
  - `public/participantes/gerenciar.php` - Gerenciar participantes vinculados
  - `public/participantes/avaliar.php` - Avaliar participante (Kirkpatrick)
  - `public/participantes/actions.php` - Ações (check-in, desvincular, exportar)

### Funcionalidades Implementadas
- ✅ Vinculação múltipla de colaboradores
- ✅ Sistema de cards interativos para seleção
- ✅ Filtros (busca, nível, departamento)
- ✅ Check-in de participantes
- ✅ Avaliação em 3 níveis (Modelo Kirkpatrick)
- ✅ Estatísticas de participação
- ✅ Exportação para CSV
- ✅ Controle de permissões por nível

### Correções Realizadas
- **2025-01-XX:** Corrigido Auth::checkAuth() para Auth::requireLogin()

---

## ✅ MÓDULO: RELATÓRIOS (100% Concluído)

### Status: ✅ Completo

### Arquivos Criados
- **Model:** `app/models/Relatorio.php`
- **Controller:** `app/controllers/RelatorioController.php`
- **Views:**
  - `public/relatorios/dashboard.php` - Dashboard principal
  - `public/relatorios/departamentos.php` - Por departamento
  - `public/relatorios/matriz.php` - Matriz de capacitações
  - `public/relatorios/actions.php` - Exportação CSV

### Funcionalidades Implementadas
- ✅ Dashboard com estatísticas gerais
- ✅ Treinamentos mais realizados
- ✅ Colaboradores mais capacitados
- ✅ Distribuição por tipo
- ✅ Relatório por departamento
- ✅ Matriz de capacitações
- ✅ Exportação CSV
- ✅ Filtros e análises

---

## ⏳ MÓDULOS PENDENTES

### Frequência
- Registro de presença por data/sessão
- QR Code para check-in
- Relatório de frequência

### Integração WordPress
- Sincronização de dados
- API REST
- Webhooks

### Configurações
- Configurações do sistema
- Gerenciamento de usuários
- Configurações de e-mail

---

## 🎨 Padrões de Design

### CSS
- **Cores principais:**
  - Primária: #667eea (roxo/azul)
  - Secundária: #764ba2 (roxo escuro)
  - Sucesso: #28a745 (verde)
  - Perigo: #dc3545 (vermelho)
  - Aviso: #ffc107 (amarelo)

- **Layout:**
  - Sidebar fixa com largura 260px
  - Sidebar colapsível (70px quando minimizado)
  - Grid responsivo
  - Cards com sombra e hover effect

### JavaScript
- Função `toggleSidebar()` - Alterna sidebar
- Função `toggleSubmenu(id)` - Alterna submenu
- LocalStorage para salvar estado do sidebar

### PHP
- Função `e($string)` - Escapa HTML (htmlspecialchars)
- Função `csrf_token()` - Gera token CSRF
- Função `csrf_validate($token)` - Valida token CSRF

---

## 🔧 Configurações Importantes

### config.php
```php
define('BASE_URL', 'https://comercial.ideinstituto.com.br/public/');
define('ITEMS_PER_PAGE', 20);
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production');
```

### Database
- Host: localhost
- Database: u411458227_sgc
- Charset: utf8mb4
- Collation: utf8mb4_unicode_ci

---

## 📝 Próximos Passos

### Prioridade Alta
1. ⏳ Finalizar módulo Treinamentos (cadastrar.php, editar.php, visualizar.php, actions.php)
2. ⏳ Criar módulo Participantes
3. ⏳ Criar módulo Frequência

### Prioridade Média
4. ⏳ Criar módulo Relatórios
5. ⏳ Implementar Matriz de Capacitações

### Prioridade Baixa
6. ⏳ Integração WordPress
7. ⏳ Módulo de Configurações
8. ⏳ Página de Perfil do Usuário

---

## 🐛 Bugs Corrigidos

### 2025-01-XX
1. **ColaboradorController.php linha 38**
   - Erro: `public function processar Cadastro()`
   - Correção: Removido espaço entre "processar" e "Cadastro"
   - Status: ✅ Corrigido

2. **Auth.php - Loop de redirecionamento**
   - Erro: Login redirecionando para logout.php?timeout=1
   - Causa: checkSessionTimeout() não verificava se usuário estava logado
   - Correção: Adicionado `if (!self::isLogged()) return false;`
   - Status: ✅ Corrigido

3. **BASE_URL - Estrutura de pastas**
   - Erro: URLs apontando para raiz sem /public/
   - Correção: Atualizado BASE_URL para incluir /public/
   - Status: ✅ Corrigido

---

## 📚 Referências de Código

### Padrão de Model
```php
class NomeModel {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }

    public function listar($params = []) {
        // Implementação com filtros e paginação
    }

    public function buscarPorId($id) {
        // Busca por ID
    }

    public function criar($dados) {
        // Cria novo registro
    }

    public function atualizar($id, $dados) {
        // Atualiza registro
    }
}
```

### Padrão de Controller
```php
class NomeController {
    private $model;

    public function __construct() {
        $this->model = new NomeModel();
    }

    public function processarCadastro() {
        // Valida CSRF
        if (!csrf_validate($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'message' => 'Token inválido'];
        }

        // Valida dados
        $erros = $this->validarDados($_POST);
        if (!empty($erros)) {
            return ['success' => false, 'message' => implode('<br>', $erros)];
        }

        // Sanitiza dados
        $dados = $this->sanitizarDados($_POST);

        // Cria registro
        return $this->model->criar($dados);
    }

    private function validarDados($dados) {
        // Validação
    }

    private function sanitizarDados($dados) {
        // Sanitização
    }
}
```

### Padrão de View (Listagem)
```php
<?php
define('SGC_SYSTEM', true);
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/classes/Database.php';
require_once __DIR__ . '/../../app/classes/Auth.php';
require_once __DIR__ . '/../../app/models/NomeModel.php';
require_once __DIR__ . '/../../app/controllers/NomeController.php';

$controller = new NomeController();
$resultado = $controller->listar();

$pageTitle = 'Título';
$breadcrumb = '<a href="../dashboard.php">Dashboard</a> > Título';
include __DIR__ . '/../../app/views/layouts/header.php';
?>

<!-- Conteúdo da página -->

<?php include __DIR__ . '/../../app/views/layouts/footer.php'; ?>
```

---

**Última Atualização:** 2025-01-XX
**Versão do Log:** 1.0
