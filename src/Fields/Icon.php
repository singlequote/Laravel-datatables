<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Icon extends Field
{
    /**
     * The date view
     *
     * @var string
     */
    protected $view = "icon";

    /**
     * Font awesome icon
     *
     * @var mixed
     */
    public $fa;

    /**
     * Material icon
     *
     * @var mixed
     */
    public $material;

    /**
     * Feather icon
     *
     * @var mixed
     */
    public $feather;

    /**
     * Custom icon
     *
     * @var mixed
     */
    public $custom;

    /**
     * Custom name icon
     *
     * @var mixed
     */
    public $customName;

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Icon;
        $class->column = $column;
        return $class;
    }

    /**
     * Set an material icon
     *
     * @param string $icon
     * @return $this
     */
    public function material(string $icon)
    {
        $this->material = $icon;

        return $this;
    }

    /**
     * Set a feather icon
     *
     * @param string $icon
     * @return $this
     */
    public function feather(string $icon)
    {
        $this->feather = $icon;

        return $this;
    }

    /**
     * Set the class
     *
     * @param string $class
     * @return $this
     */
    public function fa(string $fa)
    {
        $this->fa = $fa;

        return $this;
    }
    
    /**
     * Set the class
     *
     * @param string $class
     * @return $this
     */
    public function custom(string $custom, string $name = null)
    {
        $this->custom       = $custom;
        $this->customName   = $name;

        return $this;
    }
}
