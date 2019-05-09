<?php

namespace ACFBentveld\DataTables;

use ACFBentveld\DataTables\DataTablesException;
use Request;
use Schema;
use ACFBentveld\DataTables\DataTablesQueryBuilders;
use ACFBentveld\DataTables\DataTablesColumnBuilder;

/**
 * An laravel jquery datatables package
 *
 * @author Wim Pruiksma
 */
class DataTables extends DataTablesQueryBuilders
{
    /**
     * The collectiosn model
     *
     * @var mixed
     * @author Wim Pruiksma
     */
    protected $model;

    /**
     * Set to true to enable caching
     *
     * @var boolean
     */
    protected $remember = false;

    /**
     * Set the keys for encrypting
     *
     * @var array
     * @author Wim Pruiksma
     */
    protected $encrypt;

    /**
     * Set the search keys
     *
     * @var array
     * @author Wim Pruiksma
     */
    protected $search;

    /**
     * The database columns
     *
     * @var mixed
     * @author Wim Pruiksma
     */
    protected $columns;

    /**
     * The database table name
     *
     * @var string
     * @author Wim Pruiksma
     */
    protected $table;

    /**
     * Searchable keys
     *
     * @var array
     * @author Wim Pruiksma
     */
    protected $searchable;
    
    /**
     * The table ID
     *
     * @var mixed
     * @author Wim Pruiksma
     */
    protected $tableid = false;

    /**
     * If datables has searchable keys
     *
     * @var boolean
     * @author Wim Pruiksma
     */
    protected $hasSearchable = false;

    /**
     * Set the class and create a new model instance
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     * @throws DataTablesException
     * @author Wim Pruiksma
     */
    public function model($model)
    {
        $this->instanceCheck($model);
        $this->build();
        $this->model            = $model;
        $this->columnBuilder    = new DataTablesColumnBuilder($this->model);
        $this->table            = $this->model->getTable();
        $this->columns          = Schema::getColumnListing($this->table);
        return $this;
    }

    /**
     * The collect method
     * Really bad for performance
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @return $this
     * @throws DataTablesException
     * @author Wim Pruiksma
     */
    public function collect($collection)
    {
        $this->instanceCheck($collection);
        $allowedID              = $collection->pluck('id');
        $first                  = $collection->first();
        $empty                  = $first ? new $first : null;
        $this->build();
        $this->model            = $first ? $first::query()->whereIn('id', $allowedID) : null;
        $this->columnBuilder    = new DataTablesColumnBuilder($this->model);
        $this->table            = $first ? $empty->getTable() : null;
        $this->columns          = Schema::getColumnListing($this->table);
        return $this;
    }

