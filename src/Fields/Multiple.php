<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Multiple extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "multiple";

    /**
     * Set the closure fields
     *
     * @var array
     */
    public $fields;

    /**
     * Set the closure fields
     *
     * @var array
     */
    public $eachFields;

    /**
     * Set the closure counters
     *
     * @var array
     */
    public $count;

    /**
     * Implode an array
     *
     * @var string
     */
    public $implode;

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
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Multiple;
        $class->column = $column;
        return $class;
    }

    /**
     * Loop closure fields and render output
     * Is used for relations with multiple results
     *
     * @param \Closure $closure
     * @return $this
     */
    public function each(string $column, \Closure $closure)
    {
        foreach ($closure() as $field) {
            $this->eachFields[] = [
                "rendered" => $this->getBetweenTags($field->build(), 'script'),
                "path" => $column,
                "column" => $field->columnPath($field->columnName())
            ];
        }

        return $this;
    }
    
    /**
     * Loop closre fields and render output
     *
     * @param \Closure $closure
     */
    public function fields(\Closure $closure)
    {
        $this->emptyCheck = false;
        foreach ($closure() as $field) {
            $this->fields[] = [
                "rendered" => $this->getBetweenTags($field->build(), 'script'),
                "column" => $field->columnPath($field->columnName())
            ];
        }
        
        return $this;
    }

    /**
     * Counter fields
     * Merges the fields together as one output
     * Can be used for counting numbers
     *
     * @param \Closure $closure
     * @return $this
     */
    public function count(\Closure $closure)
    {
        foreach ($closure() as $field) {
            $this->count[] = [
                "rendered" => $this->getBetweenTags($field->build(), 'script'),
                "column" => $field->columnPath($field->columnName())
            ];
        }

        return $this;
    }

    /**
     * Set the implode fields
     *
     * @param string $separate
     * @return $this
     */
    public function implode(string $separate = ", ")
    {
        $this->emptyCheck = false;

        $explode = explode('.', $this->overwrite ?? $this->column);

        $this->implode['name'] = array_pop($explode);

        $this->implode['path'] = implode('.', $explode);

        $this->implode['seperate'] = $separate;

        return $this;
    }

    /**
     * Format the outcome result
     *
     * @param int $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return $this
     */
    public function format(int $decimals = 2, string $dec_point = ".", string $thousands_sep = ",")
    {
        $this->decimals = $decimals;
        $this->dec_point = $dec_point;
        $this->thousands_sep = $thousands_sep;

        return $this;
    }
}
