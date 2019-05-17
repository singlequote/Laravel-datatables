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
abstract class Field
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

    
    public static abstract function make(string $column);

    /**
     * Render the Field class
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function build()
    {
        $this->column       = $this->toLower($this->column);

        $class = $this;

        return view($this->getView())
            ->with(compact('class'))
            ->render();
    }

    /**
     * Return the custom view when it exists.
     * If not return the default package view from the vendor
     *
     * @return string
     */
    private function getView()
    {
        if(view()->exists("$this->view")){
            return $this->view;
        }
        return "laravel-datatables::fields.$this->view";
    }

    /**
     * Set the empty response for when the value is 0, null or false
     *
     * @param mixed $returnWhenEmpty
     * @return $this
     */
    public function returnWhenEmpty($returnWhenEmpty)
    {
        $this->returnWhenEmpty = $returnWhenEmpty;
        
        return $this;
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
}