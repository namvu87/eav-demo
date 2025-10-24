<?php

namespace App\Console\Commands\FileGenerator;

use App\Console\Commands\FileGenerator\FileHelper\File;
use Exception;
use Illuminate\Console\Command;

class MakeResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bota-resource {name} {--module=} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return void
     */
    public function handle()
    {
        $resource = trim($this->argument('name'), '"\'');
        $module = trim($this->option('module'), '"\'');
        $table = trim($this->option('table'), '"\'');

        try {
            if (empty($resource)) {
                throw new Exception('Resource name cannot be empty!');
            }

            $this->info('Creating resource ...');

            $fileHelper = new File();
            $result = $fileHelper->createResource($module, $resource, $table);

            $this->info($result);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
