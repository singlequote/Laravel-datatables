<?php
namespace SingleQuote\DataTables\Controllers;

use Illuminate\Support\Str;

/**
 * Description of FieldsClass
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
abstract class Field
{
    /**
     * Set the required permissions for this field
     *
     * @var array
     */
    public $permissions = [];
    /**
     * Set the required roles for this field
     *
     * @var array
     */
    public $roles = [];
    
    /**
     * Set the column to be searchable
     *
     * @var bool
     */
    public $searchable = true;

    /**
     * Name of the column
     *
     * @var string
     */
    public $column;

    /**
     * Return when the value is empty
     *
     * @var mixed
     */
    public $returnWhenEmpty;

    /**
     * Set a condition
     *
     * @var mixed
     */
    public $condition;

    /**
     * The fields dom class
     *
     * @var string
     */
    public $class;

    /**
     *The name to oevrwrite the column name
     *
     * @var string
     */
    public $overwrite;

    /**
     * Before string
     *
     * @var string
     */
    public $before;

    /**
     * After string
     *
     * @var string
     */
    public $after;

    /**
     * Set to true to check if a column is empty
     *
     * @var bool
     */
    public $emptyCheck = true;
    
    /**
     * The data attributes
     *
     * @var array
     */
    public $data = [];
    
    /**
     * Set the title and toggle
     *
     * @var array
     */
    public $title = [
        'title' => "",
        'toggle' => ""
    ];

    /**
     * Required function to every field class
     * This is needed to call the classes
     *
     */
    abstract public static function make(string $column);

    /**
     * Set the required permissions
     *
     * @param string $permissions
     * @return $this
     */
    public function permission(string $permissions)
    {
        $required = str_replace([', ', ' ,', ', ' , ' | ', ' |', '| '], ',', $permissions);
        $else = explode('|', $required);

        foreach ($else as $key => $item) {
            $this->permissions[] = explode(',', $item);
        }
        
        return $this;
    }

    /**
     * Set the required permissions
     *
     * @param string $roles
     * @return $this
     */
    public function role(string $roles)
    {
        $required = str_replace([', ', ' ,', ', ' , ' | ', ' |', '| '], ',', $roles);
        $else = explode('|', $required);

        foreach ($else as $key => $item) {
            $this->roles[] = explode(',', $item);
        }
        
        return $this;
    }
    
    /**
     * Render the Field class
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function build()
    {
        $this->column = $this->toLower($this->column);
        
        $class = $this;

        return view($this->getView())
            ->with(compact('class'))
            ->render();
    }

    /**
     * Get the column path for example relation.name becomes relation
     *
     * @return string
     */
    public function columnPath(string $string = null) : string
    {
        $explode = explode('.', $this->overwrite ?? $this->column);
        array_pop($explode);
        $add = $string ? ".$string": "";
        if (count($explode) === 0) {
            return $string ?? "";
        }
        return implode('.', $explode).$add;
    }

    /**
     * Return the column name for example relation.name becomes name
     *
     * @return string
     */
    public function columnName() : string
    {
        $explode = explode('.', $this->column);
        return array_pop($explode) ?? "";
    }

    /**
     * Overwrite the make column to show diffrent data
     *
     * @param string $column
     * @return $this
     */
    public function column(string $column)
    {
        $this->overwrite = $column;
        return $this;
    }
    
    /**
     * Set a condition
     *
     * @param mixed $condition
     * @return $this
     */
    public function condition($condition)
    {
        $this->emptyCheck = false;
        $this->condition  = $condition;

        return $this;
    }

    /**
     * Set the class for the button
     *
     * @param string $class
     * @return $this
     */
    public function class(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Return the custom view when it exists.
     * If not return the default package view from the vendor
     *
     * @return string
     */
    private function getView()
    {
        if (view()->exists("$this->view")) {
            return $this->view;
        }
        return "laravel-datatables::fields.$this->view";
    }

    /**
     * Set the empty response for when the value is 0, null or false
     *
     * @param mixed $returnWhenEmpty
     * @return $this
     */
    public function returnWhenEmpty($returnWhenEmpty)
    {
        $this->returnWhenEmpty = $returnWhenEmpty;
        
        return $this;
    }

    /**
     * Translate someVariable to some_variable
     * Needed for relations
     *
     * @param string $string
     * @return string
     */
    public function toLower(string $string) : string
    {
        return strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $string));
    }

    /**
     * Set a string to display before the output
     *
     * @param string $before
     * @return $this
     */
    public function before(string $before)
    {
        $this->before = $before;

        return $this;
    }

    /**
     * Set a string to display after the output
     *
     * @param string $after
     * @return $this
     */
    public function after(string $after)
    {
        $this->after = $after;

        return $this;
    }
    
    /**
     * Return the string inside the tags
     *
     * @param string $string
     * @param string $tagname
     * @return string
     */
    public function getBetweenTags(string $string, string $tagname) : string
    {
        $after = Str::after($string, "<$tagname>");
        return Str::before($after, "</$tagname>");
    }
    
    /**
     * Set a dom element title title="..."
     * Use the toggle property to set data-toggle="..."
     *
     * @param string $title
     * @param string $toggle
     * @return $this
     */
    public function title(string $title, string $toggle = "tooltip")
    {
        $this->title = [
            'title' => $title,
            'toggle' => $toggle
        ];
        
        return $this;
    }
    
    /**
     * Set data attributes
     * 
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->data = $data;
        
        return $this;
    }
}
