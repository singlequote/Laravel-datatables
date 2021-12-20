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
}
