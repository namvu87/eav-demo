<?php

namespace App\Console\Commands\FileGenerator\FileHelper;

use Illuminate\Support\Facades\DB;

class DbContext
{
    /**
     * Parse db context from context string
     * @param $dbContext
     * @throws \Exception
     * @return array
     */
    public static function parseDbContext($dbContext)
    {
        $db_context = [];
        $dbContext = explode(';', trim(preg_replace('/^dbcontext:/i', '', $dbContext), '[]'));

        if (empty($dbContext)) {
            throw new \Exception('Invalid context string');
        }

        foreach ($dbContext as $v) {
            $seg = explode(':', $v);
            if (!empty($seg[0])) {
                $db_context[$seg[0]] = @$seg[1];
            }
        }

        if (empty($db_context['host'])) {
            throw new \Exception('Host not found.');
        }

        if (empty($db_context['dbname'])) {
            throw new \Exception('Db not found.');
        }

        if (empty($db_context['user'])) {
            throw new \Exception('Username cannot be empty.');
        }

        return $db_context;
    }

    /**
     * Get table primary key
     * @param $table
     * @return string
     */
    public function getTablePrimaryKey($table)
    {
        $tableRealName = '_'.trim($table, '_');
        $resource = $this->getPdo()->prepare("DESCRIBE `{$tableRealName}`");
        $resource->execute();

        $primaryKey = '';
        $found = false;
        while (!$found && ($row = $resource->fetch(\PDO::FETCH_ASSOC))) {
            if ($row['Key'] == 'PRI') {
                $primaryKey = $row['Field'];
                $found = true;
            }
        }
        $resource = null;

        return $primaryKey;
    }

    /**
     * Get tables
     * @return array
     */
    public function getTables()
    {
        $tables = [];
        $resource = $this->getPdo()->prepare("SHOW TABLES");
        $resource->execute();
        while ($row = $resource->fetch()) {
            $tables[] = $row[0];
        }
        $resource = null;

        return $tables;
    }

    /**
     * Get table alias
     * return string
     * @param string $table
     * @param array $tablesAlias
     */
    public function getTableAlias($table, array $tablesAlias)
    {
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return DB::connection()->getPdo();
    }
}
