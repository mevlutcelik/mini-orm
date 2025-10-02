<?php

namespace MiniOrm\Relations;

use MiniOrm\Model;
use MiniOrm\QueryBuilder;

abstract class Relation
{
    protected Model $parent;
    protected string $related;
    protected QueryBuilder $query;

    public function __construct(Model $parent, string $related)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->query = $this->getRelationQuery();
    }

    abstract public function getResults();

    abstract protected function getRelationQuery(): QueryBuilder;

    public function where(string $column, string $operator = '=', $value = null): self
    {
        $this->query->where($column, $operator, $value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query->orderBy($column, $direction);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query->limit($limit);
        return $this;
    }

    protected function newRelatedInstance(): Model
    {
        return new $this->related();
    }
}