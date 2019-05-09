<?php
namespace ACFBentveld\DataTables\Fields;

/**
 * Description of Number
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Date
{
    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * The value that needs to be returned when empty
     *
     * @var mixed
     */
    protected $empty;

    /**
     * Build the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Date
     */
    public static function make(string $name)
    {
        $class = new Date;
        $class->name = $name;
        return $class;
    }

    /**
     * Format date string
     *
     * @param string $column
     * @param string $format
     * @return mixed
     */
    public function format(string $format = "Y-m-d")
    {

        $function = "$this->empty function dateFormatter(date, format) {
            let  monthNames = [
              'January', 'February', 'March',
              'April', 'May', 'June', 'July',
              'August', 'September', 'October',
              'November', 'December'
            ];

            let replaced = format.replace('d', date.getDate())
            .replace('D', date.getDay())
            .replace('m', date.getMonth() + 1)
            .replace('Y', date.getFullYear())
            .replace('y', date.getYear())
            .replace('H', date.getHours())
            .replace('i', date.getMinutes())
            .replace('s', date.getSeconds());

            return replaced;
        }";

        return [
            "code"      => "$function",
            "output"    => "dateFormatter(new Date(row.$this->name), '$format');"
        ];
    }

    /**
     * Set the empty string
     *
     * @param string $empty
     * @return $this
     */
    public function returnWhenEmpty(string $empty)
    {
        $this->empty = "if(!row.$this->name || !row.$this->name === 0){ return '$empty'; }";
        return $this;
    }
    




}