<?php

namespace SingleQuote\DataTables\Controllers;

use Illuminate\Support\Str;
use Request;

/**
 * An laravel jquery datatables package
 *
 * @author Wim Pruiksma
 */
class DataTable
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
        $this->model        = $model;
        $this->tableModel   = $tableModel;
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
        $col = $this->column[Request::get('order')[0]['column']];

        $this->order  = [
            'column' => $col['data'],
            'dir' => Request::get('order')[0]['dir']
        ];

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
        if($this->id !== $this->tableModel->id){
            dd('stp[p');
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
     * Order the model
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function sortModel()
    {
        if(str_contains($this->order['column'], '.')){

            $build = $this->model->get();

            $original = $this->findOriginalColumn($this->order['column']);
            
            return  $this->order['dir'] === 'asc' ? $build->sortBy($original)->slice($this->start, $this->length) : $build->sortByDesc($original)->slice($this->start, $this->length);
        }

        $build = $this->model->skip($this->start)->take($this->length);
        $model = $build->orderBy($this->order['column'], $this->order['dir'])->get();

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
