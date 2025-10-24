<?php

namespace App\Console\Commands\FileGenerator;

use App\Console\Commands\FileGenerator\FileHelper\File;
use Exception;
use Illuminate\Console\Command;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bota-repository {repository}';

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
        $repository = trim($this->argument('repository'), '"\'');


        try {
            if (empty($repository)) {
                throw new Exception('Repository name cannot be empty!');
            }

            $this->info('Creating repository ...');

            $fileHelper = new File();
            $result = $fileHelper->createRepository($repository);

            $this->info($result);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
