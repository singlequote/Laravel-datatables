<?php

namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Checkbox extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "checkbox";

    /**
     * Unique checkbox id
     *
     * @var string
     */
    public $id;

    /**
     * Set the icon
     *
     * @var string
     */
    public $icon;

    /**
     * Set the checkbox label
     *
     * @var string
     */
    public $label;

    /**
     * Set the checkbox name
     *
     * @var string
     */
    public $name;

    /**
     * Set onclick method
     *
     * @var string
     */
    public $onClick;
    
    /**
     * Set the checkbox to checked
     *
     * @var bool
     */
    public $checked = false;

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class         = new self;
        $class->column = $column;
        $class->id     = uniqid('checkbox_');

        return $class;
    }

    /**
     * Set the icon for the checkbox
     *
     * @param string $class
     * @param string $name
     * @return $this
     */
    public function icon(string $class, string $name = "")
    {
        $this->icon = "<i class=\"$class\">$name</i>";

        return $this;
    }

    /**
     * Set the icon for the checkbox
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the checkbox label
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
     * Set the onclick method
     *
     * @param string $onClick
     * @return $this
     */
    public function onclick(string $onClick)
    {
        $this->onClick = $onClick;

        return $this;
    }
    
    /**
     * Set the resource to be checked
     * 
     * @param mixed $checked
     * @return $this
     */
    public function checked($checked = false)
    {
        $this->checked = $checked; 
        
        return $this;
    }
}
