<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SingleQuote\DataTables\Controllers;

/**
 * Description of FieldsClass
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
abstract class FieldsClass
{
    /**
     * Set the column to be searchable
     *
     * @var bool
     */
    public $searchable = true;

    /**
     * Name of the column
     *
     * @var string
     */
    public $column;

    /**
     * Return when the value is empty
     *
     * @var mixed
     */
    public $returnWhenEmpty;

    
    protected static abstract function make(string $column);

    /**
     * Render the Field class
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function build()
    {
        $this->column       = $this->toLower($this->column);

        $class = $this;

        return view("laravel-datatables::fields.$this->view")
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
    private function toLower(string $string) : string
    {
        return strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $string));
    }
}