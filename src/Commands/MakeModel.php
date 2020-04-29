<?php
namespace SingleQuote\DataTables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * THis command generates a new table model
 *
 * @author Wim Pruiksma SingleQuote
 */
class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:table-model {name} {--route=} {--class=} {--buttons} {--translations} {--query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new tableModel';

    /**
     * Model path. the generated files will be placed here
     *
     * @var string
     */
    protected $path = "TableModels";
    
    /**
     * Set the stub file
     *
     * @var string
     */
    protected $stubFile = "TableModel.stub";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \App\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $this->getStubFile();
        $this->parseStubFile();
        $this->cleanUpStub();
        $this->generateStubFile();
    }

    /**
     * Generate the stub file
     *
     */
    private function generateStubFile()
    {
        $make = true;

        if (!File::isDirectory(app_path($this->path))) {
            File::makeDirectory(app_path($this->path));
        }
        if (File::exists(app_path("$this->path/{$this->argument('name')}.php"))) {
            $make = $this->confirm("The table model {$this->argument('name')} already exists. Do you want to replace it?");
        }

        if ($make) {
            File::put(app_path("$this->path/{$this->argument('name')}.php"), $this->stub);
            $this->info("TableModel created inside the folder ".app_path($this->path));
        }
    }

    /**
     * Parse the stub file
     *
     */
    private function parseStubFile()
    {
        $this->replaceValues('name', $this->argument('name'));
        
        $this->replaceValues('class', "
    // @var array
    public \$tableClass = \"{$this->option('class')}\";
        ", $this->option('route'));
        
        if ($this->option('buttons')) {
            $this->replaceValues('button namespace', "use SingleQuote\DataTables\Fields\Button; //button field");
            
            $this->replaceValues('show-button', "Button::make('id')->class('my-button-class')->route('<stub>route</stub>.show', 'id'),");
            $this->replaceValues('edit-button', "Button::make('id')->class('my-button-class')->route('<stub>route</stub>.edit', 'id'),");
            $this->replaceValues('destroy-button', "Button::make('id')->class('my-button-class')->method('delete')->route('<stub>route</stub>.destroy', 'id'),");

            $this->replaceValues(
                'button column',
                '[
            "data"          => "id",
            "name"          => "id",
            "class"         => "my-class",
            "orderable"     => false,
            "searchable"    => false
        ]'
            );
        }

        if ($this->option('translations')) {
            $this->replaceValues('translations', "
   /**
    * Set the translation columns for the headers
    *
    * @return array
    */
    public function translate() : array
    {
        return [
            'name' => trans('Name'),
        ];
    }
            ");
        }

        if ($this->option('query')) {
            $this->replaceValues('query', "
   /**
    * Run an elequent query
    *
    * @param \Illuminate\Database\Query\Builder \$query
    * @return \Illuminate\Database\Query\Builder
    */
    public function query(\$query)
    {
        return \$query->whereNotNull('id');
    }
            ");
        }

        $this->replaceValues('route', $this->option('route') ?? "my-route");
    }

    /**
     * Clean upt he stub file
     * Removing all the option values
     *
     */
    private function cleanUpStub()
    {
        $this->stub = preg_replace('#<stub[^>]*>.*?</stub>#si', '', $this->stub);
    }

    /**
     * Find and replace value in Stub respond
     *
     * @param string $find
     * @param string $replace
     */
    private function replaceValues(string $find, string $replace, $if = true)
    {
        if ($if) {
            $this->stub = str_replace("<stub>$find</stub>", $replace, $this->stub);
        }
    }


    /**
     * Return the stub file for generating new table model
     *
     * @return string
     */
    private function getStubFile() : string
    {
        $this->stub = File::get(__dir__."/stubs/$this->stubFile");
        return $this->stub;
    }
}
