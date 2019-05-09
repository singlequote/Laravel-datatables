<?php
namespace SingleQuote\DataTables\Controllers;

/**
 * Description of ColumnBuilder
 *
 * @author Wim Pruiksma <wim.pruiksma@nugtr.nl>
 */
abstract class ColumnBuilder
{
    /**
     * Protected function set fields
     *
     */
    abstract protected function fields();

    /**
     * Set the table columns
     *
     * @var array
     */
    public $defs = [];

    /**
     * Set the table columns
     *
     * @var array
     */
    public $columns = [];

    /**
     * Set the table id
     *
     * @var mixed
     */
    public $tableId;

    /**
     * Set the table classes
     *
     * @var string
     */
    public $tableClass;

    /**
     * Set the translations for the header
     *
     * @return array
     */
    public function translate() : array
    {
        return [];
    }

    public function make()
    {
        $this->fields       = $this->fields();
        $this->translate    = $this->translate();
        $this->tableId      = $this->tableId ?? uniqid('laravelDataTable');

        return $this;
    }

}