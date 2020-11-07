<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * @method static \Illuminate\Support\Collection toSelectOptions() scope ToSelectOptions
 *
 * @see     ModelTree::scopeToSelectOptions()
 *
 * Trait TModelTree
 * @package mPhpMaster\Support\Traits
 */
trait TModelTree
{
    /**
     * @var array
     */
    protected static $branchOrder = [];

    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * @var string
     */
    protected $titleColumn = 'name';

    /**
     * @var string
     */
    protected $orderColumn = 'order_by';

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * Get children of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(static::class, $this->parentColumn);
    }

    /**
     * Get parent of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(static::class, $this->parentColumn);
    }

    /**
     * @return string
     */
    public function getParentColumn(): string
    {
        return $this->parentColumn;
    }

    /**
     * Set parent column.
     *
     * @param string $column
     */
    public function setParentColumn($column): void
    {
        $this->parentColumn = $column;
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn(): string
    {
        return tool_title_locale($this->titleColumn);
    }

    /**
     * Set title column.
     *
     * @param string $column
     */
    public function setTitleColumn($column): void
    {
        $this->titleColumn = $column;
    }

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn(): string
    {
        return $this->orderColumn;
    }

    /**
     * Set order column.
     *
     * @param string $column
     */
    public function setOrderColumn($column): void
    {
        $this->orderColumn = $column;
    }

    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery(\Closure $query = null): self
    {
        $this->queryCallback = $query;

        return $this;
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree(): array
    {
        return $this->buildNestedArray();
    }

    /**
     * Build Nested array.
     *
     * @param array    $nodes
     * @param int|null $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0): array
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if (is_null($parentId) || $node[$this->parentColumn] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);
        $byOrder = $orderColumn . ' = 0,' . $orderColumn;

        $self = new static();

        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        return $self->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * Set the order of branches in the tree.
     *
     * @param array $order
     *
     * @return void
     */
    protected static function setBranchOrder(array $order): void
    {
        static::$branchOrder = array_flip(array_flatten($order));

        static::$branchOrder = array_map( static function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public static function saveOrder($tree = [], $parentId = 0): void
    {
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch['id']);

            $node->{$node->getParentColumn()} = $parentId;
            $node->{$node->getOrderColumn()} = static::$branchOrder[$branch['id']];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch['id']);
            }
        }
    }

    /**
     * Get options for Select field in form.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function selectOptions($except = null): \Illuminate\Support\Collection
    {
        $options = (new static())->buildSelectOptions([], 0, '', $except);

        return collect($options)->prepend(__('global.null'), 0)->all();
    }

    /**
     * self::toSelectOptions()
     * Get options for Select field in form.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|integer                        $except id to remove
     *
     * @return \Illuminate\Support\Collection
     */
    public static function scopeToSelectOptions($query, $except = null): \Illuminate\Support\Collection
    {
        $self = new static();

        $orderColumn = \DB::getQueryGrammar()->wrap($self->orderColumn);
        $byOrder = $orderColumn . ' = 0,' . $orderColumn;
        $nodes = $query->orderByRaw($byOrder)->get()->toArray();

        $options = (new static())->buildSelectOptions($nodes, 0, '', $except);
        return collect($options)->prepend(__('global.null'), 0);
    }

    /**
     * self::toFullPathArray()
     * Get options for Select field in form.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string|integer                        $except id to remove
     *
     * @return \Illuminate\Support\Collection
     */
    public static function scopeToFullPathArray($query, $except = null): \Illuminate\Support\Collection
    {
        $self = new static();

        $orderColumn = \DB::getQueryGrammar()->wrap($self->orderColumn);
        $byOrder = $orderColumn . ' = 0,' . $orderColumn;
        $nodes = $query->orderByRaw($byOrder)->get()->toArray();

        $options = (new static())->buildFullPath($nodes, null, '&nbsp;/&nbsp;', $except);
        return collect($options)->prepend(__('global.null'), 0);
    }

    /**
     * Returns model name ad full path
     *
     * @param int|\Model $nodeId
     * @param string    $prefix
     * @param null|int  $except
     *
     * @return string
     */
    protected function getFullPathName($nodeId = 0, $prefix = '', $except = null): string
    {
        if (!($nodeId instanceof $this)) {
            if (is_array($nodeId) || is_collection($nodeId)) {
                $nodeId = collect($nodeId);
                $node = static::find($nodeId->has('id') ? $nodeId->get('id') : $nodeId->toArray());
            } else {
                $node = static::find($nodeId);
            }
        } else {
            $node = $nodeId;
        }

        $nodeName = '';
        while ($node) {
            $nodeName = $node[$this->getTitleColumn()] . ($nodeName ? $prefix . $nodeName : $nodeName);
            $node = $node->parent;
        }

        return $nodeName;
    }

    /**
     * Build options of select field in form. as FullPath
     *
     * @param array    $nodes
     * @param int|null $parentId
     * @param string   $prefix
     *
     * @return array
     */
    protected function buildFullPath(array $nodes = [], $parentId = 0, $prefix = '', $except = null): array
    {
        $prefix = $prefix ?: '/';

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $nodeKey => $node) {
            $node[$this->titleColumn] = $this->getFullPathName($node, $prefix);//$node[$this->getTitleColumn()];
            $node[$this->parentColumn] = $node[$this->parentColumn] ?? 0;
            $node[$this->getKeyName()] = $node[$this->getKeyName()] ?? 0;

            if ($except != null && $node['id'] == $except) {
                continue;
            }

            if (is_null($parentId) || $node[$this->parentColumn] == $parentId) {
                $children = $this->buildFullPath($nodes, $node[$this->getKeyName()], $prefix, $except);

                $options[$node[$this->getKeyName()]] = $node[$this->titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    /**
     * Build options of select field in form.
     *
     * @param array    $nodes
     * @param int|null $parentId
     * @param string   $prefix
     *
     * @return array
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '', $except = null): array
    {
        $prefix = $prefix ?: str_repeat('&nbsp;', 4) . '';

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $nodeKey => $node) {
            if ($except != null && $node['id'] == $except) {
                continue;
            }
            $node[$this->titleColumn] = $prefix . '&nbsp;' . $node[$this->getTitleColumn()];
            if (is_null($parentId) || $node[$this->parentColumn] == $parentId) {
                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $prefix . $prefix, $except);

                $options[$node[$this->getKeyName()]] = $node[$this->titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function delete()
    {
        /** @var \Model $this */
        $this->where($this->parentColumn, $this->getKey())->delete();

        return parent::delete();
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving( static function (\Model $branch) {
            $parentColumn = $branch->getParentColumn();

            if (Request::has($parentColumn) && Request::input($parentColumn) == $branch->getKey()) {
                throw new \RuntimeException( trans( 'admin.parent_select_error'));
            }

            if (Request::has('_order')) {
                $order = Request::input('_order');

                Request::offsetUnset('_order');

//                static::tree()->saveOrder($order);

                return false;
            }

            return $branch;
        });
    }
}
