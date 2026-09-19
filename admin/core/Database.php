<?php

// mysqli fetch-mode constants, referenced by app code (fetch_all(MYSQLI_ASSOC) etc).
define('MYSQLI_ASSOC', 1);
define('MYSQLI_NUM', 2);
define('MYSQLI_BOTH', 3);

/**
 * Postgres-backed connection exposing the mysqli method surface
 * (prepare/bind_param/get_result/fetch_assoc/insert_id) so the ~80
 * call sites written against mysqli need no changes.
 */
class Database {
    private static ?PdoMysqliCompat $instance = null;

    public static function get(): PdoMysqliCompat {
        if (self::$instance === null) {
            try {
                $pdo = new PDO(
                    "pgsql:host={$_ENV['DB_SERVER']};dbname={$_ENV['DB_DATABASE']}",
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                http_response_code(503);
                exit('Servicio temporalmente no disponible.');
            }
            self::$instance = new PdoMysqliCompat($pdo);
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}

class PdoMysqliCompat {
    public int $insert_id = 0;
    public string $error = '';

    public function __construct(private PDO $pdo) {}

    public function prepare(string $sql): PdoMysqliStatement|false {
        try {
            return new PdoMysqliStatement($this->pdo, $sql, $this);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /** mysqli::query — direct (unparameterized) query, used for static SELECTs. */
    public function query(string $sql): PdoMysqliResult|false {
        try {
            $stmt = $this->pdo->query($sql);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return new PdoMysqliResult($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

class PdoMysqliStatement {
    private array $refs = [];
    private ?string $types = null;
    /** @var array<int, string> paramNum => accumulated blob data */
    private array $longData = [];
    private PDOStatement $stmt;
    /** Buffered rows — mysqli's num_rows/bind_result require a buffered resultset. */
    private array $rows = [];
    private int $cursor = 0;
    private bool $isSelect = false;
    private array $boundResultVars = [];

    public function __construct(private PDO $pdo, string $sql, private PdoMysqliCompat $conn) {
        $this->stmt = $pdo->prepare($sql);
        $this->isSelect = (bool) preg_match('/^\s*(SELECT|SHOW|WITH)\b/i', $sql);
    }

    /** mysqli_stmt::bind_param — binds by reference, evaluated at execute() time. */
    public function bind_param(string $types, &...$vars): bool {
        $this->types = $types;
        $this->refs = &$vars;
        return true;
    }

    /** mysqli_stmt::send_long_data — accumulates blob chunks for a given param index. */
    public function send_long_data(int $paramNum, string $data): bool {
        $this->longData[$paramNum] = ($this->longData[$paramNum] ?? '') . $data;
        return true;
    }

    public function execute(): bool {
        foreach ($this->refs as $i => &$value) {
            $type = $this->types[$i] ?? 's';
            $paramNum = $i + 1;
            if ($type === 'b') {
                $this->stmt->bindValue($paramNum, $this->longData[$i] ?? $value, PDO::PARAM_LOB);
            } else {
                $this->stmt->bindValue($paramNum, $value);
            }
        }

        try {
            $ok = $this->stmt->execute();
        } catch (PDOException $e) {
            $this->conn->error = $e->getMessage();
            return false;
        }

        if ($ok && preg_match('/^\s*INSERT\b/i', $this->stmt->queryString)) {
            $this->conn->insert_id = (int) $this->pdo->lastInsertId();
        }

        if ($ok && $this->isSelect) {
            $this->rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->cursor = 0;
        }

        return $ok;
    }

    /** mysqli_stmt::store_result — no-op here since execute() already buffers SELECTs. */
    public function store_result(): bool {
        return true;
    }

    /** mysqli_stmt::bind_result — binds output columns by position, applied on fetch(). */
    public function bind_result(&...$vars): bool {
        $this->boundResultVars = &$vars;
        return true;
    }

    public function get_result(): PdoMysqliResult {
        return new PdoMysqliResult($this->rows);
    }

    public function fetch(): bool|array|null {
        if ($this->boundResultVars) {
            if ($this->cursor >= count($this->rows)) {
                return null;
            }
            $row = array_values($this->rows[$this->cursor++]);
            foreach ($this->boundResultVars as $i => &$var) {
                $var = $row[$i] ?? null;
            }
            return true;
        }
        return $this->rows[$this->cursor++] ?? false;
    }

    public function num_rows(): int {
        return count($this->rows);
    }

    /** mysqli_stmt::close — no-op, PDOStatement needs no explicit close. */
    public function close(): bool {
        return true;
    }

    public function __get(string $name) {
        if ($name === 'insert_id') {
            return $this->conn->insert_id;
        }
        if ($name === 'affected_rows') {
            return $this->stmt->rowCount();
        }
        if ($name === 'num_rows') {
            return count($this->rows);
        }
        throw new \Exception("Undefined property: PdoMysqliStatement::\$$name");
    }
}

class PdoMysqliResult {
    private int $cursor = 0;

    public function __construct(private array $rows) {}

    public function fetch_assoc(): array|false {
        return $this->rows[$this->cursor++] ?? false;
    }

    public function fetch_row(): array|false {
        $row = $this->rows[$this->cursor++] ?? false;
        return $row === false ? false : array_values($row);
    }

    public function fetch_all(int $mode = MYSQLI_ASSOC): array {
        if ($mode === MYSQLI_NUM) {
            return array_map('array_values', $this->rows);
        }
        return $this->rows;
    }

    public function num_rows(): int {
        return count($this->rows);
    }

    public function __get(string $name) {
        if ($name === 'num_rows') {
            return count($this->rows);
        }
        throw new \Exception("Undefined property: PdoMysqliResult::\$$name");
    }
}
