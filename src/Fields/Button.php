<?php
namespace ACFBentveld\DataTables\Fields;

/**
 * Description of Number
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Button
{

    /**
     * unique id
     *
     * @var string
     */
    protected $id;

    /**
     * Set the column name
     *
     * @var string
     */
    protected $name;

    /**
     * Set the class
     *
     * @var string
     */
    protected $class;

    /**
     * Set the route
     *
     * @var string
     */
    protected $route;

    /**
     * Set addition tot he route
     *
     * @var string
     */
    protected $addition;

    /**
     * Set the label
     *
     * @var string
     */
    protected $label;

    /**
     * Set the title
     *
     * @var string
     */
    protected $title;

    /**
     * Set the icon
     *
     * @var string
     */
    protected $icon;

    /**
     * Set the data methods
     *
     * @var string
     */
    protected $method = "";

    /**
     * Set the route replacing methods
     *
     * @var string
     */
    protected $routeSet = "";

    /**
     * Make the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Button
     */
    public static function make(string $name = null)
    {
        $class = new Button;
        $class->name = $name;
        $class->id = uniqid("button_");
        return $class;
    }

    /**
     * Set the classes
     *
     * @param string $class
     * @return $this
     */
    public function class(string $class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set the methods
     *
     * @param string $method
     * @param string $value
     * @return $this
     */
    public function method(string $method, string $value)
    {
        $this->method .= "data-$method=$value";
        return $this;
    }

    /**
     * Set the route
     *
     * @param string $url
     * @param array $keys
     * @return $this
     */
    public function route(string $url, $keys = [])
    {
        if(is_string($keys)){
            $keys  = [0 => $keys];
        }

        foreach($keys as $key => $value){
                $route[$key] = "*$value*";
        }

        $this->route = route($url, $route);
        $this->addition = '';
        foreach($keys as $key => $value){
            if(is_string($key)){
                $this->routeSet .= "let $key$this->id = $value;";
                $this->addition .= ".replace('*$value*', $key$this->id)";
            }else{
                $this->addition .= ".replace('*$value*', row.$value)";
            }
        }
        return $this;
    }

    /**
     * Set the label
     *
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set the popup title
     * 
     * @param string $title
     * @return $this
     */
    public function title(string $title, string $placement = "top")
    {
        $this->title = "data-toggle=\"tooltip\" data-placement=\"$placement\" title=\"$title\"";
        return $this;
    }

    /**
     * Set the icon
     *
     * @param string $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Build the button
     *
     * @return mixed
     */
    public function build()
    {
        return [
            "code"      => "$this->routeSet let $this->id = '$this->route'$this->addition;",
            "output"    => "'<button $this->method $this->title data-url=\"'+$this->id+'\" class=\"$this->class\">$this->icon $this->label</button>';"
        ];
    }


}