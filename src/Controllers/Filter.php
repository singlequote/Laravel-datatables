<?php
namespace SingleQuote\DataTables\Controllers;

use Illuminate\Support\Str;

/**
 * Description of FieldsClass
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
abstract class Filter
{
    /**
     * Required function to every field class
     * This is needed to call the classes
     *
     */
    abstract public static function make(string $column);
    
    /**
     * The columns
     *
     * @var string
     */
    protected $column;
    
    /**
     * The columns
     *
     * @var string
     */
    protected $size = 'col-4';
    
    /**
     * The columns
     *
     * @var string
     */
    protected $label = '';
    
    /**
     * The filter ID
     *
     * @var string
     */
    protected $id;
    
    /**
     * The filter class
     *
     * @var string
     */
    protected $class = '';
    
    /**
     * The data items for the filter
     *
     * @var array
     */
    protected $data = [];
    
    
    /**
     * The dom attributes
     *
     * @var array
     */
    protected $string = '';
    
    /**
     * The trigger for the filter element
     *
     * @var string
     */
    protected $trigger = 'change';
        
    /**
     * Set the size for the parent element
     *
     * @param string $size
     * @return $this
     */
    public function size(string $size)
    {
        $this->size = $size;
        
        return $this;
    }
    /**
     * Return the size for the filter element
     *
     * @return string
     */
    public function getSize() : string
    {
        return $this->size;
    }
        
    /**
     * Return the name for the filter element
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->column;
    }
    
    /**
     * Set the trigger for the element
     *
     * @param string $trigger
     * @return $this
     */
    public function trigger(string $trigger)
    {
        $this->trigger = $trigger;
        
        return $this;
    }
    
    /**
     * Return the trigger for the filter element
     *
     * @return string
     */
    public function getTrigger() : string
    {
        return $this->trigger;
    }
    
    /**
     * Set the label for the element
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
     * Return the label for the filter element
     *
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }
    
    /**
     * Return true if the element is multiple
     *
     * @return bool
     */
    public function getMultiple() : bool
    {
        return Str::contains($this->string, 'multiple');
    }
    
    /**
     * Set the data items for the filter
     *
     * @param mixed $data
     * @param \Closure $closure
     * @return $this
     */
    public function data($data, \Closure $closure = null)
    {
        foreach ($data as $item) {
            if (!$closure) {
                $this->data[] = [
                    'value' => is_array($item) ? $item['id'] : $item->id,
                    'label' => is_array($item) ? $item['name'] : $item->name
                ];
            } else {
                $this->data[] = $closure($item);
            }
        }
        
        return $this;
    }
    
    /**
     * Returnt he data for the filter
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }
        
    /**
     * Set the filter class
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
     * Return the filter class
     *
     * @return string
     */
    public function getClass() : string
    {
        return $this->class;
    }
       
    /**
     * Set the filter id
     *
     * @param string $class
     * @return $this
     */
    public function id(string $id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Return the filter ID
     * Generate ne wone if no id is set
     *
     * @return string
     */
    public function getID() : string
    {
        return $this->id ? $this->id : $this->id = uniqid();
    }
    
    /**
     * Set dom attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function attributes(... $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!is_array($attribute)) {
                $this->string .= "$attribute ";
                continue;
            }
            
            foreach ($attribute as $key => $value) {
                $this->string .= "$key=$value ";
            }
        }
        
        return $this;
    }
        
    /**
     * Return the attributes as string
     *
     * @return string
     */
    public function getString() : string
    {
        return $this->string;
    }
    
    /**
     * Render the Field class
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function build()
    {
        $this->column   = $this->toLower($this->column);
        $class          = $this;

        return view($this->getView())
            ->with(compact('class'))
            ->render();
    }
    
    
    /**
     * Translate someVariable to some_variable
     * Needed for relations
     *
     * @param string $string
     * @return string
     */
    public function toLower(string $string) : string
    {
        return strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $string));
    }
    
    /**
     * Return the custom view when it exists.
     * If not return the default package view from the vendor
     *
     * @return string
     */
    private function getView()
    {
        if (view()->exists("$this->view")) {
            return $this->view;
        }
        return "laravel-datatables::filters.$this->view";
    }
}
