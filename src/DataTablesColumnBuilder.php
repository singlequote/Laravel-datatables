<?php
namespace ACFBentveld\DataTables;

use Illuminate\Support\Str;
/**
 *
 */
class DataTablesColumnBuilder
{
    /**
     * The registered columns
     *
     * @var array 
     */
    protected $columns = [];
    /**
     * The registered columns
     *
     * @var array
     */
    protected $columnDefs = [];

    /**
     * Registered fillable columns
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Set the table ID
     *
     * @var string
     */
    protected $tableID;

    /**
     * Set the type of log
     *
     * @var string
     */
    protected $log = "console";

    /**
     * Prevnt the package to use triggers
     *
     * @var bool
     */
    protected $preventTriggers = false;

    /**
     * Set the default classes
     *
     * @var array
     */
    protected $defaultClasses = [
        'table laravel-datatable'
    ];

    /**
     * Set the default config for the datatable
     *
     * @var array
     */
    protected $defaultConfig = [
        "paging"        => true,
        "autoWidth"     => false,
        "pageLength"    => 10,
        "processing"    => true,
        "serverSide"    => true,
        
    ];

    /**
     *
     * @param type $model
     */
    public function __construct($model)
    {
        $this->model    = $model;
        $this->fillable = $model ?$model->getFillable() : [];
        $this->defaultClasses = collect($this->defaultClasses);
        $this->tableID = uniqid("laravelDataTable_");
        $this->defaultConfig["ajax"] = url(\Request::route()->uri);
        $this->defaultConfig["language"] = [
            'lengthMenu' => __("datatables.lengthMenu"),
            'zeroRecords' => __("datatables.zeroRecords"),
            'info' => __("datatables.info"),
            'infoEmpty' => __("datatables.infoEmpty"),
            'infoFiltered' => __("datatables.infoFiltered"),
            'search' => __("datatables.search"),
            'paginate' => [
                'previous' => __("datatables.paginate.previous"),
                'next' => __("datatables.paginate.next")
            ]
        ];
        $this->defaultConfig["dom"] = "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>> <'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>";
    }

    /**
     * call method
     *
     * @param string $name
     * @param mixed $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name) && starts_with($name, 'set')) {
            return $this->set($name, $arguments);
        }
        return $this;
    }

    /**
     * Set prevention methods
     *
     * @param string $type
     * @param bool $value
     */
    public function prevent(string $type, bool $value = true)
    {
        $method = "prevent".ucFirst($type);
        $this->{$method} = $value;
        return $this;
    }

    /**
     * Set config
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    private function set(string $name, $value)
    {
        $this->defaultConfig[lcfirst(str_after($name, 'set'))] = $value[0];
        return $this;
    }

    /**
     * Add a new column
     *
     * @param string $column
     * @param array $closure
     * @return $this
     */
    public function addColumn(string $column, array $closure = [])
    {

        $length = count($this->columns);
        
        $this->defaultObject = $this->setDefaultObject($column, $length);
        $output = $this->defColumnCheck($column);
        $output .= "let output = '';";
        
        foreach($closure as $target => $response){
            $item = is_object($response) ? $response->build() : $response;
            if(is_array($item)){
                $output .= $item['code'];
                $output .= "output += {$item['output']}";
            }else{
                $output .= "output += $item";
            }
        }
        
        $output .= "return output;";

        $this->columnDefs[$length]['def'] = str_replace("\n", "", $output);

        if(count($closure) === 0){
            $this->columnDefs[$length]['def'] = "return row.$column;";
        }
        return $this;
    }

    /**
     * Set current columns label
     *
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->defaultObject->label = $label;
        return $this;
    }

    /**
     * Set the column class
     *
     * @param string $class
     * @return $this
     */
    public function class(string $class)
    {
        $this->columnDefs[$this->length]['class'] = $class;
        return $this;
    }

