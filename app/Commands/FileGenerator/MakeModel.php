<?php

namespace App\Console\Commands\FileGenerator;

use App\Console\Commands\FileGenerator\FileHelper\File;
use Exception;
use Illuminate\Console\Command;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bota-model {name} {--table=}';

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
        $model = trim($this->argument('name'), '"\'');
        $table = $this->option('table');

        try {
            if (empty($model)) {
                throw new Exception('Model name cannot be empty!');
            }

            $this->info('Creating model ...');

            $fileHelper = new File();
            $result = $fileHelper->createModel($model, $table);

            $this->info($result);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