    /**
     * Build the collection for the datatable
     *
     * @return $this
     * @author Wim Pruiksma
     */
    public function build()
    {
        if (Request::has('draw')) {
            $this->response = 'json';
        
            $this->draw   = Request::get('draw');
            $this->column = $this->filterColumns(Request::get('columns'));
            $col = $this->column[Request::get('order')[0]['column']];

            $this->order  = [
                'column' => isset($col['name']) ? $col['name'] : $col['data'],
                'dir' => Request::get('order')[0]['dir']
            ];

            $this->start  = Request::get('start');
            $this->length = Request::get('length');
            $this->search = Request::has('search') && Request::get('search')['value'] ? Request::get('search') : null;
        }
        return $this;
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
     * Check the instance of the given model or collection
     *
     * @param type $instance
     * @return boolean
     * @throws DataTablesException
     * @author Wim Pruiksma
     */
    protected function instanceCheck($instance)
    {
        if (
            !$instance instanceof \Illuminate\Database\Eloquent\Model &&
            !$instance instanceof \Illuminate\Database\Eloquent\Collection &&
            !$instance instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany &&
            !$instance instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo &&
            !$instance instanceof \Illuminate\Database\Eloquent\Relations\HasMany &&
            !$instance instanceof \Illuminate\Database\Eloquent\Relations\HasOne
        ) {
            throw new DataTablesException('Model must be an instance of Illuminate\Database\Eloquent\Model or an instance of Illuminate\Database\Eloquent\Collection');
        }
        return true;
    }

    /**
     * Enable caching
     * Check if the cache exists.
     * If the cache exists, stop executing and return the json
     *
     * @return $this
     * @deprecated since version 2.0.17
     */
    public function remember(string $name, int $minutes = 60)
    {
        $this->remember = true;
        $this->cacheName = "$name";
        $this->cacheFor = $minutes;
        return $this;
    }

    /**
     * Set the searchkeys
     *
     * @param mixed $searchkeys
     * @return $this
     */
    public function searchable(... $searchkeys)
    {
        $last = [];
        foreach($searchkeys as $key => $value){
            if(str_contains($value, '.')){
                $last[] = $value;
            }else{
                $this->searchable[] = $value;
            }
        }
        $this->searchable = array_merge($this->searchable, $last);
        $this->hasSearchable = true;
        return $this;
    }

    /**
     * Run the query
     * return as json string
     * @author Wim Pruiksma
     */
    public function get()
    {
        if(!Request::has('draw')
            || ($this->tableid !== false
            && Request::has("table")
            && Request::get('table') !== $this->tableid) ){
            return $this->columnBuilder;
        }
        
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

        if ($this->model && $this->search && $this->hasSearchable) {
            $this->searchOnModel();
        }
        
        $model = $this->model ? $this->sortModel() : null;
        
        $build = collect([]);        

        if($model){
			
			$model->each(function($item, $key) use ($build) {
				$build->put($key+$this->start, $item);
			});
			
            $collection  = $this->encryptKeys($build->unique()->values()->toArray());
        }
        
        $data['recordsTotal']    = $count;
        $data['recordsFiltered'] = $count;
        $data['data']            = $collection ?? [];
        
        return $data;
    }

    /**
     * Order the model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function sortModel()
    {

        $build = $this->hasSearchable ? $this->model->skip($this->start)->take($this->length) : $this->model;

        $sortByRelation = str_contains($this->order['column'], '.');

        if($sortByRelation){
            $model = $this->order['dir'] === 'asc' ? $build->get()->sortBy($this->order['column']) : $build->get()->sortByDesc($this->order['column']);
        }else{
            $model = $build->orderBy($this->order['column'], $this->order['dir'])->get();
        }

        if($this->search && !$this->hasSearchable){
            $model = $this->searchOnCollection($model);
        }

        if(!$this->hasSearchable){
            return $model->slice($this->start, $this->length);
        }

        return $model;
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
        $this->model->where(function($query){
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
            $explode = explode('.', $column);

            $query->orWhereHas($explode[0], function($query) use($explode){
                $query->whereRaw("lower($explode[1]) LIKE ?", "%{$this->search['value']}%");
            });
        }
    }    

    /**
     * Create a macro search on the collection
     *
     * @param mixed $collection
     * @return collection
     */
    private function searchOnCollection($collection)
    {
        $this->createSearchMacro();
        $this->createSearchableKeys();
        $search = $this->search['value'];
        $result = [];
        foreach ($this->searchable as $searchKey) {
            $result[] = $collection->like($searchKey, strtolower($search));
        }
        return collect($result)->flatten();
    }

    /**
     * Create searchable keys
     * If none given it creates its own
     *
     * @author Wim Pruiksma
     */
    private function createSearchableKeys()
    {
        $builder = $this->model;
        foreach ($this->column as $column) {
            $name = str_before($column['data'], '.');
            if ($column['searchable'] != true) {
                continue;
            }
            if (in_array($name, $this->columns)) {
                $this->searchable[] = $name;
                continue;
            }
            if ($name !== 'function' && $builder->has($name) && $builder->first()) {
                if (optional($builder->first()->$name)->first()) {
                    $collect = $builder->first()->$name;
                    foreach ($collect->first()->toArray() as $col => $value) {
                        $type  = $collect instanceof \Illuminate\Database\Eloquent\Collection ? '.*.' : '.';
                        $this->searchable[] = $name.$type.$col;
                    }
                }
            }
        }
    }

    /**
     * Create a macro for the collection
     * It searches inside the collections
     *
     * @author Wim Pruiksma
     */
    private function createSearchMacro()
    {
        \Illuminate\Database\Eloquent\Collection::macro('like',
            function ($key, $search) {
                return $this->filter(function ($item) use ($key, $search) {
                    $collection = data_get($item, $key, '');
                    if (is_array($collection)) {
                        foreach ($collection as $collect) {
                            $contains = str_contains(strtolower($collect),
                                    $search) || str_contains(strtolower($collect),
                                    $search) || strtolower($collect) == $search;
                            if ($contains) {
                                return true;
                            }
                        }
                    } else {
                        return str_contains(strtolower(data_get($item, $key, '')),
                            $search);
                    }
                });
            });
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
        if(!is_array($this->encrypt)){
            return $value;
        }
        if(in_array($key, $this->encrypt)){
            return encrypt($value);
        }else{
            return $value;
        }
    }

    /**
     * Set the keys to encrypt
     *
     * @param mixed $encrypt
     * @return $this
     * @author Wim Pruiksma
     */
    public function encrypt(...$encrypt)
    {
        $this->encrypt = (isset($encrypt[0]) && is_array($encrypt[0])) ? $encrypt[0]
                : $encrypt;
        return $this;
    }
    
    /**
     * Set the table
     *
     * @param string $table
     * @return $this
     * @author Wim Pruiksma
     */
    public function table(string $table)
    {
        $this->tableid = $table;
        return $this;
    }

    /**
     * Use the function to exclude certain column
     *
     * @param mixed $noselect
     * @return $this
     * @deprecated in version ^2.0.0
     * @author Wim Pruiksma
     */
    public function noSelect($noselect)
    {
        return $this->exclude($noselect);
    }

    /**
     * Keys are always returned so this method is depricated
     *
     * @return $this
     * @deprecated in version ^2.0.0
     * @author Wim Pruiksma
     */
    public function withKeys()
    {
        return $this;
    }
}
