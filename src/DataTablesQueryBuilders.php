<?php

namespace ACFBentveld\DataTables;

use App\Http\Controllers\Controller;

/**
 *
 */
class DataTablesQueryBuilders extends Controller
{

    /**
     * call method
     *
     * @param string $name
     * @param mixed $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name) && starts_with($name, 'where')) {
            return $this->where(strtolower(preg_replace('/([A-Z]+)/', "_$1", lcfirst(str_after($name, 'where')))), ... $arguments);
        }
        return $this;
    }

    /**
     * Set the query builders for where
     *
     * @param string $column
     * @param mixed $seperator
     * @param mixed $value
     * @return $this
     */
    public function where(string $column, $seperator, $value = null)
    {
        $this->model   = $this->model->where($column, $seperator, $value);
        $this->where[] = [
            $column, $seperator, $value
        ];
        return $this;
    }

    /**
     * Set the query builders for whereIn
     *
     * @param string $column
     * @param mixed $seperator
     * @param mixed $value
     * @return $this
     */
    public function whereIn(string $column, $value)
    {
        $this->model     = $this->model->whereIn($column, $value);
        $this->whereIn[] = [
            $column, $value
        ];
        return $this;
    }

    /**
     * Set the query builders
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function whereHas(string $column, $value = null)
    {
        $this->model = $this->model->whereHas($column, $value);
        return $this;
    }

    /**
     * Set the query builders
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function orWhereHas(string $column, $value = null)
    {
        $this->model = $this->model->orWhereHas($column, $value);
        return $this;
    }

    /**
     * Set the query builders
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function whereYear(string $column, $value)
    {
        $this->model   = $this->model->whereYear($column, $value);
        $this->where[] = [
            $column, $value
        ];
        return $this;
    }

    /**
     * Add a scope
     *
     * @param string $scope
     * @param mixed $data
     * @return $this
     */
    public function addScope(string $scope, $data = null)
    {
        $this->model = $this->model->{$scope}($data);
        return $this;
    }

    /**
     * Querying soft deleted models
     * Only works on soft delete models
     *
     * @return $this
     */
    public function withTrashed()
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    /**
     * Querying soft deleted models
     * Only works on soft delete models
     *
     * @return $this
     */
    public function onlyTrashed()
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    /**
     * Set the relations
     *
     * @param mixed $with
     * @return $this
     */
    public function with(...$with)
    {
        $with       = (isset($with[0]) && is_array($with[0])) ? $with[0] : $with;
        $this->with = $with;
        $this->model = $this->model->with($with);
        return $this;
    }

    /**
     * Exclude columns from selection
     *
     * @param mixed $exclude
     * @return $this
     */
    public function exclude(...$exclude)
    {
        foreach ($this->columns as $key => $column) {
            if (in_array($column, $exclude)) {
                unset($this->columns[$key]);
            }
        }
        return $this;
    }

    /**
     * Select keys
     *
     * @param array $exclude
     * @return $this
     * @throws DataTablesException
     */
    public function select(...$exclude)
    {
        foreach ($this->columns as $key => $column) {
            if (!in_array($column, $exclude)) {
                unset($this->columns[$key]);
            }
        }
        return $this;
    }
}
