<?php
namespace SingleQuote\DataTables\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Request;
use SebastianBergmann\CodeCoverage\Node\Builder as Builder2;
use SingleQuote\DataTables\DataTable as ParentClass;
use Str;

/**
 * An laravel jquery datatables package
 *
 * @author Wim Pruiksma
 */
class DataTable extends ParentClass
{

    /**
     * The original collection model
     *
     * @var mixed
     */
    protected $originalModel;

    /**
     * Set the search keys
     *
     * @var array
     */
    protected $searchable = [];

    /**
     * The columns with searchable inputs
     *
     * @var array
     */
    protected $filterSearch = [];

    /**
     * Has searchables for columns
     * SearchFilter columns
     * 
     * @var bool
     */
    protected $hasSearch = false;

    /**
     * Global search field
     * 
     * @var mixed 
     */
    protected $search;

    /**
     * 
     * @param Elequent $model
     * @param Elequent $tableModel
     * @return mixed
     */
    public function __construct(protected $model, protected $tableModel)
    {
        $this->getSearchableColumns();

        $this->originalModel = $this->model->getModel();
                        
        return $this->build();
    }

    /**
     * Set the columns search fields.
     * Is used to filter a single column
     */
    private function getSearchableColumns()
    {
        $inputs = rtrim(Request::get('filtersearch', '|'), '|');
        $searchables = explode('|', $inputs);
        foreach ($searchables as $searchable) {
            $keys = explode(';', $searchable);
            if (count($keys) === 2) {
                $this->hasSearch = true;
                $this->filterSearch[] = $keys;
            }
        }
    }

    /**
     * Build the collection for the datatable
     *
     * @return $this
     * @author Wim Pruiksma
     */
    public function build()
    {
        $this->draw = Request::get('draw');
        $this->column = $this->filterColumns(Request::get('columns'));

        foreach (Request::get('order') as $index => $order) {
            $col = $this->column[$order['column']];

            $this->order[$index] = [
                'column' => $col['data'],
                'dir' => $order['dir']
            ];
        }

        $this->start = Request::get('start');
        $this->length = Request::get('length');
        $this->search = Request::has('search') && Request::get('search')['value'] ? Request::get('search') : null;
        $this->id = Request::get('id');

        $this->searchable($this->tableModel->searchable ?? $this->tableModel->columns);
        
        return $this->get();
    }

    /**
     * Filter columns on nullable results
     * Remove them from the arrya
     *
     * @param array $columns
     */
    private function filterColumns(array $columns = null)
    {
        if (!$columns) {
            return [];
        }
        $fields = [];
        foreach ($columns as $key => $column) {
            if ($column['data'] || $column['name']) {
                $fields[] = $column;
            }
        }
        return $fields;
    }

    /**
     * Set the searchkeys
     *
     * @param array $searchkeys
     */
    private function searchable(array $searchkeys)
    {
        $last = [];
        foreach ($searchkeys as $key => $value) {
            
            if(isset($value['searchable']) && !$value['searchable']){
                continue;
            }
            
            $data = is_array($value) ? $value['data'] : $value;
            if (Str::contains($data, '.')) {
                $last[] = $data;
            } else {
                $this->searchable[] = $data;
            }
        }
        $this->searchable = array_merge($this->searchable, $last);
    }

    /**
     * Run the query
     * return as json string
     *
     * @author Wim Pruiksma
     */
    public function get()
    {
        $data = $this->execute();

        $data['draw'] = $this->draw;

        $response = response()->json($data);

        foreach ($response->headers->all() as $header => $value) {
            $set = implode(',', $value);
            header("$header: $set");
        }

        echo $response->getContent();
        exit;
    }

    /**
     * execute the queries
     *
     * @return array
     */
    protected function execute()
    {
        $count = $this->model ? $this->model->count() : 0;

        if ($this->search || $this->hasSearch) {
            $this->searchOnModel();
        }

        $model = $this->sortModel();

        $build = collect([]);
        
        $model->each(
            function ($item, $key) use ($build) {
                $build->put($key + $this->start, $item);
            }
        );
        
        $middlewared = Request::user() ? $this->runMiddleware($build) : $build;
        
        $collection = $this->encryptKeys($middlewared->unique()->values()->toArray());

        $data['recordsTotal'] = $count;
        $data['recordsFiltered'] = $count;
        $data['data'] = $collection ?? [];

        return $data;
    }

    /**
     * Run the middleware checks
     *
     * @param  object $collection
     * @return object
     */
    private function runMiddleware(object $collection): object
    {
        $middlewares = array_filter(
            $this->tableModel->fields, function ($field) {
                return is_object($field) && get_class($field) === 'SingleQuote\DataTables\Fields\Middleware';
            }
        );
        foreach ($middlewares as $middleware) {
            $collection = $this->filterResultsOnMiddleware($middleware, $collection, $middleware->middleware);
        }

        return $collection;
    }

