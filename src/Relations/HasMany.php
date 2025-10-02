<?php

namespace MiniOrm\Relations;

use MiniOrm\Model;
use MiniOrm\QueryBuilder;

class HasMany extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(Model $parent, string $related, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
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
            ->where($this->foreignKey, $this->parent->getAttribute($this->localKey));
    }
}