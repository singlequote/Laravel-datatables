<?php
namespace SingleQuote\DataTables\Filters;

use SingleQuote\DataTables\Controllers\Filter;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Date extends Filter
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "date";
    
    /**
     * The date view
     *
     * @var string
     */
    public $inputType = "date";

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new self;
        $class->column = $column;
        return $class;
    }
    
    /**
     * @param string $type
     * @return $this
     */
    public function inputType(string $type)
    {
        $this->inputType = $type;
        
        return $this;
    }
}