    /**
     * Filter the results when a middleware has failed
     *
     * @param  object $middleware
     * @param  object $collection
     * @param  array  $restrictions
     * @return object
     */
    private function filterResultsOnMiddleware(object $middleware, object $collection, array $restrictions): object
    {
        $proceedRole = $this->filterRoles($restrictions);
        $proceedPermission = $middleware->middlewareModel ? true : $this->filterPermissions($restrictions);

        if (!$proceedPermission && !$proceedRole) {
            $collection->each(
                function ($model) use ($middleware) {
                    $model->{$middleware->column} = null;
                }
            );
        }

        if ($middleware->middlewareModel) {
            $collection->each(
                function ($model) use ($middleware, $restrictions) {
                    $modelItem = $middleware->middlewareModel === 'model' ? $model : $middleware->middlewareModel;
                    $model->idem = $model->{$middleware->column};
                    $model->{$middleware->column} = $this->filterPermissions($restrictions, $modelItem) ? $model->{$middleware->column} : null;
                }
            );
        }

        return $collection;
    }

    /**
     * Filter the user roles
     * 
     * @param  array $restrictions
     * @return bool
     */
    private function filterRoles(array $restrictions): bool
    {
        $proceedRole = !count($restrictions['roles']) > 0;

        foreach ($restrictions['roles'] as $roles) {
            $check = array_filter(
                $roles, function ($role) {
                    return Request::user()->hasRole($role);
                }
            );
            $proceedRole = count($roles) === count($check);
        }

        return $proceedRole;
    }

    /**
     * Filter the user permissions.
     * 
     * @param  array $restrictions
     * @param  mixed $model
     * @return bool
     */
    private function filterPermissions(array $restrictions, $model = null): bool
    {
        $proceedPermission = !count($restrictions['permissions']) > 0;
        foreach ($restrictions['permissions'] as $permissions) {
            $check = array_filter(
                $permissions, function ($permission) use ($model) {
                    return Request::user()->can($permission, $model);
                }
            );
            $proceedPermission = count($permissions) === count($check);
        }

        return $proceedPermission;
    }

    /**
     * Order the model, check if it's a relation or not
     *
     * @return Collection
     */
    private function sortModel()
    {
        $model = $this->model->skip($this->start)->take($this->length);

        foreach ($this->order as $order) {
            $model = $this->runOrderBuild($model, $order);
        }

        try{
            return $model->prefix($this->tableModel->elequentPrefix)->{$this->tableModel->elequentMethod}();
        } catch (Exception $ex) {
            return $model->get();
        }
    }

    /**
     * Run the builder for the order method
     *
     * @param  Builder2 $model
     * @param  array   $order
     * @param  array   $select
     * @return Builder2
     */
    private function runOrderBuild($model, array $order)
    {        
        $column = $this->extractColumnFromTable($order['column']);
        
        if($column['json']){
            $order['column'] = str_replace('.', '->', $order['column']);
        }

        if (Str::contains($order['column'], '.')) {
            
            if(substr_count($order['column'], '.') > 1){
                return $model;
            }
            
            return $this->orderRelation($model, $order);
        }

        return $model->orderBy($order['column'], $order['dir']);
    }
    
    /**
     * @param mixed $model
     * @param array $order
     * @param array $select
     * @return mixed
     */
    private function orderRelation(mixed $model, array $order, array $select = []) : mixed
    {
        $relation = $this->getPath($this->findOriginalColumn($order['column']));
        $name = $this->getName($this->findOriginalColumn($order['column']));

        $foreignName = $this->getForeignName($relation);
        $ownerName = $this->getOwnerName($relation);

        $relationName = $this->getPath($foreignName);
        $owner = $this->getPath($ownerName);

        foreach($model->getQuery()->columns ?? ["*"] as $col){
            $select[] = "$owner.$col";
        }

        $as = str_replace('.', '_', "$relationName$name");

        $select[] = "$relationName.$name as $as";

        return $model->with($relation)
            ->join($relationName, $foreignName, '=', $ownerName)
            ->select($select)
            ->orderBy("$as", $order['dir']);
    }
    
    /**
     * @param string $data
     * @return array|null
     */
    private function extractColumnFromTable(string $data) : ?array
    {
        foreach($this->tableModel->columns as $column){
            if($column['data'] === $data){
                return $column;
            }
        }
        
        throw new \Exception;
    }
    