    /**
     * Set default column object
     *
     * @param string $column
     * @param int $length
     * @return \stdClass
     */
    private function setDefaultObject(string $column, int $length)
    {
        $defaultObject          = new \stdClass;
        $defaultObject->name    = $column;
        $defaultObject->data    = $column;
        $defaultObject->label   = null;

        $this->columns[$length] = $defaultObject;

        $this->columnDefs[$length] = [
            'column' => $column,
            'def' => null,
            'class' => null
        ];

        $this->length = $length;
        
        return $defaultObject;
    }

    /**
     * Export the table
     *
     * @param bool $return
     * @return string
     */
    public function exportTable($return = false)
    {
        $table = "<table id='$this->tableID' class='{$this->defaultClasses->implode(' ')}'><thead><tr>";
        foreach($this->columns as $item){
            $column = $this->fillColumn($item);
            $table .= "<th>$item->label</th>";
        }
        $table .= "</tr></thead></table>";

        if($return){ return $table; } echo $table;
    }

    /**
     * Export the scripts
     *
     * @param bool $return
     * @return string
     */
    public function exportScript($return = false)
    {
        
        $json = $this->buildJson();

        $call       = "$(\"#$this->tableID\").DataTable($json);";
        $logType    = $this->getLogType();
        $triggers   = $this->preventTriggers ? "" : $this->setTriggers();
        $script = "<script type='text/javascript'>$triggers {$this->documentScript($call, $logType)}</script>";
        
        if($return){ return $script; } echo $script;
    }

    /**
     * Set triggers
     *
     * @return string
     */
    private function setTriggers() : string
    {
        return "$(document).on('click', \"[data-url]\", (e) => { location.href=$(e.currentTarget).data('url'); });";
    }

    /**
     * Set the log type
     *
     * @return string
     */
    private function getLogType() : string
    {
        switch($this->log){
            case "console":
                return "$.fn.DataTable.ext.errMode = 'none'; $(\"#$this->tableID\").on( 'error.dt', ( e, settings, techNote, message ) => { console.error(message); });";
            default:
                return "";
        }
    }

    /**
     * Set the document load script
     *
     * @param array $script
     * @return string
     */
    private function documentScript(... $scripts) : string
    {
        $lines = implode(" ",$scripts);
        return "(() => { $lines })();";
    }

    /**
     * Build the json needed for the columns
     *
     * @return array
     */
    private function buildJson() : string
    {
        $json = $this->defaultConfig;
        
        $columnDef = $this->getColumnDefs();

        foreach($this->columns as $item){
            $column = $this->fillColumn($item);
            $json["columns"][] = [
                'data' => Str::contains($column->data, '.') ? Str::before($column->data, '.') : $column->data,
                'name' => $column->name
            ];
        }
        $json['columnDefs'] = $columnDef;

        $converted = json_encode($json);
        
        return str_replace(['"Q', 'Q"'], '', $converted);
    }

    /**
     * Set the column defs
     *
     * @return array
     */
    private function getColumnDefs() : array
    {
        $json = [];

        foreach($this->columnDefs as $target => $def){
            $emptycheck = Str::contains($def['column'], '.') ? "if(!row.".Str::before($def['column'], '.')."){ return ''; }" : "";
            
            $json[] = [
                "class" => $def["class"],
                "render" => "Q(data, type, row) => { $emptycheck {$def['def']} }Q",
                "targets" => $target
            ];
        }
        return $json;
    }

    /**
     * Export complete datatable
     *
     */
    public function export()
    {
        $table = $this->exportTable(true);
        $script = $this->exportScript(true);
        
        echo $table.$script;
    }

    /**
     * Filter the object
     *
     * @param object $item
     * @return object
     */
    public function fillColumn(object $item) : object
    {
        $item->name     = $item->name ?? $item->data;
        $item->label    = $item->label ?? $item->name;

        return $item;
    }

    /**
     * Check the column defs
     *
     * @param string $column
     * @return string
     */
    private function defColumnCheck(string $column) : string
    {
        $check = '';
        $set = 'row';
        $loop = explode('.', $column);
        $max = count($loop) - 1;
        foreach($loop as $key => $item){
            $set .= $key === $max ? "" : ".$item";
            $check .= $key === $max ? "!$set" : "!$set && ";
        }
        return "if ($check) {return '';}";
    }
    
}
