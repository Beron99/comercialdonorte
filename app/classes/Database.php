<?php
/**
 * Classe Database
 * Gerencia conexão com banco de dados usando PDO e Singleton
 * Sistema de Gestão de Capacitações (SGC)
 */

class Database {
    private static $instance = null;
    private $connection;

    /**
     * Construtor privado (Singleton)
     * @throws Exception Se houver erro na conexão
     */
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
        } catch (PDOException $e) {
            // Log do erro
            $this->logError($e->getMessage());

            // Em produção, não expor detalhes do erro
            if (APP_ENV === 'development') {
                throw new Exception("Erro de conexão com banco de dados: " . $e->getMessage());
            } else {
                throw new Exception("Erro ao conectar ao banco de dados. Contate o administrador.");
            }
        }
    }

    /**
     * Retorna instância única da classe (Singleton)
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna conexão PDO
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Testa se a conexão está ativa
     * @return bool
     */
    public function isConnected() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Inicia uma transação
     * @return bool
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirma uma transação
     * @return bool
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Reverte uma transação
     * @return bool
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * Retorna o último ID inserido
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Executa uma query e retorna o statement
     * @param string $sql
     * @param array $params
     * @return PDOStatement|false
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }

    /**
     * Executa uma query e retorna todos os resultados
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Executa uma query e retorna um único resultado
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Executa uma query e retorna o número de linhas afetadas
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Registra erros em arquivo de log
     * @param string $message
     */
    private function logError($message) {
        $logFile = LOGS_PATH . 'database.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;

        // Cria diretório de logs se não existir
        if (!is_dir(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0755, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Previne clonagem
     */
    private function __clone() {}

    /**
     * Previne deserialização
     * @throws Exception
     */
    public function __wakeup() {
        throw new Exception("Não é possível deserializar Singleton");
    }

    /**
     * Fecha a conexão ao destruir o objeto
     */
    public function __destruct() {
        $this->connection = null;
    }
}
