<?php
namespace SingleQuote\DataTables;

use SingleQuote\DataTables\Controllers\DataTable as ServerSide;
use SingleQuote\DataTables\DataTableException;
use SingleQuote\DataTables\Fields\Label;
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
     * Constructor
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * The columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Set the model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     * @throws DataTableException
     */
    public function model($model)
    {
        if($model instanceof \Illuminate\Database\Eloquent\Model || is_string($model)){

            if(is_string($model)){
                $model = new $model;
            }

            $this->model            = $model;
            $this->originalModel    = $model;
            return $this;
        }
        throw new DataTableException("$model must be an an instance of \Illuminate\Database\Eloquent\Model");
    }

    /**
     * Create the tbale model
     *
     * @param string $class
     * @param mixed $params
     * @return $this
     */
    public function tableModel(string $class, ... $params)
    {
        $reflection = new \ReflectionClass($class);
        $this->view = $reflection->newInstanceArgs($params);
        $this->view->id = base64_encode($class);

        if(Request::filled('laravel-datatables') && Request::filled('filter')){
            $filters = $this->parseFilters(Request::get('filter'));
            $this->view->filtered = $filters;
        }

        $this->view->make($this->model, $params);

        $this->checkColumns();

        if(Request::filled('laravel-datatables')){
            return new ServerSide($this->view->query, $this->view);
        }

        return $this->build();
    }

    /**
     * Parse the filters
     *
     * @param string $encoded
     * @return array
     */
    private function parseFilters(string $encoded) : array
    {
        $explode = explode('|', rtrim($encoded, '|'));

        $filters = [];

        foreach($explode as $index => $value){
            $name = Str::before($value, ';');
            $multiple = Str::contains($value, ';m*');
            $value = Str::after($value, '*');
            if(!$value || strlen($value) === 0){
                continue;
            }
            $filters[$name] = (object)['name' => $name, 'value' => $value, 'multiple' => $multiple];
        }

        return $filters;
    }

    /**
     * Build the table
     *
     * @return $this
     */
    private function build()
    {
        $this->checkFilters();
        $this->checkDefs();
        $this->generateTable();
        $this->generateScripts();

        return $this;
    }

    /**
     * Build the filters
     *
     */
    private function checkFilters()
    {
        foreach($this->view->filters  as $index => $filter){
            $this->view->filters[$index]->build = $filter->build();
        }
    }

    /**
     * Check the columns and fill the defs
     * Fill the defs and set the targets for every column
     *
     */
    private function checkColumns()
    {
        foreach($this->view->columns as $index => $column){
            $data       = is_array($column) ? isset($column['data']) ? $column['data'] : null  : $column;
            $original   = $data;
            $name       = is_array($column) ? isset($column['name']) ? $column['name'] : null  : null;
            $searchable = is_array($column) ? isset($column['searchable']) ? $column['searchable'] : true  : true;
            $orderable  = is_array($column) ? isset($column['orderable']) ? $column['orderable'] : true  : true;
            $class      = is_array($column) ? isset($column['class']) ? $column['class'] : null  : null;
            $columnSearch = is_array($column) ? isset($column['columnSearch']) ? $column['columnSearch'] : false  : null;

            if(Str::contains($data, ' as ')){
                $name = $name ?? Str::after($data, ' as ');
                $data = Str::before($data, ' as ');
            }

            if(Str::contains($data, '.')){
                $explode = explode('.', $data);
                array_pop($explode);
                $this->view->query = $this->view->query->with(implode('.', $explode));
            }

            $this->buildColumns($index, $data, $name ?? $data, $original, $searchable, $orderable, $class, $columnSearch);

            $this->columns[] = $name ?? $data;

            $this->buildColumnsDef($index, $name ?? $data, $class);
        }
    }


    /**
     * BUild the columns list
     *
     * @param int $index
     * @param string $data
     * @param string $name
     * @param bool $searchable
     * @param bool $orderable
     * @param string $class
     */
    private function buildColumns(int $index, string $data, string $name, string $original, bool $searchable, bool $orderable, string $class = null, bool $columnSearch = false)
    {
        $this->view->columns[$index] = [
            'data'          => $this->toLower($data),
            'name'          => $this->toLower($name),
            'original'      => $original,
            'searchable'    => $searchable,
            'orderable'     => $orderable ?? true,
            'class'         => $class,
            'columnSearch' => $columnSearch,
        ];
    }

    /**
     * Build the columns def
     *
     * @param int $index
     * @param string $data
     * @param string $class
     */
    private function buildColumnsDef(int $index, string $data, string $class = null)
    {
        $this->view->defs[$data] = [
            'class'     => $class,
            'id'        => uniqid('column'),
            'target'    => $index,
            'def'       => []
        ];
    }

    /**
     * Translate someVariable to some_variable
     * Needed for relations
     *
     * @param string $string
     * @return string
     */
    private function toLower(string $string) : string
    {
        return strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $string));
    }

    /**
     * Fill in the fields defs
     * Fill the defs when there is no field set
     * Check if the column does exist in the column array
     *
     */
    private function checkDefs()
    {
        foreach($this->view->fields as $index => $field){
            if(is_array($field)){
                foreach($field as $item){
                   $this->view->fields[] = $item;
                }
                unset($this->view->fields[$index]);
            }
        }

        foreach($this->view->fields as $field){
            if(array_search($field->column, $this->columns) !== false){
                $this->view->defs[$field->column]['def'][] = $field;
            }
        }

        $this->buildDef();
    }

    /**
     * Render the defs
     * THe defs define the columns behaviour
     *
     */
    private function buildDef()
    {
        foreach($this->view->defs as $column => $def){
            if(count($def['def']) === 0){
                $this->view->defs[$column]['def'] = [Label::make($column)];
            }
        }

        foreach($this->view->defs as $column => $def){
            foreach($def['def'] as $index => $field){
                $rendered = $this->getBetweenTags($field->build(), 'script');
                if($field->permissions || $field->roles){
                    $rendered = $this->checkMiddlewares($field, $rendered);
                }

                $this->view->defs[$column]['rendered'][$index] = $rendered;
            }
        }
    }

    /**
     * Run the middleware checks
     * Remove the complete code output of a field when the middleware fails
     *
     * @param object $field
     * @param string $rendered
     * @return string
     */
    private function checkMiddlewares(object $field, string $rendered)
    {
        $proceedRole = count($field->roles) > 0;
        $proceedPermission = count($field->permissions) > 0;

        foreach($field->roles as $roles){
            $check = array_filter($roles, function($role){
                return Request::user()->hasRole($role);
            });
            $proceedRole = count($roles) === count($check);
        }

        foreach($field->permissions as $permissions){
            $check = array_filter($permissions, function($permission){
                return Request::user()->can($permission);
            });
            $proceedPermission = count($permissions) === count($check);
        }
        if(!$proceedPermission && !$proceedRole){
            return "return '';";
        }

        return $rendered;
    }

    /**
     * Return the string inside the tags
     *
     * @param string $string
     * @param string $tagname
     * @return string
     */
    private function getBetweenTags(string $string, string $tagname) : string
    {
        $after = Str::after($string, "<$tagname>");

        return Str::before($after, "</$tagname>");
    }

    /**
     * Generate the table
     * The tables are generated in a view
     *
     */
    private function generateTable()
    {
        $view = $this->view;

        return view("laravel-datatables::table")
            ->with(compact('view'))
            ->render();
    }

    /**
     * Generate the scripts
     * The scripts are generated in a view
     *
     */
    private function generateScripts()
    {
        $view = $this->view;

        return view("laravel-datatables::scripts")
            ->with(compact('view'))
            ->render();
    }

    /**
     * Return the generated table
     *
     * @return string
     */
    public function table()
    {
        return $this->generateTable();
    }

    /**
     * return the generated script
     *
     * @return string
     */
    public function script()
    {
        return $this->generateScripts();
    }


    /**
     * Get the column path for example relation.name becomes relation
     *
     * @return string
     */
    public function getPath(string $string = null) : string
    {
        $explode = explode('.', $string);
        array_pop($explode);
        if(count($explode) === 0){
            return "";
        }
        return implode('.', $explode);
    }

    /**
     * Return the column name for example relation.name becomes name
     *
     * @return string
     */
    public function getName(string $string) : string
    {
        $explode = explode('.', $string);
        return array_pop($explode) ?? "";
    }


}