    /**
     * Get the owner name by relation class
     * 
     * @param string $relation
     * @return string
     */
    private function getForeignName(string $relation){
        $type = get_class($this->originalModel->{$relation}());
        $class = explode('\\', $type);

        switch(end($class)){
            case "BelongsTo" : 
                return $this->originalModel->{$relation}()->getQualifiedOwnerKeyName();
            default : 
                return $this->originalModel->{$relation}()->getQualifiedForeignKeyName();
        }        
    }
    
    /**
     * Get the owner name by relation class
     * 
     * @param string $relation
     * @return string
     */
    private function getOwnerName(string $relation){
        $type = get_class($this->originalModel->{$relation}());
        $class = explode('\\', $type);

        switch(end($class)){
            case "HasOne" : 
                return $this->originalModel->{$relation}()->getQualifiedParentKeyName();
            case "HasMany" : 
                return $this->originalModel->{$relation}()->getQualifiedParentKeyName();
            case "BelongsTo" : 
                return $this->originalModel->{$relation}()->getQualifiedForeignKeyName();
            case "BelongsToMany" : 
                return $this->originalModel->{$relation}()->getQualifiedForeignKeyName();
            default : 
                return $this->originalModel->{$relation}()->getQualifiedOwnerKeyName();
        }        
    }

    /**
     * Search on the model
     *
     * @param  Collection $collection
     * @return Collection
     * @author Wim Pruiksma
     */
    private function searchOnModel()
    {
        foreach ($this->filterSearch as $filterSearch) {
            $this->model = $this->model->where(function ($query) use($filterSearch) {
                $this->searchOnRelation($filterSearch[1], $filterSearch[0], $query);
                $this->searchOnQuery($filterSearch[1], $filterSearch[0], $query);
            });
        }
        
        if (!$this->search) {
            return;
        }

        $this->model = $this->model->where(function ($query) {
            foreach ($this->searchable as $column) {
                $this->searchOnRelation($this->search['value'], $column, $query);
                $this->searchOnQuery($this->search['value'], $column, $query);
            }
        });
    }

    /**
     * @param string $phrase
     * @param string $column
     * @param Builder $query
     * @param int $index
     * @param string $secondSearchType
     */
    private function searchOnQuery(string $phrase, string $column, Builder $query) : Builder
    {
        $table = $this->originalModel->getTable();
        $view = $this->extractColumnFromTable($column);
        
        if($view['json']){
            $jsonColumn = $this->parseJsonColumnName($column);
            $parent = Str::before($column, '.');
            return $query->orWhereRaw("lower(json_unquote(json_extract(`$parent`, $jsonColumn))) LIKE ?", "{$phrase}%");
        }
        
        if(!Str::contains($column, '.')){
            return $query->orWhereRaw("lower($table.$column) LIKE ?", "{$phrase}%");
        }
        
        return $query;
    }
    
    /**
     * @param string $column
     * @return string
     */
    private function parseJsonColumnName(string $column) : string
    {
        $fields = explode(".", $column);

        $parsed = "'$.";
        
        foreach($fields as $index => $field){
            if($index === 0){
                continue;
            }
            if(count($fields)  -1 === $index){
                return $parsed."\"$field\"'";
            }
            
            $parsed .= "\"$field\".";
        }
    }

    /**
     * Search on relation
     *
     * @param string                                $column
     * @param Builder $query
     */
    public function searchOnRelation(string $phrase, string $column, Builder $query)
    {
        $view = $this->extractColumnFromTable($column);
        
        if (Str::contains($column, '.') && !$view['json']) {
            $original = $this->findOriginalColumn($column);

            $explode = explode('.', $original);
            
            $query->orWhereHas($explode[0], function ($query) use ($explode, $phrase) {
                $query->whereRaw("lower($explode[1]) LIKE ?", "{$phrase}%");
            });
        }
    }

    /**
     * Get original column name.
     * Needed for relations
     *
     * @param  string $column
     * @return string
     */
    private function findOriginalColumn(string $column): string
    {
        foreach ($this->tableModel->columns as $view) {
            if ($view['data'] === $column) {
                return $view['original'];
            }
        }
        return $column;
    }

    /**
     * Encrypt the given keys
     *
     * @param  array $data
     * @return array
     * @author Wim Pruiksma
     */
    protected function encryptKeys($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->encryptKeys($value);
            } else {
                $data[$key] = $this->encryptValues($key, $value);
            }
        }
        return $data;
    }

    /**
     * Encrypt the value keys
     *
     * @param  mixed $value
     * @return mixed
     */
    private function encryptValues($key, $value)
    {
        if (!is_array($this->tableModel->encrypt)) {
            return $value;
        }
        if (in_array($key, $this->tableModel->encrypt)) {
            return encrypt($value);
        } else {
            return $value;
        }
    }
}