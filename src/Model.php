<?php

namespace MiniOrm;

use MiniOrm\Relations\Relation;
use MiniOrm\Relations\HasOne;
use MiniOrm\Relations\HasMany;
use MiniOrm\Relations\BelongsTo;
use MiniOrm\Relations\BelongsToMany;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;
    protected array $with = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        
        if (!isset($this->table)) {
            $this->table = $this->getDefaultTableName();
        }
    }

    // Static methods for fluent queries
    public static function query(): QueryBuilder
    {
        return (new static())->newQuery();
    }

    public static function where(string $column, string $operator = '=', $value = null): QueryBuilder
    {
        return static::query()->where($column, $operator, $value);
    }

    public static function find($id): ?static
    {
        $result = static::query()->find($id);
        return $result ? static::newFromArray($result) : null;
    }

    public static function first(): ?static
    {
        $result = static::query()->first();
        return $result ? static::newFromArray($result) : null;
    }

    public static function get(): array
    {
        $results = static::query()->get();
        return array_map(fn($item) => static::newFromArray($item), $results);
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function update($id, array $attributes): bool
    {
        return static::query()->where('id', $id)->update($attributes) > 0;
    }

    public static function delete($id): bool
    {
        return static::query()->where('id', $id)->delete() > 0;
    }

    public static function with(array $relations): QueryBuilder
    {
        $instance = new static();
        $instance->with = $relations;
        return $instance->newQuery();
    }

    public static function count(): int
    {
        return static::query()->count();
    }

    public static function exists(): bool
    {
        return static::query()->exists();
    }

    // Instance methods
    public function save(): bool
    {
        if ($this->exists) {
            return $this->performUpdate();
        } else {
            return $this->performInsert();
        }
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    // Relationship methods
    protected function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): HasOne
    {
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $localKey = $localKey ?? $this->primaryKey;
        
        return new HasOne($this, $related, $foreignKey, $localKey);
    }

    protected function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): HasMany
    {
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $localKey = $localKey ?? $this->primaryKey;
        
        return new HasMany($this, $related, $foreignKey, $localKey);
    }

    protected function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): BelongsTo
    {
        $foreignKey = $foreignKey ?? $this->getBelongsToForeignKey($related);
        $ownerKey = $ownerKey ?? (new $related())->primaryKey;
        
        return new BelongsTo($this, $related, $foreignKey, $ownerKey);
    }

    protected function belongsToMany(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null
    ): BelongsToMany {
        $table = $table ?? $this->getPivotTableName($related);
        $foreignPivotKey = $foreignPivotKey ?? $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?? (new $related())->getForeignKey();
        
        return new BelongsToMany($this, $related, $table, $foreignPivotKey, $relatedPivotKey);
    }

    // Protected helper methods
    protected function newQuery(): QueryBuilder
    {
        return new QueryBuilder($this->table);
    }

    protected function performInsert(): bool
    {
        $query = $this->newQuery();
        
        if ($query->insert($this->attributes)) {
            $this->exists = true;
            $this->original = $this->attributes;
            return true;
        }
        
        return false;
    }

    protected function performUpdate(): bool
    {
        $dirty = $this->getDirty();
        
        if (empty($dirty)) {
            return true;
        }

        $query = $this->newQuery()->where($this->primaryKey, $this->getKey());
        
        if ($query->update($dirty) > 0) {
            $this->original = $this->attributes;
            return true;
        }
        
        return false;
    }

    protected function getDirty(): array
    {
        $dirty = [];
        
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || 
                $this->original[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }
        
        return $dirty;
    }

    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }

        return empty($this->fillable) || in_array($key, $this->fillable);
    }

    protected function getKey()
    {
        return $this->getAttribute($this->primaryKey);
    }

    protected function getDefaultTableName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower($className) . 's';
    }

    protected function getForeignKey(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower($className) . '_id';
    }

    protected function getBelongsToForeignKey(string $related): string
    {
        $className = (new \ReflectionClass($related))->getShortName();
        return strtolower($className) . '_id';
    }

    protected function getPivotTableName(string $related): string
    {
        $models = [
            strtolower((new \ReflectionClass($this))->getShortName()),
            strtolower((new \ReflectionClass($related))->getShortName())
        ];
        
        sort($models);
        return implode('_', $models);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    public static function newFromArray(array $attributes): static
    {
        $model = new static();
        $model->attributes = $attributes;
        $model->original = $attributes;
        $model->exists = true;
        
        return $model;
    }
}