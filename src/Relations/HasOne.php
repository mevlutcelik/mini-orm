<?php

namespace MiniOrm\Relations;

use MiniOrm\Model;
use MiniOrm\QueryBuilder;

class HasOne extends Relation
{
    protected string $foreignKey;
    protected string $localKey;

    public function __construct(Model $parent, string $related, string $foreignKey, string $localKey)
    {
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        parent::__construct($parent, $related);
    }

    public function getResults(): ?Model
    {
        $result = $this->query->first();
        
        if ($result) {
            $related = $this->newRelatedInstance();
            $related = $related::newFromArray($result);
            return $related;
        }
        
        return null;
    }

    protected function getRelationQuery(): QueryBuilder
    {
        $related = $this->newRelatedInstance();
        return (new QueryBuilder($related->getTable()))
            ->where($this->foreignKey, $this->parent->getAttribute($this->localKey));
    }
}