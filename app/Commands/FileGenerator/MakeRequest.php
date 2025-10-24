<?php

namespace App\Console\Commands\FileGenerator;

use App\Console\Commands\FileGenerator\FileHelper\File;
use Exception;
use Illuminate\Console\Command;

class MakeRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:bota-request {name} {--module=} {--table=} {--store=} {--update=}';

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
        $request = trim($this->argument('name'), '"\'');
        $module = trim($this->option('module'), '"\'');
        $table = trim($this->option('table'), '"\'');
        $store = (boolean) (($this->option('store')) ?? false);
        $update = (boolean) (($this->option('update')) ?? false);

        try {
            if (empty($request)) {
                throw new Exception('Request name cannot be empty!');
            }

            $this->info('Creating request ...');

            $fileHelper = new File();

            if ($store) {
                $result = $fileHelper->createRequestStore($module, $request, $table);
            } elseif ($update) {
                $result = $fileHelper->createRequestUpdate($module, $request, $table);
            } else {
                $result = $fileHelper->createRequest($module, $request, $table);
            }

            $this->info($result);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
