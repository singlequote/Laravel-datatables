<?php
namespace SingleQuote\DataTables;

use SingleQuote\DataTables\DataTableException;
use SingleQuote\DataTables\Fields\Label;
use Illuminate\Support\Str;
use Request;

/**
 * An laravel jquery datatables package
 *
 * @author Wim Pruiksma
 */
class DataTable
{

    public function __construct()
    {
        //
    }

    /**
     * Set the model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     * @throws DataTableException
     */
    public function model($model)
    {
        if($model instanceof \Illuminate\Database\Eloquent\Model){
            $this->model = $model;
            return $this;
        }
        throw new DataTableException("$model must be an an instance of \Illuminate\Database\Eloquent\Model");
    }

    public function tableModel($class)
    {
        $this->view = new $class;
        $this->view->make();
        return $this->build();
    }

    /**
     * Build the table
     *
     * @return $this
     */
    private function build()
    {
        $this->checkColumns();
        $this->checkDefs();

        $this->generateTable();
        $this->generateScripts();


        return $this;
    }

    /**
     * Check the columns and fill the defs
     * Fill the defs and set the targets for every column
     *
     */
    private function checkColumns()
    {
        foreach($this->view->columns as $index => $column){

            $name = is_array($column) ? isset($column['name']) ? $column['name'] : null  : $column;
            $data = is_array($column) ? isset($column['data']) ? $column['data'] : null  : $column;

            $this->view->columns[$index] = [
                'data' => $data ?? $name,
                'name' => $name ?? $data
            ];
            
            $this->view->defs[$data] = [
                'id'        => uniqid('function'),
                'target'    => $index,
                'def'       => []
            ];

        }
    }

    /**
     * Fill in the fields defs
     * Fill the defs when there is no field set
     *
     */
    private function checkDefs()
    {
        foreach($this->view->fields as $field){
            $this->view->defs[$field->column]['def'][] = $field;
        }
        
        $this->buildDef();
    }

    /**
     * Render the defs
     * THe defs define the columns behaviour
     *
     */
    private function buildDef()
    {
        foreach($this->view->defs as $column => $def){
            if(count($def['def']) === 0){
                $this->view->defs[$column]['def'] = [Label::make($column)];
            }
        }
        
        foreach($this->view->defs as $column => $def){
            foreach($def['def'] as $index => $field){
                $rendered = $this->getBetweenTags($field->build(), 'script');
                $this->view->defs[$column]['rendered'][$index] = $rendered;
            }
        }
    }

    /**
     * Return the string inside the tags
     *
     * @param string $string
     * @param string $tagname
     * @return string
     */
    private function getBetweenTags(string $string, string $tagname) : string
    {
        $after = Str::after($string, "<$tagname>");
        return Str::before($after, "</$tagname>");
    }

    /**
     * Generate the table
     * The tables are generated in a view
     *
     */
    private function generateTable()
    {
        $view = $this->view;
        $this->generatedTable = view("laravel-datatables::table")
            ->with(compact('view'))
            ->render();
    }

    /**
     * Generate the scripts
     * The scripts are generated in a view
     *
     */
    public function generateScripts()
    {
        $view = $this->view;
        $this->generatedScript = view("laravel-datatables::scripts")
            ->with(compact('view'))
            ->render();
    }

}
