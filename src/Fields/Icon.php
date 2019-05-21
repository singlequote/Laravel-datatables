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
     * Custom icon
     *
     * @var mixed
     */
    public $custom;

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

    public function feather(string $icon)
    {
        $this->feather = $icon;

        return $this;
    }
    

}