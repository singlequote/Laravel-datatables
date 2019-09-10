<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Date
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Date extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "date";

    /**
     *
     * @var
     */
    public $format = "Y-m-d";

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Date;
        $class->column = $column;
        return $class;
    }

    /**
     * Set the format
     *
     * @param string $format
     * @return $this
     */
    public function format(string $format)
    {
        $this->format = $format;
        return $this;
    }
}
