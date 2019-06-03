<?php
namespace SingleQuote\DataTables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * THis command generates a new table model
 *
 * @author Wim Pruiksma SingleQuote
 */
class MakeField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:table-field {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new table field';

    /**
     * Model path. the generated files will be placed here
     *
     * @var string
     */
    protected $path = "TableModels/Fields";
    
    /**
     * Set the stub file
     *
     * @var string
     */
    protected $stubFile = "TableField.stub";

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

        if(!File::isDirectory(app_path($this->path))){
            File::makeDirectory(app_path($this->path));
        }
        if(!File::isDirectory(resource_path("views/vendor/laravel-datatables/fields/"))){
            File::makeDirectory(resource_path("views/vendor/laravel-datatables/fields/"), 493, true);
        }
        if(File::exists(app_path("$this->path/{$this->argument('name')}.php"))){
            $make = $this->confirm("The table field {$this->argument('name')} already exists. Do you want to replace it?");
        }

        if($make){
            File::put(app_path("$this->path/{$this->argument('name')}.php"), $this->stub);
            File::put(resource_path("views/vendor/laravel-datatables/fields/".strtolower($this->argument('name')).".blade.php"), $this->view);
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
        $this->replaceValues('view', strtolower($this->argument('name')));

        
    }

    /**
     * Clean upt he stub file
     * Removing all the option values
     *
     */
    private function cleanUpStub()
    {
        $this->stub = preg_replace('#<stub[^>]*>.*?</stub>#si', '',  $this->stub);
    }

    /**
     * Find and replace value in Stub respond
     *
     * @param string $find
     * @param string $replace
     */
    private function replaceValues(string $find, string $replace, $if = true)
    {
        if($if){
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
        $this->view = File::get(__dir__."/stubs/view.stub");
        return $this->stub;
    }
}