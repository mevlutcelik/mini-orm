<?php

namespace MiniOrm\Relations;

use MiniOrm\Model;
use MiniOrm\QueryBuilder;

class BelongsTo extends Relation
{
    protected string $foreignKey;
    protected string $ownerKey;

    public function __construct(Model $parent, string $related, string $foreignKey, string $ownerKey)
    {
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
        parent::__construct($parent, $related);
    }

    public function getResults(): ?Model
    {
        $result = $this->query->first();
        
        if ($result) {
            $related = $this->newRelatedInstance();
            return $related::newFromArray($result);
        }
        
        return null;
    }

    protected function getRelationQuery(): QueryBuilder
    {
        $related = $this->newRelatedInstance();
        return (new QueryBuilder($related->getTable()))
            ->where($this->ownerKey, $this->parent->getAttribute($this->foreignKey));
    }
}