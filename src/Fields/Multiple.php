<?php
namespace ACFBentveld\DataTables\Fields;

use Illuminate\Support\Str;

/**
 * Description of Multiple
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Multiple
{
    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * The code that needs to be returned when empty
     *
     * @var string
     */
    protected $empty;

    /**
     * The value that needs to be returned when empty
     *
     * @var string
     */
    protected $showEmpty;

    /**
     * The value that needs to be returned
     *
     * @var string
     */
    protected $column;

    /**
     * The seperator character
     *
     * @var mixed
     */
    protected $seperate = "";


    /**
     * Build the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Multiple
     */
    static function make(string $name = null)
    {
        $class = new Multiple;
        $class->name = Str::before($name, '.');
        $class->column = Str::after($name, '.');
        $class->returnWhenEmpty('');
        return $class;
    }

    /**
     * Set the items seperator
     *
     * @param mixed $seperator
     * @return $this
     */
    public function seperate($seperator)
    {
        $this->seperate = $seperator;
        return $this;
    }

    /**
     * Set the empty string
     *
     * @param string $empty
     * @return $this
     */
    public function returnWhenEmpty(string $empty)
    {
        $this->showEmpty = $empty;
        $this->empty = "if(!row.$this->name || !row.$this->name.length === 0){ return '$empty'; }";
        return $this;
    }

    /**
     * Build the class
     *
     * @return array
     */
    public function build()
    {
        $output = "$this->empty let items = ''; let glue = '$this->seperate';";
        $output .= "Object.keys(row.$this->name).forEach((key) => {";
        $output .= "glue = parseInt(key) === row.$this->name.length -1 ? '' : glue;";
        $output .= "items += row.$this->name[key]['$this->column'] + glue;";
        $output .= "});";
        
        return [
            'code' => "$output",
            'output' => "items;"
        ];
    }

    



}