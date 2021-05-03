<?php
namespace SingleQuote\DataTables\Fields;

use SingleQuote\DataTables\Controllers\Field;

/**
 * Description of Label
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
class Middleware extends Field
{

    /**
     * Set the middleware keys
     *
     * @var array
     */
    public $middleware = [
        'roles' => [], 'permissions' => []
    ];

    /**
     * The model
     *
     * @var mixed
     */
    public $middlewareModel;

    /**
     * The date view
     *
     * @var string
     */
    protected $view = "middleware";

    /**
     * Init the fields class
     *
     * @param string $column
     * @return $this
     */
    public static function make(string $column)
    {
        $class = new Middleware;
        $class->column = $column;
        return $class;
    }

    /**
     * Set the required roles
     *
     * @param string $required
     * @return $this
     */
    public function role(string $required)
    {
        $role = str_replace([', ', ' ,', ', ', ' | ', ' |', '| '], ',', $required);
        $else = explode('|', $role);

        foreach ($else as $key => $item) {
            $this->middleware['roles'][] = explode(',', $item);
        }

        return $this;
    }

    /**
     * Set the required permissions
     * Pass a model for policy restrictions
     *
     * @param string $required
     * @param mixed $model
     * @return $this
     */
    public function permission(string $required, string $model = null)
    {
        $role = str_replace([', ', ' ,', ', ', ' | ', ' |', '| '], ',', $required);
        $else = explode('|', $role);

        foreach ($else as $key => $item) {
            $this->middleware['permissions'][] = explode(',', $item);
        }

        $this->middlewareModel = $model;

        return $this;
    }

    /**
     * Wrap the fields
     *
     * @param \Closure $closure
     * @return $this
     */
    public function wrap(\Closure $closure)
    {
        foreach ($closure() as $field) {
            $this->fields[] = [
                "rendered" => $this->getBetweenTags($field->build(), 'script'),
                "path" => $this->column,
                "column" => $field->columnPath($field->columnName())
            ];
        }

        return $this;
    }
}
