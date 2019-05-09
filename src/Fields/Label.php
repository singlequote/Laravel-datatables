<?php
namespace ACFBentveld\DataTables\Fields;

/**
 * Description of Number
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Label
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
     * Seperater for multiple labels
     *
     * @var mixed
     */
    protected $seperate;

    /**
     * Title for multiple labels
     *
     * @var mixed
     */
    protected $title;

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
     * The value that needs to be returned before the ouput
     *
     * @var string
     */
    protected $before;

    /**
     * The value that needs to be returned before the ouput
     *
     * @var string
     */
    protected $class;

    /**
     * Make the class
     *
     * @param string $name
     * @return \ACFBentveld\DataTables\Fields\Button
     */
    public static function make(string $name = null)
    {
        $class = new Label;
        $class->name = $name ? "'+row.$name+'" : "";
        $class->id = uniqid("label_");
        return $class;
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
        $this->empty = "if(!$this->name || !$this->name === 0){ return '$empty'; }";

        return $this;
    }

    /**
     *
     * @param mixed $before
     */
    public function before($before)
    {
        $this->before = $before;
        
        return $this;
    }

    /**
     *
     * @param mixed $before
     */
    public function class($class)
    {
        $this->class = $class;

        return $this;
    }


    /**
     * Set the title
     *
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = "$title";

        return $this;
    }

    /**
     *
     * @param type $seperate
     * @return $this
     */
    public function seperate($seperate)
    {
        $this->seperate = $seperate;
        
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
            "code"      => "$this->empty",
            "output"    => "'$this->before<label class=\"$this->class\">$this->title $this->name</label>$this->seperate';"
        ];
    }


}