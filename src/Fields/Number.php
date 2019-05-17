<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\FieldsClass;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Number extends FieldsClass
{

    /**
     * The date view
     *
     * @var string
     */
    protected $view = "number";

    /**
     * Unique id
     *
     * @var string
     */
    public $id;

    /**
     * Render as currency
     *
     * @var bool
     */
    public $asCurrency = false;

    /**
     * Render as format
     *
     * @var bool
     */
    public $format = false;

    /**
     * Decimals
     *
     * @var int
     */
    public $decimals;

    /**
     * Decimal point
     *
     * @var string
     */
    public $dec_point;

    /**
     * Thousand step
     *
     * @var string
     */
    public $thousands_sep;

    /**
     * Sum columns
     *
     * @var mixed
     */
    public $sum;

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Number;
        $class->column = $column;
        $class->id = uniqid('Number');
        return $class;
    }

    /**
     * Format the value as currency
     *
     * @param int $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return $this
     */
    public function asCurrency(int $decimals = 2, string $dec_point = ".", string $thousands_sep = ",")
    {
        $this->asCurrency = true;
        $this->decimals = $decimals;
        $this->dec_point = $dec_point;
        $this->thousands_sep = $thousands_sep;

        return $this;
    }

    /**
     * Format the value as default format
     *
     * @param int $decimals
     * @return $this
     */
    public function format(int $decimals = 2)
    {
        $this->format   = true;
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * Set the sum columns
     *
     * @param array $sum
     * @return $this
     */
    public function sum(... $sum)
    {
        $this->sum = $sum;
        
        return $this;
    }

    

}