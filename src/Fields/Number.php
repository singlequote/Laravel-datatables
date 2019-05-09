<?php
namespace ACFBentveld\DataTables\Fields;

use Illuminate\Support\Str;
/**
 * Description of Number
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Number
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
     * Code output
     *
     * @var string
     */
    protected $code = '';

    /**
     * Output
     *
     * @var string
     */
    protected $output = "'';";

    /**
     * Build the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Button
     */
    static function make(string $name = null)
    {
        $class = new Number;
        $class->name = $name ? "row.".Str::before($name, '.') : false;
        $class->column = $name ? Str::after($name, '.') : false;
        
        return $class;
    }

    /**
     * Format a number with grouped thousands
     *
     * @param string $column
     * @param int $decimals
     * @return string
     */
    public function format(int $decimals = 0)
    {
        $this->code = $this->empty;
        $this->output = $this->name.toFixed($decimals);

        return $this;
    }

    /**
     * Format a number with grouped thousands
     * Format is used for currencies
     *
     * @param string $column
     * @param int $decimal
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string
     */
    public function currency(int $decimal = 2, string $dec_point = ".", string $thousands_sep = ",")
    {
        $function = "Number.prototype.format = function(n, x, s, c) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));
            return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));}; var result = 0;";

        $this->code = "$this->empty $function";
        $this->code .= "if(typeof $this->name == 'object'){";
        $this->code .= "$.each($this->name, (key, value) => { result += value.$this->column; });";
        $this->code .= "}else{";
        $this->code .= "result = $this->name;";
        $this->code .= "}";


        $this->output = "parseFloat(result).format($decimal, 3, '$dec_point', '$thousands_sep');";

        return $this;
    }

    /**
     * Sum currency
     *
     * @param mixed $columns
     * @return array
     */
    public function sumCurrency(... $columns)
    {
        $items = collect($columns);
        foreach($items as $index => $item){
            $items[$index] = "value.$item";
        }

        $this->column = $items->implode(' + ');
        $this->name = $items->implode(' + ');
        
        $function = "Number.prototype.format = function(n, x, s, c) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));
            return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));}; var result = 0;";

        $this->code = "$this->empty $function";
        $this->code .= "if(typeof $this->name == 'object'){";
        $this->code .= "$.each($this->name, (key, value) => { result += $this->column; });";
        $this->code .= "}else{";
//        $this->code .= "console.log(data);";
        $this->code .= "}";


        $this->output = "parseFloat(result).format(2, 3, '.', ',');";
        
        return $this;
    }

    /**
     * Sum columns
     *
     * @param mixed $columns
     * @return string
     */
    public function sum(... $columns)
    {
        $items = collect($columns);

        foreach($items as $index => $item){
            $items[$index] = "row.$item";
        }

        $this->output = "{$items->implode('+ ')};";

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
        if($this->name){
            $this->empty = "if(!$this->name || !$this->name === 0){ return '$empty'; }";
        }
        
        return $this;
    }

    /**
     * Set the build method
     *
     * @return array
     */
    public function build() : array
    {
        return [
            "code"      => $this->code,
            "output"    => "$this->output"
        ];
    }


}