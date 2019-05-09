<?php
namespace ACFBentveld\DataTables\Fields;

/**
 * Description of Number
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Image
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
     * Set the columnn src
     *
     * @var string
     */
    protected $src = "";

    /**
     * Set the route replacing methods
     *
     * @var string
     */
    protected $routeSet = "";

    /**
     * Set the width
     *
     * @var mixed
     */
    protected $width;

    /**
     * Set the height
     *
     * @var mixed
     */
    protected $height;


    /**
     * Make the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Button
     */
    public static function make(string $name = null)
    {
        $class = new Image;
        $class->name = $name;
        $class->id = uniqid("image_");
        return $class;
    }

    /**
     * Set the source
     *
     * @param string $src
     * @return $this
     */
    public function src(string $src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Set the width
     *
     * @param type $width
     * @return $this
     */
    public function width($width = 0)
    {
        $this->width = "width=$width";
        return $this;
    }

    /**
     * set the height
     *
     * @param type $height
     * @return $this
     */
    public function height($height = 0)
    {
        $this->height = "height=$height";
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
            if(is_string($value)){
                $keys[$key] = "*$value*";
            }
        }

        $this->route = route($url, $keys);

        $this->addition = '';

        foreach($keys as $key => $value){
            if(is_string($key)){
                $this->routeSet .= "let $key = $value;";
                $this->addition .= ".replace('$value', ".str_replace('*', "", $value).")";
            }elseif(is_string($value)){
                $this->addition .= ".replace('$value', row.".str_replace('*', "", $value).")";
            }
        }
        
        $this->src = $this->route;
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
            "code"      => "$this->routeSet let $this->id = '$this->src'$this->addition;",
            "output"    => "'<img $this->width $this->height src=\"'+$this->id+'\">';"
        ];
    }


}