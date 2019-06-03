<?php

namespace SingleQuote\DataTables\Controllers;

use SingleQuote\DataTables\DataTable as ParentClass;
use Illuminate\Support\Str;
use Request;

/**
 * An laravel jquery datatables package
 *
 * @author Wim Pruiksma
 */
class DataTable extends ParentClass
{
    /**
     * The collection model
     *
     * @var mixed
     */
    protected $model;

    /**
     * The collection table model
     *
     * @var mixed
     */
    protected $tableModel;

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
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param TableModel $tableModel
     * @return mixed
     */
    public function __construct($model, $tableModel)
    {
        $this->model            = $model;
        $this->cacheName        = "datatables::{$tableModel->id}";
        
        $this->originalModel    = cache()->rememberForever("{$this->cacheName}originalModel", function() use($model){
            return $model->get()->first();
        });

        $this->tableModel       = $tableModel;
        
        return $this->build();
    }

    /**
     * Build the collection for the datatable
     *
     * @return $this
     * @author Wim Pruiksma
     */
    public function build()
    {
        $this->draw   = Request::get('draw');
        $this->column = $this->filterColumns(Request::get('columns'));

        foreach(Request::get('order') as $index => $order){
            $col = $this->column[$order['column']];

            $this->order[$index]  = [
                'column' => $col['data'],
                'dir' => $order['dir']
            ];
        }

        $this->start        = Request::get('start');
        $this->length       = Request::get('length');
        $this->search       = Request::has('search') && Request::get('search')['value'] ? Request::get('search') : null;
        $this->id           = Request::get('id');
        
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
        if(!$columns){
            return [];
        }
        $fields = [];
        foreach($columns as $key => $column){
            if( $column['data'] ||  $column['name']){
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
        foreach($searchkeys as $key => $value){
            $data = is_array($value) ? $value['data'] : $value;
            if(str_contains($data, '.')){
                $last[] = $data;
            }else{
                $this->searchable[] = $data;
            }
        }
        $this->searchable = array_merge($this->searchable, $last);
    }

    /**
     * Run the query
     * return as json string
     * @author Wim Pruiksma
     */
    public function get()
    {
        $data = $this->execute();

        $data['draw'] = $this->draw;

        $response = response()->json($data);

        foreach($response->headers->all() as $header => $value){
            $set = implode($value, ',');
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

        if ($this->search) {
            $this->searchOnModel();
        }

        $model = $this->sortModel();

        $build = collect([]);

        $model->each(function($item, $key) use ($build) {
            $build->put($key+$this->start, $item);
        });

        $collection  = $this->encryptKeys($build->unique()->values()->toArray());

        $data['recordsTotal']    = $count;
        $data['recordsFiltered'] = $count;
        $data['data']            = $collection ?? [];

        return $data;
    }

    /**
     * Order the model, check if it's a relation or not
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function sortModel()
    {
        $model = $this->model->skip($this->start)->take($this->length);

        foreach($this->order as $order){

            $model = $this->runOrderBuild($model, $order);

        }
        
        return $model->get();
    }

    /**
     * Run the builder for the order method
     *
     * @param Builder $model
     * @param array $order
     * @return Builder
     */
    private function runOrderBuild($model, array $order)
    {
        if(str_contains($order['column'], '.')){
            $relation   = $this->getPath($this->findOriginalColumn($order['column']));
            $name       = $this->getName($this->findOriginalColumn($order['column']));

            $foreignName    = $this->originalModel->{$relation}()->getQualifiedForeignKeyName();
            $ownerName      = $this->originalModel->{$relation}()->getQualifiedOwnerKeyName();
            $relationName   = $this->getPath($ownerName);

            return $model->leftJoin($relationName, $foreignName, '=', $ownerName)
                ->orderBy("$relationName.$name", $order['dir']);
        }

        return $model->orderBy($order['column'], $order['dir']);
    }


    /**
     * Search on the model
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @return \Illuminate\Database\Eloquent\Collection
     * @author Wim Pruiksma
     */
    private function searchOnModel()
    {
        $this->model = $this->model->where(function($query){
            foreach($this->searchable as $index => $column){
                $this->searchOnRelation($column, $query);
                $this->searchOnQuery($column, $query, $index);
            }
        });
    }

    /**
     * Execute the search queries
     *
     * @param string $column
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $index
     */
    private function searchOnQuery(string $column, \Illuminate\Database\Eloquent\Builder $query, int $index)
    {
        if($index === 0 && !str_contains($column, '.')){
            $query->whereRaw("lower($column) LIKE ?", "%{$this->search['value']}%");
        }elseif($index > 0 && !str_contains($column, '.')){
            $query->orWhereRaw("lower($column) LIKE ?", "%{$this->search['value']}%");
        }
    }

    /**
     * Search on relation
     *
     * @param string $column
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function searchOnRelation(string $column, \Illuminate\Database\Eloquent\Builder $query)
    {
        
        if(str_contains($column, '.')){
            
            $original = $this->findOriginalColumn($column);

            $explode = explode('.', $original);

            $query->orWhereHas($explode[0], function($query) use($explode){
                $query->whereRaw("lower($explode[1]) LIKE ?", "%{$this->search['value']}%");
            });
        }
    }

    /**
     * Get original column name.
     * Needed for relations
     *
     * @param string $column
     * @return string
     */
    private function findOriginalColumn(string $column) : string
    {
        foreach($this->tableModel->columns as $view){
            if($view['data'] === $column){
                return $view['original'];
            }
        }
        return $column;
    }

    /**
     * Encrypt the given keys
     *
     * @param array $data
     * @return array
     * @author Wim Pruiksma
     */
    protected function encryptKeys($data)
    {
        foreach($data as $key => $value){
            if(is_array($value)){
                $data[$key] = $this->encryptKeys($value);
            }else{
                $data[$key] = $this->encryptValues($key, $value);
            }
        }
        return $data;
    }

    /**
     * Encrypt the value keys
     *
     * @param mixed $value
     * @return mixed
     */
    private function encryptValues($key, $value)
    {
        if(!is_array($this->tableModel->encrypt)){
            return $value;
        }
        if(in_array($key, $this->tableModel->encrypt)){
            return encrypt($value);
        }else{
            return $value;
        }
    }
}
