<?php

namespace MiniOrm\Relations;

use MiniOrm\Model;
use MiniOrm\QueryBuilder;

class BelongsToMany extends Relation
{
    protected string $table;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;

    public function __construct(
        Model $parent, 
        string $related, 
        string $table, 
        string $foreignPivotKey, 
        string $relatedPivotKey
    ) {
        $this->table = $table;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        parent::__construct($parent, $related);
    }

    public function getResults(): array
    {
        $results = $this->query->get();
        $related = $this->newRelatedInstance();
        
        return array_map(function($item) use ($related) {
            return $related::newFromArray($item);
        }, $results);
    }

    protected function getRelationQuery(): QueryBuilder
    {
        $related = $this->newRelatedInstance();
        
        return (new QueryBuilder($related->getTable()))
            ->join(
                $this->table,
                $related->getTable() . '.' . $related->getKeyName(),
                '=',
                $this->table . '.' . $this->relatedPivotKey
            )
            ->where($this->table . '.' . $this->foreignPivotKey, $this->parent->getKey());
    }
}