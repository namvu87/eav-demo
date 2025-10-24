<?php

namespace App\Console\Commands;
use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class CreateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
     * @return int
     */
    public function handle()
    {
        // Gọi hàm tạo sitemap
        $sitemap = new SitemapController;
        $sitemap->createSitemap();
        
        $this->info('Sitemap created successfully!');
    }
}
