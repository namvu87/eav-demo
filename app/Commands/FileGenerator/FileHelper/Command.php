<?php

namespace App\Console\Commands\FileGenerator\FileHelper;

/**
 * Class Command
 * @package App\Libraries\Cmd
 */
abstract class Command
{
    /**
     * @var DbContext $dbContext
     */
    protected $dbContext;

    /**
     * Get db context
     * @throws \Exception
     */
    protected function getDbContext()
    {
        $this->dbContext = new DbContext();
    }
}
