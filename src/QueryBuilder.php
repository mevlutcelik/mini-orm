<?php

namespace MiniOrm;

use PDO;
use PDOStatement;

class QueryBuilder
{
    protected PDO $pdo;
    protected string $table;
    protected array $wheres = [];
    protected array $bindings = [];
    protected array $orderBy = [];
    protected ?int $limitValue = null;
    protected ?int $offsetValue = null;
    protected array $joins = [];
    protected array $select = ['*'];

    public function __construct(?string $table = null)
    {
        $this->pdo = Database::getInstance();
        if ($table) {
            $this->table = $table;
        }
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator = '=', $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = $this->createPlaceholder($column);
        $this->wheres[] = "{$column} {$operator} :{$placeholder}";
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetValue = $offset;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        return $this->execute($sql);
    }

    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function find($id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function count(): int
    {
        $originalSelect = $this->select;
        $this->select = ['COUNT(*) as count'];
        
        $sql = $this->buildSelectQuery();
        $result = $this->execute($sql);
        
        $this->select = $originalSelect;
        return (int) ($result[0]['count'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        return $this->execute($sql, $data) !== false;
    }

    public function update(array $data): int
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            "UPDATE %s SET %s%s",
            $this->table,
            implode(', ', $sets),
            $this->buildWhereClause()
        );

        $bindings = array_merge($data, $this->bindings);
        $stmt = $this->execute($sql, $bindings);
        
        return $stmt ? $stmt->rowCount() : 0;
    }

    public function delete(): int
    {
        $sql = sprintf(
            "DELETE FROM %s%s",
            $this->table,
            $this->buildWhereClause()
        );

        $stmt = $this->execute($sql, $this->bindings);
        return $stmt ? $stmt->rowCount() : 0;
    }

    protected function buildSelectQuery(): string
    {
        $sql = sprintf(
            "SELECT %s FROM %s",
            implode(', ', $this->select),
            $this->table
        );

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->buildWhereClause();

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }

        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }

        return $sql;
    }

    protected function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        return ' WHERE ' . implode(' AND ', $this->wheres);
    }

    protected function execute(string $sql, array $bindings = []): mixed
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            
            $bindingsToUse = empty($bindings) ? $this->bindings : $bindings;
            
            foreach ($bindingsToUse as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();

            if (stripos($sql, 'SELECT') === 0) {
                return $stmt->fetchAll();
            }

            return $stmt;
        } catch (\Exception $e) {
            throw new \Exception("Query execution failed: " . $e->getMessage() . " SQL: " . $sql);
        }
    }

    protected function createPlaceholder(string $column): string
    {
        $base = str_replace('.', '_', $column);
        $counter = 1;
        $placeholder = $base;

        while (isset($this->bindings[$placeholder])) {
            $placeholder = $base . '_' . $counter++;
        }

        return $placeholder;
    }
}