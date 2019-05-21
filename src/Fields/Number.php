<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Number extends Field
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
     * Render as raw output
     *
     * @var bool
     */
    public $raw = false;

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
     * Sum columns from an object
     * Loop through relation fields and sum the selected columns
     *
     * @var mixed
     */
    public $sumEach;

    /**
     * Set the start number. Counting starts from this number when set.
     * This overrides the data value
     *
     * @var mixed
     */
    public $startAt;

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
     *
     * @param float $number
     * @return $this
     */
    public function startAt(float $number)
    {
        $this->startAt = $number;

        return $this;
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
     * Return value as raw count
     *
     * @return $this
     */
    public function raw()
    {
        $this->raw      = true;

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
        $this->startAt = $this->startAt ?? 0;
        $this->sum = $sum;
        
        return $this;
    }

    /**
     * Set the sum columns
     *
     * @param array $sum
     * @return $this
     */
    public function sumEach(... $sum)
    {
        $this->startAt = $this->startAt ?? 0;
        $this->sumEach = $sum;

        return $this;
    }

    

}