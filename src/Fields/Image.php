<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Image extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "image";

    /**
     * Image route
     *
     * @var string
     */
    public $route;

    /**
     * Image source
     *
     * @var string
     */
    public $src;

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Image;
        $class->column = $column;
        return $class;
    }

    /**
     * Set the route to redirect the user to when the button is clicked
     *
     * @param string $route
     * @param mixed $parameters
     * @return $this
     */
    public function route(string $route, $parameters)
    {
        $params = is_array($parameters) ? $parameters : [$parameters];

        foreach($params as $index => $param){
            if(is_int($param)){
                continue;
            }
            $this->routeReplace["*$param*"] = $this->columnPath($param);
            $params[$index] = "*$param*";
        }

        $this->route = route($route, $params);

        return $this;
    }

    /**
     * Set the source for the image
     *
     * @param string $src
     * @return $this
     */
    public function src(string $src)
    {
        $this->src = $src;

        return $this;
    }

}