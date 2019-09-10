<?php
namespace SingleQuote\DataTables\Filter;

use SingleQuote\DataTables\Controllers\Filter;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Dropdown extends Filter
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "dropdown";

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Dropdown;
        $class->column = $column;
        return $class;
    }
}
